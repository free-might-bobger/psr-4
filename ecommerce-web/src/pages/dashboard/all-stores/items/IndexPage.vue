<template>
  <div class="items-page-container">
    <!-- Header Section -->
    <div class="page-header q-mb-md">
      <div class="header-content">
        <div class="header-left">
          <q-btn
            flat
            round
            dense
            color="grey-8"
            icon="arrow_back"
            @click="router.back()"
            class="back-btn"
          >
            <q-tooltip>Back to Store</q-tooltip>
          </q-btn>
          <div class="header-title-section">
            <q-icon name="inventory_2" size="32px" color="primary" class="q-mr-sm" />
            <div>
              <h2 class="page-title">{{ store.name || 'Store Items' }}</h2>
              <p class="page-subtitle">Manage store items and inventory</p>
            </div>
          </div>
        </div>
        <div class="header-actions">
          <q-input
            v-model="search"
            placeholder="Search items..."
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
          <q-select
            outlined
            v-model="selectedCategory"
            :options="categories"
            label="Category"
            hide-bottom-space
            use-input
            dense
            clearable
            class="category-select"
            @update:model-value="handleCategoryChange"
          >
            <template v-slot:prepend>
              <q-icon name="category" />
            </template>
            <template v-slot:append>
              <q-icon
                v-if="selectedCategory"
                name="close"
                @click.stop.prevent="handleCategoryChange('')"
                class="cursor-pointer"
              />
            </template>
          </q-select>
        </div>
      </div>
    </div>

    <!-- Desktop Table View -->
    <div class="desktop-only">
      <div v-if="typedResult.length === 0" class="empty-state-desktop">
        <q-icon name="inventory_2" size="80px" color="grey-4" />
        <div class="text-h5 q-mt-md text-grey-6">No items found</div>
        <div class="text-body2 text-grey-5 q-mt-sm">Try adjusting your search criteria</div>
      </div>
      <q-table
        v-else
        flat
        bordered
        :rows="typedResult"
        :columns="columns"
        row-key="optimus_id"
        class="items-table"
      >
        <template v-slot:body-cell-name="props">
          <q-td :props="props">
            <router-link
              :to="`${$route.path}/${props.row.optimus_id}`"
              class="item-name-link"
            >
              <q-icon name="inventory_2" color="primary" class="q-mr-xs" />
              <span>{{ props.row.name }}</span>
            </router-link>
          </q-td>
        </template>

        <template v-slot:body-cell-actions="props">
          <q-td :props="props">
            <div class="action-buttons">
              <q-btn
                unelevated
                dense
                round
                color="primary"
                icon="attach_money"
                :to="`${$route.path}/${props.row.optimus_id}/item-prices?filters=store_id:${store.optimus_id}`"
                size="sm"
                class="q-mr-xs"
              >
                <q-tooltip>Item Prices</q-tooltip>
              </q-btn>
              <q-btn
                unelevated
                dense
                round
                color="primary"
                icon="edit_note"
                :to="`${$route.path}/${props.row.optimus_id}?filters=store_id:${store.optimus_id}`"
                size="sm"
                class="q-mr-xs"
              >
                <q-tooltip>Edit Item</q-tooltip>
              </q-btn>
              <q-btn
                unelevated
                dense
                round
                color="negative"
                icon="delete_forever"
                @click="handleDeleteItem(props.row)"
                size="sm"
              >
                <q-tooltip>Delete Item</q-tooltip>
              </q-btn>
            </div>
          </q-td>
        </template>

        <template v-slot:bottom>
          <div class="table-pagination">
            <div class="pagination-info">
              Showing {{ pagination.from }} - {{ pagination.to }} of {{ pagination.rowsNumber }} items
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
        </template>
      </q-table>
    </div>

    <!-- Mobile Card View -->
    <div class="mobile-only">
      <div v-if="typedResult.length === 0" class="empty-state">
        <q-icon name="inventory_2" size="64px" color="grey-4" />
        <div class="text-h6 q-mt-md text-grey-6">No items found</div>
      </div>
      <div v-else class="items-cards">
        <q-card
          v-for="item in typedResult"
          :key="item.optimus_id"
          flat
          bordered
          class="item-card q-mb-md"
        >
          <q-card-section>
            <div class="item-card-header">
              <div class="item-card-title">
                <q-icon name="inventory_2" color="primary" size="24px" class="q-mr-sm" />
                <router-link
                  :to="`${$route.path}/${item.optimus_id}`"
                  class="item-name-link"
                >
                  {{ item.name }}
                </router-link>
              </div>
            </div>
            <div class="item-card-actions q-mt-md">
              <q-btn
                unelevated
                dense
                color="primary"
                icon="attach_money"
                label="Prices"
                :to="`${$route.path}/${item.optimus_id}/item-prices?filters=store_id:${store.optimus_id}`"
                class="action-btn-mobile action-btn-prices-mobile"
              />
              <q-btn
                unelevated
                dense
                color="primary"
                icon="edit_note"
                label="Edit"
                :to="`${$route.path}/${item.optimus_id}?filters=store_id:${store.optimus_id}`"
                class="action-btn-mobile action-btn-edit-mobile"
              />
              <q-btn
                unelevated
                dense
                color="negative"
                icon="delete_forever"
                label="Delete"
                @click="handleDeleteItem(item)"
                class="action-btn-mobile action-btn-delete-mobile"
              />
            </div>
          </q-card-section>
        </q-card>
      </div>
      <!-- Mobile Pagination -->
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

const handleCategoryChange = (value: any) => {
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

const categories = ref<Array<{ id: number; name: string; [key: string]: any }>>([]);
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
    ) as any;

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
      ? (selectedCategory.value as any).id 
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

watch(search, (newValue) => {
  useItem.setSearchString(newValue || '');
  requestItems();
});
</script>

<style scoped lang="scss">
@import 'src/css/dashboard/all-stores/items/index.scss';

.items-table {
  width: 100%;
}

.item-name-link {
  text-decoration: none;
  color: inherit;
  display: flex;
  align-items: center;
  font-size: 16px;
  font-weight: normal;

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
</style>
