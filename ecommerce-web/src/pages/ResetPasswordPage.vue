<template>
    <div class="reset-page">
        <div class="reset-split">

            <!-- Left Brand Panel -->
            <div class="brand-panel">
                <div class="brand-bg">
                    <div class="brand-orb orb-1"></div>
                    <div class="brand-orb orb-2"></div>
                    <div class="brand-orb orb-3"></div>
                    <div class="brand-grid"></div>
                </div>
                <div class="brand-content">
                    <router-link to="/" class="brand-logo-link">
                        <div class="brand-icon-wrap">
                            <q-icon name="vpn_key" size="26px" color="white" />
                        </div>
                        <span class="brand-name">My Near Shops</span>
                    </router-link>
                    <h2 class="brand-headline">Create a new<br><span class="brand-accent">password</span></h2>
                    <p class="brand-desc">Choose a strong password you haven't used before. A secure password keeps your
                        account and orders safe.</p>
                    <div class="brand-tips">
                        <div class="brand-tip">
                            <div class="bt-icon"><q-icon name="check_circle" size="16px" color="white" /></div>
                            <span>At least 8 characters</span>
                        </div>
                        <div class="brand-tip">
                            <div class="bt-icon"><q-icon name="check_circle" size="16px" color="white" /></div>
                            <span>Mix of letters and numbers</span>
                        </div>
                        <div class="brand-tip">
                            <div class="bt-icon"><q-icon name="check_circle" size="16px" color="white" /></div>
                            <span>One special character</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Form Panel -->
            <div class="form-panel">
                <div class="form-inner">
                    <div class="form-breadcrumbs">
                        <BreadCrumbsWrapper
                            :bread-crumbs="[{ name: 'Login', path: '/login' }, { name: 'Reset Password', path: '' }]" />
                    </div>

                    <!-- Success state -->
                    <div v-if="resetComplete" class="success-state">
                        <div class="success-icon-wrap">
                            <q-icon name="lock_reset" size="36px" color="white" />
                        </div>
                        <h1 class="form-title">Password updated!</h1>
                        <p class="form-subtitle">Your password has been reset successfully. You can now sign in with
                            your new password.</p>
                        <router-link to="/login" class="back-link">
                            <q-icon name="arrow_forward" size="16px" class="q-mr-xs" />
                            Sign in to your account
                        </router-link>
                    </div>

                    <!-- Reset form -->
                    <div v-else>
                        <div class="form-header">
                            <div class="form-header-icon">
                                <q-icon name="vpn_key" size="24px" color="white" />
                            </div>
                            <div>
                                <h1 class="form-title">Set new password</h1>
                                <p class="form-subtitle">Enter your email and a new password below</p>
                            </div>
                        </div>

                        <q-form class="reset-form" @submit.prevent="submitResetPassword">
                            <div class="field-group">
                                <label class="field-label">Email address</label>
                                <q-input v-model="email" outlined dense type="email" placeholder="you@example.com"
                                    class="reset-input" lazy-rules autocomplete="email" :rules="[
                                        (val) => !!val?.trim() || 'Email is required',
                                        (val) => /.+@.+\..+/.test(val) || 'Enter a valid email address',
                                    ]">
                                    <template #prepend><q-icon name="email" size="18px" color="grey-5" /></template>
                                </q-input>
                            </div>

                            <div class="field-group">
                                <label class="field-label">New password</label>
                                <q-input v-model="password" outlined dense :type="showPassword ? 'text' : 'password'"
                                    placeholder="Enter a new password" class="reset-input" lazy-rules
                                    autocomplete="new-password" :rules="[
                                        (val) => !!val || 'Password is required',
                                        (val) => val.length >= 8 || 'Password must be at least 8 characters',
                                        (val) => /[a-zA-Z]/.test(val) && /[0-9]/.test(val) || 'Password must include letters and numbers',
                                    ]">
                                    <template #prepend><q-icon name="lock" size="18px" color="grey-5" /></template>
                                    <template #append>
                                        <q-btn flat round dense size="sm" color="grey-6" tabindex="-1"
                                            :icon="showPassword ? 'visibility_off' : 'visibility'"
                                            @click="showPassword = !showPassword" />
                                    </template>
                                </q-input>
                            </div>

                            <div class="field-group">
                                <label class="field-label">Confirm password</label>
                                <q-input v-model="confirmPassword" outlined dense
                                    :type="showConfirmPassword ? 'text' : 'password'"
                                    placeholder="Re-enter your new password" class="reset-input" lazy-rules
                                    autocomplete="new-password" :rules="[
                                        (val) => !!val || 'Please confirm your password',
                                        (val) => val === password || 'Passwords do not match',
                                    ]">
                                    <template #prepend><q-icon name="lock_clock" size="18px"
                                            color="grey-5" /></template>
                                    <template #append>
                                        <q-btn flat round dense size="sm" color="grey-6" tabindex="-1"
                                            :icon="showConfirmPassword ? 'visibility_off' : 'visibility'"
                                            @click="showConfirmPassword = !showConfirmPassword" />
                                    </template>
                                </q-input>
                            </div>

                            <button type="submit" class="submit-btn" :disabled="isSubmitting">
                                <q-spinner v-if="isSubmitting" size="18px" color="white" class="q-mr-sm" />
                                <q-icon v-else name="save" size="18px" class="q-mr-sm" />
                                {{ isSubmitting ? 'Resetting…' : 'Reset Password' }}
                            </button>
                        </q-form>

                        <div class="form-footer">
                            Didn't receive a link?
                            <router-link to="/forgot-password" class="login-link">Request again</router-link>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRoute } from 'vue-router';
import { axios } from 'src/boot/axios';
import { useQuasar } from 'quasar';
import BreadCrumbsWrapper from 'src/components/BreadCrumbsWrapper.vue';

const $q = useQuasar();
const route = useRoute();

const email = ref('');
const password = ref('');
const confirmPassword = ref('');
const showPassword = ref(false);
const showConfirmPassword = ref(false);
const isSubmitting = ref(false);
const resetComplete = ref(false);

const token = route.params.code as string;

const submitResetPassword = async () => {
    isSubmitting.value = true;
    try {
        await axios.post('reset-password', {
            email: email.value.trim(),
            token: token,
            password: password.value,
        });
        resetComplete.value = true;
    } catch (err: unknown) {
        const ax = err as { response?: { data?: { message?: string } } };
        $q.notify({
            message: ax.response?.data?.message ?? 'Failed to reset password. Please try again.',
            type: 'negative',
            position: 'top',
            icon: 'error',
        });
    } finally {
        isSubmitting.value = false;
    }
};
</script>

<style scoped lang="scss">
// ── Layout ────────────────────────────────────────────────────────────────
.reset-page {
    min-height: calc(100vh - 68px);
    display: flex;
}

.reset-split {
    display: flex;
    width: 100%;
    min-height: calc(100vh - 68px);
}

// ── Brand Panel ───────────────────────────────────────────────────────────
.brand-panel {
    flex: 0 0 44%;
    position: relative;
    background: linear-gradient(145deg, #1e1b4b 0%, #312e81 45%, #4c1d95 100%);
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
}

.brand-bg {
    position: absolute;
    inset: 0;
}

.brand-orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(70px);
    animation: orbFloat 10s ease-in-out infinite;

    &.orb-1 {
        width: 420px;
        height: 420px;
        background: rgba(139, 92, 246, 0.4);
        top: -130px;
        right: -80px;
    }

    &.orb-2 {
        width: 300px;
        height: 300px;
        background: rgba(99, 102, 241, 0.3);
        bottom: -80px;
        left: -60px;
        animation-delay: 3s;
    }

    &.orb-3 {
        width: 200px;
        height: 200px;
        background: rgba(251, 191, 36, 0.14);
        top: 50%;
        left: 30%;
        animation-delay: 6s;
    }
}

.brand-grid {
    position: absolute;
    inset: 0;
    background-image:
        linear-gradient(rgba(255, 255, 255, 0.04) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255, 255, 255, 0.04) 1px, transparent 1px);
    background-size: 48px 48px;
}

@keyframes orbFloat {

    0%,
    100% {
        transform: translate(0, 0) scale(1);
    }

    33% {
        transform: translate(16px, -24px) scale(1.04);
    }

    66% {
        transform: translate(-12px, 16px) scale(0.96);
    }
}

.brand-content {
    position: relative;
    z-index: 1;
    padding: 52px;
    width: 100%;
}

.brand-logo-link {
    display: flex;
    align-items: center;
    gap: 14px;
    text-decoration: none;
    margin-bottom: 52px;
}

.brand-icon-wrap {
    width: 46px;
    height: 46px;
    border-radius: 13px;
    background: rgba(255, 255, 255, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.brand-name {
    font-size: 20px;
    font-weight: 800;
    color: white;
    letter-spacing: -0.4px;
}

.brand-headline {
    font-size: 46px;
    font-weight: 900;
    color: white;
    line-height: 1.1;
    letter-spacing: -1.5px;
    margin: 0 0 20px;
}

.brand-accent {
    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.brand-desc {
    font-size: 16px;
    color: rgba(255, 255, 255, 0.62);
    line-height: 1.7;
    margin: 0 0 40px;
    max-width: 340px;
}

.brand-tips {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.brand-tip {
    display: flex;
    align-items: center;
    gap: 14px;
    color: rgba(255, 255, 255, 0.82);
    font-size: 15px;
    font-weight: 500;
}

.bt-icon {
    width: 34px;
    height: 34px;
    border-radius: 9px;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.14);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

// ── Form Panel ────────────────────────────────────────────────────────────
.form-panel {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 48px 28px;
    background: #f4f5f7;
    overflow-y: auto;
}

.form-inner {
    width: 100%;
    max-width: 440px;
}

.form-breadcrumbs {
    margin-bottom: 28px;
}

// ── Form Header ───────────────────────────────────────────────────────────
.form-header {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 32px;
}

.form-header-icon {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    background: linear-gradient(135deg, #312e81 0%, #6d28d9 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 8px 20px rgba(79, 70, 229, 0.3);
}

.form-title {
    font-size: 26px;
    font-weight: 800;
    color: #111827;
    margin: 0 0 4px;
    letter-spacing: -0.5px;
    line-height: 1.2;
}

.form-subtitle {
    font-size: 14px;
    color: #6b7280;
    margin: 0;
    font-weight: 500;
    line-height: 1.6;
}

// ── Form Fields ───────────────────────────────────────────────────────────
.reset-form {
    display: flex;
    flex-direction: column;
    gap: 16px;
    margin-bottom: 24px;
}

.field-group {
    display: flex;
    flex-direction: column;
    gap: 7px;
}

.field-label {
    font-size: 12px;
    font-weight: 700;
    color: #374151;
    text-transform: uppercase;
    letter-spacing: 0.6px;
}

.reset-input {
    :deep(.q-field__control) {
        border-radius: 12px;
        background: white;
        font-size: 15px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
        transition: box-shadow 0.2s ease;

        &:hover {
            box-shadow: 0 2px 10px rgba(99, 102, 241, 0.12);
        }
    }

    :deep(.q-field--focused .q-field__control) {
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.18), 0 1px 4px rgba(0, 0, 0, 0.06);
    }
}

.submit-btn {
    width: 100%;
    height: 52px;
    border: none;
    border-radius: 13px;
    background: linear-gradient(135deg, #312e81 0%, #6d28d9 100%);
    color: white;
    font-size: 15px;
    font-weight: 800;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 6px 20px rgba(79, 70, 229, 0.35);
    transition: all 0.25s ease;
    letter-spacing: 0.3px;

    &:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 10px 28px rgba(79, 70, 229, 0.45);
    }

    &:disabled {
        opacity: 0.65;
        cursor: not-allowed;
    }
}

// ── Footer ────────────────────────────────────────────────────────────────
.form-footer {
    text-align: center;
    font-size: 14px;
    color: #6b7280;
    font-weight: 500;
    margin-top: 24px;
}

.login-link {
    color: #4c1d95;
    text-decoration: none;
    font-weight: 700;
    margin-left: 4px;
    transition: color 0.2s ease;

    &:hover {
        color: #6d28d9;
        text-decoration: underline;
    }
}

// ── Success State ─────────────────────────────────────────────────────────
.success-state {
    text-align: center;
    padding: 20px 0;
}

.success-icon-wrap {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #312e81 0%, #6d28d9 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 24px;
    box-shadow: 0 12px 32px rgba(79, 70, 229, 0.35);
}

.back-link {
    display: inline-flex;
    align-items: center;
    margin-top: 24px;
    color: #4c1d95;
    text-decoration: none;
    font-size: 14px;
    font-weight: 700;
    transition: color 0.2s ease;

    &:hover {
        color: #6d28d9;
        text-decoration: underline;
    }
}

// ── Responsive ────────────────────────────────────────────────────────────
@media (max-width: 900px) {
    .brand-panel {
        display: none;
    }

    .form-panel {
        padding: 36px 20px;
        background: white;
    }
}

@media (max-width: 480px) {
    .form-panel {
        padding: 28px 16px;
    }

    .form-title {
        font-size: 22px;
    }
}
</style>
