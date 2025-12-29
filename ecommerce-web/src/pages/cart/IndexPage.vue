<template>
  <div class="cart-container" v-if="countTotalItems > 0">
    <div class="row q-gutter-md q-mb-md">
      <div class="col">
        <BreadCrumbsWrapper :bread-crumbs="[
          {
            name: 'Cart',
            path: '',
          },
        ]" />
      </div>
    </div>

    <div class="cart-items-section">
      <div class="text-h5 text-weight-bold q-mb-md">
        Shopping Cart ({{ countTotalItems }} {{ countTotalItems === 1 ? 'item' : 'items' }})
      </div>

      <div v-for="(item, i) in cart" :key="i" class="cart-item-card q-mb-md">
        <q-card flat bordered class="cart-item">
          <q-card-section>
            <div class="row q-col-gutter-md">
              <!-- Product Image -->
              <div class="col-12 col-sm-3 col-md-2">
                <router-link :to="'/public_stores/' +
                  item.store.optimus_id +
                  '/item/' +
                  item.optimus_id
                  " class="cart-item-image-link">
                  <q-img :src="item.primary_img.path_url" class="cart-item-image" :ratio="1" fit="cover" />
                </router-link>
              </div>

              <!-- Product Details -->
              <div class="col-12 col-sm-9 col-md-10">
                <div class="row q-col-gutter-md full-height">
                  <div class="col-12 col-md-8">
                    <router-link :to="'/public_stores/' +
                      item.store.optimus_id +
                      '/item/' +
                      item.optimus_id
                      " class="cart-item-name-link">
                      <div class="text-h6 text-weight-medium q-mb-sm cart-item-name">
                        {{ item.name }}
                      </div>
                    </router-link>

                    <!-- Order Details -->
                    <div class="cart-item-details q-mt-sm">
                      <div v-for="(orderDetail, index) in getOrderDetail(
                        item.item_price as any,
                        item.variations
                      )" :key="index" class="cart-item-variation q-mb-xs">
                        <q-icon name="check_circle" size="xs" color="positive" class="q-mr-xs" />
                        <span class="text-body2">
                          <strong>{{ orderDetail.count }}</strong> {{ orderDetail.unit_name }} ×
                          <span class="text-primary">{{ formatMoney(orderDetail.price) }}</span> =
                          <strong class="text-primary">{{ formatMoney(computePrice(orderDetail.count,
                            orderDetail.price)) }}</strong>
                        </span>
                      </div>
                    </div>
                  </div>

                  <!-- Actions -->
                  <div class="col-12 col-md-4 flex flex-center flex-column justify-between">
                    <q-btn color="negative" outline icon="delete_outline" label="Remove"
                      @click="userCart.removeItem(item.optimus_id)" class="q-mt-sm full-width" unelevated />
                  </div>
                </div>
              </div>
            </div>
          </q-card-section>
        </q-card>
      </div>
    </div>

    <!-- Options Section -->
    <div class="cart-options-section q-mt-lg">
      <div class="row q-col-gutter-md">
        <!-- Delivery Method -->
        <div class="col-12 col-md-6">
          <q-card flat bordered class="options-card">
            <q-card-section>
              <div class="text-subtitle1 text-weight-bold q-mb-md">
                <q-icon name="local_shipping" class="q-mr-sm" />
                Delivery Method
              </div>
              <div class="row q-col-gutter-sm">
                <div class="col-12" v-for="receiveMethod in receiveMethods" :key="receiveMethod.id">
                  <q-radio v-model="selectedReceiveMethod" :val="receiveMethod.value" :label="receiveMethod.name"
                    color="primary" class="delivery-option" />
                </div>
              </div>
            </q-card-section>
          </q-card>
        </div>

        <!-- Payment Method -->
        <div class="col-12 col-md-6">
          <q-card flat bordered class="options-card">
            <q-card-section>
              <div class="text-subtitle1 text-weight-bold q-mb-md">
                <q-icon name="payment" class="q-mr-sm" />
                Payment Method
              </div>
              <div class="row q-col-gutter-sm">
                <div class="col-12" v-for="peymentMethod in paymentMethods" :key="peymentMethod.id">
                  <q-radio v-model="selectedPaymenthMethod" :val="peymentMethod.value" :label="peymentMethod.name"
                    color="primary" class="payment-option" />
                </div>
              </div>
            </q-card-section>
          </q-card>
        </div>
      </div>
    </div>

    <!-- Order Summary -->
    <div class="cart-summary-section q-mt-lg">
      <q-card flat bordered class="summary-card">
        <q-card-section>
          <div class="text-h6 text-weight-bold q-mb-md">
            <q-icon name="receipt" class="q-mr-sm" />
            Order Summary
          </div>

          <q-separator class="q-mb-md" />

          <div class="summary-row q-mb-sm">
            <span class="summary-label">Subtotal:</span>
            <span class="summary-value">{{ formatMoney(total) }}</span>
          </div>

          <div v-if="selectedReceiveMethod === DELIVERY_TYPE.DELIVER" class="summary-row q-mb-sm">
            <span class="summary-label">Delivery Charge:</span>
            <span class="summary-value">{{ formatMoney(deliveryCharge) }}</span>
          </div>

          <q-separator class="q-my-md" />

          <div class="summary-row-total">
            <span class="summary-label-total">Grand Total:</span>
            <span class="summary-value-total">{{
              decimalThousandSeparator(total + deliveryCharge)
            }}</span>
          </div>
        </q-card-section>
      </q-card>
    </div>

    <!-- Checkout Button -->
    <q-btn color="primary" class="checkout-btn q-mt-lg" to="/cart/checkout" label="Proceed to Checkout"
      icon="shopping_cart" size="lg" unelevated :disable="disabledCheckout" />
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
interface ReceiveMethod {
  id: number;
  value: number;
  name: string;
}

interface PaymentMethod {
  id: number;
  value: number;
  name: string;
}

const receiveMethods = ref<ReceiveMethod[]>([]);

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

  receiveMethods.value = (result as any).data?.data || [];
};

const paymentMethods = ref<PaymentMethod[]>([]);
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

  paymentMethods.value = (result as any).data?.data || [];
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
  updateDeliveryCharge();
});

const updateDeliveryCharge = async () => {
  if (selectedReceiveMethod.value === DELIVERY_TYPE.DELIVER) {
    const result = await get(
      {
        entity: 'delivery_charges',
        query: {
          type: 'collection',
          storeIds: storeIds.value.filter((id): id is number => id !== undefined),
          latitude: lat.value,
          longitude: lng.value,
        },
        message: '',
      },
      true
    );
    const delivery = (result as any).data?.data?.find((v: any) => v);
    if (delivery) {
      deliveryCharge.value = delivery.delivery_amount;
      disabledCheckout.value = false;
    }
    return;
  }
  deliveryCharge.value = 0;
  disabledCheckout.value = false;
};
</script>

<style scoped lang="scss">
.cart-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 16px;
}

.cart-items-section {
  margin-bottom: 32px;
}

.cart-item-card {
  transition: transform 0.2s ease, box-shadow 0.2s ease;

  &:hover {
    transform: translateY(-2px);
  }
}

.cart-item {
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  transition: box-shadow 0.3s ease;

  &:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
  }
}

.cart-item-image-link {
  display: block;
  text-decoration: none;
  border-radius: 8px;
  overflow: hidden;
}

.cart-item-image {
  border-radius: 8px;
  background: #f5f5f5;
}

.cart-item-name-link {
  text-decoration: none;
  color: inherit;
  transition: color 0.2s ease;

  &:hover {
    color: var(--q-primary);
  }
}

.cart-item-name {
  color: #1a1a1a;
  line-height: 1.4;
}

.cart-item-details {
  padding: 8px 0;
}

.cart-item-variation {
  display: flex;
  align-items: center;
  padding: 4px 0;
  color: #666;
}

.cart-options-section {
  margin-top: 32px;
}

.options-card {
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  height: 100%;
}

.delivery-option,
.payment-option {
  padding: 8px;
  border-radius: 8px;
  transition: background-color 0.2s ease;

  &:hover {
    background-color: #f5f5f5;
  }
}

.cart-summary-section {
  margin-top: 32px;
}

.summary-card {
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
}

.summary-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 8px 0;
}

.summary-label {
  font-size: 16px;
  color: #666;
}

.summary-value {
  font-size: 16px;
  font-weight: 600;
  color: #1a1a1a;
}

.summary-row-total {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px 0 0;
}

.summary-label-total {
  font-size: 20px;
  font-weight: bold;
  color: #1a1a1a;
}

.summary-value-total {
  font-size: 24px;
  font-weight: bold;
  color: var(--q-primary);
}

.checkout-btn {
  width: 100%;
  height: 56px;
  font-size: 18px;
  font-weight: 600;
  border-radius: 8px;
  letter-spacing: 0.5px;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);

  &:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
  }

  &:disabled {
    opacity: 0.6;
    cursor: not-allowed;
  }
}

@media (max-width: 600px) {
  .cart-container {
    padding: 8px;
  }

  .cart-item-name {
    font-size: 1.1rem;
  }

  .summary-label-total {
    font-size: 18px;
  }

  .summary-value-total {
    font-size: 20px;
  }

  .checkout-btn {
    height: 48px;
    font-size: 16px;
  }
}
</style>
