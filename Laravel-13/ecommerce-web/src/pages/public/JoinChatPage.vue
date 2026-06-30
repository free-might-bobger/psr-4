<template>
  <div class="join-chat-page q-pa-md">
    <q-card flat bordered class="store-page-container">
      <q-card-section>
        <div class="text-h6 q-mb-sm">Chat with customer — Agent</div>
        <p class="text-body2 text-grey-7 q-mb-md">
          Reply to the customer in real time. This page works without an extra login form.
        </p>
        <div v-if="errorMsg" class="text-negative q-mb-md">{{ errorMsg }}</div>

        <div class="chat-box q-pa-sm q-mb-md" ref="chatBoxRef">
          <div v-for="msg in chatMessages" :key="msg.id" class="chat-row q-mb-sm"
            :class="msg.sender === 'agent' ? 'mine' : 'theirs'">
            <div class="bubble">
              <div class="text-caption text-grey-7 q-mb-xs">
                {{ msg.sender === 'agent' ? 'You' : 'Customer' }}
              </div>
              <div class="text-body2">{{ msg.text }}</div>
            </div>
          </div>
        </div>

        <q-input v-model="draftMessage" outlined autogrow type="textarea" placeholder="Type a message..."
          @keyup.enter.exact.prevent="sendMessage" />
      </q-card-section>
      <q-card-actions align="right">
        <q-btn flat color="primary" label="Send" icon="send" :loading="sending" @click="sendMessage" />
      </q-card-actions>
    </q-card>
  </div>
</template>

<script setup lang="ts">
import { nextTick, onMounted, onUnmounted, ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import { isFirebaseConfigured } from 'src/helpers/firebaseCore';
import {
  sendChatMessage,
  subscribeChatMessages,
  type ChatMessage,
} from 'src/helpers/firebaseAgentChat';

const route = useRoute();
const errorMsg = ref('');
const roomId = ref('');
const draftMessage = ref('');
const sending = ref(false);
const chatMessages = ref<ChatMessage[]>([]);
const chatBoxRef = ref<HTMLElement | null>(null);
let unsubChat: (() => void) | null = null;

const scrollToBottom = async () => {
  await nextTick();
  const box = chatBoxRef.value;
  if (!box) return;
  box.scrollTop = box.scrollHeight;
};

watch(chatMessages, () => {
  void scrollToBottom();
});

const sendMessage = async () => {
  if (!roomId.value || !draftMessage.value.trim()) return;
  sending.value = true;
  try {
    await sendChatMessage(roomId.value, 'agent', draftMessage.value);
    draftMessage.value = '';
  } catch (e: unknown) {
    errorMsg.value = e instanceof Error ? e.message : 'Could not send message.';
  } finally {
    sending.value = false;
  }
};

onMounted(async () => {
  if (!isFirebaseConfigured()) {
    errorMsg.value = 'Firebase is not configured. Add VITE_FIREBASE_* to your .env file.';
    return;
  }
  roomId.value = (route.params.roomId as string) || '';
  if (!roomId.value) {
    errorMsg.value = 'Invalid chat link.';
    return;
  }
  try {
    unsubChat = subscribeChatMessages(
      roomId.value,
      (messages) => {
        chatMessages.value = messages;
      },
      (message) => {
        errorMsg.value = message;
      }
    );
  } catch (e: unknown) {
    errorMsg.value = e instanceof Error ? e.message : 'Could not start chat.';
  }
});

onUnmounted(() => {
  unsubChat?.();
  unsubChat = null;
});
</script>

<style scoped lang="scss">
.join-chat-page {
  max-width: 900px;
  margin: 0 auto;
}

.chat-box {
  height: 55vh;
  max-height: 600px;
  overflow-y: auto;
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  background: #fafafa;
}

.chat-row {
  display: flex;
}

.chat-row.mine {
  justify-content: flex-end;
}

.chat-row.theirs {
  justify-content: flex-start;
}

.bubble {
  max-width: min(80%, 520px);
  padding: 8px 10px;
  border-radius: 10px;
  background: #fff;
  border: 1px solid #ececec;
}

.chat-row.mine .bubble {
  background: #eaf4ff;
  border-color: #d3e7ff;
}
</style>
