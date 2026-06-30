<template>
  <div class="tx-page">

    <!-- Hero Header -->
    <div class="page-hero q-mb-xl">
      <div class="hero-accent-overlay"></div>
      <div class="hero-inner">
        <div class="hero-icon-wrap">
          <q-icon name="receipt_long" size="28px" color="white" />
        </div>
        <div class="hero-text">
          <h1 class="hero-title">My Transactions</h1>
          <div class="hero-subtitle">Track and manage your orders</div>
        </div>
      </div>
      <div class="hero-controls">
        <div class="search-wrap">
          <q-icon name="search" size="20px" class="search-icon" />
          <input v-model="search" type="text" placeholder="Search transactions..." class="dark-search" />
        </div>
      </div>
    </div>

    <!-- Stats Row -->
    <div class="stats-row q-mb-lg">
      <div class="stat-card">
        <div class="stat-icon-wrap stat-icon-indigo">
          <q-icon name="receipt_long" size="20px" color="white" />
        </div>
        <div>
          <div class="stat-value">{{ typedResult.length }}</div>
          <div class="stat-label">On this page</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon-wrap stat-icon-blue">
          <q-icon name="data_usage" size="20px" color="white" />
        </div>
        <div>
          <div class="stat-value">{{ pagination.rowsNumber }}</div>
          <div class="stat-label">Total records</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon-wrap stat-icon-green">
          <q-icon name="menu_book" size="20px" color="white" />
        </div>
        <div>
          <div class="stat-value">{{ pagination.page }} / {{ pagination.lastPage }}</div>
          <div class="stat-label">Current page</div>
        </div>
      </div>
    </div>

    <!-- Desktop Table -->
    <div class="desktop-only">
      <div v-if="typedResult.length === 0" class="empty-state">
        <div class="empty-icon-wrap">
          <q-icon name="receipt_long" size="48px" color="white" />
        </div>
        <div class="empty-title">No transactions found</div>
        <div class="empty-subtitle">Your transaction history will appear here</div>
      </div>

      <div v-else class="table-card">
        <table class="dark-table">
          <thead>
            <tr>
              <th>Reference</th>
              <th>Status</th>
              <th>Summary</th>
              <th class="th-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="tx in typedResult" :key="tx.optimus_id">
              <td>
                <router-link :to="`${$route.path}/${tx.optimus_id}`" class="tx-ref-link">
                  <div class="tx-ref-id">#{{ tx.reference_id }}</div>
                  <div class="tx-date">
                    <q-icon name="calendar_today" size="12px" />
                    {{ formatDate(tx.created_at) }}
                  </div>
                </router-link>
              </td>
              <td>
                <span class="status-pill" :class="`status-${getStatusColor(tx.status?.label)}`">
                  {{ tx.status?.label || 'Pending' }}
                </span>
              </td>
              <td>
                <div class="tx-summary">
                  <div class="tx-total">{{ formatMoney(tx.grand_total) }}</div>
                  <div class="tx-meta">
                    <span><q-icon name="payment" size="12px" /> {{ tx.payment_method?.name || 'N/A' }}</span>
                    <span><q-icon name="local_shipping" size="12px" /> {{ tx.receive_method?.name || 'N/A' }}</span>
                  </div>
                </div>
              </td>
              <td class="td-center">
                <div class="action-btns">
                  <button v-if="tx.status?.name !== TRANSACTION_STATUS.COMPLETED" class="action-pill action-received"
                    @click="markedAsReceived(tx.optimus_id)">
                    <q-icon name="inventory_2" size="16px" />
                    Received
                  </button>
                  <router-link :to="`${$route.path}/${tx.optimus_id}`" class="action-pill action-view">
                    <q-icon name="info" size="16px" />
                    View
                  </router-link>
                </div>
              </td>
            </tr>
          </tbody>
        </table>

        <div class="table-pagination">
          <div class="pagination-info">
            Showing {{ pagination.from }}&ndash;{{ pagination.to }} of {{ pagination.rowsNumber }} transactions
          </div>
          <div class="pagination-controls">
            <button v-if="pagination.lastPage > 2" class="pg-btn" :disabled="pagination.page === 1"
              @click="goToFirstPage">
              <q-icon name="first_page" size="18px" />
            </button>
            <button class="pg-btn" :disabled="pagination.page === 1" @click="goToPreviousPage">
              <q-icon name="chevron_left" size="18px" />
            </button>
            <span class="pg-current">{{ pagination.page }} / {{ pagination.lastPage }}</span>
            <button class="pg-btn" :disabled="pagination.page === pagination.lastPage" @click="goToNextPage">
              <q-icon name="chevron_right" size="18px" />
            </button>
            <button v-if="pagination.lastPage > 2" class="pg-btn" :disabled="pagination.page === pagination.lastPage"
              @click="goToLastPage">
              <q-icon name="last_page" size="18px" />
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Mobile Card View -->
    <div class="mobile-only">
      <div v-if="typedResult.length === 0" class="empty-state">
        <div class="empty-icon-wrap">
          <q-icon name="receipt_long" size="48px" color="white" />
        </div>
        <div class="empty-title">No transactions found</div>
        <div class="empty-subtitle">Your transaction history will appear here</div>
      </div>

      <div v-else class="mobile-cards">
        <div v-for="tx in typedResult" :key="tx.optimus_id" class="m-card">
          <div class="m-card-top">
            <div class="m-card-avatar">
              <q-icon name="receipt_long" size="20px" color="white" />
            </div>
            <div class="m-card-info">
              <div class="m-card-name">#{{ tx.reference_id }}</div>
              <span class="status-pill" :class="`status-${getStatusColor(tx.status?.label)}`"
                style="margin-top:6px;display:inline-flex;">
                {{ tx.status?.label || 'Pending' }}
              </span>
            </div>
          </div>

          <div class="m-card-detail">
            <div class="detail-icon"><q-icon name="calendar_today" size="16px" /></div>
            <span class="detail-text">{{ formatDate(tx.created_at) }}</span>
          </div>
          <div class="m-card-detail">
            <div class="detail-icon"><q-icon name="payments" size="16px" /></div>
            <span class="detail-text">{{ formatMoney(tx.grand_total) }}</span>
          </div>
          <div class="m-card-detail">
            <div class="detail-icon"><q-icon name="payment" size="16px" /></div>
            <span class="detail-text">{{ tx.payment_method?.name || 'N/A' }}</span>
          </div>
          <div class="m-card-detail">
            <div class="detail-icon"><q-icon name="local_shipping" size="16px" /></div>
            <span class="detail-text">{{ tx.receive_method?.name || 'N/A' }}</span>
          </div>

          <div class="m-card-actions">
            <button v-if="tx.status?.name !== TRANSACTION_STATUS.COMPLETED" class="m-action m-action-received"
              @click="markedAsReceived(tx.optimus_id)">
              <q-icon name="inventory_2" size="16px" class="q-mr-xs" />
              Received
            </button>
            <router-link :to="`${$route.path}/${tx.optimus_id}`" class="m-action m-action-view">
              <q-icon name="info" size="16px" class="q-mr-xs" />
              View
            </router-link>
          </div>
        </div>
      </div>

      <div v-if="typedResult.length > 0" class="m-pagination">
        <div class="pagination-controls">
          <button v-if="pagination.lastPage > 2" class="pg-btn" :disabled="pagination.page === 1"
            @click="goToFirstPage">
            <q-icon name="first_page" size="18px" />
          </button>
          <button class="pg-btn" :disabled="pagination.page === 1" @click="goToPreviousPage">
            <q-icon name="chevron_left" size="18px" />
          </button>
          <span class="pg-current">{{ pagination.page }} / {{ pagination.lastPage }}</span>
          <button class="pg-btn" :disabled="pagination.page === pagination.lastPage" @click="goToNextPage">
            <q-icon name="chevron_right" size="18px" />
          </button>
          <button v-if="pagination.lastPage > 2" class="pg-btn" :disabled="pagination.page === pagination.lastPage"
            @click="goToLastPage">
            <q-icon name="last_page" size="18px" />
          </button>
        </div>
        <div class="pagination-info">
          Showing {{ pagination.from }}&ndash;{{ pagination.to }} of {{ pagination.rowsNumber }} transactions
        </div>
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
import { formatMoney } from 'src/boot/utilities';
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

const markedAsReceived = async (transactionId: number | string) => {
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
// ── Tokens ────────────────────────────────────────────────────────────────────
$dark-base: #0f172a;
$dark-card: #1e293b;
$dark-elevated: #273549;
$border: rgba(255, 255, 255, 0.08);
$border-strong: rgba(255, 255, 255, 0.15);
$accent: #6366f1;
$accent-2: #7c3aed;
$blue: #3b82f6;
$blue-2: #2563eb;
$green: #10b981;
$green-2: #059669;
$red: #ef4444;
$white: #ffffff;
$muted: rgba(255, 255, 255, 0.5);
$muted-2: rgba(255, 255, 255, 0.3);

// ── Page ──────────────────────────────────────────────────────────────────────
.tx-page {
  padding: 28px 24px 60px;
  max-width: 1400px;
  margin: 0 auto;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  color: $white;
}

// ── Hero ──────────────────────────────────────────────────────────────────────
.page-hero {
  position: relative;
  background: $dark-card;
  border-radius: 24px;
  border: 1px solid $border;
  box-shadow: 0 8px 40px rgba(0, 0, 0, 0.3);
  overflow: hidden;
}

.hero-accent-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, rgba($accent, 0.18) 0%, rgba($accent-2, 0.10) 60%, transparent 100%);
  pointer-events: none;
}

.hero-inner {
  position: relative;
  display: flex;
  align-items: center;
  gap: 18px;
  padding: 32px 36px 0;
}

.hero-icon-wrap {
  width: 56px;
  height: 56px;
  border-radius: 16px;
  background: linear-gradient(135deg, $accent 0%, $accent-2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 8px 24px rgba($accent, 0.4);
  flex-shrink: 0;
}

.hero-title {
  font-size: 26px;
  font-weight: 800;
  color: $white !important;
  margin: 0 0 4px;
  letter-spacing: -0.3px;
  line-height: 1.2;
}

.hero-subtitle {
  font-size: 14px;
  color: $muted;
  font-weight: 500;
}

.hero-controls {
  position: relative;
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 20px 36px 28px;
  flex-wrap: wrap;
}

// ── Search ────────────────────────────────────────────────────────────────────
.search-wrap {
  position: relative;
  flex: 1;
  min-width: 200px;
  max-width: 360px;
}

.search-icon {
  position: absolute;
  left: 14px;
  top: 50%;
  transform: translateY(-50%);
  color: $muted !important;
  pointer-events: none;
}

.dark-search {
  width: 100%;
  height: 44px;
  border-radius: 12px;
  border: 1px solid $border;
  background: $dark-elevated;
  padding: 0 16px 0 44px;
  font-size: 14px;
  color: $white;
  outline: none;
  transition: all 0.2s ease;

  &::placeholder {
    color: $muted-2;
  }

  &:focus {
    border-color: rgba($accent, 0.5);
    box-shadow: 0 0 0 3px rgba($accent, 0.12);
  }
}

// ── Stats ─────────────────────────────────────────────────────────────────────
.stats-row {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
}

.stat-card {
  background: $dark-card;
  border: 1px solid $border;
  border-radius: 16px;
  padding: 20px;
  display: flex;
  align-items: center;
  gap: 14px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
}

.stat-icon-wrap {
  width: 42px;
  height: 42px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;

  &.stat-icon-indigo {
    background: linear-gradient(135deg, $accent 0%, $accent-2 100%);
    box-shadow: 0 4px 16px rgba($accent, 0.3);
  }

  &.stat-icon-blue {
    background: linear-gradient(135deg, $blue 0%, $blue-2 100%);
    box-shadow: 0 4px 16px rgba($blue, 0.3);
  }

  &.stat-icon-green {
    background: linear-gradient(135deg, $green 0%, $green-2 100%);
    box-shadow: 0 4px 16px rgba($green, 0.3);
  }
}

.stat-value {
  font-size: 18px;
  font-weight: 800;
  color: $white;
}

.stat-label {
  font-size: 12px;
  font-weight: 600;
  color: $muted;
  text-transform: uppercase;
  letter-spacing: 0.3px;
}

// ── Empty state ───────────────────────────────────────────────────────────────
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 80px 20px;
  text-align: center;
}

.empty-icon-wrap {
  width: 96px;
  height: 96px;
  border-radius: 24px;
  background: linear-gradient(135deg, $accent, $accent-2);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 12px 32px rgba($accent, 0.3);
  margin-bottom: 24px;
}

.empty-title {
  font-size: 20px;
  font-weight: 700;
  color: $white;
  margin-bottom: 8px;
}

.empty-subtitle {
  font-size: 14px;
  color: $muted;
}

// ── Table card ────────────────────────────────────────────────────────────────
.table-card {
  background: $dark-card;
  border: 1px solid $border;
  border-radius: 20px;
  box-shadow: 0 8px 40px rgba(0, 0, 0, 0.25);
  overflow: hidden;
}

.dark-table {
  width: 100%;
  border-collapse: collapse;

  thead tr {
    background: $dark-elevated;
  }

  th {
    padding: 16px 24px;
    text-align: left;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: $muted;
    border-bottom: 1px solid $border;

    &.th-center {
      text-align: center;
    }
  }

  tbody tr {
    transition: background 0.15s ease;
    border-bottom: 1px solid $border;

    &:last-child {
      border-bottom: none;
    }

    &:hover {
      background: rgba($accent, 0.04);
    }
  }

  td {
    padding: 18px 24px;
    font-size: 14px;
    color: rgba(255, 255, 255, 0.85);
    vertical-align: middle;

    &.td-center {
      text-align: center;
    }
  }
}

// ── Reference cell ────────────────────────────────────────────────────────────
.tx-ref-link {
  text-decoration: none;
  transition: color 0.2s;

  &:hover .tx-ref-id {
    color: $accent;
  }
}

.tx-ref-id {
  font-size: 15px;
  font-weight: 700;
  color: #a5b4fc;
  transition: color 0.2s;
}

.tx-date {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 12px;
  color: $muted;
  margin-top: 4px;
}

// ── Status pill ───────────────────────────────────────────────────────────────
.status-pill {
  display: inline-flex;
  align-items: center;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 700;
  letter-spacing: 0.3px;

  &.status-positive {
    background: rgba($green, 0.12);
    color: #6ee7b7;
    border: 1px solid rgba($green, 0.2);
  }

  &.status-warning {
    background: rgba(#f59e0b, 0.12);
    color: #fcd34d;
    border: 1px solid rgba(#f59e0b, 0.25);
  }

  &.status-negative {
    background: rgba($red, 0.12);
    color: #fca5a5;
    border: 1px solid rgba($red, 0.2);
  }

  &.status-primary {
    background: rgba($accent, 0.12);
    color: #a5b4fc;
    border: 1px solid rgba($accent, 0.2);
  }

  &.status-grey {
    background: rgba(255, 255, 255, 0.06);
    color: $muted;
    border: 1px solid $border;
  }
}

// ── Summary cell ──────────────────────────────────────────────────────────────
.tx-summary {
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.tx-total {
  font-size: 15px;
  font-weight: 800;
  color: #a5b4fc;
}

.tx-meta {
  display: flex;
  flex-direction: column;
  gap: 3px;
  font-size: 12px;
  color: $muted;

  span {
    display: flex;
    align-items: center;
    gap: 4px;
  }
}

// ── Action pills ──────────────────────────────────────────────────────────────
.action-btns {
  display: flex;
  gap: 8px;
  justify-content: center;
}

.action-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 7px 16px;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 600;
  border: none;
  cursor: pointer;
  text-decoration: none;
  transition: all 0.2s ease;

  &.action-received {
    background: rgba($green, 0.10);
    color: #6ee7b7;
    border: 1px solid rgba($green, 0.2);

    &:hover {
      background: rgba($green, 0.2);
      border-color: rgba($green, 0.4);
    }
  }

  &.action-view {
    background: rgba($accent, 0.12);
    color: #a5b4fc;
    border: 1px solid rgba($accent, 0.2);

    &:hover {
      background: rgba($accent, 0.2);
      border-color: rgba($accent, 0.4);
    }
  }
}

// ── Table pagination ──────────────────────────────────────────────────────────
.table-pagination {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 24px;
  background: $dark-elevated;
  border-top: 1px solid $border;
  flex-wrap: wrap;
  gap: 12px;
}

.pagination-info {
  font-size: 13px;
  color: $muted;
  font-weight: 500;
}

.pagination-controls {
  display: flex;
  align-items: center;
  gap: 6px;
}

.pg-btn {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  border: 1px solid $border;
  background: transparent;
  color: $muted;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s ease;

  &:hover:not(:disabled) {
    background: rgba($accent, 0.12);
    border-color: rgba($accent, 0.3);
    color: #a5b4fc;
  }

  &:disabled {
    opacity: 0.3;
    cursor: not-allowed;
  }
}

.pg-current {
  font-size: 13px;
  font-weight: 700;
  color: $white;
  padding: 0 12px;
  min-width: 60px;
  text-align: center;
}

// ── Mobile cards ──────────────────────────────────────────────────────────────
.mobile-cards {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.m-card {
  background: $dark-card;
  border: 1px solid $border;
  border-radius: 18px;
  box-shadow: 0 4px 24px rgba(0, 0, 0, 0.2);
  overflow: hidden;
  transition: all 0.2s ease;

  &:hover {
    border-color: rgba($accent, 0.2);
  }
}

.m-card-top {
  display: flex;
  align-items: flex-start;
  gap: 14px;
  padding: 20px 20px 16px;
}

.m-card-avatar {
  width: 44px;
  height: 44px;
  border-radius: 13px;
  background: linear-gradient(135deg, $accent, $accent-2);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 6px 16px rgba($accent, 0.3);
  flex-shrink: 0;
}

.m-card-info {
  flex: 1;
  min-width: 0;
}

.m-card-name {
  font-size: 16px;
  font-weight: 700;
  color: #a5b4fc;
  word-break: break-word;
}

.m-card-detail {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 0 20px 12px;
}

.detail-icon {
  width: 32px;
  height: 32px;
  border-radius: 9px;
  background: rgba(255, 255, 255, 0.06);
  display: flex;
  align-items: center;
  justify-content: center;
  color: $muted;
  flex-shrink: 0;
}

.detail-text {
  font-size: 14px;
  font-weight: 600;
  color: rgba(255, 255, 255, 0.8);
}

.m-card-actions {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;
  padding: 0 20px 20px;
}

.m-action {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 12px 14px;
  border-radius: 12px;
  font-size: 13px;
  font-weight: 700;
  border: none;
  cursor: pointer;
  text-decoration: none;
  transition: all 0.2s ease;

  &.m-action-received {
    background: rgba($green, 0.12);
    color: #6ee7b7;
    border: 1px solid rgba($green, 0.2);

    &:hover {
      background: rgba($green, 0.2);
      border-color: rgba($green, 0.35);
    }
  }

  &.m-action-view {
    background: linear-gradient(135deg, $accent, $accent-2);
    color: $white;
    box-shadow: 0 4px 16px rgba($accent, 0.3);

    &:hover {
      box-shadow: 0 6px 20px rgba($accent, 0.45);
      transform: translateY(-1px);
    }
  }
}

// ── Mobile pagination ─────────────────────────────────────────────────────────
.m-pagination {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
  margin-top: 16px;
  padding: 20px;
  background: $dark-card;
  border: 1px solid $border;
  border-radius: 16px;
}

// ── Responsive ────────────────────────────────────────────────────────────────
@media (max-width: 768px) {
  .tx-page {
    padding: 16px 12px 48px;
  }

  .hero-inner {
    padding: 24px 20px 0;
    flex-wrap: wrap;
  }

  .hero-title {
    font-size: 22px;
  }

  .hero-controls {
    padding: 16px 20px 24px;
    flex-direction: column;
    align-items: stretch;
    gap: 12px;
  }

  .search-wrap {
    max-width: 100%;
    min-width: 0;
  }

  .stats-row {
    grid-template-columns: 1fr;
    gap: 10px;
  }

  .m-card-actions {
    padding: 0 16px 16px;
    gap: 8px;
  }

  .m-card-top {
    padding: 16px 16px 12px;
  }

  .m-card-detail {
    padding: 0 16px 10px;
  }
}

@media (max-width: 600px) {
  .tx-page {
    padding: 10px 8px 40px;
  }

  .hero-inner {
    padding: 20px 16px 0;
    gap: 12px;
  }

  .hero-icon-wrap {
    width: 46px;
    height: 46px;
    border-radius: 13px;
  }

  .hero-title {
    font-size: 20px;
  }

  .hero-controls {
    padding: 14px 16px 20px;
  }
}
</style>
