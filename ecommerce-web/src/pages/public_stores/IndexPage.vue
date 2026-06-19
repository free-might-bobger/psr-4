<template>
  <div class="store-page-container">

    <!-- Store Hero Banner -->
    <div class="store-hero">
      <div class="store-hero-bg">
        <div class="hero-orb orb-1"></div>
        <div class="hero-orb orb-2"></div>
        <div class="hero-grid"></div>
      </div>
      <div class="store-hero-inner">
        <div class="breadcrumb-wrap">
          <BreadCrumbsWrapper :bread-crumbs="[{ name: store.name, path: '' }]" />
        </div>
        <div class="store-hero-content" v-if="store.name">
          <div class="store-logo-wrap">
            <q-img v-if="store.logo?.path_url" :src="store.logo.path_url" class="store-logo-img" fit="cover" />
            <q-icon v-else name="storefront" size="32px" color="white" />
          </div>
          <div class="store-meta">
            <div class="store-name">{{ store.name }}</div>
            <div class="store-address" v-if="store.default_address?.complete_address">
              <q-icon name="location_on" size="14px" class="q-mr-xs" />
              {{ store.default_address.complete_address }}
            </div>
          </div>
        </div>
        <div class="store-hero-stats">
          <div class="hero-stat">
            <div class="hero-stat-value">{{ result.length }}</div>
            <div class="hero-stat-label">Products</div>
          </div>
          <div class="hero-stat-divider"></div>
          <div class="hero-stat">
            <div class="hero-stat-value">{{ categories.length }}</div>
            <div class="hero-stat-label">Categories</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar">
      <div class="filter-bar-inner">
        <div class="filter-search">
          <q-input outlined v-model="searchString" placeholder="Search products..." debounce="1000" class="filter-input"
            clearable>
            <template v-slot:prepend>
              <q-icon name="search" size="18px" />
            </template>
          </q-input>
        </div>
        <div class="filter-cat">
          <q-select outlined v-model="selectedCategory" :options="categories" label="Category" use-input clearable
            class="filter-input" @clear="selectedCategory = ''">
            <template v-slot:prepend>
              <q-icon name="category" size="18px" />
            </template>
            <template v-slot:no-option>
              <q-item>
                <q-item-section class="text-grey">No categories found</q-item-section>
              </q-item>
            </template>
          </q-select>
        </div>
        <q-btn v-if="searchString || selectedCategory" no-caps flat class="clear-btn" icon="filter_alt_off"
          label="Clear" @click="clearFilters" />
      </div>

      <!-- Active chips -->
      <div class="active-filters" v-if="selectedCategory || searchString">
        <q-chip v-if="searchString" removable @remove="searchString = ''" class="filter-chip" icon="search">
          {{ searchString }}
        </q-chip>
        <q-chip v-if="selectedCategory && typeof selectedCategory === 'object' && 'name' in selectedCategory" removable
          @remove="selectedCategory = ''" class="filter-chip" icon="category">
          {{ (selectedCategory as any).name }}
        </q-chip>
      </div>
    </div>

    <!-- Results bar -->
    <div class="results-bar" v-if="result.length > 0">
      <span class="results-count">{{ result.length }} {{ result.length === 1 ? 'product' : 'products' }}</span>
    </div>

    <!-- Products Grid -->
    <div class="products-grid" v-if="result.length > 0">
      <router-link :to="`/public_stores/${route.params.id}/item/${val.optimus_id}`" class="product-card-link"
        v-for="val in result" :key="val.id">
        <div class="product-card">
          <div class="product-image-wrap">
            <q-img
              :src="val.primary_img?.path_thumbnail || (val.primary_img as any)?.path_url || '/placeholder-image.png'"
              :alt="val.name" class="product-image" :ratio="1" fit="cover">
              <template v-slot:error>
                <div class="absolute-full flex flex-center bg-grey-2">
                  <q-icon name="image_not_supported" size="40px" color="grey-5" />
                </div>
              </template>
            </q-img>
            <div class="product-overlay">
              <div class="overlay-btn">
                <q-icon name="visibility" size="20px" color="white" />
                <span>View</span>
              </div>
            </div>
          </div>
          <div class="product-info">
            <div class="product-name">{{ val.name }}</div>
            <div class="product-price-row">
              <span class="product-price">{{ getPriceRange(val.item_price || []) }}</span>
              <div class="product-arrow">
                <q-icon name="arrow_forward_ios" size="11px" />
              </div>
            </div>
          </div>
        </div>
      </router-link>
    </div>

    <!-- Empty State -->
    <div class="empty-state" v-else>
      <div class="empty-icon-wrap">
        <q-icon name="inventory_2" size="40px" color="grey-4" />
      </div>
      <div class="empty-title">No products found</div>
      <div class="empty-sub">
        <span v-if="searchString || selectedCategory">Try adjusting your filters or search terms</span>
        <span v-else>This store doesn't have any products yet</span>
      </div>
      <q-btn v-if="searchString || selectedCategory" no-caps unelevated class="clear-filters-btn" @click="clearFilters"
        icon="filter_alt_off" label="Clear Filters" />
    </div>

    <!-- Pagination -->
    <div class="pagination-wrap" v-if="result.length > 0 && pagination.lastPage > 1">
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

const { pagination, result: resultRef } = storeToRefs(useCommon);
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
    entity: 'find-store',
    optimus_id: storeId.value,
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
    categories.value = (cat as { data: { data: unknown[] } }).data.data;
  }
};

const onRequest = async () => {
  let filters = `store_id:${storeId.value}`;
  if (searchString.value) {
    filters = `store_id:${storeId.value},` + 'name:' + searchString.value;
  }

  if (selectedCategory.value && typeof selectedCategory.value === 'object' && 'id' in selectedCategory.value) {
    filters += ',category_id:' + (selectedCategory.value as { id: number }).id;
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

useCommon.$subscribe(async (mutation) => {
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
// ── Page ──────────────────────────────────────────────────────────────────
.store-page-container {
  max-width: 1400px;
  margin: 0 auto;
  width: 100%;
  overflow-x: hidden;
}

// ── Hero Banner ───────────────────────────────────────────────────────────
.store-hero {
  position: relative;
  background: linear-gradient(145deg, #1e1b4b 0%, #312e81 55%, #4c1d95 100%);
  overflow: hidden;
  width: 100%;
}

.store-hero-bg {
  position: absolute;
  inset: 0;
  pointer-events: none;
}

.hero-orb {
  position: absolute;
  border-radius: 50%;
  filter: blur(70px);
  animation: heroOrb 12s ease-in-out infinite;

  &.orb-1 {
    width: 500px;
    height: 500px;
    background: rgba(139, 92, 246, 0.3);
    top: -200px;
    right: -100px;
  }

  &.orb-2 {
    width: 280px;
    height: 280px;
    background: rgba(99, 102, 241, 0.2);
    bottom: -60px;
    left: -40px;
    animation-delay: 5s;
  }
}

.hero-grid {
  position: absolute;
  inset: 0;
  background-image:
    linear-gradient(rgba(255, 255, 255, 0.04) 1px, transparent 1px),
    linear-gradient(90deg, rgba(255, 255, 255, 0.04) 1px, transparent 1px);
  background-size: 48px 48px;
}

@keyframes heroOrb {

  0%,
  100% {
    transform: translate(0, 0);
  }

  50% {
    transform: translate(16px, -20px);
  }
}

.store-hero-inner {
  position: relative;
  z-index: 1;
  padding: 20px 32px 32px;
}

.breadcrumb-wrap {
  margin-bottom: 20px;

  :deep(.q-breadcrumbs) {
    color: rgba(255, 255, 255, 0.5);
  }

  :deep(.q-breadcrumbs__el) {
    color: rgba(255, 255, 255, 0.5);
  }

  :deep(.q-breadcrumbs__separator) {
    color: rgba(255, 255, 255, 0.3);
  }

  :deep(a) {
    color: rgba(255, 255, 255, 0.65) !important;
  }
}

.store-hero-content {
  display: flex;
  align-items: center;
  gap: 20px;
  margin-bottom: 24px;
}

.store-logo-wrap {
  width: 72px;
  height: 72px;
  border-radius: 18px;
  background: rgba(255, 255, 255, 0.12);
  border: 2px solid rgba(255, 255, 255, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  overflow: hidden;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
}

.store-logo-img {
  width: 100%;
  height: 100%;
}

.store-meta {
  flex: 1;
  min-width: 0;
}

.store-name {
  font-size: 28px;
  font-weight: 900;
  color: white;
  letter-spacing: -0.6px;
  line-height: 1.15;
  margin-bottom: 6px;
}

.store-address {
  display: flex;
  align-items: center;
  font-size: 13px;
  color: rgba(255, 255, 255, 0.5);
  font-weight: 500;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.store-hero-stats {
  display: inline-flex;
  align-items: center;
  gap: 20px;
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.12);
  border-radius: 14px;
  padding: 14px 24px;
  backdrop-filter: blur(10px);
}

.hero-stat-value {
  font-size: 26px;
  font-weight: 900;
  color: white;
  line-height: 1;
  margin-bottom: 3px;
}

.hero-stat-label {
  font-size: 11px;
  color: rgba(255, 255, 255, 0.4);
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.hero-stat-divider {
  width: 1px;
  height: 36px;
  background: rgba(255, 255, 255, 0.15);
}

// ── Filter Bar ────────────────────────────────────────────────────────────
.filter-bar {
  background: white;
  border-bottom: 1px solid #e9ecef;
  padding: 16px 32px;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
  position: sticky;
  top: 0;
  z-index: 10;
}

.filter-bar-inner {
  display: grid;
  grid-template-columns: 1fr 1fr auto;
  gap: 12px;
  align-items: center;
}

.filter-input {
  :deep(.q-field__control) {
    border-radius: 10px;
    height: 44px;
    transition: all 0.2s;
  }

  :deep(.q-field--focused .q-field__control) {
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
  }
}

.clear-btn {
  height: 44px;
  padding: 0 16px;
  border-radius: 10px;
  color: #6366f1;
  font-weight: 600;
  white-space: nowrap;

  &:hover {
    background: rgba(99, 102, 241, 0.08);
  }
}

.active-filters {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-top: 12px;
}

.filter-chip {
  background: rgba(99, 102, 241, 0.1) !important;
  color: #4f46e5 !important;
  font-weight: 600;
  border-radius: 8px;
}

// ── Results Bar ───────────────────────────────────────────────────────────
.results-bar {
  padding: 14px 32px;
  background: #f8f9fb;
  border-bottom: 1px solid #eef0f3;
}

.results-count {
  font-size: 13px;
  font-weight: 700;
  color: #6b7280;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

// ── Products Grid ─────────────────────────────────────────────────────────
.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  gap: 1px;
  background: #e9ecef;
  margin-bottom: 0;
}

.product-card-link {
  text-decoration: none;
  color: inherit;
  display: block;
  background: white;
}

.product-card {
  display: flex;
  flex-direction: column;
  height: 100%;
  background: white;
  transition: all 0.25s ease;
  cursor: pointer;

  &:hover {
    z-index: 2;
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.12);
    transform: translateY(-2px);
  }
}

.product-image-wrap {
  position: relative;
  width: 100%;
  overflow: hidden;
  background: #f8f9fa;
}

.product-image {
  width: 100%;
  transition: transform 0.4s ease;
}

.product-card:hover .product-image {
  transform: scale(1.06);
}

.product-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, rgba(99, 102, 241, 0.85) 0%, rgba(139, 92, 246, 0.85) 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.25s ease;
}

.product-card:hover .product-overlay {
  opacity: 1;
}

.overlay-btn {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
  color: white;
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 1px;
  transform: translateY(8px);
  transition: transform 0.25s ease;
}

.product-card:hover .overlay-btn {
  transform: translateY(0);
}

.product-info {
  padding: 14px 16px;
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.product-name {
  font-size: 14px;
  font-weight: 600;
  color: #111827;
  line-height: 1.45;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  min-height: 2.9em;
  margin-bottom: 10px;
}

.product-price-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.product-price {
  font-size: 15px;
  font-weight: 800;
  background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.product-arrow {
  width: 24px;
  height: 24px;
  border-radius: 6px;
  background: #f3f4f6;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #9ca3af;
  transition: all 0.2s;
}

.product-card:hover .product-arrow {
  background: #4f46e5;
  color: white;
}

// ── Empty State ───────────────────────────────────────────────────────────
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 80px 32px;
  text-align: center;
  background: white;
}

.empty-icon-wrap {
  width: 88px;
  height: 88px;
  border-radius: 22px;
  background: #f3f4f6;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 20px;
}

.empty-title {
  font-size: 20px;
  font-weight: 800;
  color: #374151;
  margin-bottom: 8px;
}

.empty-sub {
  font-size: 14px;
  color: #9ca3af;
  font-weight: 500;
  line-height: 1.6;
  margin-bottom: 24px;
}

.clear-filters-btn {
  background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
  color: white;
  padding: 0 24px;
  height: 44px;
  border-radius: 12px;
  font-weight: 700;
  box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
}

// ── Pagination ────────────────────────────────────────────────────────────
.pagination-wrap {
  display: flex;
  justify-content: center;
  padding: 32px;
  background: white;
  border-top: 1px solid #e9ecef;
}

.store-pagination {
  :deep(.q-btn) {
    border-radius: 8px;
    margin: 0 2px;
    min-width: 36px;
    height: 36px;
  }
}

// ── Responsive ────────────────────────────────────────────────────────────
@media (max-width: 768px) {
  .store-hero-inner {
    padding: 16px 20px 24px;
  }

  .store-name {
    font-size: 22px;
  }

  .store-hero-stats {
    width: 100%;
    justify-content: center;
  }

  .filter-bar {
    padding: 14px 16px;
    position: static;
  }

  .filter-bar-inner {
    grid-template-columns: 1fr 1fr;
    grid-template-rows: auto auto;
  }

  .clear-btn {
    grid-column: 1 / -1;
    width: 100%;
  }

  .results-bar {
    padding: 12px 16px;
  }

  .products-grid {
    grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
  }
}

@media (max-width: 480px) {
  .store-hero-content {
    flex-direction: column;
    align-items: flex-start;
    gap: 14px;
  }

  .store-logo-wrap {
    width: 56px;
    height: 56px;
  }

  .store-name {
    font-size: 20px;
  }

  .filter-bar-inner {
    grid-template-columns: 1fr;
  }

  .clear-btn {
    grid-column: 1;
  }

  .products-grid {
    grid-template-columns: 1fr 1fr;
    gap: 1px;
  }

  .product-info {
    padding: 12px;
  }

  .product-name {
    font-size: 13px;
  }

  .product-price {
    font-size: 14px;
  }

  .empty-state {
    padding: 60px 20px;
  }

  .pagination-wrap {
    padding: 20px;
  }
}
</style>
