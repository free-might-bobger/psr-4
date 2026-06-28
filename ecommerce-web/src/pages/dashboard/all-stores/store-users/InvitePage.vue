<template>
    <div class="invite-page">

        <!-- Hero Header -->
        <div class="page-hero q-mb-xl">
            <div class="hero-accent-overlay"></div>
            <div class="hero-inner">
                <router-link :to="`/dashboard/my-stores/${route.params.id}/store-users`" class="back-btn">
                    <q-icon name="arrow_back" size="20px" />
                </router-link>
                <div class="hero-icon-wrap">
                    <q-icon name="person_add" size="28px" color="white" />
                </div>
                <div class="hero-text">
                    <h1 class="hero-title">Invite User</h1>
                    <div class="hero-subtitle">Send an invitation to collaborate on your store</div>
                </div>
            </div>
        </div>

        <!-- Invite Form Card -->
        <div class="form-card">
            <div class="form-card-header">
                <div class="form-header-icon">
                    <q-icon name="email" size="22px" color="white" />
                </div>
                <div>
                    <div class="form-header-title">Email Address</div>
                    <div class="form-header-subtitle">Enter the email of the user you want to invite</div>
                </div>
            </div>

            <q-form @submit="handleInvite" class="invite-form">
                <div class="input-wrap">
                    <q-icon name="email" size="20px" class="input-icon" />
                    <input v-model="email" type="email" placeholder="colleague@example.com" class="dark-input"
                        required />
                </div>

                <div class="form-actions">
                    <router-link :to="`/dashboard/my-stores/${route.params.id}/store-users`"
                        class="action-btn action-cancel">
                        <q-icon name="close" size="18px" />
                        <span>Cancel</span>
                    </router-link>
                    <button type="submit" class="action-btn action-submit" :disabled="loading">
                        <q-icon v-if="!loading" name="send" size="18px" />
                        <q-spinner-dots v-else size="18px" color="white" />
                        <span>Send Invite</span>
                    </button>
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
// ── Dark theme tokens (matching DashboardLayout / ProfilePage) ───────────────
$dark-base: #0f172a;
$dark-card: #1e293b;
$dark-elevated: #273549;
$border: rgba(255, 255, 255, 0.08);
$accent: #6366f1;
$accent-2: #7c3aed;
$green: #10b981;
$red: #ef4444;
$white: #ffffff;
$muted: rgba(255, 255, 255, 0.5);
$muted-2: rgba(255, 255, 255, 0.3);

// ── Page Container ───────────────────────────────────────────────────────────
.invite-page {
    max-width: 680px;
    margin: 0 auto;
    padding: 28px 24px 60px;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    color: $white;
}

// ── Hero Header ──────────────────────────────────────────────────────────────
.page-hero {
    position: relative;
    background: $dark-card;
    border-radius: 20px;
    border: 1px solid $border;
    box-shadow: 0 8px 40px rgba(0, 0, 0, 0.3);
    overflow: hidden;
}

.hero-accent-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba($accent, 0.18) 0%, rgba($accent-2, 0.10) 60%, transparent 100%);
    pointer-events: none;
}

.hero-inner {
    position: relative;
    display: flex;
    align-items: center;
    padding: 32px 36px;
    gap: 16px;
    flex-wrap: wrap;
}

.back-btn {
    width: 44px;
    height: 44px;
    border-radius: 14px;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid $border;
    color: $white;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: all 0.25s ease;
    flex-shrink: 0;

    &:hover {
        background: rgba(255, 255, 255, 0.14);
        transform: translateX(-3px);
    }
}

.hero-icon-wrap {
    width: 56px;
    height: 56px;
    border-radius: 16px;
    background: linear-gradient(135deg, $accent 0%, $accent-2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 24px rgba($accent, 0.4);
    flex-shrink: 0;
}

.hero-text {
    flex: 1;
    min-width: 0;
}

.hero-title {
    font-size: 26px;
    font-weight: 800;
    color: $white !important;
    margin: 0 0 4px;
    letter-spacing: -0.3px;
    line-height: 1.2;
}

.hero-subtitle {
    font-size: 14px;
    color: $muted;
    font-weight: 500;
}

// ── Form Card ────────────────────────────────────────────────────────────────
.form-card {
    background: $dark-card;
    border: 1px solid $border;
    border-radius: 20px;
    box-shadow: 0 8px 40px rgba(0, 0, 0, 0.25);
    overflow: hidden;
}

.form-card-header {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 24px 32px;
    background: $dark-elevated;
    border-bottom: 1px solid $border;
}

.form-header-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: linear-gradient(135deg, $accent, $accent-2);
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 6px 16px rgba($accent, 0.3);
    flex-shrink: 0;
}

.form-header-title {
    font-size: 16px;
    font-weight: 700;
    color: $white;
}

.form-header-subtitle {
    font-size: 13px;
    color: $muted;
    margin-top: 2px;
}

.invite-form {
    padding: 32px;
}

// ── Dark Input ───────────────────────────────────────────────────────────────
.input-wrap {
    position: relative;
}

.input-icon {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: $muted !important;
    pointer-events: none;
}

.dark-input {
    width: 100%;
    height: 52px;
    border-radius: 14px;
    border: 1px solid $border;
    background: $dark-elevated;
    padding: 0 18px 0 50px;
    font-size: 15px;
    color: $white;
    outline: none;
    transition: all 0.2s ease;
    font-family: inherit;

    &::placeholder {
        color: $muted-2;
    }

    &:focus {
        border-color: rgba($accent, 0.5);
        box-shadow: 0 0 0 3px rgba($accent, 0.12);
    }
}

// ── Form Actions ─────────────────────────────────────────────────────────────
.form-actions {
    display: flex;
    gap: 12px;
    margin-top: 28px;
}

.action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 14px 28px;
    border-radius: 14px;
    font-size: 14px;
    font-weight: 700;
    border: none;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.25s ease;
    flex: 1;

    &.action-cancel {
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid $border;
        color: $muted;

        &:hover {
            background: rgba(255, 255, 255, 0.1);
            color: $white;
            border-color: rgba(255, 255, 255, 0.15);
        }
    }

    &.action-submit {
        background: linear-gradient(135deg, $accent 0%, $accent-2 100%);
        color: $white;
        box-shadow: 0 6px 24px rgba($accent, 0.35);

        &:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 32px rgba($accent, 0.5);
        }

        &:active {
            transform: translateY(0);
        }

        &:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
    }
}

// ── Responsive ───────────────────────────────────────────────────────────────
@media (max-width: 768px) {
    .invite-page {
        padding: 16px 12px 48px;
    }

    .hero-inner {
        padding: 24px 20px;
        gap: 12px;
    }

    .hero-title {
        font-size: 22px;
    }

    .form-card-header {
        padding: 20px 20px;
    }

    .invite-form {
        padding: 24px 20px;
    }

    .form-actions {
        flex-direction: column;
    }
}

@media (max-width: 600px) {
    .invite-page {
        padding: 10px 8px 40px;
    }

    .hero-inner {
        padding: 20px 16px;
        gap: 10px;
    }

    .hero-icon-wrap {
        width: 46px;
        height: 46px;
        border-radius: 13px;
    }

    .hero-title {
        font-size: 20px;
    }

    .invite-form {
        padding: 20px 16px;
    }
}
</style>
