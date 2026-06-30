<template>
  <div class="delivery-page-container">

    <!-- Hero Header -->
    <div class="page-hero q-mb-xl">
      <div class="hero-accent-overlay"></div>
      <div class="hero-inner">
        <div class="hero-left">
          <div class="hero-icon-wrap">
            <q-icon name="local_shipping" size="28px" color="white" />
          </div>
          <div>
            <h1 class="page-title">Deliveries Near Me</h1>
            <div class="page-subtitle">Find nearby orders ready for delivery</div>
          </div>
        </div>
        <div class="hero-right">
          <div class="location-badge">
            <q-icon name="location_on" size="18px" class="location-badge-icon" />
            <span v-if="(entityQuery.query.latitude ?? 1) > 1 && (entityQuery.query.longitude ?? 1) > 1">
              {{ Number(entityQuery.query.latitude ?? 1).toFixed(4) }}°,
              {{ Number(entityQuery.query.longitude ?? 1).toFixed(4) }}°
            </span>
            <span v-else>Locating…</span>
          </div>
          <q-btn flat round dense :icon="locationLoading ? '' : 'my_location'" class="location-refresh-btn"
            :loading="locationLoading" @click="refreshLocation">
            <q-tooltip>Refresh Location</q-tooltip>
          </q-btn>
        </div>
      </div>
    </div>

    <!-- Stats Row -->
    <div class="stats-row q-mb-lg">
      <div class="stat-chip">
        <q-icon name="local_shipping" size="18px" class="stat-chip-icon" />
        <span class="stat-chip-value">{{ pagination.rowsNumber || typedResult.length }}</span>
        <span class="stat-chip-label">Deliveries</span>
      </div>
      <div class="stat-chip stat-chip--green">
        <q-icon name="near_me" size="18px" class="stat-chip-icon" />
        <span class="stat-chip-value">{{ nearestDistance !== null ? nearestDistance.toFixed(2) : '0.00' }}</span>
        <span class="stat-chip-label">Nearest km</span>
      </div>
      <div class="stat-chip stat-chip--indigo">
        <q-icon name="payments" size="18px" class="stat-chip-icon" />
        <span class="stat-chip-value">{{ totalEarnings }}</span>
        <span class="stat-chip-label">Total Earnings</span>
      </div>
    </div>

    <!-- Desktop Table View -->
    <div class="desktop-only">
      <div v-if="typedResult.length === 0" class="empty-state-desktop">
        <div class="empty-icon-wrap">
          <q-icon name="local_shipping" size="48px" color="white" />
        </div>
        <div class="empty-title q-mt-md">No deliveries nearby</div>
        <div class="empty-subtitle q-mt-sm">Delivery opportunities will appear here</div>
      </div>

      <div v-else class="table-section">
        <table class="delivery-table">
          <thead>
            <tr>
              <th>Store</th>
              <th>Distance</th>
              <th>Delivery Charge</th>
              <th>Order Total</th>
              <th>Grand Total</th>
              <th style="text-align:right;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="delivery in typedResult" :key="delivery.optimus_id" class="delivery-row">
              <td>
                <div class="store-cell">
                  <div class="store-icon-wrap">
                    <q-icon name="store" size="16px" color="white" />
                  </div>
                  <div>
                    <div class="store-name">{{ delivery.store?.name || 'Unknown Store' }}</div>
                    <div class="store-address">ID: {{ delivery.reference_id || delivery.optimus_id }}</div>
                  </div>
                </div>
              </td>
              <td>
                <div class="distance-cell">
                  <q-icon name="location_searching" size="14px" class="distance-icon" />
                  {{ Number(delivery.distance).toFixed(2) }} km
                </div>
              </td>
              <td>{{ formatMoney(Number(delivery.delivery_charge)) }}</td>
              <td>{{ formatMoney(delivery.total) }}</td>
              <td>
                <span class="grand-total">{{ formatMoney(delivery.grand_total) }}</span>
              </td>
              <td>
                <div class="action-buttons">
                  <q-btn unelevated dense icon="directions_bike" label="Navigate"
                    :to="`${$route.path}/${delivery.optimus_id}`" size="sm" class="tbl-btn tbl-btn--indigo">
                    <q-tooltip>Navigate to store</q-tooltip>
                  </q-btn>
                </div>
              </td>
            </tr>
          </tbody>
        </table>

        <div class="table-pagination">
          <div class="pagination-info">
            Showing {{ pagination.from || 1 }}&ndash;{{ pagination.to || typedResult.length }}
            of {{ pagination.rowsNumber || typedResult.length }} deliveries
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
          <q-icon name="local_shipping" size="40px" color="white" />
        </div>
        <div class="empty-title q-mt-md">No deliveries nearby</div>
      </div>

      <div v-else class="delivery-cards">
        <div v-for="delivery in typedResult" :key="delivery.optimus_id" class="delivery-card q-mb-sm">
          <div class="mobile-card-accent"></div>
          <div class="mobile-card-body">
            <!-- Card Header -->
            <div class="delivery-card-header">
              <div class="delivery-card-title">
                <div class="store-icon-wrap-sm">
                  <q-icon name="store" size="14px" color="white" />
                </div>
                <div class="store-name">{{ delivery.store?.name || 'Unknown Store' }}</div>
              </div>
              <div class="distance-pill">
                <q-icon name="location_searching" size="12px" class="q-mr-xs" />
                {{ Number(delivery.distance).toFixed(2) }} km
              </div>
            </div>

            <!-- Details -->
            <div class="delivery-card-details">
              <div class="detail-row">
                <span class="detail-label">Delivery Charge</span>
                <span class="detail-value">{{ formatMoney(Number(delivery.delivery_charge)) }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Order Total</span>
                <span class="detail-value">{{ formatMoney(delivery.total) }}</span>
              </div>
              <div class="detail-row detail-row--highlight">
                <span class="detail-label">Grand Total</span>
                <span class="detail-value grand-total">{{ formatMoney(delivery.grand_total) }}</span>
              </div>
            </div>

            <!-- Actions -->
            <div class="delivery-card-actions">
              <q-btn unelevated dense icon="directions_bike" label="Navigate to Store"
                :to="`${$route.path}/${delivery.optimus_id}`" class="action-btn-mobile action-btn-mobile--indigo" />
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
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import { storeToRefs } from 'pinia';
import { CustomerTransactionRow } from 'src/boot/interfaces';
import { getLocation, watchLocation, clearWatch, formatMoney } from 'src/boot/utilities';

const search = ref('');
const watchId = ref<number>(-1);
const locationLoading = ref(false);
const useCommon = useCommonStore();
const { entityQuery, pagination, result } = storeToRefs(useCommon);

entityQuery.value = {
  message: 'Getting deliveries...',
  entity: 'deliveries',
  query: {
    latitude: 1,
    longitude: 1,
    page: pagination.value.page,
    limit: 12,
    with: 'orders.store',
    radius: 15
  },
};

const typedResult = computed<CustomerTransactionRow[]>(() => {
  const data = result.value;
  if (!Array.isArray(data)) return [];
  return data.filter((item): item is CustomerTransactionRow => item != null);
});

const nearestDistance = computed(() => {
  if (!typedResult.value.length) return null;
  const distances = typedResult.value.map(d => Number(d.distance)).filter(n => !isNaN(n));
  return distances.length ? Math.min(...distances) : null;
});

const totalEarnings = computed(() => {
  const total = typedResult.value.reduce((sum, d) => sum + (Number(d.delivery_charge) || 0), 0);
  return total.toFixed(2);
});

const handlePageChange = (page: number) => {
  entityQuery.value.query.page = page;
  onRequest(entityQuery.value);
};

onMounted(() => {
  result.value = []
  entityQuery.value.query.page = 1;
  refreshLocation();
  watchId.value = watchLocation(
    (position) => {
      entityQuery.value.query.latitude = position.coords.latitude;
      entityQuery.value.query.longitude = position.coords.longitude;
      debouncedRefreshLocation();
    },
    (error) => {
      console.error('Location watch error:', error);
    }
  );
});

onUnmounted(() => {
  if (refreshTimeout) {
    clearTimeout(refreshTimeout);
  }
  if (watchId.value !== -1) {
    clearWatch(watchId.value);
  }
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

const refreshLocation = async () => {
  locationLoading.value = true;
  try {
    const position = await getLocation();
    entityQuery.value.query.latitude = position.coords.latitude;
    entityQuery.value.query.longitude = position.coords.longitude;
    onRequest(entityQuery.value, true);
  } catch (err) {
    console.error('Location error:', err);
  } finally {
    locationLoading.value = false;
  }
};

let refreshTimeout: ReturnType<typeof setTimeout> | null = null;

const debouncedRefreshLocation = () => {
  if (refreshTimeout) {
    clearTimeout(refreshTimeout);
  }
  refreshTimeout = setTimeout(() => {
    refreshLocation();
  }, 5000);
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
$yellow: #fbbf24;
$red: #ef4444;
$white: #ffffff;
$muted: rgba(255, 255, 255, 0.55);

// ── Container ────────────────────────────────────────────────────────────────
.delivery-page-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 28px 24px;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  color: $white;
}

// ── Hero Header ──────────────────────────────────────────────────────────────
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
  display: flex;
  align-items: center;
  gap: 10px;
}

.location-refresh-btn {
  color: rgba(255, 255, 255, 0.7);
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid $border;
  transition: all 0.2s;

  &:hover {
    color: $white;
    background: rgba($accent, 0.2);
    border-color: rgba($accent, 0.4);
  }
}

.location-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: $dark-elevated;
  border: 1px solid $border;
  border-radius: 12px;
  padding: 10px 18px;
  font-size: 13px;
  color: $muted;
  font-weight: 600;
}

.location-badge-icon {
  color: $accent;
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

  &--indigo .stat-chip-icon {
    color: #a5b4fc;
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

// ── Desktop Table ───────────────────────────────────────────────────────────
.table-section {
  background: $dark-card;
  border: 1px solid $border;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 8px 40px rgba(0, 0, 0, 0.25);
}

.delivery-table {
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

  tbody tr.delivery-row {
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

.store-cell {
  display: flex;
  align-items: center;
  gap: 10px;
}

.store-icon-wrap {
  width: 34px;
  height: 34px;
  border-radius: 9px;
  background: linear-gradient(135deg, $accent 0%, $accent-2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  box-shadow: 0 3px 10px rgba(99, 102, 241, 0.3);
}

.store-name {
  font-weight: 700;
  font-size: 14px;
  color: $white;
}

.store-address {
  font-size: 12px;
  color: $muted;
  margin-top: 2px;
}

.distance-cell {
  display: flex;
  align-items: center;
  gap: 6px;
  font-weight: 600;
  color: #a5b4fc;
}

.distance-icon {
  color: $accent;
}

.grand-total {
  font-weight: 700;
  color: #6ee7b7;
  font-size: 14px;
}

// ── Action buttons ───────────────────────────────────────────────────────────
.action-buttons {
  display: flex;
  gap: 6px;
  justify-content: flex-end;
}

.tbl-btn {
  border-radius: 9px !important;

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

// ── Pagination ───────────────────────────────────────────────────────────────
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

// ── Mobile Cards ─────────────────────────────────────────────────────────────
.delivery-cards {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.delivery-card {
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

.delivery-card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
}

.delivery-card-title {
  display: flex;
  align-items: center;
  gap: 8px;
  min-width: 0;
}

.store-icon-wrap-sm {
  width: 26px;
  height: 26px;
  border-radius: 7px;
  background: linear-gradient(135deg, $accent 0%, $accent-2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.distance-pill {
  display: inline-flex;
  align-items: center;
  font-size: 11px;
  font-weight: 700;
  padding: 4px 8px;
  border-radius: 20px;
  background: rgba($accent, 0.15);
  color: #a5b4fc;
  border: 1px solid rgba($accent, 0.25);
  white-space: nowrap;
}

.delivery-card-details {
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

  &--highlight {
    padding-top: 4px;
    border-top: 1px solid $border;
  }
}

.detail-label {
  color: $muted;
  font-weight: 500;
}

.detail-value {
  font-weight: 600;
  color: $white;
}

.delivery-card-actions {
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
  .delivery-page-container {
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

  .location-badge {
    width: 100%;
    justify-content: center;
  }

  .stats-row {
    gap: 8px;
  }

  .stat-chip {
    flex: 1;
    justify-content: center;
    padding: 10px 12px;
  }

  .table-section {
    overflow-x: auto;
  }
}
</style>
