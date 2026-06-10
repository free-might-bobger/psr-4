<template>
  <div class="transaction-detail-container">
    <!-- Header Section -->
    <div class="transaction-header-section q-mb-lg">
      <div class="row items-center">
        <div class="col">
          <div class="text-h4 text-weight-bold">
            <q-icon name="receipt_long" color="primary" class="q-mr-sm" />
            Transaction Details
          </div>
          <div class="text-body2 text-grey-7 q-mt-xs">
            View complete transaction information
          </div>
        </div>
        <q-btn outline color="primary" icon="arrow_back" label="Back" @click="$router.back()" />
      </div>
    </div>

    <!-- Transaction Card -->
    <q-card flat bordered class="transaction-detail-card">
      <!-- Header with Reference and Date -->
      <q-card-section class="transaction-detail-header">
        <div class="row items-center justify-between">
          <div class="col-auto">
            <div class="text-h6 text-weight-bold q-mb-xs">
              <q-icon name="tag" color="primary" class="q-mr-sm" />
              Reference: {{ localResult.reference_id }}
            </div>
            <div class="text-body2 text-grey-7">
              <q-icon name="calendar_today" size="xs" class="q-mr-xs" />
              {{ localResult.created_at }}
            </div>
          </div>
          <div class="col-auto status-select-wrap">
            <q-select
              v-model="selectedStatusId"
              :options="localStatuses"
              option-value="id"
              :option-label="statusOptionLabel"
              emit-value
              map-options
              outlined
              dense
              label="Status"
              :loading="statusUpdateLoading"
              :disable="localStatuses.length === 0"
              class="status-select"
              @update:model-value="onStatusChange"
            >
              <template v-slot:prepend>
                <q-icon name="flag" color="primary" />
              </template>
            </q-select>
          </div>
        </div>
      </q-card-section>

      <q-separator />

      <!-- Transaction Information -->
      <q-card-section class="transaction-info-section">
        <div class="row q-col-gutter-md">
          <!-- Left Column -->
          <div class="col-12 col-md-6">
            <div class="info-group">
              <div class="text-subtitle2 text-weight-bold text-grey-8 q-mb-md">
                <q-icon name="info" size="sm" class="q-mr-xs" />
                Transaction Information
              </div>

              <q-list dense class="info-list">
                <q-item class="info-item">
                  <q-item-section avatar>
                    <q-icon name="payment" color="primary" />
                  </q-item-section>
                  <q-item-section>
                    <q-item-label class="info-label">Payment Method</q-item-label>
                    <q-item-label caption class="info-value">
                      {{ localResult.payment_method?.name || 'N/A' }}
                    </q-item-label>
                  </q-item-section>
                </q-item>

                <q-item class="info-item">
                  <q-item-section avatar>
                    <q-icon name="local_shipping" color="primary" />
                  </q-item-section>
                  <q-item-section>
                    <q-item-label class="info-label">Receive Method</q-item-label>
                    <q-item-label caption class="info-value">
                      {{ localResult.receive_method?.name || 'N/A' }}
                    </q-item-label>
                  </q-item-section>
                </q-item>

                <q-item class="info-item">
                  <q-item-section avatar>
                    <q-icon name="phone" color="primary" />
                  </q-item-section>
                  <q-item-section>
                    <q-item-label class="info-label">Contact Number</q-item-label>
                    <q-item-label caption class="info-value">
                      {{ localResult.contact_number || 'N/A' }}
                    </q-item-label>
                  </q-item-section>
                </q-item>

                <q-item class="info-item" v-if="localResult.lat && localResult.lng">
                  <q-item-section avatar>
                    <q-icon name="location_on" color="primary" />
                  </q-item-section>
                  <q-item-section>
                    <q-item-label class="info-label">Location</q-item-label>
                    <q-item-label caption class="info-value">
                      {{ localResult.lat }}, {{ localResult.lng }}
                    </q-item-label>
                  </q-item-section>
                </q-item>
              </q-list>
            </div>
          </div>

          <!-- Right Column - Pricing -->
          <div class="col-12 col-md-6">
            <div class="info-group">
              <div class="text-subtitle2 text-weight-bold text-grey-8 q-mb-md">
                <q-icon name="attach_money" size="sm" class="q-mr-xs" />
                Pricing Summary
              </div>

              <q-list dense class="info-list">
                <q-item class="info-item">
                  <q-item-section>
                    <q-item-label class="info-label">Subtotal</q-item-label>
                  </q-item-section>
                  <q-item-section side>
                    <q-item-label class="info-value text-weight-medium">
                      ₱{{ formatCurrency(localResult.total) }}
                    </q-item-label>
                  </q-item-section>
                </q-item>

                <q-item class="info-item">
                  <q-item-section>
                    <q-item-label class="info-label">Delivery Charge</q-item-label>
                  </q-item-section>
                  <q-item-section side>
                    <q-item-label class="info-value text-weight-medium">
                      ₱{{ localResult.delivery_charge || '0.00' }}
                    </q-item-label>
                  </q-item-section>
                </q-item>

                <q-separator class="q-my-sm" />

                <q-item class="info-item grand-total">
                  <q-item-section>
                    <q-item-label class="info-label text-h6 text-weight-bold">Grand Total</q-item-label>
                  </q-item-section>
                  <q-item-section side>
                    <q-item-label class="info-value text-h6 text-weight-bold text-primary">
                      ₱{{ formatCurrency(localResult.grand_total) }}
                    </q-item-label>
                  </q-item-section>
                </q-item>
              </q-list>
            </div>
          </div>
        </div>
      </q-card-section>
    </q-card>

    <!-- Orders Section -->
    <q-card flat bordered class="orders-card q-mt-lg" v-if="localResult.orders && localResult.orders.length > 0">
      <q-card-section class="orders-header">
        <div class="text-h5 text-weight-bold">
          <q-icon name="shopping_bag" color="primary" class="q-mr-sm" />
          Order Items ({{ localResult.orders.length }})
        </div>
        <div class="text-body2 text-grey-7 q-mt-xs">
          Items included in this transaction
        </div>
      </q-card-section>

      <q-separator />

      <q-card-section class="orders-content">
        <div class="orders-list">
          <div v-for="(order, index) in localResult.orders" :key="order.id || index" class="order-item">
            <div class="order-item-header">
              <div class="order-item-number">
                <q-icon name="inventory_2" color="primary" size="sm" class="q-mr-xs" />
                <span class="text-weight-bold">Item {{ index + 1 }}</span>
              </div>
              <a v-if="order.store" :href="`/public_stores/${order.store.optimus_id}`" target="_blank">
                <q-chip color="primary" text-color="white" size="sm" outline>
                  <q-icon name="store" size="xs" class="q-mr-xs" />
                  {{ order.store.name }}
                </q-chip>
              </a>
            </div>

            <div class="order-item-body">
              <a v-if="order.store"  :href="`/public_stores/${order.store.optimus_id}/item/${order.optimus_item}`"
                target="_blank">
              <div class="order-item-info">
                <div class="order-item-name">
                    <q-icon name="label" color="grey-6" size="sm" class="q-mr-xs" />
                    <span class="text-weight-medium text-grey-8">{{ order.item_name }}</span>
                </div>
                <div class="order-item-description" v-if="order.item_description">
                  <p class="text-body2 text-grey-7 q-mt-xs q-mb-none">
                    {{ order.item_description }}
                  </p>
                </div>
              </div>
              </a>

              <div class="order-item-details">
                <div class="order-detail-row">
                  <span class="order-detail-label">
                    <q-icon name="shopping_cart" size="xs" class="q-mr-xs" />
                    Quantity:
                  </span>
                  <span class="order-detail-value text-weight-medium">{{ order.qty }}</span>
                </div>
                <div class="order-detail-row">
                  <span class="order-detail-label">
                    <q-icon name="attach_money" size="xs" class="q-mr-xs" />
                    Unit Price:
                  </span>
                  <span class="order-detail-value">₱{{ formatCurrency(order.online_price) }}</span>
                </div>
                <div class="order-detail-row order-subtotal">
                  <span class="order-detail-label text-weight-bold">
                    <q-icon name="receipt" size="xs" class="q-mr-xs" />
                    Subtotal:
                  </span>
                  <span class="order-detail-value text-weight-bold text-primary">
                    ₱{{ formatCurrency(order.subtotal) }}
                  </span>
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
.transaction-detail-container {
  padding: 24px;
  max-width: 1200px;
  margin: 0 auto;
}

.transaction-header-section {
  padding: 16px 0;
}

.transaction-detail-card {
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.transaction-detail-header {
  background: linear-gradient(135deg, #f5f7fa 0%, #ffffff 100%);
  padding: 24px;
}

.status-select-wrap {
  min-width: 200px;
  max-width: 300px;
}

.status-select {
  width: 100%;
}

.transaction-info-section {
  padding: 24px;
}

.info-group {
  background: #f8f9fa;
  border-radius: 8px;
  padding: 20px;
  height: 100%;
}

.info-list {
  background: transparent;
}

.info-item {
  padding: 12px 0;
  border-bottom: 1px solid rgba(0, 0, 0, 0.05);

  &:last-child {
    border-bottom: none;
  }
}

.info-label {
  font-size: 13px;
  color: #666;
  margin-bottom: 4px;
}

.info-value {
  font-size: 15px;
  color: #1a1a1a;
}

.grand-total {
  background: rgba(25, 118, 210, 0.05);
  border-radius: 8px;
  padding: 16px !important;
  margin-top: 8px;
  border: 2px solid rgba(25, 118, 210, 0.2);

  .info-label,
  .info-value {
    font-size: 18px;
  }
}

@media (max-width: 768px) {
  .transaction-detail-container {
    padding: 16px;
  }

  .transaction-detail-header {
    padding: 16px;
  }

  .transaction-info-section {
    padding: 16px;
  }

  .info-group {
    padding: 16px;
    margin-bottom: 16px;
  }
}

.orders-card {
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.orders-header {
  background: linear-gradient(135deg, #f5f7fa 0%, #ffffff 100%);
  padding: 24px;
}

.orders-content {
  padding: 24px;
}

.orders-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.order-item {
  background: #f8f9fa;
  border-radius: 8px;
  padding: 20px;
  border: 1px solid rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;

  &:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
  }
}

.order-item-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
  padding-bottom: 12px;
  border-bottom: 2px solid rgba(0, 0, 0, 0.1);
}

.order-item-number {
  display: flex;
  align-items: center;
  font-size: 16px;
  color: #1a1a1a;
}

.order-item-body {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.order-item-info {
  flex: 1;
}

.order-item-name {
  display: flex;
  align-items: center;
  font-size: 16px;
  color: #1a1a1a;
  margin-bottom: 8px;
}

.order-item-description {
  padding-left: 24px;
}

.order-item-details {
  display: flex;
  flex-direction: column;
  gap: 8px;
  background: white;
  padding: 16px;
  border-radius: 8px;
  border: 1px solid rgba(0, 0, 0, 0.05);
}

.order-detail-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 8px 0;
  border-bottom: 1px solid rgba(0, 0, 0, 0.05);

  &:last-child {
    border-bottom: none;
  }
}

.order-detail-label {
  display: flex;
  align-items: center;
  font-size: 14px;
  color: #666;
}

.order-detail-value {
  font-size: 15px;
  color: #1a1a1a;
}

.order-subtotal {
  margin-top: 8px;
  padding-top: 12px;
  border-top: 2px solid rgba(25, 118, 210, 0.2);
  background: rgba(25, 118, 210, 0.05);
  border-radius: 6px;
  padding: 12px 16px;

  .order-detail-label,
  .order-detail-value {
    font-size: 16px;
  }
}

@media (max-width: 768px) {
  .orders-header {
    padding: 16px;
  }

  .orders-content {
    padding: 16px;
  }

  .order-item {
    padding: 16px;
  }

  .order-item-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
  }

  .order-item-body {
    gap: 12px;
  }

  .order-item-details {
    padding: 12px;
  }
}
</style>
