<template>
  <div class="q-ma-sm">
    <div>
      <q-form
        @submit.prevent="sendEmailInvitation"
        class="row q-gutter-sm"
        ref="myForm"
      >
        <div class="col">
          <q-select
            clearable
            outlined
            v-model="selectedStore"
            :options="stores"
            dense
            label="Select store"
            lazy-rules
            use-chips
            :rules="[(val) => validateRequired(val) || 'Access Right is required.']"
          />
        </div>
        <div class="col">
          <q-input
            label="Invite user by email..."
            v-model="email"
            dense
            outlined
            :rules="[(val) => onValidateValidEmail(val) || 'Please enter a valid email.']"
            debounce="500"
          >
          </q-input>
        </div>
        <div class="col-2">
          <q-btn
          type="submit"
          label="Send invitation"
          dense
          flat
          color="primary"
          class="q-mt-sm"
        />
        </div>
        
      </q-form>
    </div>
    <br />
    <q-table
      flat
      bordered
      :rows="result"
      :columns="StoreMenuAccessColumns"
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
              {{ col.value }}
            </span>
            <span v-else-if="col.name === 'Actions'">
              <q-btn
                class="q-ml-sm"
                outline
                dense
                color="primary"
                icon="far fa-address-card"
                :to="`${$route.path}/${props.row.store.optimus_id}/create/${props.row.user.optimus_id}`"
              >
                <q-tooltip> New store menu access </q-tooltip>
              </q-btn>
              <q-btn
                class="q-ml-sm"
                outline
                dense
                color="primary"
                icon="edit"
                :to="`${$route.path}/${props.row.optimus_id}`"
              >
                <q-tooltip> Edit store menu access </q-tooltip>
              </q-btn>
              <q-btn
                outline
                dense
                class="q-ml-sm"
                color="negative"
                icon="delete"
                @click="
                  onDeleteEntity(
                    'store-menu-access',
                    props.row.optimus_id,
                    `${props.row.user.name} access`
                  )
                "
              >
                <q-tooltip>Delete store menu access</q-tooltip>
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
import { onRequest, get, create } from 'boot/axios-call';
import { useCommonStore } from 'src/stores/common';
import { onMounted, ref, watch } from 'vue';
import { storeToRefs } from 'pinia';
import { StoreMenuAccessColumns } from 'boot/columns';
import { onDeleteEntity } from 'boot/services';
import { validateRequired, validateValidEmail } from 'src/boot/validators';
const email = ref('');
const search = ref('');
const useCommon = useCommonStore();
const { entityQuery, pagination, result } = storeToRefs(useCommon);
entityQuery.value = {
  entity: 'store-menu-access',
  query: {
    orderBy: 'created_at:desc',
    page: pagination.value.page,
    limit: 10,
    with: 'accessRight,storeMenu,user,store',
  },
};

onMounted(() => {
  result.value = [];
  entityQuery.value.query.page = 1;
  onRequest(entityQuery.value, true);
  getMyStores();
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

const sendEmailInvitation = async () => {
  await create({
    entity: 'send-email-invitation',
    data: {
      email: email.value,
      storeId: selectedStore.value?.id
    }
  })
};

const selectedStore = ref<{ id: number }|null>(null);

const stores = ref([]);

const getMyStores = async () => {
  const result = await get(
    {
      entity: 'my-stores',
      query: {
        orderBy: 'created_at:desc',
        page: pagination.value.page,
        limit: 10,
      },
    },
    false
  );
  stores.value = result.data.data;
};

const onValidateValidEmail =(email: string): boolean => {
  const result = validateValidEmail(email);
  return result.status;
};
</script>
