<template>
  <div class="txn-detail-page">

    <!-- Hero Header -->
    <div class="page-hero q-mb-xl">
      <div class="hero-accent-overlay"></div>
      <div class="hero-inner">
        <q-btn flat round dense icon="arrow_back" @click="$router.back()" class="back-btn">
          <q-tooltip>Go Back</q-tooltip>
        </q-btn>
        <div class="hero-icon-wrap">
          <q-icon name="receipt_long" size="28px" color="white" />
        </div>
        <div class="hero-text">
          <h1 class="page-title">Transaction Details</h1>
          <div class="page-subtitle">View complete transaction information</div>
        </div>
      </div>
    </div>

    <!-- Reference & Status Card -->
    <div class="ref-card q-mb-lg">
      <div class="ref-card-inner">
        <div class="ref-left">
          <div class="ref-badge">
            <q-icon name="tag" size="18px" color="white" />
          </div>
          <div>
            <div class="ref-id">#{{ localResult.reference_id }}</div>
            <div class="ref-date">
              <q-icon name="calendar_today" size="14px" class="q-mr-xs" />
              {{ localResult.created_at }}
            </div>
          </div>
        </div>
        <div class="ref-right">
          <q-select v-model="selectedStatusId" :options="localStatuses" option-value="id"
            :option-label="statusOptionLabel" emit-value map-options outlined dense label="Status"
            :loading="statusUpdateLoading" :disable="localStatuses.length === 0" class="status-select"
            @update:model-value="onStatusChange">
            <template v-slot:prepend>
              <q-icon name="flag" />
            </template>
          </q-select>
        </div>
      </div>
    </div>

    <!-- Info Grid -->
    <div class="info-grid q-mb-lg">
      <!-- Transaction Info Card -->
      <div class="info-card">
        <div class="info-card-header">
          <div class="info-icon-wrap info-icon">
            <q-icon name="info" size="20px" color="white" />
          </div>
          <div class="info-card-title">Transaction Information</div>
        </div>
        <div class="info-card-body">
          <div class="info-row">
            <div class="info-row-icon">
              <q-icon name="payment" size="18px" />
            </div>
            <div class="info-row-content">
              <div class="info-label">Payment Method</div>
              <div class="info-value">{{ localResult.payment_method?.name || 'N/A' }}</div>
            </div>
          </div>
          <div class="info-row">
            <div class="info-row-icon">
              <q-icon name="local_shipping" size="18px" />
            </div>
            <div class="info-row-content">
              <div class="info-label">Receive Method</div>
              <div class="info-value">{{ localResult.receive_method?.name || 'N/A' }}</div>
            </div>
          </div>
          <div class="info-row">
            <div class="info-row-icon">
              <q-icon name="phone" size="18px" />
            </div>
            <div class="info-row-content">
              <div class="info-label">Contact Number</div>
              <div class="info-value">{{ localResult.contact_number || 'N/A' }}</div>
            </div>
          </div>
          <div class="info-row" v-if="localResult.lat && localResult.lng">
            <div class="info-row-icon">
              <q-icon name="location_on" size="18px" />
            </div>
            <div class="info-row-content">
              <div class="info-label">Location</div>
              <div class="info-value">{{ localResult.lat }}, {{ localResult.lng }}</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Pricing Summary Card -->
      <div class="info-card">
        <div class="info-card-header">
          <div class="info-icon-wrap pricing-icon">
            <q-icon name="attach_money" size="20px" color="white" />
          </div>
          <div class="info-card-title">Pricing Summary</div>
        </div>
        <div class="info-card-body">
          <div class="price-row">
            <span class="price-label">Subtotal</span>
            <span class="price-value">₱{{ formatCurrency(localResult.total) }}</span>
          </div>
          <div class="price-row">
            <span class="price-label">Delivery Charge</span>
            <span class="price-value">₱{{ localResult.delivery_charge || '0.00' }}</span>
          </div>
          <div class="price-divider"></div>
          <div class="grand-total-row">
            <span class="grand-total-label">Grand Total</span>
            <span class="grand-total-value">₱{{ formatCurrency(localResult.grand_total) }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Orders Section -->
    <div class="orders-section" v-if="localResult.orders && localResult.orders.length > 0">
      <div class="orders-header">
        <div class="orders-header-left">
          <div class="info-icon-wrap orders-icon">
            <q-icon name="shopping_bag" size="20px" color="white" />
          </div>
          <div>
            <div class="orders-title">Order Items</div>
            <div class="orders-subtitle">{{ localResult.orders.length }} item{{ localResult.orders.length !== 1 ? 's' :
              ''
            }} in this transaction</div>
          </div>
        </div>
        <div class="orders-count-badge">{{ localResult.orders.length }}</div>
      </div>

      <div class="orders-list">
        <div v-for="(order, index) in localResult.orders" :key="order.id || index" class="order-card">
          <div class="order-card-top">
            <div class="order-number-wrap">
              <div class="order-number-badge">{{ index + 1 }}</div>
              <a v-if="order.store" :href="`/public_stores/${order.store.optimus_id}/item/${order.optimus_item}`"
                target="_blank" class="item-link">
                <span class="item-name">{{ order.item_name }}</span>
                <q-icon name="open_in_new" size="14px" class="external-icon" />
              </a>
              <span v-else class="item-name">{{ order.item_name }}</span>
            </div>
            <a v-if="order.store" :href="`/public_stores/${order.store.optimus_id}`" target="_blank" class="store-chip">
              <q-icon name="store" size="14px" class="q-mr-xs" />
              {{ order.store.name }}
            </a>
          </div>

          <div class="order-card-body">
            <div class="order-detail">
              <span class="order-detail-label">
                <q-icon name="shopping_cart" size="16px" class="q-mr-xs" />
                Quantity
              </span>
              <span class="order-detail-value">{{ order.qty }}</span>
            </div>
            <div class="order-detail">
              <span class="order-detail-label">
                <q-icon name="sell" size="16px" class="q-mr-xs" />
                Unit Price
              </span>
              <span class="order-detail-value">₱{{ formatCurrency(order.online_price) }}</span>
            </div>
            <div class="order-subtotal-row">
              <span class="order-subtotal-label">Subtotal</span>
              <span class="order-subtotal-value">₱{{ formatCurrency(order.subtotal) }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { show, get, update } from 'src/boot/axios-call';
import { onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';
import type { AxiosResponse } from 'axios';

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
  contact_number: string;
  delivery_charge: string;
  total: number;
  grand_total: number;
  created_at: string;
  payment_method?: { name: string };
  receive_method?: { name: string };
  status?: { label: string; name: string };
  orders?: OrderItem[];
}

/** Matches API `statuses` list (Status model: id, name, label, value). */
interface Status {
  id: number;
  name?: string;
  label?: string;
  value?: number;
  optimus_id?: number;
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
  contact_number: '',
  delivery_charge: '0.00',
  total: 0,
  grand_total: 0,
  created_at: '',
  orders: []
});

const localStatuses = ref<Status[]>([]);
const selectedStatusId = ref<number | null>(null);
const statusUpdateLoading = ref(false);

function statusOptionLabel(opt: Status): string {
  return opt.label || opt.name || '';
}

async function fetchTransactionData() {
  const result = await show<TransactionDetail>({
    message: 'Getting transaction...',
    entity: 'all-transactions',
    optimus_id: Number(route.params.id),
    query: {
      with: 'paymentMethod,receiveMethod,status,orders.store',
    },
  });
  if (result) {
    localResult.value = result;
    selectedStatusId.value = result.status_id;
  }
  const statusesRes = (await get(
    { entity: 'statuses', query: { limit: 500 } },
    false
  )) as AxiosResponse<{ data: Status[] }> | undefined;
  if (statusesRes?.data?.data) {
    localStatuses.value = statusesRes.data.data;
  }
}

onMounted(async () => {
  await fetchTransactionData();
});

async function onStatusChange(newStatusId: number | null) {
  if (newStatusId == null || newStatusId === localResult.value.status_id) return;
  const previousId = localResult.value.status_id;
  const previousStatus = localResult.value.status;
  statusUpdateLoading.value = true;
  try {
    const updated = await update(
      {
        entity: 'all-transactions',
        optimus_id: Number(route.params.id),
        data: { status_id: newStatusId },
      },
      true,
      true
    );
    if (updated) {
      localResult.value.status_id = newStatusId;
      const s = localStatuses.value.find((x) => x.id === newStatusId);
      if (s) {
        localResult.value.status = {
          label: s.label || s.name || '',
          name: s.name || '',
        };
      }
    } else {
      selectedStatusId.value = previousId;
      localResult.value.status = previousStatus;
    }
  } finally {
    statusUpdateLoading.value = false;
    await fetchTransactionData();
  }
}

const formatCurrency = (amount: number | string): string => {
  if (typeof amount === 'string') {
    return parseFloat(amount).toFixed(2);
  }
  return amount.toFixed(2);
};
</script>

<style scoped lang="scss">
// ── Dark theme tokens (matching DashboardLayout and ProfilePage) ─────────────
$dark-base: #0f172a;
$dark-card: #1e293b;
$dark-elevated: #273549;
$border: rgba(255, 255, 255, 0.08);
$border-strong: rgba(255, 255, 255, 0.12);
$accent: #6366f1;
$accent-2: #7c3aed;
$green: #10b981;
$green-2: #059669;
$blue: #3b82f6;
$blue-2: #2563eb;
$yellow: #fbbf24;
$red: #ef4444;
$white: #ffffff;
$muted: rgba(255, 255, 255, 0.5);
$muted-2: rgba(255, 255, 255, 0.3);

// ── Page Container ───────────────────────────────────────────────────────────
.txn-detail-page {
  max-width: 1000px;
  margin: 0 auto;
  padding: 28px 24px 60px;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  color: $white;
}

// ── Hero Header ──────────────────────────────────────────────────────────────
.page-hero {
  position: relative;
  background: $dark-card;
  border-radius: 20px;
  border: 1px solid $border;
  box-shadow: 0 8px 40px rgba(0, 0, 0, 0.3);
  overflow: hidden;
}

.hero-accent-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, rgba(99, 102, 241, 0.18) 0%, rgba(124, 58, 237, 0.10) 60%, transparent 100%);
  pointer-events: none;
}

.hero-inner {
  position: relative;
  display: flex;
  align-items: center;
  padding: 32px 36px;
  gap: 16px;
}

.back-btn {
  width: 44px;
  height: 44px;
  border-radius: 14px;
  background: rgba(255, 255, 255, 0.08) !important;
  color: $white !important;
  transition: all 0.25s ease;
  flex-shrink: 0;

  &:hover {
    background: rgba(255, 255, 255, 0.14) !important;
    transform: translateX(-3px);
  }
}

.hero-icon-wrap {
  width: 56px;
  height: 56px;
  border-radius: 16px;
  background: linear-gradient(135deg, $accent 0%, $accent-2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 8px 24px rgba(99, 102, 241, 0.4);
  flex-shrink: 0;
}

.page-title {
  font-size: 26px;
  font-weight: 800;
  color: $white !important;
  margin: 0 0 4px;
  letter-spacing: -0.3px;
  line-height: 1.2;
}

.page-subtitle {
  font-size: 14px;
  color: $muted;
  font-weight: 500;
}

// ── Reference & Status Card ─────────────────────────────────────────────────
.ref-card {
  background: $dark-card;
  border: 1px solid $border;
  border-radius: 18px;
  box-shadow: 0 4px 24px rgba(0, 0, 0, 0.2);
}

.ref-card-inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 24px 28px;
  gap: 20px;
  flex-wrap: wrap;
}

.ref-left {
  display: flex;
  align-items: center;
  gap: 14px;
}

.ref-badge {
  width: 42px;
  height: 42px;
  border-radius: 12px;
  background: linear-gradient(135deg, $accent 0%, $accent-2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
  flex-shrink: 0;
}

.ref-id {
  font-size: 18px;
  font-weight: 800;
  color: #a5b4fc;
}

.ref-date {
  font-size: 13px;
  color: $muted-2;
  display: flex;
  align-items: center;
  margin-top: 2px;
}

.ref-right {
  min-width: 200px;
  max-width: 260px;
}

.status-select {
  width: 100%;

  :deep(.q-field__control) {
    background: $dark-elevated !important;
    border-color: $border !important;
    border-radius: 12px !important;
    color: $white !important;
  }

  :deep(.q-field__native) {
    color: $white !important;
    font-size: 14px;
  }

  :deep(.q-field__label) {
    color: $muted !important;
  }

  :deep(.q-field__marginal) {
    color: $muted !important;
  }
}

// ── Info Grid ────────────────────────────────────────────────────────────────
.info-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}

.info-card {
  background: $dark-card;
  border: 1px solid $border;
  border-radius: 18px;
  box-shadow: 0 4px 24px rgba(0, 0, 0, 0.2);
  overflow: hidden;
}

.info-card-header {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 24px 24px 0;
  margin-bottom: 20px;
}

.info-icon-wrap {
  width: 40px;
  height: 40px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;

  &.info-icon {
    background: linear-gradient(135deg, $blue 0%, $blue-2 100%);
    box-shadow: 0 4px 16px rgba(59, 130, 246, 0.3);
  }

  &.pricing-icon {
    background: linear-gradient(135deg, $green 0%, $green-2 100%);
    box-shadow: 0 4px 16px rgba(16, 185, 129, 0.3);
  }

  &.orders-icon {
    background: linear-gradient(135deg, $accent 0%, $accent-2 100%);
    box-shadow: 0 4px 16px rgba(99, 102, 241, 0.3);
  }
}

.info-card-title {
  font-size: 16px;
  font-weight: 700;
  color: $white;
}

.info-card-body {
  padding: 0 24px 24px;
}

// ── Info Rows ────────────────────────────────────────────────────────────────
.info-row {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 14px 0;
  border-bottom: 1px solid $border;

  &:last-child {
    border-bottom: none;
  }
}

.info-row-icon {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  background: rgba(255, 255, 255, 0.06);
  display: flex;
  align-items: center;
  justify-content: center;
  color: $muted;
  flex-shrink: 0;
}

.info-row-content {
  flex: 1;
}

.info-label {
  font-size: 12px;
  font-weight: 600;
  color: $muted;
  text-transform: uppercase;
  letter-spacing: 0.3px;
  margin-bottom: 2px;
}

.info-value {
  font-size: 15px;
  font-weight: 600;
  color: rgba(255, 255, 255, 0.85);
}

// ── Pricing Rows ─────────────────────────────────────────────────────────────
.price-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 0;
  border-bottom: 1px solid $border;

  &:last-child {
    border-bottom: none;
  }
}

.price-label {
  font-size: 14px;
  color: $muted;
  font-weight: 500;
}

.price-value {
  font-size: 15px;
  font-weight: 600;
  color: rgba(255, 255, 255, 0.85);
}

.price-divider {
  height: 1px;
  background: $border;
  margin: 8px 0;
}

.grand-total-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: rgba($accent, 0.08);
  border: 1px solid rgba($accent, 0.2);
  border-radius: 14px;
  padding: 18px 20px;
  margin-top: 8px;
}

.grand-total-label {
  font-size: 16px;
  font-weight: 800;
  color: $white;
}

.grand-total-value {
  font-size: 22px;
  font-weight: 800;
  color: #a5b4fc;
}

// ── Orders Section ───────────────────────────────────────────────────────────
.orders-section {
  background: $dark-card;
  border: 1px solid $border;
  border-radius: 20px;
  box-shadow: 0 8px 40px rgba(0, 0, 0, 0.25);
  overflow: hidden;
}

.orders-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 28px 28px 0;
  margin-bottom: 24px;
}

.orders-header-left {
  display: flex;
  align-items: center;
  gap: 14px;
}

.orders-title {
  font-size: 18px;
  font-weight: 700;
  color: $white;
}

.orders-subtitle {
  font-size: 13px;
  color: $muted;
  margin-top: 2px;
}

.orders-count-badge {
  width: 40px;
  height: 40px;
  border-radius: 12px;
  background: rgba($accent, 0.15);
  color: #a5b4fc;
  font-size: 16px;
  font-weight: 800;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid rgba($accent, 0.2);
}

.orders-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
  padding: 0 28px 28px;
}

// ── Order Card ───────────────────────────────────────────────────────────────
.order-card {
  background: $dark-elevated;
  border: 1px solid $border;
  border-radius: 16px;
  overflow: hidden;
  transition: all 0.2s ease;

  &:hover {
    border-color: rgba($accent, 0.2);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
  }
}

.order-card-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 18px 20px;
  border-bottom: 1px solid $border;
  gap: 12px;
  flex-wrap: wrap;
}

.order-number-wrap {
  display: flex;
  align-items: center;
  gap: 12px;
  flex: 1;
  min-width: 0;
}

.order-number-badge {
  width: 32px;
  height: 32px;
  border-radius: 10px;
  background: linear-gradient(135deg, $accent 0%, $accent-2 100%);
  color: $white;
  font-size: 14px;
  font-weight: 800;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
}

.item-link {
  display: flex;
  align-items: center;
  gap: 6px;
  text-decoration: none;
  transition: all 0.2s ease;
  min-width: 0;

  &:hover .item-name {
    color: $accent;
  }

  &:hover .external-icon {
    opacity: 1;
    transform: translateX(2px);
  }
}

.item-name {
  font-size: 15px;
  font-weight: 700;
  color: #a5b4fc;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  transition: color 0.2s ease;
}

.external-icon {
  color: $muted;
  opacity: 0.5;
  transition: all 0.2s ease;
  flex-shrink: 0;
}

.store-chip {
  display: inline-flex;
  align-items: center;
  font-size: 12px;
  font-weight: 600;
  color: $green;
  background: rgba($green, 0.12);
  border: 1px solid rgba($green, 0.2);
  border-radius: 10px;
  padding: 6px 14px;
  text-decoration: none;
  transition: all 0.2s ease;
  flex-shrink: 0;

  &:hover {
    background: rgba($green, 0.2);
    border-color: rgba($green, 0.35);
  }
}

.order-card-body {
  padding: 18px 20px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.order-detail {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 8px 0;
  border-bottom: 1px solid $border;

  &:last-of-type {
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
  font-weight: 600;
  color: rgba(255, 255, 255, 0.85);
}

.order-subtotal-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: rgba($accent, 0.08);
  border: 1px solid rgba($accent, 0.15);
  border-radius: 12px;
  padding: 14px 16px;
  margin-top: 4px;
}

.order-subtotal-label {
  font-size: 14px;
  font-weight: 800;
  color: $white;
}

.order-subtotal-value {
  font-size: 16px;
  font-weight: 800;
  color: #a5b4fc;
}

// ── Responsive ───────────────────────────────────────────────────────────────
@media (max-width: 768px) {
  .txn-detail-page {
    padding: 16px 12px 48px;
  }

  .hero-inner {
    flex-wrap: wrap;
    padding: 24px 20px;
    gap: 12px;
  }

  .page-title {
    font-size: 22px;
  }

  .ref-card-inner {
    flex-direction: column;
    align-items: stretch;
    gap: 16px;
    padding: 20px;
  }

  .ref-right {
    min-width: 0;
    max-width: 100%;
  }

  .info-grid {
    grid-template-columns: 1fr;
    gap: 16px;
  }

  .orders-header {
    padding: 20px 20px 0;
    flex-wrap: wrap;
    gap: 12px;
  }

  .orders-list {
    padding: 0 20px 20px;
    gap: 12px;
  }

  .order-card-top {
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
    padding: 16px;
  }

  .order-card-body {
    padding: 16px;
  }
}
</style>
