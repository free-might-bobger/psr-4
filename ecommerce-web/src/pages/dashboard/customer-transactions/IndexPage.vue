<template>
  <div class="transactions-container">
    <!-- Header Section -->
    <div class="transactions-header q-mb-lg">
      <div class="row items-center q-mb-md">
        <div class="col">
          <div class="text-h4 text-weight-bold">
            <q-icon name="receipt_long" class="q-mr-sm" />
            My Transactions
          </div>
          <div class="text-body2 text-grey-7 q-mt-xs">
            View and manage your order history
          </div>
        </div>
      </div>
    </div>

    <!-- Transactions Grid -->
    <q-table grid :rows="result" :columns="customerTransactionsColumns as any" row-key="optimus_id" hide-header
      :visible-columns="[]" class="transactions-table" hide-bottom :loading="false">
      <template v-slot:item="props">
        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 q-pa-sm">
          <q-card flat bordered class="transaction-card" :class="{ 'transaction-card-selected': props.selected }">
            <!-- Status Badge -->
            <q-card-section class="transaction-header q-pb-sm">
              <div class="flex justify-between items-center">
                <q-badge :color="getStatusColor(props.row.status?.label)" :label="props.row.status?.label"
                  class="status-badge" />
                <div class="text-caption text-grey-6">
                  <q-icon name="calendar_today" size="xs" class="q-mr-xs" />
                  {{ formatDate(props.row.created_at) }}
                </div>
              </div>
            </q-card-section>

            <q-separator />

            <!-- Transaction Details -->
            <q-card-section class="transaction-details">
              <q-list dense class="transaction-list">
                <q-item v-for="(col, k) in props.cols" :key="k" class="transaction-item">
                  <q-item-section>
                    <q-item-label class="transaction-label">
                      <q-icon :name="getColumnIcon(col.label)" size="xs" class="q-mr-xs" />
                      {{ col.label }}
                    </q-item-label>
                  </q-item-section>
                  <q-item-section side>
                    <q-item-label class="transaction-value">
                      {{ col.value }}
                    </q-item-label>
                  </q-item-section>
                </q-item>
              </q-list>
            </q-card-section>

            <q-separator />

            <!-- Action Buttons -->
            <q-card-section class="transaction-actions q-pt-md">
              <div class="row q-gutter-sm">
                <q-btn :to="`${$route.path}/${props.row.optimus_id}`" outline color="negative" icon="check_circle"
                  label="Received" class="col" size="sm" />
                <q-btn :to="`${$route.path}/${props.row.optimus_id}`" color="primary" icon="visibility" label="View"
                  class="col" size="sm" unelevated />
              </div>
            </q-card-section>
          </q-card>
        </div>
      </template>

      <!-- Empty State -->
      <template v-slot:no-data>
        <div class="empty-state">
          <q-icon name="receipt_long" size="64px" color="grey-4" />
          <div class="text-h6 text-grey-6 q-mt-md">No transactions found</div>
          <div class="text-body2 text-grey-5 q-mt-sm">
            Your transaction history will appear here
          </div>
        </div>
      </template>
    </q-table>

    <!-- Pagination -->
    <div class="pagination-container q-mt-lg">
      <q-pagination v-model="pagination.page" color="primary" :max="pagination.lastPage" boundary-links direction-links
        :max-pages="7" class="transactions-pagination" />
    </div>
  </div>
</template>
<script setup lang="ts">
import { onRequest } from 'boot/axios-call';
import { useCommonStore } from 'src/stores/common';
import { onMounted, ref, watch } from 'vue';
import { storeToRefs } from 'pinia';
import { customerTransactionsColumns } from 'boot/columns';

const search = ref('');
const useCommon = useCommonStore();
const { entityQuery, pagination, result } = storeToRefs(useCommon);

entityQuery.value = {
  message: 'Getting transactions...',
  entity: 'customer-transactions',
  query: {
    with: 'status,paymentMethod,receiveMethod',
    orderBy: 'created_at:desc',
    page: pagination.value.page,
    limit: 10,
  },
};

onMounted(() => {
  entityQuery.value.query.page = 1;
  onRequest(entityQuery.value, true);
});

watch(search, (newValue) => {
  if (!newValue) {
    delete entityQuery.value.query.filters;
    onRequest(entityQuery.value);
    return;
  }
  entityQuery.value.query.filters = 'name:' + search.value;
  onRequest(entityQuery.value);
});

watch(() => pagination.value.page, (newPage) => {
  entityQuery.value.query.page = newPage;
  onRequest(entityQuery.value);
});

// Helper functions for UI
const getStatusColor = (status: string | undefined): string => {
  if (!status) return 'grey';
  const statusLower = status.toLowerCase();
  if (statusLower.includes('completed') || statusLower.includes('delivered')) return 'positive';
  if (statusLower.includes('preparing') || statusLower.includes('processing')) return 'warning';
  if (statusLower.includes('cancelled') || statusLower.includes('rejected')) return 'negative';
  return 'primary';
};

const getColumnIcon = (label: string): string => {
  const labelLower = label.toLowerCase();
  if (labelLower.includes('reference') || labelLower.includes('id')) return 'tag';
  if (labelLower.includes('total') || labelLower.includes('amount') || labelLower.includes('price')) return 'attach_money';
  if (labelLower.includes('payment')) return 'payment';
  if (labelLower.includes('delivery') || labelLower.includes('receive')) return 'local_shipping';
  if (labelLower.includes('date') || labelLower.includes('time')) return 'schedule';
  return 'info';
};

const formatDate = (dateString: string | undefined): string => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  });
};
</script>

<style scoped lang="scss">
.transactions-container {
  padding: 16px;
  max-width: 1400px;
  margin: 0 auto;
}

.transactions-header {
  padding: 16px 0;
}

.transactions-table {
  margin: 0;
}

.transaction-card {
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
  height: 100%;
  display: flex;
  flex-direction: column;

  &:hover {
    transform: translateY(-4px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
  }
}

.transaction-card-selected {
  background-color: #f5f5f5;
  border: 2px solid var(--q-primary);
}

.transaction-header {
  background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
  border-radius: 12px 12px 0 0;
}

.status-badge {
  font-size: 12px;
  font-weight: 600;
  padding: 6px 12px;
  border-radius: 16px;
}

.transaction-details {
  flex: 1;
  padding: 16px;
}

.transaction-list {
  .transaction-item {
    padding: 8px 0;
    min-height: auto;
  }
}

.transaction-label {
  font-size: 12px;
  color: #666;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.transaction-value {
  font-size: 14px;
  color: #1a1a1a;
  font-weight: 600;
}

.transaction-actions {
  padding: 16px;
  background: #fafafa;
  border-radius: 0 0 12px 12px;
}

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 64px 16px;
  text-align: center;
}

.pagination-container {
  display: flex;
  justify-content: center;
  padding: 24px 0;
}

.transactions-pagination {
  :deep(.q-btn) {
    border-radius: 8px;
    margin: 0 2px;
  }
}

@media (max-width: 600px) {
  .transactions-container {
    padding: 8px;
  }

  .transaction-card {
    margin-bottom: 16px;
  }

  .transaction-actions {
    .row {
      flex-direction: column;
    }

    .q-btn {
      width: 100%;
      margin-bottom: 8px;
    }
  }
}
</style>
