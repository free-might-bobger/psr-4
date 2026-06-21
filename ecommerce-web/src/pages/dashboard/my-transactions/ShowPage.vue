<template>
  <div class="transaction-detail-container">

    <!-- Hero Header -->
    <div class="detail-hero q-mb-xl">
      <div class="hero-accent"></div>
      <div class="hero-body">
        <div class="hero-left">
          <div class="hero-icon-wrap">
            <q-icon name="receipt_long" size="26px" color="white" />
          </div>
          <div>
            <h2 class="hero-title">Transaction Details</h2>
            <div class="hero-sub">View complete transaction information</div>
          </div>
        </div>
        <q-btn flat class="back-btn" icon="arrow_back" label="Back" @click="$router.back()" />
      </div>
    </div>

    <!-- Transaction Card -->
    <q-card flat class="detail-card q-mb-xl">
      <q-card-section class="detail-card-header">
        <div class="ref-row">
          <div>
            <div class="ref-id">
              <q-icon name="tag" size="18px" class="q-mr-xs" />
              {{ localResult.reference_id }}
            </div>
            <div class="ref-date">
              <q-icon name="calendar_today" size="13px" class="q-mr-xs" />
              {{ localResult.created_at }}
            </div>
          </div>
          <q-badge v-if="localResult.status" :color="getStatusColor(localResult.status.label)"
            :label="localResult.status.label" class="status-badge-large" />
        </div>
      </q-card-section>

      <div class="card-divider"></div>

      <q-card-section class="detail-grid-section">
        <div class="row q-col-gutter-lg">
          <!-- Left: Transaction Info -->
          <div class="col-12 col-md-6">
            <div class="info-group">
              <div class="group-badge">
                <q-icon name="info" size="14px" />
                <span>Transaction Information</span>
              </div>
              <div class="info-rows">
                <div class="info-row">
                  <div class="info-icon-wrap"><q-icon name="payment" size="18px" /></div>
                  <div>
                    <div class="info-label">Payment Method</div>
                    <div class="info-value">{{ localResult.payment_method?.name || 'N/A' }}</div>
                  </div>
                </div>
                <div class="info-row">
                  <div class="info-icon-wrap"><q-icon name="local_shipping" size="18px" /></div>
                  <div>
                    <div class="info-label">Receive Method</div>
                    <div class="info-value">{{ localResult.receive_method?.name || 'N/A' }}</div>
                  </div>
                </div>
                <div class="info-row">
                  <div class="info-icon-wrap"><q-icon name="phone" size="18px" /></div>
                  <div>
                    <div class="info-label">Receiver's Mobile</div>
                    <div class="info-value">{{ localResult.receivers_mobile || 'N/A' }}</div>
                  </div>
                </div>
                <div class="info-row" v-if="localResult.lat && localResult.lng">
                  <div class="info-icon-wrap"><q-icon name="location_on" size="18px" /></div>
                  <div>
                    <div class="info-label">Location</div>
                    <div class="info-value">{{ localResult.lat }}, {{ localResult.lng }}</div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Right: Pricing -->
          <div class="col-12 col-md-6">
            <div class="info-group">
              <div class="group-badge">
                <q-icon name="payments" size="14px" />
                <span>Pricing Summary</span>
              </div>
              <div class="price-rows">
                <div class="price-row">
                  <span class="price-label">Subtotal</span>
                  <span class="price-value">₱{{ formatCurrency(localResult.total) }}</span>
                </div>
                <div class="price-row">
                  <span class="price-label">Delivery Charge</span>
                  <span class="price-value">₱{{ localResult.delivery_charge || '0.00' }}</span>
                </div>
                <div class="price-divider"></div>
                <div class="price-row grand-total-row">
                  <span class="grand-label">Grand Total</span>
                  <span class="grand-value">₱{{ formatCurrency(localResult.grand_total) }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </q-card-section>
    </q-card>

    <!-- Orders Section -->
    <q-card flat class="orders-card" v-if="localResult.orders && localResult.orders.length > 0">
      <q-card-section class="orders-header-section">
        <div class="orders-title-row">
          <div class="orders-icon-wrap">
            <q-icon name="shopping_bag" size="20px" color="white" />
          </div>
          <div>
            <div class="orders-title">Order Items <span class="orders-count">({{ localResult.orders.length }})</span>
            </div>
            <div class="orders-sub">Items included in this transaction</div>
          </div>
        </div>
      </q-card-section>

      <div class="card-divider"></div>

      <q-card-section class="orders-body">
        <div class="orders-list">
          <div v-for="(order, index) in localResult.orders" :key="order.id || index" class="order-item">
            <div class="order-item-header">
              <div class="order-item-number">
                <div class="order-num-badge">{{ index + 1 }}</div>
                <span class="order-num-label">Item {{ index + 1 }}</span>
              </div>
              <a v-if="order.store" :href="`/public_stores/${order.store.optimus_id}`" target="_blank"
                class="store-chip-link">
                <div class="store-chip">
                  <q-icon name="store" size="13px" class="q-mr-xs" />
                  {{ order.store.name }}
                </div>
              </a>
            </div>

            <div class="order-item-body">
              <router-link v-if="order.store"
                :to="`/public_stores/${order.store.optimus_id}/item/${order.optimus_item}`" target="_blank"
                class="order-item-link">
                <div class="order-item-name">
                  <q-icon name="label" size="16px" class="q-mr-xs" />
                  <span>{{ order.item_name }}</span>
                  <q-icon name="open_in_new" size="13px" class="q-ml-xs" />
                </div>
              </router-link>

              <div class="order-item-details">
                <div class="order-detail-row">
                  <span class="order-detail-label">
                    <q-icon name="shopping_cart" size="13px" class="q-mr-xs" />Quantity
                  </span>
                  <span class="order-detail-value">{{ order.qty }}</span>
                </div>
                <div class="order-detail-row">
                  <span class="order-detail-label">
                    <q-icon name="attach_money" size="13px" class="q-mr-xs" />Unit Price
                  </span>
                  <span class="order-detail-value">₱{{ formatCurrency(order.online_price) }}</span>
                </div>
                <div class="order-detail-row order-subtotal-row">
                  <span class="order-detail-label order-subtotal-label">
                    <q-icon name="receipt" size="13px" class="q-mr-xs" />Subtotal
                  </span>
                  <span class="order-subtotal-value">₱{{ formatCurrency(order.subtotal) }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </q-card-section>
    </q-card>
  </div>
</template>

<script setup lang="ts">
import { show } from 'src/boot/axios-call';
import { onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';

interface OrderItem {
  id: number;
  transaction_id: number;
  store_id: number;
  item_id: number;
  optimus_item?: number;
  item_name: string;
  item_description?: string;
  unit_id: number;
  base_price: number;
  store_price: number;
  online_price: number;
  qty: number;
  subtotal: number;
  format_subtotal?: string;
  format_price?: string;
  store?: {
    id: number;
    name: string;
    optimus_id: number;
  };
}

interface TransactionDetail {
  id: number;
  user_id: number;
  reference_id: string;
  status_id: number;
  payment_method_id: number;
  receive_method_id: number;
  lat: number | null;
  lng: number | null;
  receivers_mobile: string;
  delivery_charge: string;
  total: number;
  grand_total: number;
  created_at: string;
  payment_method?: { name: string };
  receive_method?: { name: string };
  status?: { label: string; name: string };
  orders?: OrderItem[];
}

const route = useRoute();

const localResult = ref<TransactionDetail>({
  id: 0,
  user_id: 0,
  reference_id: '',
  status_id: 0,
  payment_method_id: 0,
  receive_method_id: 0,
  lat: null,
  lng: null,
  receivers_mobile: '',
  delivery_charge: '0.00',
  total: 0,
  grand_total: 0,
  created_at: '',
  orders: []
});

onMounted(async () => {
  const result = await show<TransactionDetail>({
    message: 'Getting transaction...',
    entity: 'my-transactions',
    optimus_id: Number(route.params.id),
    query: {
      with: 'paymentMethod,receiveMethod,status,orders.store',
    },
  });
  if (result) {
    localResult.value = result;
  }
});

// Helper functions
const getStatusColor = (status: string | undefined): string => {
  if (!status) return 'grey';
  const statusLower = status.toLowerCase();
  if (statusLower.includes('completed') || statusLower.includes('delivered')) return 'positive';
  if (statusLower.includes('preparing') || statusLower.includes('processing')) return 'warning';
  if (statusLower.includes('cancelled') || statusLower.includes('rejected')) return 'negative';
  return 'primary';
};

const formatCurrency = (amount: number | string): string => {
  if (typeof amount === 'string') {
    return parseFloat(amount).toFixed(2);
  }
  return amount.toFixed(2);
};
</script>

<style scoped lang="scss">
// ── Dark theme tokens ──────────────────────────────────────────────────────
$dark-base: #0f172a;
$dark-card: #1e293b;
$dark-elevated: #273549;
$border: rgba(255, 255, 255, 0.08);
$accent: #6366f1;
$accent-2: #7c3aed;
$white: #ffffff;
$muted: rgba(255, 255, 255, 0.5);

// ── Container ──────────────────────────────────────────────────────────────
.transaction-detail-container {
  padding: 28px 24px;
  max-width: 1200px;
  margin: 0 auto;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  color: $white;
}

// ── Hero header ────────────────────────────────────────────────────────────
.detail-hero {
  position: relative;
  background: $dark-card;
  border-radius: 20px;
  border: 1px solid $border;
  box-shadow: 0 8px 40px rgba(0, 0, 0, 0.3);
  overflow: hidden;
}

.hero-accent {
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, rgba(99, 102, 241, 0.18) 0%, rgba(124, 58, 237, 0.10) 60%, transparent 100%);
  pointer-events: none;
}

.hero-body {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 28px 32px;
  gap: 16px;
}

.hero-left {
  display: flex;
  align-items: center;
  gap: 18px;
}

.hero-icon-wrap {
  width: 52px;
  height: 52px;
  border-radius: 14px;
  background: linear-gradient(135deg, $accent 0%, $accent-2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 16px rgba(99, 102, 241, 0.4);
  flex-shrink: 0;
}

.hero-title {
  font-size: 24px;
  font-weight: 800;
  color: $white;
  margin: 0 0 4px;
  letter-spacing: -0.3px;
  line-height: 1.2;
}

.hero-sub {
  font-size: 13px;
  color: $muted;
  font-weight: 500;
}

.back-btn {
  color: $white !important;
  border: 1px solid $border !important;
  border-radius: 12px !important;
  text-transform: none !important;
  font-weight: 600 !important;
  letter-spacing: 0 !important;
  padding: 6px 18px !important;

  &:hover {
    background: rgba(255, 255, 255, 0.07) !important;
  }
}

// ── Shared card styles ─────────────────────────────────────────────────────
.detail-card,
.orders-card {
  background: $dark-card !important;
  border: 1px solid $border !important;
  border-radius: 20px !important;
  box-shadow: 0 8px 40px rgba(0, 0, 0, 0.25) !important;
  overflow: hidden;
}

.card-divider {
  height: 1px;
  background: $border;
  margin: 0;
}

// ── Transaction card header ────────────────────────────────────────────────
.detail-card-header {
  padding: 24px 28px;
  background: $dark-elevated !important;
}

.ref-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  flex-wrap: wrap;
}

.ref-id {
  display: flex;
  align-items: center;
  font-size: 18px;
  font-weight: 700;
  color: $white;
  margin-bottom: 6px;
}

.ref-date {
  display: flex;
  align-items: center;
  font-size: 13px;
  color: $muted;
}

.status-badge-large {
  font-size: 13px;
  padding: 6px 16px;
  border-radius: 20px;
  font-weight: 700;
  letter-spacing: 0.2px;
}

// ── Info grid ──────────────────────────────────────────────────────────────
.detail-grid-section {
  padding: 28px;
}

.info-group {
  background: $dark-elevated;
  border-radius: 16px;
  padding: 22px;
  height: 100%;
  border: 1px solid $border;
}

.group-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: linear-gradient(135deg, rgba(99, 102, 241, 0.18) 0%, rgba(124, 58, 237, 0.12) 100%);
  border: 1px solid rgba(99, 102, 241, 0.3);
  color: #a5b4fc;
  font-size: 12px;
  font-weight: 700;
  letter-spacing: 0.5px;
  text-transform: uppercase;
  padding: 5px 12px;
  border-radius: 20px;
  margin-bottom: 20px;
}

.info-rows {
  display: flex;
  flex-direction: column;
  gap: 0;
}

.info-row {
  display: flex;
  align-items: flex-start;
  gap: 14px;
  padding: 14px 0;
  border-bottom: 1px solid $border;

  &:last-child {
    border-bottom: none;
    padding-bottom: 0;
  }
}

.info-icon-wrap {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  background: rgba(99, 102, 241, 0.12);
  color: #a5b4fc;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.info-label {
  font-size: 12px;
  color: $muted;
  font-weight: 500;
  margin-bottom: 3px;
  text-transform: uppercase;
  letter-spacing: 0.4px;
}

.info-value {
  font-size: 15px;
  color: $white;
  font-weight: 600;
}

// ── Pricing ────────────────────────────────────────────────────────────────
.price-rows {
  display: flex;
  flex-direction: column;
  gap: 0;
}

.price-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 13px 0;
  border-bottom: 1px solid $border;

  &:last-child {
    border-bottom: none;
    padding-bottom: 0;
  }
}

.price-label {
  font-size: 14px;
  color: $muted;
  font-weight: 500;
}

.price-value {
  font-size: 15px;
  color: $white;
  font-weight: 600;
}

.price-divider {
  height: 1px;
  background: rgba(99, 102, 241, 0.25);
  margin: 4px 0;
}

.grand-total-row {
  background: rgba(99, 102, 241, 0.10);
  border-radius: 12px;
  padding: 14px 16px !important;
  border: 1px solid rgba(99, 102, 241, 0.25) !important;
  margin-top: 4px;
}

.grand-label {
  font-size: 16px;
  font-weight: 800;
  color: $white;
}

.grand-value {
  font-size: 20px;
  font-weight: 800;
  color: #a5b4fc;
}

// ── Orders section ─────────────────────────────────────────────────────────
.orders-header-section {
  padding: 24px 28px;
  background: $dark-elevated !important;
}

.orders-title-row {
  display: flex;
  align-items: center;
  gap: 16px;
}

.orders-icon-wrap {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  background: linear-gradient(135deg, $accent 0%, $accent-2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 14px rgba(99, 102, 241, 0.35);
  flex-shrink: 0;
}

.orders-title {
  font-size: 18px;
  font-weight: 800;
  color: $white;
  line-height: 1.2;
}

.orders-count {
  color: #a5b4fc;
}

.orders-sub {
  font-size: 13px;
  color: $muted;
  margin-top: 2px;
}

.orders-body {
  padding: 24px 28px;
}

.orders-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

// ── Order item card ────────────────────────────────────────────────────────
.order-item {
  background: $dark-elevated;
  border-radius: 16px;
  padding: 20px;
  border: 1px solid $border;
  position: relative;
  overflow: hidden;

  &::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 3px;
    height: 100%;
    background: linear-gradient(180deg, $accent 0%, $accent-2 100%);
  }
}

.order-item-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
  padding-bottom: 14px;
  border-bottom: 1px solid $border;
}

.order-item-number {
  display: flex;
  align-items: center;
  gap: 10px;
}

.order-num-badge {
  width: 28px;
  height: 28px;
  border-radius: 8px;
  background: linear-gradient(135deg, $accent 0%, $accent-2 100%);
  color: $white;
  font-size: 13px;
  font-weight: 800;
  display: flex;
  align-items: center;
  justify-content: center;
}

.order-num-label {
  font-size: 15px;
  font-weight: 700;
  color: $white;
}

.store-chip-link {
  text-decoration: none;
}

.store-chip {
  display: inline-flex;
  align-items: center;
  background: rgba(99, 102, 241, 0.12);
  border: 1px solid rgba(99, 102, 241, 0.3);
  color: #a5b4fc;
  font-size: 12px;
  font-weight: 700;
  padding: 4px 12px;
  border-radius: 20px;
  transition: background 0.2s;

  &:hover {
    background: rgba(99, 102, 241, 0.22);
  }
}

.order-item-body {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.order-item-link {
  text-decoration: none;
}

.order-item-name {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: 15px;
  font-weight: 700;
  color: #a5b4fc;
  padding: 8px 12px;
  border-radius: 10px;
  background: rgba(99, 102, 241, 0.08);
  border: 1px solid rgba(99, 102, 241, 0.15);
  transition: background 0.2s, color 0.2s;

  &:hover {
    background: rgba(99, 102, 241, 0.18);
    color: #c4b5fd;
  }
}

.order-item-details {
  background: rgba(15, 23, 42, 0.5);
  border-radius: 12px;
  border: 1px solid $border;
  overflow: hidden;
}

.order-detail-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 16px;
  border-bottom: 1px solid $border;

  &:last-child {
    border-bottom: none;
  }
}

.order-detail-label {
  display: flex;
  align-items: center;
  font-size: 13px;
  color: $muted;
  font-weight: 500;
}

.order-detail-value {
  font-size: 14px;
  color: $white;
  font-weight: 600;
}

.order-subtotal-row {
  background: rgba(99, 102, 241, 0.08);
}

.order-subtotal-label {
  color: $white !important;
  font-weight: 700 !important;
}

.order-subtotal-value {
  font-size: 15px;
  font-weight: 800;
  color: #a5b4fc;
}

// ── Responsive ─────────────────────────────────────────────────────────────
@media (max-width: 768px) {
  .transaction-detail-container {
    padding: 16px 12px;
  }

  .hero-body {
    padding: 20px;
    flex-direction: column;
    align-items: flex-start;
  }

  .detail-grid-section,
  .orders-body {
    padding: 16px;
  }

  .detail-card-header,
  .orders-header-section {
    padding: 16px 20px;
  }

  .order-item-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
  }
}
</style>

<style>
.transaction-detail-container .detail-card,
.transaction-detail-container .orders-card {
  background: #1e293b !important;
}

.transaction-detail-container .detail-card .q-card__section,
.transaction-detail-container .orders-card .q-card__section {
  background: transparent !important;
  color: #ffffff !important;
}

.transaction-detail-container .detail-card-header {
  background: #273549 !important;
}

.transaction-detail-container .orders-header-section {
  background: #273549 !important;
}
</style>
