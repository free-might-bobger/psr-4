/**
 * Firebase Firestore: pushes a real-time "agent call" request so dashboards/agents
 * can listen with onSnapshot on collection `agent_calls`.
 *
 * Configure VITE_FIREBASE_* in .env (see .env.example).
 * Enable Anonymous auth + Firestore rules for `agent_calls` and `webrtc_calls`.
 */
import { getFirestore, collection, addDoc, serverTimestamp } from 'firebase/firestore';
import { getFirebaseApp } from './firebaseCore';

export { isFirebaseConfigured as isFirebaseAgentNotifyConfigured } from './firebaseCore';

export type AgentCallPayload = {
  roomId: string;
  /** Full URL for an agent to answer the same WebRTC session */
  joinUrl: string;
  storeOptimusId: string | number;
  itemOptimusId: string | number;
  storeName?: string;
  itemName?: string;
};

export async function pushAgentCallRequest(payload: AgentCallPayload): Promise<boolean> {
  const fb = getFirebaseApp();
  if (!fb) {
    return false;
  }
  const db = getFirestore(fb);
  await addDoc(collection(db, 'agent_calls'), {
    ...payload,
    status: 'pending',
    source: 'public_store_item',
    createdAt: serverTimestamp(),
  });
  return true;
}
