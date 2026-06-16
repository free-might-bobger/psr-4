import { pushAgentCallRequest, isFirebaseAgentNotifyConfigured } from './firebaseAgentNotify';
import { ensureFirebaseAnonymousAuth } from './webrtcFirebaseCall';

function sanitizeRoomSegment(value: string | number): string {
  return String(value).replace(/[^a-zA-Z0-9_-]/g, '-').slice(0, 40);
}

export function buildJoinCallUrl(roomId: string): string {
  if (typeof window === 'undefined') {
    return '';
  }
  const base = window.location.origin;
  return `${base}/join-call/${encodeURIComponent(roomId)}`;
}

export type StartAgentCallParams = {
  storeOptimusId: string | number;
  itemOptimusId: string | number;
  storeName?: string;
  itemName?: string;
};

export type StartAgentCallResult = {
  roomId: string;
  joinUrl: string;
};

/**
 * Prepares a Firebase-backed WebRTC room (no Jitsi). Customer uses the same app;
 * agents open `joinUrl` to answer. Anonymous Firebase Auth is used (no login UI).
 *
 * Returns immediately so the UI never waits on Firestore (addDoc can hang on bad network/rules).
 * Agent notification runs in the background.
 */
export function startAgentCallSession(params: StartAgentCallParams): StartAgentCallResult {
  const roomId =
    typeof crypto !== 'undefined' && crypto.randomUUID
      ? crypto.randomUUID()
      : `My Near Shops-${sanitizeRoomSegment(params.storeOptimusId)}-${sanitizeRoomSegment(params.itemOptimusId)}-${Date.now()}`;

  const joinUrl = buildJoinCallUrl(roomId);

  if (isFirebaseAgentNotifyConfigured()) {
    void (async () => {
      try {
        await ensureFirebaseAnonymousAuth();
        await pushAgentCallRequest({
          roomId,
          joinUrl,
          storeOptimusId: params.storeOptimusId,
          itemOptimusId: params.itemOptimusId,
          storeName: params.storeName,
          itemName: params.itemName,
        });
      } catch {
        /* Firestore/rules/network — call still works via join URL */
      }
    })();
  }

  return { roomId, joinUrl };
}
