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
            <h2 class="page-title">All Transactions</h2>
            <div class="page-subtitle">Store transaction history</div>
          </div>
        </div>
        <div class="header-actions">
          <q-input v-model="search" placeholder="Search transactions..." outlined dense clearable debounce="1000"
            class="search-input">
            <template v-slot:prepend>
              <q-icon name="search" color="grey-5" />
            </template>
          </q-input>
        </div>
      </div>
    </div>

    <!-- Desktop Grid View -->
    <div class="desktop-only">
      <div v-if="typedResult.length === 0" class="empty-state-desktop">
        <div class="empty-icon-wrap">
          <q-icon name="receipt_long" size="48px" color="white" />
        </div>
        <div class="empty-title q-mt-md">No transactions found</div>
        <div class="empty-subtitle q-mt-sm">Your transaction history will appear here</div>
      </div>
      <div v-else>
        <!-- Grid Header -->
        <div class="grid-header transactions-grid-header">
          <div class="grid-header-cell">Reference</div>
          <div class="grid-header-cell">Status</div>
          <div class="grid-header-cell">Summary</div>
          <div class="grid-header-cell" style="text-align:right">Actions</div>
        </div>

        <!-- Grid Rows -->
        <div class="stores-grid">
          <div v-for="transaction in typedResult" :key="transaction.optimus_id" class="store-grid-item">
            <div class="grid-row transaction-grid-row">
              <div class="grid-cell cell-name">
                <router-link :to="`${$route.path}/${transaction.optimus_id}`" class="transaction-reference">
                  <div class="ref-icon-wrap">
                    <q-icon name="receipt" size="16px" color="white" />
                  </div>
                  <div class="transaction-reference-text">
                    <div class="transaction-reference-id">{{ transaction.reference_id }}</div>
                    <div class="transaction-date">
                      <q-icon name="calendar_today" size="xs" class="q-mr-xs" />
                      {{ formatDate(transaction.created_at) }}
                    </div>
                  </div>
                </router-link>
              </div>
              <div class="grid-cell cell-status">
                <q-badge :color="getStatusColor(transaction.status?.label)"
                  :label="transaction.status?.label || 'Pending'" class="status-badge" />
              </div>
              <div class="grid-cell cell-mobile">
                <div class="transaction-summary">
                  <div class="transaction-total">
                    Grand Total: <span>{{ transaction.grand_total }}</span>
                  </div>
                  <div class="transaction-meta">
                    <div class="transaction-meta-item">
                      <q-icon name="payment" size="xs" class="q-mr-xs" />
                      {{ transaction.payment_method?.name || 'N/A' }}
                    </div>
                    <div class="transaction-meta-item">
                      <q-icon name="local_shipping" size="xs" class="q-mr-xs" />
                      {{ transaction.receive_method?.name || 'N/A' }}
                    </div>
                  </div>
                </div>
              </div>
              <div class="grid-cell cell-actions">
                <div class="action-buttons">
                  <q-btn unelevated dense icon="view_list" :to="`${$route.path}/${transaction.optimus_id}`"
                    class="view-btn">
                    <q-tooltip>View details</q-tooltip>
                  </q-btn>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div class="grid-pagination">
          <div class="pagination-info">
            Showing {{ pagination.from }} – {{ pagination.to }} of {{ pagination.rowsNumber }} transactions
          </div>
          <div class="pagination-controls">
            <q-btn v-if="pagination.lastPage > 2" flat round dense icon="first_page" :disable="pagination.page === 1"
              @click="goToFirstPage" />
            <q-btn flat round dense icon="chevron_left" :disable="pagination.page === 1" @click="goToPreviousPage" />
            <span class="page-number">{{ pagination.page }} / {{ pagination.lastPage }}</span>
            <q-btn flat round dense icon="chevron_right" :disable="pagination.page === pagination.lastPage"
              @click="goToNextPage" />
            <q-btn v-if="pagination.lastPage > 2" flat round dense icon="last_page"
              :disable="pagination.page === pagination.lastPage" @click="goToLastPage" />
          </div>
        </div>
      </div>
    </div>

    <!-- Mobile Card View -->
    <div class="mobile-only">
      <div v-if="typedResult.length === 0" class="empty-state">
        <div class="empty-icon-wrap">
          <q-icon name="receipt_long" size="40px" color="white" />
        </div>
        <div class="empty-title q-mt-md">No transactions found</div>
        <div class="empty-subtitle q-mt-xs">Your transaction history will appear here</div>
      </div>
      <div v-else class="stores-cards">
        <div v-for="transaction in typedResult" :key="transaction.optimus_id" class="store-card q-mb-sm">
          <div class="mobile-card-accent"></div>
          <div class="mobile-card-body">
            <div class="mobile-card-top">
              <div class="mobile-ref-wrap">
                <div class="mobile-ref-icon">
                  <q-icon name="receipt" size="14px" color="white" />
                </div>
                <div>
                  <div class="transaction-reference-id">{{ transaction.reference_id }}</div>
                  <div class="transaction-date">
                    <q-icon name="calendar_today" size="xs" class="q-mr-xs" />
                    {{ formatDate(transaction.created_at) }}
                  </div>
                </div>
              </div>
              <q-badge :color="getStatusColor(transaction.status?.label)"
                :label="transaction.status?.label || 'Pending'" class="status-badge" />
            </div>
            <div class="mobile-divider"></div>
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
            <div class="mobile-card-actions">
              <q-btn unelevated dense icon="view_list" label="View Details"
                :to="`${$route.path}/${transaction.optimus_id}`" class="mobile-view-btn" />
            </div>
          </div>
        </div>
      </div>
      <div v-if="typedResult.length > 0" class="mobile-pagination q-mt-md">
        <q-pagination v-model="pagination.page" :max="pagination.lastPage" :max-pages="5" direction-links boundary-links
          color="primary" @update:model-value="handlePageChange" />
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
import { onRequest, firstPage, previousPage, nextPage, lastPage } from 'src/boot/axios-call';
import { useCommonStore } from 'src/stores/common';
import { onMounted, ref, watch } from 'vue';
import { storeToRefs } from 'pinia';
import { CustomerTransactionRow } from 'src/boot/interfaces';

const search = ref('');
const useCommon = useCommonStore();
const { entityQuery, pagination, result } = storeToRefs(useCommon);

entityQuery.value = {
  message: 'Getting transactions...',
  entity: 'my-store-transactions',
  query: {
    with: 'status,paymentMethod,receiveMethod',
    orderBy: 'created_at:desc',
    page: pagination.value.page,
    limit: 12,
  },
};

const typedResult = result as unknown as CustomerTransactionRow[];

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
.stores-page-container {
  padding: 28px 24px;
  max-width: 1400px;
  margin: 0 auto;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  background: $dark-base !important;
  color: $white;
}

// ── Header ─────────────────────────────────────────────────────────────────
.page-header {
  position: relative;
  background: $dark-card;
  border-radius: 20px;
  border: 1px solid $border;
  box-shadow: 0 8px 40px rgba(0, 0, 0, 0.3);
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
  gap: 24px;
  padding: 28px 32px;
}

.header-title-section {
  display: flex;
  align-items: center;
  gap: 18px;
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

.search-input {
  min-width: 280px;
  max-width: 380px;

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

// ── Empty states ───────────────────────────────────────────────────────────
.empty-state-desktop,
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 80px 20px;
  text-align: center;
  background: $dark-card;
  border-radius: 20px;
  border: 1px solid $border;
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
  font-size: 18px;
  font-weight: 700;
  color: $white;
}

.empty-subtitle {
  font-size: 14px;
  color: $muted;
}

// ── Desktop grid container ─────────────────────────────────────────────────
.stores-grid {
  display: flex;
  flex-direction: column;
  gap: 0;
  background: $dark-card;
  border: 1px solid $border;
  border-top: none;
  border-radius: 0 0 16px 16px;
  overflow: hidden;
}

// ── Grid header ────────────────────────────────────────────────────────────
.grid-header {
  display: grid;
  background: $dark-elevated;
  border: 1px solid $border;
  border-radius: 16px 16px 0 0;
  padding: 0;
  overflow: hidden;
}

.transactions-grid-header {
  grid-template-columns: 2fr 1.5fr 2fr 120px;
}

.grid-header-cell {
  padding: 14px 20px;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.8px;
  color: $muted;
}

// ── Grid rows ──────────────────────────────────────────────────────────────
.store-grid-item {
  border-bottom: 1px solid rgba(255, 255, 255, 0.04);
  transition: background 0.15s;

  &:last-child {
    border-bottom: none;
  }

  &:hover {
    background: rgba(255, 255, 255, 0.025);
  }
}

.grid-row {
  display: grid;
  align-items: center;
}

.transaction-grid-row {
  grid-template-columns: 2fr 1.5fr 2fr 120px;
}

.grid-cell {
  padding: 16px 20px;
  color: $white;
  font-size: 14px;
}

.cell-status {
  display: flex;
  align-items: center;
}

.cell-actions {
  display: flex;
  justify-content: flex-end;
}

// ── Reference cell ─────────────────────────────────────────────────────────
.transaction-reference {
  display: flex;
  align-items: center;
  gap: 12px;
  text-decoration: none;
  color: $white;

  &:hover .transaction-reference-id {
    color: #a5b4fc;
  }
}

.ref-icon-wrap {
  width: 34px;
  height: 34px;
  border-radius: 10px;
  background: linear-gradient(135deg, $accent 0%, $accent-2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  box-shadow: 0 3px 10px rgba(99, 102, 241, 0.3);
}

.transaction-reference-text {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.transaction-reference-id {
  font-size: 14px;
  font-weight: 700;
  color: $white;
  transition: color 0.2s;
  word-break: break-word;
}

.transaction-date {
  font-size: 12px;
  color: $muted;
  display: flex;
  align-items: center;
}

// ── Status badge ───────────────────────────────────────────────────────────
.status-badge {
  font-size: 11px !important;
  font-weight: 700 !important;
  padding: 5px 12px !important;
  border-radius: 20px !important;
  letter-spacing: 0.3px;
}

// ── Summary cell ───────────────────────────────────────────────────────────
.transaction-summary {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.transaction-total {
  font-size: 13px;
  color: $muted;
  font-weight: 500;

  span {
    color: $white;
    font-weight: 700;
    font-size: 14px;
  }
}

.transaction-meta {
  display: flex;
  flex-direction: column;
  gap: 4px;
  font-size: 12px;
  color: $muted;
}

.transaction-meta-item {
  display: flex;
  align-items: center;
}

// ── View button (desktop) ──────────────────────────────────────────────────
.view-btn {
  background: rgba(99, 102, 241, 0.15) !important;
  color: #a5b4fc !important;
  border: 1px solid rgba(99, 102, 241, 0.3) !important;
  border-radius: 10px !important;
  font-weight: 700 !important;
  font-size: 12px !important;
  text-transform: none !important;
  padding: 6px 14px !important;
  transition: background 0.2s !important;

  &:hover {
    background: rgba(99, 102, 241, 0.28) !important;
    color: $white !important;
  }
}

// ── Pagination footer ──────────────────────────────────────────────────────
.grid-pagination {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 14px 20px;
  background: $dark-elevated;
  border: 1px solid $border;
  border-top: none;
  border-radius: 0 0 16px 16px;
  margin-top: 0;
}

.pagination-info {
  font-size: 13px;
  color: $muted;
  font-weight: 500;
}

.pagination-controls {
  display: flex;
  align-items: center;
  gap: 4px;

  :deep(.q-btn) {
    color: $muted !important;

    &:hover {
      color: $white !important;
      background: rgba(255, 255, 255, 0.06) !important;
    }
  }
}

.page-number {
  font-size: 14px;
  color: $white;
  font-weight: 700;
  min-width: 60px;
  text-align: center;
  padding: 0 8px;
}

// ── Mobile cards ───────────────────────────────────────────────────────────
.stores-cards {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.store-card {
  position: relative;
  background: $dark-card;
  border: 1px solid $border;
  border-radius: 16px;
  overflow: hidden;
  display: flex;
  transition: box-shadow 0.2s, border-color 0.2s;

  &:hover {
    box-shadow: 0 6px 24px rgba(0, 0, 0, 0.3);
    border-color: rgba(99, 102, 241, 0.3);
  }
}

.mobile-card-accent {
  width: 3px;
  flex-shrink: 0;
  background: linear-gradient(180deg, $accent 0%, $accent-2 100%);
}

.mobile-card-body {
  flex: 1;
  padding: 16px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.mobile-card-top {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 12px;
}

.mobile-ref-wrap {
  display: flex;
  align-items: center;
  gap: 10px;
}

.mobile-ref-icon {
  width: 32px;
  height: 32px;
  border-radius: 9px;
  background: linear-gradient(135deg, $accent 0%, $accent-2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  box-shadow: 0 3px 10px rgba(99, 102, 241, 0.3);
}

.mobile-divider {
  height: 1px;
  background: $border;
}

.transaction-mobile-details {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.transaction-detail-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 13px;
}

.transaction-detail-label {
  color: $muted;
  font-weight: 500;
}

.transaction-detail-value {
  color: $white;
  font-weight: 700;
}

.mobile-card-actions {
  padding-top: 4px;
}

.mobile-view-btn {
  width: 100%;
  background: rgba(99, 102, 241, 0.15) !important;
  color: #a5b4fc !important;
  border: 1px solid rgba(99, 102, 241, 0.3) !important;
  border-radius: 10px !important;
  font-weight: 700 !important;
  font-size: 13px !important;
  text-transform: none !important;
  height: 36px !important;

  &:hover {
    background: rgba(99, 102, 241, 0.28) !important;
    color: $white !important;
  }
}

.mobile-pagination {
  display: flex;
  justify-content: center;
  padding: 16px 0 4px;
}

// ── Responsive ─────────────────────────────────────────────────────────────
@media (max-width: 768px) {
  .stores-page-container {
    padding: 16px 12px;
  }

  .header-content {
    flex-direction: column;
    align-items: stretch;
    padding: 20px;
  }

  .page-title {
    font-size: 20px !important;
  }

  .search-input {
    min-width: 0;
    max-width: 100%;
  }
}
</style>

<style>
.stores-page-container .stores-grid {
  background: #1e293b !important;
}

.stores-page-container .store-grid-item:hover {
  background: rgba(255, 255, 255, 0.025) !important;
}

.stores-page-container .empty-state-desktop,
.stores-page-container .empty-state {
  background: #1e293b !important;
}

.stores-page-container .store-card {
  background: #1e293b !important;
  border-color: rgba(255, 255, 255, 0.08) !important;
}
</style>
