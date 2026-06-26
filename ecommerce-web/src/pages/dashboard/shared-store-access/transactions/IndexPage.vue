<template>
  <div class="txn-page-container">

    <!-- Hero Header -->
    <div class="page-hero q-mb-xl">
      <div class="hero-accent-overlay"></div>
      <div class="hero-inner">
        <div class="hero-left">
          <div class="hero-icon-wrap">
            <q-icon name="receipt_long" size="28px" color="white" />
          </div>
          <div>
            <h1 class="page-title">All Transactions</h1>
            <div class="page-subtitle">View and manage store transaction history</div>
          </div>
        </div>
        <div class="hero-right">
          <div class="search-input-wrap">
            <q-icon name="search" size="20px" class="search-icon" />
            <q-input v-model="search" placeholder="Search by reference..." outlined dense clearable debounce="1000"
              class="search-field" hide-bottom-space />
          </div>
        </div>
      </div>
    </div>

    <!-- Stats Row -->
    <div class="stats-row q-mb-lg">
      <div class="stat-chip">
        <q-icon name="receipt_long" size="18px" class="stat-chip-icon" />
        <span class="stat-chip-value">{{ pagination.rowsNumber || typedResult.length }}</span>
        <span class="stat-chip-label">Total</span>
      </div>
      <div class="stat-chip stat-chip--green">
        <q-icon name="check_circle" size="18px" class="stat-chip-icon" />
        <span class="stat-chip-value">{{typedResult.filter(t => t.status?.label?.toLowerCase().includes('complet') ||
          t.status?.label?.toLowerCase().includes('deliver')).length}}</span>
        <span class="stat-chip-label">Completed</span>
      </div>
      <div class="stat-chip stat-chip--yellow">
        <q-icon name="pending" size="18px" class="stat-chip-icon" />
        <span class="stat-chip-value">{{typedResult.filter(t => t.status?.label?.toLowerCase().includes('prepar') ||
          t.status?.label?.toLowerCase().includes('process')).length}}</span>
        <span class="stat-chip-label">Processing</span>
      </div>
    </div>

    <!-- Desktop Table View -->
    <div class="desktop-only">
      <div v-if="typedResult.length === 0" class="empty-state-desktop">
        <div class="empty-icon-wrap">
          <q-icon name="receipt_long" size="48px" color="white" />
        </div>
        <div class="empty-title q-mt-md">No transactions found</div>
        <div class="empty-subtitle q-mt-sm">Transaction history will appear here</div>
      </div>

      <div v-else class="table-section">
        <table class="txn-table">
          <thead>
            <tr>
              <th>Reference</th>
              <th>Date</th>
              <th>Status</th>
              <th>Grand Total</th>
              <th>Payment</th>
              <th>Delivery</th>
              <th style="text-align:right;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="transaction in typedResult" :key="transaction.optimus_id" class="txn-row">
              <td>
                <div class="ref-cell">
                  <div class="ref-icon-wrap">
                    <q-icon name="receipt" size="16px" color="white" />
                  </div>
                  <div>
                    <router-link :to="`${$route.path}/${transaction.optimus_id}`" class="ref-link">
                      #{{ transaction.reference_id }}
                    </router-link>
                  </div>
                </div>
              </td>
              <td>
                <div class="date-cell">
                  <q-icon name="calendar_today" size="13px" class="date-icon" />
                  {{ formatDate(transaction.created_at) }}
                </div>
              </td>
              <td>
                <span :class="['status-pill', `status-pill--${getStatusKey(transaction.status?.label)}`]">
                  {{ transaction.status?.label || 'Pending' }}
                </span>
              </td>
              <td>
                <span class="total-value">{{ transaction.grand_total }}</span>
              </td>
              <td>
                <div class="meta-cell">
                  <q-icon name="payment" size="13px" class="meta-icon" />
                  {{ transaction.payment_method?.name || 'N/A' }}
                </div>
              </td>
              <td>
                <div class="meta-cell">
                  <q-icon name="local_shipping" size="13px" class="meta-icon" />
                  {{ transaction.receive_method?.name || 'N/A' }}
                </div>
              </td>
              <td>
                <div class="action-buttons">
                  <q-btn unelevated dense icon="view_list" :to="`${$route.path}/${transaction.optimus_id}`" size="md"
                    class="tbl-btn tbl-btn--indigo">
                    <q-tooltip>View details</q-tooltip>
                  </q-btn>
                </div>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Pagination -->
        <div class="table-pagination">
          <div class="pagination-info">
            Showing {{ pagination.from || 1 }}&ndash;{{ pagination.to || typedResult.length }}
            of {{ pagination.rowsNumber || typedResult.length }} transactions
          </div>
          <div class="pagination-controls">
            <q-btn v-if="pagination.lastPage > 2" flat round dense icon="first_page" :disable="pagination.page === 1"
              @click="goToFirstPage" class="pagination-btn" />
            <q-btn flat round dense icon="chevron_left" :disable="pagination.page === 1" @click="goToPreviousPage"
              class="pagination-btn" />
            <span class="page-number">{{ pagination.page }} / {{ pagination.lastPage }}</span>
            <q-btn flat round dense icon="chevron_right" :disable="pagination.page === pagination.lastPage"
              @click="goToNextPage" class="pagination-btn" />
            <q-btn v-if="pagination.lastPage > 2" flat round dense icon="last_page"
              :disable="pagination.page === pagination.lastPage" @click="goToLastPage" class="pagination-btn" />
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
      </div>

      <div v-else class="txn-cards">
        <div v-for="transaction in typedResult" :key="transaction.optimus_id" class="txn-card q-mb-sm">
          <div class="mobile-card-accent"></div>
          <div class="mobile-card-body">
            <!-- Card Header -->
            <div class="txn-card-header">
              <div class="txn-card-title">
                <div class="ref-icon-wrap-sm">
                  <q-icon name="receipt" size="14px" color="white" />
                </div>
                <router-link :to="`${$route.path}/${transaction.optimus_id}`" class="ref-link">
                  #{{ transaction.reference_id }}
                </router-link>
              </div>
              <span :class="['status-pill', `status-pill--${getStatusKey(transaction.status?.label)}`]">
                {{ transaction.status?.label || 'Pending' }}
              </span>
            </div>

            <!-- Date -->
            <div class="txn-card-date">
              <q-icon name="calendar_today" size="12px" class="date-icon" />
              {{ formatDate(transaction.created_at) }}
            </div>

            <!-- Details -->
            <div class="txn-card-details">
              <div class="detail-row">
                <span class="detail-label">Grand Total</span>
                <span class="detail-value total-value">{{ transaction.grand_total }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">
                  <q-icon name="payment" size="12px" class="q-mr-xs" />Payment
                </span>
                <span class="detail-value">{{ transaction.payment_method?.name || 'N/A' }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">
                  <q-icon name="local_shipping" size="12px" class="q-mr-xs" />Delivery
                </span>
                <span class="detail-value">{{ transaction.receive_method?.name || 'N/A' }}</span>
              </div>
            </div>

            <!-- Actions -->
            <div class="txn-card-actions">
              <q-btn unelevated dense icon="view_list" label="View Details"
                :to="`${$route.path}/${transaction.optimus_id}`" class="action-btn-mobile action-btn-mobile--indigo" />
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

const getStatusKey = (status: string | undefined): string => {
  if (!status) return 'default';
  const s = status.toLowerCase();
  if (s.includes('complet') || s.includes('deliver')) return 'green';
  if (s.includes('prepar') || s.includes('process')) return 'yellow';
  if (s.includes('cancel') || s.includes('reject')) return 'red';
  return 'default';
};
</script>

<style scoped lang="scss">
// ── Dark theme tokens (matching DashboardLayout and ProfilePage) ──────────────
$dark-base: #0f172a;
$dark-card: #1e293b;
$dark-elevated: #273549;
$border: rgba(255, 255, 255, 0.08);
$accent: #6366f1;
$accent-2: #7c3aed;
$green: #10b981;
$green-2: #059669;
$yellow: #fbbf24;
$yellow-2: #f59e0b;
$red: #ef4444;
$red-2: #dc2626;
$blue: #3b82f6;
$white: #ffffff;
$muted: rgba(255, 255, 255, 0.5);

// ── Container ────────────────────────────────────────────────────────────────
.txn-page-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 28px 24px;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  color: $white;
}

// ── Hero ──────────────────────────────────────────────────────────────────────
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
  justify-content: space-between;
  padding: 32px 36px;
  gap: 24px;
  flex-wrap: wrap;
}

.hero-left {
  display: flex;
  align-items: center;
  gap: 20px;
}

.hero-icon-wrap {
  width: 64px;
  height: 64px;
  border-radius: 16px;
  background: linear-gradient(135deg, $accent 0%, $accent-2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 8px 24px rgba(99, 102, 241, 0.4);
  flex-shrink: 0;
}

.page-title {
  font-size: 28px;
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

.hero-right {
  flex-shrink: 0;
}

.search-input-wrap {
  display: flex;
  align-items: center;
  background: $dark-elevated;
  border: 1px solid $border;
  border-radius: 14px;
  padding: 4px 16px;
  width: 300px;
  transition: all 0.2s ease;

  &:focus-within {
    border-color: rgba($accent, 0.4);
    box-shadow: 0 0 0 3px rgba($accent, 0.1);
  }
}

.search-icon {
  color: $muted;
  margin-right: 12px;
  flex-shrink: 0;
}

.search-field {
  flex: 1;

  :deep(.q-field__control) {
    background: transparent !important;
    border: none !important;
  }

  :deep(.q-field__native) {
    color: $white !important;
    font-size: 14px;
    padding: 8px 0;
  }

  :deep(.q-field__native::placeholder) {
    color: $muted !important;
  }
}

// ── Stats Row ────────────────────────────────────────────────────────────────
.stats-row {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
}

.stat-chip {
  display: flex;
  align-items: center;
  gap: 8px;
  background: $dark-card;
  border: 1px solid $border;
  border-radius: 12px;
  padding: 10px 18px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
  transition: all 0.2s ease;

  .stat-chip-icon {
    color: $accent;
  }

  &--green .stat-chip-icon {
    color: $green;
  }

  &--yellow .stat-chip-icon {
    color: $yellow;
  }

  &:hover {
    border-color: rgba($accent, 0.25);
    transform: translateY(-1px);
  }
}

.stat-chip-value {
  font-size: 18px;
  font-weight: 800;
  color: $white;
  line-height: 1;
}

.stat-chip-label {
  font-size: 12px;
  color: $muted;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

// ── Empty states ──────────────────────────────────────────────────────────────
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

// ── Table ─────────────────────────────────────────────────────────────────────
.table-section {
  background: $dark-card;
  border: 1px solid $border;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 8px 40px rgba(0, 0, 0, 0.25);
}

.txn-table {
  width: 100%;
  border-collapse: collapse;

  thead tr {
    background: $dark-elevated;
  }

  th {
    font-size: 11px;
    font-weight: 700;
    color: $muted;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    padding: 14px 20px;
    text-align: left;
    border: none;
    border-bottom: 1px solid $border;
  }

  tbody tr.txn-row {
    border-bottom: 1px solid $border;
    transition: background 0.18s ease;

    &:last-child {
      border-bottom: none;
    }

    &:hover {
      background: rgba($accent, 0.04);
    }
  }

  td {
    padding: 16px 20px;
    border: none;
    vertical-align: middle;
    color: $white;
    font-size: 13px;
  }
}

// ── Reference cell ────────────────────────────────────────────────────────────
.ref-cell {
  display: flex;
  align-items: center;
  gap: 10px;
}

.ref-icon-wrap {
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

.ref-link {
  font-weight: 700;
  font-size: 14px;
  color: #a5b4fc;
  text-decoration: none;
  transition: color 0.2s;

  &:hover {
    color: $accent;
  }
}

// ── Date / meta cells ─────────────────────────────────────────────────────────
.date-cell,
.meta-cell {
  display: flex;
  align-items: center;
  gap: 6px;
  color: $muted;
  font-size: 13px;
}

.date-icon,
.meta-icon {
  color: $muted;
  flex-shrink: 0;
}

// ── Status pill ───────────────────────────────────────────────────────────────
.status-pill {
  display: inline-flex;
  align-items: center;
  font-size: 11px;
  font-weight: 700;
  padding: 4px 10px;
  border-radius: 20px;
  text-transform: uppercase;
  letter-spacing: 0.4px;
  white-space: nowrap;

  &--green {
    background: rgba($green, 0.15);
    color: #6ee7b7;
    border: 1px solid rgba($green, 0.25);
  }

  &--yellow {
    background: rgba($yellow, 0.15);
    color: $yellow;
    border: 1px solid rgba($yellow, 0.25);
  }

  &--red {
    background: rgba($red, 0.15);
    color: #fca5a5;
    border: 1px solid rgba($red, 0.25);
  }

  &--default {
    background: rgba($accent, 0.15);
    color: #a5b4fc;
    border: 1px solid rgba($accent, 0.25);
  }
}

// ── Grand total ───────────────────────────────────────────────────────────────
.total-value {
  font-weight: 700;
  color: #6ee7b7;
  font-size: 14px;
}

// ── Table action buttons ──────────────────────────────────────────────────────
.action-buttons {
  display: flex;
  gap: 6px;
  justify-content: flex-end;
}

.tbl-btn {
  border-radius: 9px !important;
  width: 34px !important;
  height: 34px !important;

  &--indigo {
    background: rgba($accent, 0.15) !important;
    color: #a5b4fc !important;
    border: 1px solid rgba($accent, 0.3) !important;

    &:hover {
      background: rgba($accent, 0.28) !important;
      color: $white !important;
    }
  }
}

// ── Pagination ────────────────────────────────────────────────────────────────
.table-pagination {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px 24px;
  background: $dark-elevated;
  border-top: 1px solid $border;
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
}

.pagination-btn {
  width: 34px;
  height: 34px;
  border-radius: 9px;
  background: rgba(255, 255, 255, 0.06) !important;
  color: $muted !important;
  border: 1px solid $border !important;

  &:hover:not(:disabled) {
    background: rgba(255, 255, 255, 0.12) !important;
    color: $white !important;
  }

  &:disabled {
    opacity: 0.4;
  }
}

.page-number {
  font-size: 13px;
  font-weight: 700;
  color: $white;
  min-width: 56px;
  text-align: center;
  padding: 0 8px;
}

// ── Mobile cards ─────────────────────────────────────────────────────────────
.txn-cards {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.txn-card {
  display: flex;
  background: $dark-card;
  border: 1px solid $border;
  border-radius: 16px;
  overflow: hidden;
  transition: box-shadow 0.2s, border-color 0.2s;

  &:hover {
    box-shadow: 0 6px 24px rgba(0, 0, 0, 0.3);
    border-color: rgba($accent, 0.3);
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
  gap: 10px;
}

.txn-card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
}

.txn-card-title {
  display: flex;
  align-items: center;
  gap: 8px;
}

.ref-icon-wrap-sm {
  width: 26px;
  height: 26px;
  border-radius: 7px;
  background: linear-gradient(135deg, $accent 0%, $accent-2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.txn-card-date {
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 12px;
  color: $muted;
}

.txn-card-details {
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid $border;
  border-radius: 10px;
  padding: 10px 14px;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.detail-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 13px;
}

.detail-label {
  color: $muted;
  font-weight: 500;
  display: flex;
  align-items: center;
}

.detail-value {
  font-weight: 600;
  color: $white;
}

.txn-card-actions {
  padding-top: 4px;
}

.action-btn-mobile {
  width: 100%;
  border-radius: 10px !important;
  font-weight: 700 !important;
  font-size: 13px !important;
  height: 36px !important;
  text-transform: none !important;
  letter-spacing: 0 !important;

  &--indigo {
    background: rgba($accent, 0.15) !important;
    color: #a5b4fc !important;
    border: 1px solid rgba($accent, 0.3) !important;

    &:hover {
      background: rgba($accent, 0.28) !important;
      color: $white !important;
    }
  }
}

.mobile-pagination {
  display: flex;
  justify-content: center;
  padding: 16px 0 4px;
}

// ── Responsive ────────────────────────────────────────────────────────────────
@media (max-width: 768px) {
  .txn-page-container {
    padding: 16px 12px;
  }

  .hero-inner {
    flex-direction: column;
    gap: 16px;
    padding: 24px 20px;
  }

  .hero-left {
    gap: 14px;
  }

  .page-title {
    font-size: 22px;
  }

  .search-input-wrap {
    width: 100%;
  }

  .stats-row {
    gap: 8px;
  }

  .stat-chip {
    flex: 1;
    justify-content: center;
    padding: 10px 12px;
  }
}
</style>
