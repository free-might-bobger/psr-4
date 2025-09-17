<template>
  <div>
    <br />
    <div class="row q-px-sm">
      <div class="col text-left">
        <span style="font-size: 20px">Transactions</span>
      </div>
      <!-- <div class="col-8">
        <q-input
          outlined
          dense
          debounce="300"
          v-model="searchStore"
          placeholder="Search "
          class="full-width"
          clearable
        >
          <template v-slot:append>
            <q-icon name="search" />
          </template>
        </q-input>
      </div> -->
    </div>
    <q-table
      grid
      title="Transactions"
      :rows="result"
      :columns="customerTransactionsColumns"
      row-key="name"
      hide-header
      @request="onRequest"
      :visible-columns="[]"
      class="no-margin no-padding"
      hide-bottom
    >
      <template v-slot:top> </template>
      <template v-slot:item="props">
        <div
          class="q-pa-xs col-xs-12 col-sm-6 col-md-4 col-lg-3 grid-style-transition"
          :style="props.selected ? 'transform: scale(0.95);' : ''"
        >
          <q-card
            flat
            bordered
            :class="props.selected ? 'bg-grey-2' : ''"
            @click="props.selected = !props.selected"
          >
            <q-card-section>
              <div
                class="flex justify-between items-center"
                style="height: 35px"
              >
                <span class="text-body1">{{ props.row.status?.label }}</span>
                <q-btn-group outline rounded>
                  <q-btn
                    :to="`${$route.path}/${props.row.optimus_id}`"
                    outline
                    color="negative"
                  >
                    Received
                  </q-btn>
                  <q-btn
                    :to="`${$route.path}/${props.row.optimus_id}`"
                    outline
                    color="primary"
                  >
                    View
                  </q-btn>
                </q-btn-group>
              </div>
              <div style="clear: both" />
            </q-card-section>
            <q-separator />
            <q-list dense>
              <q-item v-for="(col, k) in props.cols" :key="k">
                <q-item-section>
                  <q-item-label overline>{{ col.label }}</q-item-label>
                </q-item-section>
                <q-item-section side>
                  <q-item-label caption>{{ col.value }}</q-item-label>
                </q-item-section>
              </q-item>
            </q-list>
            <br />
          </q-card>
        </div>
        
      </template>
    </q-table>
    <br />
    <div class="flex justify-center">
      <q-pagination v-model="pagination.page" color="primary" :max="pagination.lastPage" boundary-links />
    </div>
  </div>
</template>
<script setup lang="ts">
import { onRequest } from 'boot/axios-call';
import { useCommonStore } from 'src/stores/common';
import { onMounted, ref, watch } from 'vue';
import { storeToRefs } from 'pinia';
import { customerTransactionsColumns } from 'boot/columns';

const search = ref('');
const useCommon = useCommonStore();
const { entityQuery, pagination, result } = storeToRefs(useCommon);

entityQuery.value = {
  message: 'Getting transactions...',
  entity: 'customer-transactions',
  query: {
    with: 'status,paymentMethod,receiveMethod',
    orderBy: 'created_at:desc',
    page: pagination.value.page,
    limit: 10,
  },
};

onMounted(() => {
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

watch(() => pagination.value.page, (newPage) => {
  entityQuery.value.query.page = newPage;
  onRequest(entityQuery.value)
})
</script>
