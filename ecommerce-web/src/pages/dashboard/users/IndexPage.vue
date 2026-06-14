<template>
  <div class="stores-page-container">
    <!-- Header Section -->
    <div class="page-header q-mb-md">
      <div class="header-content">
        <div class="header-title-section">
          <q-icon name="person" size="32px" color="primary" class="q-mr-sm" />
          <h2 class="page-title">Users</h2>
        </div>
        <div class="header-actions">
          <q-input v-model="search" placeholder="Search users..." outlined dense clearable debounce="1000"
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
        <q-icon name="person_off" size="80px" color="grey-4" />
        <div class="text-h5 q-mt-md text-grey-6">No users found</div>
        <div class="text-body2 text-grey-5 q-mt-sm">Try adjusting your search criteria</div>
      </div>
      <q-table v-else flat bordered :rows="typedResult" :columns="columns" row-key="optimus_id" class="users-table"
        :rows-per-page-options="[0]" hide-pagination>
        <template v-slot:body-cell-name="props">
          <q-td :props="props">
            <router-link :to="`${$route.path}/${props.row.optimus_id}`" class="user-name-link">
              <q-icon name="person" color="primary" class="q-mr-xs" />
              <span>{{ props.row.name || props.row.label || 'N/A' }}</span>
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
                <q-tooltip>Edit User</q-tooltip>
              </q-btn>
              <q-btn unelevated dense color="negative" icon="delete_forever" @click="handleDeleteUser(props.row)"
                size="md">
                <q-tooltip>Delete User</q-tooltip>
              </q-btn>
            </div>
          </q-td>
        </template>

        <template v-slot:bottom>
          <div class="table-pagination">
            <div class="pagination-info">
              Showing {{ pagination.from }} - {{ pagination.to }} of {{ pagination.rowsNumber }} users
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
        <q-icon name="person_off" size="64px" color="grey-4" />
        <div class="text-h6 q-mt-md text-grey-6">No users found</div>
      </div>
      <div v-else class="stores-cards">
        <q-card v-for="user in typedResult" :key="user.id" flat bordered class="store-card q-mb-md">
          <q-card-section>
            <div class="store-card-header">
              <div class="store-card-title">
                <q-icon name="person" color="primary" size="24px" class="q-mr-sm" />
                <router-link :to="`${$route.path}/${user.optimus_id}`" class="store-name-link">
                  {{ user.name || user.label || 'N/A' }}
                </router-link>
              </div>
            </div>
            <div v-if="user.mobile" class="store-card-info q-mt-sm">
              <q-icon name="phone" size="16px" color="grey-6" class="q-mr-xs" />
              <span class="text-body2 text-grey-7">{{ user.mobile }}</span>
            </div>
            <div class="store-card-actions q-mt-md">
              <q-btn unelevated dense color="primary" icon="edit_note" label="Edit"
                :to="`${$route.path}/${user.optimus_id}`" class="action-btn-mobile action-btn-edit-mobile" />
              <q-btn unelevated dense color="negative" icon="delete_forever" label="Delete"
                @click="handleDeleteUser(user)" class="action-btn-mobile action-btn-delete-mobile" />
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
import { onDeleteEntity } from 'src/boot/services';

interface UserRow {
  id: number;
  name?: string;
  label?: string;
  mobile?: string;
  optimus_id: number;
}

const useCommon = useCommonStore();
const { pagination, result, entityQuery } = storeToRefs(useCommon);

const search = ref('');

entityQuery.value = {
  message: 'Getting users...',
  entity: 'users',
  query: {
    orderBy: 'name:asc',
    page: pagination.value.page,
    limit: 12,
  },
};

const typedResult = result as unknown as UserRow[];

const columns = [
  {
    name: 'name',
    required: true,
    label: 'User Name',
    align: 'left' as const,
    field: (row: UserRow) => row.name || row.label || 'N/A',
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

const handleDeleteUser = (user: UserRow) => {
  onDeleteEntity('users', user.optimus_id, user.name || user.label || 'User');
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
</script>

<style scoped lang="scss">
@import 'src/css/dashboard/all-stores/index.scss';

.stores-page-container {
  padding: 24px;
  max-width: 1400px;
  margin: 0 auto;
}

.page-header {
  background: #ffffff;
  border-radius: 16px;
  padding: 24px 32px;
  margin-bottom: 24px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 24px;
}

.header-title-section {
  display: flex;
  align-items: center;
  gap: 12px;
}

.page-title {
  font-size: 28px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0;
}

.search-input {
  min-width: 300px;
  max-width: 400px;

  :deep(.q-field__control) {
    border-radius: 10px;
  }
}

.empty-state-desktop {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 80px 24px;
  background: #ffffff;
  border-radius: 16px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
}

.users-table {
  width: 100%;

  :deep(.q-table) {
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
  }

  :deep(.q-table thead tr) {
    background: #f8f9fa;
  }

  :deep(.q-table th) {
    font-weight: 600;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 18px 24px;
    color: #6b7280;
    border-bottom: 2px solid #e5e7eb;
  }

  :deep(.q-table td) {
    padding: 18px 24px;
    font-size: 14px;
    border-bottom: 1px solid #f3f4f6;
    color: #374151;
  }

  :deep(.q-table tbody tr) {
    transition: all 0.2s ease;

    &:hover {
      background: #f9fafb;
    }
  }

  :deep(.q-table tbody tr:last-child td) {
    border-bottom: none;
  }
}

.user-name-link {
  text-decoration: none;
  color: #1a1a1a;
  display: flex;
  align-items: center;
  gap: 8px;
  font-weight: 500;
  font-size: 14px;
  transition: all 0.2s ease;

  &:hover {
    color: #3b82f6;
  }
}

.mobile-cell {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
  color: #374151;
}

.action-buttons {
  display: flex;
  gap: 6px;
  justify-content: center;
}

.table-pagination {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px 24px;
  background: #f8f9fa;
  border-top: 1px solid #e5e7eb;
}

.pagination-info {
  font-size: 13px;
  color: #6b7280;
  font-weight: 500;
}

.pagination-controls {
  display: flex;
  align-items: center;
  gap: 4px;
}

.page-number {
  font-size: 14px;
  color: #1a1a1a;
  font-weight: 600;
  min-width: 60px;
  text-align: center;
  padding: 0 8px;
}

// Mobile styles
.stores-cards {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.store-card {
  border-radius: 16px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
  overflow: hidden;
  transition: all 0.2s ease;

  &:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
  }
}

.store-card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.store-card-title {
  display: flex;
  align-items: center;
  gap: 10px;
  flex: 1;
}

.store-name-link {
  text-decoration: none;
  color: #1a1a1a;
  font-weight: 600;
  font-size: 16px;
  transition: all 0.2s ease;

  &:hover {
    color: #3b82f6;
  }
}

.store-card-info {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 0;
}

.store-card-actions {
  display: flex;
  gap: 8px;
  padding-top: 16px;
  border-top: 1px solid #f3f4f6;
}

.action-btn-mobile {
  flex: 1;
  border-radius: 10px;
  font-weight: 600;
  text-transform: none;
}

.action-btn-edit-mobile {
  background: #3b82f6;
}

.action-btn-delete-mobile {
  background: #ef4444;
}

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px 24px;
  background: #ffffff;
  border-radius: 16px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
}

.mobile-pagination {
  display: flex;
  justify-content: center;
  padding: 20px;
  background: #ffffff;
  border-radius: 16px;
  margin-top: 16px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
}

@media (max-width: 768px) {
  .stores-page-container {
    padding: 16px;
  }

  .page-header {
    padding: 20px;
    border-radius: 12px;
  }

  .header-content {
    flex-direction: column;
    align-items: stretch;
    gap: 16px;
  }

  .header-title-section {
    justify-content: center;
  }

  .page-title {
    font-size: 24px;
  }

  .search-input {
    max-width: 100%;
  }

  .empty-state-desktop,
  .empty-state {
    padding: 48px 20px;
  }
}
</style>
