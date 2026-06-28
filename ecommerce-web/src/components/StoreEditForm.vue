<template>
  <q-card flat class="form-card">
    <q-form @submit.prevent="handleSubmit" ref="myForm">

      <!-- Store Information Section -->
      <div class="form-section-card q-mb-lg">
        <div class="form-section-header">
          <div class="form-section-icon">
            <q-icon name="info" size="18px" color="white" />
          </div>
          <div>
            <div class="form-section-title">Store Information</div>
            <div class="form-section-sub">Basic details about your store</div>
          </div>
        </div>
        <div class="card-divider"></div>
        <div class="form-section-body">
          <div class="form-row">
            <div class="form-col">
              <q-input clearable v-model="localStore.name" dense outlined label="Store Name"
                :rules="[(val) => (val && val.length > 0) || 'Store name is required.']" hide-bottom-space
                class="dark-input">
                <template v-slot:prepend>
                  <q-icon name="store" color="grey-5" />
                </template>
              </q-input>
            </div>
            <div class="form-col">
              <q-input clearable v-model="localStore.mobile" dense outlined label="Mobile Number"
                :rules="[(val) => (val && val.length > 0) || 'Mobile number is required.']" hide-bottom-space
                class="dark-input">
                <template v-slot:prepend>
                  <q-icon name="phone" color="grey-5" />
                </template>
              </q-input>
            </div>
          </div>

          <div class="q-mt-md" v-if="props.isActive">
            <div class="status-toggle-row">
              <div>
                <div class="field-label" style="margin-bottom: 2px;">Store Status</div>
                <div class="status-toggle-hint">{{ localStore.is_active ? 'Store is visible to customers' : 'Store is hidden from customers' }}</div>
                </div>
              <q-toggle v-model="localStore.is_active" :color="localStore.is_active ? 'positive' : 'grey-6'"
                keep-color />
            </div>
          </div>

          <div class="q-mt-lg">
            <label class="field-label">Description</label>
            <div class="wysiwyg-editor">
              <div class="editor-toolbar">
                <q-btn flat dense size="sm" @click="execCommand('bold')" icon="format_bold" class="toolbar-btn">
                  <q-tooltip>Bold</q-tooltip>
                </q-btn>
                <q-btn flat dense size="sm" @click="execCommand('italic')" icon="format_italic" class="toolbar-btn">
                  <q-tooltip>Italic</q-tooltip>
                </q-btn>
                <q-btn flat dense size="sm" @click="execCommand('underline')" icon="format_underlined"
                  class="toolbar-btn">
                  <q-tooltip>Underline</q-tooltip>
                </q-btn>
                <div class="toolbar-divider"></div>
                <q-btn flat dense size="sm" @click="execCommand('insertUnorderedList')" icon="format_list_bulleted"
                  class="toolbar-btn">
                  <q-tooltip>Bullet List</q-tooltip>
                </q-btn>
                <q-btn flat dense size="sm" @click="execCommand('insertOrderedList')" icon="format_list_numbered"
                  class="toolbar-btn">
                  <q-tooltip>Numbered List</q-tooltip>
                </q-btn>
              </div>
              <div class="editor-content" contenteditable="true" @input="onEditorInput" ref="editorRef"
                data-placeholder="Enter store description..."></div>
            </div>
            <div v-if="!store.desc" class="field-error">Description is required.</div>
          </div>

          <div v-if="localStore.images && localStore.images.length > 0" class="q-mt-lg">
            <label class="field-label">Verification Documents</label>
            <div class="store-images-grid">
              <div v-for="image in localStore.images" :key="image.id" class="store-image-item"
                @click="openLightbox(image.path_url)">
                <img :src="image.path_thumbnail" :alt="image.name" class="store-image" />
                <div class="store-image-overlay"><q-icon name="zoom_in" size="22px" color="white" /></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Image Lightbox Modal -->
      <q-dialog v-model="lightboxOpen" maximized transition-show="fade" transition-hide="fade">
        <div class="lightbox-backdrop" @click.self="closeLightbox" @wheel.prevent="onWheel">
          <q-btn round flat icon="close" color="white" class="lightbox-close" @click="closeLightbox" />
          <div class="lightbox-actions">
            <q-btn round flat icon="zoom_in" color="white" @click="lightboxZoomIn" />
            <span class="lightbox-zoom-label">{{ Math.round(zoomScale * 100) }}%</span>
            <q-btn round flat icon="zoom_out" color="white" @click="lightboxZoomOut" />
            <q-btn round flat icon="zoom_out_map" color="white" @click="resetZoom" />
          </div>
          <div class="lightbox-img-wrap">
            <img :src="lightboxSrc" class="lightbox-img" :style="{ transform: `scale(${zoomScale})` }"
              draggable="false" />
          </div>
        </div>
      </q-dialog>

      <!-- Location Section -->
      <div class="form-section-card q-mb-lg">
        <div class="form-section-header">
          <div class="form-section-icon form-section-icon--green">
            <q-icon name="place" size="18px" color="white" />
          </div>
          <div>
            <div class="form-section-title">Store Location</div>
            <div class="form-section-sub">Drag the map marker to set the location</div>
          </div>
        </div>
        <div class="card-divider"></div>
        <div class="form-section-body">

          <div class="location-hint q-mb-lg">
            <q-icon name="info" size="16px" class="q-mr-sm" />
            Drag the marker on the map to set the store location. Coordinates will update automatically.
          </div>

          <div class="coordinates-display q-mb-lg">
            <div class="coordinate-item">
              <div class="coordinate-icon">
                <q-icon name="my_location" size="16px" color="white" />
              </div>
              <div>
                <div class="coordinate-label">Latitude</div>
                <div class="coordinate-value">{{ localStore.latitude?.toFixed(6) || 'N/A' }}</div>
              </div>
            </div>
            <div class="coordinate-item">
              <div class="coordinate-icon">
                <q-icon name="explore" size="16px" color="white" />
              </div>
              <div>
                <div class="coordinate-label">Longitude</div>
                <div class="coordinate-value">{{ localStore.longitude?.toFixed(6) || 'N/A' }}</div>
              </div>
            </div>
          </div>

          <div class="map-container">
            <GoogleMap ref="mapRef" :api-key="GOOGLE_MAP_API_KEY" :map-id="GOOGLE_MAP_ID" class="google-map"
              :center="{ lat: localStore.latitude || 14.5995, lng: localStore.longitude || 120.9842 }"
              :zoom="currentZoom" :draggable="true" :clickable-icons="false">
              <AdvancedMarker :options="getStoreMarkerOptions()" @drag="markerDrag">
                <InfoWindow v-model="showInfoWindow" :options="{
                  position: { lat: localStore.latitude || 14.5995, lng: localStore.longitude || 120.9842 },
                  headerContent: '&nbsp;&nbsp;&nbsp;' + (localStore.name || 'Store Location'),
                  disableAutoPan: false
                }">
                  <div class="info-window-content">
                    <div class="info-window-header">
                      <q-icon name="store" color="primary" size="sm" class="q-mr-xs" />
                      <span class="text-weight-bold">{{ localStore.name || 'Store Location' }}</span>
                    </div>
                    <div class="info-window-body">
                      <p class="text-caption text-grey-7 q-ma-none">Drag the marker to update the store location</p>
                      <div class="text-caption q-mt-xs">
                        <strong>Lat:</strong> {{ localStore.latitude?.toFixed(6) || 'N/A' }}<br>
                        <strong>Lng:</strong> {{ localStore.longitude?.toFixed(6) || 'N/A' }}
                      </div>
                    </div>
                  </div>
                </InfoWindow>
              </AdvancedMarker>
            </GoogleMap>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="form-actions">
        <q-btn unelevated type="submit" icon="save" label="Update Store" class="submit-btn" :loading="isSubmitting" />
        <q-btn flat icon="cancel" label="Cancel" @click="handleCancel" class="cancel-btn" />
      </div>

    </q-form>
  </q-card>
</template>

<script lang="ts" setup>
import { GOOGLE_MAP_API_KEY, GOOGLE_MAP_ID } from 'src/boot/constant';
import { GoogleMap, AdvancedMarker, InfoWindow } from 'vue3-google-map';
import { ref, watch, onMounted, nextTick } from 'vue';
import type { QForm } from 'quasar';

interface StoreImage {
  id: number;
  path_url: string;
  path_thumbnail: string;
  name: string;
  is_primary: number;
  optimus_id: number;
}

interface StoreData {
  name: string;
  desc: string;
  mobile: string;
  latitude: number;
  longitude: number;
  optimus_id: number;
  is_active: boolean;
  images?: StoreImage[];
}

interface Props {
  store: StoreData;
  isSubmitting: boolean;
  isActive: boolean;
}

interface Emits {
  (e: 'submit', data: StoreData): void;
  (e: 'cancel'): void;
}

const props = defineProps<Props>();
const emit = defineEmits<Emits>();

const localStore = ref<StoreData>({ ...props.store });

watch(() => props.store, (newStore) => {
  localStore.value = { ...newStore };
}, { deep: true });

const currentZoom = ref(15);
const showInfoWindow = ref(true);
const mapRef = ref<any>(null);

const lightboxOpen = ref(false);
const lightboxSrc = ref('');
const zoomScale = ref(1);

const openLightbox = (src: string) => {
  lightboxSrc.value = src;
  zoomScale.value = 1;
  lightboxOpen.value = true;
};

const closeLightbox = () => {
  lightboxOpen.value = false;
};

const lightboxZoomIn = () => { zoomScale.value = Math.min(zoomScale.value + 0.25, 5); };
const lightboxZoomOut = () => { zoomScale.value = Math.max(zoomScale.value - 0.25, 0.25); };
const resetZoom = () => { zoomScale.value = 1; };

const onWheel = (e: WheelEvent) => {
  const delta = e.deltaY > 0 ? -0.1 : 0.1;
  zoomScale.value = Math.min(Math.max(zoomScale.value + delta, 0.25), 5);
};
const myForm = ref<QForm | null>(null);
const editorRef = ref<HTMLElement | null>(null);

// WYSIWYG Editor Functions
const execCommand = (command: string) => {
  document.execCommand(command, false, undefined);
  editorRef.value?.focus();
};

const onEditorInput = () => {
  if (editorRef.value) {
    localStore.value.desc = editorRef.value.innerHTML;
  }
};

// Watch for desc changes to update editor content
watch(() => localStore.value.desc, async (newDesc) => {
  if (editorRef.value && newDesc && editorRef.value.innerHTML !== newDesc) {
    await nextTick();
    editorRef.value.innerHTML = newDesc;
  }
});

const getStoreMarkerOptions = () => {
  return {
    position: { lat: localStore.value.latitude || 14.5995, lng: localStore.value.longitude || 120.9842 },
    gmpDraggable: true,
    title: localStore.value.name || 'Store Location',
    content: createStoreMarkerElement(),
  };
};

const createStoreMarkerElement = (): HTMLElement => {
  const markerDiv = document.createElement('div');
  markerDiv.className = 'custom-marker store-marker';
  markerDiv.innerHTML = `
    <div class="marker-pulse"></div>
    <div class="marker-icon">
      <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z" fill="#FFFFFF"/>
      </svg>
    </div>
  `;
  return markerDiv;
};

const markerDrag = (e: { latLng: google.maps.LatLng }) => {
  localStore.value.latitude = e.latLng.lat();
  localStore.value.longitude = e.latLng.lng();
};

const zoomIn = () => {
  const map = mapRef.value?.$mapObject || mapRef.value?.map || mapRef.value?.$map;
  if (map) {
    const currentZoomLevel = map.getZoom() || currentZoom.value;
    if (currentZoomLevel < 21) {
      const newZoom = currentZoomLevel + 1;
      map.setZoom(newZoom);
      currentZoom.value = newZoom;
    }
  }
};

const zoomOut = () => {
  const map = mapRef.value?.$mapObject || mapRef.value?.map || mapRef.value?.$map;
  if (map) {
    const currentZoomLevel = map.getZoom() || currentZoom.value;
    if (currentZoomLevel > 1) {
      const newZoom = currentZoomLevel - 1;
      map.setZoom(newZoom);
      currentZoom.value = newZoom;
    }
  }
};

const waitForGoogleMaps = () => {
  return new Promise((resolve) => {
    const checkGoogleMaps = () => {
      if (window.google &&
        window.google.maps &&
        window.google.maps.Map) {
        resolve(void 0);
      } else {
        setTimeout(checkGoogleMaps, 100);
      }
    };
    checkGoogleMaps();
  });
};

const waitForMapReady = () => {
  return new Promise((resolve) => {
    const checkMapReady = () => {
      const map = mapRef.value?.$mapObject || mapRef.value?.map || mapRef.value?.$map;
      if (map) {
        addZoomControls(map);
        resolve(void 0);
      } else {
        setTimeout(checkMapReady, 200);
      }
    };
    checkMapReady();
  });
};

const addZoomControls = (map: google.maps.Map) => {
  const zoomControlDiv = document.createElement('div');
  zoomControlDiv.style.cssText = `
    display: flex;
    flex-direction: column;
    gap: 2px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    overflow: hidden;
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 1000;
    pointer-events: auto;
  `;

  const zoomInButton = document.createElement('button');
  zoomInButton.style.cssText = `
    width: 40px;
    height: 40px;
    border: none;
    background: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    padding: 0;
    margin: 0;
    font-size: 20px;
    font-weight: 600;
    color: #333;
    user-select: none;
    border-bottom: 1px solid #e0e0e0;
  `;
  zoomInButton.innerHTML = '<span style="display: flex; align-items: center; justify-content: center; width: 100%; height: 100%; line-height: 1;">+</span>';
  zoomInButton.title = 'Zoom in';
  zoomInButton.addEventListener('click', (e) => {
    e.stopPropagation();
    zoomIn();
  });
  zoomInButton.addEventListener('mouseenter', () => {
    zoomInButton.style.background = '#f5f5f5';
  });
  zoomInButton.addEventListener('mouseleave', () => {
    zoomInButton.style.background = 'white';
  });
  zoomInButton.addEventListener('mousedown', () => {
    zoomInButton.style.background = '#e0e0e0';
    zoomInButton.style.transform = 'scale(0.95)';
  });
  zoomInButton.addEventListener('mouseup', () => {
    zoomInButton.style.background = '#f5f5f5';
    zoomInButton.style.transform = 'scale(1)';
  });

  const zoomOutButton = document.createElement('button');
  zoomOutButton.style.cssText = `
    width: 40px;
    height: 40px;
    border: none;
    background: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    padding: 0;
    margin: 0;
    font-size: 20px;
    font-weight: 600;
    color: #333;
    user-select: none;
  `;
  zoomOutButton.innerHTML = '<span style="display: flex; align-items: center; justify-content: center; width: 100%; height: 100%; line-height: 1;">−</span>';
  zoomOutButton.title = 'Zoom out';
  zoomOutButton.addEventListener('click', (e) => {
    e.stopPropagation();
    zoomOut();
  });
  zoomOutButton.addEventListener('mouseenter', () => {
    zoomOutButton.style.background = '#f5f5f5';
  });
  zoomOutButton.addEventListener('mouseleave', () => {
    zoomOutButton.style.background = 'white';
  });
  zoomOutButton.addEventListener('mousedown', () => {
    zoomOutButton.style.background = '#e0e0e0';
    zoomOutButton.style.transform = 'scale(0.95)';
  });
  zoomOutButton.addEventListener('mouseup', () => {
    zoomOutButton.style.background = '#f5f5f5';
    zoomOutButton.style.transform = 'scale(1)';
  });

  zoomControlDiv.appendChild(zoomInButton);
  zoomControlDiv.appendChild(zoomOutButton);

  setTimeout(() => {
    const mapContainer = map.getDiv();
    if (mapContainer) {
      mapContainer.appendChild(zoomControlDiv);
    }
  }, 200);
};

const handleSubmit = () => {
  myForm.value?.validate().then((success: any) => {
    if (success) {
      emit('submit', localStore.value);
    }
  });
};

const handleCancel = () => {
  emit('cancel');
};

onMounted(async () => {
  await nextTick();
  await waitForGoogleMaps();
  await waitForMapReady();

  // Initialize editor content
  if (editorRef.value && localStore.value.desc) {
    editorRef.value.innerHTML = localStore.value.desc;
  }
});
</script>

<style scoped lang="scss">
// ── Dark theme tokens ──────────────────────────────────────────────────────
$dark-base: #0f172a;
$dark-card: #1e293b;
$dark-elevated: #273549;
$border: rgba(255, 255, 255, 0.08);
$accent: #6366f1;
$accent-2: #7c3aed;
$green: #10b981;
$green-2: #059669;
$white: #ffffff;
$muted: rgba(255, 255, 255, 0.5);

// ── Card wrapper ────────────────────────────────────────────────────────────
.form-card {
  background: transparent !important;
}

// ── Section cards ───────────────────────────────────────────────────────────
.form-section-card {
  background: $dark-card;
  border: 1px solid $border;
  border-radius: 20px;
  box-shadow: 0 8px 40px rgba(0, 0, 0, 0.25);
  overflow: hidden;
}

.form-section-header {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 20px 24px;
  background: $dark-elevated;
}

.form-section-icon {
  width: 40px;
  height: 40px;
  border-radius: 11px;
  background: linear-gradient(135deg, $accent 0%, $accent-2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 3px 12px rgba(99, 102, 241, 0.35);
  flex-shrink: 0;

  &--green {
    background: linear-gradient(135deg, $green 0%, $green-2 100%);
    box-shadow: 0 3px 12px rgba(16, 185, 129, 0.35);
  }
}

.form-section-title {
  font-size: 15px;
  font-weight: 800;
  color: $white;
  line-height: 1.2;
}

.form-section-sub {
  font-size: 12px;
  color: $muted;
  margin-top: 2px;
}

.card-divider {
  height: 1px;
  background: $border;
}

.form-section-body {
  padding: 24px;
}

// ── Form layout ─────────────────────────────────────────────────────────────
.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.form-col {
  display: flex;
  flex-direction: column;
}

// ── Dark inputs ─────────────────────────────────────────────────────────────
.dark-input {
  :deep(.q-field__control) {
    background: $dark-elevated !important;
    border-radius: 12px !important;
  }

  :deep(.q-field__native),
  :deep(.q-field__input) {
    color: $white !important;
  }

  :deep(.q-field__label) {
    color: $muted !important;
  }

  :deep(.q-field__prepend .q-icon),
  :deep(.q-field__append .q-icon) {
    color: $muted !important;
  }

  :deep(.q-field--outlined .q-field__control:before) {
    border-color: $border !important;
  }

  :deep(.q-field--outlined:hover .q-field__control:before) {
    border-color: rgba(99, 102, 241, 0.4) !important;
  }

  :deep(.q-field--focused .q-field__control:before) {
    border-color: $accent !important;
  }
}

// ── Store images gallery ────────────────────────────────────────────────────
.store-images-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
  gap: 12px;
}

.store-image-item {
  position: relative;
  aspect-ratio: 1;
  border-radius: 12px;
  overflow: hidden;
  border: 1px solid $border;
  background: $dark-elevated;
  transition: border-color 0.2s;

  &:hover {
    border-color: rgba(99, 102, 241, 0.4);
  }
}

.store-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.store-image-overlay {
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0);
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.2s;
  pointer-events: none;
}

.store-image-item {
  cursor: pointer;

  &:hover .store-image-overlay {
    background: rgba(0, 0, 0, 0.45);
  }
}

// ── Lightbox ────────────────────────────────────────────────────────────────
.lightbox-backdrop {
  position: relative;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.92);
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

.lightbox-close {
  position: absolute;
  top: 16px;
  right: 16px;
  z-index: 10;
  background: rgba(255, 255, 255, 0.1) !important;

  &:hover {
    background: rgba(255, 255, 255, 0.2) !important;
  }
}

.lightbox-actions {
  position: absolute;
  bottom: 24px;
  left: 50%;
  transform: translateX(-50%);
  z-index: 10;
  display: flex;
  align-items: center;
  gap: 8px;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 40px;
  padding: 6px 16px;
  backdrop-filter: blur(8px);
}

.lightbox-zoom-label {
  color: $white;
  font-size: 13px;
  font-weight: 700;
  min-width: 44px;
  text-align: center;
}

.lightbox-img-wrap {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  height: 100%;
  overflow: hidden;
}

.lightbox-img {
  max-width: 90vw;
  max-height: 85vh;
  object-fit: contain;
  border-radius: 8px;
  transition: transform 0.15s ease;
  user-select: none;
}

// ── Status toggle ────────────────────────────────────────────────────────────
.status-toggle-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  background: $dark-elevated;
  border: 1px solid $border;
  border-radius: 12px;
  padding: 14px 16px;
}

.status-toggle-hint {
  font-size: 12px;
  color: $muted;
  margin-top: 2px;
}

// ── Field label / error ──────────────────────────────────────────────────────
.field-label {
  display: block;
  font-weight: 700;
  color: $muted;
  margin-bottom: 10px;
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.field-error {
  color: #f87171;
  font-size: 12px;
  margin-top: 6px;
}

// ── WYSIWYG editor ──────────────────────────────────────────────────────────
.wysiwyg-editor {
  border: 1px solid $border;
  border-radius: 14px;
  overflow: hidden;
  background: $dark-elevated;
  transition: border-color 0.2s;

  &:focus-within {
    border-color: $accent;
    box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.15);
  }
}

.editor-toolbar {
  display: flex;
  align-items: center;
  gap: 2px;
  padding: 8px 12px;
  background: rgba(255, 255, 255, 0.03);
  border-bottom: 1px solid $border;
}

.toolbar-btn {
  color: $muted !important;
  border-radius: 8px !important;

  &:hover {
    color: $white !important;
    background: rgba(255, 255, 255, 0.08) !important;
  }
}

.toolbar-divider {
  width: 1px;
  height: 20px;
  background: $border;
  margin: 0 4px;
}

.editor-content {
  min-height: 150px;
  padding: 16px;
  outline: none;
  line-height: 1.7;
  color: $white;
  font-size: 14px;

  &[contenteditable]:empty::before {
    content: attr(data-placeholder);
    color: $muted;
  }
}

// ── Location hint ────────────────────────────────────────────────────────────
.location-hint {
  display: flex;
  align-items: flex-start;
  gap: 8px;
  background: rgba(16, 185, 129, 0.08);
  border: 1px solid rgba(16, 185, 129, 0.2);
  color: #6ee7b7;
  font-size: 13px;
  font-weight: 500;
  padding: 12px 16px;
  border-radius: 12px;
  line-height: 1.5;

  .q-icon {
    color: #6ee7b7 !important;
    flex-shrink: 0;
    margin-top: 1px;
  }
}

// ── Coordinates display ───────────────────────────────────────────────────────
.coordinates-display {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.coordinate-item {
  display: flex;
  align-items: center;
  gap: 12px;
  background: $dark-elevated;
  border: 1px solid $border;
  border-radius: 12px;
  padding: 14px 16px;
}

.coordinate-icon {
  width: 32px;
  height: 32px;
  border-radius: 9px;
  background: linear-gradient(135deg, $green 0%, $green-2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  box-shadow: 0 3px 10px rgba(16, 185, 129, 0.3);
}

.coordinate-label {
  font-size: 11px;
  color: $muted;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 3px;
}

.coordinate-value {
  font-size: 14px;
  font-weight: 700;
  color: $white;
  font-family: 'JetBrains Mono', 'Fira Code', monospace;
}

// ── Map ────────────────────────────────────────────────────────────────────
.map-container {
  border-radius: 16px;
  overflow: hidden;
  border: 1px solid $border;
  box-shadow: 0 4px 24px rgba(0, 0, 0, 0.3);
}

.google-map {
  width: 100%;
  height: 400px;
}

// ── Action buttons ───────────────────────────────────────────────────────────
.form-actions {
  display: flex;
  align-items: center;
  gap: 12px;
  padding-top: 8px;
}

.submit-btn {
  background: linear-gradient(135deg, $accent 0%, $accent-2 100%) !important;
  color: $white !important;
  border-radius: 12px !important;
  font-weight: 700 !important;
  font-size: 14px !important;
  text-transform: none !important;
  letter-spacing: 0 !important;
  height: 44px !important;
  padding: 0 28px !important;
  box-shadow: 0 4px 16px rgba(99, 102, 241, 0.4) !important;
  transition: opacity 0.2s !important;

  &:hover {
    opacity: 0.88;
  }
}

.cancel-btn {
  color: $muted !important;
  border: 1px solid $border !important;
  border-radius: 12px !important;
  text-transform: none !important;
  font-weight: 600 !important;
  letter-spacing: 0 !important;
  height: 44px !important;
  padding: 0 20px !important;

  &:hover {
    color: $white !important;
    background: rgba(255, 255, 255, 0.06) !important;
  }
}

// ── Responsive ─────────────────────────────────────────────────────────────
@media (max-width: 768px) {
  .form-row {
    grid-template-columns: 1fr;
  }

  .coordinates-display {
    grid-template-columns: 1fr;
  }

  .form-section-body {
    padding: 16px;
  }

  .form-section-header {
    padding: 16px 18px;
  }

  .google-map {
    height: 300px;
  }

  .form-actions {
    flex-direction: column;
    align-items: stretch;
  }

  .submit-btn,
  .cancel-btn {
    width: 100%;
  }
}
</style>

<style>
.form-section-card .q-card__section {
  background: transparent !important;
  color: #ffffff !important;
}

/* Store marker */
.store-marker {
  position: relative;
  width: 44px;
  height: 44px;
  display: flex;
  align-items: center;
  justify-content: center;
  transform-origin: center bottom;
  transition: transform 0.2s ease;
}

.store-marker:hover {
  transform: scale(1.1) translateY(-2px);
}

.marker-icon {
  position: relative;
  z-index: 2;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4), 0 0 0 4px rgba(16, 185, 129, 0.15);
  display: flex;
  align-items: center;
  justify-content: center;
}

.marker-icon svg {
  width: 22px;
  height: 22px;
  filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.15));
}

.marker-pulse {
  position: absolute;
  z-index: 1;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: rgba(16, 185, 129, 0.35);
  animation: markerPulse 2s ease-out infinite;
}

@keyframes markerPulse {
  0% {
    transform: scale(1);
    opacity: 0.6;
  }

  100% {
    transform: scale(2.4);
    opacity: 0;
  }
}
</style>
