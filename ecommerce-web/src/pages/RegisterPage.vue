<template>
  <div class="reg-page">
    <div class="reg-split">

      <!-- Left Form Panel -->
      <div class="reg-form-panel">
        <div class="reg-form-inner">

          <div class="reg-breadcrumbs">
            <BreadCrumbsWrapper :bread-crumbs="[{ name: 'Register', path: '' }]" />
          </div>

          <div class="form-header">
            <div class="form-header-icon">
              <q-icon name="person_add" size="24px" color="white" />
            </div>
            <div>
              <h1 class="form-title">Create your account</h1>
              <p class="form-subtitle">Join thousands of local shoppers today</p>
            </div>
          </div>

          <!-- Steps indicator -->
          <div class="reg-steps">
            <div class="reg-step active">
              <div class="step-dot">1</div>
              <span>Your Info</span>
            </div>
            <div class="step-connector"></div>
            <div class="reg-step">
              <div class="step-dot">2</div>
              <span>Verify Email</span>
            </div>
            <div class="step-connector"></div>
            <div class="reg-step">
              <div class="step-dot">3</div>
              <span>Start Shopping</span>
            </div>
          </div>

          <q-form class="reg-form" @submit.prevent="submitRegister">
            <div class="field-group">
              <label class="field-label">Full Name</label>
              <q-input v-model="name" outlined dense placeholder="John Dela Cruz" class="login-input" lazy-rules
                autocomplete="name" :rules="[(val) => !!val?.trim() || 'Name is required']">
                <template #prepend><q-icon name="badge" size="18px" color="grey-5" /></template>
              </q-input>
            </div>

            <div class="field-group">
              <label class="field-label">Email Address</label>
              <q-input v-model="email" outlined dense type="email" placeholder="you@example.com" class="login-input"
                lazy-rules autocomplete="email"
                :rules="[(val) => !!val?.trim() || 'Email is required', (val) => /.+@.+\..+/.test(val) || 'Enter a valid email']">
                <template #prepend><q-icon name="email" size="18px" color="grey-5" /></template>
              </q-input>
            </div>

            <div class="field-row">
              <div class="field-group">
                <label class="field-label">Password</label>
                <q-input v-model="password" outlined dense :type="showPassword ? 'text' : 'password'"
                  placeholder="Min. 6 characters" class="login-input" lazy-rules autocomplete="new-password"
                  :rules="[(val) => (val && val.length >= 6) || 'Use at least 6 characters']">
                  <template #prepend><q-icon name="lock" size="18px" color="grey-5" /></template>
                  <template #append>
                    <q-btn flat round dense size="sm" color="grey-6"
                      :icon="showPassword ? 'visibility_off' : 'visibility'" tabindex="-1"
                      @click="showPassword = !showPassword" />
                  </template>
                </q-input>
              </div>

              <div class="field-group">
                <label class="field-label">Confirm Password</label>
                <q-input v-model="confirmPassword" outlined dense :type="showPassword ? 'text' : 'password'"
                  placeholder="Repeat password" class="login-input" lazy-rules autocomplete="new-password"
                  :rules="[(val) => val === password || 'Passwords must match']">
                  <template #prepend><q-icon name="lock_outline" size="18px" color="grey-5" /></template>
                </q-input>
              </div>
            </div>

            <div class="terms-row">
              <q-icon name="info" size="14px" color="grey-5" />
              <span>By registering, you agree to our <a href="#" class="terms-link">Terms of Service</a> and <a href="#"
                  class="terms-link">Privacy Policy</a>.</span>
            </div>

            <q-btn type="submit" no-caps unelevated class="submit-btn full-width" size="lg" :loading="isSubmitting">
              <div class="submit-inner">
                <q-icon name="person_add" size="18px" />
                <span>Create Free Account</span>
              </div>
            </q-btn>
          </q-form>

          <div class="trust-row">
            <div class="trust-chip"><q-icon name="lock" size="12px" color="positive" /><span>Secure &amp; Private</span>
            </div>
            <div class="trust-chip"><q-icon name="verified" size="12px" color="positive" /><span>Free Forever</span>
            </div>
            <div class="trust-chip"><q-icon name="bolt" size="12px" color="positive" /><span>Instant Access</span></div>
          </div>

          <div class="form-footer">
            Already have an account?
            <router-link to="/login" class="signin-link">Sign in here</router-link>
          </div>
        </div>
      </div>

      <!-- Right Brand Panel -->
      <div class="reg-brand-panel">
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
          <h2 class="brand-headline">Join your<br><span class="brand-accent">community.</span></h2>
          <p class="brand-desc">Create a free account and unlock access to hundreds of local shops, exclusive deals, and
            fast
            delivery right to your door.</p>
          <div class="brand-perks">
            <div class="brand-perk">
              <div class="perk-icon"><q-icon name="storefront" size="18px" color="white" /></div>
              <div>
                <div class="perk-title">500+ Local Shops</div>
                <div class="perk-sub">Explore verified shops near you</div>
              </div>
            </div>
            <div class="brand-perk">
              <div class="perk-icon"><q-icon name="local_offer" size="18px" color="white" /></div>
              <div>
                <div class="perk-title">Exclusive Deals</div>
                <div class="perk-sub">Members-only discounts &amp; offers</div>
              </div>
            </div>
            <div class="brand-perk">
              <div class="perk-icon"><q-icon name="bolt" size="18px" color="white" /></div>
              <div>
                <div class="perk-title">Fast Delivery</div>
                <div class="perk-sub">Same-day delivery from local stores</div>
              </div>
            </div>
            <div class="brand-perk">
              <div class="perk-icon"><q-icon name="card_giftcard" size="18px" color="white" /></div>
              <div>
                <div class="perk-title">Free to Join</div>
                <div class="perk-sub">No credit card required, ever</div>
              </div>
            </div>
          </div>
        </div>
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
// ── Layout ────────────────────────────────────────────────────────────────────
.reg-page {
  min-height: calc(100vh - 68px);
  display: flex;
}

.reg-split {
  display: flex;
  width: 100%;
  min-height: calc(100vh - 68px);
}

// ── Form Panel (Left) ─────────────────────────────────────────────────────────
.reg-form-panel {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 48px 28px;
  background: #f4f5f7;
  overflow-y: auto;
}

.reg-form-inner {
  width: 100%;
  max-width: 520px;
}

.reg-breadcrumbs {
  margin-bottom: 28px;
}

// ── Form Header ───────────────────────────────────────────────────────────────
.form-header {
  display: flex;
  align-items: center;
  gap: 16px;
  margin-bottom: 28px;
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

// ── Steps ─────────────────────────────────────────────────────────────────────
.reg-steps {
  display: flex;
  align-items: center;
  gap: 0;
  margin-bottom: 28px;
  padding: 16px 20px;
  background: white;
  border-radius: 12px;
  box-shadow: 0 1px 6px rgba(0, 0, 0, 0.06);
}

.reg-step {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 12px;
  font-weight: 600;
  color: #9ca3af;

  &.active {
    color: #312e81;

    .step-dot {
      background: linear-gradient(135deg, #312e81 0%, #6d28d9 100%);
      color: white;
      box-shadow: 0 4px 10px rgba(79, 70, 229, 0.35);
    }
  }
}

.step-dot {
  width: 26px;
  height: 26px;
  border-radius: 50%;
  background: #e5e7eb;
  color: #9ca3af;
  font-size: 12px;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  transition: all 0.2s ease;
}

.step-connector {
  flex: 1;
  height: 1px;
  background: #e5e7eb;
  margin: 0 8px;
}

// ── Form Fields ───────────────────────────────────────────────────────────────
.reg-form {
  display: flex;
  flex-direction: column;
  gap: 16px;
  margin-bottom: 20px;
}

.field-group {
  display: flex;
  flex-direction: column;
  gap: 7px;
  flex: 1;
}

.field-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
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

.terms-row {
  display: flex;
  align-items: flex-start;
  gap: 7px;
  font-size: 12px;
  color: #9ca3af;
  line-height: 1.5;
  padding: 0 2px;
}

.terms-link {
  color: #4c1d95;
  text-decoration: none;
  font-weight: 600;

  &:hover {
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

// ── Trust & Footer ────────────────────────────────────────────────────────────
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

.form-footer {
  text-align: center;
  font-size: 14px;
  color: #6b7280;
  font-weight: 500;
}

.signin-link {
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

// ── Brand Panel (Right) ───────────────────────────────────────────────────────
.reg-brand-panel {
  flex: 0 0 40%;
  position: relative;
  background: linear-gradient(145deg, #0f0c29 0%, #1e1b4b 45%, #312e81 100%);
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;

  @media (max-width: 900px) {
    display: none;
  }
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
    width: 380px;
    height: 380px;
    background: rgba(109, 40, 217, 0.4);
    bottom: -100px;
    right: -80px;
  }

  &.orb-2 {
    width: 280px;
    height: 280px;
    background: rgba(99, 102, 241, 0.3);
    top: -60px;
    left: -40px;
    animation-delay: 4s;
  }

  &.orb-3 {
    width: 180px;
    height: 180px;
    background: rgba(251, 191, 36, 0.15);
    top: 40%;
    right: 20%;
    animation-delay: 7s;
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
    transform: translate(14px, -20px) scale(1.04);
  }

  66% {
    transform: translate(-10px, 14px) scale(0.96);
  }
}

.brand-content {
  position: relative;
  z-index: 1;
  padding: 52px 48px;
  width: 100%;
}

.brand-logo-link {
  display: flex;
  align-items: center;
  gap: 14px;
  text-decoration: none;
  margin-bottom: 48px;
}

.brand-name {
  font-size: 20px;
  font-weight: 800;
  color: white;
  letter-spacing: -0.4px;
}

.brand-headline {
  font-size: 42px;
  font-weight: 900;
  color: white;
  line-height: 1.1;
  letter-spacing: -1.5px;
  margin: 0 0 16px;
}

.brand-accent {
  background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.brand-desc {
  font-size: 15px;
  color: rgba(255, 255, 255, 0.58);
  line-height: 1.75;
  margin: 0 0 36px;
  max-width: 320px;
}

.brand-perks {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.brand-perk {
  display: flex;
  align-items: flex-start;
  gap: 14px;
}

.perk-icon {
  width: 38px;
  height: 38px;
  border-radius: 10px;
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.14);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  margin-top: 2px;
}

.perk-title {
  font-size: 14px;
  font-weight: 700;
  color: white;
  line-height: 1.3;
  margin-bottom: 2px;
}

.perk-sub {
  font-size: 13px;
  color: rgba(255, 255, 255, 0.5);
  font-weight: 400;
  line-height: 1.4;
}

// ── Responsive ────────────────────────────────────────────────────────────────
@media (max-width: 900px) {
  .reg-form-panel {
    padding: 36px 20px;
    background: white;
  }

  .field-row {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 480px) {
  .reg-form-panel {
    padding: 28px 16px;
  }

  .form-title {
    font-size: 22px;
  }

  .trust-row {
    gap: 10px;
  }

  .reg-steps {
    padding: 12px 14px;
  }
}
</style>
