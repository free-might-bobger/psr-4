<template>
  <div class="join-call-page q-pa-md">
    <q-card flat bordered class="store-page-container">
      <q-card-section>
        <div class="text-h6 q-mb-sm">Answer call — Agent</div>
        <p class="text-body2 text-grey-7 q-mb-md">
          Open this link from the agent dashboard or notification. Allow camera and microphone when prompted.
        </p>
        <div v-if="errorMsg" class="text-negative q-mb-md">{{ errorMsg }}</div>
        <div class="row q-col-gutter-sm">
          <div class="col-12 col-md-6">
            <div class="text-caption text-grey-7 q-mb-xs">You</div>
            <video ref="localVideoRef" class="call-video" autoplay playsinline muted />
          </div>
          <div class="col-12 col-md-6">
            <div class="text-caption text-grey-7 q-mb-xs">Customer</div>
            <video ref="remoteVideoRef" class="call-video" autoplay playsinline />
          </div>
        </div>
      </q-card-section>
      <q-card-actions align="right">
        <q-btn color="negative" flat label="Hang up" icon="call_end" @click="hangUp" />
      </q-card-actions>
    </q-card>
  </div>
</template>

<script setup lang="ts">
import { onMounted, onUnmounted, ref, nextTick } from 'vue';
import { useRoute } from 'vue-router';
import { isFirebaseConfigured } from 'src/helpers/firebaseCore';

const route = useRoute();
const localVideoRef = ref<HTMLVideoElement | null>(null);
const remoteVideoRef = ref<HTMLVideoElement | null>(null);
const errorMsg = ref('');
let handles: { hangUp: () => void } | null = null;

const hangUp = () => {
  handles?.hangUp();
  handles = null;
};

onMounted(async () => {
  if (!isFirebaseConfigured()) {
    errorMsg.value = 'Firebase is not configured. Add VITE_FIREBASE_* to your .env file.';
    return;
  }
  const roomId = route.params.roomId as string;
  if (!roomId) {
    errorMsg.value = 'Invalid call link.';
    return;
  }
  await nextTick();
  let local = localVideoRef.value;
  let remote = remoteVideoRef.value;
  if (!local || !remote) {
    await new Promise((r) => setTimeout(r, 50));
    local = localVideoRef.value;
    remote = remoteVideoRef.value;
  }
  if (!local || !remote) {
    errorMsg.value = 'Could not attach video. Refresh the page.';
    return;
  }
  // WebRTC call functionality removed - webrtcFirebaseCall.ts deleted
  errorMsg.value = 'Call functionality has been removed.';
});

onUnmounted(() => {
  hangUp();
});
</script>

<style scoped lang="scss">
.join-call-page {
  max-width: 900px;
  margin: 0 auto;
}

.call-video {
  width: 100%;
  max-height: 320px;
  background: #111;
  border-radius: 8px;
  object-fit: cover;
}
</style>
