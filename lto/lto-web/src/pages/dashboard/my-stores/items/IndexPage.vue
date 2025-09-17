<template>
  <div class="q-ma-sm">
    <div class="row q-gutter-sm">
      <div class="col">
        <q-btn
          :label="store.name"
          outline
          class="q-mt-sm"
          icon="fa-solid fa-angles-left"
          color="primary"
          @click="router.back()"
        />
      </div>
      <div class="col">
        <q-input
          outlined
          dense
          debounce="300"
          v-model="search"
          placeholder="Search by name"
          class="full-width q-mt-sm"
          clearable
        >
          <template v-slot:append>
            <q-icon name="search" />
          </template>
        </q-input>
      </div>
      <div class="col">
        <q-select
          outlined
          v-model="selectedCategory"
          :options="categories"
          label="Select Category"
          hide-bottom-space
          use-input
          dense
          class="q-mt-sm"
        >
          <template v-slot:append>
            <q-icon
              v-if="selectedCategory !== ''"
              name="close"
              @click.stop.prevent="selectedCategory = ''"
              class="cursor-pointer"
            />
          </template>
        </q-select>
      </div>
    </div>
    <div class="row">
      <div class="col q-mt-sm">
        <q-table
          flat
          bordered
          :rows="result"
          :columns="itemColumns"
          row-key="name"
          virtual-scroll
          v-model:pagination="pagination"
          @request="onRequest"
          :rows-per-page-options="[]"
        >
          <template v-slot:header="props">
            <q-tr :props="props">
              <q-th v-for="col in props.cols" :key="col.name" :props="props">
                {{ col.label }}
              </q-th>
            </q-tr>
          </template>

          <template v-slot:body="props">
            <q-tr :props="props">
              <q-td v-for="col in props.cols" :key="col.name" :props="props">
                <span v-if="col.name === 'name'">
                  <router-link :to="`${$route.path}/${props.row.optimus_id}`" class="router-link"
                    >{{ col.value }}
                  </router-link>
                </span>
                <span v-else-if="col.name === 'Actions'">
                  <q-btn
                    outline
                    class="q-ml-sm"
                    dense
                    color="primary"
                    icon="fa-solid fa-money-check-dollar"
                    :to="`${$route.path}/${props.row.optimus_id}/item-prices?filters=store_id:${store.optimus_id}`"
                  >
                  <q-tooltip> Item Prices </q-tooltip>
                  </q-btn>
                  <q-btn
                    class="q-ml-sm"
                    outline
                    dense
                    color="primary"
                    icon="edit"
                    :to="`${$route.path}/${props.row.optimus_id}?filters=store_id:${store.optimus_id}`"
                  >
                  <q-tooltip> Edit Item </q-tooltip>
                  </q-btn>
                  <q-btn
                    class="q-ml-sm"
                    outline
                    dense
                    color="negative"
                    icon="delete"
                    @click="onDeleteEntity('items', props.row.optimus_id, props.row.name)"
                  >
                    <q-tooltip>Delete Item</q-tooltip>
                  </q-btn>
                </span>
                <span v-else>{{ col.value }}</span>
              </q-td>
            </q-tr>
          </template>
        </q-table>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useItemStore } from 'src/stores/item';
import { ref, onBeforeMount, watch } from 'vue';
import { show, get } from 'src/boot/axios-call';
import { useRoute, useRouter  } from 'vue-router';
import { storeToRefs } from 'pinia';
import { useCommonStore } from 'src/stores/common';
import { itemColumns } from 'boot/columns';
import { onDeleteEntity } from 'boot/services';

const useCommon = useCommonStore();
const { pagination, result } = storeToRefs(useCommon);
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
});

const search = ref('')
onBeforeMount(async () => {
  result.value = []
  store.value = await show({
    entity: 'my-stores',
    optimus_id: Number(route.params.id),
  });
  await onRequest();
  getCategories();
});

const categories = ref([]);
const getCategories = async () => {
  let cat = await get(
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

const onRequest = async () => {
  let filters = `store_id:${store.value.optimus_id}`;
  if (searchString.value) {
    filters =
      `store_id:${store.value.optimus_id},` + 'name:' + searchString.value;
  }

  if (selectedCategory.value) {
    filters += ',category_id:' + selectedCategory.value?.id;
  }

  useCommon.setResultPagination(
    {
      message: 'Getting items...',
      entity: 'items',
      query: {
        filters: filters,
        page: pagination.value.page,
      },
    },
    true
  );
};

watch(selectedCategory, () => {
  onRequest()
})
</script>
