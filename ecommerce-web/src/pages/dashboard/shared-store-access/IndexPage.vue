<template>
  <div class="shared-stores-page-container">

    <!-- Hero Header -->
    <div class="page-hero q-mb-xl">
      <div class="hero-accent-overlay"></div>
      <div class="hero-inner">
        <div class="hero-left">
          <div class="hero-icon-wrap">
            <q-icon name="store" size="28px" color="white" />
          </div>
          <div>
            <h1 class="page-title">Shared Store Access</h1>
            <div class="page-subtitle">Manage stores shared with you</div>
          </div>
        </div>
        <div class="hero-right">
          <div class="search-input-wrap">
            <q-icon name="search" size="20px" class="search-icon" />
            <q-input v-model="search" placeholder="Search stores..." outlined dense clearable debounce="1000"
              class="search-field" hide-bottom-space>
            </q-input>
          </div>
        </div>
      </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-section q-mb-lg">
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon-wrap access-icon">
            <q-icon name="store" size="24px" color="white" />
          </div>
          <div class="stat-content">
            <div class="stat-number">{{ typedResult.length }}</div>
            <div class="stat-label">Shared Stores</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon-wrap active-icon">
            <q-icon name="check_circle" size="24px" color="white" />
          </div>
          <div class="stat-content">
            <div class="stat-number">{{typedResult.filter(s => s.store?.name).length}}</div>
            <div class="stat-label">Active Stores</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon-wrap items-icon">
            <q-icon name="shopping_bag" size="24px" color="white" />
          </div>
          <div class="stat-content">
            <div class="stat-number">{{ typedResult.length }}</div>
            <div class="stat-label">Total Items</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Desktop Table View -->
    <div class="desktop-only">
      <!-- Empty State -->
      <div v-if="typedResult.length === 0" class="empty-state-section">
        <div class="empty-card">
          <div class="empty-icon-wrap">
            <q-icon name="store" size="64px" color="white" />
          </div>
          <div class="empty-title">No shared stores found</div>
          <div class="empty-subtitle">Stores shared with you will appear here</div>
        </div>
      </div>

      <!-- Stores Table -->
      <div v-else class="stores-table-section">
        <div class="table-card">
          <table class="stores-table">
            <!-- Header -->
            <thead>
              <tr class="table-header">
                <th class="table-header-cell">Store Information</th>
                <th class="table-header-cell">Contact</th>
                <th class="table-header-cell">Access Level</th>
                <th class="table-header-cell">Actions</th>
              </tr>
            </thead>

            <!-- Body -->
            <tbody>
              <tr v-for="store in typedResult" :key="store.id" class="table-row">
                <!-- Store Info -->
                <td class="table-cell">
                  <div class="store-info-cell">
                    <div class="store-avatar">
                      <q-icon name="store" size="20px" color="white" />
                    </div>
                    <div class="store-details">
                      <router-link :to="`${$route.path}/${store.optimus_id}`" class="store-name">
                        {{ store.store?.name || 'Unknown Store' }}
                      </router-link>
                      <div class="store-meta">Store ID: {{ store.store?.optimus_id || store.id }}</div>
                    </div>
                  </div>
                </td>

                <!-- Contact -->
                <td class="table-cell">
                  <div class="contact-info">
                    <div class="contact-item">
                      <q-icon name="phone" size="14px" class="contact-icon" />
                      <span>{{ store.store?.mobile || 'No Mobile' }}</span>
                    </div>
                  </div>
                </td>

                <!-- Access Level -->
                <td class="table-cell">
                  <div class="access-badge">
                    <q-icon name="admin_panel_settings" size="14px" class="access-icon" />
                    <span>Item Management</span>
                  </div>
                </td>

                <!-- Actions -->
                <td class="table-cell">
                  <div class="actions-cell">
                    <q-btn unelevated dense icon="shopping_bag" :to="`${$route.path}/${store.store?.optimus_id}/items`"
                      class="action-btn items-btn">
                      <q-tooltip>Manage items</q-tooltip>
                    </q-btn>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="pagination-section">
          <div class="pagination-card">
            <div class="pagination-info">
              <span class="pagination-text">
                Showing {{ pagination.from || 1 }}-{{ pagination.to || typedResult.length }}
                of {{ pagination.rowsNumber || typedResult.length }} stores
              </span>
            </div>
            <div class="pagination-controls">
              <q-btn v-if="pagination.lastPage > 2" flat round dense icon="first_page" :disable="pagination.page === 1"
                @click="goToFirstPage" class="pagination-btn">
                <q-tooltip>First page</q-tooltip>
              </q-btn>
              <q-btn flat round dense icon="chevron_left" :disable="pagination.page === 1" @click="goToPreviousPage"
                class="pagination-btn">
                <q-tooltip>Previous page</q-tooltip>
              </q-btn>
              <div class="page-indicator">
                <span class="current-page">{{ pagination.page }}</span>
                <span class="page-separator">/</span>
                <span class="total-pages">{{ pagination.lastPage }}</span>
              </div>
              <q-btn flat round dense icon="chevron_right" :disable="pagination.page === pagination.lastPage"
                @click="goToNextPage" class="pagination-btn">
                <q-tooltip>Next page</q-tooltip>
              </q-btn>
              <q-btn v-if="pagination.lastPage > 2" flat round dense icon="last_page"
                :disable="pagination.page === pagination.lastPage" @click="goToLastPage" class="pagination-btn">
                <q-tooltip>Last page</q-tooltip>
              </q-btn>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Mobile Card View -->
    <div class="mobile-only">
      <!-- Empty State -->
      <div v-if="typedResult.length === 0" class="empty-state-mobile">
        <div class="empty-icon-mobile">
          <q-icon name="store" size="48px" color="white" />
        </div>
        <div class="empty-title-mobile">No shared stores</div>
        <div class="empty-subtitle-mobile">Stores shared with you will appear here</div>
      </div>

      <!-- Store Cards -->
      <div v-else class="stores-cards-mobile">
        <q-card v-for="store in typedResult" :key="store.id" flat class="store-card-mobile q-mb-md">
          <q-card-section class="store-card-header">
            <div class="store-avatar-mobile">
              <q-icon name="store" size="24px" color="white" />
            </div>
            <div class="store-info-mobile">
              <router-link :to="`${$route.path}/${store.optimus_id}`" class="store-name-mobile">
                {{ store.store?.name || 'Unknown Store' }}
              </router-link>
              <div class="store-meta-mobile">ID: {{ store.store?.optimus_id || store.id }}</div>
            </div>
            <div class="access-badge-mobile">
              <q-icon name="admin_panel_settings" size="16px" class="access-icon-mobile" />
            </div>
          </q-card-section>

          <q-separator class="store-card-divider" />

          <q-card-section class="store-card-body">
            <div class="store-meta-list">
              <div class="meta-item-mobile">
                <q-icon name="phone" size="14px" class="meta-icon" />
                <span>{{ store.store?.mobile || 'No Mobile' }}</span>
              </div>
              <div class="meta-item-mobile">
                <q-icon name="admin_panel_settings" size="14px" class="meta-icon" />
                <span>Item Management</span>
              </div>
            </div>
          </q-card-section>

          <q-card-actions class="store-card-actions">
            <q-btn unelevated icon="shopping_bag" label="Items" :to="`${$route.path}/${store.store?.optimus_id}/items`"
              class="mobile-action-btn items-btn-mobile" />
          </q-card-actions>
        </q-card>
      </div>

      <!-- Mobile Pagination -->
      <div v-if="typedResult.length > 0" class="mobile-pagination">
        <q-pagination v-model="pagination.page" :max="pagination.lastPage" :max-pages="5" direction-links boundary-links
          color="primary" @update:model-value="handlePageChange" />
      </div>
    </div>
  </div>
</template>
<style scoped lang="scss">
// ── Dark theme tokens (matching DashboardLayout and ProfilePage) ─────────────────
$dark-base: #0f172a;
$dark-card: #1e293b;
$dark-elevated: #273549;
$border: rgba(255, 255, 255, 0.08);
$accent: #6366f1;
$accent-2: #7c3aed;
$green: #10b981;
$green-2: #059669;
$blue: #3b82f6;
$blue-2: #2563eb;
$yellow: #fbbf24;
$yellow-2: #f59e0b;
$red: #ef4444;
$red-2: #dc2626;
$white: #ffffff;
$muted: rgba(255, 255, 255, 0.5);
$muted-2: rgba(255, 255, 255, 0.3);

// ── Container ────────────────────────────────────────────────────────────────
.shared-stores-page-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 28px 24px;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  color: $white;
  position: relative;
}

// ── Hero Header ───────────────────────────────────────────────────────────────
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
  color: #ffffff !important;
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
  transition: all 0.2s ease;
  width: 320px;

  &:focus-within {
    border-color: rgba($accent, 0.4);
    box-shadow: 0 0 0 3px rgba($accent, 0.1);
  }
}

.search-icon {
  color: $muted;
  margin-right: 12px;
}

.search-field {
  flex: 1;

  :deep(.q-field__control) {
    background: transparent !important;
    border: none !important;
    color: $white !important;
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

// ── Statistics Section ─────────────────────────────────────────────────────────
.stats-section {
  margin-bottom: 32px;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 20px;
}

.stat-card {
  background: $dark-card;
  border: 1px solid $border;
  border-radius: 16px;
  padding: 24px;
  display: flex;
  align-items: center;
  gap: 16px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
  transition: all 0.2s ease;

  &:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
    border-color: rgba($accent, 0.2);
  }
}

.stat-icon-wrap {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;

  &.access-icon {
    background: linear-gradient(135deg, $accent 0%, $accent-2 100%);
    box-shadow: 0 4px 16px rgba(99, 102, 241, 0.3);
  }

  &.active-icon {
    background: linear-gradient(135deg, $green 0%, $green-2 100%);
    box-shadow: 0 4px 16px rgba(16, 185, 129, 0.3);
  }

  &.items-icon {
    background: linear-gradient(135deg, $blue 0%, $blue-2 100%);
    box-shadow: 0 4px 16px rgba(59, 130, 246, 0.3);
  }
}

.stat-content {
  flex: 1;
}

.stat-number {
  font-size: 28px;
  font-weight: 800;
  color: $white;
  line-height: 1;
  margin-bottom: 4px;
}

.stat-label {
  font-size: 12px;
  color: $muted;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

// ── Empty States ──────────────────────────────────────────────────────────────
.empty-state-section {
  display: flex;
  justify-content: center;
  padding: 80px 24px;
}

.empty-card {
  background: $dark-card;
  border: 1px solid $border;
  border-radius: 24px;
  padding: 60px 48px;
  text-align: center;
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.25);
  max-width: 480px;
}

.empty-icon-wrap {
  width: 120px;
  height: 120px;
  border-radius: 24px;
  background: linear-gradient(135deg, rgba($red, 0.2) 0%, rgba($red-2, 0.1) 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 24px;
  box-shadow: 0 8px 32px rgba($red, 0.2);
}

.empty-title {
  font-size: 24px;
  font-weight: 800;
  color: $white;
  margin-bottom: 8px;
}

.empty-subtitle {
  font-size: 14px;
  color: $muted;
  line-height: 1.5;
}

// ── Table Section ────────────────────────────────────────────────────────────
.stores-table-section {
  background: $dark-card;
  border: 1px solid $border;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 8px 40px rgba(0, 0, 0, 0.25);
}

.table-card {
  border-radius: 20px;
  overflow: hidden;
}

.stores-table {
  width: 100%;
  background: transparent;
  border-collapse: collapse;

  thead {
    background: $dark-elevated;
  }

  th {
    font-size: 12px;
    font-weight: 700;
    color: $muted;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 16px 20px;
    text-align: left;
    border: none;
  }

  tbody tr {
    transition: background-color 0.2s ease;
    border-bottom: 1px solid $border;

    &:hover {
      background: rgba($accent, 0.04);
    }
  }

  td {
    padding: 20px;
    border: none;
    vertical-align: middle;
  }
}

// ── Store Info Cell ────────────────────────────────────────────────────────────
.store-info-cell {
  display: flex;
  align-items: center;
  gap: 14px;
}

.store-avatar {
  width: 40px;
  height: 40px;
  border-radius: 12px;
  background: linear-gradient(135deg, $accent 0%, $accent-2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
  flex-shrink: 0;
}

.store-details {
  flex: 1;
}

.store-name {
  font-size: 14px;
  font-weight: 700;
  color: $accent;
  text-decoration: none;
  transition: color 0.2s ease;
  display: block;
  line-height: 1.3;

  &:hover {
    color: $accent-2;
  }
}

.store-meta {
  font-size: 12px;
  color: $muted;
  margin-top: 2px;
}

// ── Contact Info ───────────────────────────────────────────────────────────────
.contact-info {
  display: flex;
  align-items: center;
}

.contact-item {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  color: $white;
}

.contact-icon {
  color: $muted;
}

// ── Access Badge ───────────────────────────────────────────────────────────────
.access-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: rgba($blue, 0.15);
  border: 1px solid rgba($blue, 0.2);
  border-radius: 8px;
  padding: 6px 12px;
  font-size: 12px;
  color: $blue;
  font-weight: 600;
}

.access-icon {
  color: $blue;
}

// ── Actions Cell ──────────────────────────────────────────────────────────────
.actions-cell {
  display: flex;
  align-items: center;
  gap: 8px;
}

.action-btn {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  font-size: 16px;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;

  &.items-btn {
    background: rgba($blue, 0.15) !important;
    color: $blue !important;
    border: 1px solid rgba($blue, 0.2) !important;

    &:hover {
      background: rgba($blue, 0.25) !important;
      border-color: rgba($blue, 0.4) !important;
      transform: translateY(-1px);
    }
  }
}

// ── Pagination ───────────────────────────────────────────────────────────────
.pagination-section {
  padding: 20px 24px;
  background: $dark-elevated;
  border-top: 1px solid $border;
}

.pagination-card {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.pagination-info {
  flex: 1;
}

.pagination-text {
  font-size: 13px;
  color: $muted;
  font-weight: 500;
}

.pagination-controls {
  display: flex;
  align-items: center;
  gap: 8px;
}

.pagination-btn {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  background: rgba(255, 255, 255, 0.06) !important;
  color: $muted !important;
  border: 1px solid $border !important;
  transition: all 0.2s ease;

  &:hover:not(:disabled) {
    background: rgba(255, 255, 255, 0.12) !important;
    color: $white !important;
    border-color: rgba(255, 255, 255, 0.14) !important;
  }

  &:disabled {
    opacity: 0.4;
  }
}

.page-indicator {
  display: flex;
  align-items: center;
  gap: 4px;
  margin: 0 12px;
  font-size: 13px;
  font-weight: 600;
}

.current-page {
  color: $white;
}

.page-separator {
  color: $muted;
}

.total-pages {
  color: $muted;
}

// ── Mobile Styles ─────────────────────────────────────────────────────────────
.empty-state-mobile {
  text-align: center;
  padding: 60px 24px;
}

.empty-icon-mobile {
  width: 80px;
  height: 80px;
  border-radius: 20px;
  background: linear-gradient(135deg, rgba($red, 0.2) 0%, rgba($red-2, 0.1) 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 20px;
  box-shadow: 0 6px 24px rgba($red, 0.2);
}

.empty-title-mobile {
  font-size: 20px;
  font-weight: 800;
  color: $white;
  margin-bottom: 6px;
}

.empty-subtitle-mobile {
  font-size: 13px;
  color: $muted;
  margin-bottom: 24px;
}

// ── Mobile Store Cards ─────────────────────────────────────────────────────────
.stores-cards-mobile {
  padding: 0 4px;
}

.store-card-mobile {
  background: $dark-card !important;
  border: 1px solid $border !important;
  border-radius: 16px !important;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2) !important;
  overflow: hidden;
}

.store-card-header {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 20px 20px 16px;
}

.store-avatar-mobile {
  width: 48px;
  height: 48px;
  border-radius: 14px;
  background: linear-gradient(135deg, $accent 0%, $accent-2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
  flex-shrink: 0;
}

.store-info-mobile {
  flex: 1;
}

.store-name-mobile {
  font-size: 16px;
  font-weight: 700;
  color: $accent;
  text-decoration: none;
  display: block;
  line-height: 1.3;

  &:hover {
    color: $accent-2;
  }
}

.store-meta-mobile {
  font-size: 13px;
  color: $muted;
  margin-top: 4px;
}

.access-badge-mobile {
  flex-shrink: 0;
}

.access-icon-mobile {
  color: $blue;
}

.store-card-divider {
  background: $border !important;
  margin: 0;
}

.store-card-body {
  padding: 0 20px 16px;
}

.store-meta-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.meta-item-mobile {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  color: $white;
}

.meta-icon {
  color: $muted;
}

.store-card-actions {
  padding: 16px 20px;
  gap: 8px;
  display: flex;
}

.mobile-action-btn {
  flex: 1;
  height: 40px !important;
  border-radius: 10px !important;
  font-weight: 600 !important;
  font-size: 13px !important;

  &.items-btn-mobile {
    background: rgba($blue, 0.15) !important;
    color: $blue !important;
    border: 1px solid rgba($blue, 0.2) !important;

    &:hover {
      background: rgba($blue, 0.25) !important;
      border-color: rgba($blue, 0.4) !important;
    }
  }
}

.mobile-pagination {
  display: flex;
  justify-content: center;
  padding: 24px 0;
}

// ── Responsive Design ─────────────────────────────────────────────────────────
@media (max-width: 768px) {
  .shared-stores-page-container {
    padding: 16px 12px;
  }

  .hero-inner {
    flex-direction: column;
    gap: 20px;
    padding: 24px 20px;
    text-align: center;
  }

  .hero-left {
    flex-direction: column;
    gap: 16px;
  }

  .page-title {
    font-size: 24px;
  }

  .search-input-wrap {
    width: 100%;
  }

  .stats-grid {
    grid-template-columns: 1fr;
    gap: 16px;
  }

  .table-card {
    border-radius: 16px;
  }

  .store-card-mobile {
    margin: 0 0 16px 0;
  }
}
</style>

<script setup lang="ts">
import { onMounted, ref, watch } from 'vue';
import { onRequest, firstPage, previousPage, nextPage, lastPage } from 'src/boot/axios-call';
import { storeToRefs } from 'pinia';
import { useCommonStore } from 'src/stores/common';
import { onDeleteEntity } from 'src/boot/services';
import { StoreRow } from 'src/boot/interfaces';

const useCommon = useCommonStore();
const { pagination, result, entityQuery } = storeToRefs(useCommon);

const search = ref('');

entityQuery.value = {
  message: 'Getting store access...',
  entity: 'store-users',
  query: {
    orderBy: 'created_at:desc',
    page: pagination.value.page,
    limit: 10,
    with: 'store'
  },
};

const typedResult = result as unknown as StoreRow[];

const columns = [
  {
    name: 'name',
    required: true,
    label: 'Name',
    align: 'left' as const,
    field: (v: StoreRow) => v.name,
    sortable: true
  },
  {
    name: 'mobile',
    required: true,
    label: 'Mobile',
    align: 'left' as const,
    field: (v: StoreRow) => v.mobile,
    sortable: true
  },
  {
    name: 'actions',
    required: true,
    label: 'Actions',
    align: 'center' as const,
    field: ''
  }
];

const handleDeleteStore = (store: StoreRow) => {
  onDeleteEntity('stores', store.optimus_id, store.name);
};

const handlePageChange = (page: number) => {
  entityQuery.value.query.page = page;
  onRequest(entityQuery.value);
};

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

onMounted(() => {
  result.value = [];
  entityQuery.value.query.page = 1;
  onRequest(entityQuery.value, true);
});

watch(search, (newValue) => {
  if (newValue) {
    entityQuery.value.query.filters = 'name:' + search.value;
  } else {
    delete entityQuery.value.query.filters;
  }
  entityQuery.value.query.page = 1;
  onRequest(entityQuery.value);
});
</script>

<style scoped lang="scss">
@import 'src/css/dashboard/all-stores/index.scss';

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.header-title-section {
  display: flex;
  align-items: center;
}

.search-input {
  min-width: 300px;
}

.stores-table {
  width: 100%;
}

.store-name-link {
  text-decoration: none;
  color: inherit;
  font-weight: normal;
  font-size: 14px;

  &:hover {
    color: #1976d2;
  }
}

.action-buttons {
  display: flex;
  gap: 4px;
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

.empty-state-desktop {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 80px 24px;
}

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px 24px;
}

.stores-cards {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.store-card {
  border-radius: 8px;
}

.store-card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.store-card-title {
  display: flex;
  align-items: center;
}

.store-name-link {
  text-decoration: none;
  color: inherit;
  font-weight: normal;
  font-size: 14px;

  &:hover {
    color: #1976d2;
  }
}

.store-card-actions {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.action-btn-mobile {
  flex: 1;
  min-width: 80px;
}

.mobile-pagination {
  display: flex;
  justify-content: center;
}

@media (max-width: 768px) {
  .header-content {
    flex-direction: column;
    align-items: stretch;
    gap: 16px;
  }

  .search-input {
    min-width: 100%;
  }
}
</style>
