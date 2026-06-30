<template>
  <div class="stores-page">

    <!-- Hero Header -->
    <div class="page-hero q-mb-xl">
      <div class="hero-accent-overlay"></div>
      <div class="hero-inner">
        <div class="hero-icon-wrap">
          <q-icon name="store" size="28px" color="white" />
        </div>
        <div class="hero-text">
          <h1 class="hero-title">All Stores</h1>
          <div class="hero-subtitle">Manage your store listings</div>
        </div>
      </div>
      <div class="hero-controls">
        <label class="dark-checkbox">
          <input type="checkbox" v-model="includeDeleted" />
          <span class="checkbox-box">
            <q-icon name="check" size="14px" />
          </span>
          <span class="checkbox-label">Include deleted</span>
        </label>
        <div class="search-wrap">
          <q-icon name="search" size="20px" class="search-icon" />
          <input v-model="search" type="text" placeholder="Search stores..." class="dark-search" />
        </div>
      </div>
    </div>

    <!-- Stats Row -->
    <div class="stats-row q-mb-lg">
      <div class="stat-card">
        <div class="stat-icon-wrap stat-icon-indigo">
          <q-icon name="store" size="20px" color="white" />
        </div>
        <div>
          <div class="stat-value">{{ typedResult.length }}</div>
          <div class="stat-label">Stores on page</div>
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
          <q-icon name="store" size="48px" color="white" />
        </div>
        <div class="empty-title">No stores found</div>
        <div class="empty-subtitle">Try adjusting your search criteria</div>
      </div>

      <div v-else class="table-card">
        <table class="dark-table">
          <thead>
            <tr>
              <th>Store Name</th>
              <th>Mobile</th>
              <th class="th-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="store in typedResult" :key="store.optimus_id">
              <td>
                <router-link :to="`/dashboard/all-stores/${store.optimus_id}`" class="store-link">
                  <div class="store-avatar">
                    <q-icon name="store" size="16px" />
                  </div>
                  <span class="store-name-text">{{ store.name }}</span>
                  <span class="status-dot" :class="store.deleted_at ? 'dot-red' : 'dot-green'"></span>
                </router-link>
              </td>
              <td>
                <div class="phone-cell">
                  <q-icon name="phone" size="16px" />
                  <span>{{ store.mobile || 'N/A' }}</span>
                </div>
              </td>
              <td class="td-center">
                <div class="action-btns">
                  <router-link :to="`/dashboard/all-stores/${store.optimus_id}/store-users`"
                    class="action-pill action-users">
                    <q-icon name="people" size="16px" />
                    Users
                  </router-link>
                  <router-link :to="`/dashboard/all-stores/${store.optimus_id}`" class="action-pill action-edit">
                    <q-icon name="edit_note" size="16px" />
                    Edit
                  </router-link>
                  <button v-if="!store.deleted_at" class="action-pill action-delete" @click="handleDeleteStore(store)">
                    <q-icon name="delete_forever" size="16px" />
                    Delete
                  </button>
                  <button v-else class="action-pill action-restore" @click="handleRestoreStore(store)">
                    <q-icon name="restore" size="16px" />
                    Restore
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Desktop Pagination -->
        <div class="table-pagination">
          <div class="pagination-info">
            Showing {{ pagination.from }}&ndash;{{ pagination.to }} of {{ pagination.rowsNumber }} stores
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
          <q-icon name="store_mall_directory" size="48px" color="white" />
        </div>
        <div class="empty-title">No stores found</div>
        <div class="empty-subtitle">Try adjusting your search</div>
      </div>

      <div v-else class="mobile-cards">
        <div v-for="store in typedResult" :key="store.id" class="m-card">
          <div class="m-card-top">
            <div class="m-card-avatar">
              <q-icon name="store" size="20px" color="white" />
            </div>
            <div class="m-card-info">
              <router-link :to="`/dashboard/all-stores/${store.optimus_id}`" class="m-card-name">
                {{ store.name }}
              </router-link>
              <div class="m-card-status" :class="store.deleted_at ? 'status-archived' : 'status-active'">
                <q-icon :name="store.deleted_at ? 'archive' : 'check_circle'" size="12px" />
                {{ store.deleted_at ? 'Archived' : 'Active' }}
              </div>
            </div>
          </div>

          <div v-if="store.mobile" class="m-card-detail">
            <div class="detail-icon">
              <q-icon name="phone" size="16px" />
            </div>
            <span class="detail-text">{{ store.mobile }}</span>
          </div>

          <div class="m-card-actions">
            <router-link :to="`/dashboard/all-stores/${store.optimus_id}`" class="m-action m-action-edit">
              <q-icon name="edit" size="16px" class="q-mr-xs" />
              Edit
            </router-link>
            <button class="m-action" :class="store.deleted_at ? 'm-action-restore' : 'm-action-delete'"
              @click="store.deleted_at ? handleRestoreStore(store) : handleDeleteStore(store)">
              <q-icon :name="store.deleted_at ? 'restore' : 'delete'" size="16px" class="q-mr-xs" />
              {{ store.deleted_at ? 'Restore' : 'Delete' }}
            </button>
          </div>
        </div>
      </div>

      <!-- Mobile Pagination -->
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
          Showing {{ pagination.from }}&ndash;{{ pagination.to }} of {{ pagination.rowsNumber }} stores
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref, watch } from 'vue';
import { onRequest, firstPage, previousPage, nextPage, lastPage } from 'src/boot/axios-call';
import { storeToRefs } from 'pinia';
import { useCommonStore } from 'src/stores/common';
import { onDeleteEntity, onRestoreEntity } from 'src/boot/services';
import { StoreRow } from 'src/boot/interfaces';

const useCommon = useCommonStore();
const { pagination, result, entityQuery } = storeToRefs(useCommon);

const search = ref('');
const includeDeleted = ref(false);

// Initialize entity query
entityQuery.value = {
  message: 'Getting stores...',
  entity: 'all_stores',
  query: {
    orderBy: 'created_at:desc',
    columns: 'id,name,mobile',
    page: pagination.value.page,
    limit: 12,
  },
};

// Type the result as StoreRow array
const typedResult = result as unknown as StoreRow[];

const columns = [
  {
    name: 'name',
    required: true,
    label: 'Store Name',
    align: 'left' as const,
    field: 'name',
    sortable: true
  },
  {
    name: 'mobile',
    required: true,
    label: 'Mobile',
    align: 'left' as const,
    field: 'mobile',
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
  onDeleteEntity('all_stores', store.optimus_id, store.name);
};

const handleRestoreStore = (store: StoreRow) => {
  onRestoreEntity('all_stores', store.optimus_id, store.name);
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

let searchTimer: ReturnType<typeof setTimeout> | null = null;
watch(search, (newValue) => {
  if (searchTimer) clearTimeout(searchTimer);
  searchTimer = setTimeout(() => {
    if (newValue) {
      entityQuery.value.query.filters = 'name:' + search.value;
    } else {
      delete entityQuery.value.query.filters;
    }
    entityQuery.value.query.page = 1;
    onRequest(entityQuery.value);
  }, 1000);
});

watch(includeDeleted, (newValue) => {
  if (newValue) {
    entityQuery.value.query.deleted = 1;
  } else {
    delete entityQuery.value.query.deleted;
  }
  entityQuery.value.query.page = 1;
  onRequest(entityQuery.value, true);
});
</script>

<style scoped lang="scss">
// ── Dark theme tokens (matching DashboardLayout / ProfilePage) ───────────────
$dark-base: #0f172a;
$dark-card: #1e293b;
$dark-elevated: #273549;
$border: rgba(255, 255, 255, 0.08);
$border-strong: rgba(255, 255, 255, 0.12);
$accent: #6366f1;
$accent-2: #7c3aed;
$green: #10b981;
$green-2: #059669;
$blue: #3b82f6;
$blue-2: #2563eb;
$red: #ef4444;
$yellow: #fbbf24;
$white: #ffffff;
$muted: rgba(255, 255, 255, 0.5);
$muted-2: rgba(255, 255, 255, 0.3);

// ── Page Container ───────────────────────────────────────────────────────────
.stores-page {
  max-width: 1200px;
  margin: 0 auto;
  padding: 28px 24px 60px;
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
  background: linear-gradient(135deg, rgba($accent, 0.18) 0%, rgba($accent-2, 0.10) 60%, transparent 100%);
  pointer-events: none;
}

.hero-inner {
  position: relative;
  display: flex;
  align-items: center;
  padding: 32px 36px 0;
  gap: 16px;
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

// ── Custom dark checkbox ─────────────────────────────────────────────────────
.dark-checkbox {
  display: flex;
  align-items: center;
  gap: 10px;
  cursor: pointer;
  flex-shrink: 0;

  input {
    display: none;
  }

  .checkbox-box {
    width: 22px;
    height: 22px;
    border-radius: 7px;
    border: 2px solid $border-strong;
    background: $dark-elevated;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    color: transparent;
  }

  input:checked+.checkbox-box {
    background: $accent;
    border-color: $accent;
    color: $white;
    box-shadow: 0 4px 12px rgba($accent, 0.35);
  }

  .checkbox-label {
    font-size: 14px;
    color: $muted;
    font-weight: 500;
  }
}

// ── Dark search ──────────────────────────────────────────────────────────────
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

// ── Stats Row ────────────────────────────────────────────────────────────────
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

// ── Empty State ──────────────────────────────────────────────────────────────
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

// ── Table Card ───────────────────────────────────────────────────────────────
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

// ── Store link in table ──────────────────────────────────────────────────────
.store-link {
  display: flex;
  align-items: center;
  gap: 12px;
  text-decoration: none;
  transition: all 0.2s ease;

  &:hover .store-name-text {
    color: $accent;
  }

  &:hover .store-avatar {
    border-color: rgba($accent, 0.4);
    box-shadow: 0 0 0 3px rgba($accent, 0.1);
  }
}

.store-avatar {
  width: 34px;
  height: 34px;
  border-radius: 10px;
  background: rgba($accent, 0.12);
  border: 1px solid rgba($accent, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #a5b4fc;
  flex-shrink: 0;
  transition: all 0.2s ease;
}

.store-name-text {
  font-weight: 700;
  color: #a5b4fc;
  transition: color 0.2s ease;
}

.status-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  flex-shrink: 0;

  &.dot-green {
    background: $green;
    box-shadow: 0 0 8px rgba($green, 0.5);
  }

  &.dot-red {
    background: $red;
    box-shadow: 0 0 8px rgba($red, 0.5);
  }
}

.phone-cell {
  display: flex;
  align-items: center;
  gap: 8px;
  color: $muted;
}

// ── Action pills ─────────────────────────────────────────────────────────────
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

  &.action-users {
    background: rgba($green, 0.1);
    color: #6ee7b7;
    border: 1px solid rgba($green, 0.2);

    &:hover {
      background: rgba($green, 0.2);
      border-color: rgba($green, 0.4);
    }
  }

  &.action-edit {
    background: rgba($accent, 0.12);
    color: #a5b4fc;
    border: 1px solid rgba($accent, 0.2);

    &:hover {
      background: rgba($accent, 0.2);
      border-color: rgba($accent, 0.4);
    }
  }

  &.action-delete {
    background: rgba($red, 0.10);
    color: #fca5a5;
    border: 1px solid rgba($red, 0.2);

    &:hover {
      background: rgba($red, 0.18);
      border-color: rgba($red, 0.35);
    }
  }

  &.action-restore {
    background: rgba($green, 0.10);
    color: #6ee7b7;
    border: 1px solid rgba($green, 0.2);

    &:hover {
      background: rgba($green, 0.18);
      border-color: rgba($green, 0.35);
    }
  }
}

// ── Table Pagination ─────────────────────────────────────────────────────────
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

// ── Mobile Cards ─────────────────────────────────────────────────────────────
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
  display: block;
  font-size: 16px;
  font-weight: 700;
  color: #a5b4fc;
  text-decoration: none;
  word-break: break-word;
  transition: color 0.2s ease;

  &:hover {
    color: $accent;
  }
}

.m-card-status {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  margin-top: 8px;
  padding: 4px 10px;
  border-radius: 20px;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.3px;

  &.status-active {
    background: rgba($green, 0.12);
    color: #6ee7b7;
    border: 1px solid rgba($green, 0.2);
  }

  &.status-archived {
    background: rgba($red, 0.10);
    color: #fca5a5;
    border: 1px solid rgba($red, 0.2);
  }
}

.m-card-detail {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 0 20px 16px;
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

  &.m-action-edit {
    background: linear-gradient(135deg, $accent, $accent-2);
    color: $white;
    box-shadow: 0 4px 16px rgba($accent, 0.3);

    &:hover {
      box-shadow: 0 6px 20px rgba($accent, 0.45);
      transform: translateY(-1px);
    }
  }

  &.m-action-delete {
    background: rgba($red, 0.12);
    color: #fca5a5;
    border: 1px solid rgba($red, 0.2);

    &:hover {
      background: rgba($red, 0.2);
      border-color: rgba($red, 0.35);
    }
  }

  &.m-action-restore {
    background: rgba($green, 0.12);
    color: #6ee7b7;
    border: 1px solid rgba($green, 0.2);

    &:hover {
      background: rgba($green, 0.2);
      border-color: rgba($green, 0.35);
    }
  }
}

// ── Mobile Pagination ────────────────────────────────────────────────────────
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

// ── Responsive ───────────────────────────────────────────────────────────────
@media (max-width: 768px) {
  .stores-page {
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
    padding: 0 16px 12px;
  }
}

@media (max-width: 600px) {
  .stores-page {
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
