<template>
  <div class="stores-page-container">
    <!-- Header Section -->
    <div class="page-header q-mb-md">
      <div class="header-content">
        <div class="header-title-section">
          <q-icon name="store" size="32px" color="primary" class="q-mr-sm" />
          <h2 class="page-title">All Stores</h2>
        </div>
        <div class="header-actions">
          <q-checkbox v-model="includeDeleted" label="Include deleted" class="q-mr-sm" />
          <q-input v-model="search" placeholder="Search stores..." outlined dense clearable debounce="1000"
            class="search-input">
            <template v-slot:prepend>
              <q-icon name="search" />
            </template>
          </q-input>
        </div>
      </div>
    </div>

    <!-- Desktop Table View -->
    <div class="desktop-only">
      <div v-if="typedResult.length === 0" class="empty-state-desktop">
        <q-icon name="store" size="80px" color="grey-4" />
        <div class="text-h5 q-mt-md text-grey-6">No stores found</div>
        <div class="text-body2 text-grey-5 q-mt-sm">Try adjusting your search criteria</div>
      </div>
      <q-table v-else flat bordered :rows="typedResult" :columns="columns" row-key="optimus_id" class="stores-table"
        :rows-per-page-options="[0]" hide-pagination>
        <template v-slot:body-cell-name="props">
          <q-td :props="props">
            <router-link :to="`/dashboard/all-stores/${props.row.optimus_id}`" class="store-name-link">
              <q-icon name="store" color="primary" class="q-mr-xs" />
              <span>{{ props.row.name }}</span>
            </router-link>
          </q-td>
        </template>

        <template v-slot:body-cell-mobile="props">
          <q-td :props="props">
            <div class="mobile-cell">
              <q-icon name="phone" color="primary" class="q-mr-xs" />
              <span>{{ props.row.mobile || 'N/A' }}</span>
            </div>
          </q-td>
        </template>

        <template v-slot:body-cell-actions="props">
          <q-td :props="props">
            <div class="action-buttons">
              <q-btn unelevated dense color="primary" icon="edit_note" :to="`${$route.path}/${props.row.optimus_id}`"
                size="md" class="q-mr-xs">
                <q-tooltip>Edit Store</q-tooltip>
              </q-btn>
              <q-btn unelevated dense color="negative" icon="delete_forever" @click="handleDeleteStore(props.row)"
                size="md" v-if="!props.row.deleted_at">
                <q-tooltip>Delete Store</q-tooltip>
              </q-btn>
              <q-btn unelevated dense color="secondary" icon="restore" @click="handleRestoreStore(props.row)" size="md"
                v-else>
                <q-tooltip>Restore Store</q-tooltip>
              </q-btn>
            </div>
          </q-td>
        </template>

        <template v-slot:bottom>
          <div class="table-pagination">
            <div class="pagination-info">
              Showing {{ pagination.from }} - {{ pagination.to }} of {{ pagination.rowsNumber }} stores
            </div>
            <div class="pagination-controls">
              <q-btn v-if="pagination.lastPage > 2" flat round dense icon="first_page" color="grey-8"
                :disable="pagination.page === 1" @click="goToFirstPage" />
              <q-btn flat round dense icon="chevron_left" color="grey-8" :disable="pagination.page === 1"
                @click="goToPreviousPage" />
              <span class="page-number">{{ pagination.page }} / {{ pagination.lastPage }}</span>
              <q-btn flat round dense icon="chevron_right" color="grey-8"
                :disable="pagination.page === pagination.lastPage" @click="goToNextPage" />
              <q-btn v-if="pagination.lastPage > 2" flat round dense icon="last_page" color="grey-8"
                :disable="pagination.page === pagination.lastPage" @click="goToLastPage" />
            </div>
          </div>
        </template>
      </q-table>
    </div>

    <!-- Mobile Card View -->
    <div class="mobile-only">
      <div v-if="typedResult.length === 0" class="empty-state">
        <q-icon name="store" size="64px" color="grey-4" />
        <div class="text-h6 q-mt-md text-grey-6">No stores found</div>
      </div>
      <div v-else class="stores-cards">
        <q-card v-for="store in typedResult" :key="store.id" flat bordered class="store-card q-mb-md">
          <q-card-section>
            <div class="store-card-header">
              <div class="store-card-title">
                <q-icon name="store" color="primary" size="24px" class="q-mr-sm" />
                <router-link :to="`/dashboard/all-stores/${store.optimus_id}`" class="store-name-link">
                  {{ store.name }}
                </router-link>
              </div>
            </div>
            <div v-if="store.mobile" class="store-card-info q-mt-sm">
              <q-icon name="phone" size="16px" color="grey-6" class="q-mr-xs" />
              <span class="text-body2 text-grey-7">{{ store.mobile }}</span>
            </div>
            <div class="store-card-actions q-mt-md">
              <q-btn unelevated dense color="primary" icon="edit_note" label="Edit"
                :to="`/dashboard/all-stores/${store.optimus_id}`" class="action-btn-mobile action-btn-edit-mobile" />
              <q-btn unelevated dense color="negative" icon="delete_forever" label="Delete" v-if="!store.deleted_at"
                @click="handleDeleteStore(store)" class="action-btn-mobile action-btn-delete-mobile" />
            </div>
          </q-card-section>
        </q-card>
      </div>
      <!-- Mobile Pagination -->
      <div v-if="typedResult.length > 0" class="mobile-pagination q-mt-md">
        <q-pagination v-model="pagination.page" :max="pagination.lastPage" :max-pages="5" direction-links boundary-links
          color="primary" @update:model-value="handlePageChange" />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref, watch } from 'vue';
import { onRequest, firstPage, previousPage, nextPage, lastPage } from 'src/boot/axios-call';
import { storeToRefs } from 'pinia';
import { useCommonStore } from 'src/stores/common';
import { onDeleteEntity, onRestoreEntity } from 'src/boot/services';
import { StoreRow } from 'src/boot/interfaces';

const useCommon = useCommonStore();
const { pagination, result, entityQuery } = storeToRefs(useCommon);

const search = ref('');
const includeDeleted = ref(false);

// Initialize entity query
entityQuery.value = {
  message: 'Getting stores...',
  entity: 'all_stores',
  query: {
    orderBy: 'name:asc',
    columns: 'id,name,mobile',
    page: pagination.value.page,
    limit: 12,
  },
};

// Type the result as StoreRow array
const typedResult = result as unknown as StoreRow[];

const columns = [
  {
    name: 'name',
    required: true,
    label: 'Store Name',
    align: 'left' as const,
    field: 'name',
    sortable: true
  },
  {
    name: 'mobile',
    required: true,
    label: 'Mobile',
    align: 'left' as const,
    field: 'mobile',
    sortable: true
  },
  {
    name: 'actions',
    required: true,
    label: 'Actions',
    align: 'center' as const,
    field: ''
  }
];

const handleDeleteStore = (store: StoreRow) => {
  onDeleteEntity('all_stores', store.optimus_id, store.name);
};

const handleRestoreStore = (store: StoreRow) => {
  onRestoreEntity('all_stores', store.optimus_id, store.name);
};

const handlePageChange = (page: number) => {
  entityQuery.value.query.page = page;
  onRequest(entityQuery.value);
};

const goToFirstPage = () => {
  firstPage(entityQuery.value);
};

const goToPreviousPage = () => {
  previousPage(entityQuery.value);
};

const goToNextPage = () => {
  nextPage(entityQuery.value);
};

const goToLastPage = () => {
  lastPage(entityQuery.value, pagination.value);
};

onMounted(() => {
  result.value = [];
  entityQuery.value.query.page = 1;
  onRequest(entityQuery.value, true);
});

watch(search, (newValue) => {
  if (newValue) {
    entityQuery.value.query.filters = 'name:' + search.value;
  } else {
    delete entityQuery.value.query.filters;
  }
  entityQuery.value.query.page = 1;
  onRequest(entityQuery.value);
});

watch(includeDeleted, (newValue) => {
  if (newValue) {
    entityQuery.value.query.deleted = 1;
  } else {
    delete entityQuery.value.query.deleted;
  }
  entityQuery.value.query.page = 1;
  onRequest(entityQuery.value, true);
});
</script>

<style scoped lang="scss">
@import 'src/css/dashboard/all-stores/index.scss';

.stores-table {
  width: 100%;

  :deep(.q-table) {
    border-radius: 12px;
    overflow: hidden;
  }

  :deep(.q-table th) {
    font-weight: 600;
    font-size: 14px;
    padding: 16px 24px;
    background: linear-gradient(135deg, #f5f7fa 0%, #ffffff 100%);
    color: #1a1a1a;
  }

  :deep(.q-table td) {
    padding: 20px 24px;
    font-size: 15px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.06);
  }

  :deep(.q-table tbody tr) {
    transition: all 0.3s ease;

    &:hover {
      background: rgba(25, 118, 210, 0.04);
    }
  }

  :deep(.q-table tbody tr:last-child td) {
    border-bottom: none;
  }
}

.store-name-link {
  text-decoration: none;
  color: inherit;
  display: flex;
  align-items: center;
  font-weight: normal;
  font-size: 14px;
  transition: all 0.3s ease;

  &:hover {
    color: #1976d2;
    transform: translateX(4px);
  }
}

.mobile-cell {
  display: flex;
  align-items: center;
  font-size: 15px;
  color: #1a1a1a;
}

.action-buttons {
  display: flex;
  gap: 8px;
  justify-content: center;
}

.table-pagination {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 16px;
}

.pagination-info {
  font-size: 13px;
  color: #666;
}

.pagination-controls {
  display: flex;
  align-items: center;
  gap: 8px;
}

.page-number {
  font-size: 13px;
  color: #1a1a1a;
  font-weight: 600;
  min-width: 50px;
  text-align: center;
}
</style>
