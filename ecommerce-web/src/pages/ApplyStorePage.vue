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

        <div class="field-group">
          <label class="field-label">Verification Document</label>
          <div class="upload-area">
            <div class="upload-icon-wrap">
              <q-icon name="cloud_upload" size="32px" color="white" />
            </div>
            <div class="upload-text">Upload government ID (individual) or business permit</div>
            <div class="upload-hint">Your information is kept confidential and will not be published.</div>
            <q-file :model-value="form.images" label="Browse files" outlined dense multiple accept="image/*"
              class="upload-input" @update:model-value="appendImages">
              <template v-slot:prepend>
                <q-icon name="attach_file" />
              </template>
            </q-file>

            <div v-if="previewImages.length > 0" class="preview-grid">
              <div v-for="(file, index) in previewImages" :key="index" class="preview-item">
                <img :src="previewUrl(file)" class="preview-image" />
                <q-btn flat dense round icon="close" size="xs" class="remove-btn" @click="removeImage(index)" />
              </div>
            </div>
          </div>
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
import { computed, reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { axios } from 'src/boot/axios';
import { Loading, Notify } from 'quasar';

const router = useRouter();

const form = reactive({
  name: '',
  mobile: '',
  desc: '',
  latitude: '',
  longitude: '',
  images: [] as File[],
});

const locating = ref(false);
const submitting = ref(false);
const locationError = ref('');

const previewImages = computed(() => form.images);

const appendImages = (files: File | File[]) => {
  const incoming = Array.isArray(files) ? files : [files];
  incoming.forEach((file) => {
    if (!form.images.some((f) => f.name === file.name && f.size === file.size)) {
      form.images.push(file);
    }
  });
};

const previewUrl = (file: File) => URL.createObjectURL(file);

const removeImage = (index: number) => {
  form.images.splice(index, 1);
};

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
  Loading.show({ message: 'Submitting application...' });

  const formData = new FormData();
  formData.append('name', form.name.trim());
  formData.append('mobile', form.mobile.trim());
  formData.append('desc', form.desc.trim());
  formData.append('latitude', form.latitude);
  formData.append('longitude', form.longitude);

  form.images.forEach((file) => {
    formData.append('images[]', file);
  });

  try {
    await axios.post('/apply-store', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });

    Loading.hide();
    Notify.create({
      position: 'bottom',
      type: 'positive',
      message: 'Store application submitted successfully.',
    });
    router.push('/');
  } catch (err: any) {
    Loading.hide();
    Notify.create({
      position: 'bottom',
      type: 'negative',
      message: err.response?.data?.message || 'An error occurred while submitting your application.',
    });
  } finally {
    submitting.value = false;
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

.upload-area {
  border: 2px dashed rgba(99, 102, 241, 0.25);
  border-radius: 16px;
  padding: 28px 24px;
  background: rgba(99, 102, 241, 0.04);
  text-align: center;
  transition: all 0.25s ease;

  &:hover {
    border-color: rgba(99, 102, 241, 0.45);
    background: rgba(99, 102, 241, 0.08);
  }
}

.upload-icon-wrap {
  width: 56px;
  height: 56px;
  border-radius: 14px;
  background: linear-gradient(135deg, rgba(99, 102, 241, 0.2) 0%, rgba(124, 58, 237, 0.12) 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 14px;
}

.upload-text {
  font-size: 14px;
  color: #4b5563;
  font-weight: 600;
  margin-bottom: 4px;
}

.upload-hint {
  font-size: 12px;
  color: #6b7280;
  margin-bottom: 16px;
}

.upload-input {
  max-width: 280px;
  margin: 0 auto;

  :deep(.q-field__control) {
    border-radius: 10px !important;
  }
}

.preview-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(90px, 1fr));
  gap: 12px;
  margin-top: 20px;
}

.preview-item {
  position: relative;
  aspect-ratio: 1;
  border-radius: 12px;
  overflow: hidden;
  border: 1px solid #e2e8f0;
  transition: all 0.2s ease;

  &:hover {
    border-color: rgba(99, 102, 241, 0.3);
  }
}

.preview-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.remove-btn {
  position: absolute;
  top: 6px;
  right: 6px;
  width: 24px !important;
  height: 24px !important;
  background: rgba(239, 68, 68, 0.85) !important;
  color: white !important;
  border-radius: 8px !important;
  transition: all 0.2s ease;

  &:hover {
    background: #ef4444 !important;
    transform: scale(1.1);
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
