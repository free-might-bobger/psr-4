<template>
  <div class="checkout-page">
    <div class="checkout-container">
      <!-- Header -->
      <div class="checkout-header">
        <BreadCrumbsWrapper :bread-crumbs="[
          {
            name: 'Cart',
            path: '/cart',
          },
          {
            name: 'Checkout',
            path: '',
          },
        ]" />
      </div>

      <!-- Main Content -->
      <div class="checkout-content">
        <!-- Left Column: Map -->
        <div class="checkout-main">
          <div class="section-card map-section">
            <div class="section-header">
              <div class="section-title">
                <q-icon name="location_on" color="primary" size="24px" class="q-mr-sm" />
                <span>Delivery Location</span>
              </div>
              <q-chip size="sm" color="primary" text-color="white" icon="edit">
                Adjust on Map
              </q-chip>
            </div>
            <div class="search-location-wrapper">
              <q-input ref="searchInputRef" v-model="searchLocation" outlined dense
                placeholder="Search for a location ..." class="location-search-input">
                <template v-slot:prepend>
                  <q-icon name="search" />
                </template>
                <template v-slot:append>
                  <q-btn v-if="searchLocation" icon="close" flat round dense size="sm" @click="clearSearch" />
                </template>
              </q-input>
            </div>
            <div class="map-wrapper">
              <GoogleMap ref="mapRef" :api-key="GOOGLE_MAP_API_KEY" :map-id="GOOGLE_MAP_ID" class="checkout-map"
                :center="{ lat: lat, lng: lng }" :zoom="currentZoom" :draggable="true" :clickable-icons="false">
                <AdvancedMarker :options="getDeliveryMarkerOptions()" @drag="markerDrag">
                  <InfoWindow v-model="showInfoWindow" :options="{
                    position: { lat: lat, lng: lng },
                    headerContent: 'Delivery Location',
                    disableAutoPan: false
                  }">
                    <div class="info-window-content">
                      <div class="info-window-header">
                        <q-icon name="local_shipping" color="primary" size="sm" class="q-mr-xs" />
                        <span class="text-weight-bold">Delivery Location</span>
                      </div>
                    </div>
                  </InfoWindow>
                </AdvancedMarker>
              </GoogleMap>
            </div>
            <div class="map-hint">
              <q-icon name="info" size="xs" color="grey-6" class="q-mr-xs" />
              <span class="text-caption text-grey-7">Drag the marker to set your delivery location</span>
            </div>
          </div>
        </div>

        <!-- Right Column: Order Summary & Form -->
        <div class="checkout-sidebar">
          <!-- Order Summary -->
          <div class="section-card order-summary-card">
            <div class="section-header">
              <div class="section-title">
                <q-icon name="shopping_cart" color="primary" size="24px" class="q-mr-sm" />
                <span>Order Summary</span>
              </div>
              <q-chip size="sm" color="grey-3" text-color="grey-8">
                {{ countTotalItems }} items
              </q-chip>
            </div>
            <div class="order-summary-content">
              <div class="summary-row">
                <span class="summary-label">Subtotal</span>
                <span class="summary-value">{{ formatMoney(total) }}</span>
              </div>
              <div class="summary-row">
                <span class="summary-label">Delivery Charge</span>
                <span class="summary-value">{{ formatMoney(deliveryCharge) }}</span>
              </div>
              <q-separator class="q-my-md" />
              <div class="summary-row summary-total">
                <span class="summary-label total-label">Total</span>
                <span class="summary-value total-value">{{ decimalThousandSeparator(total + deliveryCharge) }}</span>
              </div>
            </div>
          </div>

          <!-- Contact Information -->
          <div class="section-card contact-card">
            <div class="section-header">
              <div class="section-title">
                <q-icon name="contact_phone" color="primary" size="24px" class="q-mr-sm" />
                <span>Contact Information</span>
              </div>
            </div>
            <q-form @submit="processCustomerOrder" ref="myForm">
              <q-input v-model="mobile" class="mobile-input" outlined label="Receiver's Mobile Number"
                placeholder="9XX XXX XXXX" :rules="[
                  async (val) =>
                    isValidMobileNumber(val) ||
                    'Please enter a valid mobile number.',
                ]" hide-bottom-space prefix="+63" dense>
                <template v-slot:prepend>
                  <q-icon name="phone" color="grey-7" />
                </template>
              </q-input>
              <q-input v-model="note" type="textarea" outlined label="Order Note (Optional)"
                placeholder="Add any special instructions for your order..." class="q-mb-md" dense
                input-style="min-height: 4.5em; height: 4.5em;">
                <template v-slot:prepend>
                  <q-icon name="notes" color="grey-7" />
                </template>
              </q-input>
              <q-btn type="submit" class="complete-order-btn full-width" label="Complete Order" color="primary"
                unelevated size="lg" icon="check_circle" />
            </q-form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { GOOGLE_MAP_API_KEY, GOOGLE_MAP_ID } from 'src/boot/constant';
import { GoogleMap, AdvancedMarker, InfoWindow } from 'vue3-google-map';
import BreadCrumbsWrapper from 'src/components/BreadCrumbsWrapper.vue';
import { ref, watch, nextTick, onMounted } from 'vue';
import { useCommonStore } from 'src/stores/common';
import { storeToRefs } from 'pinia';
import { create, isMobileExist } from 'src/boot/axios-call';
import { isValidMobileNumber } from 'src/boot/validators';
import { useQuasar } from 'quasar';
import { useRouter } from 'vue-router';
import { useUserCartStore } from 'src/stores/userCart';
import { useUserStore } from 'src/stores/user';
import {
  ItemOrder,
  CustomerOrder,
  GroupStoreItemInterface,
} from 'src/boot/interfaces';
import type { QForm } from 'quasar';
import { formatMoney, decimalThousandSeparator } from 'boot/utilities';

const $q = useQuasar();
const userCart = useUserCartStore();
const {
  total,
  groupByStore,
  selectedPaymenthMethod,
  selectedReceiveMethod,
  countTotalItems,
  deliveryCharge,
} = storeToRefs(userCart);

const router = useRouter();
const useCommon = useCommonStore();
const { lat, lng, mobile } = storeToRefs(useCommon);
const note = ref('');
const showInfoWindow = ref(true);
const mapRef = ref<HTMLElement | null>(null);
const currentZoom = ref(15);
const searchLocation = ref('');
const searchInputRef = ref<HTMLInputElement | null>(null);
let autocomplete: google.maps.places.Autocomplete | null = null;

// Create animated delivery location marker element
const createDeliveryMarkerElement = (): HTMLElement => {
  const markerDiv = document.createElement('div');
  markerDiv.className = 'custom-marker delivery-marker';
  markerDiv.innerHTML = `
    <div class="marker-pulse"></div>
    <div class="marker-icon">
      <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M20 8h-3V4H3c-1.1 0-2 .9-2 2v11h2c0 1.66 1.34 3 3 3s3-1.34 3-3h6c0 1.66 1.34 3 3 3s3-1.34 3-3h2v-5l-3-4zM6 18.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm13.5-9l1.96 2.5H17V9.5h2.5zm-1.5 9c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z" fill="#FFFFFF"/>
      </svg>
    </div>
  `;
  return markerDiv;
};

// Get delivery marker options
const getDeliveryMarkerOptions = () => {
  return {
    position: { lat: lat.value, lng: lng.value },
    gmpDraggable: true,
    title: 'Delivery Location',
    content: createDeliveryMarkerElement(),
  };
};

const markerDrag = (e: { latLng: google.maps.LatLng }) => {
  lat.value = e.latLng.lat();
  lng.value = e.latLng.lng();
};

// Initialize Google Places Autocomplete
const initAutocomplete = () => {
  if (searchInputRef.value && searchInputRef.value.$el) {
    const inputElement = searchInputRef.value.$el.querySelector('input');
    if (inputElement && window.google && window.google.maps && window.google.maps.places) {
      autocomplete = new google.maps.places.Autocomplete(inputElement, {
        fields: ['formatted_address', 'geometry', 'name'],
        types: ['geocode', 'establishment'],
      });

      autocomplete.addListener('place_changed', onPlaceChanged);
    }
  }
};

// Handle place selection from autocomplete
const onPlaceChanged = () => {
  if (!autocomplete) return;

  const place = autocomplete.getPlace();
  if (!place.geometry || !place.geometry.location) {
    $q.notify({
      message: 'No details available for input: \'' + place.name + '\'',
      type: 'warning',
      position: 'top',
      icon: 'warning'
    });
    return;
  }

  // Update lat/lng
  lat.value = place.geometry.location.lat();
  lng.value = place.geometry.location.lng();

  // Update search input with formatted address
  searchLocation.value = place.formatted_address || place.name || '';

  // Update map center
  const map = mapRef.value?.$mapObject || mapRef.value?.map || mapRef.value?.$map;
  if (map) {
    map.setCenter({ lat: lat.value, lng: lng.value });
    map.setZoom(16);
    currentZoom.value = 16;
  }

  $q.notify({
    message: 'Location updated successfully',
    type: 'positive',
    position: 'top',
    icon: 'check_circle'
  });
};

// Clear search input
const clearSearch = () => {
  searchLocation.value = '';
};

// Zoom functions
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
  // Create container for zoom controls
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

  // Zoom In Button
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

  // Zoom Out Button
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

  // Position the control
  setTimeout(() => {
    const mapContainer = map.getDiv();
    if (mapContainer) {
      mapContainer.appendChild(zoomControlDiv);
    }
  }, 200);
};

onMounted(async () => {
  await nextTick();
  await waitForMapReady();
  await initAutocomplete();
});

const myForm = ref<QForm | null>(null);

const showOldPasscode = ref(false);

watch(mobile, async (currentVal) => {
  if (!isValidMobileNumber(currentVal)) {
    return;
  }
  const result = await isMobileExist({ mobile: currentVal });
  if (result) {
    showOldPasscode.value = true;
  }
});

const storeId = ref(0);
const processCustomerOrder = async () => {
  let customerOrders: CustomerOrder[] = [];
  Object.entries(groupByStore.value as unknown as Record<string, GroupStoreItemInterface[]>).forEach(
    ([key, items]) => {
      const itemOrders: ItemOrder[] = items.map(
        (item: GroupStoreItemInterface) => {
          return {
            item_id: item.id,
            variations: item.variations,
            qty: item.count,
            unit_id: item.unit_id
          };
        }
      );
      storeId.value = Number(key);
      customerOrders.push({
        store_id: key,
        items: itemOrders,
      });
    }
  );

  const result = await create(
    {
      entity: 'my-transactions',
      data: {
        store_id: storeId.value,
        total: total.value,
        items: customerOrders,
        deliveryCharge: deliveryCharge.value,
        selectedReceiveMethod: selectedReceiveMethod.value,
        selectedPaymenthMethod: selectedPaymenthMethod.value,
        lat: lat.value,
        lng: lng.value,
        receivers_mobile: mobile.value,
        note: note.value,
      },
    },
    false
  );
  if (result) {
    userCart.emptyCart();
    router.push({
      path: '/dashboard/my-transactions',
    });
  }
};
</script>

<style scoped lang="scss">
.checkout-page {
  background: #f5f7fa;
  min-height: 100vh;
}

.checkout-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 24px;
}

.checkout-header {
  margin-bottom: 24px;
}

.checkout-content {
  display: grid;
  grid-template-columns: 1fr 400px;
  gap: 24px;
  align-items: start;
}

.section-card {
  background: #ffffff;
  border-radius: 16px;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
  overflow: hidden;
  transition: all 0.3s ease;

  &:hover {
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  }
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 24px;
  border-bottom: 1px solid #f0f0f0;
}

.section-title {
  display: flex;
  align-items: center;
  font-size: 18px;
  font-weight: 600;
  color: #1a1a1a;
}

.map-section {
  .search-location-wrapper {
    padding: 0 0 16px 0;
    margin: 0 12px;
  }

  .location-search-input {
    :deep(.q-field__control) {
      border-radius: 8px;
      border: 1px solid #e0e0e0;
      background: #fafafa;
    }

    :deep(.q-field__control:hover) {
      border-color: #d0d0d0;
    }

    :deep(.q-field--focused .q-field__control) {
      background: white;
      border-color: var(--q-primary);
    }
  }

  .map-wrapper {
    padding: 0;
  }

  .checkout-map {
    height: 500px;
    width: 100%;
  }

  .map-hint {
    padding: 16px 24px;
    background: #fafafa;
    border-top: 1px solid #f0f0f0;
    display: flex;
    align-items: center;
  }
}

.order-summary-card {
  margin-bottom: 24px;
}

.order-summary-content {
  padding: 24px;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 0;
}

.summary-label {
  font-size: 14px;
  color: #666;
  font-weight: 500;
}

.summary-value {
  font-size: 16px;
  color: #1a1a1a;
  font-weight: 600;
}

.summary-total {
  padding: 16px 0 8px;
}

.total-label {
  font-size: 16px;
  color: #1a1a1a;
  font-weight: 600;
}

.total-value {
  font-size: 20px;
  color: var(--q-primary);
  font-weight: 700;
}

.contact-card {
  .q-card-section {
    padding: 24px;
  }
}

.mobile-input {
  margin-bottom: 20px;
}

.complete-order-btn {
  height: 52px;
  font-size: 16px;
  font-weight: 600;
  border-radius: 10px;
  letter-spacing: 0.5px;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(25, 118, 210, 0.2);

  &:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(25, 118, 210, 0.3);
  }
}

// Custom animated marker styles
:deep(.custom-marker) {
  position: relative;
  width: 24px;
  height: 24px;
  cursor: pointer;
  animation: markerBounce 2s infinite;
}

:deep(.marker-icon) {
  position: relative;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
  transition: transform 0.3s ease;

  svg {
    width: 12px;
    height: 12px;
  }
}

:deep(.delivery-marker .marker-icon) {
  background: linear-gradient(135deg, #FBBC04 0%, #F57F17 100%);
  border: 3px solid #FFFFFF;
}

:deep(.marker-pulse) {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 24px;
  height: 24px;
  border-radius: 50%;
  z-index: 1;
  animation: markerPulse 2s ease-out infinite;
}

:deep(.delivery-marker .marker-pulse) {
  background: rgba(251, 188, 4, 0.4);
  border: 2px solid rgba(251, 188, 4, 0.6);
}

:deep(.custom-marker:hover .marker-icon) {
  transform: scale(1.15);
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.4);
}

@keyframes markerBounce {

  0%,
  100% {
    transform: translateY(0);
  }

  50% {
    transform: translateY(-8px);
  }
}

@keyframes markerPulse {
  0% {
    transform: translate(-50%, -50%) scale(1);
    opacity: 1;
  }

  100% {
    transform: translate(-50%, -50%) scale(2);
    opacity: 0;
  }
}

.info-window-content {
  min-width: 200px;
  padding: 8px;
}

.info-window-header {
  display: flex;
  align-items: center;
  margin-bottom: 8px;
  padding-bottom: 8px;
  border-bottom: 1px solid #e0e0e0;
  font-size: 14px;
  color: #1a1a1a;
}

.info-window-body {
  padding-top: 4px;
}

.passcode-modal {
  min-width: 400px;
  border-radius: 12px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);

  .q-card__section {
    padding: 24px;
  }
}

@media (max-width: 1024px) {
  .checkout-content {
    grid-template-columns: 1fr;
  }

  .checkout-sidebar {
    order: -1;
  }
}

@media (max-width: 600px) {
  .checkout-container {
    padding: 16px;
  }

  .section-header {
    padding: 16px;
  }

  .section-title {
    font-size: 16px;
  }

  .map-section .checkout-map {
    height: 350px;
  }

  .order-summary-content {
    padding: 16px;
  }

  .contact-card .q-card-section {
    padding: 16px;
  }

  .complete-order-btn {
    height: 48px;
    font-size: 15px;
  }
}
</style>

<style>
/* Global styles for Google Maps */
.gm-style-iw button.gm-ui-hover-effect {
  display: none !important;
}
</style>
