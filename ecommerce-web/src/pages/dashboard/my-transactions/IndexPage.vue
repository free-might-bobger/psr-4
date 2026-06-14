<template>
  <div class="stores-page-container">
    <!-- Header Section -->
    <div class="page-header q-mb-md">
      <div class="header-content">
        <div class="header-title-section">
          <q-icon name="receipt_long" size="32px" color="primary" class="q-mr-sm" />
          <h2 class="page-title">My Transactions</h2>
        </div>
        <div class="header-actions">
          <q-input v-model="search" placeholder="Search transactions..." outlined dense clearable debounce="300"
            class="search-input">
            <template v-slot:prepend>
              <q-icon name="search" />
            </template>
          </q-input>
        </div>
      </div>
    </div>

    <!-- Desktop Table View -->
    <div class="desktop-only">
      <div v-if="typedResult.length === 0" class="empty-state-desktop">
        <q-icon name="receipt_long" size="80px" color="grey-4" />
        <div class="text-h5 q-mt-md text-grey-6">No transactions found</div>
        <div class="text-body2 text-grey-5 q-mt-sm">Your transaction history will appear here</div>
      </div>
      <q-table v-else flat bordered :rows="typedResult" :columns="columns" row-key="optimus_id"
        class="transactions-table" :rows-per-page-options="[0]" hide-pagination>
        <template v-slot:body-cell-reference="props">
          <q-td :props="props">
            <router-link :to="`${$route.path}/${props.row.optimus_id}`" class="transaction-reference-link">
              <div class="transaction-reference-id">{{ props.row.reference_id }}</div>
              <div class="transaction-date">
                <q-icon name="calendar_today" size="xs" class="q-mr-xs" />
                {{ formatDate(props.row.created_at) }}
              </div>
            </router-link>
          </q-td>
        </template>

        <template v-slot:body-cell-status="props">
          <q-td :props="props">
            <q-badge :color="getStatusColor(props.row.status?.label)" :label="props.row.status?.label || 'Pending'"
              class="status-badge" />
          </q-td>
        </template>

        <template v-slot:body-cell-summary="props">
          <q-td :props="props">
            <div class="transaction-summary">
              <div class="transaction-total">
                Grand Total: <span>{{ props.row.grand_total }}</span>
              </div>
              <div class="transaction-meta">
                <div class="transaction-meta-item">
                  <q-icon name="payment" size="xs" class="q-mr-xs" />
                  {{ props.row.payment_method?.name || 'N/A' }}
                </div>
                <div class="transaction-meta-item">
                  <q-icon name="local_shipping" size="xs" class="q-mr-xs" />
                  {{ props.row.receive_method?.name || 'N/A' }}
                </div>
              </div>
            </div>
          </q-td>
        </template>

        <template v-slot:body-cell-actions="props">
          <q-td :props="props">
            <div class="action-buttons">
              <q-btn v-if="props.row.status?.name !== TRANSACTION_STATUS.COMPLETED" unelevated dense color="secondary"
                icon="inventory_2" @click="markedAsReceived(props.row.optimus_id)" size="md" class="q-mr-xs">
                <q-tooltip>Mark as received</q-tooltip>
              </q-btn>
              <q-btn unelevated dense color="primary" icon="info" :to="`${$route.path}/${props.row.optimus_id}`"
                size="md" class="q-mr-xs">
                <q-tooltip>View details</q-tooltip>
              </q-btn>
              <q-btn v-if="props.row.status" unelevated dense color="negative" icon="currency_exchange"
                :to="`${$route.path}/${props.row.optimus_id}`" size="md">
                <q-tooltip>Request refund</q-tooltip>
              </q-btn>
            </div>
          </q-td>
        </template>

        <template v-slot:bottom>
          <div class="table-pagination">
            <div class="pagination-info">
              Showing {{ pagination.from }} - {{ pagination.to }} of {{ pagination.rowsNumber }} transactions
            </div>
            <div class="pagination-controls">
              <q-btn v-if="pagination.lastPage > 2" flat round dense icon="first_page" color="grey-8"
                :disable="pagination.page === 1" @click="goToFirstPage" />
              <q-btn flat round dense icon="chevron_left" color="grey-8" :disable="pagination.page === 1"
                @click="goToPreviousPage" />
              <span class="page-number">{{ pagination.page }} / {{ pagination.lastPage }}</span>
              <q-btn flat round dense icon="chevron_right" color="grey-8"
                :disable="pagination.page === pagination.lastPage" @click="goToNextPage" />
              <q-btn v-if="pagination.lastPage > 2" flat round dense icon="last_page" color="grey-8"
                :disable="pagination.page === pagination.lastPage" @click="goToLastPage" />
            </div>
          </div>
        </template>
      </q-table>
    </div>

    <!-- Mobile Card View -->
    <div class="mobile-only">
      <div v-if="typedResult.length === 0" class="empty-state">
        <q-icon name="receipt_long" size="64px" color="grey-4" />
        <div class="text-h6 q-mt-md text-grey-6">No transactions found</div>
        <div class="text-body2 text-grey-5 q-mt-sm">Your transaction history will appear here</div>
      </div>
      <div v-else class="stores-cards">
        <q-card v-for="transaction in typedResult" :key="transaction.optimus_id" flat bordered
          class="store-card q-mb-md">
          <q-card-section>
            <div class="store-card-header transaction-store-card-header">
              <div class="store-card-title">
                <div class="transaction-reference-id">#{{ transaction.reference_id }}</div>
              </div>
              <div class="transaction-header-badge">
                <q-badge :color="getStatusColor(transaction.status?.label)"
                  :label="transaction.status?.label || 'Pending'" class="status-badge" />
              </div>
            </div>
            <div class="store-card-info">
              <span class="text-body2 text-grey-7">{{ formatDate(transaction.created_at) }}</span>
            </div>
            <div class="transaction-mobile-details">
              <div class="transaction-detail-row">
                <span class="transaction-detail-label">Grand Total</span>
                <span class="transaction-detail-value">{{ transaction.grand_total }}</span>
              </div>
              <div class="transaction-detail-row">
                <span class="transaction-detail-label">Payment</span>
                <span class="transaction-detail-value">{{ transaction.payment_method?.name || 'N/A' }}</span>
              </div>
              <div class="transaction-detail-row">
                <span class="transaction-detail-label">Receiving</span>
                <span class="transaction-detail-value">{{ transaction.receive_method?.name || 'N/A' }}</span>
              </div>
            </div>
            <div class="store-card-actions q-mt-md">
              <q-btn unelevated dense color="negative" icon="check_circle" label="Received"
                :to="`${$route.path}/${transaction.optimus_id}`" class="action-btn-mobile action-btn-delete-mobile" />
              <q-btn unelevated dense color="primary" icon="visibility" label="View"
                :to="`${$route.path}/${transaction.optimus_id}`" class="action-btn-mobile action-btn-edit-mobile" />
            </div>
          </q-card-section>
        </q-card>
      </div>
      <div v-if="typedResult.length > 0" class="mobile-pagination q-mt-md">
        <q-pagination v-model="pagination.page" :max="pagination.lastPage" :max-pages="5" direction-links boundary-links
          color="primary" @update:model-value="handlePageChange" />
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
import { onRequest, firstPage, previousPage, nextPage, lastPage, update } from 'boot/axios-call';
import { useCommonStore } from 'src/stores/common';
import { onMounted, ref, watch } from 'vue';
import { storeToRefs } from 'pinia';
import { CustomerTransactionRow } from 'src/boot/interfaces';
import { TRANSACTION_STATUS } from 'src/boot/constant';
import { Notify } from 'quasar';

const search = ref('');
const useCommon = useCommonStore();
const { entityQuery, pagination, result } = storeToRefs(useCommon);

entityQuery.value = {
  message: 'Getting transactions...',
  entity: 'my-transactions',
  query: {
    with: 'status,paymentMethod,receiveMethod',
    orderBy: 'created_at:desc',
    page: pagination.value.page,
    limit: 12,
  },
};

const typedResult = result as unknown as CustomerTransactionRow[];

const columns = [
  {
    name: 'reference',
    required: true,
    label: 'Reference',
    align: 'left',
    field: 'reference_id',
    sortable: true
  },
  {
    name: 'status',
    required: true,
    label: 'Order Status',
    align: 'left',
    field: (row: CustomerTransactionRow) => row.status?.label || 'Pending',
    sortable: true
  },
  {
    name: 'summary',
    required: true,
    label: 'Summary',
    align: 'left',
    field: 'grand_total'
  },
  {
    name: 'actions',
    required: true,
    label: 'Actions',
    align: 'center',
    field: ''
  }
];

const handlePageChange = (page: number) => {
  entityQuery.value.query.page = page;
  onRequest(entityQuery.value);
};

onMounted(() => {
  entityQuery.value.query.page = 1;
  onRequest(entityQuery.value, true);
});

watch(search, (newValue) => {
  if (newValue) {
    entityQuery.value.query.filters = 'reference_id:' + search.value;
  } else {
    delete entityQuery.value.query.filters;
  }
  entityQuery.value.query.page = 1;
  onRequest(entityQuery.value);
});

watch(() => pagination.value.page, (newPage) => {
  entityQuery.value.query.page = newPage;
  onRequest(entityQuery.value);
});

const goToFirstPage = () => {
  firstPage(entityQuery.value);
};

const goToPreviousPage = () => {
  previousPage(entityQuery.value);
};

const goToNextPage = () => {
  nextPage(entityQuery.value);
};

const goToLastPage = () => {
  lastPage(entityQuery.value, pagination.value);
};

// Helper functions for UI
const getStatusColor = (status: string | undefined): string => {
  if (!status) return 'grey';
  const statusLower = status.toLowerCase();
  if (statusLower.includes('completed') || statusLower.includes('delivered')) return 'positive';
  if (statusLower.includes('preparing') || statusLower.includes('processing')) return 'warning';
  if (statusLower.includes('cancelled') || statusLower.includes('rejected')) return 'negative';
  return 'primary';
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

const markedAsReceived = async (transactionId: string) => {
  await update(
    {
      entity: 'my-transactions-marked-as-received',
      data: {},
      optimus_id: transactionId,
    },
    true,
    true
  );

  onRequest(entityQuery.value, true);
};
</script>

<style scoped lang="scss">
@import 'src/css/dashboard/all-stores/index.scss';

.transactions-table {
  width: 100%;

  :deep(.q-table) {
    border-radius: 12px;
    overflow: hidden;
  }

  :deep(.q-table th) {
    font-weight: 600;
    font-size: 14px;
    padding: 16px 24px;
    background: linear-gradient(135deg, #f5f7fa 0%, #ffffff 100%);
    color: #1a1a1a;
  }

  :deep(.q-table td) {
    padding: 20px 24px;
    font-size: 15px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.06);
  }

  :deep(.q-table tbody tr) {
    transition: all 0.3s ease;

    &:hover {
      background: rgba(25, 118, 210, 0.04);
    }
  }

  :deep(.q-table tbody tr:last-child td) {
    border-bottom: none;
  }
}

.transaction-reference-link {
  text-decoration: none;
  color: inherit;
  transition: all 0.3s ease;

  &:hover {
    color: #1976d2;
  }
}

.transaction-reference-id {
  font-size: 15px;
  font-weight: 600;
  color: #1a1a1a;
}

.transaction-date {
  font-size: 13px;
  color: #666;
  display: flex;
  align-items: center;
  margin-top: 4px;
}

.status-badge {
  font-size: 13px;
  font-weight: 600;
  padding: 6px 12px;
  border-radius: 12px;
}

.transaction-summary {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.transaction-total {
  font-size: 15px;
  color: #1a1a1a;
  font-weight: 600;

  span {
    font-weight: 700;
  }
}

.transaction-meta {
  display: flex;
  flex-direction: column;
  gap: 6px;
  color: #666;
  font-size: 13px;
}

.transaction-meta-item {
  display: flex;
  align-items: center;
}

.action-buttons {
  display: flex;
  gap: 8px;
  justify-content: center;
}

.table-pagination {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 16px;
}

.pagination-info {
  font-size: 13px;
  color: #666;
}

.pagination-controls {
  display: flex;
  align-items: center;
  gap: 8px;
}

.page-number {
  font-size: 13px;
  color: #1a1a1a;
  font-weight: 600;
  min-width: 50px;
  text-align: center;
}

/* Mobile card styles */
.store-card-header.transaction-store-card-header {
  flex-wrap: wrap;
  align-items: flex-start;
  gap: 8px 12px;
}

.store-card-header.transaction-store-card-header .store-card-title {
  flex: 1 1 auto;
  min-width: 0;
  max-width: 100%;
}

.store-card-header.transaction-store-card-header .transaction-reference-id {
  word-break: break-word;
}

.store-card-header.transaction-store-card-header .transaction-header-badge {
  flex: 0 1 auto;
  min-width: 0;
  max-width: 100%;
  display: flex;
  justify-content: flex-end;
}

.store-card-header.transaction-store-card-header .transaction-header-badge :deep(.q-badge) {
  white-space: normal;
  text-align: center;
  word-break: break-word;
  max-width: 100%;
}

.transaction-mobile-details {
  margin-top: 12px;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.transaction-detail-row {
  display: flex;
  justify-content: space-between;
  font-size: 14px;
  color: #1a1a1a;
}

.transaction-detail-label {
  color: #666;
  font-weight: 500;
}

.transaction-detail-value {
  font-weight: 600;
}
</style>
