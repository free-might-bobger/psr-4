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
        <GoogleMap :api-key="GOOGLE_MAP_API_KEY" :map-id="GOOGLE_MAP_ID" class="checkout-map"
          :center="{ lat: lat, lng: lng }" :zoom="15" :draggable="true" :clickable-icons="false">
          <AdvancedMarker :options="{
            position: { lat: lat, lng: lng },
            gmpDraggable: false,
            title: 'Delivery Location',
          }" @drag="markerDrag" :pin-options="{ background: '#FBBC04' }">
            <InfoWindow v-model="showInfoWindow" :options="{
              position: { lat: lat, lng: lng },
              headerContent: 'Delivery Location',
            }">
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
            <q-btn class="full-width" color="primary" unelevated size="lg"
              icon="verified_user" label="Get Verification Code" type="submit" />
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
              <q-btn label="Verify & Complete Order" color="primary" unelevated class="col"
                icon="check_circle" type="submit" />
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
import { ref, watch, nextTick } from 'vue';
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
  countTotalItems,
  deliveryCharge,
} = storeToRefs(userCart);

const router = useRouter();
const useCommon = useCommonStore();
const { lat, lng, mobile } = storeToRefs(useCommon);
const { profile } = storeToRefs(useUserStore());
const showInfoWindow = ref(true);

const hasVerificationCode = ref(false);
const markerDrag = (e: { latLng: google.maps.LatLng }) => {
  lat.value = e.latLng.lat();
  lng.value = e.latLng.lng();
};

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
            mobile: 0 + mobile.value,
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
        mobile: 0 + mobile.value,
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
        selectedReceiveMethod: selectedPaymenthMethod.value,
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
