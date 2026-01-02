<template>
  <div class="cart-container" v-if="countTotalItems > 0">
    <!-- Header -->
    <div class="cart-header q-mb-lg">
      <BreadCrumbsWrapper :bread-crumbs="[
        {
          name: 'Cart',
          path: '',
        },
      ]" />
      <div class="cart-title-section q-mt-md">
        <div class="text-h4 text-weight-bold">
          <q-icon name="shopping_cart" class="q-mr-sm" />
          Shopping Cart
        </div>
        <div class="text-body2 text-grey-7 q-mt-xs">
          {{ countTotalItems }} {{ countTotalItems === 1 ? 'item' : 'items' }} in your cart
        </div>
      </div>
    </div>

    <div class="cart-layout">
      <!-- Left Column: Cart Items -->
      <div class="cart-items-column">
        <!-- Cart Items -->
        <div class="cart-items-section">
          <div v-for="(item, i) in cart" :key="i" class="cart-item-card q-mb-md">
            <q-card flat bordered class="cart-item">
              <q-card-section>
                <div class="row q-col-gutter-md">
                  <!-- Product Image -->
                  <div class="col-12 col-sm-4 col-md-3">
                    <router-link :to="`/public_stores/${item.store.optimus_id}/item/${item.optimus_id}`"
                      class="cart-item-image-link">
                      <q-img :src="item.primary_img.path_url" class="cart-item-image" :ratio="1" fit="cover">
                        <template v-slot:error>
                          <div class="absolute-full flex flex-center bg-grey-3">
                            <q-icon name="image_not_supported" size="32px" color="grey-6" />
                          </div>
                        </template>
                      </q-img>
                    </router-link>
                  </div>

                  <!-- Product Details -->
                  <div class="col-12 col-sm-8 col-md-9">
                    <div class="cart-item-content">
                      <!-- Product Name and Remove -->
                      <div class="flex justify-between items-start q-mb-sm">
                        <router-link :to="`/public_stores/${item.store.optimus_id}/item/${item.optimus_id}`"
                          class="cart-item-name-link">
                          <div class="text-h6 text-weight-medium cart-item-name">
                            {{ item.name }}
                          </div>
                        </router-link>
                        <q-btn flat round dense icon="close" color="grey-7" size="sm"
                          @click="userCart.removeItem(item.optimus_id)" class="remove-btn">
                          <q-tooltip>Remove item</q-tooltip>
                        </q-btn>
                      </div>

                      <!-- Order Details -->
                      <div class="cart-item-details">
                        <div v-for="(orderDetail, index) in getOrderDetail(
                          item.item_price as any,
                          item.variations
                        )" :key="index" class="cart-item-variation">
                          <div class="variation-info">
                            <q-icon name="check_circle" size="xs" color="positive" class="q-mr-xs" />
                            <span class="text-body2">
                              <strong>{{ orderDetail.count }}</strong> {{ orderDetail.unit_name }} ×
                              <span class="text-primary">{{ formatMoney(orderDetail.price) }}</span>
                            </span>
                          </div>
                          <div class="variation-total text-body1 text-weight-bold text-primary">
                            {{ formatMoney(computePrice(orderDetail.count, orderDetail.price)) }}
                          </div>
                        </div>
                      </div>

                      <!-- Item Subtotal -->
                      <div class="cart-item-subtotal q-mt-sm">
                        <q-separator class="q-mb-sm" />
                        <div class="flex justify-between items-center">
                          <span class="text-body2 text-grey-7">Item Subtotal:</span>
                          <span class="text-h6 text-weight-bold text-primary">
                            {{ formatMoney(getItemTotal(item)) }}
                          </span>
                        </div>
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
      </div>

      <!-- Right Column: Order Summary (Sticky) -->
      <div class="cart-summary-column">
        <div class="summary-sticky">
          <q-card flat bordered class="summary-card">
            <q-card-section>
              <div class="text-h6 text-weight-bold q-mb-md">
                <q-icon name="receipt_long" class="q-mr-sm" />
                Order Summary
              </div>

              <q-separator class="q-mb-md" />

              <div class="summary-row q-mb-sm">
                <span class="summary-label">Subtotal ({{ countTotalItems }} {{ countTotalItems === 1 ? 'item' : 'items'
                }}):</span>
                <span class="summary-value">{{ formatMoney(total) }}</span>
              </div>

              <div v-if="selectedReceiveMethod === DELIVERY_TYPE.DELIVER" class="summary-row q-mb-sm">
                <span class="summary-label">
                  <q-icon name="local_shipping" size="xs" class="q-mr-xs" />
                  Delivery Charge:
                </span>
                <span class="summary-value">{{ formatMoney(deliveryCharge) }}</span>
              </div>

              <div v-else class="summary-row q-mb-sm">
                <span class="summary-label text-positive">
                  <q-icon name="check_circle" size="xs" class="q-mr-xs" />
                  Pickup (Free):
                </span>
                <span class="summary-value text-positive">₱0.00</span>
              </div>

              <q-separator class="q-my-md" />

              <div class="summary-row-total">
                <span class="summary-label-total">Total Amount:</span>
                <span class="summary-value-total">{{
                  decimalThousandSeparator(total + deliveryCharge)
                }}</span>
              </div>

              <!-- Trust Indicators -->
              <div class="trust-indicators q-mt-md">
                <div class="trust-item">
                  <q-icon name="lock" size="sm" color="positive" class="q-mr-xs" />
                  <span class="text-caption">Secure Checkout</span>
                </div>
                <div class="trust-item">
                  <q-icon name="verified" size="sm" color="positive" class="q-mr-xs" />
                  <span class="text-caption">Protected Payment</span>
                </div>
              </div>

              <!-- Checkout Button -->
              <q-btn color="primary" class="checkout-btn q-mt-lg" to="/cart/checkout" label="Proceed to Checkout"
                icon="arrow_forward" size="lg" unelevated :disable="disabledCheckout" :loading="disabledCheckout" />
            </q-card-section>
          </q-card>
        </div>
      </div>
    </div>
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

// Calculate total for a single item
const getItemTotal = (item: any): number => {
  const orderDetails = getOrderDetail(item.item_price as any, item.variations);
  return orderDetails.reduce((sum, detail) => {
    return sum + computePrice(detail.count, detail.price);
  }, 0);
};
</script>

<style scoped lang="scss">
.cart-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 16px;
}

.cart-header {
  padding: 16px 0;
}

.cart-title-section {
  margin-top: 16px;
}

.cart-layout {
  display: grid;
  grid-template-columns: 1fr 400px;
  gap: 32px;
  align-items: start;
}

.cart-items-column {
  min-width: 0;
}

.cart-items-section {
  margin-bottom: 32px;
}

.cart-item-card {
  transition: transform 0.2s ease;

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
  background: #f5f5f5;
}

.cart-item-image {
  border-radius: 8px;
  transition: transform 0.3s ease;
}

.cart-item-image-link:hover .cart-item-image {
  transform: scale(1.05);
}

.cart-item-content {
  display: flex;
  flex-direction: column;
  height: 100%;
}

.cart-item-name-link {
  text-decoration: none;
  color: inherit;
  transition: color 0.2s ease;
  flex: 1;

  &:hover {
    color: var(--q-primary);
  }
}

.cart-item-name {
  color: #1a1a1a;
  line-height: 1.4;
  margin-right: 8px;
}

.remove-btn {
  transition: all 0.2s ease;

  &:hover {
    background-color: #ffebee;
    color: #c62828;
  }
}

.cart-item-details {
  padding: 12px 0;
}

.cart-item-variation {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 8px 0;
  color: #666;
  border-bottom: 1px solid #f0f0f0;

  &:last-child {
    border-bottom: none;
  }
}

.variation-info {
  display: flex;
  align-items: center;
  flex: 1;
}

.variation-total {
  margin-left: 16px;
  min-width: 100px;
  text-align: right;
}

.cart-item-subtotal {
  padding-top: 8px;
}

.cart-options-section {
  margin-top: 32px;
}

.options-card {
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  height: 100%;
  transition: box-shadow 0.3s ease;

  &:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
  }
}

.delivery-option,
.payment-option {
  padding: 10px 12px;
  border-radius: 8px;
  transition: all 0.2s ease;
  margin-bottom: 4px;

  &:hover {
    background-color: #f5f5f5;
  }
}

.cart-summary-column {
  position: relative;
}

.summary-sticky {
  position: sticky;
  top: 80px;
}

.summary-card {
  border-radius: 12px;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
  background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
  border: 1px solid #e0e0e0;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 0;
}

.summary-label {
  font-size: 15px;
  color: #666;
  display: flex;
  align-items: center;
}

.summary-value {
  font-size: 15px;
  font-weight: 600;
  color: #1a1a1a;
  text-align: right;
}

.summary-row-total {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px 0 0;
  margin-top: 8px;
}

.summary-label-total {
  font-size: 18px;
  font-weight: bold;
  color: #1a1a1a;
}

.summary-value-total {
  font-size: 24px;
  font-weight: bold;
  color: var(--q-primary);
  letter-spacing: 0.5px;
}

.trust-indicators {
  display: flex;
  justify-content: space-around;
  padding: 12px 0;
  border-top: 1px solid #e0e0e0;
  border-bottom: 1px solid #e0e0e0;
  margin: 16px 0;
}

.trust-item {
  display: flex;
  align-items: center;
  color: #666;
  font-size: 12px;
}

.checkout-btn {
  width: 100%;
  height: 56px;
  font-size: 16px;
  font-weight: 600;
  border-radius: 8px;
  letter-spacing: 0.5px;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  text-transform: none;

  &:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
  }

  &:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
  }
}

@media (max-width: 1024px) {
  .cart-layout {
    grid-template-columns: 1fr;
    gap: 24px;
  }

  .summary-sticky {
    position: static;
  }
}

@media (max-width: 600px) {
  .cart-container {
    padding: 8px;
  }

  .cart-item-name {
    font-size: 1rem;
  }

  .cart-layout {
    gap: 16px;
  }

  .summary-label-total {
    font-size: 16px;
  }

  .summary-value-total {
    font-size: 20px;
  }

  .checkout-btn {
    height: 48px;
    font-size: 15px;
  }

  .variation-total {
    min-width: 80px;
    font-size: 14px;
  }

  .trust-indicators {
    flex-direction: column;
    gap: 8px;
    align-items: center;
  }
}
</style>
