<template>
  <div class="apply-store-page">
    <div class="apply-store-card">
      <div class="form-header">
        <div class="form-header-icon">
          <q-icon name="store" size="28px" color="white" />
        </div>
        <div>
          <h1 class="form-title">Create A Store</h1>
          <p class="form-subtitle">Register your store to start selling</p>
        </div>
      </div>

      <q-form class="apply-store-form" @submit.prevent="submit">
        <div class="field-group">
          <label class="field-label">Store Name</label>
          <q-input v-model="form.name" outlined dense placeholder="Your store name" lazy-rules
            :rules="[(val) => !!val?.trim() || 'Name is required']">
            <template #prepend><q-icon name="storefront" size="18px" color="grey-5" /></template>
          </q-input>
        </div>

        <div class="field-group">
          <label class="field-label">Mobile Number</label>
          <q-input v-model="form.mobile" outlined dense placeholder="09XXXXXXXXX" lazy-rules
            :rules="[(val) => !!val?.trim() || 'Mobile is required']">
            <template #prepend><q-icon name="phone" size="18px" color="grey-5" /></template>
          </q-input>
        </div>

        <div class="field-group">
          <label class="field-label">Description</label>
          <q-input v-model="form.desc" type="textarea" outlined dense placeholder="Tell customers what you sell"
            rows="4" lazy-rules :rules="[(val) => !!val?.trim() || 'Description is required']" />
        </div>

        <div class="location-section">
          <div class="location-header">
            <label class="field-label">Store Location</label>
            <q-btn size="sm" unelevated color="primary" icon="my_location" label="Get Current Location"
              :loading="locating" @click="fetchLocation" />
          </div>

          <div class="field-row">
            <div class="field-group">
              <q-input v-model="form.latitude" outlined dense placeholder="Latitude" readonly
                :rules="[(val) => !!val || 'Latitude is required']">
                <template #prepend><q-icon name="place" size="18px" color="grey-5" /></template>
              </q-input>
            </div>
            <div class="field-group">
              <q-input v-model="form.longitude" outlined dense placeholder="Longitude" readonly
                :rules="[(val) => !!val || 'Longitude is required']">
                <template #prepend><q-icon name="place" size="18px" color="grey-5" /></template>
              </q-input>
            </div>
          </div>

          <div v-if="locationError" class="location-error">
            <q-icon name="error_outline" size="16px" />
            {{ locationError }}
          </div>
        </div>

        <q-btn type="submit" unelevated no-caps class="submit-btn" :loading="submitting">
          Submit Application
        </q-btn>
      </q-form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { create } from 'src/boot/axios-call';

const router = useRouter();

const form = reactive({
  name: '',
  mobile: '',
  desc: '',
  latitude: '',
  longitude: '',
});

const locating = ref(false);
const submitting = ref(false);
const locationError = ref('');

function fetchLocation() {
  locating.value = true;
  locationError.value = '';

  if (!navigator.geolocation) {
    locationError.value = 'Geolocation is not supported by your browser.';
    locating.value = false;
    return;
  }

  navigator.geolocation.getCurrentPosition(
    (position) => {
      form.latitude = String(position.coords.latitude);
      form.longitude = String(position.coords.longitude);
      locating.value = false;
    },
    () => {
      locationError.value = 'Unable to retrieve your location. Please allow location access.';
      locating.value = false;
    }
  );
}

async function submit() {
  submitting.value = true;

  const result = await create({
    entity: 'apply-store',
    data: {
      name: form.name.trim(),
      mobile: form.mobile.trim(),
      desc: form.desc.trim(),
      latitude: form.latitude,
      longitude: form.longitude,
    },
  }, true, 'Submitting application...', 'Store application submitted successfully.');

  submitting.value = false;

  if (result) {
    router.push('/');
  }
}
</script>

<style scoped lang="scss">
.apply-store-page {
  min-height: calc(100vh - 68px);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 32px 16px;
  background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 100%);
}

.apply-store-card {
  width: 100%;
  max-width: 560px;
  background: white;
  border-radius: 20px;
  box-shadow: 0 20px 60px rgba(30, 27, 75, 0.1);
  padding: 32px;
}

.form-header {
  display: flex;
  align-items: center;
  gap: 16px;
  margin-bottom: 28px;
}

.form-header-icon {
  width: 56px;
  height: 56px;
  border-radius: 14px;
  background: linear-gradient(135deg, #312e81 0%, #6d28d9 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 8px 20px rgba(79, 70, 229, 0.3);
  flex-shrink: 0;
}

.form-title {
  font-size: 24px;
  font-weight: 800;
  color: #1e1b4b;
  line-height: 1.2;
  margin: 0;
}

.form-subtitle {
  font-size: 14px;
  color: #6b7280;
  margin: 4px 0 0;
}

.apply-store-form {
  display: flex;
  flex-direction: column;
  gap: 18px;
}

.field-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.field-label {
  font-size: 13px;
  font-weight: 700;
  color: #374151;
}

.field-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.location-section {
  display: flex;
  flex-direction: column;
  gap: 12px;
  padding: 16px;
  background: #f8fafc;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
}

.location-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  flex-wrap: wrap;
}

.location-error {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 13px;
  color: #dc2626;
}

.submit-btn {
  height: 48px;
  border-radius: 12px;
  background: linear-gradient(135deg, #312e81 0%, #6d28d9 100%);
  color: white;
  font-weight: 700;
  font-size: 15px;
  box-shadow: 0 4px 16px rgba(79, 70, 229, 0.3);

  &:hover {
    transform: translateY(-1px);
    box-shadow: 0 8px 24px rgba(79, 70, 229, 0.4);
  }
}

@media (max-width: 480px) {
  .apply-store-card {
    padding: 24px;
  }

  .field-row {
    grid-template-columns: 1fr;
  }
}
</style>
