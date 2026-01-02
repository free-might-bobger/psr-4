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
            <q-form @submit.prevent="onSubmit" class="login-form" ref="myForm">
              <!-- Mobile Number Input -->
              <div class="form-group q-mb-md">
                <label class="form-label">
                  <q-icon name="phone_android" size="sm" class="q-mr-xs" />
                  Mobile Number
                </label>
                <q-input v-model="loginInfo.mobile" class="full-width q-mb-md" outlined label="Enter mobile number"
                  placeholder="9XX XXX XXXX" :rules="[
                    async (val) =>
                      isValidMobileNumber(val) ||
                      'Please enter a valid mobile number.',
                  ]" hide-bottom-space prefix="+63">
                  <template v-slot:prepend>
                    <q-icon name="phone" />
                  </template>
                </q-input>
                <div class="form-helper-text q-mt-xs">
                  We'll send a verification code to this number
                </div>
              </div>

              <!-- Get Passcode Button -->
              <q-btn label="Get Verification Code" color="primary" outline class="full-width q-mb-lg"
                @click="getPassCode" icon="verified_user" size="md" :disable="!isValidMobileNumber(loginInfo.mobile)" />

              <!-- Passcode Input -->
              <div class="form-group q-mb-md">
                <label class="form-label">
                  <q-icon name="vpn_key" size="sm" class="q-mr-xs" />
                  Verification Code
                </label>
                <q-input :type="showPassword ? 'text' : 'password'" v-model="loginInfo.password" outlined
                  label="Enter verification code" placeholder="Enter the code sent to your mobile" :rules="[
                    (val) => (val && val.length > 0) || 'Verification code is required.'
                  ]" hide-bottom-space class="login-input" mask="######">
                  <template v-slot:prepend>
                    <q-icon name="lock" />
                  </template>
                  <template v-slot:append>
                    <q-icon :name="showPassword ? 'visibility' : 'visibility_off'" class="cursor-pointer"
                      @click="showPassword = !showPassword" />
                  </template>
                </q-input>
                <div class="form-helper-text q-mt-xs">
                  Enter the 6-digit code sent to your mobile number
                </div>
              </div>

              <!-- Submit Button -->
              <q-btn label="Sign In" type="submit" color="primary" class="login-submit-btn full-width" unelevated
                size="lg" icon="login" :loading="isSubmitting" />

              <!-- Trust Indicators -->
              <div class="trust-section q-mt-lg">
                <div class="trust-item">
                  <q-icon name="lock" size="xs" color="positive" />
                  <span class="text-caption">Secure Login</span>
                </div>
                <div class="trust-item">
                  <q-icon name="verified" size="xs" color="positive" />
                  <span class="text-caption">Verified Account</span>
                </div>
              </div>
            </q-form>
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
import { ref } from 'vue';
import { login, create } from 'src/boot/axios-call';
import type { QForm } from 'quasar';
import { LoginInterface } from 'boot/interfaces';
import { useQuasar } from 'quasar';
import { isValidMobileNumber } from 'src/boot/validators';
import { useRouter } from 'vue-router';

const router = useRouter();
const $q = useQuasar();
const loginInfo = ref<LoginInterface>({
  mobile: '',
  password: '',
});
const myForm = ref<QForm | null>(null);
const showPassword = ref(false);
const isSubmitting = ref(false);

const onSubmit = async () => {
  myForm.value?.validate().then(async (success: any) => {
    if (success) {
      isSubmitting.value = true;
      try {
        const result = await login({
          mobile: loginInfo.value.mobile,
          password: loginInfo.value.password,
        });
        if (result) {
          $q.notify({
            message: 'Login successful! Welcome back.',
            type: 'positive',
            position: 'top',
            icon: 'check_circle'
          });
          router.push('/');
        }
      } catch (error) {
        $q.notify({
          message: 'Invalid credentials. Please try again.',
          type: 'negative',
          position: 'top',
          icon: 'error'
        });
      } finally {
        isSubmitting.value = false;
      }
    }
  });
};

const getPassCode = async () => {
  if (!isValidMobileNumber(loginInfo.value.mobile)) {
    $q.notify({
      message: 'Please enter a valid mobile number.',
      type: 'negative',
      position: 'top'
    });
    return;
  }

  try {
   await create(
      {
        entity: 'create-new-activation-code',
        data: {
          mobile: loginInfo.value.mobile,
        },
      },
      true,
      'Sending verification code to your mobile number...',
      'Verification code sent! Please check your mobile.'
    );

  } catch (error) {
    $q.notify({
      message: 'Failed to send verification code. Please try again.',
      type: 'negative',
      position: 'bottom',
      icon: 'error'
    });
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

.login-form {
  .form-group {
    margin-bottom: 20px;
  }
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
