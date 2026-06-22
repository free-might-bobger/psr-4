<template>
    <div class="store-users-page-container">
        <!-- Hero Header -->
        <div class="page-hero q-mb-xl">
            <div class="hero-accent-overlay"></div>
            <div class="hero-inner">
                <div class="hero-left">
                    <div class="hero-icon-wrap">
                        <q-icon name="groups" size="28px" color="white" />
                    </div>
                    <div>
                        <h1 class="page-title">Store Users</h1>
                        <div class="page-subtitle">Manage user access and permissions</div>
                    </div>
                </div>
                <div class="hero-right">
                    <q-btn unelevated icon="add" label="Invite User" :to="`${$route.path}/invite-users`"
                        class="invite-btn">
                        <q-tooltip>Invite new user to store</q-tooltip>
                    </q-btn>
                </div>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="search-section q-mb-lg">
            <div class="search-card">
                <div class="search-input-wrap">
                    <q-icon name="search" size="20px" class="search-icon" />
                    <q-input v-model="search" placeholder="Search users by email..." outlined dense clearable
                        debounce="1000" class="search-field" hide-bottom-space>
                    </q-input>
                </div>
                <div class="search-stats">
                    <div class="stat-item">
                        <div class="stat-number">{{ typedResult.length }}</div>
                        <div class="stat-label">Total Users</div>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="stat-item">
                        <div class="stat-number">{{typedResult.filter(u => u.store).length}}</div>
                        <div class="stat-label">With Store</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Desktop Table View -->
        <div class="desktop-only">
            <!-- Empty State -->
            <div v-if="typedResult.length === 0" class="empty-state-section">
                <div class="empty-card">
                    <div class="empty-icon-wrap">
                        <q-icon name="group_off" size="64px" color="white" />
                    </div>
                    <div class="empty-title">No users found</div>
                    <div class="empty-subtitle">Start by inviting users to your store</div>
                    <q-btn unelevated icon="add" label="Invite First User" :to="`${$route.path}/invite-users`"
                        class="empty-action-btn" />
                </div>
            </div>

            <!-- Users Table -->
            <div v-else class="users-table-section">
                <div class="table-card">
                    <table class="users-table">
                        <!-- Header -->
                        <thead>
                            <tr class="table-header">
                                <th class="table-header-cell">User Information</th>
                                <th class="table-header-cell">Store Mobile</th>
                                <th class="table-header-cell">Status</th>
                                <th class="table-header-cell">Actions</th>
                            </tr>
                        </thead>

                        <!-- Body -->
                        <tbody>
                            <tr v-for="user in typedResult" :key="user.id" class="table-row">
                                <!-- User Info -->
                                <td class="table-cell">
                                    <div class="user-info-cell">
                                        <div class="user-avatar">
                                            <q-icon name="person" size="20px" color="white" />
                                        </div>
                                        <div class="user-details">
                                            {{ user.email || `User #${user.id}` }}
                                            <div class="user-email">{{ user.store?.name || 'No Store' }}</div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Store Mobile -->
                                <td class="table-cell">
                                    <div class="store-badge">
                                        <q-icon name="phone" size="14px" class="q-mr-xs" />
                                        {{ user.store?.mobile || 'No Mobile' }}
                                    </div>
                                </td>

                                <!-- Status -->
                                <td class="table-cell">
                                    <div class="verification-status">
                                        <q-icon v-if="user.is_verified" name="verified_user" size="18px"
                                            class="verified-icon" />
                                        <q-icon v-else name="pending" size="18px" class="pending-icon" />
                                        <span :class="user.is_verified ? 'verified-text' : 'pending-text'">
                                            {{ user.is_verified ? 'Verified' : 'Pending' }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Actions -->
                                <td class="table-cell">
                                    <div class="actions-cell">
                                        <q-btn v-if="user.is_verified" unelevated dense icon="manage_accounts"
                                            :to="`${$route.path}/menus`" class="action-btn access-btn">
                                            <q-tooltip>Manage access permissions</q-tooltip>
                                        </q-btn>
                                        <q-btn unelevated dense icon="delete_forever" @click="handleDeleteUser(user)"
                                            class="action-btn delete-btn">
                                            <q-tooltip>Remove user from store</q-tooltip>
                                        </q-btn>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="pagination-section">
                    <div class="pagination-card">
                        <div class="pagination-info">
                            <span class="pagination-text">
                                Showing {{ pagination.from || 1 }}-{{ pagination.to || typedResult.length }}
                                of {{ pagination.rowsNumber || typedResult.length }} users
                            </span>
                        </div>
                        <div class="pagination-controls">
                            <q-btn v-if="pagination.lastPage > 2" flat round dense icon="first_page"
                                :disable="pagination.page === 1" @click="goToFirstPage" class="pagination-btn">
                                <q-tooltip>First page</q-tooltip>
                            </q-btn>
                            <q-btn flat round dense icon="chevron_left" :disable="pagination.page === 1"
                                @click="goToPreviousPage" class="pagination-btn">
                                <q-tooltip>Previous page</q-tooltip>
                            </q-btn>
                            <div class="page-indicator">
                                <span class="current-page">{{ pagination.page }}</span>
                                <span class="page-separator">/</span>
                                <span class="total-pages">{{ pagination.lastPage }}</span>
                            </div>
                            <q-btn flat round dense icon="chevron_right"
                                :disable="pagination.page === pagination.lastPage" @click="goToNextPage"
                                class="pagination-btn">
                                <q-tooltip>Next page</q-tooltip>
                            </q-btn>
                            <q-btn v-if="pagination.lastPage > 2" flat round dense icon="last_page"
                                :disable="pagination.page === pagination.lastPage" @click="goToLastPage"
                                class="pagination-btn">
                                <q-tooltip>Last page</q-tooltip>
                            </q-btn>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Card View -->
        <div class="mobile-only">
            <!-- Empty State -->
            <div v-if="typedResult.length === 0" class="empty-state-mobile">
                <div class="empty-icon-mobile">
                    <q-icon name="group_off" size="48px" color="white" />
                </div>
                <div class="empty-title-mobile">No users found</div>
                <div class="empty-subtitle-mobile">Invite users to get started</div>
                <q-btn unelevated icon="add" label="Invite User" :to="`${$route.path}/invite-users`"
                    class="empty-btn-mobile" />
            </div>

            <!-- User Cards -->
            <div v-else class="users-cards-mobile">
                <q-card v-for="storeUser in typedResult" :key="storeUser.id" flat class="user-card-mobile q-mb-md">
                    <q-card-section class="user-card-header">
                        <div class="user-avatar-mobile">
                            <q-icon name="person" size="24px" color="white" />
                        </div>
                        <div class="user-info-mobile">
                            <router-link :to="`${$route.path}/${storeUser.optimus_id}`" class="user-name-mobile">
                                {{ storeUser.email || `User #${storeUser.id}` }}
                            </router-link>
                            <div class="user-email-mobile">{{ storeUser.store?.name || 'No Store' }}</div>
                        </div>
                        <div class="verification-badge-mobile">
                            <q-icon v-if="storeUser.is_verified" name="verified_user" size="16px"
                                class="verified-icon-mobile" />
                            <q-icon v-else name="pending" size="16px" class="pending-icon-mobile" />
                        </div>
                    </q-card-section>

                    <q-separator class="user-card-divider" />

                    <q-card-section class="user-card-body">
                        <div class="user-meta-mobile">
                            <div class="meta-item-mobile">
                                <q-icon name="store" size="14px" class="meta-icon" />
                                <span>{{ storeUser.store?.name || 'No Store' }}</span>
                            </div>
                            <div class="meta-item-mobile">
                                <q-icon :name="storeUser.is_verified ? 'verified_user' : 'pending'" size="14px"
                                    :class="storeUser.is_verified ? 'verified-icon' : 'pending-icon'" />
                                <span>{{ storeUser.is_verified ? 'Verified' : 'Pending' }}</span>
                            </div>
                        </div>
                    </q-card-section>

                    <q-card-actions class="user-card-actions">
                        <q-btn v-if="storeUser.is_verified" unelevated icon="manage_accounts" label="Access"
                            :to="`${$route.path}/access`" class="mobile-action-btn access-btn-mobile" />
                        <q-btn unelevated icon="delete_forever" label="Remove" @click="handleDeleteUser(storeUser)"
                            class="mobile-action-btn delete-btn-mobile" />
                    </q-card-actions>
                </q-card>
            </div>

            <!-- Mobile Pagination -->
            <div v-if="typedResult.length > 0" class="mobile-pagination">
                <q-pagination v-model="pagination.page" :max="pagination.lastPage" :max-pages="5" direction-links
                    boundary-links color="primary" @update:model-value="handlePageChange" />
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { onMounted, ref, watch, computed } from 'vue';
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
        with: 'store,user'
    },
};

const typedResult = computed<StoreUser[]>(() => {
    // Based on the store structure, result.value should directly be the array
    if (result.value && Array.isArray(result.value)) {
        return result.value as StoreUser[];
    }
    return [];
});

const columns = [
    {
        name: 'userInfo',
        required: true,
        label: 'User Information',
        align: 'left' as const,
        field: 'email',
        sortable: true
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
        // Search by email since that's now available
        entityQuery.value.query.filters = 'email:' + search.value;
    } else {
        delete entityQuery.value.query.filters;
    }
    entityQuery.value.query.page = 1;
    onRequest(entityQuery.value);
});
</script>

<style scoped lang="scss">
// ── Dark theme tokens (matching DashboardLayout) ─────────────────────────────────
$dark-base: #0f172a;
$dark-card: #1e293b;
$dark-elevated: #273549;
$border: rgba(255, 255, 255, 0.08);
$accent: #6366f1;
$accent-2: #7c3aed;
$green: #10b981;
$green-2: #059669;
$yellow: #fbbf24;
$yellow-2: #f59e0b;
$red: #ef4444;
$red-2: #dc2626;
$white: #ffffff;
$muted: rgba(255, 255, 255, 0.5);
$muted-2: rgba(255, 255, 255, 0.3);

// ── Container ────────────────────────────────────────────────────────────────
.store-users-page-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 28px 24px;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    color: $white;
    position: relative;
}

// ── Hero Header ───────────────────────────────────────────────────────────────
.page-hero {
    position: relative;
    background: $dark-card;
    border-radius: 20px;
    border: 1px solid $border;
    box-shadow: 0 8px 40px rgba(0, 0, 0, 0.3);
    overflow: hidden;
}

.hero-accent-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.18) 0%, rgba(124, 58, 237, 0.10) 60%, transparent 100%);
    pointer-events: none;
}

.hero-inner {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 32px 36px;
}

.hero-left {
    display: flex;
    align-items: center;
    gap: 20px;
}

.hero-icon-wrap {
    width: 64px;
    height: 64px;
    border-radius: 16px;
    background: linear-gradient(135deg, $accent 0%, $accent-2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 24px rgba(99, 102, 241, 0.4);
    flex-shrink: 0;
}

.page-title {
    font-size: 28px;
    font-weight: 800;
    color: $white;
    margin: 0 0 4px;
    letter-spacing: -0.3px;
    line-height: 1.2;
}

.page-subtitle {
    font-size: 14px;
    color: $muted;
    font-weight: 500;
}

.invite-btn {
    background: linear-gradient(135deg, $accent 0%, $accent-2 100%) !important;
    color: $white !important;
    border-radius: 12px !important;
    font-weight: 700 !important;
    font-size: 14px !important;
    text-transform: none !important;
    letter-spacing: 0 !important;
    height: 44px !important;
    padding: 0 20px !important;
    box-shadow: 0 4px 16px rgba(99, 102, 241, 0.4) !important;

    &:hover {
        box-shadow: 0 6px 22px rgba(99, 102, 241, 0.55) !important;
        transform: translateY(-1px);
    }
}

// ── Search Section ───────────────────────────────────────────────────────────
.search-section {
    margin-bottom: 32px;
}

.search-card {
    background: $dark-card;
    border: 1px solid $border;
    border-radius: 20px;
    padding: 24px;
    display: flex;
    align-items: center;
    gap: 24px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
}

.search-input-wrap {
    flex: 1;
    display: flex;
    align-items: center;
    background: $dark-elevated;
    border: 1px solid $border;
    border-radius: 14px;
    padding: 4px 16px;
    transition: all 0.2s ease;

    &:focus-within {
        border-color: rgba($accent, 0.4);
        box-shadow: 0 0 0 3px rgba($accent, 0.1);
    }
}

.search-icon {
    color: $muted;
    margin-right: 12px;
}

.search-field {
    flex: 1;

    :deep(.q-field__control) {
        background: transparent !important;
        border: none !important;
        color: $white !important;
    }

    :deep(.q-field__native) {
        color: $white !important;
        font-size: 14px;
        padding: 8px 0;
    }

    :deep(.q-field__native::placeholder) {
        color: $muted !important;
    }
}

.search-stats {
    display: flex;
    align-items: center;
    gap: 16px;
}

.stat-item {
    text-align: center;
}

.stat-number {
    font-size: 24px;
    font-weight: 800;
    color: $white;
    line-height: 1;
}

.stat-label {
    font-size: 11px;
    color: $muted;
    font-weight: 600;
    text-transform: uppercase;
    margin-top: 4px;
}

.stat-divider {
    width: 1px;
    height: 32px;
    background: $border;
}

// ── Table Section ────────────────────────────────────────────────────────────
.users-table-section {
    background: $dark-card;
    border: 1px solid $border;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 8px 40px rgba(0, 0, 0, 0.25);
}

.table-card {
    border-radius: 20px;
    overflow: hidden;
}

.users-table {
    width: 100%;
    background: transparent;
    border-collapse: collapse;

    thead {
        background: $dark-elevated;
    }

    th {
        font-size: 12px;
        font-weight: 700;
        color: $muted;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 16px 20px;
        text-align: left;
        border: none;
    }

    tbody tr {
        transition: background-color 0.2s ease;
        border-bottom: 1px solid $border;

        &:hover {
            background: rgba($accent, 0.04);
        }
    }

    td {
        padding: 20px;
        border: none;
        vertical-align: middle;
    }
}

.table-header {
    background: $dark-elevated;
}

.table-header-cell {
    font-size: 12px;
    font-weight: 700;
    color: $muted;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 16px 20px !important;
    border: none !important;
}

.table-row {
    transition: background-color 0.2s ease;

    &:hover {
        background: rgba($accent, 0.04);
    }
}

.table-cell {
    padding: 20px !important;
    border: none !important;
    vertical-align: middle;
}

// ── User Info Cell ────────────────────────────────────────────────────────────
.user-info-cell {
    display: flex;
    align-items: center;
    gap: 14px;
}

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    background: linear-gradient(135deg, $accent 0%, $accent-2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
    flex-shrink: 0;
}

.user-details {
    flex: 1;
}

.user-name {
    font-size: 14px;
    font-weight: 700;
    color: $accent;
    text-decoration: none;
    transition: color 0.2s ease;
    display: block;
    line-height: 1.3;

    &:hover {
        color: $accent-2;
    }
}

.user-email {
    font-size: 12px;
    color: $muted;
    margin-top: 2px;
}

// ── Store Badge ───────────────────────────────────────────────────────────────
.store-badge {
    display: inline-flex;
    align-items: center;
    background: $dark-elevated;
    border: 1px solid $border;
    border-radius: 8px;
    padding: 6px 12px;
    font-size: 12px;
    color: $white;
    font-weight: 600;
}

// ── Verification Status ───────────────────────────────────────────────────────
.verification-status {
    display: flex;
    align-items: center;
    gap: 8px;
}

.verified-icon {
    color: $green;
}

.pending-icon {
    color: $yellow;
}

.verified-text {
    font-size: 13px;
    font-weight: 600;
    color: $green;
}

.pending-text {
    font-size: 13px;
    font-weight: 600;
    color: $yellow;
}

// ── Actions Cell ──────────────────────────────────────────────────────────────
.actions-cell {
    display: flex;
    align-items: center;
    gap: 8px;
}

.action-btn {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    font-size: 16px;

    &.access-btn {
        background: rgba($accent, 0.15) !important;
        color: $accent !important;
        border: 1px solid rgba($accent, 0.2) !important;

        &:hover {
            background: rgba($accent, 0.25) !important;
            border-color: rgba($accent, 0.4) !important;
            transform: translateY(-1px);
        }
    }

    &.delete-btn {
        background: rgba($red, 0.15) !important;
        color: $red !important;
        border: 1px solid rgba($red, 0.2) !important;

        &:hover {
            background: rgba($red, 0.25) !important;
            border-color: rgba($red, 0.4) !important;
            transform: translateY(-1px);
        }
    }
}

// ── Pagination ───────────────────────────────────────────────────────────────
.pagination-section {
    padding: 20px 24px;
    background: $dark-elevated;
    border-top: 1px solid $border;
}

.pagination-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.pagination-info {
    flex: 1;
}

.pagination-text {
    font-size: 13px;
    color: $muted;
    font-weight: 500;
}

.pagination-controls {
    display: flex;
    align-items: center;
    gap: 8px;
}

.pagination-btn {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.06) !important;
    color: $muted !important;
    border: 1px solid $border !important;
    transition: all 0.2s ease;

    &:hover:not(:disabled) {
        background: rgba(255, 255, 255, 0.12) !important;
        color: $white !important;
        border-color: rgba(255, 255, 255, 0.14) !important;
    }

    &:disabled {
        opacity: 0.4;
    }
}

.page-indicator {
    display: flex;
    align-items: center;
    gap: 4px;
    margin: 0 12px;
    font-size: 13px;
    font-weight: 600;
}

.current-page {
    color: $white;
}

.page-separator {
    color: $muted;
}

.total-pages {
    color: $muted;
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

// ── Empty States ──────────────────────────────────────────────────────────────
.empty-state-section {
    display: flex;
    justify-content: center;
    padding: 80px 24px;
}

.empty-card {
    background: $dark-card;
    border: 1px solid $border;
    border-radius: 24px;
    padding: 60px 48px;
    text-align: center;
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.25);
    max-width: 480px;
}

.empty-icon-wrap {
    width: 120px;
    height: 120px;
    border-radius: 24px;
    background: linear-gradient(135deg, rgba($red, 0.2) 0%, rgba($red-2, 0.1) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 24px;
    box-shadow: 0 8px 32px rgba($red, 0.2);
}

.empty-title {
    font-size: 24px;
    font-weight: 800;
    color: $white;
    margin-bottom: 8px;
}

.empty-subtitle {
    font-size: 14px;
    color: $muted;
    margin-bottom: 32px;
    line-height: 1.5;
}

.empty-action-btn {
    background: linear-gradient(135deg, $accent 0%, $accent-2 100%) !important;
    color: $white !important;
    border-radius: 12px !important;
    font-weight: 700 !important;
    font-size: 14px !important;
    height: 44px !important;
    padding: 0 24px !important;
    box-shadow: 0 4px 16px rgba(99, 102, 241, 0.4) !important;

    &:hover {
        box-shadow: 0 6px 22px rgba(99, 102, 241, 0.55) !important;
        transform: translateY(-1px);
    }
}

// ── Mobile Styles ─────────────────────────────────────────────────────────────
.empty-state-mobile {
    text-align: center;
    padding: 60px 24px;
}

.empty-icon-mobile {
    width: 80px;
    height: 80px;
    border-radius: 20px;
    background: linear-gradient(135deg, rgba($red, 0.2) 0%, rgba($red-2, 0.1) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    box-shadow: 0 6px 24px rgba($red, 0.2);
}

.empty-title-mobile {
    font-size: 20px;
    font-weight: 800;
    color: $white;
    margin-bottom: 6px;
}

.empty-subtitle-mobile {
    font-size: 13px;
    color: $muted;
    margin-bottom: 24px;
}

.empty-btn-mobile {
    background: linear-gradient(135deg, $accent 0%, $accent-2 100%) !important;
    color: $white !important;
    border-radius: 12px !important;
    font-weight: 700 !important;
    font-size: 14px !important;
    height: 44px !important;
    padding: 0 20px !important;
    box-shadow: 0 4px 16px rgba(99, 102, 241, 0.4) !important;
}

// ── Mobile User Cards ─────────────────────────────────────────────────────────
.users-cards-mobile {
    padding: 0 4px;
}

.user-card-mobile {
    background: $dark-card !important;
    border: 1px solid $border !important;
    border-radius: 16px !important;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2) !important;
    overflow: hidden;
}

.user-card-header {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 20px 20px 16px;
}

.user-avatar-mobile {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    background: linear-gradient(135deg, $accent 0%, $accent-2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
    flex-shrink: 0;
}

.user-info-mobile {
    flex: 1;
}

.user-name-mobile {
    font-size: 16px;
    font-weight: 700;
    color: $accent;
    text-decoration: none;
    display: block;
    line-height: 1.3;

    &:hover {
        color: $accent-2;
    }
}

.user-email-mobile {
    font-size: 13px;
    color: $muted;
    margin-top: 4px;
}

.verification-badge-mobile {
    flex-shrink: 0;
}

.verified-icon-mobile {
    color: $green;
}

.pending-icon-mobile {
    color: $yellow;
}

.user-card-divider {
    background: $border !important;
    margin: 0;
}

.user-card-body {
    padding: 0 20px 16px;
}

.user-meta-mobile {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.meta-item-mobile {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: $white;
}

.meta-icon {
    color: $muted;
}

.user-card-actions {
    padding: 16px 20px;
    gap: 8px;
    display: flex;
}

.mobile-action-btn {
    flex: 1;
    height: 40px !important;
    border-radius: 10px !important;
    font-weight: 600 !important;
    font-size: 13px !important;

    &.access-btn-mobile {
        background: rgba($accent, 0.15) !important;
        color: $accent !important;
        border: 1px solid rgba($accent, 0.2) !important;

        &:hover {
            background: rgba($accent, 0.25) !important;
            border-color: rgba($accent, 0.4) !important;
        }
    }

    &.delete-btn-mobile {
        background: rgba($red, 0.15) !important;
        color: $red !important;
        border: 1px solid rgba($red, 0.2) !important;

        &:hover {
            background: rgba($red, 0.25) !important;
            border-color: rgba($red, 0.4) !important;
        }
    }
}

.mobile-pagination {
    display: flex;
    justify-content: center;
    padding: 24px 0;
}

// ── Responsive Design ─────────────────────────────────────────────────────────
@media (max-width: 768px) {
    .store-users-page-container {
        padding: 16px 12px;
    }

    .hero-inner {
        flex-direction: column;
        gap: 20px;
        padding: 24px 20px;
        text-align: center;
    }

    .hero-left {
        flex-direction: column;
        gap: 16px;
    }

    .page-title {
        font-size: 24px;
    }

    .search-card {
        flex-direction: column;
        gap: 20px;
        padding: 20px;
    }

    .search-stats {
        justify-content: center;
        gap: 24px;
    }

    .table-card {
        border-radius: 16px;
    }

    .user-card-mobile {
        margin: 0 0 16px 0;
    }
}
</style>
