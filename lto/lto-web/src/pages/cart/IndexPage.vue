<template>
  <div class="q-ma-sm" v-if="countTotalItems > 0">
    <div class="row q-gutter-md">
      <div class="col">
        <BreadCrumbsWrapper
          :bread-crumbs="[
            {
              name: 'Cart',
              path: '',
            },
          ]"
        />
      </div>
    </div>
    <div v-for="(item, i) in cart" :key="i">
      <q-card flat bordered class="q-py-sm q-mt-sm">
        <q-card-section horizontal>
          <router-link
            :to="
              '/public_stores/' +
              item.store.optimus_id +
              '/item/' +
              item.optimus_id
            "
            class="col-3"
          >
            <q-img :src="item.primary_img.path_url" class="q-mt-lg" />
          </router-link>

          <div class="q-ma-md">
            <router-link
              :to="
                '/public_stores/' +
                item.store.optimus_id +
                '/item/' +
                item.optimus_id
              "
            >
              <strong>{{ item.name }}</strong> <br />
            </router-link>

            <div
              class="q-mt-sm"
              v-for="(orderDetail, index) in getOrderDetail(
                item.item_price,
                item.variations
              )"
              :key="index"
            >
              {{ orderDetail.count }} {{ orderDetail.unit_name }} x
              {{ orderDetail.price }} =
              {{
                formatMoney(computePrice(orderDetail.count, orderDetail.price))
              }}
            </div>
            <br />
            <q-btn
              color="negative"
              outline
              @click="userCart.removeItem(item.optimus_id)"
              >Remove</q-btn
            >
          </div>
        </q-card-section>
      </q-card>
    </div>

    <q-card flat bordered class="q-pa-sm q-mt-sm">
      <q-card-section>
        <div class="row text-center">
          <div
            class="col"
            v-for="receiveMethod in receiveMethods"
            :key="receiveMethod.id"
          >
            <q-radio
              dense
              v-model="selectedReceiveMethod"
              :val="receiveMethod.value"
              :label="receiveMethod.name"
            />
          </div>
        </div>
      </q-card-section>
    </q-card>

    <q-card flat bordered class="q-pa-sm q-mt-sm">
      <q-card-section>
        <div class="row text-center">
          <div
            class="col"
            v-for="peymentMethod in paymentMethods"
            :key="peymentMethod.id"
          >
            <q-radio
              dense
              v-model="selectedPaymenthMethod"
              :val="peymentMethod.value"
              :label="peymentMethod.name"
            />
          </div>
        </div>
      </q-card-section>
    </q-card>

    <q-card flat bordered class="q-pa-sm q-mt-sm">
      <q-card-section horizontal>
        <span class="cart-total">Subtotal :</span>
        <q-space />
        <span class="cart-amount">{{ total.toFixed(2) }}</span>
      </q-card-section>
      <q-card-section
        horizontal
        v-if="selectedReceiveMethod === DELIVERY_TYPE.DELIVER"
      >
        <span class="cart-total">Delivery Charge :</span>
        <q-space />
        <span class="cart-amount">{{ deliveryCharge.toFixed(2) }}</span>
      </q-card-section>

      <q-card-section horizontal>
        <span class="cart-total">Grandtotal :</span>
        <q-space />
        <span class="cart-amount">{{
          decimalThousandSeparator(total + deliveryCharge)
        }}</span>
      </q-card-section>
    </q-card>

    <q-btn
      color="primary"
      class="full-width q-mt-sm"
      to="/cart/checkout"
      label="Checkout"
      unelevated
      :disable="disabledCheckout"
    ></q-btn>
  </div>
</template>

<script lang="ts" setup>
import BreadCrumbsWrapper from 'src/components/BreadCrumbsWrapper.vue';
import { useUserCartStore } from 'src/stores/userCart';
import { formatMoney, computePrice, getOrderDetail } from 'boot/utilities';
import { storeToRefs } from 'pinia';
import { onMounted, ref, watch } from 'vue';
import { get } from 'src/boot/axios-call';
import { DELIVERY_TYPE } from 'src/boot/constant';
import { decimalThousandSeparator } from 'boot/utilities';
import { useCommonStore } from 'src/stores/common';

const userCart = useUserCartStore();
const {
  cart,
  total,
  selectedReceiveMethod,
  storeIds,
  selectedPaymenthMethod,
  countTotalItems,
  deliveryCharge
} = storeToRefs(userCart);
const useCommon = useCommonStore();
const { lat, lng } = storeToRefs(useCommon);
const receiveMethods = ref([]);

const getReceiveMethods = async () => {
  const result = await get(
    {
      entity: 'receive_methods',
      query: {
        type: 'collection',
      },
      message: '',
    },
    false
  );

  receiveMethods.value = result.data.data;
};

const paymentMethods = ref([]);
const getPaymentMethods = async () => {
  const result = await get(
    {
      entity: 'payment_methods',
      query: {
        type: 'collection',
      },
      message: '',
    },
    false
  );

  paymentMethods.value = result.data.data;
};

const disabledCheckout = ref(true);
onMounted(() => {
  if (countTotalItems.value > 0) {
    updateDeliveryCharge();
    getReceiveMethods();
    getPaymentMethods();
  }
});


watch(selectedReceiveMethod, async (currentVal) => {
  console.log(currentVal);
  updateDeliveryCharge();
});

const updateDeliveryCharge = async () => {
  if (selectedReceiveMethod.value === DELIVERY_TYPE.DELIVER) {
    const result = await get(
      {
        entity: 'delivery_charges',
        query: {
          type: 'collection',
          storeIds: storeIds.value,
          latitude: lat.value,
          longitude: lng.value,
        },
        message: '',
      },
      true
    );
    const delivery = result.data.data.find((v: object) => v);
    deliveryCharge.value = delivery.delivery_amount;
    disabledCheckout.value = false;
    return;
  }
  deliveryCharge.value = 0;
};
</script>

<style>
.cart-total {
  display: inline-block;
  font-size: 20px;
  font-weight: bold;
}

.cart-amount {
  display: inline-block;
  font-size: 20px;
  text-align: right;
}
</style>
