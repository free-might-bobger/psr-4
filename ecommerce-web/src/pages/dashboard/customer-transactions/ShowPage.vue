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
        <q-btn to="/dashboard/customer-transactions" outline color="primary" icon="arrow_back" label="Back" />
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
          <div class="col-auto">
            <q-badge v-if="localResult.status" :color="getStatusColor(localResult.status.label)"
              :label="localResult.status.label" class="status-badge-large" />
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
  </div>
</template>

<script setup lang="ts">
import { show } from 'src/boot/axios-call';
import { onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';

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
  created_at: ''
});

onMounted(async () => {
  const result = await show<TransactionDetail>({
    message: 'Getting transaction...',
    entity: 'customer-transactions',
    optimus_id: Number(route.params.id),
    query: {
      with: 'paymentMethod,receiveMethod,status',
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

.status-badge-large {
  font-size: 14px;
  padding: 8px 16px;
  border-radius: 20px;
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
</style>
