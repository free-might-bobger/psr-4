import { initializeApp, getApps, type FirebaseApp, type FirebaseOptions } from 'firebase/app';

/**
 * Shared Firebase app instance (Firestore + Auth). Configure VITE_FIREBASE_* in .env.
 */
export function getFirebaseApp(): FirebaseApp | null {
  const apiKey = import.meta.env.VITE_FIREBASE_API_KEY as string | undefined;
  if (!apiKey) {
    return null;
  }
  if (getApps().length > 0) {
    return getApps()[0];
  }
  const config: FirebaseOptions = {
    apiKey,
    authDomain: import.meta.env.VITE_FIREBASE_AUTH_DOMAIN as string,
    projectId: import.meta.env.VITE_FIREBASE_PROJECT_ID as string,
    storageBucket: import.meta.env.VITE_FIREBASE_STORAGE_BUCKET as string,
    messagingSenderId: import.meta.env.VITE_FIREBASE_MESSAGING_SENDER_ID as string,
    appId: import.meta.env.VITE_FIREBASE_APP_ID as string,
  };
  const measurementId = import.meta.env.VITE_FIREBASE_MEASUREMENT_ID as string | undefined;
  if (measurementId) {
    config.measurementId = measurementId;
  }
  return initializeApp(config);
}

export function isFirebaseConfigured(): boolean {
  return Boolean(import.meta.env.VITE_FIREBASE_API_KEY);
}
