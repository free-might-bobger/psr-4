<template>
  <div class="store-page-container">
    <!-- Store Header -->
    <div class="store-header q-mb-lg">
      <BreadCrumbsWrapper :bread-crumbs="[{
        name: store.name,
        path: ''
      }]" />

      <div class="store-info q-mt-md" v-if="store.name">
        <div class="text-h4 text-weight-bold">
          <q-icon name="store" class="q-mr-sm" />
          {{ store.name }}
        </div>
        <div class="text-body2 text-grey-7 q-mt-xs" v-if="store.default_address?.complete_address">
          <q-icon name="location_on" size="xs" class="q-mr-xs" />
          {{ store.default_address.complete_address }}
        </div>
      </div>
    </div>

    <!-- Filters and Search Section -->
    <div class="filters-section q-mb-lg">
      <q-card flat bordered class="filters-card">
        <q-card-section>
          <div class="row q-col-gutter-md">
            <!-- Search Input -->
            <div class="col-12 col-md-6">
              <q-input outlined v-model="searchString" placeholder="Search products..." debounce="800"
                class="search-input">
                <template v-slot:prepend>
                  <q-icon name="search" />
                </template>
                <template v-slot:append v-if="searchString">
                  <q-icon name="close" @click="searchString = ''" class="cursor-pointer" />
                </template>
              </q-input>
            </div>

            <!-- Category Filter -->
            <div class="col-12 col-md-6">
              <q-select outlined v-model="selectedCategory" :options="categories" label="Filter by Category" use-input
                clearable class="category-select" @clear="selectedCategory = ''">
                <template v-slot:prepend>
                  <q-icon name="category" />
                </template>
                <template v-slot:no-option>
                  <q-item>
                    <q-item-section class="text-grey">
                      No categories found
                    </q-item-section>
                  </q-item>
                </template>
              </q-select>
            </div>
          </div>

          <!-- Active Filters -->
          <div class="active-filters q-mt-md" v-if="selectedCategory || searchString">
            <div class="text-caption text-weight-medium q-mb-xs">Active Filters:</div>
            <div class="filter-chips">
              <q-chip v-if="searchString" removable @remove="searchString = ''" color="primary" text-color="white"
                icon="search">
                Search: {{ searchString }}
              </q-chip>
              <q-chip v-if="selectedCategory && typeof selectedCategory === 'object' && 'name' in selectedCategory"
                removable @remove="selectedCategory = ''" color="primary" text-color="white" icon="category">
                {{ (selectedCategory as any).name }}
              </q-chip>
            </div>
          </div>
        </q-card-section>
      </q-card>
    </div>

    <!-- Results Count -->
    <div class="results-header q-mb-md" v-if="result.length > 0">
      <div class="text-body1 text-grey-7">
        Showing {{ result.length }} {{ result.length === 1 ? 'product' : 'products' }}
      </div>
    </div>

    <!-- Products Grid -->
    <div class="products-grid" v-if="result.length > 0">
      <router-link :to="`/public_stores/${route.params.id}/item/${val.optimus_id}`" class="product-card-link"
        v-for="val in result" :key="val.id">
        <q-card flat bordered class="product-card">
          <!-- Product Image -->
          <div class="product-image-container">
            <q-img
              :src="val.primary_img?.path_thumbnail || (val.primary_img as any)?.path_url || '/placeholder-image.png'"
              :alt="val.name" class="product-image" :ratio="1" fit="cover">
              <template v-slot:error>
                <div class="absolute-full flex flex-center bg-grey-3 text-grey-8">
                  <q-icon name="image_not_supported" size="48px" />
                </div>
              </template>
            </q-img>
            <!-- Hover Overlay -->
            <div class="product-overlay">
              <q-btn round color="primary" icon="visibility" size="md" class="view-btn">
                <q-tooltip>View Details</q-tooltip>
              </q-btn>
            </div>
          </div>

          <!-- Product Info -->
          <q-card-section class="product-info">
            <div class="product-name text-body1 text-weight-medium">
              {{ val.name }}
            </div>
            <div class="product-price text-h6 text-weight-bold text-primary q-mt-sm">
              {{ getPriceRange(val.item_price || []) }}
            </div>
          </q-card-section>
        </q-card>
      </router-link>
    </div>

    <!-- Empty State -->
    <div class="empty-state" v-else>
      <q-icon name="inventory_2" size="80px" color="grey-4" />
      <div class="text-h6 text-grey-6 q-mt-md">No products found</div>
      <div class="text-body2 text-grey-5 q-mt-sm">
        <span v-if="searchString || selectedCategory">
          Try adjusting your filters or search terms
        </span>
        <span v-else>
          This store doesn't have any products yet
        </span>
      </div>
      <q-btn v-if="searchString || selectedCategory" outline color="primary" label="Clear Filters" class="q-mt-md"
        @click="clearFilters" icon="filter_alt_off" />
    </div>

    <!-- Pagination -->
    <div class="pagination-container q-mt-lg" v-if="result.length > 0 && pagination.lastPage > 1">
      <q-pagination v-model="pagination.page" :max="pagination.lastPage" color="primary" boundary-links direction-links
        :max-pages="7" class="store-pagination" />
    </div>
  </div>
</template>
<script setup lang="ts">
import { useItemStore } from 'src/stores/item';
import { storeToRefs } from 'pinia';
import { useRoute } from 'vue-router';
import { onMounted, ref, watch, computed } from 'vue';
import { get, show } from 'src/boot/axios-call';
import { useCommonStore } from 'src/stores/common';
import { getPriceRange } from 'src/boot/utilities';
import BreadCrumbsWrapper from 'src/components/BreadCrumbsWrapper.vue'
import { ItemInterface } from 'src/boot/interfaces';

const useItem = useItemStore();
const { searchString, selectedCategory } = storeToRefs(useItem);
const useCommon = useCommonStore();

const { pagination, result: resultRef, entityQuery } = storeToRefs(useCommon);
const result = computed(() => resultRef.value as ItemInterface[]);
const store = ref<{
  id?: number;
  name: string;
  logo: { path_url: string };
  default_address: {
    complete_address: string;
  };
}>({
  name: '',
  logo: { path_url: '' },
  default_address: {
    complete_address: '',
  },
});

const storeId = ref();
const route = useRoute();
storeId.value = route.params.id;

const showStore = async () => {
  store.value = await show({
    message: 'Getting store...',
    entity: 'public_stores',
    optimus_id: storeId.value,
    query: {
      columns: 'id,name'
    },
  });
  getCategories();

};

const categories = ref([]);
const getCategories = async () => {
  if (!store.value.id) return;

  let cat = await get(
    {
      message: 'Getting Categories',
      entity: 'categories',
      query: {
        orderBy: 'name:asc',
        type: 'collection',
        whereHas: 'items:store_id;' + store.value.id
      },
    },
    false
  );

  if (cat && typeof cat === 'object' && 'data' in cat) {
    categories.value = (cat as any).data.data;
  }
};

const onRequest = async () => {
  let filters = `store_id:${storeId.value}`;
  if (searchString.value) {
    filters = `store_id:${storeId.value},` + 'name:' + searchString.value;
  }

  if (selectedCategory.value && typeof selectedCategory.value === 'object' && 'id' in selectedCategory.value) {
    filters += ',category_id:' + (selectedCategory.value as any).id;
  }

  useCommon.setResultPagination(
    {
      entity: 'public_store_items',
      query: {
        with: 'images:is_primary;1,itemPrice.unit,store',
        filters: filters,
        page: pagination.value.page,
        isOptimus: 'false'
      },
    },
    true
  );

};

onMounted(() => {
  pagination.value.page = 1;
  showStore();
  onRequest();
});

useCommon.$subscribe(async (mutation, state) => {
  if (mutation.events) {
    const events = Array.isArray(mutation.events) ? mutation.events : [mutation.events];
    if (events.some(event => event.key === 'page')) {
      onRequest();
    }
  }
});

watch(selectedCategory, () => {
  onRequest();
});

watch(searchString, () => {
  onRequest();
});

const clearFilters = () => {
  searchString.value = '';
  selectedCategory.value = '';
};
</script>

<style scoped lang="scss">
.store-page-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 16px;
}

.store-header {
  padding: 16px 0;
}

.store-info {
  margin-top: 16px;
}

.filters-section {
  margin-top: 24px;
}

.filters-card {
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.search-input,
.category-select {
  :deep(.q-field__control) {
    border-radius: 8px;
    height: 48px;
    min-height: 48px;
    font-size: 14px;
    transition: all 0.3s ease;

    &:hover {
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
  }
}

.active-filters {
  padding-top: 12px;
  border-top: 1px solid #e0e0e0;
}

.filter-chips {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.results-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
  gap: 24px;
  margin-bottom: 32px;
}

.product-card-link {
  text-decoration: none;
  color: inherit;
  display: block;
  transition: transform 0.3s ease;

  &:hover {
    transform: translateY(-4px);
  }
}

.product-card {
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  overflow: hidden;
  transition: all 0.3s ease;
  height: 100%;
  display: flex;
  flex-direction: column;

  &:hover {
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
  }
}

.product-image-container {
  position: relative;
  width: 100%;
  overflow: hidden;
  background: #f5f5f5;
}

.product-image {
  width: 100%;
  transition: transform 0.3s ease;
}

.product-card-link:hover .product-image {
  transform: scale(1.05);
}

.product-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.4);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.product-card-link:hover .product-overlay {
  opacity: 1;
}

.view-btn {
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.product-info {
  padding: 16px;
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.product-name {
  color: #1a1a1a;
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
  min-height: 2.8em;
}

.product-price {
  margin-top: 8px;
  letter-spacing: 0.5px;
}

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 80px 16px;
  text-align: center;
  min-height: 400px;
}

.pagination-container {
  display: flex;
  justify-content: center;
  padding: 32px 0;
}

.store-pagination {
  :deep(.q-btn) {
    border-radius: 8px;
    margin: 0 2px;
    min-width: 36px;
    height: 36px;
  }
}

@media (max-width: 1024px) {
  .products-grid {
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 16px;
  }
}

@media (max-width: 600px) {
  .store-page-container {
    padding: 8px;
  }

  .products-grid {
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: 12px;
  }

  .product-info {
    padding: 12px;
  }

  .product-name {
    font-size: 14px;
  }

  .product-price {
    font-size: 16px;
  }

  .filters-card {
    .q-card__section {
      padding: 16px;
    }
  }
}

@media (max-width: 400px) {
  .products-grid {
    grid-template-columns: 1fr 1fr;
  }
}
</style>
