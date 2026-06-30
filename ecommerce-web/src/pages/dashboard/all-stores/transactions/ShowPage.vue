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
            <h1 class="hero-title">Transaction Details</h1>
            <div class="hero-sub">View complete transaction information</div>
          </div>
        </div>
        <q-btn flat icon="arrow_back" label="Back" @click="$router.back()" class="back-btn" />
      </div>
    </div>

    <!-- Reference + Status card -->
    <div class="section-card q-mb-lg">
      <div class="section-card-header">
        <div class="section-icon-wrap">
          <q-icon name="tag" size="18px" color="white" />
        </div>
        <div>
          <div class="section-title">Reference</div>
          <div class="section-sub">{{ localResult.reference_id }}</div>
        </div>
        <div class="ref-meta">
          <q-icon name="calendar_today" size="14px" class="q-mr-xs" />
          {{ localResult.created_at }}
        </div>
      </div>
      <div class="card-divider"></div>
      <div class="section-body ref-status-body">
        <div class="status-select-wrap">
          <q-select v-model="selectedStatusId" :options="localStatuses" option-value="id"
            :option-label="statusOptionLabel" emit-value map-options outlined dense label="Update Status"
            :loading="statusUpdateLoading" :disable="localStatuses.length === 0" class="dark-select"
            @update:model-value="onStatusChange">
            <template v-slot:prepend>
              <q-icon name="flag" color="grey-5" />
            </template>
          </q-select>
        </div>
      </div>
    </div>

    <!-- Info + Pricing row -->
    <div class="two-col-grid q-mb-lg">

      <!-- Transaction Information -->
      <div class="section-card">
        <div class="section-card-header">
          <div class="section-icon-wrap">
            <q-icon name="info" size="18px" color="white" />
          </div>
          <div>
            <div class="section-title">Transaction Information</div>
            <div class="section-sub">Payment &amp; delivery details</div>
          </div>
        </div>
        <div class="card-divider"></div>
        <div class="section-body">
          <div class="info-row">
            <div class="info-icon-wrap info-icon--indigo">
              <q-icon name="payment" size="15px" color="white" />
            </div>
            <div>
              <div class="info-label">Payment Method</div>
              <div class="info-value">{{ localResult.payment_method?.name || 'N/A' }}</div>
            </div>
          </div>
          <div class="info-row">
            <div class="info-icon-wrap info-icon--indigo">
              <q-icon name="local_shipping" size="15px" color="white" />
            </div>
            <div>
              <div class="info-label">Receive Method</div>
              <div class="info-value">{{ localResult.receive_method?.name || 'N/A' }}</div>
            </div>
          </div>
          <div class="info-row">
            <div class="info-icon-wrap info-icon--indigo">
              <q-icon name="phone" size="15px" color="white" />
            </div>
            <div>
              <div class="info-label">Contact Number</div>
              <div class="info-value">{{ localResult.contact_number || 'N/A' }}</div>
            </div>
          </div>
          <div class="info-row" v-if="localResult.lat && localResult.lng">
            <div class="info-icon-wrap info-icon--indigo">
              <q-icon name="location_on" size="15px" color="white" />
            </div>
            <div>
              <div class="info-label">Location</div>
              <div class="info-value">{{ localResult.lat }}, {{ localResult.lng }}</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Pricing Summary -->
      <div class="section-card">
        <div class="section-card-header">
          <div class="section-icon-wrap section-icon-wrap--green">
            <q-icon name="payments" size="18px" color="white" />
          </div>
          <div>
            <div class="section-title">Pricing Summary</div>
            <div class="section-sub">Breakdown of charges</div>
          </div>
        </div>
        <div class="card-divider"></div>
        <div class="section-body">
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

    <!-- Orders Section -->
    <div class="section-card" v-if="localResult.orders && localResult.orders.length > 0">
      <div class="section-card-header">
        <div class="section-icon-wrap section-icon-wrap--amber">
          <q-icon name="shopping_bag" size="18px" color="white" />
        </div>
        <div>
          <div class="section-title">Order Items</div>
          <div class="section-sub">{{ localResult.orders.length }} item{{ localResult.orders.length !== 1 ? 's' : '' }}
            in
            this transaction</div>
        </div>
        <div class="orders-count-badge">{{ localResult.orders.length }}</div>
      </div>
      <div class="card-divider"></div>
      <div class="section-body orders-body">
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
                  <q-icon name="store" size="12px" class="q-mr-xs" />
                  {{ order.store.name }}
                </div>
              </a>
            </div>

            <div class="order-item-body">
              <a v-if="order.store" :href="`/public_stores/${order.store.optimus_id}/item/${order.optimus_item}`"
                target="_blank" class="order-item-name-link">
                <div class="order-item-name">
                  <q-icon name="label" size="sm" class="q-mr-xs" />
                  {{ order.item_name }}
                </div>
                <div class="order-item-description" v-if="order.item_description">
                  {{ order.item_description }}
                </div>
              </a>

              <div class="order-item-details">
                <div class="order-detail-row">
                  <span class="order-detail-label">
                    <q-icon name="shopping_cart" size="xs" class="q-mr-xs" />Quantity
                  </span>
                  <span class="order-detail-value">{{ order.qty }}</span>
                </div>
                <div class="order-detail-row">
                  <span class="order-detail-label">
                    <q-icon name="attach_money" size="xs" class="q-mr-xs" />Unit Price
                  </span>
                  <span class="order-detail-value">₱{{ formatCurrency(order.online_price) }}</span>
                </div>
                <div class="order-detail-row order-subtotal-row">
                  <span class="order-detail-label order-subtotal-label">
                    <q-icon name="receipt" size="xs" class="q-mr-xs" />Subtotal
                  </span>
                  <span class="order-subtotal-value">₱{{ formatCurrency(order.subtotal) }}</span>
                </div>
              </div>
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

onMounted(async () => {
  const result = await show<TransactionDetail>({
    message: 'Getting transaction...',
    entity: 'my-store-transactions',
    optimus_id: Number(route.params.transactionId),
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
});

async function onStatusChange(newStatusId: number | null) {
  if (newStatusId == null || newStatusId === localResult.value.status_id) return;
  const previousId = localResult.value.status_id;
  const previousStatus = localResult.value.status;
  statusUpdateLoading.value = true;
  try {
    const updated = await update(
      {
        entity: 'my-store-transactions',
        optimus_id: Number(route.params.transactionId),
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
// ── Dark theme tokens ──────────────────────────────────────────────────────
$dark-base: #0f172a;
$dark-card: #1e293b;
$dark-elevated: #273549;
$border: rgba(255, 255, 255, 0.08);
$accent: #6366f1;
$accent-2: #7c3aed;
$green: #10b981;
$green-2: #059669;
$amber: #f59e0b;
$amber-2: #d97706;
$white: #ffffff;
$muted: rgba(255, 255, 255, 0.5);

// ── Container ──────────────────────────────────────────────────────────────
.transaction-detail-container {
  padding: 28px 24px;
  max-width: 1100px;
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
  flex-wrap: wrap;
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
  font-size: 22px;
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
  padding: 6px 16px !important;
  height: 40px !important;

  &:hover {
    background: rgba(255, 255, 255, 0.07) !important;
  }
}

// ── Section cards ──────────────────────────────────────────────────────────
.section-card {
  background: $dark-card;
  border: 1px solid $border;
  border-radius: 20px;
  box-shadow: 0 8px 40px rgba(0, 0, 0, 0.25);
  overflow: hidden;
}

.section-card-header {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 20px 24px;
  background: $dark-elevated;
}

.section-icon-wrap {
  width: 40px;
  height: 40px;
  border-radius: 11px;
  background: linear-gradient(135deg, $accent 0%, $accent-2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 3px 12px rgba(99, 102, 241, 0.35);
  flex-shrink: 0;

  &--green {
    background: linear-gradient(135deg, $green 0%, $green-2 100%);
    box-shadow: 0 3px 12px rgba(16, 185, 129, 0.35);
  }

  &--amber {
    background: linear-gradient(135deg, $amber 0%, $amber-2 100%);
    box-shadow: 0 3px 12px rgba(245, 158, 11, 0.35);
  }
}

.section-title {
  font-size: 15px;
  font-weight: 800;
  color: $white;
  line-height: 1.2;
}

.section-sub {
  font-size: 12px;
  color: $muted;
  margin-top: 2px;
}

.ref-meta {
  margin-left: auto;
  display: flex;
  align-items: center;
  font-size: 12px;
  color: $muted;
  font-weight: 500;
}

.card-divider {
  height: 1px;
  background: $border;
}

.section-body {
  padding: 24px;
}

// ── Reference card status select ───────────────────────────────────────────
.ref-status-body {
  display: flex;
  align-items: center;
}

.status-select-wrap {
  min-width: 260px;
  max-width: 360px;
}

.dark-select {
  :deep(.q-field__control) {
    background: $dark-elevated !important;
    border-radius: 12px !important;
  }

  :deep(.q-field__native),
  :deep(.q-field__input) {
    color: $white !important;
  }

  :deep(.q-field__label) {
    color: $muted !important;
  }

  :deep(.q-field__prepend .q-icon),
  :deep(.q-field__append .q-icon) {
    color: $muted !important;
  }

  :deep(.q-field--outlined .q-field__control:before) {
    border-color: $border !important;
  }

  :deep(.q-field--outlined:hover .q-field__control:before) {
    border-color: rgba(99, 102, 241, 0.4) !important;
  }

  :deep(.q-field--focused .q-field__control:before) {
    border-color: $accent !important;
  }
}

// ── Two-column layout ──────────────────────────────────────────────────────
.two-col-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}

// ── Info rows ──────────────────────────────────────────────────────────────
.info-row {
  display: flex;
  align-items: flex-start;
  gap: 14px;
  padding: 14px 0;
  border-bottom: 1px solid rgba(255, 255, 255, 0.04);

  &:last-child {
    border-bottom: none;
    padding-bottom: 0;
  }

  &:first-child {
    padding-top: 0;
  }
}

.info-icon-wrap {
  width: 32px;
  height: 32px;
  border-radius: 9px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  margin-top: 2px;

  &.info-icon--indigo {
    background: linear-gradient(135deg, $accent 0%, $accent-2 100%);
    box-shadow: 0 3px 10px rgba(99, 102, 241, 0.3);
  }
}

.info-label {
  font-size: 11px;
  color: $muted;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 4px;
}

.info-value {
  font-size: 14px;
  color: $white;
  font-weight: 600;
}

// ── Pricing rows ───────────────────────────────────────────────────────────
.price-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 0;
  border-bottom: 1px solid rgba(255, 255, 255, 0.04);

  &:first-child {
    padding-top: 0;
  }
}

.price-label {
  font-size: 13px;
  color: $muted;
  font-weight: 500;
}

.price-value {
  font-size: 14px;
  color: $white;
  font-weight: 600;
}

.price-divider {
  height: 1px;
  background: $border;
  margin: 4px 0;
}

.grand-total-row {
  border-bottom: none;
  padding-bottom: 0;
  margin-top: 4px;
}

.grand-label {
  font-size: 15px;
  font-weight: 800;
  color: $white;
}

.grand-value {
  font-size: 20px;
  font-weight: 900;
  background: linear-gradient(135deg, $green 0%, #34d399 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

// ── Orders section ─────────────────────────────────────────────────────────
.orders-count-badge {
  margin-left: auto;
  min-width: 28px;
  height: 28px;
  border-radius: 8px;
  background: linear-gradient(135deg, $amber 0%, $amber-2 100%);
  color: $white;
  font-size: 13px;
  font-weight: 800;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0 8px;
  box-shadow: 0 3px 10px rgba(245, 158, 11, 0.35);
}

.orders-body {
  padding: 20px 24px;
}

.orders-list {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.order-item {
  background: $dark-elevated;
  border: 1px solid $border;
  border-radius: 16px;
  overflow: hidden;
  transition: border-color 0.2s, box-shadow 0.2s;

  &:hover {
    border-color: rgba(99, 102, 241, 0.3);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
  }
}

.order-item-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 14px 18px;
  background: rgba(255, 255, 255, 0.03);
  border-bottom: 1px solid $border;
}

.order-item-number {
  display: flex;
  align-items: center;
  gap: 10px;
}

.order-num-badge {
  width: 26px;
  height: 26px;
  border-radius: 8px;
  background: linear-gradient(135deg, $accent 0%, $accent-2 100%);
  color: $white;
  font-size: 12px;
  font-weight: 800;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 2px 8px rgba(99, 102, 241, 0.35);
}

.order-num-label {
  font-size: 14px;
  font-weight: 700;
  color: $white;
}

.store-chip-link {
  text-decoration: none;
}

.store-chip {
  display: flex;
  align-items: center;
  background: rgba(99, 102, 241, 0.12);
  border: 1px solid rgba(99, 102, 241, 0.3);
  color: #a5b4fc;
  font-size: 12px;
  font-weight: 700;
  padding: 4px 10px;
  border-radius: 20px;
  transition: background 0.2s;

  &:hover {
    background: rgba(99, 102, 241, 0.22);
    color: $white;
  }
}

.order-item-body {
  display: flex;
  flex-direction: column;
  gap: 14px;
  padding: 16px 18px;
}

.order-item-name-link {
  text-decoration: none;

  &:hover .order-item-name {
    color: #a5b4fc;
  }
}

.order-item-name {
  display: flex;
  align-items: center;
  font-size: 15px;
  font-weight: 700;
  color: $white;
  margin-bottom: 6px;
  transition: color 0.2s;

  :deep(.q-icon) {
    color: $muted !important;
  }
}

.order-item-description {
  font-size: 13px;
  color: $muted;
  line-height: 1.5;
  padding-left: 24px;
}

.order-item-details {
  background: rgba(0, 0, 0, 0.2);
  border: 1px solid $border;
  border-radius: 12px;
  overflow: hidden;
}

.order-detail-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 16px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.04);

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
  background: rgba(16, 185, 129, 0.06);
}

.order-subtotal-label {
  color: #6ee7b7 !important;
  font-weight: 700 !important;
}

.order-subtotal-value {
  font-size: 15px;
  font-weight: 800;
  color: #6ee7b7;
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

  .back-btn {
    width: 100%;
    justify-content: center;
  }

  .two-col-grid {
    grid-template-columns: 1fr;
  }

  .ref-meta {
    display: none;
  }

  .status-select-wrap {
    min-width: 0;
    width: 100%;
    max-width: 100%;
  }

  .section-body {
    padding: 16px;
  }

  .section-card-header {
    padding: 16px 18px;
  }

  .orders-body {
    padding: 16px;
  }

  .order-item-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
  }
}
</style>

<style>
.transaction-detail-container .section-card .q-card__section {
  background: transparent !important;
  color: #ffffff !important;
}
</style>
