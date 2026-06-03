<template>
    <div class="invite-page-container">
        <div class="invite-card">
            <div class="invite-header">
                <q-icon name="person_add" size="48px" color="primary" class="q-mb-md" />
                <h2 class="invite-title">Invite User</h2>
                <p class="invite-subtitle">Enter the email address of the user you want to invite</p>
            </div>

            <q-form @submit="handleInvite" class="invite-form">
                <q-input v-model="email" label="Email Address" type="email" outlined dense :rules="[isValidEmail]"
                    lazy-rules class="q-mb-md">
                    <template v-slot:prepend>
                        <q-icon name="email" />
                    </template>
                </q-input>

                <div class="form-actions">
                    <q-btn flat label="Cancel" color="grey-7" class="cancel-btn"
                        :to="`/dashboard/my-stores/${route.params.id}/users`" />
                    <q-btn type="submit" label="Send Invite" color="primary" unelevated :loading="loading"
                        class="full-width" size="md" />
                </div>
            </q-form>

        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRoute } from 'vue-router';
import { useQuasar } from 'quasar';
import { axios } from 'src/boot/axios';

const route = useRoute();
const $q = useQuasar();

const email = ref('');
const loading = ref(false);

const isValidEmail = (val: string) => {
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailPattern.test(val) || 'Please enter a valid email address';
};

const handleInvite = async () => {
    if (!email.value) {
        return;
    }

    loading.value = true;
    try {
        await axios.post('invite-user', {
            email: email.value,
            store_id: route.params.id,
        });

        $q.notify({
            message: 'Invitation sent successfully!',
            type: 'positive',
            position: 'top',
        });

        email.value = '';
    } catch (error) {
        const errorMessage = error && typeof error === 'object' && 'response' in error && error.response && typeof error.response === 'object' && 'data' in error.response && error.response.data && typeof error.response.data === 'object' && 'message' in error.response.data
            ? (error.response.data as { message: string }).message
            : 'Failed to send invitation. Please try again.';

        $q.notify({
            message: errorMessage,
            type: 'negative',
            position: 'top',
        });
    } finally {
        loading.value = false;
    }
};
</script>

<style scoped lang="scss">
.invite-page-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 60vh;
    padding: 24px;
}

.invite-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    padding: 40px;
    width: 100%;
    max-width: 100%;
}

.invite-header {
    text-align: center;
    margin-bottom: 32px;
}

.invite-title {
    font-size: 24px;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0 0 8px 0;
}

.invite-subtitle {
    font-size: 14px;
    color: #666;
    margin: 0;
}

.invite-form {
    margin-bottom: 24px;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

.form-actions {
    margin-top: 24px;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
    display: flex;
    gap: 12px;

    .q-btn {
        flex: 1;
    }
}

.cancel-btn {
    flex: 1;
}

@media (max-width: 768px) {
    .invite-card {
        padding: 24px;
    }

    .invite-title {
        font-size: 20px;
    }

    .invite-form,
    .form-actions {
        max-width: 100%;
    }
}
</style>
