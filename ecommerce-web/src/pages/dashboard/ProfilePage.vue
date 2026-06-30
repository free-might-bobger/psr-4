<template>
  <div class="profile-page-container">

    <!-- Hero Header -->
    <div class="profile-hero q-mb-xl">
      <div class="hero-bg-accent"></div>
      <div class="hero-content">
        <div class="hero-avatar-wrap">
          <div class="hero-avatar">
            <q-icon name="account_circle" size="56px" color="white" />
          </div>
          <div class="avatar-status-dot"></div>
        </div>
        <div class="hero-text">
          <h2 class="hero-name">{{ profile.name || 'Your Name' }}</h2>
          <div class="hero-meta">
            <q-icon name="shield" size="14px" />
            <span>Administrator</span>
            <span class="hero-dot">·</span>
            <q-icon name="phone" size="14px" />
            <span>{{ profile.mobile ? '+63 ' + profile.mobile : 'No mobile set' }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Form Card -->
    <q-card flat class="profile-card">
      <q-card-section class="card-header-section">
        <div class="section-badge">
          <q-icon name="edit" size="16px" />
          <span>Edit Profile</span>
        </div>
        <p class="section-desc">Keep your personal information up to date.</p>
      </q-card-section>

      <q-separator class="card-divider" />

      <q-card-section class="q-pt-lg">
        <q-form @submit.prevent="onSubmit" @reset="onReset" class="profile-form" ref="myForm">
          <div class="form-grid">
            <!-- Name Field -->
            <div class="field-group">
              <div class="field-label">
                <q-icon name="person" size="16px" class="field-label-icon" />
                Full Name
              </div>
              <q-input v-model="profile.name" outlined placeholder="Enter your full name"
                :rules="[(val) => (val && val.length > 0) || 'Name is required.']" hide-bottom-space
                class="profile-input" />
            </div>

            <!-- Mobile Field -->
            <div class="field-group">
              <div class="field-label">
                <q-icon name="phone" size="16px" class="field-label-icon" />
                Mobile Number
              </div>
              <q-input v-model="profile.mobile" outlined placeholder="Enter your mobile number" :rules="[
                (val) => (val && val.length > 0) || 'Mobile is required.',
                (val) => isValidMobileNumber(val) || 'Please enter a valid mobile number.'
              ]" class="profile-input" prefix="+63" />
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="form-actions">
            <q-btn label="Discard" type="reset" flat class="cancel-btn" @click="onReset" />
            <q-btn label="Save Changes" type="submit" unelevated class="save-btn" icon="check" />
          </div>
        </q-form>
      </q-card-section>
    </q-card>

  </div>
</template>
<script setup lang="ts">
import { ref } from 'vue';
import { useUserStore } from 'src/stores/user';
import { storeToRefs } from 'pinia';
import { create } from 'src/boot/axios-call';
import { useRoute } from 'vue-router';
import { useQuasar } from 'quasar';
import { isValidMobileNumber } from 'src/boot/validators';
import type { QForm } from 'quasar';

const $q = useQuasar();
const route = useRoute();
const useUser = useUserStore();
const { profile } = storeToRefs(useUser);

const myForm = ref<QForm | null>(null);

const onSubmit = async () => {
  myForm.value?.validate().then(async (success: any) => {
    if (success) {
      try {
        await create({
          entity: 'profile-update',
          data: {
            name: profile.value.name,
            mobile: profile.value.mobile
          }
        }, false);
        $q.notify({
          message: 'Profile updated successfully!',
          type: 'positive',
          position: 'top',
          icon: 'check_circle'
        });
      } catch (error) {
        $q.notify({
          message: 'Failed to update profile. Please try again.',
          type: 'negative',
          position: 'top',
          icon: 'error'
        });
      }
    }
  });
};

const onReset = () => {
  myForm.value?.resetValidation();
};

</script>

<style scoped lang="scss">
$dark-base: #0f172a;
$dark-card: #1e293b;
$dark-elevated: #273549;
$border: rgba(255, 255, 255, 0.08);
$accent: #6366f1;
$accent-2: #7c3aed;
$white: #ffffff;
$text-muted: rgba(255, 255, 255, 0.55);

.profile-page-container {
  max-width: 760px;
  margin: 0 auto;
  padding: 28px 20px;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
}

/* Hero */
.profile-hero {
  position: relative;
  background: $dark-card;
  border-radius: 20px;
  padding: 36px 32px;
  border: 1px solid $border;
  overflow: hidden;
  box-shadow: 0 8px 40px rgba(0, 0, 0, 0.3);
}

.hero-bg-accent {
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, rgba(99, 102, 241, 0.18) 0%, rgba(124, 58, 237, 0.10) 60%, transparent 100%);
  pointer-events: none;
}

.hero-content {
  position: relative;
  display: flex;
  align-items: center;
  gap: 24px;
}

.hero-avatar-wrap {
  position: relative;
  flex-shrink: 0;
}

.hero-avatar {
  width: 84px;
  height: 84px;
  border-radius: 50%;
  background: linear-gradient(135deg, $accent 0%, $accent-2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  border: 3px solid rgba(255, 255, 255, 0.15);
  box-shadow: 0 0 0 6px rgba(99, 102, 241, 0.15);
}

.avatar-status-dot {
  position: absolute;
  bottom: 4px;
  right: 4px;
  width: 14px;
  height: 14px;
  border-radius: 50%;
  background: #22c55e;
  border: 2px solid $dark-card;
  box-shadow: 0 0 8px rgba(34, 197, 94, 0.5);
}

.hero-text {
  flex: 1;
}

.hero-name {
  font-size: 26px;
  font-weight: 800;
  color: $white;
  margin: 0 0 8px;
  letter-spacing: -0.3px;
}

.hero-meta {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 13px;
  font-weight: 500;
  color: $text-muted;

  .q-icon {
    opacity: 0.7;
  }
}

.hero-dot {
  opacity: 0.4;
}

/* Card */
.profile-card {
  background: $dark-card;
  border-radius: 20px;
  border: 1px solid $border;
  box-shadow: 0 8px 40px rgba(0, 0, 0, 0.25);
  overflow: hidden;
}

.card-header-section {
  padding: 28px 28px 20px;
}

.section-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(124, 58, 237, 0.15));
  border: 1px solid rgba(99, 102, 241, 0.3);
  border-radius: 30px;
  padding: 6px 16px;
  font-size: 13px;
  font-weight: 700;
  color: #a5b4fc;
  letter-spacing: 0.3px;
  margin-bottom: 10px;
}

.section-desc {
  font-size: 14px;
  color: $text-muted;
  margin: 0;
}

.card-divider {
  background: $border;
}

/* Form */
.profile-form {
  padding: 0;
}

.form-grid {
  display: flex;
  flex-direction: column;
  gap: 24px;
  padding: 0 28px;
}

.field-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.field-label {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  font-weight: 700;
  color: rgba(255, 255, 255, 0.7);
  text-transform: uppercase;
  letter-spacing: 0.6px;
}

.field-label-icon {
  color: $accent;
  opacity: 0.9;
}

.profile-input {
  :deep(.q-field__control) {
    border-radius: 12px;
    background: $dark-elevated;
    border-color: $border;
  }

  :deep(.q-field__native),
  :deep(.q-field__prefix),
  :deep(.q-field__input) {
    color: $white !important;
    font-weight: 500;
  }

  :deep(.q-field__label) {
    color: $text-muted;
  }

  :deep(.q-field__prepend .q-icon),
  :deep(.q-field__append .q-icon) {
    color: rgba(255, 255, 255, 0.4);
  }

  :deep(.q-field--focused .q-field__control) {
    border-color: $accent !important;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
  }
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  align-items: center;
  gap: 12px;
  padding: 24px 28px 28px;
  margin-top: 8px;
  border-top: 1px solid $border;
}

.cancel-btn {
  color: rgba(255, 255, 255, 0.55);
  font-weight: 600;
  border-radius: 12px;
  padding: 0 20px;
  height: 44px;

  &:hover {
    color: $white;
    background: rgba(255, 255, 255, 0.06);
  }
}

.save-btn {
  background: linear-gradient(135deg, $accent 0%, $accent-2 100%);
  color: $white;
  font-weight: 700;
  font-size: 14px;
  border-radius: 12px;
  padding: 0 28px;
  height: 44px;
  box-shadow: 0 4px 20px rgba(99, 102, 241, 0.4);
  transition: all 0.2s ease;

  &:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(99, 102, 241, 0.5);
  }
}

@media (max-width: 600px) {
  .profile-page-container {
    padding: 16px 12px;
  }

  .profile-hero {
    padding: 24px 20px;
  }

  .hero-content {
    flex-direction: column;
    text-align: center;
  }

  .hero-meta {
    justify-content: center;
    flex-wrap: wrap;
  }

  .form-grid {
    padding: 0 16px;
  }

  .form-actions {
    padding: 20px 16px 20px;
    flex-direction: column;

    .cancel-btn,
    .save-btn {
      width: 100%;
    }
  }
}
</style>
