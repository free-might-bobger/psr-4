<template>
  <div class="items-page">

    <!-- Hero Header -->
    <div class="page-hero q-mb-xl">
      <div class="hero-accent-overlay"></div>
      <div class="hero-inner">
        <button class="back-btn" @click="router.back()">
          <q-icon name="arrow_back" size="20px" />
          <q-tooltip>Back to Store</q-tooltip>
        </button>
        <div class="hero-icon-wrap">
          <q-icon name="inventory_2" size="28px" color="white" />
        </div>
        <div class="hero-text">
          <h1 class="hero-title">{{ store.name || 'Store Items' }}</h1>
          <div class="hero-subtitle">Manage store items and inventory</div>
        </div>
      </div>
      <div class="hero-controls">
        <div class="search-wrap">
          <q-icon name="search" size="20px" class="search-icon" />
          <input v-model="search" type="text" placeholder="Search items..." class="dark-search" />
        </div>
        <q-select outlined v-model="selectedCategory" :options="categories" label="Category" hide-bottom-space use-input
          dense clearable class="dark-select" @update:model-value="handleCategoryChange">
          <template v-slot:prepend>
            <q-icon name="category" />
          </template>
          <template v-slot:append>
            <q-icon v-if="selectedCategory" name="close" @click.stop.prevent="handleCategoryChange('')"
              class="cursor-pointer" />
          </template>
        </q-select>
      </div>
    </div>

    <!-- Stats Row -->
    <div class="stats-row q-mb-lg">
      <div class="stat-card">
        <div class="stat-icon-wrap stat-icon-indigo">
          <q-icon name="inventory_2" size="20px" color="white" />
        </div>
        <div>
          <div class="stat-value">{{ typedResult.length }}</div>
          <div class="stat-label">Items on page</div>
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
          <q-icon name="inventory_2" size="48px" color="white" />
        </div>
        <div class="empty-title">No items found</div>
        <div class="empty-subtitle">Try adjusting your search criteria</div>
      </div>

      <div v-else class="table-card">
        <table class="dark-table">
          <thead>
            <tr>
              <th>Item Name</th>
              <th class="th-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in typedResult" :key="item.optimus_id">
              <td>
                <router-link :to="`${$route.path}/${item.optimus_id}`" class="item-link">
                  <div class="item-avatar">
                    <q-icon name="inventory_2" size="16px" />
                  </div>
                  <span class="item-name-text">{{ item.name }}</span>
                </router-link>
              </td>
              <td class="td-center">
                <div class="action-btns">
                  <router-link
                    :to="`${$route.path}/${item.optimus_id}/item-prices?filters=store_id:${store.optimus_id}`"
                    class="action-pill action-prices">
                    <q-icon name="attach_money" size="16px" />
                    Prices
                  </router-link>
                  <router-link :to="`${$route.path}/${item.optimus_id}?filters=store_id:${store.optimus_id}`"
                    class="action-pill action-edit">
                    <q-icon name="edit_note" size="16px" />
                    Edit
                  </router-link>
                  <button class="action-pill action-delete" @click="handleDeleteItem(item)">
                    <q-icon name="delete_forever" size="16px" />
                    Delete
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Desktop Pagination -->
        <div class="table-pagination">
          <div class="pagination-info">
            Showing {{ pagination.from }}&ndash;{{ pagination.to }} of {{ pagination.rowsNumber }} items
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
          <q-icon name="inventory_2" size="48px" color="white" />
        </div>
        <div class="empty-title">No items found</div>
        <div class="empty-subtitle">Try adjusting your search</div>
      </div>

      <div v-else class="mobile-cards">
        <div v-for="item in typedResult" :key="item.optimus_id" class="m-card">
          <div class="m-card-top">
            <div class="m-card-avatar">
              <q-icon name="inventory_2" size="20px" color="white" />
            </div>
            <router-link :to="`${$route.path}/${item.optimus_id}`" class="m-card-name">
              {{ item.name }}
            </router-link>
          </div>
          <div class="m-card-actions">
            <router-link :to="`${$route.path}/${item.optimus_id}/item-prices?filters=store_id:${store.optimus_id}`"
              class="m-action m-action-prices">
              <q-icon name="attach_money" size="16px" class="q-mr-xs" />
              Prices
            </router-link>
            <router-link :to="`${$route.path}/${item.optimus_id}?filters=store_id:${store.optimus_id}`"
              class="m-action m-action-edit">
              <q-icon name="edit_note" size="16px" class="q-mr-xs" />
              Edit
            </router-link>
            <button class="m-action m-action-delete" @click="handleDeleteItem(item)">
              <q-icon name="delete_forever" size="16px" class="q-mr-xs" />
              Delete
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
          Showing {{ pagination.from }}&ndash;{{ pagination.to }} of {{ pagination.rowsNumber }} items
        </div>
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

const useCommon = useCommonStore();
const { pagination, result, entityQuery } = storeToRefs(useCommon);
const useItem = useItemStore();
const { searchString, selectedCategory } = storeToRefs(useItem);
const route = useRoute();
const router = useRouter();

const store = ref({
  optimus_id: '',
  name: '',
  desc: '',
  latitude: 0,
  longitude: 0,
  id: 0,
});

const search = ref('');

// Type the result
const typedResult = result as unknown as Array<{ optimus_id: number; name: string }>;

interface Category {
  id: number;
  name: string;
  optimus_id?: number;
}

interface ApiResponse<T> {
  data: {
    data: T[];
  };
}

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

const handleCategoryChange = (value: Category | number | null) => {
  selectedCategory.value = value;
  requestItems();
};

onBeforeMount(async () => {
  result.value = [];
  store.value = await show({
    entity: 'all_stores',
    optimus_id: Number(route.params.id),
  });
  await requestItems();
  getCategories();
});

onMounted(() => {
  entityQuery.value.query.page = 1;
});

const categories = ref<Category[]>([]);
const getCategories = async () => {
  try {
    const cat = await get(
      {
        entity: 'categories',
        query: {
          orderBy: 'name:asc',
          type: 'collection',
          whereHas: 'items:store_id;' + (store.value.id || store.value.optimus_id),
        },
      },
      false
    ) as ApiResponse<Category>;

    if (cat && cat.data && cat.data.data) {
      categories.value = cat.data.data;
    }
  } catch (error) {
    console.error('Error fetching categories:', error);
    categories.value = [];
  }
};

const requestItems = async () => {
  let filters = `store_id:${store.value.optimus_id}`;
  if (searchString.value) {
    filters = `store_id:${store.value.optimus_id},` + 'name:' + searchString.value;
  }

  if (selectedCategory.value) {
    const categoryId = typeof selectedCategory.value === 'object' && 'id' in selectedCategory.value
      ? (selectedCategory.value as Category).id
      : selectedCategory.value;
    if (categoryId) {
      filters += ',category_id:' + categoryId;
    }
  }

  entityQuery.value = {
    message: 'Getting items...',
    entity: 'items',
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

let searchTimer: ReturnType<typeof setTimeout> | null = null;
watch(search, (newValue) => {
  if (searchTimer) clearTimeout(searchTimer);
  searchTimer = setTimeout(() => {
    useItem.setSearchString(newValue || '');
    requestItems();
  }, 1000);
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
.items-page {
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

.back-btn {
  width: 44px;
  height: 44px;
  border-radius: 14px;
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid $border;
  color: $white;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.25s ease;
  flex-shrink: 0;

  &:hover {
    background: rgba(255, 255, 255, 0.14);
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
  box-shadow: 0 8px 24px rgba($accent, 0.4);
  flex-shrink: 0;
}

.hero-text {
  flex: 1;
  min-width: 0;
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
  gap: 14px;
  padding: 20px 36px 28px;
  flex-wrap: wrap;
}

// ── Dark search ──────────────────────────────────────────────────────────────
.search-wrap {
  position: relative;
  flex: 1;
  min-width: 180px;
  max-width: 320px;
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

// ── Dark select ──────────────────────────────────────────────────────────────
.dark-select {
  min-width: 180px;
  max-width: 240px;

  :deep(.q-field__control) {
    background: $dark-elevated !important;
    border-color: $border !important;
    border-radius: 12px !important;
    color: $white !important;
  }

  :deep(.q-field__native) {
    color: $white !important;
    font-size: 14px;
  }

  :deep(.q-field__label) {
    color: $muted !important;
  }

  :deep(.q-field__marginal) {
    color: $muted !important;
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
    background: linear-gradient(135deg, $accent, $accent-2);
    box-shadow: 0 4px 16px rgba($accent, 0.3);
  }

  &.stat-icon-blue {
    background: linear-gradient(135deg, $blue, $blue-2);
    box-shadow: 0 4px 16px rgba($blue, 0.3);
  }

  &.stat-icon-green {
    background: linear-gradient(135deg, $green, $green-2);
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

// ── Item link ────────────────────────────────────────────────────────────────
.item-link {
  display: flex;
  align-items: center;
  gap: 12px;
  text-decoration: none;
  transition: all 0.2s ease;

  &:hover .item-name-text {
    color: $accent;
  }

  &:hover .item-avatar {
    border-color: rgba($accent, 0.4);
    box-shadow: 0 0 0 3px rgba($accent, 0.1);
  }
}

.item-avatar {
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

.item-name-text {
  font-weight: 700;
  color: #a5b4fc;
  transition: color 0.2s ease;
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

  &.action-prices {
    background: rgba($green, 0.12);
    color: #6ee7b7;
    border: 1px solid rgba($green, 0.2);

    &:hover {
      background: rgba($green, 0.2);
      border-color: rgba($green, 0.35);
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
  align-items: center;
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

.m-card-name {
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

.m-card-actions {
  display: grid;
  grid-template-columns: 1fr 1fr 1fr;
  gap: 8px;
  padding: 0 20px 20px;
}

.m-action {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 11px 10px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 700;
  border: none;
  cursor: pointer;
  text-decoration: none;
  transition: all 0.2s ease;

  &.m-action-prices {
    background: rgba($green, 0.12);
    color: #6ee7b7;
    border: 1px solid rgba($green, 0.2);

    &:hover {
      background: rgba($green, 0.2);
    }
  }

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
  .items-page {
    padding: 16px 12px 48px;
  }

  .hero-inner {
    padding: 24px 20px 0;
    flex-wrap: wrap;
    gap: 12px;
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

  .dark-select {
    max-width: 100%;
    min-width: 0;
    width: 100%;
  }

  .stats-row {
    grid-template-columns: 1fr;
    gap: 10px;
  }

  .m-card-actions {
    grid-template-columns: 1fr 1fr 1fr;
    gap: 6px;
    padding: 0 16px 16px;
  }

  .m-card-top {
    padding: 16px 16px 12px;
  }
}

@media (max-width: 600px) {
  .items-page {
    padding: 10px 8px 40px;
  }

  .hero-inner {
    padding: 20px 16px 0;
    gap: 10px;
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

  .m-card-actions {
    grid-template-columns: 1fr;
    gap: 8px;
  }
}
</style>
