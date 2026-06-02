<template>
  <div class="login-page-container">
    <div class="login-wrapper">
      <!-- Breadcrumbs -->
      <div class="login-breadcrumbs q-mb-md">
        <BreadCrumbsWrapper :bread-crumbs="[
          {
            name: 'Login',
            path: '',
          },
        ]" />
      </div>

      <!-- Login Card -->
      <div class="login-card-wrapper">
        <q-card flat bordered class="login-card">
          <!-- Header -->
          <q-card-section class="login-header">
            <div class="login-icon-wrapper">
              <q-icon name="lock" size="48px" color="primary" />
            </div>
            <div class="text-h4 text-weight-bold q-mt-md login-header-title">
              Welcome Back
            </div>
            <div class="text-body2 q-mt-xs login-header-subtitle">
              Sign in to your account to continue shopping
            </div>
          </q-card-section>

          <q-separator />

          <!-- Login Form -->
          <q-card-section class="login-form-section">
            <div class="social-login-content">
              <div class="social-login-copy q-mb-md">
                <div class="text-subtitle1 text-weight-medium q-mb-sm">
                  Sign in with email
                </div>
                <div class="form-helper-text">
                  Use the email and password for your account.
                </div>
              </div>

              <q-form class="q-gutter-y-md" @submit.prevent="loginWithEmail">
                <q-input
                  v-model="email"
                  outlined
                  dense
                  type="email"
                  label="Email"
                  class="login-input"
                  :rules="[
                    (val) => !!val?.trim() || 'Email is required',
                    (val) => /.+@.+\..+/.test(val) || 'Enter a valid email',
                  ]"
                  lazy-rules
                  autocomplete="email"
                >
                  <template #prepend>
                    <q-icon name="email" />
                  </template>
                </q-input>

                <q-input
                  v-model="password"
                  outlined
                  dense
                  :type="showPassword ? 'text' : 'password'"
                  label="Password"
                  class="login-input"
                  :rules="[(val) => !!val || 'Password is required']"
                  lazy-rules
                  autocomplete="current-password"
                >
                  <template #prepend>
                    <q-icon name="lock" />
                  </template>
                  <template #append>
                    <q-btn
                      flat
                      round
                      dense
                      :icon="showPassword ? 'visibility_off' : 'visibility'"
                      tabindex="-1"
                      @click="showPassword = !showPassword"
                    />
                  </template>
                </q-input>

                <q-btn
                  type="submit"
                  label="Sign in"
                  color="primary"
                  class="login-submit-btn full-width"
                  unelevated
                  size="lg"
                  :loading="isEmailSubmitting"
                />
              </q-form>

              <div class="or-separator q-my-lg">
                <q-separator />
                <span class="or-separator-label text-caption text-grey-6">or</span>
                <q-separator />
              </div>

              <div class="social-login-copy q-mb-md">
                <div class="text-subtitle1 text-weight-medium q-mb-sm">
                  Continue with Google Account
                </div>
                <div class="form-helper-text">
                  Use your google account to sign in quickly and continue shopping.
                </div>
              </div>

              <q-btn
                label="Continue with Google"
                color="primary"
                class="login-submit-btn google-login-btn full-width q-mt-sm"
                unelevated
                size="lg"
                :loading="isGoogleRedirecting"
                :disable="isEmailSubmitting"
                @click="loginWithGoogle"
              />

              <q-btn
                label="Continue with Facebook"
                color="primary"
                class="login-submit-btn facebook-login-btn full-width"
                unelevated
                size="lg"
                :loading="isFacebookRedirecting"
                :disable="isEmailSubmitting"
                @click="loginWithFacebook"
                :disabled=true
              />

              

              <div class="trust-section q-mt-lg">
                <div class="trust-item">
                  <q-icon name="lock" size="xs" color="positive" />
                  <span class="text-caption">Secure sign-in</span>
                </div>
                <div class="trust-item">
                  <q-icon name="verified" size="xs" color="positive" />
                  <span class="text-caption">Fast account access</span>
                </div>
              </div>
            </div>
          </q-card-section>

          <!-- Footer -->
          <q-card-section class="login-footer">
            <div class="text-body2 text-center text-grey-6">
              Don't have an account?
              <router-link to="/register" class="register-link">
                Create one here
              </router-link>
            </div>
          </q-card-section>
        </q-card>
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
.login-page-container {
  min-height: calc(100vh - 64px);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 32px 16px;
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
}

.login-wrapper {
  width: 100%;
  max-width: 500px;
}

.login-breadcrumbs {
  margin-bottom: 16px;
}

.login-card-wrapper {
  width: 100%;
}

.login-card {
  border-radius: 16px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
  overflow: hidden;
  background: white;
}

.login-header {
  text-align: center;
  padding: 32px 24px 24px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.login-icon-wrapper {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.2);
  backdrop-filter: blur(10px);
  margin-bottom: 8px;

  .q-icon {
    color: white;
  }
}

.login-header-title,
.login-header-subtitle {
  color: white !important;
}

.login-header-title {
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.login-header-subtitle {
  opacity: 0.95;
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
}

.login-form-section {
  padding: 32px 24px;
}

.login-form {
  .form-group {
    margin-bottom: 20px;
  }
}

.social-login-content {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.form-label {
  display: flex;
  align-items: center;
  font-size: 14px;
  font-weight: 600;
  color: #1a1a1a;
  margin-bottom: 8px;
}

.login-input {
  :deep(.q-field__control) {
    height: 48px;
    min-height: 48px;
    border-radius: 8px;
    font-size: 15px;
    transition: all 0.3s ease;

    &:hover {
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
  }

  :deep(.q-field--focused .q-field__control) {
    box-shadow: 0 0 0 2px rgba(var(--q-primary-rgb), 0.2);
  }
}

.form-helper-text {
  font-size: 12px;
  color: #666;
  padding-left: 4px;
}

.login-submit-btn {
  height: 52px;
  font-size: 16px;
  font-weight: 600;
  border-radius: 8px;
  letter-spacing: 0.5px;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  text-transform: none;

  &:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
  }
}

.facebook-login-btn {
  background: #1877f2 !important;
}

.google-login-btn {
  background: #4285f4 !important;
}

.or-separator {
  display: grid;
  grid-template-columns: 1fr auto 1fr;
  align-items: center;
  gap: 12px;
  position: relative;
}

.or-separator-label {
  text-transform: lowercase;
  letter-spacing: 0.08em;
}

.trust-section {
  display: flex;
  justify-content: center;
  gap: 24px;
  padding: 16px 0;
  border-top: 1px solid #e0e0e0;
}

.trust-item {
  display: flex;
  align-items: center;
  gap: 6px;
  color: #666;
  font-size: 12px;
}

.login-footer {
  padding: 20px 24px;
  background: #f8f9fa;
  border-top: 1px solid #e0e0e0;
}

.register-link {
  color: var(--q-primary);
  text-decoration: none;
  font-weight: 600;
  transition: color 0.2s ease;

  &:hover {
    color: var(--q-primary);
    text-decoration: underline;
  }
}

@media (max-width: 600px) {
  .login-page-container {
    padding: 16px;
    min-height: calc(100vh - 64px);
  }

  .login-header {
    padding: 24px 16px 20px;
  }

  .login-icon-wrapper {
    width: 64px;
    height: 64px;

    .q-icon {
      font-size: 32px;
    }
  }

  .login-form-section {
    padding: 24px 16px;
  }

  .login-card {
    border-radius: 12px;
  }
}
</style>
