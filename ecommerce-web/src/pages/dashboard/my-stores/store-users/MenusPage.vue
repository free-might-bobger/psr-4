<template>
    <div class="store-user-menus-page-container">
        <div class="page-hero q-mb-xl">
            <div class="hero-accent-overlay"></div>
            <div class="hero-inner">
                <div class="hero-left">
                    <div class="hero-icon-wrap">
                        <q-icon name="menu_open" size="28px" color="white" />
                    </div>
                    <div>
                        <h1 class="page-title">Assign Store Menus</h1>
                        <div class="page-subtitle">Select a user and assign accessible menus</div>
                    </div>
                </div>
            </div>
        </div>

        <q-form @submit.prevent="onSubmit" class="form-card" ref="myForm">
            <div class="form-row">
                <div class="form-col">
                    <q-select dense outlined clearable use-chips v-model="selectedStoreUser" :options="typedResult"
                        option-value="optimus_id" option-label="email" label="Select Store User"
                        popup-content-class="dark-select-popup"
                        :rules="[(val) => validateRequired(val) || 'Store user is required.']" hide-bottom-space>
                        <template v-slot:option="scope">
                            <q-item v-bind="scope.itemProps">
                                <q-item-section>
                                    <q-item-label>{{ scope.opt.email || `User #${scope.opt.id}` }}</q-item-label>
                                    <q-item-label caption v-if="scope.opt.store?.name">
                                        {{ scope.opt.store.name }} — {{ scope.opt.store.mobile || 'No mobile' }}
                                    </q-item-label>
                                </q-item-section>
                            </q-item>
                        </template>
                    </q-select>
                </div>

                <div class="form-col">
                    <q-select dense outlined clearable use-chips v-model="selectedStoreMenu" :options="storeMenus"
                        option-value="id" option-label="label" label="Select Store Menu"
                        popup-content-class="dark-select-popup"
                        :rules="[(val) => validateRequired(val) || 'Store menu is required.']" hide-bottom-space>
                        <template v-slot:option="scope">
                            <q-item v-bind="scope.itemProps">
                                <q-item-section avatar>
                                    <q-icon :name="scope.opt.icon" />
                                </q-item-section>
                                <q-item-section>
                                    <q-item-label>{{ scope.opt.label }}</q-item-label>
                                </q-item-section>
                            </q-item>
                        </template>
                    </q-select>
                </div>
            </div>

            <div class="form-actions">
                <q-btn type="submit" label="Assign Menu" unelevated class="submit-btn" :loading="submitting" />
            </div>
        </q-form>

        <div class="users-section q-mt-lg">
            <div class="section-title">Store Users</div>
            <div v-if="typedResult.length === 0" class="empty-state">
                <q-icon name="group_off" size="48px" color="white" />
                <div class="empty-title">No users found</div>
            </div>
            <div v-else class="users-grid">
                <q-card v-for="user in typedResult" :key="user.optimus_id" flat class="user-card"
                    :class="{ selected: selectedStoreUser?.optimus_id === user.optimus_id }"
                    @click="selectedStoreUser = user">
                    <q-card-section class="user-card-section">
                        <div class="user-avatar">
                            <q-icon name="person" size="24px" color="white" />
                        </div>
                        <div class="user-info">
                            <div class="user-name">{{ user.email || `User #${user.id}` }}</div>
                            <div class="user-meta" v-if="user.store?.name">{{ user.store.name }}</div>
                        </div>
                        <q-icon v-if="selectedStoreUser?.optimus_id === user.optimus_id" name="check_circle"
                            class="selected-icon" size="24px" />
                    </q-card-section>
                </q-card>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { onMounted, ref, computed } from 'vue';
import { onRequest, get, create } from 'src/boot/axios-call';
import { storeToRefs } from 'pinia';
import { useCommonStore } from 'src/stores/common';
import { validateRequired } from 'src/boot/validators';
import type { QForm } from 'quasar';
import type { StoreUser } from 'src/boot/interfaces';

const useCommon = useCommonStore();
const { pagination, result, entityQuery } = storeToRefs(useCommon);

entityQuery.value = {
    message: 'Getting users...',
    entity: 'store-users',
    query: {
        orderBy: 'created_at:desc',
        page: pagination.value.page,
        limit: 10,
        with: 'store,user',
    },
};

const typedResult = computed<StoreUser[]>(() => {
    if (result.value && Array.isArray(result.value)) {
        return result.value as StoreUser[];
    }
    return [];
});

const storeMenus = ref<{ id: number; optimus_id: number; name: string; icon: string; label: string; value: number }[]>([]);
const selectedStoreUser = ref<StoreUser | null>(null);
const selectedStoreMenu = ref<{ id: number; optimus_id: number; name: string; icon: string; label: string; value: number } | null>(null);
const submitting = ref(false);
const myForm = ref<QForm | null>(null);

const getStoreMenus = async () => {
    const response = await get(
        {
            entity: 'listing_api',
            query: {
                listingApi: 'storeMenus',
            },
        },
        false
    );
    if (response && typeof response === 'object' && 'data' in response) {
        storeMenus.value = (response as { data: { data: { storeMenus: { id: number; optimus_id: number; name: string; icon: string; label: string; value: number }[] } } }).data.data.storeMenus;
    }
};

const onSubmit = async () => {
    const valid = await myForm.value?.validate();
    if (!valid) return;

    submitting.value = true;
    await create(
        {
            entity: 'store-user-menus',
            data: {
                store_user_id: selectedStoreUser.value?.optimus_id,
                store_menu_id: selectedStoreMenu.value?.optimus_id,
            },
        },
        true,
        'Assigning menu...',
        'Menu assigned successfully.'
    );
    submitting.value = false;
    selectedStoreUser.value = null;
    selectedStoreMenu.value = null;
    myForm.value?.resetValidation();
};

onMounted(() => {
    result.value = [];
    entityQuery.value.query.page = 1;
    onRequest(entityQuery.value, true);
    getStoreMenus();
});
</script>

<style scoped lang="scss">
$dark-base: #0f172a;
$dark-card: #1e293b;
$dark-elevated: #273549;
$border: rgba(255, 255, 255, 0.08);
$accent: #6366f1;
$accent-2: #7c3aed;
$green: #10b981;
$white: #ffffff;
$muted: rgba(255, 255, 255, 0.5);

.store-user-menus-page-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 28px 24px;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    color: $white;
}

.page-hero {
    position: relative;
    background: $dark-card;
    border-radius: 20px;
    border: 1px solid $border;
    box-shadow: 0 8px 40px rgba(0, 0, 0, 0.3);
    overflow: hidden;
    margin-bottom: 32px;
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

.form-card {
    background: $dark-card;
    border: 1px solid $border;
    border-radius: 20px;
    padding: 24px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    margin-top: 20px;
}

.submit-btn {
    background: linear-gradient(135deg, $accent 0%, $accent-2 100%) !important;
    color: $white !important;
    border-radius: 12px !important;
    font-weight: 700 !important;
    font-size: 14px !important;
    text-transform: none !important;
    height: 44px !important;
    padding: 0 24px !important;
    box-shadow: 0 4px 16px rgba(99, 102, 241, 0.4) !important;
}

.section-title {
    font-size: 18px;
    font-weight: 700;
    color: $white;
    margin-bottom: 16px;
}

:global(.dark-select-popup) {
    background: $dark-elevated !important;
    color: $white !important;
    border: 1px solid $border !important;
    border-radius: 12px !important;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4) !important;

    .q-item {
        color: $white !important;
        min-height: 40px;
        padding: 10px 16px;

        &:hover,
        &.q-manual-focusable--focused,
        &.q-item--active {
            background: rgba(255, 255, 255, 0.08);
            color: $white !important;
        }
    }

    .q-item__label {
        color: $white !important;
    }

    .q-item__label--caption {
        color: $muted !important;
    }

    .q-icon {
        color: $white !important;
    }
}

.form-card {
    .q-select {
        :deep(.q-field__label) {
            color: $muted !important;
        }

        :deep(.q-field__native) {
            color: $white !important;
        }

        :deep(.q-field__input) {
            color: $white !important;
        }

        :deep(.q-field__marginal) {
            color: $muted !important;
        }

        :deep(.q-field__messages) {
            color: #f87171 !important;
        }

        :deep(.text-negative) {
            color: #f87171 !important;
        }

        &.q-field--focused {
            :deep(.q-field__label) {
                color: $accent !important;
            }
        }
    }
}

.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 60px 24px;
    background: $dark-card;
    border: 1px solid $border;
    border-radius: 20px;
    gap: 16px;
}

.empty-title {
    font-size: 18px;
    color: $muted;
}

.users-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 16px;
}

.user-card {
    background: $dark-card !important;
    border: 1px solid $border;
    border-radius: 16px !important;
    cursor: pointer;
    transition: all 0.2s ease;

    &:hover {
        border-color: rgba($accent, 0.4);
        transform: translateY(-2px);
    }

    &.selected {
        border-color: $accent;
        box-shadow: 0 4px 16px rgba(99, 102, 241, 0.2);
    }
}

.user-card-section {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 16px;
}

.user-avatar {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: linear-gradient(135deg, $accent 0%, $accent-2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.user-info {
    flex: 1;
    min-width: 0;
}

.user-name {
    font-size: 14px;
    font-weight: 700;
    color: $white;
}

.user-meta {
    font-size: 12px;
    color: $muted;
    margin-top: 2px;
}

.selected-icon {
    color: $green;
    flex-shrink: 0;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>
