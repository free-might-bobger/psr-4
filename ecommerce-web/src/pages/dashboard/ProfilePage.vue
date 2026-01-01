<template>
  <div class="profile-container">
    <!-- Header Section -->
    <div class="profile-header q-mb-lg">
      <div class="text-h4 text-weight-bold">
        <q-icon name="account_circle" class="q-mr-sm" />
        Profile Settings
      </div>
      <div class="text-body2 text-grey-7 q-mt-xs">
        Update your personal information
      </div>
    </div>

    <!-- Profile Form Card -->
    <q-card flat bordered class="profile-card">
      <q-card-section>
        <div class="text-h6 text-weight-medium q-mb-md">
          <q-icon name="edit" class="q-mr-sm" />
          Personal Information
        </div>
        <q-separator class="q-mb-lg" />

        <q-form @submit.prevent="onSubmit" @reset="onReset" class="profile-form" ref="myForm">
          <div class="form-fields">
            <!-- Name Field -->
            <q-input v-model="profile.name" outlined label="Full Name" placeholder="Enter your full name"
              :rules="[(val) => (val && val.length > 0) || 'Name is required.']" hide-bottom-space
              class="profile-input q-mb-md">
              <template v-slot:prepend>
                <q-icon name="person" />
              </template>
            </q-input>

            <!-- Mobile Field -->
            <q-input v-model="profile.mobile" outlined label="Mobile Number" placeholder="Enter your mobile number"
              :rules="[
                (val) => (val && val.length > 0) || 'Mobile is required.',
                (val) => isValidMobileNumber(val) || 'Please enter a valid mobile number.'
              ]" class="profile-input q-mb-lg" prefix="+63">
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
.profile-container {
  max-width: 800px;
  margin: 0 auto;
  padding: 16px;
}

.profile-header {
  padding: 16px 0;
}

.profile-card {
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  overflow: hidden;
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
  min-width: 120px;
  height: 42px;
  font-weight: 600;
  border-radius: 8px;
  transition: all 0.3s ease;

  &:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
  }
}

@media (max-width: 600px) {
  .profile-container {
    padding: 8px;
  }

  .form-actions {
    flex-direction: column;

    .action-btn {
      width: 100%;
      margin-bottom: 8px;
    }
  }
}
</style>
