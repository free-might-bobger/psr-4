<template>
    <div>
      <div class="row q-mb-sm">
        <div class="col">
          <q-input
        clearable
        v-model="search"
        label="Search payment options..."
        dense
        outlined
        class="q-mb-sm"
        debounce="800"
      >
        <template v-slot:append>
          <q-icon name="search" />
        </template>
      </q-input>
        </div>
        <div class="col-3">
          <q-btn outline glossy color="primary" class="q-ma-xs" to="/dashboard/payment-options/create">New Payment Options</q-btn>
        </div>
      </div>
      <q-card flat bordered class="desktop-only">
        <q-table
          class="my-sticky-header-table desktop-only"
          flat
          bordered
          :rows="result"
          :columns="paymentOptionColumns"
          row-key="id"
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
                <span v-if="col.name === 'Actions'">
                  <q-btn
                    flat
                    color="primary"
                    icon="edit"
                    :to="`${$route.path}/${props.row.optimus_id}`"
                  >
                    <q-tooltip>Edit Subject</q-tooltip>
                  </q-btn>
                  <q-btn
                    flat
                    color="negative"
                    icon="delete"
                    @click="onDeleteEntity('payment_options', props.row)"
                  >
                    <q-tooltip>Delete Subject</q-tooltip>
                  </q-btn>
                </span>
                <span v-else>{{ col.value }}</span>
              </q-td>
            </q-tr>
            
          </template>
          <template v-slot:pagination="scope">
            <PaginationScope :scope="scope" />
          </template>
        </q-table>
      </q-card>
    </div>
  </template>
  
  <script setup lang="ts">
  import { onRequest } from 'boot/axios-call';
  import { useCommonStore } from 'src/stores/common';
  import { storeToRefs } from 'pinia';
  import { onMounted, ref, watch } from 'vue';
  import { paymentOptionColumns } from 'boot/columns';
  import { onDeleteEntity } from 'boot/services';
  import PaginationScope from 'src/components/PaginationScope.vue';
  
  const search = ref('');
  const useCommon = useCommonStore();
  const { admissions, entityQuery, pagination, result } = storeToRefs(useCommon);
  
  entityQuery.value =  {
        message: 'Getting Payment Options...',
        entity: 'payment_options',
        query: {
          orderBy: 'created_at:desc',
          page: pagination.value.page,
          limit: 10
        },
      };
  
  onMounted(() => {
    entityQuery.value.query.page = 1;
    onRequest(
  
      entityQuery.value,
      {
        collection: admissions,
        searchCollection: admissions,
      },
      true
    );
  });
  
  watch(search, (newValue) => {
    if (newValue) {
      entityQuery.value.query.filters = 'name:' + search.value;
    } else {
      delete entityQuery.value.query.filters;
    }
    onRequest(entityQuery.value);
  });

  </script>
  