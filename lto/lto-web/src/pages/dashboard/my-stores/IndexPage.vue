<template>
  <div class="q-ma-sm">
    <q-table
      flat
      bordered
      :rows="result"
      :columns="MyStoreColumns"
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
              <router-link :to="`${$route.path}/${props.row.optimus_id}`"
                >{{ col.value }}
              </router-link>
            </span>
            <span v-else-if="col.name === 'Actions'">
              <q-btn
                outline
                dense
                color="primary"
                icon="list"
                :to="`${$route.path}/${props.row.optimus_id}/items`"
              >
                <q-tooltip>Show Items</q-tooltip>
              </q-btn>
              <q-btn
                class="q-ml-sm"
                outline
                dense
                color="primary"
                icon="edit"
                :to="`${$route.path}/${props.row.optimus_id}`"
              >
                <q-tooltip> Edit Store </q-tooltip>
              </q-btn>
            </span>
            <span v-else>{{ col.value }}</span>
          </q-td>
        </q-tr>
      </template>
    </q-table>
  </div>
</template>
<script setup lang="ts">
import { onRequest } from 'boot/axios-call';
import { useCommonStore } from 'src/stores/common';
import { onMounted, ref, watch } from 'vue';
import { storeToRefs } from 'pinia';
import { MyStoreColumns } from 'boot/columns';

const search = ref('');
const useCommon = useCommonStore();
const { entityQuery, pagination, result } = storeToRefs(useCommon);
entityQuery.value = {
  entity: 'my-stores',
  query: {
    orderBy: 'created_at:desc',
    page: pagination.value.page,
    limit: 10,
  },
};

onMounted(() => {
  result.value = []
  entityQuery.value.query.page = 1;
  onRequest(entityQuery.value, true);
});

watch(search, (newValue) => {
  if (!newValue) {
    delete entityQuery.value.query.filters;
    onRequest(entityQuery.value);
    return;
  }
  entityQuery.value.query.filters = 'name:' + search.value;
  onRequest(entityQuery.value);
});

watch(
  () => pagination.value.page,
  (newPage) => {
    entityQuery.value.query.page = newPage;
    onRequest(entityQuery.value);
  }
);
</script>
