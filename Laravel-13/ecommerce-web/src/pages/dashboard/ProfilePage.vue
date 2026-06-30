<template>
  <div class="profile-page-container">
    <!-- Header Section -->
    <div class="page-header q-mb-md">
      <div class="header-content">
        <div class="header-title-section">
          <q-icon name="account_circle" size="32px" color="primary" class="q-mr-sm" />
          <h2 class="page-title">Profile Settings</h2>
        </div>
        <div class="header-subtitle text-body2 text-grey-7">
          Update your personal information
        </div>
      </div>
    </div>

    <!-- Profile Form Card -->
    <q-card flat bordered class="profile-card">
      <q-card-section>
        <div class="card-title">
          <q-icon name="edit" class="q-mr-sm" />
          <span>Personal Information</span>
        </div>
        <q-separator class="q-mb-lg" />

        <q-form @submit.prevent="onSubmit" @reset="onReset" class="profile-form" ref="myForm">
          <div class="form-fields">
            <!-- Name Field -->
            <q-input
              v-model="profile.name"
              outlined
              label="Full Name"
              placeholder="Enter your full name"
              :rules="[(val) => (val && val.length > 0) || 'Name is required.']"
              hide-bottom-space
              class="profile-input q-mb-md"
            >
              <template v-slot:prepend>
                <q-icon name="person" />
              </template>
            </q-input>

            <!-- Mobile Field -->
            <q-input
              v-model="profile.mobile"
              outlined
              label="Mobile Number"
              placeholder="Enter your mobile number"
              :rules="[
                (val) => (val && val.length > 0) || 'Mobile is required.',
                (val) => isValidMobileNumber(val) || 'Please enter a valid mobile number.'
              ]"
              class="profile-input q-mb-lg"
              prefix="+63"
            >
              <template v-slot:prepend>
                <q-icon name="phone" />
              </template>
            </q-input>
          </div>

          <!-- Action Buttons -->
          <div class="form-actions">
            <q-btn label="Cancel" type="reset" color="grey-7" outline class="action-btn" @click="onReset" />
            <q-btn label="Update Profile" type="submit" color="primary" unelevated class="action-btn" icon="save" />
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
import { update } from 'src/boot/axios-call';
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
      await update({
          entity: 'profile',
          optimus_id: profile.value.optimus_id,
          data: {
            name: profile.value.name,
            mobile: profile.value.mobile
          }
        });
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
.profile-page-container {
  max-width: 1000px;
  margin: 0 auto;
  padding: 16px;
}

.page-header {
  background: #fff;
  border-radius: 12px;
  padding: 16px 20px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
}

.header-content {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.header-title-section {
  display: flex;
  align-items: center;
  gap: 8px;
}

.page-title {
  font-size: 24px;
  margin: 0;
  font-weight: 700;
  color: #2c2c2c;
}

.header-subtitle {
  margin-left: 40px;
}

.profile-card {
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  overflow: hidden;
  background: #fff;
}

.card-title {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 18px;
  font-weight: 600;
  color: #2c2c2c;
  margin-bottom: 12px;
}

.profile-form {
  padding: 8px 0;
}

.form-fields {
  margin-bottom: 24px;
}

.profile-input {
  :deep(.q-field__control) {
    border-radius: 8px;
  }

  :deep(.q-field__prepend) {
    padding-right: 12px;
  }
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  padding-top: 16px;
  border-top: 1px solid #e0e0e0;
}

.action-btn {
  min-width: 140px;
  height: 42px;
  font-weight: 600;
  border-radius: 8px;
  transition: all 0.3s ease;

  &:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
  }
}

@media (max-width: 900px) {
  .header-subtitle {
    margin-left: 0;
  }
}

@media (max-width: 600px) {
  .profile-page-container {
    padding: 8px;
  }

  .page-header {
    padding: 14px 16px;
  }

  .form-actions {
    flex-direction: column;
  }

  .action-btn {
    width: 100%;
  }
}
</style>
