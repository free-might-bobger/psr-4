<template>
    <div class="stores-page-container">
        <!-- Header Section -->
        <div class="page-header q-mb-md">
            <div class="header-content">
                <div class="header-title-section">
                    <q-icon name="store" size="32px" color="primary" class="q-mr-sm" />
                    <h2 class="page-title">Store Users</h2>
                </div>
                <div class="header-actions">
                    <q-btn unelevated color="primary" icon="add" label="Invite User" :to="`${$route.path}/invite-users`"
                        class="q-mr-md" />
                    <q-input v-model="search" placeholder="Search email..." outlined dense clearable debounce="300"
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
            <q-table v-else flat bordered :rows="typedResult" :columns="columns" row-key="id"
                :pagination="{ rowsPerPage: 0 }" class="stores-table">
                <template v-slot:body-cell-name="props">
                    <q-td :props="props">
                        <router-link :to="`${$route.path}/${props.row.optimus_id}`" class="store-name-link">
                            {{ props.row.name }}
                        </router-link>
                    </q-td>
                </template>

                <template v-slot:body-cell-mobile="props">
                    <q-td :props="props">
                        {{ props.row.mobile || 'N/A' }}
                    </q-td>
                </template>

                <template v-slot:body-cell-actions="props">
                    <q-td :props="props">
                        <div class="action-buttons">
                             <q-btn unelevated dense color="primary" icon="list"
                                :to="`${$route.path}/access`" size="md" v-if="props.row.verified">
                                <q-tooltip>Access Management</q-tooltip>
                            </q-btn>
                            <q-btn unelevated dense color="negative" icon="delete_forever"
                                @click="handleDeleteUser(props.row)" size="md">
                                <q-tooltip>Delete User</q-tooltip>
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
                <div class="text-h6 q-mt-md text-grey-6">No store user found</div>
            </div>
            <div v-else class="stores-cards">
                <q-card v-for="storeUser in typedResult" :key="storeUser.id" flat bordered class="store-card q-mb-md">
                    <q-card-section>
                        <div class="store-card-header">
                            <div class="store-card-title">
                                <q-icon name="store" color="primary" size="24px" class="q-mr-sm" />
                                <router-link :to="`${$route.path}/${storeUser.optimus_id}`" class="store-name-link">
                                    {{ storeUser.email }}
                                </router-link>
                            </div>
                        </div>
                        <div class="store-card-actions q-mt-md">
                            <q-btn unelevated dense color="negative" icon="delete_forever" label="Delete"
                                @click="handleDeleteUser(storeUser)" class="action-btn-mobile action-btn-delete-mobile" />
                        </div>
                    </q-card-section>
                </q-card>
            </div>
            <!-- Mobile Pagination -->
            <div v-if="typedResult.length > 0" class="mobile-pagination q-mt-md">
                <q-pagination v-model="pagination.page" :max="pagination.lastPage" :max-pages="5" direction-links
                    boundary-links color="primary" @update:model-value="handlePageChange" />
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
import { StoreUser } from 'src/boot/interfaces';

const useCommon = useCommonStore();
const { pagination, result, entityQuery } = storeToRefs(useCommon);

const search = ref('');

entityQuery.value = {
    message: 'Getting users...',
    entity: 'store-users',
    query: {
        orderBy: 'created_at:desc',
        page: pagination.value.page,
        limit: 10,
        with: 'store'
    },
};

const typedResult = result as unknown as StoreUser[];

const columns = [
    {
        name: 'storeName',
        required: true,
        label: 'Store Name',
        align: 'left' as const,
        field: (row: StoreUser) => row.store?.name || 'N/A',
        sortable: true
    },
    {
        name: 'email',
        required: true,
        label: 'Email',
        align: 'left' as const,
        field: 'email',
        sortable: true
    },
    {
        name: 'verified',
        required: true,
        label: 'Verified',
        align: 'left' as const,
        field: 'verifed',
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

const handleDeleteUser = (user: StoreUser) => {
    onDeleteEntity('store-users', user.optimus_id, user.email);
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

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.header-title-section {
    display: flex;
    align-items: center;
}

.search-input {
    min-width: 300px;
}

.stores-table {
    width: 100%;
}

.store-name-link {
    text-decoration: none;
    color: inherit;
    font-weight: normal;
    font-size: 14px;

    &:hover {
        color: #1976d2;
    }
}

.action-buttons {
    display: flex;
    gap: 4px;
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

.empty-state-desktop {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 80px 24px;
}

.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 60px 24px;
}

.stores-cards {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.store-card {
    border-radius: 8px;
}

.store-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.store-card-title {
    display: flex;
    align-items: center;
}

.store-name-link {
    text-decoration: none;
    color: inherit;
    font-weight: normal;
    font-size: 14px;

    &:hover {
        color: #1976d2;
    }
}

.store-card-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.action-btn-mobile {
    flex: 1;
    min-width: 80px;
}

.mobile-pagination {
    display: flex;
    justify-content: center;
}

@media (max-width: 768px) {
    .header-content {
        flex-direction: column;
        align-items: stretch;
        gap: 16px;
    }

    .search-input {
        min-width: 100%;
    }
}
</style>
