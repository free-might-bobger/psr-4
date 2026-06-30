<template>
  <div class="stores-page-container">
    <!-- Header Section -->
    <div class="page-header q-mb-md">
      <div class="header-content">
        <div class="header-title-section">
          <q-icon name="receipt_long" size="32px" color="primary" class="q-mr-sm" />
          <h2 class="page-title">All Transactions</h2>
        </div>
        <div class="header-actions">
          <q-input
            v-model="search"
            placeholder="Search transactions..."
            outlined
            dense
            clearable
            debounce="300"
            class="search-input"
          >
            <template v-slot:prepend>
              <q-icon name="search" />
            </template>
          </q-input>
        </div>
      </div>
    </div>

    <!-- Desktop Grid View -->
    <div class="desktop-only">
      <div v-if="typedResult.length === 0" class="empty-state-desktop">
        <q-icon name="receipt_long" size="80px" color="grey-4" />
        <div class="text-h5 q-mt-md text-grey-6">No transactions found</div>
        <div class="text-body2 text-grey-5 q-mt-sm">Your transaction history will appear here</div>
      </div>
      <div v-else>
        <!-- Grid Header -->
        <div class="grid-header transactions-grid-header">
          <div class="grid-header-cell header-name">
            <q-icon name="tag" size="20px" color="primary" class="q-mr-xs" />
            <span class="header-label">Reference</span>
          </div>
          <div class="grid-header-cell header-name">
            <q-icon name="tag" size="20px" color="primary" class="q-mr-xs" />
            <span class="header-label">Order Status</span>
          </div>
          <div class="grid-header-cell header-mobile">
            <q-icon name="payments" size="20px" color="primary" class="q-mr-xs" />
            <span class="header-label">Summary</span>
          </div>
          <div class="grid-header-cell header-actions">
            <q-icon name="settings" size="20px" color="primary" class="q-mr-xs" />
            <span class="header-label">Actions</span>
          </div>
        </div>

        <!-- Grid Rows -->
        <div class="stores-grid">
          <q-card
            v-for="transaction in typedResult"
            :key="transaction.optimus_id"
            flat
            bordered
            class="store-grid-item"
          >
            <div class="grid-row transaction-grid-row">
              <div class="grid-cell cell-name">
                <router-link
                  :to="`${$route.path}/${transaction.optimus_id}`"
                  class="transaction-reference"
                >
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
              <q-badge
                :color="getStatusColor(transaction.status?.label)"
                :label="transaction.status?.label || 'Pending'"
                class="status-badge"
              />
            </div>
              <div class="grid-cell cell-mobile">
                <div class="transaction-summary">
                  <div class="transaction-total">
                    Grand Total:
                    <span>{{ transaction.grand_total }}</span>
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
                  <q-btn
                    round
                    dense
                    color="primary"
                    icon="view_list"
                    :to="`${$route.path}/${transaction.optimus_id}`"
                    class="action-btn-grid action-btn-edit"
                  >
                    <q-tooltip>View details</q-tooltip>
                  </q-btn>
                </div>
              </div>
            </div>
          </q-card>
        </div>

        <!-- Pagination -->
        <div class="grid-pagination">
          <div class="pagination-info">
            Showing {{ pagination.from }} - {{ pagination.to }} of {{ pagination.rowsNumber }} transactions
          </div>
          <div class="pagination-controls">
            <q-btn
              v-if="pagination.lastPage > 2"
              flat
              round
              dense
              icon="first_page"
              color="grey-8"
              :disable="pagination.page === 1"
              @click="goToFirstPage"
            />
            <q-btn
              flat
              round
              dense
              icon="chevron_left"
              color="grey-8"
              :disable="pagination.page === 1"
              @click="goToPreviousPage"
            />
            <span class="page-number">{{ pagination.page }} / {{ pagination.lastPage }}</span>
            <q-btn
              flat
              round
              dense
              icon="chevron_right"
              color="grey-8"
              :disable="pagination.page === pagination.lastPage"
              @click="goToNextPage"
            />
            <q-btn
              v-if="pagination.lastPage > 2"
              flat
              round
              dense
              icon="last_page"
              color="grey-8"
              :disable="pagination.page === pagination.lastPage"
              @click="goToLastPage"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Mobile Card View -->
    <div class="mobile-only">
      <div v-if="typedResult.length === 0" class="empty-state">
        <q-icon name="receipt_long" size="64px" color="grey-4" />
        <div class="text-h6 q-mt-md text-grey-6">No transactions found</div>
        <div class="text-body2 text-grey-5 q-mt-sm">Your transaction history will appear here</div>
      </div>
      <div v-else class="stores-cards">
        <q-card
          v-for="transaction in typedResult"
          :key="transaction.optimus_id"
          flat
          bordered
          class="store-card q-mb-md"
        >
          <q-card-section>
            <div class="store-card-header transaction-store-card-header">
              <div class="store-card-title">
                <div class="transaction-reference-id">#{{ transaction.reference_id }}</div>
              </div>
              <div class="transaction-header-badge">
                <q-badge
                  :color="getStatusColor(transaction.status?.label)"
                  :label="transaction.status?.label || 'Pending'"
                  class="status-badge"
                />
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
              <q-btn
                unelevated
                dense
                color="negative"
                icon="check_circle"
                label="Received"
                :to="`${$route.path}/${transaction.optimus_id}`"
                class="action-btn-mobile action-btn-delete-mobile"
              />
              <q-btn
                unelevated
                dense
                color="primary"
                icon="view_list"
                label="View"
                :to="`${$route.path}/${transaction.optimus_id}`"
                class="action-btn-mobile action-btn-edit-mobile"
              />
            </div>
          </q-card-section>
        </q-card>
      </div>
      <div v-if="typedResult.length > 0" class="mobile-pagination q-mt-md">
        <q-pagination
          v-model="pagination.page"
          :max="pagination.lastPage"
          :max-pages="5"
          direction-links
          boundary-links
          color="primary"
          @update:model-value="handlePageChange"
        />
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
@import 'src/css/dashboard/all-stores/index.scss';

.transactions-grid-header {
  grid-template-columns: 2fr 1.5fr 2fr 1fr;
}

.transaction-grid-row {
  grid-template-columns: 2fr 1.5fr 2fr 1fr;
}

.cell-status {
  display: flex;
  align-items: center;
}

.status-badge {
  font-size: 12px;
  font-weight: 600;
  padding: 6px 12px;
  border-radius: 16px;
}

/* Mobile card: wrap status badge to next line when header row is too tight */
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

.transaction-reference {
  display: flex;
  align-items: center;
  gap: 12px;
  color: #1a1a1a;
}

.transaction-reference-text {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.transaction-reference-id {
  font-size: 16px;
  font-weight: 700;
  color: #1a1a1a;
}

.transaction-date {
  font-size: 12px;
  color: #666;
  display: flex;
  align-items: center;
}

.transaction-summary {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.transaction-total {
  font-size: 14px;
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

@media (max-width: 768px) {
  .transactions-grid-header {
    display: none;
  }

  .transaction-grid-row {
    grid-template-columns: 1fr;
  }

  .transaction-reference {
    gap: 10px;
  }

  .transaction-reference-id {
    font-size: 18px;
  }
}
</style>
