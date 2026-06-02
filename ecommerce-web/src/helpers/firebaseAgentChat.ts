import {
  addDoc,
  collection,
  doc,
  getFirestore,
  onSnapshot,
  orderBy,
  query,
  serverTimestamp,
  setDoc,
  type Timestamp,
  type Unsubscribe,
} from 'firebase/firestore';
import { getFirebaseApp } from './firebaseCore';

function sanitizeRoomSegment(value: string | number): string {
  return String(value).replace(/[^a-zA-Z0-9_-]/g, '-').slice(0, 40);
}

export function buildJoinChatUrl(roomId: string): string {
  if (typeof window === 'undefined') {
    return '';
  }
  const base = window.location.origin;
  return `${base}/join-chat/${encodeURIComponent(roomId)}`;
}

export type ChatMessage = {
  id: string;
  sender: 'customer' | 'agent';
  text: string;
  createdAtMs: number;
};

export type StartAgentChatParams = {
  storeOptimusId: string | number;
  itemOptimusId: string | number;
  storeName?: string;
  itemName?: string;
};

export type StartAgentChatResult = {
  roomId: string;
  joinUrl: string;
};

export function startAgentChatSession(params: StartAgentChatParams): StartAgentChatResult {
  const roomId =
    typeof crypto !== 'undefined' && crypto.randomUUID
      ? crypto.randomUUID()
      : `chat-${sanitizeRoomSegment(params.storeOptimusId)}-${sanitizeRoomSegment(params.itemOptimusId)}-${Date.now()}`;
  const joinUrl = buildJoinChatUrl(roomId);

  // Fire-and-forget so UI never blocks on slow Firestore/Auth.
  void (async () => {
    try {
      const app = getFirebaseApp();
      if (!app) {
        return;
      }
      const db = getFirestore(app);

      await setDoc(
        doc(db, 'chat_rooms', roomId),
        {
          roomId,
          joinUrl,
          status: 'open',
          source: 'public_store_item',
          storeOptimusId: params.storeOptimusId,
          itemOptimusId: params.itemOptimusId,
          storeName: params.storeName || '',
          itemName: params.itemName || '',
          createdAt: serverTimestamp(),
          updatedAt: serverTimestamp(),
        },
        { merge: true }
      );

      await addDoc(collection(db, 'agent_chats'), {
        roomId,
        joinUrl,
        status: 'pending',
        source: 'public_store_item',
        storeOptimusId: params.storeOptimusId,
        itemOptimusId: params.itemOptimusId,
        storeName: params.storeName || '',
        itemName: params.itemName || '',
        createdAt: serverTimestamp(),
      });
    } catch {
      // Keep chat usable via direct room messages even if notification fails.
    }
  })();

  return { roomId, joinUrl };
}

export async function sendChatMessage(
  roomId: string,
  sender: 'customer' | 'agent',
  text: string
): Promise<void> {
  const trimmed = text.trim();
  if (!trimmed) {
    return;
  }
  const app = getFirebaseApp();
  if (!app) {
    throw new Error('Firebase is not configured');
  }
  const db = getFirestore(app);
  await addDoc(collection(db, 'chat_rooms', roomId, 'messages'), {
    sender,
    text: trimmed,
    createdAt: serverTimestamp(),
    clientCreatedAt: Date.now(),
  });
  await setDoc(
    doc(db, 'chat_rooms', roomId),
    {
      updatedAt: serverTimestamp(),
      lastMessage: trimmed,
      lastSender: sender,
    },
    { merge: true }
  );
}

function tsToMs(value: Timestamp | null | undefined, fallback?: number): number {
  if (typeof fallback === 'number') {
    return value?.toMillis?.() ?? fallback;
  }
  return value?.toMillis?.() ?? Date.now();
}

export function subscribeChatMessages(
  roomId: string,
  onMessages: (messages: ChatMessage[]) => void,
  onError?: (message: string) => void
): Unsubscribe {
  const app = getFirebaseApp();
  if (!app) {
    throw new Error('Firebase is not configured');
  }
  const db = getFirestore(app);
  const q = query(collection(db, 'chat_rooms', roomId, 'messages'), orderBy('createdAt', 'asc'));
  return onSnapshot(
    q,
    (snap) => {
      const messages: ChatMessage[] = snap.docs.map((d) => {
        const data = d.data() as {
          sender?: 'customer' | 'agent';
          text?: string;
          createdAt?: Timestamp;
          clientCreatedAt?: number;
        };
        return {
          id: d.id,
          sender: data.sender === 'agent' ? 'agent' : 'customer',
          text: data.text || '',
          createdAtMs: tsToMs(data.createdAt, data.clientCreatedAt),
        };
      });
      onMessages(messages);
    },
    (err) => {
      onError?.(err?.message || 'Could not subscribe to chat messages.');
    }
  );
}
