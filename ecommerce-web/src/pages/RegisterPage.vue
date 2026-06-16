<template>
  <div class="login-page-container">
    <div class="login-wrapper">
      <div class="login-breadcrumbs q-mb-md">
        <BreadCrumbsWrapper
          :bread-crumbs="[
            {
              name: 'Register',
              path: '',
            },
          ]"
        />
      </div>

      <div class="login-card-wrapper">
        <q-card flat bordered class="login-card">
          <q-card-section class="login-header">
            <div class="login-icon-wrapper">
              <q-icon name="person_add" size="48px" color="primary" />
            </div>
            <div class="text-h4 text-weight-bold q-mt-md login-header-title">
              Create an account
            </div>
            <div class="text-body2 q-mt-xs login-header-subtitle">
              Enter your details to get started
            </div>
          </q-card-section>

          <q-separator />

          <q-card-section class="login-form-section">
            <q-form class="social-login-content" @submit.prevent="submitRegister">
              <q-input
                v-model="name"
                outlined
                dense
                label="Full name"
                class="login-input"
                :rules="[(val) => !!val?.trim() || 'Name is required']"
                lazy-rules
                autocomplete="name"
              >
                <template #prepend>
                  <q-icon name="badge" />
                </template>
              </q-input>

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
                :rules="[(val) => (val && val.length >= 6) || 'Use at least 6 characters']"
                lazy-rules
                autocomplete="new-password"
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

              <q-input
                v-model="confirmPassword"
                outlined
                dense
                :type="showPassword ? 'text' : 'password'"
                label="Confirm password"
                class="login-input"
                :rules="[(val) => val === password || 'Passwords must match']"
                lazy-rules
                autocomplete="new-password"
              >
                <template #prepend>
                  <q-icon name="lock_outline" />
                </template>
              </q-input>

              <q-btn
                type="submit"
                label="Create account"
                color="primary"
                class="login-submit-btn full-width"
                unelevated
                size="lg"
                :loading="isSubmitting"
              />
            </q-form>
          </q-card-section>

          <q-card-section class="login-footer">
            <div class="text-body2 text-center text-grey-6">
              Already have an account?
              <router-link to="/login" class="register-link">Sign in</router-link>
            </div>
          </q-card-section>
        </q-card>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import BreadCrumbsWrapper from 'src/components/BreadCrumbsWrapper.vue';
import { ref } from 'vue';
import { axios } from 'src/boot/axios';
import { useQuasar } from 'quasar';
import { useRouter } from 'vue-router';
import { useUserStore } from 'src/stores/user';
import type { ProfileState } from 'boot/interfaces';

const router = useRouter();
const $q = useQuasar();
const userStore = useUserStore();

const name = ref('');
const email = ref('');
const password = ref('');
const confirmPassword = ref('');
const showPassword = ref(false);
const isSubmitting = ref(false);

const applyAuthPayload = (data: {
  token: string;
  name?: string | null;
  mobile?: string | number | null;
  optimus_id?: number;
  userMenu?: ProfileState['userMenu'];
}) => {
  const profile: ProfileState = {
    token: data.token,
    name: data.name ?? null,
    mobile: data.mobile ?? '',
    optimus_id: Number(data.optimus_id ?? 0),
    userMenu: Array.isArray(data.userMenu) ? data.userMenu : [],
  };
  userStore.setProfile(profile);
  userStore.setUser(profile);
};

const submitRegister = async () => {
  if (password.value !== confirmPassword.value) {
    $q.notify({
      message: 'Passwords do not match.',
      type: 'warning',
      position: 'top',
      icon: 'warning',
    });
    return;
  }

  isSubmitting.value = true;
  try {
    const res = await axios.post('register', {
      name: name.value.trim(),
      email: email.value.trim(),
      password: password.value,
    });
    if (res.data?.success) {
      applyAuthPayload(res.data.data);
      $q.notify({
        message: 'Account created! A confirmation email has been sent to your inbox.',
        type: 'positive',
        position: 'top',
        icon: 'check_circle',
        closeBtn: true,
        timeout: 0,
      });
      await router.replace('/');
    }
  } catch (err: unknown) {
    const ax = err as { response?: { data?: { message?: string } } };
    $q.notify({
      message: ax.response?.data?.message ?? 'Registration failed. Please try again.',
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

.social-login-content {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.login-input {
  :deep(.q-field__control) {
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
