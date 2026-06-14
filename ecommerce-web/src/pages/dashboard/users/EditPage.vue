<template>
  <div class="stores-page-container user-edit-page">
    <!-- Header Section -->
    <div class="page-header q-mb-md">
      <div class="header-content">
        <div class="header-title-section">
          <q-icon name="badge" :size="$q.screen.lt.sm ? '28px' : '32px'" color="primary"
            class="q-mr-sm flex-shrink-0" />
          <h2 class="page-title text-break">{{ userName }}</h2>
        </div>
      </div>
    </div>

    <!-- Assign Role Form -->
    <q-card flat bordered class="store-grid-item q-mb-lg">
      <div class="grid-row assign-form-row">
        <div class="assign-form-inner">
          <q-form @submit.prevent="onAssignRole" class="row q-col-gutter-md">
            <div class="col-12 col-md-8">
              <q-select v-model="selectedRoleId" :options="roles" option-value="id" option-label="label" emit-value
                map-options outlined dense label="Select role" :loading="rolesLoading" clearable class="search-input">
                <template v-slot:prepend>
                  <q-icon name="badge" />
                </template>
              </q-select>
            </div>
            <div class="col-12 col-md-4 flex" :class="$q.screen.lt.md ? 'items-stretch' : 'items-center'">
              <q-btn type="submit" unelevated color="primary" icon="add_circle" label="Assign role"
                :loading="assignLoading" class="action-btn-mobile full-width" />
            </div>
          </q-form>
        </div>
      </div>
    </q-card>

    <div class="text-subtitle1 text-weight-medium q-mb-sm" :class="{ 'text-h6': $q.screen.gt.sm }">
      Assigned roles
    </div>
    <template v-if="!userRoles.length">
      <div class="text-grey-7 q-mb-lg">No roles assigned yet.</div>
    </template>
    <template v-else>

      <div v-show="!showRolesTable" class="roles-cards">
        <q-card v-for="role in userRoles" :key="role.id" class="role-card q-mb-sm">
          <div class="role-accent-bar"></div>
          <q-card-section class="q-pa-md">
            <div class="row items-center justify-between">
              <div class="col">
                <div class="row items-center q-gutter-x-sm">
                  <q-avatar size="36px" color="primary" text-color="white" icon="badge" />
                  <div>
                    <div class="text-subtitle2 text-weight-medium text-dark">{{ role.label || role.name }}</div>
                    <div v-if="role.slug_name" class="text-caption text-grey-5">
                      {{ role.slug_name }}
                    </div>
                  </div>
                </div>
                <div v-if="role.description" class="text-body2 text-grey-6 q-mt-sm">
                  {{ role.description }}
                </div>
              </div>
              <div class="col-auto">
                <q-btn flat round size="sm" color="grey-6" icon="close" :loading="removingRoleId === role.id"
                  @click="confirmRemoveRole(role)" class="role-delete-btn">
                  <q-tooltip>Remove role</q-tooltip>
                </q-btn>
              </div>
            </div>
          </q-card-section>
        </q-card>
      </div>
    </template>

    <q-dialog v-model="showRemoveRoleDialog" persistent>
      <q-card class="remove-role-dialog">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-h6">Remove role</div>
        </q-card-section>
        <q-card-section class="q-pt-sm">
          <span v-if="roleToRemove">
            Remove "{{ roleToRemove.label || roleToRemove.name }}" from this user?
          </span>
        </q-card-section>
        <q-card-actions align="right">
          <q-btn flat label="Cancel" color="primary" @click="closeRemoveDialog" />
          <q-btn unelevated label="Remove" color="negative" @click="onRemoveDialogConfirm" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </div>
</template>
<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import { get, show } from 'src/boot/axios-call';
import { axios } from 'src/boot/axios';
import { useRoute } from 'vue-router'
import { Notify, Loading, useQuasar } from 'quasar';
import type { AxiosResponse } from 'axios';

const route = useRoute();
const $q = useQuasar();

/** Table from md and up; cards below (phones + small tablets). */
const showRolesTable = computed(() => $q.screen.gt.md);

interface RoleOption {
  id: number;
  optimus_id: number;
  label: string;
}

interface UserRoleRow {
  id: number;
  name: string;
  label: string;
  description?: string;
  slug_name?: string;
}

interface UserShowData {
  id?: number;
  name?: string;
  optimus_id?: number;
  roles?: UserRoleRow[];
}

const roles = ref<RoleOption[]>([]);
const userRoles = ref<UserRoleRow[]>([]);
const rolesLoading = ref(false);
const assignLoading = ref(false);
const removingRoleId = ref<number | null>(null);
const userName = ref('');
const userData = ref<UserShowData | null>(null);
const selectedRoleId = ref<number | null>(null);
const showRemoveRoleDialog = ref(false);
const roleToRemove = ref<UserRoleRow | null>(null);

const roleColumns = [
  {
    name: 'label',
    required: true,
    label: 'Role',
    field: (row: UserRoleRow) => row.label || row.name,
    align: 'left' as const,
    sortable: true,
  },
  {
    name: 'description',
    required: true,
    label: 'Description',
    field: 'description',
    align: 'left' as const,
    sortable: true,
  },
  {
    name: 'slug_name',
    required: true,
    label: 'Slug',
    field: 'slug_name',
    align: 'left' as const,
    sortable: true,
  },
  {
    name: 'actions',
    required: true,
    label: 'Actions',
    field: '',
    align: 'center' as const,
  },
];

async function loadRoles() {
  rolesLoading.value = true;
  try {
    const res = (await get(
      { entity: 'roles', query: { limit: 500 } },
      false
    )) as AxiosResponse<{ data: RoleOption[] }>;
    roles.value = res?.data?.data ?? [];
  } finally {
    rolesLoading.value = false;
  }
}

async function loadUser() {
  const loaded = await show<UserShowData>({
    entity: 'users',
    optimus_id: Number(route.params.id),
    query: {
      with: 'roles',
    },
  });
  userData.value = loaded ?? null;
  if (loaded?.name) userName.value = loaded.name;
  userRoles.value = loaded?.roles ?? [];
}

async function onAssignRole() {
  if (selectedRoleId.value == null || userData.value?.id == null) return;
  assignLoading.value = true;
  Loading.show({ message: 'Assigning role...' });
  try {
    await axios.post('role-user', {
      user_id: userData.value.id,
      role_id: selectedRoleId.value,
    });
    Loading.hide();
    Notify.create({
      position: 'bottom',
      type: 'positive',
      message: 'Role assigned successfully.',
    });
    selectedRoleId.value = null;
    await loadUser();
  } catch (e: unknown) {
    Loading.hide();
    const msg =
      (e as { response?: { data?: { message?: string } } })?.response?.data?.message ??
      'Failed to assign role.';
    Notify.create({ position: 'bottom', type: 'negative', message: msg });
  } finally {
    assignLoading.value = false;
  }
}

function confirmRemoveRole(role: UserRoleRow) {
  roleToRemove.value = role;
  showRemoveRoleDialog.value = true;
}

function closeRemoveDialog() {
  showRemoveRoleDialog.value = false;
  roleToRemove.value = null;
}

async function onRemoveDialogConfirm() {
  const role = roleToRemove.value;
  if (!role) return;
  showRemoveRoleDialog.value = false;
  roleToRemove.value = null;
  await removeRole(role);
}

async function removeRole(role: UserRoleRow) {
  if (userData.value?.id == null) return;
  removingRoleId.value = role.id;
  Loading.show({ message: 'Removing role...' });
  try {
    await axios.delete('role-user', {
      data: {
        user_id: userData.value.id,
        role_id: role.id,
      },
    });
    Loading.hide();
    Notify.create({
      position: 'bottom',
      type: 'positive',
      message: 'Role removed.',
    });
    await loadUser();
  } catch (e: unknown) {
    Loading.hide();
    const msg =
      (e as { response?: { data?: { message?: string } } })?.response?.data?.message ??
      'Failed to remove role.';
    Notify.create({ position: 'bottom', type: 'negative', message: msg });
  } finally {
    removingRoleId.value = null;
  }
}

onMounted(async () => {
  await loadUser();
  loadRoles();
});
</script>

<style scoped lang="scss">
@import 'src/css/dashboard/all-stores/index.scss';

.assign-form-row {
  grid-template-columns: 1fr;
  padding: 24px;
}

.assign-form-inner {
  width: 100%;
}

.assign-form-row .action-btn-mobile {
  min-height: 40px;
}

.user-edit-page {
  box-sizing: border-box;
  min-width: 0;
}

@media (max-width: 599px) {
  .user-edit-page.stores-page-container {
    padding: 16px;
  }

  .user-edit-page .page-header {
    padding: 16px;
  }

  .assign-form-row {
    padding: 16px;
  }
}

.roles-table-scroll {
  max-width: 100%;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}

.roles-table {
  width: 100%;
}

:deep(.roles-table) {
  min-width: 480px;
}

.min-width-0 {
  min-width: 0;
}

.roles-cards {
  min-width: 0;
}

.roles-cards .store-grid-item {
  min-width: 0;
}

@media (max-width: 1023px) {
  .user-edit-page .page-title {
    font-size: clamp(1.125rem, 4vw, 1.75rem);
  }
}

.remove-role-dialog {
  min-width: min(100vw - 32px, 360px);
}

.role-card {
  position: relative;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
  transition: all 0.2s ease;
  overflow: hidden;
}

.role-card:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
  transform: translateY(-1px);
}

.role-accent-bar {
  position: absolute;
  left: 0;
  top: 0;
  bottom: 0;
  width: 4px;
  background: linear-gradient(180deg, #1976d2 0%, #42a5f5 100%);
  border-radius: 12px 0 0 12px;
}

.role-delete-btn {
  opacity: 0.6;
  transition: all 0.2s ease;
}

.role-card:hover .role-delete-btn {
  opacity: 1;
}

.role-delete-btn:hover {
  background-color: #ffebee;
  color: #c62828 !important;
}
</style>