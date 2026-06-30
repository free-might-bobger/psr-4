<template>
  <div class="checkout-page">

    <!-- Hero Banner -->
    <div class="checkout-hero">
      <div class="hero-bg">
        <div class="hero-orb orb-1"></div>
        <div class="hero-orb orb-2"></div>
        <div class="hero-grid"></div>
      </div>
      <div class="hero-inner">
        <BreadCrumbsWrapper :bread-crumbs="[
          { name: 'Cart', path: '/cart' },
          { name: 'Checkout', path: '' },
        ]" />
        <div class="hero-content">
          <div class="hero-icon-wrap">
            <q-icon name="receipt_long" size="28px" color="white" />
          </div>
          <div>
            <div class="hero-title">Checkout</div>
            <div class="hero-sub">Review your order and confirm delivery details</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Body -->
    <div class="checkout-body">
      <div class="checkout-layout">

        <!-- Left: Map -->
        <div class="checkout-main">
          <div class="panel-card">
            <div class="panel-header">
              <div class="panel-header-icon">
                <q-icon name="location_on" size="20px" color="white" />
              </div>
              <div class="panel-header-text">
                <div class="panel-title">Delivery Location</div>
                <div class="panel-subtitle">Drag the pin to set your exact address</div>
              </div>
              <div class="panel-header-badge">
                <q-icon name="edit" size="14px" class="q-mr-xs" />
                Adjust on Map
              </div>
            </div>

            <div class="map-search-wrap">
              <q-input ref="searchInputRef" v-model="searchLocation" outlined dense
                placeholder="Search for a location..." class="map-search-input">
                <template v-slot:prepend>
                  <q-icon name="search" color="grey-5" />
                </template>
                <template v-slot:append>
                  <q-btn v-if="searchLocation" icon="close" flat round dense size="sm" @click="clearSearch" />
                </template>
              </q-input>
            </div>

            <div class="map-wrap">
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

            <div class="map-footer">
              <q-icon name="info_outline" size="16px" color="indigo-4" class="q-mr-xs" />
              <span>Drag the marker to fine-tune your delivery address</span>
            </div>
          </div>
        </div>

        <!-- Right: Summary + Form -->
        <div class="checkout-sidebar">

          <!-- Order Summary -->
          <div class="panel-card summary-panel">
            <div class="panel-header">
              <div class="panel-header-icon">
                <q-icon name="shopping_bag" size="20px" color="white" />
              </div>
              <div class="panel-header-text">
                <div class="panel-title">Order Summary</div>
                <div class="panel-subtitle">{{ countTotalItems }} {{ countTotalItems === 1 ? 'item' : 'items' }}</div>
              </div>
            </div>

            <div class="summary-body">
              <div class="summary-line">
                <span class="summary-line-label">Subtotal</span>
                <span class="summary-line-value">{{ formatMoney(total) }}</span>
              </div>
              <div class="summary-line">
                <span class="summary-line-label">
                  <q-icon name="local_shipping" size="14px" class="q-mr-xs" />
                  Delivery Charge
                </span>
                <span class="summary-line-value">{{ formatMoney(deliveryCharge) }}</span>
              </div>
              <div class="summary-divider"></div>
              <div class="summary-total-row">
                <span class="summary-total-label">Total</span>
                <span class="summary-total-value">{{ decimalThousandSeparator(total + deliveryCharge) }}</span>
              </div>
            </div>
          </div>

          <!-- Contact Form -->
          <div class="panel-card contact-panel">
            <div class="panel-header">
              <div class="panel-header-icon">
                <q-icon name="contact_phone" size="20px" color="white" />
              </div>
              <div class="panel-header-text">
                <div class="panel-title">Contact Information</div>
                <div class="panel-subtitle">We'll send order updates here</div>
              </div>
            </div>

            <q-form @submit="processCustomerOrder" ref="myForm" class="contact-form">
              <div class="form-field-label">Receiver's Mobile Number</div>
              <q-input v-model="mobile" outlined dense placeholder="9XX XXX XXXX" class="form-input q-mb-lg" :rules="[
                async (val) => isValidMobileNumber(val) || 'Please enter a valid mobile number.',
              ]" hide-bottom-space prefix="+63">
                <template v-slot:prepend>
                  <q-icon name="phone" color="indigo-4" />
                </template>
              </q-input>

              <div class="form-field-label">Order Note <span class="form-field-optional">(Optional)</span></div>
              <q-input v-model="note" type="textarea" outlined dense
                placeholder="Add any special instructions for your order..." class="form-input q-mb-lg"
                input-style="min-height: 5em; height: 5em;">
                <template v-slot:prepend>
                  <q-icon name="notes" color="indigo-4" />
                </template>
              </q-input>

              <button type="submit" class="complete-btn">
                <q-icon name="check_circle" size="20px" class="q-mr-sm" />
                Complete Order
              </button>
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
// ── Page ──────────────────────────────────────────────────────────────────
.checkout-page {
  background: #f4f5f7;
  min-height: 100vh;
}

// ── Hero ──────────────────────────────────────────────────────────────────
.checkout-hero {
  position: relative;
  background: linear-gradient(145deg, #1e1b4b 0%, #312e81 55%, #4c1d95 100%);
  overflow: hidden;
}

.hero-bg {
  position: absolute;
  inset: 0;
  pointer-events: none;
}

.hero-orb {
  position: absolute;
  border-radius: 50%;
  filter: blur(70px);
  animation: heroFloat 12s ease-in-out infinite;

  &.orb-1 {
    width: 420px;
    height: 420px;
    background: rgba(139, 92, 246, 0.28);
    top: -180px;
    right: -60px;
  }

  &.orb-2 {
    width: 220px;
    height: 220px;
    background: rgba(99, 102, 241, 0.2);
    bottom: -60px;
    left: -40px;
    animation-delay: 6s;
  }
}

@keyframes heroFloat {

  0%,
  100% {
    transform: translate(0, 0);
  }

  50% {
    transform: translate(14px, -18px);
  }
}

.hero-grid {
  position: absolute;
  inset: 0;
  background-image:
    linear-gradient(rgba(255, 255, 255, 0.04) 1px, transparent 1px),
    linear-gradient(90deg, rgba(255, 255, 255, 0.04) 1px, transparent 1px);
  background-size: 48px 48px;
}

.hero-inner {
  position: relative;
  z-index: 1;
  padding: 20px 32px 28px;

  :deep(.q-breadcrumbs) {
    color: rgba(255, 255, 255, 0.45);
  }

  :deep(.q-breadcrumbs__el) {
    color: rgba(255, 255, 255, 0.45);
  }

  :deep(.q-breadcrumbs__separator) {
    color: rgba(255, 255, 255, 0.25);
  }

  :deep(a) {
    color: rgba(255, 255, 255, 0.65) !important;
  }
}

.hero-content {
  display: flex;
  align-items: center;
  gap: 16px;
  margin-top: 14px;
}

.hero-icon-wrap {
  width: 52px;
  height: 52px;
  border-radius: 14px;
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.hero-title {
  font-size: 26px;
  font-weight: 900;
  color: white;
  letter-spacing: -0.5px;
  line-height: 1.2;
}

.hero-sub {
  font-size: 13px;
  color: rgba(255, 255, 255, 0.55);
  font-weight: 500;
  margin-top: 3px;
}

// ── Body ──────────────────────────────────────────────────────────────────
.checkout-body {
  max-width: 1400px;
  margin: 0 auto;
  padding: 32px;
}

.checkout-layout {
  display: grid;
  grid-template-columns: 1fr 390px;
  gap: 28px;
  align-items: start;
}

.checkout-main {
  min-width: 0;
}

.checkout-sidebar {
  min-width: 0;
}

// ── Panel Card ────────────────────────────────────────────────────────────
.panel-card {
  background: white;
  border-radius: 20px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.07);
  overflow: hidden;
  margin-bottom: 24px;

  &:last-child {
    margin-bottom: 0;
  }
}

.panel-header {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 18px 24px;
  background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
}

.panel-header-icon {
  width: 40px;
  height: 40px;
  border-radius: 11px;
  background: rgba(255, 255, 255, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.panel-header-text {
  flex: 1;
  min-width: 0;
}

.panel-title {
  font-size: 15px;
  font-weight: 800;
  color: white;
  letter-spacing: 0.2px;
}

.panel-subtitle {
  font-size: 12px;
  color: rgba(255, 255, 255, 0.6);
  margin-top: 2px;
}

.panel-header-badge {
  display: flex;
  align-items: center;
  padding: 5px 12px;
  border-radius: 20px;
  background: rgba(255, 255, 255, 0.18);
  color: white;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.3px;
  white-space: nowrap;
  border: 1px solid rgba(255, 255, 255, 0.25);
}

// ── Map ───────────────────────────────────────────────────────────────────
.map-search-wrap {
  padding: 16px 20px 0;
}

.map-search-input {
  :deep(.q-field__control) {
    border-radius: 10px;
    background: #f8f9fb;
  }
}

.map-wrap {
  padding: 16px 0 0;
}

.checkout-map {
  height: 480px;
  width: 100%;
  display: block;
}

.map-footer {
  display: flex;
  align-items: center;
  padding: 12px 20px;
  background: #f9fafb;
  border-top: 1px solid #f0f1f3;
  font-size: 12px;
  color: #6b7280;
  font-weight: 500;
}

// ── Order Summary ─────────────────────────────────────────────────────────
.summary-panel {
  margin-bottom: 24px;
}

.summary-body {
  padding: 20px 24px;
}

.summary-line {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 0;
  border-bottom: 1px dashed #f3f4f6;

  &:last-of-type {
    border-bottom: none;
  }
}

.summary-line-label {
  font-size: 14px;
  color: #6b7280;
  display: flex;
  align-items: center;
}

.summary-line-value {
  font-size: 14px;
  font-weight: 700;
  color: #111827;
}

.summary-divider {
  height: 1px;
  background: linear-gradient(90deg, transparent, #e5e7eb, transparent);
  margin: 10px 0 14px;
}

.summary-total-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.summary-total-label {
  font-size: 16px;
  font-weight: 800;
  color: #111827;
}

.summary-total-value {
  font-size: 26px;
  font-weight: 900;
  background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  line-height: 1;
}

// ── Contact Form ──────────────────────────────────────────────────────────
.contact-panel {}

.contact-form {
  padding: 20px 24px 24px;
}

.form-field-label {
  font-size: 11px;
  font-weight: 800;
  color: #9ca3af;
  text-transform: uppercase;
  letter-spacing: 1.5px;
  margin-bottom: 8px;
}

.form-field-optional {
  font-weight: 500;
  color: #c4c9d4;
  text-transform: none;
  letter-spacing: 0;
  font-size: 11px;
}

.form-input {
  :deep(.q-field__control) {
    border-radius: 11px;
    background: #f9fafb;
  }

  :deep(.q-field--focused .q-field__control) {
    background: white;
  }
}

.complete-btn {
  width: 100%;
  height: 52px;
  border: none;
  border-radius: 14px;
  background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
  color: white;
  font-size: 15px;
  font-weight: 800;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 6px 20px rgba(99, 102, 241, 0.35);
  transition: all 0.25s ease;
  letter-spacing: 0.3px;

  &:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 28px rgba(99, 102, 241, 0.45);
  }

  &:active {
    transform: translateY(0);
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

@media (max-width: 1100px) {
  .checkout-layout {
    grid-template-columns: 1fr;
    gap: 0;
  }

  .checkout-sidebar {
    order: -1;
    margin-bottom: 24px;
  }
}

@media (max-width: 768px) {
  .hero-inner {
    padding: 16px 20px 22px;
  }

  .hero-title {
    font-size: 22px;
  }

  .checkout-body {
    padding: 20px;
  }

  .checkout-map {
    height: 360px;
  }
}

@media (max-width: 480px) {
  .checkout-body {
    padding: 12px;
  }

  .checkout-map {
    height: 300px;
  }

  .summary-total-value {
    font-size: 22px;
  }

  .complete-btn {
    height: 48px;
    font-size: 14px;
  }
}
</style>

<style>
/* Global styles for Google Maps */
.gm-style-iw button.gm-ui-hover-effect {
  display: none !important;
}
</style>
