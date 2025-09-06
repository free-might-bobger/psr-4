<template>
  <div class="q-ma-sm">
    <div class="row q-gutter-md">
      <div class="col-12">
        <BreadCrumbsWrapper
          :bread-crumbs="[
            {
              name: 'Cart',
              path: '/cart',
            },
            {
              name: 'Checkout',
              path: '',
            },
          ]"
        />
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <GoogleMap
          class="q-mt-sm"
          :api-key="GOOGLE_MAP_API_KEY"
          :map-id="GOOGLE_MAP_ID"
          style="height: 350px"
          :center="{ lat: lat, lng: lng }"
          :zoom="15"
          :draggable="true"
          :clickable-icons="false"
        >
          <AdvancedMarker
            :options="{
              position: { lat: lat, lng: lng },
              gmpDraggable: false,
              title: 'MARKER',
            }"
            @drag="markerDrag"
            :pin-options="{ background: '#FBBC04' }"
          >
            <InfoWindow
              v-model="showInfoWindow"
              :options="{
                position: { lat: lat, lng: lng },
                headerContent: 'My Location',
              }"
            >
            </InfoWindow>
          </AdvancedMarker>
        </GoogleMap>
      </div>
    </div>
    <br />
    <div class="row" v-if="hasVerificationCode === false && !profile.token">
      <div class="col-12">
        <q-form @submit="getVerificationCode" ref="myForm">
          <q-input
            v-model="mobile"
            class="full-width"
            outlined
            dense
            label="Enter mobile number"
            :rules="[
              async (val) =>
                isValidMobileNumber(val) ||
                'Please enter a valid mobile number.',
            ]"
            hide-bottom-space
          />
          <div class="flex justify-between">
            <q-btn
              class="q-mt-sm full-width"
              color="primary"
              @click="getVerificationCode"
              unelevated
              >Get Passcode</q-btn
            >
          </div>
        </q-form>
      </div>
    </div>
    <div class="row" v-if="profile.token && countTotalItems > 0">
      <div class="col-12">
        <q-btn
          class="full-width"
          label="Complete My Order"
          color="primary"
          @click="processCustomerOrder"
          unelevated
        />
      </div>
    </div>
    <q-dialog v-model="showPassCodeModal" persistent>
      <q-form @submit="verifyPassCode" ref="verifyForm"></q-form>
      <q-card>
        <q-card-section>
          <q-input
            v-model="passCode"
            label="Enter Passcode"
            outlined
            class="full-width"
            :rules="[
              (val) => (val && val.length > 0) || 'Passcode is required.',
            ]"
          />
          <div>
            <q-btn flat label="Cancel" color="primary" v-close-popup />
            <q-btn
              flat
              label="Complete My Order"
              color="primary"
              @click="verifyPassCode"
            />
          </div>
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
  GroupStoreItemsInterface,
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
            mobile: mobile.value,
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
  Object.entries(groupByStore.value as Record<string, GroupStoreItemInterface[]>).forEach(
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

<style>
/* Scoped CSS for Vue component */
.gm-style-iw button.gm-ui-hover-effect {
  display: none !important;
}
</style>
