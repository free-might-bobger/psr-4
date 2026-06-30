<template>
  <div class="login-page-container">
    <div class="login-split">

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
            <BiliscartLogo :size="52" />
            <span class="brand-name">My Near Shops</span>
          </router-link>
          <h2 class="brand-headline">Shop local,<br><span class="brand-accent">live better.</span></h2>
          <p class="brand-desc">Discover hundreds of local stores, compare prices, and get same-day delivery — all in
            one place.</p>
          <div class="brand-features">
            <div class="brand-feat">
              <div class="bf-icon"><q-icon name="storefront" size="16px" color="white" /></div><span>500+ verified
                shops</span>
            </div>
            <div class="brand-feat">
              <div class="bf-icon"><q-icon name="bolt" size="16px" color="white" /></div><span>Fast same-day
                delivery</span>
            </div>
            <div class="brand-feat">
              <div class="bf-icon"><q-icon name="verified_user" size="16px" color="white" /></div><span>Safe &amp;
                secure shopping</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Form Panel -->
      <div class="form-panel">
        <div class="form-inner">
          <div class="login-breadcrumbs">
            <BreadCrumbsWrapper :bread-crumbs="[{ name: 'Login', path: '' }]" />
          </div>

          <div class="form-header">
            <div class="form-header-icon">
              <q-icon name="lock_open" size="24px" color="white" />
            </div>
            <div>
              <h1 class="form-title">Welcome back</h1>
              <p class="form-subtitle">Sign in to continue to your account</p>
            </div>
          </div>

          <q-form class="login-form" @submit.prevent="loginWithEmail">
            <div class="field-group">
              <label class="field-label">Email address</label>
              <q-input v-model="email" outlined dense type="email" placeholder="you@example.com" class="login-input"
                lazy-rules autocomplete="email"
                :rules="[(val) => !!val?.trim() || 'Email is required', (val) => /.+@.+\..+/.test(val) || 'Enter a valid email']">
                <template #prepend><q-icon name="email" size="18px" color="grey-5" /></template>
              </q-input>
            </div>
            <div class="field-group">
              <label class="field-label">Password</label>
              <q-input v-model="password" outlined dense :type="showPassword ? 'text' : 'password'"
                placeholder="Enter your password" class="login-input" lazy-rules autocomplete="current-password"
                :rules="[(val) => !!val || 'Password is required']">
                <template #prepend><q-icon name="lock" size="18px" color="grey-5" /></template>
                <template #append>
                  <q-btn flat round dense size="sm" color="grey-6"
                    :icon="showPassword ? 'visibility_off' : 'visibility'" tabindex="-1"
                    @click="showPassword = !showPassword" />
                </template>
              </q-input>
            </div>
            <div class="forgot-row">
              <router-link to="/forgot-password" class="forgot-link">Forgot password?</router-link>
            </div>
            <q-btn type="submit" no-caps unelevated class="submit-btn full-width" size="lg"
              :loading="isEmailSubmitting">
              <div class="submit-inner"><q-icon name="login" size="18px" /><span>Sign in to account</span></div>
            </q-btn>
          </q-form>

          <div class="or-divider">
            <span class="or-line"></span><span class="or-text">or continue with</span><span class="or-line"></span>
          </div>

          <div class="social-buttons">
            <button class="social-btn google-btn" :disabled="isEmailSubmitting" @click="loginWithGoogle">
              <span class="sb-icon">
                <svg width="18" height="18" viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg">
                  <path
                    d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908C16.658 14.251 17.64 11.945 17.64 9.2z"
                    fill="#4285F4" />
                  <path
                    d="M9 18c2.43 0 4.467-.806 5.956-2.18l-2.908-2.259c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332C2.438 15.983 5.482 18 9 18z"
                    fill="#34A853" />
                  <path
                    d="M3.964 10.71A5.41 5.41 0 0 1 3.682 9c0-.593.102-1.17.282-1.71V4.958H.957A8.996 8.996 0 0 0 0 9c0 1.452.348 2.827.957 4.042l3.007-2.332z"
                    fill="#FBBC05" />
                  <path
                    d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0 5.482 0 2.438 2.017.957 4.958L3.964 6.29C4.672 4.163 6.656 3.58 9 3.58z"
                    fill="#EA4335" />
                </svg>
              </span>
              <span>{{ isGoogleRedirecting ? 'Redirecting…' : 'Continue with Google' }}</span>
            </button>
            <button class="social-btn facebook-btn" disabled>
              <span class="sb-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path
                    d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047V9.41c0-3.025 1.792-4.697 4.533-4.697 1.312 0 2.686.236 2.686.236v2.97h-1.513c-1.491 0-1.956.93-1.956 1.886v2.267h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z"
                    fill="#1877F2" />
                </svg>
              </span>
              <span>Continue with Facebook</span>
              <span class="soon-badge">Soon</span>
            </button>
          </div>

          <div class="trust-row">
            <div class="trust-chip"><q-icon name="lock" size="12px" color="positive" /><span>Secure login</span></div>
            <div class="trust-chip"><q-icon name="verified" size="12px" color="positive" /><span>Verified
                platform</span></div>
            <div class="trust-chip"><q-icon name="support_agent" size="12px" color="positive" /><span>24/7
                support</span></div>
          </div>

          <div class="form-footer">
            Don't have an account?
            <router-link to="/register" class="register-link">Create one free</router-link>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script lang="ts" setup>
import BreadCrumbsWrapper from 'src/components/BreadCrumbsWrapper.vue';
import { onMounted, ref } from 'vue';
import { axios } from 'src/boot/axios';
import { useQuasar } from 'quasar';
import { useRouter, useRoute } from 'vue-router';
import { useUserStore } from 'src/stores/user';
import type { ProfileState } from 'boot/interfaces';

const router = useRouter();
const route = useRoute();
const $q = useQuasar();
const userStore = useUserStore();

const email = ref('');
const password = ref('');
const showPassword = ref(false);
const isEmailSubmitting = ref(false);
const isFacebookRedirecting = ref(false);
const isGoogleRedirecting = ref(false);

const redirectTo = route.redirectedFrom?.fullPath;
const getFacebookLoginUrl = () => {
  return new URL('auth/facebook', axios.defaults.baseURL).toString();
};

const getGoogleLoginUrl = () => {
  return new URL('auth/google', axios.defaults.baseURL).toString();
};

const getQueryValue = (value: unknown): string => {
  if (Array.isArray(value)) {
    return value[0] ?? '';
  }

  return typeof value === 'string' ? value : '';
};

const getUserMenuFromQuery = (): ProfileState['userMenu'] => {
  const userMenu = getQueryValue(route.query.userMenu);

  if (!userMenu) {
    return [];
  }

  try {
    return JSON.parse(userMenu) as ProfileState['userMenu'];
  } catch (error) {
    return [];
  }
};

const applyAuthPayload = (profile: ProfileState, successMessage: string) => {
  userStore.setProfile(profile);
  userStore.setUser(profile);
  $q.notify({
    message: successMessage,
    type: 'positive',
    position: 'top',
    icon: 'check_circle',
  });
};

const finishFacebookLoginFromQuery = async (token: string) => {
  const socialProfile: ProfileState = {
    token,
    name: getQueryValue(route.query.name) || null,
    mobile: getQueryValue(route.query.mobile),
    optimus_id: Number(getQueryValue(route.query.optimus_id) || 0),
    userMenu: getUserMenuFromQuery(),
  };

  applyAuthPayload(socialProfile, 'Facebook login successful! Welcome back.');
  await router.replace(redirectTo || '/');
};

const finishGoogleLoginFromQuery = async (token: string) => {
  const socialProfile: ProfileState = {
    token,
    name: getQueryValue(route.query.name) || null,
    mobile: getQueryValue(route.query.mobile),
    optimus_id: Number(getQueryValue(route.query.optimus_id) || 0),
    userMenu: getUserMenuFromQuery(),
  };

  applyAuthPayload(socialProfile, 'Google login successful! Welcome back.');
  await router.replace(redirectTo || '/');
};

const loginWithEmail = async () => {
  isEmailSubmitting.value = true;
  try {
    const res = await axios.post('login', {
      email: email.value.trim(),
      password: password.value,
    });
    if (res.data?.success && res.data.data?.token) {
      const data = res.data.data as ProfileState;
      const profile: ProfileState = {
        token: data.token,
        name: data.name ?? null,
        mobile: data.mobile ?? '',
        optimus_id: Number(data.optimus_id ?? 0),
        userMenu: Array.isArray(data.userMenu) ? data.userMenu : (typeof data.userMenu === 'string' ? JSON.parse(data.userMenu) : []),
      };
      applyAuthPayload(profile, 'Signed in successfully. Welcome back.');
      await router.replace(redirectTo || '/');
    }
  } catch (err: unknown) {
    const ax = err as { response?: { data?: { message?: string } } };
    $q.notify({
      message: ax.response?.data?.message ?? 'Sign in failed. Please try again.',
      type: 'negative',
      position: 'top',
      icon: 'error',
    });
  } finally {
    isEmailSubmitting.value = false;
  }
};

const loginWithFacebook = () => {
  isFacebookRedirecting.value = true;
  window.location.href = getFacebookLoginUrl();
};

const loginWithGoogle = () => {
  isGoogleRedirecting.value = true;
  window.location.href = getGoogleLoginUrl();
};

onMounted(async () => {
  const token = getQueryValue(route.query.token);
  const error = getQueryValue(route.query.error);
  const provider = getQueryValue(route.query.provider);

  if (error) {
    $q.notify({
      message: error === 'facebook_email_required'
        ? 'Facebook did not return an email address for this account.'
        : error === 'google_email_required'
          ? 'Google did not return an email address for this account.'
          : 'Social login failed. Please try again.',
      type: 'negative',
      position: 'top',
      icon: 'error'
    });
  }

  if (!token) {
    isFacebookRedirecting.value = false;
    isGoogleRedirecting.value = false;
    return;
  }

  try {
    if (provider === 'google') {
      await finishGoogleLoginFromQuery(token);
    } else {
      await finishFacebookLoginFromQuery(token);
    }
  } finally {
    isFacebookRedirecting.value = false;
    isGoogleRedirecting.value = false;
  }
});
</script>

<style scoped lang="scss">
// ── Layout ────────────────────────────────────────────────────────────────────
.login-page-container {
  min-height: calc(100vh - 68px);
  display: flex;
}

.login-split {
  display: flex;
  width: 100%;
  min-height: calc(100vh - 68px);
}

// ── Brand Panel ───────────────────────────────────────────────────────────────
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

.brand-features {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.brand-feat {
  display: flex;
  align-items: center;
  gap: 14px;
  color: rgba(255, 255, 255, 0.82);
  font-size: 15px;
  font-weight: 500;
}

.bf-icon {
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

// ── Form Panel ────────────────────────────────────────────────────────────────
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

.login-breadcrumbs {
  margin-bottom: 28px;
}

// ── Form Header ───────────────────────────────────────────────────────────────
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
}

// ── Form Fields ───────────────────────────────────────────────────────────────
.login-form {
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

.login-input {
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

.forgot-row {
  display: flex;
  justify-content: flex-end;
  margin-top: -4px;
}

.forgot-link {
  font-size: 13px;
  font-weight: 700;
  color: #4c1d95;
  text-decoration: none;
  transition: color 0.2s ease;

  &:hover {
    color: #6d28d9;
    text-decoration: underline;
  }
}

.submit-btn {
  height: 52px;
  border-radius: 13px;
  background: linear-gradient(135deg, #312e81 0%, #6d28d9 100%);
  color: white;
  font-weight: 700;
  font-size: 15px;
  box-shadow: 0 6px 20px rgba(79, 70, 229, 0.35);
  transition: all 0.25s ease;
  margin-top: 4px;

  &:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 28px rgba(79, 70, 229, 0.45);
  }
}

.submit-inner {
  display: flex;
  align-items: center;
  gap: 8px;
}

// ── OR Divider ────────────────────────────────────────────────────────────────
.or-divider {
  display: flex;
  align-items: center;
  gap: 12px;
  margin: 24px 0;
}

.or-line {
  flex: 1;
  height: 1px;
  background: #e5e7eb;
}

.or-text {
  font-size: 12px;
  color: #9ca3af;
  font-weight: 600;
  white-space: nowrap;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

// ── Social Buttons ────────────────────────────────────────────────────────────
.social-buttons {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-bottom: 24px;
}

.social-btn {
  display: flex;
  align-items: center;
  gap: 12px;
  width: 100%;
  height: 50px;
  padding: 0 20px;
  border-radius: 13px;
  border: 1.5px solid #e5e7eb;
  background: white;
  cursor: pointer;
  font-size: 15px;
  font-weight: 600;
  color: #111827;
  transition: all 0.22s ease;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
  position: relative;

  &:hover:not(:disabled) {
    border-color: #c7d2fe;
    box-shadow: 0 4px 14px rgba(99, 102, 241, 0.1);
    transform: translateY(-1px);
  }

  &:disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }
}

.sb-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.soon-badge {
  margin-left: auto;
  background: #f3f4f6;
  color: #9ca3af;
  font-size: 11px;
  font-weight: 700;
  padding: 3px 8px;
  border-radius: 20px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

// ── Trust Row ─────────────────────────────────────────────────────────────────
.trust-row {
  display: flex;
  justify-content: center;
  gap: 16px;
  flex-wrap: wrap;
  padding: 16px 0;
  border-top: 1px solid #e9ecef;
  margin-bottom: 20px;
}

.trust-chip {
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 12px;
  color: #6b7280;
  font-weight: 500;
}

// ── Footer ────────────────────────────────────────────────────────────────────
.form-footer {
  text-align: center;
  font-size: 14px;
  color: #6b7280;
  font-weight: 500;
}

.register-link {
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

// ── Responsive ────────────────────────────────────────────────────────────────
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

  .trust-row {
    gap: 10px;
  }
}
</style>
