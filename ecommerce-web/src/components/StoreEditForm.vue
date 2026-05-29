<template>
  <!-- Form Section -->
  <q-card flat bordered class="form-card">
    <q-card-section>
      <q-form @submit.prevent="handleSubmit" class="edit-store-form" ref="myForm">
          <!-- Store Information Section -->
          <div class="form-section">
            <div class="section-header">
              <q-icon name="info" color="primary" size="24px" class="q-mr-sm" />
              <h2 class="section-title">Store Information</h2>
            </div>
            
            <div class="form-row">
              <div class="form-col">
                <q-input
                  clearable
                  v-model="localStore.name"
                  dense
                  outlined
                  label="Store Name"
                  :rules="[(val) => (val && val.length > 0) || 'Store name is required.']"
                  hide-bottom-space
                  class="form-input"
                >
                  <template v-slot:prepend>
                    <q-icon name="store" color="primary" />
                  </template>
                </q-input>
              </div>
              
              <div class="form-col">
                <q-input
                  clearable
                  v-model="localStore.mobile"
                  dense
                  outlined
                  label="Mobile Number"
                  :rules="[(val) => (val && val.length > 0) || 'Mobile number is required.']"
                  hide-bottom-space
                  class="form-input"
                >
                  <template v-slot:prepend>
                    <q-icon name="phone" color="primary" />
                  </template>
                </q-input>
              </div>
            </div>
          </div>

          <div class="q-mt-md">
            <div class="form-col">
              <label class="field-label">Description</label>
              <div class="wysiwyg-editor">
                <div class="editor-toolbar">
                  <q-btn flat dense size="sm" @click="execCommand('bold')" icon="format_bold">
                    <q-tooltip>Bold</q-tooltip>
                  </q-btn>
                  <q-btn flat dense size="sm" @click="execCommand('italic')" icon="format_italic">
                    <q-tooltip>Italic</q-tooltip>
                  </q-btn>
                  <q-btn flat dense size="sm" @click="execCommand('underline')" icon="format_underlined">
                    <q-tooltip>Underline</q-tooltip>
                  </q-btn>
                  <q-btn flat dense size="sm" @click="execCommand('insertUnorderedList')" icon="format_list_bulleted">
                    <q-tooltip>Bullet List</q-tooltip>
                  </q-btn>
                  <q-btn flat dense size="sm" @click="execCommand('insertOrderedList')" icon="format_list_numbered">
                    <q-tooltip>Numbered List</q-tooltip>
                  </q-btn>
                </div>
                <div
                  class="editor-content"
                  contenteditable="true"
                  @input="onEditorInput"
                  ref="editorRef"
                  data-placeholder="Enter store description..."
                ></div>
              </div>
              <div v-if="!store.desc" class="field-error">Description is required.</div>
            </div>
          </div>

          <!-- Location Section -->
          <div class="form-section q-mt-lg">
            <div class="section-header">
              <q-icon name="place" color="primary" size="24px" class="q-mr-sm" />
              <h2 class="section-title">Store Location</h2>
            </div>
            
            <div class="location-info q-mb-md">
              <q-banner rounded class="bg-blue-1 text-blue-8">
                <template v-slot:avatar>
                  <q-icon name="info" color="primary" />
                </template>
                Drag the marker on the map to set the store location. The coordinates will update automatically.
              </q-banner>
            </div>

            <div class="coordinates-display q-mb-md">
              <div class="coordinate-item">
                <q-icon name="my_location" color="primary" size="20px" class="q-mr-sm" />
                <div>
                  <div class="coordinate-label">Latitude</div>
                  <div class="coordinate-value">{{ localStore.latitude?.toFixed(6) || 'N/A' }}</div>
                </div>
              </div>
              <div class="coordinate-item">
                <q-icon name="my_location" color="primary" size="20px" class="q-mr-sm" />
                <div>
                  <div class="coordinate-label">Longitude</div>
                  <div class="coordinate-value">{{ localStore.longitude?.toFixed(6) || 'N/A' }}</div>
                </div>
              </div>
            </div>

            <div class="map-container">
              <GoogleMap 
                ref="mapRef" 
                :api-key="GOOGLE_MAP_API_KEY" 
                :map-id="GOOGLE_MAP_ID" 
                class="google-map"
                :center="{ lat: localStore.latitude || 14.5995, lng: localStore.longitude || 120.9842 }" 
                :zoom="currentZoom" 
                :draggable="true" 
                :clickable-icons="false"
              >
                <!-- Store Location Marker -->
                <AdvancedMarker 
                  :options="getStoreMarkerOptions()" 
                  @drag="markerDrag"
                >
                  <InfoWindow 
                    v-model="showInfoWindow"
                    :options="{ 
                      position: { lat: localStore.latitude || 14.5995, lng: localStore.longitude || 120.9842 }, 
                      headerContent: '&nbsp;&nbsp;&nbsp;' + (localStore.name || 'Store Location'), 
                      disableAutoPan: false 
                    }"
                  >
                    <div class="info-window-content">
                      <div class="info-window-header">
                        <q-icon name="store" color="primary" size="sm" class="q-mr-xs" />
                        <span class="text-weight-bold">{{ localStore.name || 'Store Location' }}</span>
                      </div>
                      <div class="info-window-body">
                        <p class="text-caption text-grey-7 q-ma-none">
                          Drag the marker to update the store location
                        </p>
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

          <!-- Action Buttons -->
          <div class="form-actions q-mt-xl">
            <q-btn 
              unelevated 
              color="primary" 
              type="submit" 
              label="Update Store"
              icon="save"
              class="submit-btn"
              :loading="isSubmitting"
            />
            <q-btn 
              flat 
              color="grey-8" 
              label="Cancel"
              icon="cancel"
              @click="handleCancel"
              class="cancel-btn"
            />
          </div>
        </q-form>
      </q-card-section>
    </q-card>
</template>

<script lang="ts" setup>
import { GOOGLE_MAP_API_KEY, GOOGLE_MAP_ID } from 'src/boot/constant';
import { GoogleMap, AdvancedMarker, InfoWindow } from 'vue3-google-map';
import { ref, watch, onMounted, nextTick } from 'vue';
import type { QForm } from 'quasar';

interface StoreData {
  name: string;
  desc: string;
  mobile: string;
  latitude: number;
  longitude: number;
  optimus_id: number;
}

interface Props {
  store: StoreData;
  isSubmitting: boolean;
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
@import 'src/css/dashboard/all-stores/edit.scss';

.field-label {
  display: block;
  font-weight: 600;
  color: #333;
  margin-bottom: 8px;
  font-size: 14px;
}

.field-error {
  color: #c10015;
  font-size: 12px;
  margin-top: 4px;
}

.description-full-width {
  .form-col {
    width: 100%;
    flex: 1 1 100%;
  }
}

.wysiwyg-editor {
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  overflow: hidden;
  background: white;

  &:focus-within {
    border-color: #1976d2;
  }
}

.editor-toolbar {
  display: flex;
  gap: 4px;
  padding: 8px;
  background: #f5f5f5;
  border-bottom: 1px solid #e0e0e0;
}

.editor-content {
  min-height: 150px;
  padding: 16px;
  outline: none;
  line-height: 1.6;

  &[contenteditable]:empty::before {
    content: attr(data-placeholder);
    color: #999;
  }
}
</style>
