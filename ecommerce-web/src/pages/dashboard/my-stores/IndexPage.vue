<template>
  <div class="stores-page-container">
    <!-- Header Section -->
    <div class="page-header q-mb-lg">
      <div class="header-bg-accent"></div>
      <div class="header-content">
        <div class="header-title-section">
          <div class="header-icon-wrap">
            <q-icon name="store" size="26px" color="white" />
          </div>
          <div>
            <h2 class="page-title">My Stores</h2>
            <div class="page-subtitle">Manage your registered stores</div>
          </div>
        </div>
        <div class="header-actions">
          <q-input v-model="search" placeholder="Search stores..." outlined dense clearable debounce="1000"
            class="search-input">
            <template v-slot:prepend>
              <q-icon name="search" color="grey-5" />
            </template>
          </q-input>
        </div>
      </div>
    </div>

    <!-- Desktop Table View -->
    <div class="desktop-only">
      <div v-if="typedResult.length === 0" class="empty-state-desktop">
        <div class="empty-icon-wrap">
          <q-icon name="store" size="48px" color="white" />
        </div>
        <div class="empty-title q-mt-md">No stores found</div>
        <div class="empty-subtitle q-mt-sm">Try adjusting your search criteria</div>
      </div>
      <q-table v-else flat :rows="typedResult" :columns="columns" row-key="id" :pagination="{ rowsPerPage: 0 }"
        class="stores-table">
        <template v-slot:body-cell-name="props">
          <q-td :props="props">
            <router-link :to="`${$route.path}/${props.row.optimus_id}`" class="store-name-link">
              {{ props.row.name }}
            </router-link>
          </q-td>
        </template>

        <template v-slot:body-cell-mobile="props">
          <q-td :props="props">
            {{ props.row.mobile || 'N/A' }}
          </q-td>
        </template>

        <template v-slot:body-cell-actions="props">
          <q-td :props="props">
            <div class="action-buttons">
              <q-btn unelevated dense color="secondary" icon="people"
                :to="`${$route.path}/${props.row.optimus_id}/users`" size="md" class="q-mr-xs">
                <q-tooltip>User Management</q-tooltip>
              </q-btn>
              <q-btn unelevated dense color="primary" icon="edit_note" :to="`${$route.path}/${props.row.optimus_id}`"
                size="md" class="q-mr-xs">
                <q-tooltip>Edit Store</q-tooltip>
              </q-btn>
              <q-btn unelevated dense color="negative" icon="delete_forever" @click="handleDeleteStore(props.row)"
                size="md">
                <q-tooltip>Delete Store</q-tooltip>
              </q-btn>
            </div>
          </q-td>
        </template>

        <template v-slot:bottom>
          <div class="table-pagination">
            <div class="pagination-info">
              Showing {{ pagination.from }} - {{ pagination.to }} of {{ pagination.rowsNumber }} stores
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
          <q-icon name="store" size="40px" color="white" />
        </div>
        <div class="empty-title q-mt-md">No stores found</div>
      </div>
      <div v-else class="stores-cards">
        <q-card v-for="store in typedResult" :key="store.id" flat class="store-card q-mb-md">
          <q-card-section>
            <div class="store-card-header">
              <div class="store-card-title">
                <div class="card-store-icon">
                  <q-icon name="store" size="18px" color="white" />
                </div>
                <router-link :to="`${$route.path}/${store.optimus_id}`" class="store-name-link">
                  {{ store.name }}
                </router-link>
              </div>
            </div>
            <div class="store-card-actions q-mt-md">
              <q-btn unelevated dense color="secondary" icon="list" label="Items"
                :to="`${$route.path}/${store.optimus_id}/items`" class="action-btn-mobile" />
              <q-btn unelevated dense color="primary" icon="edit_note" label="Edit"
                :to="`${$route.path}/${store.optimus_id}`" class="action-btn-mobile" />
              <q-btn unelevated dense color="negative" icon="delete_forever" label="Delete"
                @click="handleDeleteStore(store)" class="action-btn-mobile" />
            </div>
          </q-card-section>
        </q-card>
      </div>
      <!-- Mobile Pagination -->
      <div v-if="typedResult.length > 0" class="mobile-pagination q-mt-md">
        <q-pagination v-model="pagination.page" :max="pagination.lastPage" :max-pages="5" direction-links boundary-links
          color="primary" @update:model-value="handlePageChange" />
      </div>
    </div>
  </div>
</template>
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
  message: 'Getting stores...',
  entity: 'my-stores',
  query: {
    orderBy: 'created_at:desc',
    page: pagination.value.page,
    limit: 10,
  },
};

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
  color: $white !important;
}

// ── Header ─────────────────────────────────────────────────────────────────
.page-header {
  position: relative;
  background: $dark-card !important;
  border-radius: 20px !important;
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
  font-size: 18px;
  font-weight: 700;
  color: $white;
}

.empty-subtitle {
  font-size: 14px;
  color: $muted;
}

// ── Desktop table ──────────────────────────────────────────────────────────
.stores-table {
  width: 100%;

  :deep(.q-table__container) {
    background: $dark-card !important;
    border: 1px solid $border !important;
    border-radius: 20px !important;
    box-shadow: 0 8px 40px rgba(0, 0, 0, 0.25) !important;
    overflow: hidden !important;
  }

  :deep(.q-table) {
    border: none !important;
  }

  :deep(.q-table td),
  :deep(.q-table th) {
    border-right: none !important;
  }

  :deep(.q-table thead tr th) {
    background: $dark-elevated !important;
    color: $muted !important;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    padding: 16px 24px;
    border-bottom: 1px solid $border !important;
  }

  :deep(.q-table tbody tr) {
    background: $dark-card !important;
  }

  :deep(.q-table tbody tr td) {
    background: transparent !important;
    color: $white !important;
    font-size: 14px;
    padding: 16px 24px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.04) !important;
  }

  :deep(.q-table tbody tr:last-child td) {
    border-bottom: none !important;
  }

  :deep(.q-table tbody tr:hover) {
    background: $dark-card !important;
  }

  :deep(.q-table__bottom) {
    background: $dark-elevated !important;
    border-top: 1px solid $border !important;
  }
}

.store-name-link {
  text-decoration: none;
  color: $white;
  font-weight: 600;
  font-size: 14px;
  transition: color 0.2s;

  &:hover {
    color: #a5b4fc;
  }
}

.action-buttons {
  display: flex;
  gap: 6px;
  justify-content: flex-end;
}

// ── Pagination footer ──────────────────────────────────────────────────────
.table-pagination {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 14px 24px;
  color: $white;
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
  gap: 12px;
}

.store-card {
  background: $dark-card !important;
  border: 1px solid $border !important;
  border-radius: 16px !important;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25) !important;
  overflow: hidden;
  position: relative;
  transition: transform 0.2s, box-shadow 0.2s;

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
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3) !important;
    border-color: rgba(99, 102, 241, 0.3) !important;
  }

  :deep(.q-card__section) {
    background: transparent !important;
    color: $white !important;
  }
}

.store-card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.store-card-title {
  display: flex;
  align-items: center;
  gap: 12px;
  flex: 1;
}

.card-store-icon {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  background: linear-gradient(135deg, $accent 0%, $accent-2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  box-shadow: 0 3px 10px rgba(99, 102, 241, 0.35);
}

.store-card-actions {
  display: grid;
  grid-template-columns: 1fr 1fr 1fr;
  gap: 8px;
  padding-top: 14px;
  border-top: 1px solid $border;
  margin-top: 4px;
}

.action-btn-mobile {
  border-radius: 10px !important;
  font-weight: 700 !important;
  font-size: 12px !important;
  height: 36px !important;
  text-transform: none !important;
  letter-spacing: 0 !important;
}

.mobile-pagination {
  display: flex;
  justify-content: center;
  padding: 20px 0 4px;
  background: transparent !important;
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

  .header-title-section {
    justify-content: flex-start;
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
/* Table container */
.stores-page-container .stores-table .q-table__container {
  background: #1e293b !important;
  border: 1px solid rgba(255, 255, 255, 0.08) !important;
  border-radius: 20px !important;
  overflow: hidden !important;
}

/* Strip all table borders */
.stores-page-container .stores-table .q-table,
.stores-page-container .stores-table .q-table td,
.stores-page-container .stores-table .q-table th,
.stores-page-container .stores-table .q-table tr {
  border: none !important;
  outline: none !important;
}

/* Header row */
.stores-page-container .stores-table thead tr th {
  background: #273549 !important;
  color: rgba(255, 255, 255, 0.55) !important;
  border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
}

/* Body rows — no hover flash */
.stores-page-container .stores-table tbody tr,
.stores-page-container .stores-table tbody tr:hover {
  background: #1e293b !important;
  cursor: default;
}

.stores-page-container .stores-table tbody tr td {
  background: transparent !important;
  color: #ffffff !important;
  border-bottom: 1px solid rgba(255, 255, 255, 0.05) !important;
}

.stores-page-container .stores-table tbody tr:last-child td {
  border-bottom: none !important;
}

/* Pagination footer */
.stores-page-container .stores-table .q-table__bottom {
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
