<template>
  <div class="items-page-container">
    <!-- Header Section -->
    <div class="page-header q-mb-lg">
      <div class="header-bg-accent"></div>
      <div class="header-content">
        <div class="header-left">
          <q-btn flat round dense icon="arrow_back" @click="router.back()" class="header-back-btn">
            <q-tooltip>Back to Store</q-tooltip>
          </q-btn>
          <div class="header-icon-wrap">
            <q-icon name="inventory_2" size="26px" color="white" />
          </div>
          <div>
            <h2 class="page-title">{{ store.name || 'Store Items' }}</h2>
            <div class="page-subtitle">Manage store items and inventory</div>
          </div>
        </div>
        <div class="header-actions">
          <q-input v-model="search" placeholder="Search items..." outlined dense clearable debounce="1000"
            class="search-input">
            <template v-slot:prepend>
              <q-icon name="search" color="grey-5" />
            </template>
          </q-input>
          <q-select outlined v-model="selectedCategory" :options="categories" label="Category" hide-bottom-space
            use-input dense clearable class="category-select" @update:model-value="handleCategoryChange">
            <template v-slot:prepend>
              <q-icon name="category" color="grey-5" />
            </template>
            <template v-slot:append>
              <q-icon v-if="selectedCategory" name="close" @click.stop.prevent="handleCategoryChange('')"
                class="cursor-pointer" color="grey-5" />
            </template>
          </q-select>
        </div>
      </div>
    </div>

    <!-- Desktop Table View -->
    <div class="desktop-only">
      <div v-if="typedResult.length === 0" class="empty-state-desktop">
        <div class="empty-icon-wrap">
          <q-icon name="inventory_2" size="48px" color="white" />
        </div>
        <div class="empty-title q-mt-md">No items found</div>
        <div class="empty-subtitle q-mt-sm">Try adjusting your search criteria</div>
      </div>
      <q-table v-else flat :rows="typedResult" :columns="columns" row-key="optimus_id" class="items-table"
        :rows-per-page-options="[0]" hide-pagination>
        <template v-slot:body-cell-name="props">
          <q-td :props="props">
            <router-link :to="`${$route.path}/${props.row.optimus_id}`" class="item-name-link">
              {{ props.row.name }}
            </router-link>
          </q-td>
        </template>

        <template v-slot:body-cell-actions="props">
          <q-td :props="props">
            <div class="action-buttons">
              <q-btn unelevated dense icon="attach_money"
                :to="`${$route.path}/${props.row.optimus_id}/item-prices?filters=store_id:${store.optimus_id}`"
                size="md" class="tbl-btn tbl-btn--green">
                <q-tooltip>Item Prices</q-tooltip>
              </q-btn>
              <q-btn unelevated dense icon="edit_note"
                :to="`${$route.path}/${props.row.optimus_id}?filters=store_id:${store.optimus_id}`" size="md"
                class="tbl-btn tbl-btn--indigo">
                <q-tooltip>Edit Item</q-tooltip>
              </q-btn>
              <q-btn unelevated dense icon="delete_forever" @click="handleDeleteItem(props.row)" size="md"
                class="tbl-btn tbl-btn--red">
                <q-tooltip>Delete Item</q-tooltip>
              </q-btn>
            </div>
          </q-td>
        </template>

        <template v-slot:bottom>
          <div class="table-pagination">
            <div class="pagination-info">
              Showing {{ pagination.from }} &ndash; {{ pagination.to }} of {{ pagination.rowsNumber }} items
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
        </template>
      </q-table>
    </div>

    <!-- Mobile Card View -->
    <div class="mobile-only">
      <div v-if="typedResult.length === 0" class="empty-state">
        <div class="empty-icon-wrap">
          <q-icon name="inventory_2" size="40px" color="white" />
        </div>
        <div class="empty-title q-mt-md">No items found</div>
      </div>
      <div v-else class="items-cards">
        <div v-for="item in typedResult" :key="item.optimus_id" class="item-card q-mb-sm">
          <div class="mobile-card-accent"></div>
          <div class="mobile-card-body">
            <div class="item-card-header">
              <div class="item-card-title">
                <div class="card-item-icon">
                  <q-icon name="inventory_2" size="16px" color="white" />
                </div>
                <router-link :to="`${$route.path}/${item.optimus_id}`" class="item-name-link">
                  {{ item.name }}
                </router-link>
              </div>
            </div>
            <div class="item-card-actions">
              <q-btn unelevated dense icon="attach_money" label="Prices"
                :to="`${$route.path}/${item.optimus_id}/item-prices?filters=store_id:${store.optimus_id}`"
                class="action-btn-mobile action-btn-mobile--green" />
              <q-btn unelevated dense icon="edit_note" label="Edit"
                :to="`${$route.path}/${item.optimus_id}?filters=store_id:${store.optimus_id}`"
                class="action-btn-mobile action-btn-mobile--indigo" />
              <q-btn unelevated dense icon="delete_forever" label="Delete" @click="handleDeleteItem(item)"
                class="action-btn-mobile action-btn-mobile--red" />
            </div>
          </div>
        </div>
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
import { useItemStore } from 'src/stores/item';
import { ref, onBeforeMount, watch, onMounted } from 'vue';
import { show, get, onRequest, firstPage, previousPage, nextPage, lastPage } from 'src/boot/axios-call';
import { useRoute, useRouter } from 'vue-router';
import { storeToRefs } from 'pinia';
import { useCommonStore } from 'src/stores/common';
import { onDeleteEntity } from 'boot/services';
import type { StoreInterface, CategoryInterface, ResultInterface } from 'src/boot/interfaces';

const useCommon = useCommonStore();
const { pagination, result, entityQuery } = storeToRefs(useCommon);
const useItem = useItemStore();
const { searchString, selectedCategory } = storeToRefs(useItem);
const route = useRoute();
const router = useRouter();
const store = ref<Partial<StoreInterface>>({
  optimus_id: '',
  name: '',
  latitude: 0,
  longitude: 0,
});

const search = ref('');
onBeforeMount(async () => {
  result.value = [];
  store.value = await show({
    entity: 'shared-store-access',
    optimus_id: Number(route.params.id),
  });
  await requestItems();
  getCategories();
});

onMounted(() => {
  entityQuery.value.query.page = 1;
});

const categories = ref<CategoryInterface[]>([]);
const getCategories = async () => {
  const cat = await get<ResultInterface>(
    {
      entity: 'categories',
      query: {
        orderBy: 'name:asc',
        type: 'collection',
        whereHas: 'items:store_id;' + store.value.id,
      },
    },
    false
  );

  categories.value = cat.data.data;
};

const requestItems = async () => {
  let filters = `store_id:${store.value.optimus_id}`;
  if (searchString.value) {
    filters =
      `store_id:${store.value.optimus_id},` + 'name:' + searchString.value;
  }

  if (selectedCategory.value) {
    const categoryValue = selectedCategory.value as unknown as CategoryInterface;
    const categoryId = typeof categoryValue === 'object' && 'id' in categoryValue
      ? categoryValue.id
      : selectedCategory.value;
    if (categoryId) {
      filters += ',category_id:' + categoryId;
    }
  }

  entityQuery.value = {
    message: 'Getting items...',
    entity: 'shared-item-access',
    query: {
      filters: filters,
      page: pagination.value.page,
      limit: 12,
    },
  };

  onRequest(entityQuery.value, true);
};

watch(selectedCategory, () => {
  requestItems();
});

watch(search, (newValue) => {
  useItem.setSearchString(newValue || '');
  requestItems();
});

const typedResult = result as unknown as Array<{ optimus_id: number; name: string }>;

const columns = [
  {
    name: 'name',
    required: true,
    label: 'Item Name',
    align: 'left' as const,
    field: 'name',
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

const handleDeleteItem = (item: { optimus_id: number; name: string }) => {
  onDeleteEntity('items', item.optimus_id, item.name);
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
const handleCategoryChange = (value: CategoryInterface | string | null) => {
  selectedCategory.value = value as string;
  requestItems();
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
$green: #10b981;
$green-2: #059669;
$white: #ffffff;
$muted: rgba(255, 255, 255, 0.5);

// ── Container ──────────────────────────────────────────────────────────────
.items-page-container {
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
  gap: 20px;
  padding: 24px 28px;
  flex-wrap: wrap;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 14px;
}

.header-back-btn {
  color: $muted !important;
  border: 1px solid $border !important;
  border-radius: 10px !important;
  transition: color 0.2s, background 0.2s !important;

  &:hover {
    color: $white !important;
    background: rgba(255, 255, 255, 0.07) !important;
  }
}

.header-icon-wrap {
  width: 50px;
  height: 50px;
  border-radius: 14px;
  background: linear-gradient(135deg, $accent 0%, $accent-2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 16px rgba(99, 102, 241, 0.4);
  flex-shrink: 0;
}

.page-title {
  font-size: 22px !important;
  font-weight: 800 !important;
  color: $white !important;
  margin: 0 0 3px !important;
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
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
}

// ── Dark field mixin shared by search + category ───────────────────────────
%dark-field {
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

.search-input {
  @extend %dark-field;
  min-width: 240px;
  max-width: 340px;
}

.category-select {
  @extend %dark-field;
  min-width: 180px;
  max-width: 260px;
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

// ── Desktop table ──────────────────────────────────────────────────────────
.items-table {
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

.item-name-link {
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

// Compact icon-only buttons in the table
.tbl-btn {
  border-radius: 9px !important;
  width: 34px !important;
  height: 34px !important;

  &--green {
    background: rgba(16, 185, 129, 0.15) !important;
    color: #6ee7b7 !important;
    border: 1px solid rgba(16, 185, 129, 0.3) !important;

    &:hover {
      background: rgba(16, 185, 129, 0.28) !important;
      color: $white !important;
    }
  }

  &--indigo {
    background: rgba(99, 102, 241, 0.15) !important;
    color: #a5b4fc !important;
    border: 1px solid rgba(99, 102, 241, 0.3) !important;

    &:hover {
      background: rgba(99, 102, 241, 0.28) !important;
      color: $white !important;
    }
  }

  &--red {
    background: rgba(239, 68, 68, 0.12) !important;
    color: #fca5a5 !important;
    border: 1px solid rgba(239, 68, 68, 0.25) !important;

    &:hover {
      background: rgba(239, 68, 68, 0.25) !important;
      color: $white !important;
    }
  }
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
.items-cards {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.item-card {
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
  padding: 14px 16px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.item-card-header {
  display: flex;
  align-items: center;
}

.item-card-title {
  display: flex;
  align-items: center;
  gap: 10px;
  flex: 1;
}

.card-item-icon {
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

.item-card-actions {
  display: grid;
  grid-template-columns: 1fr 1fr 1fr;
  gap: 8px;
  padding-top: 12px;
  border-top: 1px solid $border;
}

.action-btn-mobile {
  border-radius: 10px !important;
  font-weight: 700 !important;
  font-size: 12px !important;
  height: 34px !important;
  text-transform: none !important;
  letter-spacing: 0 !important;

  &--green {
    background: rgba(16, 185, 129, 0.15) !important;
    color: #6ee7b7 !important;
    border: 1px solid rgba(16, 185, 129, 0.3) !important;

    &:hover {
      background: rgba(16, 185, 129, 0.28) !important;
      color: $white !important;
    }
  }

  &--indigo {
    background: rgba(99, 102, 241, 0.15) !important;
    color: #a5b4fc !important;
    border: 1px solid rgba(99, 102, 241, 0.3) !important;

    &:hover {
      background: rgba(99, 102, 241, 0.28) !important;
      color: $white !important;
    }
  }

  &--red {
    background: rgba(239, 68, 68, 0.12) !important;
    color: #fca5a5 !important;
    border: 1px solid rgba(239, 68, 68, 0.25) !important;

    &:hover {
      background: rgba(239, 68, 68, 0.25) !important;
      color: $white !important;
    }
  }
}

.mobile-pagination {
  display: flex;
  justify-content: center;
  padding: 16px 0 4px;
}

// ── Responsive ─────────────────────────────────────────────────────────────
@media (max-width: 768px) {
  .items-page-container {
    padding: 16px 12px;
  }

  .header-content {
    flex-direction: column;
    align-items: stretch;
    padding: 18px;
    gap: 14px;
  }

  .header-left {
    flex-wrap: wrap;
  }

  .page-title {
    font-size: 18px !important;
  }

  .header-actions {
    flex-direction: column;
    align-items: stretch;
  }

  .search-input,
  .category-select {
    min-width: 0;
    max-width: 100%;
  }
}
</style>

<style>
.items-page-container .items-table .q-table__container {
  background: #1e293b !important;
  border: 1px solid rgba(255, 255, 255, 0.08) !important;
  border-radius: 20px !important;
  overflow: hidden !important;
}

.items-page-container .items-table .q-table,
.items-page-container .items-table .q-table td,
.items-page-container .items-table .q-table th,
.items-page-container .items-table .q-table tr {
  border: none !important;
  outline: none !important;
}

.items-page-container .items-table thead tr th {
  background: #273549 !important;
  color: rgba(255, 255, 255, 0.55) !important;
  border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
}

.items-page-container .items-table tbody tr,
.items-page-container .items-table tbody tr:hover {
  background: #1e293b !important;
}

.items-page-container .items-table tbody tr td {
  background: transparent !important;
  color: #ffffff !important;
  border-bottom: 1px solid rgba(255, 255, 255, 0.05) !important;
}

.items-page-container .items-table tbody tr:last-child td {
  border-bottom: none !important;
}

.items-page-container .items-table .q-table__bottom {
  background: #273549 !important;
  color: #ffffff !important;
  border-top: 1px solid rgba(255, 255, 255, 0.08) !important;
}

.items-page-container .item-card {
  background: #1e293b !important;
  border-color: rgba(255, 255, 255, 0.08) !important;
}

.items-page-container .empty-state-desktop,
.items-page-container .empty-state {
  background: #1e293b !important;
}
</style>
