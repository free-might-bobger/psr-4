<template>
  <div class="stores-page-container">
    <!-- Header Section -->
    <div class="page-header q-mb-lg">
      <div class="header-bg-accent"></div>
      <div class="header-content">
        <div class="header-title-section">
          <div class="header-icon-wrap">
            <q-icon name="receipt_long" size="26px" color="white" />
          </div>
          <div>
            <h2 class="page-title">My Transactions</h2>
            <div class="page-subtitle">Track and manage your orders</div>
          </div>
        </div>
        <div class="header-actions">
          <q-input v-model="search" placeholder="Search transactions..." outlined dense clearable debounce="1000"
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
        <div class="empty-icon-wrap">
          <q-icon name="receipt_long" size="48px" color="white" />
        </div>
        <div class="empty-title q-mt-md">No transactions found</div>
        <div class="empty-subtitle q-mt-sm">Your transaction history will appear here</div>
      </div>
      <q-table v-else flat :rows="typedResult" :columns="columns" row-key="optimus_id" class="transactions-table"
        :rows-per-page-options="[0]" hide-pagination>
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
        <div class="empty-icon-wrap">
          <q-icon name="receipt_long" size="40px" color="white" />
        </div>
        <div class="empty-title q-mt-md">No transactions found</div>
        <div class="empty-subtitle q-mt-sm">Your transaction history will appear here</div>
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
              <q-icon name="calendar_today" size="13px" class="q-mr-xs" />
              <span class="card-date">{{ formatDate(transaction.created_at) }}</span>
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
import { computed, onMounted, ref, watch } from 'vue';
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

const typedResult = computed<CustomerTransactionRow[]>(() => {
  const data = result.value;
  if (!Array.isArray(data)) return [];
  return data.filter((item): item is CustomerTransactionRow => item != null);
});

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
    align: 'right',
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

// ── Dark theme token overrides ──────────────────────────────────────────────
$dark-base: #0f172a;
$dark-card: #1e293b;
$dark-elevated: #273549;
$border: rgba(255, 255, 255, 0.08);
$accent: #6366f1;
$accent-2: #7c3aed;
$white: #ffffff;
$muted: rgba(255, 255, 255, 0.5);

// ── Layout overrides ───────────────────────────────────────────────────────
.stores-page-container {
  padding: 28px 24px;
  max-width: 1400px;
  margin: 0 auto;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  background: #0f172a !important;
  color: #ffffff !important;
}

// ── Header ──────────────────────────────────────────────────────────────────
.page-header {
  position: relative;
  background: $dark-card !important;
  border-radius: 20px !important;
  padding: 28px 32px !important;
  border: 1px solid $border !important;
  box-shadow: 0 8px 40px rgba(0, 0, 0, 0.3) !important;
  overflow: hidden;
}

.header-bg-accent {
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, rgba(99, 102, 241, 0.18) 0%, rgba(124, 58, 237, 0.10) 60%, transparent 100%);
  pointer-events: none;
}

.header-content {
  position: relative;
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 16px;
}

.header-title-section {
  display: flex;
  align-items: center;
  gap: 16px;
}

.header-icon-wrap {
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

.page-title {
  font-size: 24px !important;
  font-weight: 800 !important;
  color: $white !important;
  margin: 0 0 4px !important;
  letter-spacing: -0.3px;
  line-height: 1.2;
}

.page-subtitle {
  font-size: 13px;
  color: $muted;
  font-weight: 500;
}

.header-actions {
  display: flex;
  gap: 12px;
  align-items: center;
}

.search-input {
  min-width: 280px;
  max-width: 380px;

  :deep(.q-field__control) {
    background: $dark-elevated;
    border-radius: 12px;
    border-color: $border;
  }

  :deep(.q-field__native),
  :deep(.q-field__input) {
    color: $white;
  }

  :deep(.q-field__label),
  :deep(.q-field__prepend .q-icon) {
    color: $muted;
  }
}

// ── Desktop table ──────────────────────────────────────────────────────────
.empty-state-desktop {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 80px 20px;
  text-align: center;
  background: $dark-card !important;
  border-radius: 20px !important;
  border: 1px solid $border !important;
}

.empty-icon-wrap {
  width: 80px;
  height: 80px;
  border-radius: 24px;
  background: linear-gradient(135deg, $accent 0%, $accent-2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 8px 24px rgba(99, 102, 241, 0.35);
}

.empty-title {
  font-size: 20px;
  font-weight: 700;
  color: $white;
}

.empty-subtitle {
  font-size: 14px;
  color: $muted;
}

.transactions-table {
  width: 100%;
  border-radius: 20px;
  overflow: hidden;

  :deep(.q-table__container) {
    background: $dark-card;
    border: 1px solid $border;
    border-radius: 20px;
  }

  :deep(.q-table th) {
    font-weight: 700;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    padding: 16px 24px;
    background: $dark-elevated;
    color: rgba(255, 255, 255, 0.6);
    border-bottom: 1px solid $border;
  }

  :deep(.q-table td) {
    padding: 20px 24px;
    font-size: 14px;
    color: $white;
    border-bottom: 1px solid $border;
    background: transparent;
  }

  :deep(.q-table tbody tr) {
    transition: background 0.2s ease;

    &:hover td {
      background: rgba(255, 255, 255, 0.04);
    }
  }

  :deep(.q-table tbody tr:last-child td) {
    border-bottom: none;
  }

  :deep(.q-table__bottom) {
    background: $dark-elevated;
    border-top: 1px solid $border;
    color: $white;
  }
}

.transaction-reference-link {
  text-decoration: none;
  color: inherit;
  transition: color 0.2s ease;

  &:hover {
    color: #a5b4fc;
  }
}

.transaction-reference-id {
  font-size: 15px;
  font-weight: 700;
  color: $white;
}

.transaction-date {
  font-size: 12px;
  color: $muted;
  display: flex;
  align-items: center;
  margin-top: 4px;
  gap: 4px;
}

.status-badge {
  font-size: 12px;
  font-weight: 700;
  padding: 5px 12px;
  border-radius: 20px;
  letter-spacing: 0.3px;
}

.transaction-summary {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.transaction-total {
  font-size: 14px;
  color: $white;
  font-weight: 600;

  span {
    font-weight: 800;
    color: #a5b4fc;
  }
}

.transaction-meta {
  display: flex;
  flex-direction: column;
  gap: 4px;
  color: $muted;
  font-size: 12px;
}

.transaction-meta-item {
  display: flex;
  align-items: center;
  gap: 4px;
}

.action-buttons {
  display: flex;
  gap: 8px;
  justify-content: flex-end;
}

.table-pagination {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 14px 20px;
  color: $white;
}

.pagination-info {
  font-size: 13px;
  color: $muted;
}

.pagination-controls {
  display: flex;
  align-items: center;
  gap: 8px;

  :deep(.q-btn .q-icon) {
    color: rgba(255, 255, 255, 0.7);
  }
}

.page-number {
  font-size: 13px;
  color: $white;
  font-weight: 600;
  min-width: 60px;
  text-align: center;
}

// ── Mobile cards ───────────────────────────────────────────────────────────
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px 20px;
  text-align: center;
}

.stores-cards {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.store-card {
  background: $dark-card !important;
  border: 1px solid $border !important;
  border-radius: 16px !important;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
  transition: all 0.2s ease;
  overflow: hidden;
  position: relative;

  &::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 3px;
    height: 100%;
    background: linear-gradient(180deg, $accent 0%, $accent-2 100%);
  }

  &:hover {
    border-color: rgba(99, 102, 241, 0.3) !important;
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
  }
}

.store-card-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 12px;
}

.store-card-header.transaction-store-card-header {
  flex-wrap: wrap;
  align-items: flex-start;
  gap: 8px 12px;
}

.store-card-title {
  flex: 1 1 auto;
  min-width: 0;
}

.transaction-header-badge {
  flex: 0 1 auto;
}

.transaction-reference-id {
  font-size: 15px;
  font-weight: 700;
  color: $white;
  word-break: break-word;
}

.store-card-info {
  display: flex;
  align-items: center;
  gap: 4px;
  padding: 0 !important;
  margin-bottom: 14px;
  background: transparent !important;
}

.card-date {
  font-size: 12px;
  color: $muted;
}

.transaction-mobile-details {
  background: $dark-elevated !important;
  border-radius: 12px !important;
  padding: 14px 16px;
  display: flex;
  flex-direction: column;
  gap: 10px;
  margin-top: 4px;
}

.transaction-detail-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 13px;
}

.transaction-detail-label {
  color: $muted !important;
  font-weight: 500;
}

.transaction-detail-value {
  font-weight: 700;
  color: $white !important;
}

.store-card-actions {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;
  margin-top: 14px;
}

.action-btn-mobile {
  border-radius: 10px;
  font-weight: 700;
  font-size: 13px;
  height: 40px;
  text-transform: none;
  letter-spacing: 0;
}

.mobile-pagination {
  display: flex;
  justify-content: center;
  padding: 20px 0 4px;
  background: transparent !important;
}

// ── Responsive ────────────────────────────────────────────────────────────
@media (max-width: 768px) {
  .stores-page-container {
    padding: 16px 12px;
  }

  .page-header {
    padding: 20px;
    border-radius: 16px;
  }

  .header-content {
    flex-direction: column;
    align-items: stretch;
  }

  .search-input {
    min-width: 0;
    max-width: 100%;
    width: 100%;
  }
}

@media (max-width: 600px) {
  .stores-page-container {
    padding: 12px 8px;
  }

  .page-title {
    font-size: 20px;
  }
}
</style>

<style>
.stores-page-container .page-header {
  background: #1e293b !important;
}

.stores-page-container .page-title {
  color: #ffffff !important;
}

/* Table container — styled like ProfilePage dark card */
.stores-page-container .transactions-table .q-table__container {
  background: #1e293b !important;
  border: 1px solid rgba(255, 255, 255, 0.08) !important;
  border-radius: 20px !important;
  box-shadow: 0 8px 40px rgba(0, 0, 0, 0.25) !important;
  overflow: hidden !important;
}

/* Strip ALL Quasar table borders */
.stores-page-container .transactions-table .q-table,
.stores-page-container .transactions-table .q-table td,
.stores-page-container .transactions-table .q-table th,
.stores-page-container .transactions-table .q-table tr {
  border: none !important;
  outline: none !important;
}

/* Header row */
.stores-page-container .transactions-table thead tr th {
  background: #273549 !important;
  color: rgba(255, 255, 255, 0.55) !important;
  border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
}

/* Body rows — no hover */
.stores-page-container .transactions-table tbody tr,
.stores-page-container .transactions-table tbody tr:hover {
  background: #1e293b !important;
  cursor: default;
}

.stores-page-container .transactions-table tbody tr td {
  background: transparent !important;
  color: #ffffff !important;
  border-bottom: 1px solid rgba(255, 255, 255, 0.05) !important;
}

.stores-page-container .transactions-table tbody tr:last-child td {
  border-bottom: none !important;
}

/* Pagination footer */
.stores-page-container .transactions-table .q-table__bottom {
  background: #273549 !important;
  color: #ffffff !important;
  border-top: 1px solid rgba(255, 255, 255, 0.08) !important;
}

/* Mobile cards */
.stores-page-container .store-card {
  background: #1e293b !important;
  border-color: rgba(255, 255, 255, 0.08) !important;
}

.stores-page-container .store-card .q-card__section {
  background: transparent !important;
  color: #ffffff !important;
}

/* Empty states */
.stores-page-container .empty-state-desktop,
.stores-page-container .empty-state {
  background: #1e293b !important;
}
</style>
