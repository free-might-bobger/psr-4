<template>
  <div>
    <q-table
      class="my-sticky-header-table q-mt-sm desktop-only"
      flat
      bordered
      :rows="stores"
      :columns="storeColumns"
      row-key="name"
      virtual-scroll
      v-model:pagination="pagination"
      @request="getStores"
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
              <router-link :to="`/dashboard/stores/${props.row.optimus_id}`"
                >{{ col.value }}
              </router-link>
            </span>
            <span v-else-if="col.name === 'Actions'">
              <q-btn
                flat
                color="primary"
                icon="edit"
                :to="`/dashboard/stores/${props.row.optimus_id}`"
              >
                <q-tooltip> Edit Store </q-tooltip>
              </q-btn>
              <q-btn
                flat
                color="negative"
                icon="delete"
                @click="deleteStore(props.row)"
              >
                <q-tooltip> Delete Store </q-tooltip>
              </q-btn>
            </span>
            <span v-else>{{ col.value }}</span>
          </q-td>
        </q-tr>
      </template>
      <template v-slot:pagination="scope">
        {{ scope.pagination.from }} - {{ scope.pagination.to }} of
        {{ scope.pagination.rowsNumber }}
        <q-btn
          v-if="scope.pagesNumber > 2"
          icon="first_page"
          color="grey-8"
          round
          dense
          flat
          :disable="scope.isFirstPage"
          @click="firstPage"
        />

        <q-btn
          icon="chevron_left"
          color="grey-8"
          round
          dense
          flat
          :disable="scope.isFirstPage"
          @click="previousPage"
        />

        <q-btn
          icon="chevron_right"
          color="grey-8"
          round
          dense
          flat
          :disable="scope.isLastPage"
          @click="nextPage"
        />

        <q-btn
          v-if="scope.pagesNumber > 2"
          icon="last_page"
          color="grey-8"
          round
          dense
          flat
          :disable="scope.isLastPage"
          @click="lastPageButton"
        />
      </template>
    </q-table>
  </div>
</template>

<script lang="ts" setup>
import { onMounted, ref } from 'vue';
import { get } from 'src/boot/axios-call';
import { storeColumns } from 'src/boot/columns'
import { storeToRefs } from 'pinia';
import { useCommonStore } from 'src/stores/common';

const useCommon = useCommonStore();
const { pagination, result } = storeToRefs(useCommon);

const stores = ref([]);
const getStores = async () => {
  const result = await get(
    {
      message: 'Getting stores...',
      entity: 'stores',
      query: {
        orderBy: 'name:asc',
        columns: 'id,name',
      },
    },
    false
  );

  stores.value = result.data.data;
};

onMounted(() => {
  getStores();
});

</script>
