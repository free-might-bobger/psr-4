<template>
  <div class="cart-page">

    <!-- Hero Banner -->
    <div class="cart-hero">
      <div class="hero-bg">
        <div class="hero-orb orb-1"></div>
        <div class="hero-orb orb-2"></div>
        <div class="hero-grid"></div>
      </div>
      <div class="hero-inner">
        <BreadCrumbsWrapper :bread-crumbs="[{ name: 'Cart', path: '' }]" />
        <div class="hero-content">
          <div class="hero-icon-wrap">
            <q-icon name="shopping_cart" size="28px" color="white" />
          </div>
          <div>
            <div class="hero-title">Shopping Cart</div>
            <div class="hero-sub" v-if="countTotalItems > 0">
              {{ countTotalItems }} {{ countTotalItems === 1 ? 'item' : 'items' }} ready for checkout
            </div>
            <div class="hero-sub" v-else>Your cart is currently empty</div>
          </div>
        </div>
      </div>
    </div>

    <div class="cart-body">

      <!-- Empty State -->
      <div v-if="countTotalItems === 0" class="empty-state">
        <div class="empty-icon-wrap">
          <q-icon name="shopping_cart" size="56px" color="white" />
        </div>
        <div class="empty-title">Your cart is empty</div>
        <div class="empty-sub">Looks like you haven't added any items yet.</div>
        <button class="empty-cta" @click="$router.push('/')">
          <q-icon name="storefront" size="18px" class="q-mr-xs" />
          Start Shopping
        </button>
      </div>

      <!-- Cart Content -->
      <div v-else class="cart-layout">

        <!-- Left: Items + Options -->
        <div class="cart-left">

          <!-- Section heading -->
          <div class="section-heading">
            <span class="section-heading-dot"></span>
            Cart Items
          </div>

          <!-- Item Cards -->
          <div class="item-list">
            <div v-for="(item, i) in cart" :key="i" class="item-card">
              <!-- Image -->
              <router-link :to="`/public_stores/${item.store.optimus_id}/item/${item.optimus_id}`"
                class="item-img-link">
                <q-img :src="item.primary_img.path_url" class="item-img" :ratio="1" fit="cover">
                  <template v-slot:error>
                    <div class="absolute-full flex flex-center bg-grey-2">
                      <q-icon name="image_not_supported" size="28px" color="grey-5" />
                    </div>
                  </template>
                </q-img>
                <div class="img-overlay">
                  <q-icon name="open_in_new" size="20px" color="white" />
                </div>
              </router-link>

              <!-- Details -->
              <div class="item-details">
                <div class="item-top-row">
                  <router-link :to="`/public_stores/${item.store.optimus_id}/item/${item.optimus_id}`"
                    class="item-name">{{ item.name
                    }}</router-link>
                  <button class="remove-btn" @click="userCart.removeItem(item.optimus_id)">
                    <q-icon name="close" size="16px" />
                  </button>
                </div>

                <div class="variation-list">
                  <div v-for="(orderDetail, index) in getOrderDetail(item.item_price as any, item.variations)"
                    :key="index" class="variation-row">
                    <div class="variation-left">
                      <span class="variation-dot"></span>
                      <span class="variation-text">
                        <strong>{{ orderDetail.count }}</strong> {{ orderDetail.unit_name }}
                        <span class="variation-x">×</span>
                        <span class="variation-price">{{ formatMoney(orderDetail.price) }}</span>
                      </span>
                    </div>
                    <span class="variation-total">
                      {{ formatMoney(computePrice(orderDetail.count, orderDetail.price)) }}
                    </span>
                  </div>
                </div>

                <div class="item-subtotal-row">
                  <span class="item-subtotal-label">Item Subtotal</span>
                  <span class="item-subtotal-value">{{ formatMoney(getItemTotal(item)) }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Options: Delivery + Payment -->
          <div class="section-heading q-mt-xl">
            <span class="section-heading-dot"></span>
            Order Options
          </div>

          <div class="options-grid">
            <!-- Delivery -->
            <div class="option-card">
              <div class="option-card-header">
                <div class="option-icon-wrap">
                  <q-icon name="local_shipping" size="20px" color="white" />
                </div>
                <span class="option-card-title">Delivery Method</span>
              </div>
              <div class="option-radio-list">
                <label v-for="receiveMethod in receiveMethods" :key="receiveMethod.id" class="option-radio-row"
                  :class="{ active: selectedReceiveMethod === receiveMethod.value }">
                  <q-radio v-model="selectedReceiveMethod" :val="receiveMethod.value" color="indigo-6" dense />
                  <span class="option-radio-label">{{ receiveMethod.name }}</span>
                </label>
              </div>
            </div>

            <!-- Payment -->
            <div class="option-card">
              <div class="option-card-header">
                <div class="option-icon-wrap">
                  <q-icon name="payment" size="20px" color="white" />
                </div>
                <span class="option-card-title">Payment Method</span>
              </div>
              <div class="option-radio-list">
                <label v-for="peymentMethod in paymentMethods" :key="peymentMethod.id" class="option-radio-row"
                  :class="{ active: selectedPaymenthMethod === peymentMethod.value }">
                  <q-radio v-model="selectedPaymenthMethod" :val="peymentMethod.value" color="indigo-6" dense />
                  <span class="option-radio-label">{{ peymentMethod.name }}</span>
                </label>
              </div>
            </div>
          </div>

        </div>

        <!-- Right: Order Summary -->
        <div class="cart-right">
          <div class="summary-sticky">
            <div class="summary-card">

              <div class="summary-header">
                <q-icon name="receipt_long" size="20px" color="white" class="q-mr-sm" />
                Order Summary
              </div>

              <div class="summary-body">

                <div class="summary-line">
                  <span class="summary-line-label">
                    Subtotal
                    <span class="summary-item-count">({{ countTotalItems }} {{ countTotalItems === 1 ? 'item' : 'items'
                    }})</span>
                  </span>
                  <span class="summary-line-value">{{ formatMoney(total) }}</span>
                </div>

                <div v-if="selectedReceiveMethod === DELIVERY_TYPE.DELIVER" class="summary-line">
                  <span class="summary-line-label">
                    <q-icon name="local_shipping" size="14px" class="q-mr-xs" />
                    Delivery Charge
                  </span>
                  <span class="summary-line-value">{{ formatMoney(deliveryCharge) }}</span>
                </div>

                <div v-else class="summary-line">
                  <span class="summary-line-label summary-free">
                    <q-icon name="check_circle" size="14px" class="q-mr-xs" />
                    Pickup
                  </span>
                  <span class="summary-line-value summary-free">Free</span>
                </div>

                <div class="summary-divider"></div>

                <div class="summary-total-row">
                  <span class="summary-total-label">Total</span>
                  <span class="summary-total-value">
                    {{ decimalThousandSeparator(total + deliveryCharge) }}
                  </span>
                </div>

                <!-- Trust badges -->
                <div class="trust-row">
                  <div class="trust-badge">
                    <q-icon name="lock" size="14px" />
                    Secure Checkout
                  </div>
                  <div class="trust-badge">
                    <q-icon name="verified" size="14px" />
                    Protected
                  </div>
                </div>

                <!-- CTA -->
                <button class="checkout-btn" :class="{ 'is-disabled': disabledCheckout }" :disabled="disabledCheckout"
                  @click="$router.push('/cart/checkout')">
                  <q-spinner v-if="disabledCheckout" size="18px" color="white" class="q-mr-sm" />
                  <q-icon v-else name="arrow_forward" size="18px" class="q-mr-sm" />
                  Proceed to Checkout
                </button>

              </div>
            </div>
          </div>
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

interface ApiResponse<T> {
  data: {
    data: T;
  };
}

interface DeliveryCharge {
  delivery_amount: number;
}

interface CartItem {
  item_price: number | string;
  variations: any;
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

  receiveMethods.value = (result as ApiResponse<ReceiveMethod[]>).data?.data || [];
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

  paymentMethods.value = (result as ApiResponse<PaymentMethod[]>).data?.data || [];
};

const disabledCheckout = ref(true);
onMounted(() => {
  if (countTotalItems.value > 0) {
    updateDeliveryCharge();
    getReceiveMethods();
    getPaymentMethods();
  }
});


watch(selectedReceiveMethod, async () => {
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
    const delivery = (result as ApiResponse<DeliveryCharge[]>).data?.data?.find((v) => v);
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
const getItemTotal = (item: CartItem): number => {
  const orderDetails = getOrderDetail(item.item_price, item.variations);
  return orderDetails.reduce((sum, detail) => {
    return sum + computePrice(detail.count, detail.price);
  }, 0);
};
</script>

<style scoped lang="scss">
// ── Page ──────────────────────────────────────────────────────────────────
.cart-page {
  background: #f4f5f7;
  min-height: 100vh;
}

// ── Hero Banner ───────────────────────────────────────────────────────────
.cart-hero {
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
.cart-body {
  max-width: 1400px;
  margin: 0 auto;
  padding: 32px;
}

// ── Empty State ───────────────────────────────────────────────────────────
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 80px 20px;
  text-align: center;
}

.empty-icon-wrap {
  width: 100px;
  height: 100px;
  border-radius: 28px;
  background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 12px 32px rgba(99, 102, 241, 0.35);
  margin-bottom: 24px;
  animation: floatIcon 3s ease-in-out infinite;
}

@keyframes floatIcon {

  0%,
  100% {
    transform: translateY(0);
  }

  50% {
    transform: translateY(-12px);
  }
}

.empty-title {
  font-size: 24px;
  font-weight: 900;
  color: #111827;
  margin-bottom: 8px;
}

.empty-sub {
  font-size: 15px;
  color: #6b7280;
  margin-bottom: 28px;
}

.empty-cta {
  display: inline-flex;
  align-items: center;
  height: 50px;
  padding: 0 32px;
  border: none;
  border-radius: 14px;
  background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
  color: white;
  font-size: 15px;
  font-weight: 700;
  cursor: pointer;
  box-shadow: 0 6px 20px rgba(99, 102, 241, 0.35);
  transition: all 0.25s ease;

  &:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 28px rgba(99, 102, 241, 0.45);
  }
}

// ── Layout ────────────────────────────────────────────────────────────────
.cart-layout {
  display: grid;
  grid-template-columns: 1fr 380px;
  gap: 28px;
  align-items: start;
}

.cart-left {
  min-width: 0;
}

// ── Section Heading ───────────────────────────────────────────────────────
.section-heading {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 13px;
  font-weight: 800;
  color: #6b7280;
  text-transform: uppercase;
  letter-spacing: 1.5px;
  margin-bottom: 16px;
}

.section-heading-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: linear-gradient(135deg, #4f46e5, #7c3aed);
  flex-shrink: 0;
}

// ── Item Cards ────────────────────────────────────────────────────────────
.item-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.item-card {
  background: white;
  border-radius: 18px;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
  display: grid;
  grid-template-columns: 110px 1fr;
  overflow: hidden;
  transition: box-shadow 0.25s, transform 0.25s;

  &:hover {
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
  }
}

.item-img-link {
  display: block;
  position: relative;
  overflow: hidden;
  background: #f3f4f6;
  text-decoration: none;

  &:hover .img-overlay {
    opacity: 1;
  }

  &:hover .item-img {
    transform: scale(1.07);
  }
}

.item-img {
  width: 100%;
  height: 100%;
  transition: transform 0.35s ease;
}

.img-overlay {
  position: absolute;
  inset: 0;
  background: rgba(79, 70, 229, 0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.25s;
}

.item-details {
  padding: 18px 20px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.item-top-row {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 8px;
}

.item-name {
  font-size: 16px;
  font-weight: 700;
  color: #111827;
  text-decoration: none;
  line-height: 1.35;
  flex: 1;
  transition: color 0.15s;

  &:hover {
    color: #4f46e5;
  }
}

.remove-btn {
  width: 30px;
  height: 30px;
  border: none;
  border-radius: 8px;
  background: #f3f4f6;
  color: #9ca3af;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  transition: all 0.2s;

  &:hover {
    background: #fee2e2;
    color: #dc2626;
  }
}

.variation-list {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.variation-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 7px 10px;
  background: #f9fafb;
  border-radius: 8px;
}

.variation-left {
  display: flex;
  align-items: center;
  gap: 8px;
  flex: 1;
}

.variation-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: #4f46e5;
  flex-shrink: 0;
}

.variation-text {
  font-size: 13px;
  color: #374151;
}

.variation-x {
  color: #9ca3af;
  margin: 0 3px;
}

.variation-price {
  color: #4f46e5;
  font-weight: 600;
}

.variation-total {
  font-size: 14px;
  font-weight: 800;
  color: #111827;
  min-width: 80px;
  text-align: right;
}

.item-subtotal-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-top: 10px;
  border-top: 1px solid #f3f4f6;
}

.item-subtotal-label {
  font-size: 12px;
  font-weight: 700;
  color: #9ca3af;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.item-subtotal-value {
  font-size: 17px;
  font-weight: 900;
  background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

// ── Options Grid ──────────────────────────────────────────────────────────
.options-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.option-card {
  background: white;
  border-radius: 18px;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
  overflow: hidden;
}

.option-card-header {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 16px 20px;
  background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
}

.option-icon-wrap {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  background: rgba(255, 255, 255, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.option-card-title {
  font-size: 14px;
  font-weight: 800;
  color: white;
  letter-spacing: 0.3px;
}

.option-radio-list {
  padding: 14px 16px;
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.option-radio-row {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 12px;
  border-radius: 10px;
  border: 1.5px solid transparent;
  cursor: pointer;
  transition: all 0.2s;

  &:hover {
    background: #f5f3ff;
    border-color: #e0e7ff;
  }

  &.active {
    background: #f5f3ff;
    border-color: #a5b4fc;
  }
}

.option-radio-label {
  font-size: 14px;
  font-weight: 600;
  color: #374151;
}

// ── Summary Card ──────────────────────────────────────────────────────────
.cart-right {
  min-width: 0;
}

.summary-sticky {
  position: sticky;
  top: 24px;
}

.summary-card {
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 8px 32px rgba(79, 70, 229, 0.15);
}

.summary-header {
  display: flex;
  align-items: center;
  padding: 20px 24px;
  background: linear-gradient(135deg, #1e1b4b 0%, #312e81 55%, #4c1d95 100%);
  font-size: 16px;
  font-weight: 800;
  color: white;
  letter-spacing: 0.3px;
}

.summary-body {
  background: white;
  padding: 20px 24px 24px;
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

.summary-item-count {
  margin-left: 4px;
  color: #9ca3af;
  font-size: 13px;
}

.summary-line-value {
  font-size: 14px;
  font-weight: 700;
  color: #111827;
}

.summary-free {
  color: #059669 !important;
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
  margin-bottom: 20px;
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

.trust-row {
  display: flex;
  gap: 10px;
  margin-bottom: 20px;
}

.trust-badge {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 5px;
  padding: 8px 10px;
  border-radius: 10px;
  background: #f0fdf4;
  color: #059669;
  font-size: 11px;
  font-weight: 700;
  border: 1px solid #bbf7d0;
}

.checkout-btn {
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

  &:hover:not(.is-disabled) {
    transform: translateY(-2px);
    box-shadow: 0 10px 28px rgba(99, 102, 241, 0.45);
  }

  &.is-disabled {
    opacity: 0.55;
    cursor: not-allowed;
  }
}

// ── Responsive ────────────────────────────────────────────────────────────
@media (max-width: 1100px) {
  .cart-layout {
    grid-template-columns: 1fr;
    gap: 24px;
  }

  .summary-sticky {
    position: static;
  }

  .cart-right {
    order: -1;
  }
}

@media (max-width: 768px) {
  .hero-inner {
    padding: 16px 20px 22px;
  }

  .cart-body {
    padding: 20px;
  }

  .hero-title {
    font-size: 22px;
  }

  .options-grid {
    grid-template-columns: 1fr;
  }

  .item-card {
    grid-template-columns: 90px 1fr;
  }
}

@media (max-width: 480px) {
  .cart-body {
    padding: 12px;
  }

  .item-card {
    grid-template-columns: 80px 1fr;
  }

  .item-details {
    padding: 14px 14px;
  }

  .summary-total-value {
    font-size: 22px;
  }
}
</style>
