<template>
  <div class="items-page-container">

    <!-- Hero Header -->
    <div class="page-hero q-mb-xl">
      <div class="hero-accent-overlay"></div>
      <div class="hero-inner">
        <div class="hero-left">
          <q-btn flat round dense icon="arrow_back" @click="router.back()" class="back-btn">
            <q-tooltip>Back to Store</q-tooltip>
          </q-btn>
          <div class="hero-icon-wrap">
            <q-icon name="inventory_2" size="28px" color="white" />
          </div>
          <div>
            <h1 class="page-title">{{ store.name || 'Store Items' }}</h1>
            <div class="page-subtitle">Manage store items and inventory</div>
          </div>
        </div>
        <div class="hero-right">
          <div class="search-input-wrap">
            <q-icon name="search" size="20px" class="search-icon" />
            <q-input v-model="search" placeholder="Search items..." outlined dense clearable debounce="1000"
              class="search-field" hide-bottom-space />
          </div>
          <div class="category-select-wrap">
            <q-icon name="category" size="20px" class="search-icon" />
            <q-select outlined v-model="selectedCategory" :options="categories" label="Category" hide-bottom-space
              use-input dense clearable class="category-field" @update:model-value="handleCategoryChange">
              <template v-slot:append>
                <q-icon v-if="selectedCategory" name="close" @click.stop.prevent="handleCategoryChange('')"
                  class="cursor-pointer" />
              </template>
            </q-select>
          </div>
        </div>
      </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-section q-mb-lg">
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon-wrap items-icon">
            <q-icon name="inventory_2" size="24px" color="white" />
          </div>
          <div class="stat-content">
            <div class="stat-number">{{ typedResult.length }}</div>
            <div class="stat-label">Total Items</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon-wrap store-icon">
            <q-icon name="store" size="24px" color="white" />
          </div>
          <div class="stat-content">
            <div class="stat-number">{{ store.name ? 1 : 0 }}</div>
            <div class="stat-label">Active Store</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon-wrap category-icon">
            <q-icon name="category" size="24px" color="white" />
          </div>
          <div class="stat-content">
            <div class="stat-number">{{ categories.length }}</div>
            <div class="stat-label">Categories</div>
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
            <q-icon name="inventory_2" size="64px" color="white" />
          </div>
          <div class="empty-title">No items found</div>
          <div class="empty-subtitle">Try adjusting your search criteria or add new items</div>
        </div>
      </div>

      <!-- Items Table -->
      <div v-else class="items-table-section">
        <div class="table-card">
          <table class="items-table">
            <thead>
              <tr class="table-header">
                <th class="table-header-cell">Item Name</th>
                <th class="table-header-cell actions-header">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in typedResult" :key="item.optimus_id" class="table-row">
                <td class="table-cell">
                  <div class="item-info-cell">
                    <div class="item-avatar">
                      <q-icon name="inventory_2" size="20px" color="white" />
                    </div>
                    <router-link :to="`${$route.path}/${item.optimus_id}`" class="item-name-link">
                      {{ item.name }}
                    </router-link>
                  </div>
                </td>
                <td class="table-cell">
                  <div class="actions-cell">
                    <q-btn unelevated dense icon="attach_money"
                      :to="`${$route.path}/${item.optimus_id}/item-prices?filters=store_id:${store.optimus_id}`"
                      class="action-btn prices-btn">
                      <q-tooltip>Item Prices</q-tooltip>
                    </q-btn>
                    <q-btn unelevated dense icon="edit_note"
                      :to="`${$route.path}/${item.optimus_id}?filters=store_id:${store.optimus_id}`"
                      class="action-btn edit-btn">
                      <q-tooltip>Edit Item</q-tooltip>
                    </q-btn>
                    <q-btn unelevated dense icon="delete_forever" @click="handleDeleteItem(item)"
                      class="action-btn delete-btn">
                      <q-tooltip>Delete Item</q-tooltip>
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
                of {{ pagination.rowsNumber || typedResult.length }} items
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
          <q-icon name="inventory_2" size="48px" color="white" />
        </div>
        <div class="empty-title-mobile">No items found</div>
        <div class="empty-subtitle-mobile">Try adjusting your search criteria</div>
      </div>

      <!-- Item Cards -->
      <div v-else class="items-cards-mobile">
        <q-card v-for="item in typedResult" :key="item.optimus_id" flat class="item-card-mobile q-mb-md">
          <q-card-section class="item-card-header">
            <div class="item-avatar-mobile">
              <q-icon name="inventory_2" size="24px" color="white" />
            </div>
            <div class="item-info-mobile">
              <router-link :to="`${$route.path}/${item.optimus_id}`" class="item-name-mobile">
                {{ item.name }}
              </router-link>
            </div>
          </q-card-section>

          <q-separator class="item-card-divider" />

          <q-card-actions class="item-card-actions">
            <q-btn unelevated icon="attach_money" label="Prices"
              :to="`${$route.path}/${item.optimus_id}/item-prices?filters=store_id:${store.optimus_id}`"
              class="mobile-action-btn prices-btn-mobile" />
            <q-btn unelevated icon="edit_note" label="Edit"
              :to="`${$route.path}/${item.optimus_id}?filters=store_id:${store.optimus_id}`"
              class="mobile-action-btn edit-btn-mobile" />
            <q-btn unelevated icon="delete_forever" label="Delete" @click="handleDeleteItem(item)"
              class="mobile-action-btn delete-btn-mobile" />
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
.items-page-container {
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
  gap: 24px;
}

.hero-left {
  display: flex;
  align-items: center;
  gap: 16px;
}

.back-btn {
  width: 44px;
  height: 44px;
  border-radius: 14px;
  background: rgba(255, 255, 255, 0.08) !important;
  color: $white !important;
  transition: all 0.25s ease;
  flex-shrink: 0;

  &:hover {
    background: rgba(255, 255, 255, 0.14) !important;
    transform: translateX(-3px);
  }
}

.hero-icon-wrap {
  width: 56px;
  height: 56px;
  border-radius: 16px;
  background: linear-gradient(135deg, $accent 0%, $accent-2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 8px 24px rgba(99, 102, 241, 0.4);
  flex-shrink: 0;
}

.page-title {
  font-size: 26px;
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
  display: flex;
  align-items: center;
  gap: 12px;
  flex-shrink: 0;
}

.search-input-wrap,
.category-select-wrap {
  display: flex;
  align-items: center;
  background: $dark-elevated;
  border: 1px solid $border;
  border-radius: 14px;
  padding: 4px 16px;
  transition: all 0.2s ease;

  &:focus-within {
    border-color: rgba($accent, 0.4);
    box-shadow: 0 0 0 3px rgba($accent, 0.1);
  }
}

.search-input-wrap {
  width: 240px;
}

.category-select-wrap {
  width: 200px;
}

.search-icon {
  color: $muted;
  margin-right: 10px;
  flex-shrink: 0;
}

.search-field,
.category-field {
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

  :deep(.q-field__label) {
    color: $muted !important;
  }
}

// ── Statistics Section ─────────────────────────────────────────────────────────
.stats-section {
  margin-bottom: 32px;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
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

  &.items-icon {
    background: linear-gradient(135deg, $accent 0%, $accent-2 100%);
    box-shadow: 0 4px 16px rgba(99, 102, 241, 0.3);
  }

  &.store-icon {
    background: linear-gradient(135deg, $green 0%, $green-2 100%);
    box-shadow: 0 4px 16px rgba(16, 185, 129, 0.3);
  }

  &.category-icon {
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
  background: linear-gradient(135deg, rgba($accent, 0.2) 0%, rgba($accent-2, 0.1) 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 24px;
  box-shadow: 0 8px 32px rgba($accent, 0.2);
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
.items-table-section {
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

.items-table {
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

    &.actions-header {
      text-align: center;
    }
  }

  tbody tr {
    transition: background-color 0.2s ease;
    border-bottom: 1px solid $border;

    &:hover {
      background: rgba($accent, 0.04);
    }

    &:last-child {
      border-bottom: none;
    }
  }

  td {
    padding: 20px;
    border: none;
    vertical-align: middle;
  }
}

// ── Item Info Cell ────────────────────────────────────────────────────────────
.item-info-cell {
  display: flex;
  align-items: center;
  gap: 14px;
}

.item-avatar {
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

.item-name-link {
  font-size: 14px;
  font-weight: 700;
  color: #a5b4fc;
  text-decoration: none;
  transition: color 0.2s ease;
  display: block;
  line-height: 1.3;

  &:hover {
    color: $accent;
  }
}

// ── Actions Cell ──────────────────────────────────────────────────────────────
.actions-cell {
  display: flex;
  align-items: center;
  justify-content: center;
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
  transition: all 0.2s ease;

  &.prices-btn {
    background: rgba($green, 0.15) !important;
    color: $green !important;
    border: 1px solid rgba($green, 0.2) !important;

    &:hover {
      background: rgba($green, 0.25) !important;
      border-color: rgba($green, 0.4) !important;
      transform: translateY(-1px);
    }
  }

  &.edit-btn {
    background: rgba($blue, 0.15) !important;
    color: $blue !important;
    border: 1px solid rgba($blue, 0.2) !important;

    &:hover {
      background: rgba($blue, 0.25) !important;
      border-color: rgba($blue, 0.4) !important;
      transform: translateY(-1px);
    }
  }

  &.delete-btn {
    background: rgba($red, 0.15) !important;
    color: $red !important;
    border: 1px solid rgba($red, 0.2) !important;

    &:hover {
      background: rgba($red, 0.25) !important;
      border-color: rgba($red, 0.4) !important;
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
  background: linear-gradient(135deg, rgba($accent, 0.2) 0%, rgba($accent-2, 0.1) 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 20px;
  box-shadow: 0 6px 24px rgba($accent, 0.2);
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

// ── Mobile Item Cards ─────────────────────────────────────────────────────────
.items-cards-mobile {
  padding: 0 4px;
}

.item-card-mobile {
  background: $dark-card !important;
  border: 1px solid $border !important;
  border-radius: 16px !important;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2) !important;
  overflow: hidden;
  transition: all 0.2s ease;

  &:hover {
    border-color: rgba($accent, 0.2) !important;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3) !important;
  }
}

.item-card-header {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 20px 20px 16px;
}

.item-avatar-mobile {
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

.item-info-mobile {
  flex: 1;
}

.item-name-mobile {
  font-size: 16px;
  font-weight: 700;
  color: #a5b4fc;
  text-decoration: none;
  display: block;
  line-height: 1.3;

  &:hover {
    color: $accent;
  }
}

.item-card-divider {
  background: $border !important;
  margin: 0;
}

.item-card-actions {
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
  transition: all 0.2s ease;

  &.prices-btn-mobile {
    background: rgba($green, 0.15) !important;
    color: $green !important;
    border: 1px solid rgba($green, 0.2) !important;

    &:hover {
      background: rgba($green, 0.25) !important;
      border-color: rgba($green, 0.4) !important;
    }
  }

  &.edit-btn-mobile {
    background: rgba($blue, 0.15) !important;
    color: $blue !important;
    border: 1px solid rgba($blue, 0.2) !important;

    &:hover {
      background: rgba($blue, 0.25) !important;
      border-color: rgba($blue, 0.4) !important;
    }
  }

  &.delete-btn-mobile {
    background: rgba($red, 0.15) !important;
    color: $red !important;
    border: 1px solid rgba($red, 0.2) !important;

    &:hover {
      background: rgba($red, 0.25) !important;
      border-color: rgba($red, 0.4) !important;
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
  .items-page-container {
    padding: 16px 12px;
  }

  .hero-inner {
    flex-direction: column;
    gap: 20px;
    padding: 24px 20px;
  }

  .hero-left {
    flex-wrap: wrap;
    gap: 12px;
  }

  .hero-right {
    flex-direction: column;
    width: 100%;
  }

  .page-title {
    font-size: 22px;
  }

  .search-input-wrap,
  .category-select-wrap {
    width: 100%;
  }

  .stats-grid {
    grid-template-columns: 1fr;
    gap: 16px;
  }

  .item-card-mobile {
    margin: 0 0 16px 0;
  }
}
</style>
