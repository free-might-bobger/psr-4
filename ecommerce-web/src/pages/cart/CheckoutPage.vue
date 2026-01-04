<template>
  <div class="checkout-container">
    <div class="row q-gutter-md q-mb-md">
      <div class="col-12">
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
    </div>

    <div class="text-h5 text-weight-bold q-mb-md">
      <q-icon name="location_on" class="q-mr-sm" />
      Delivery Location
    </div>

    <!-- Map Section -->
    <q-card flat bordered class="map-card q-mb-lg">
      <q-card-section class="q-pa-none">
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
                <div class="info-window-body">
                  <p class="text-caption text-grey-7 q-ma-none">
                    Drag the marker to set your delivery location
                  </p>
                </div>
              </div>
            </InfoWindow>
          </AdvancedMarker>
        </GoogleMap>
      </q-card-section>
      <q-card-section class="q-pt-sm">
        <div class="text-caption text-grey-7">
          <q-icon name="info" size="xs" class="q-mr-xs" />
          Drag the marker to set your delivery location
      </div>
      </q-card-section>
    </q-card>

    <!-- Authentication Section -->
    <div v-if="hasVerificationCode === false && !profile.token" class="auth-section q-mb-lg">
      <q-card flat bordered class="auth-card">
        <q-card-section>
          <div class="text-h6 text-weight-bold q-mb-md">
            <q-icon name="phone_android" class="q-mr-sm" />
            Verify Your Mobile Number
    </div>
        <q-form @submit="getVerificationCode" ref="myForm">
            <q-input v-model="mobile" class="full-width q-mb-md" outlined label="Enter mobile number"
              placeholder="9XX XXX XXXX" :rules="[
              async (val) =>
                isValidMobileNumber(val) ||
                'Please enter a valid mobile number.',
              ]" hide-bottom-space prefix="+63">
              <template v-slot:prepend>
                <q-icon name="phone" />
              </template>
            </q-input>
            <q-btn class="full-width" color="primary" unelevated size="lg" icon="verified_user"
              label="Get Verification Code" type="submit" />
        </q-form>
        </q-card-section>
      </q-card>
    </div>

    <!-- Complete Order Button -->
    <div v-if="profile.token && countTotalItems > 0" class="complete-order-section">
      <q-btn class="complete-order-btn full-width" label="Complete My Order" color="primary"
        @click="processCustomerOrder" unelevated size="lg" icon="check_circle" />
    </div>
    <!-- Passcode Verification Modal -->
    <q-dialog v-model="showPassCodeModal" persistent>
      <q-card class="passcode-modal">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-h6 text-weight-bold">
            <q-icon name="lock" class="q-mr-sm" />
            Enter Verification Code
          </div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>

        <q-card-section>
          <q-form @submit="verifyPassCode" ref="verifyForm">
            <div class="text-body2 text-grey-7 q-mb-md">
              We've sent a verification code to your mobile number. Please enter it below.
            </div>
            <q-input v-model="passCode" label="Enter Verification Code" outlined class="full-width q-mb-md" :rules="[
              (val) => (val && val.length > 0) || 'Verification code is required.',
            ]" mask="######" placeholder="000000">
              <template v-slot:prepend>
                <q-icon name="vpn_key" />
              </template>
            </q-input>
            <div class="row q-gutter-sm">
              <q-btn flat label="Cancel" color="grey-7" v-close-popup class="col" />
              <q-btn label="Verify & Complete Order" color="primary" unelevated class="col" icon="check_circle"
                type="submit" />
          </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>
  </div>
</template>

<script lang="ts" setup>
import { GOOGLE_MAP_API_KEY, GOOGLE_MAP_ID } from 'src/boot/constant';
import { GoogleMap, AdvancedMarker, InfoWindow } from 'vue3-google-map';
import BreadCrumbsWrapper from 'src/components/BreadCrumbsWrapper.vue';
import { ref, watch, nextTick, onMounted } from 'vue';
import { useCommonStore } from 'src/stores/common';
import { storeToRefs } from 'pinia';
import { create, isMobileExist, login } from 'src/boot/axios-call';
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
const { profile } = storeToRefs(useUserStore());
const showInfoWindow = ref(true);
const mapRef = ref<any>(null);
const currentZoom = ref(15);

const hasVerificationCode = ref(false);

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
});

const myForm = ref<QForm | null>(null);
const getVerificationCode = async () => {
  myForm.value?.validate().then(async (success: boolean) => {
    if (success) {
      await create(
        {
          entity: 'create-new-activation-code',
          data: {
            mobile: 0 + mobile.value,
          },
        },
        true,
        'Please verify your passcode.'
      );
      showPassCodeModal.value = true;
    }
  });
};

const showPassCodeModal = ref(false);
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

const verifyPassCode = async () => {
  myForm.value?.validate().then(async (success: boolean) => {
    if (success) {
      const result = await create(
        {
          entity: 'verify-passcode',
          data: {
            passCode: passCode.value,
            mobile: mobile.value,
          },
        },
        true,
        'Verifying your passcode...',
        'Data successfully verified...'
      );

      if (!result) {
        $q.notify({
          message: 'Invalid verification code.',
          color: 'negative',
        });
        return;
      }

      await nextTick();
      myForm.value?.resetValidation();
      showPassCodeModal.value = false;

      const loginResult = await login({
        mobile: mobile.value,
        password: passCode.value,
      });

      passCode.value = '';
      mobile.value = '';

      if (loginResult) {
        processCustomerOrder();
      }
    }
  });
};

const passCode = ref('');

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

    customerOrders.push({
      store_id: key,
      items: itemOrders,
    });
  }
);

  const result = await create(
    {
      entity: 'transactions',
      data: {
        total: total.value,
        items: customerOrders,
        deliveryCharge: deliveryCharge.value,
        selectedReceiveMethod: selectedReceiveMethod.value,
        selectedPaymenthMethod: selectedPaymenthMethod.value,
        lat: lat.value,
        lng: lng.value,
      },
    },
    false
  );
  if (result) {
    userCart.emptyCart();
    router.push({
      path: '/dashboard/customer-transactions',
    });
  }
};
</script>

<style scoped lang="scss">
.checkout-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 16px;
}

.map-card {
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  overflow: hidden;
}

.checkout-map {
  height: 400px;
  width: 100%;
  border-radius: 12px 12px 0 0;
}

// Custom animated marker styles
:deep(.custom-marker) {
  position: relative;
  width: 48px;
  height: 48px;
  cursor: pointer;
  animation: markerBounce 2s infinite;
}

:deep(.marker-icon) {
  position: relative;
  width: 48px;
  height: 48px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
  transition: transform 0.3s ease;

  svg {
    width: 24px;
    height: 24px;
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
  width: 48px;
  height: 48px;
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

.auth-section {
  margin-top: 32px;
}

.auth-card {
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.complete-order-section {
  margin-top: 32px;
}

.complete-order-btn {
  height: 56px;
  font-size: 18px;
  font-weight: 600;
  border-radius: 8px;
  letter-spacing: 0.5px;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);

  &:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
  }
}

.passcode-modal {
  min-width: 400px;
  border-radius: 12px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);

  .q-card__section {
    padding: 24px;
  }
}

@media (max-width: 600px) {
  .checkout-container {
    padding: 8px;
  }

  .checkout-map {
    height: 300px;
  }

  .passcode-modal {
    min-width: 90%;
    margin: 16px;
  }

  .complete-order-btn {
    height: 48px;
    font-size: 16px;
  }
}
</style>

<style>
/* Global styles for Google Maps */
.gm-style-iw button.gm-ui-hover-effect {
  display: none !important;
}
</style>
