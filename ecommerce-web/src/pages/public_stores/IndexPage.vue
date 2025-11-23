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
    <div class="wrapper">
      <div class="flex-container">
        <router-link :to="`/public_stores/${route.params.id}/item/${val.optimus_id}`" class="generic-box"
          v-for="val in result" :key="val.id">
          <div class="row">
            <div class="col-4">
              <img :src="val.primary_img?.path_thumbnail" width="100" class="item-image" />
            </div>
            <div class="col">
              <div class="item-wrapper">
                <span class="item-desc">
                  <br />
                  <div style="font-weight: bold">{{ val.name }}</div>
                  <div class="q-mt-sm">
                    {{ getPriceRange(val.item_price) }}
                  </div>
                </span>
              </div>
            </div>

          </div>
        </router-link>
      </div>
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
import { onMounted, ref, watch } from 'vue';
import { get, show } from 'src/boot/axios-call';
import { useCommonStore } from 'src/stores/common';
import { getPriceRange } from 'src/boot/utilities';
import BreadCrumbsWrapper from 'src/components/BreadCrumbsWrapper.vue'
import { truncateString } from 'src/boot/common';

const useItem = useItemStore();
const { searchString, selectedCategory } = storeToRefs(useItem);
const useCommon = useCommonStore();
const { lat, lng, mobile } = storeToRefs(useCommon);

const { pagination, result } = storeToRefs(useCommon);
const store = ref({
  name: '',
  logo: { path_url: '' },
  default_address: {
    complete_address: '',
  },
});
const markerDrag = (e: { latLng: google.maps.LatLng }) => {
  lat.value = e.latLng.lat();
  lng.value = e.latLng.lng();
};

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

  categories.value = cat.data.data;
};

const onRequest = async () => {
  let filters = `store_id:${storeId.value}`;
  if (searchString.value) {
    filters = `store_id:${storeId.value},` + 'name:' + searchString.value;
  }

  if (selectedCategory.value) {
    filters += ',category_id:' + selectedCategory.value?.id;
  }

  useCommon.setResultPagination(
    {
      entity: 'items',
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
});

// useCommon.$subscribe(async (mutation, state) => {
//   if (mutation.events) {
//     if (mutation.events?.key == 'page') {
//       onRequest();
//     }
//   }
// });

watch(selectedCategory, () => {
  onRequest();
});

watch(searchString, () => {
  onRequest();
});
</script>


<style scoped>
.breadcrumbs-container {
  height: 40px;
  border: 1px solid #c2c2c2;
  border-radius: 5px;
  padding: 8px;
}

</style>