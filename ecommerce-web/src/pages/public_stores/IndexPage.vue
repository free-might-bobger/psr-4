<template>
  <div class="q-ma-sm">
    <div class="row q-col-gutter-xs">
      <div class="col-md-6 col-xs-12">
        <div class="flex items-center breadcrumbs-container">
          <BreadCrumbsWrapper :bread-crumbs="[{
            name: store.name,
            path: ''
          }]" />
        </div>

      </div>
      <div class="col-md-3 col-xs-6">
        <q-select outlined v-model="selectedCategory" :options="categories" label="Select Category" hide-bottom-space
          use-input dense class="category-select">
          <template v-slot:append>
            <q-icon v-if="selectedCategory !== ''" name="close" @click.stop.prevent="selectedCategory = ''"
              class="cursor-pointer" />
          </template>
        </q-select>
      </div>
      <div class="col-md-3 col-xs-6">
        <q-input outlined dense v-model="searchString" placeholder="Search Item..." debounce="800">
          <template v-slot:prepend>
            <q-icon v-if="searchString === ''" name="search" />
            <q-icon v-else name="clear" class="cursor-pointer" @click="searchString = ''" />
          </template>
        </q-input>
      </div>
    </div>
    <div class="main-store-wrapper">
      <router-link :to="`/public_stores/${route.params.id}/item/${val.optimus_id}`" class="main-store-generic-box"
        :class="{ 'main-store-generic-box-large': result.length <= 2 }" v-for="val in result" :key="val.id">
        <div class="row">
          <div class="col-12">
            <img :src="val.primary_img?.path_thumbnail" width="100" class="main-store-item-image" />
          </div>
          <div class="col-12">
            <div class="main-store-item-wrapper">
              <div class="main-store-item-desc">
                <div class="main-store-item-name">{{ val.name }}</div>
                <div class="main-store-item-price q-mt-sm">
                  {{ getPriceRange(val.item_price || []) }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </router-link>
    </div>
    <br />
    <div class="q-pa-sm flex flex-center" v-if="result.length > 0" outline>
      <q-pagination v-model="pagination.page" :max="pagination.lastPage" boundary-links />
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
</script>
