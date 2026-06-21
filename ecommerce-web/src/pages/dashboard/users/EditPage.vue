<template>
  <div class="edit-page-container">

    <!-- Hero Header -->
    <div class="detail-hero q-mb-xl">
      <div class="hero-accent"></div>
      <div class="hero-body">
        <div class="hero-left">
          <div class="hero-icon-wrap">
            <q-icon name="manage_accounts" size="26px" color="white" />
          </div>
          <div>
            <h2 class="hero-title">{{ userName || 'User' }}</h2>
            <div class="hero-sub">Manage roles for this user</div>
          </div>
        </div>
        <q-btn flat class="back-btn" icon="arrow_back" label="Back" @click="$router.back()" />
      </div>
    </div>

    <!-- Assign Role Card -->
    <q-card flat class="section-card q-mb-xl">
      <q-card-section class="section-card-header">
        <div class="section-header-row">
          <div class="section-icon-wrap">
            <q-icon name="add_moderator" size="18px" color="white" />
          </div>
          <div>
            <div class="section-title">Assign Role</div>
            <div class="section-sub">Select and assign a role to this user</div>
          </div>
        </div>
      </q-card-section>
      <div class="card-divider"></div>
      <q-card-section class="section-card-body">
        <q-form @submit.prevent="onAssignRole" class="row q-col-gutter-md">
          <div class="col-12 col-md-8">
            <q-select v-model="selectedRoleId" :options="roles" option-value="id" option-label="label" emit-value
              map-options outlined dense label="Select role" :loading="rolesLoading" clearable class="dark-select">
              <template v-slot:prepend>
                <q-icon name="badge" color="grey-5" />
              </template>
            </q-select>
          </div>
          <div class="col-12 col-md-4 flex" :class="$q.screen.lt.md ? 'items-stretch' : 'items-center'">
            <q-btn type="submit" unelevated icon="add_circle" label="Assign Role" :loading="assignLoading"
              class="assign-btn full-width" />
          </div>
        </q-form>
      </q-card-section>
    </q-card>

    <!-- Assigned Roles -->
    <q-card flat class="section-card">
      <q-card-section class="section-card-header">
        <div class="section-header-row">
          <div class="section-icon-wrap">
            <q-icon name="verified_user" size="18px" color="white" />
          </div>
          <div>
            <div class="section-title">
              Assigned Roles
              <span class="roles-count-badge" v-if="userRoles.length">{{ userRoles.length }}</span>
            </div>
            <div class="section-sub">Current roles assigned to this user</div>
          </div>
        </div>
      </q-card-section>
      <div class="card-divider"></div>
      <q-card-section class="section-card-body">
        <div v-if="!userRoles.length" class="no-roles-state">
          <div class="no-roles-icon">
            <q-icon name="person_off" size="36px" color="white" />
          </div>
          <div class="no-roles-title">No roles assigned yet</div>
          <div class="no-roles-sub">Use the form above to assign a role</div>
        </div>
        <div v-else class="roles-list">
          <div v-for="role in userRoles" :key="role.id" class="role-item">
            <div class="role-item-left">
              <div class="role-item-icon">
                <q-icon name="badge" size="18px" color="white" />
              </div>
              <div class="role-item-info">
                <div class="role-item-name">{{ role.label || role.name }}</div>
                <div v-if="role.slug_name" class="role-item-slug">{{ role.slug_name }}</div>
                <div v-if="role.description" class="role-item-desc">{{ role.description }}</div>
              </div>
            </div>
            <q-btn flat round dense icon="close" class="role-remove-btn" :loading="removingRoleId === role.id"
              @click="confirmRemoveRole(role)">
              <q-tooltip>Remove role</q-tooltip>
            </q-btn>
          </div>
        </div>
      </q-card-section>
    </q-card>

    <!-- Remove Role Dialog -->
    <q-dialog v-model="showRemoveRoleDialog" persistent>
      <q-card class="remove-role-dialog">
        <q-card-section class="dialog-header">
          <div class="dialog-icon-wrap">
            <q-icon name="warning_amber" size="22px" color="white" />
          </div>
          <div class="dialog-title">Remove Role</div>
        </q-card-section>
        <q-card-section class="dialog-body">
          <span v-if="roleToRemove">
            Remove <strong class="dialog-role-name">{{ roleToRemove.label || roleToRemove.name }}</strong> from this
            user?
          </span>
        </q-card-section>
        <q-card-actions class="dialog-actions" align="right">
          <q-btn flat label="Cancel" class="dialog-cancel-btn" @click="closeRemoveDialog" />
          <q-btn unelevated label="Remove" class="dialog-remove-btn" @click="onRemoveDialogConfirm" />
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
// ── Dark theme tokens ──────────────────────────────────────────────────────
$dark-base: #0f172a;
$dark-card: #1e293b;
$dark-elevated: #273549;
$border: rgba(255, 255, 255, 0.08);
$accent: #6366f1;
$accent-2: #7c3aed;
$white: #ffffff;
$muted: rgba(255, 255, 255, 0.5);

// ── Container ──────────────────────────────────────────────────────────────
.edit-page-container {
  padding: 28px 24px;
  max-width: 900px;
  margin: 0 auto;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  color: $white;
}

// ── Hero header ────────────────────────────────────────────────────────────
.detail-hero {
  position: relative;
  background: $dark-card;
  border-radius: 20px;
  border: 1px solid $border;
  box-shadow: 0 8px 40px rgba(0, 0, 0, 0.3);
  overflow: hidden;
}

.hero-accent {
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, rgba(99, 102, 241, 0.18) 0%, rgba(124, 58, 237, 0.10) 60%, transparent 100%);
  pointer-events: none;
}

.hero-body {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 28px 32px;
  gap: 16px;
}

.hero-left {
  display: flex;
  align-items: center;
  gap: 18px;
}

.hero-icon-wrap {
  width: 52px;
  height: 52px;
  border-radius: 14px;
  background: linear-gradient(135deg, $accent 0%, $accent-2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 16px rgba(99, 102, 241, 0.4);
  flex-shrink: 0;
}

.hero-title {
  font-size: 22px;
  font-weight: 800;
  color: $white;
  margin: 0 0 4px;
  letter-spacing: -0.3px;
  line-height: 1.2;
  word-break: break-word;
}

.hero-sub {
  font-size: 13px;
  color: $muted;
  font-weight: 500;
}

.back-btn {
  color: $white !important;
  border: 1px solid $border !important;
  border-radius: 12px !important;
  text-transform: none !important;
  font-weight: 600 !important;
  letter-spacing: 0 !important;
  padding: 6px 18px !important;
  flex-shrink: 0;

  &:hover {
    background: rgba(255, 255, 255, 0.07) !important;
  }
}

// ── Section cards ──────────────────────────────────────────────────────────
.section-card {
  background: $dark-card !important;
  border: 1px solid $border !important;
  border-radius: 20px !important;
  box-shadow: 0 8px 40px rgba(0, 0, 0, 0.25) !important;
  overflow: hidden;
}

.section-card-header {
  padding: 20px 24px !important;
  background: $dark-elevated !important;
}

.section-header-row {
  display: flex;
  align-items: center;
  gap: 14px;
}

.section-icon-wrap {
  width: 40px;
  height: 40px;
  border-radius: 11px;
  background: linear-gradient(135deg, $accent 0%, $accent-2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 3px 12px rgba(99, 102, 241, 0.35);
  flex-shrink: 0;
}

.section-title {
  font-size: 16px;
  font-weight: 800;
  color: $white;
  display: flex;
  align-items: center;
  gap: 8px;
  line-height: 1.2;
}

.section-sub {
  font-size: 12px;
  color: $muted;
  margin-top: 2px;
}

.roles-count-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, $accent 0%, $accent-2 100%);
  color: $white;
  font-size: 11px;
  font-weight: 800;
  min-width: 22px;
  height: 22px;
  border-radius: 11px;
  padding: 0 6px;
}

.card-divider {
  height: 1px;
  background: $border;
}

.section-card-body {
  padding: 24px !important;
}

// ── Dark select/input ──────────────────────────────────────────────────────
.dark-select {
  :deep(.q-field__control) {
    background: $dark-elevated !important;
    border-radius: 12px !important;
  }

  :deep(.q-field__native),
  :deep(.q-field__input) {
    color: $white !important;
  }

  :deep(.q-field__label) {
    color: $muted !important;
  }

  :deep(.q-field__prepend .q-icon),
  :deep(.q-field__append .q-icon) {
    color: $muted !important;
  }

  :deep(.q-field--outlined .q-field__control:before) {
    border-color: $border !important;
  }

  :deep(.q-field--outlined:hover .q-field__control:before) {
    border-color: rgba(99, 102, 241, 0.4) !important;
  }

  :deep(.q-field--focused .q-field__control:before) {
    border-color: $accent !important;
  }
}

// ── Assign button ──────────────────────────────────────────────────────────
.assign-btn {
  background: linear-gradient(135deg, $accent 0%, $accent-2 100%) !important;
  color: $white !important;
  border-radius: 12px !important;
  font-weight: 700 !important;
  font-size: 14px !important;
  text-transform: none !important;
  letter-spacing: 0 !important;
  height: 42px !important;
  box-shadow: 0 4px 14px rgba(99, 102, 241, 0.35) !important;
  transition: opacity 0.2s, box-shadow 0.2s !important;

  &:hover {
    opacity: 0.92;
    box-shadow: 0 6px 20px rgba(99, 102, 241, 0.5) !important;
  }
}

// ── No roles empty state ───────────────────────────────────────────────────
.no-roles-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 48px 20px;
  text-align: center;
}

.no-roles-icon {
  width: 72px;
  height: 72px;
  border-radius: 20px;
  background: linear-gradient(135deg, $accent 0%, $accent-2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 6px 20px rgba(99, 102, 241, 0.35);
  margin-bottom: 16px;
}

.no-roles-title {
  font-size: 16px;
  font-weight: 700;
  color: $white;
  margin-bottom: 6px;
}

.no-roles-sub {
  font-size: 13px;
  color: $muted;
}

// ── Roles list ─────────────────────────────────────────────────────────────
.roles-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.role-item {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 16px;
  background: $dark-elevated;
  border: 1px solid $border;
  border-radius: 14px;
  padding: 16px 18px;
  position: relative;
  overflow: hidden;
  transition: border-color 0.2s;

  &::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 3px;
    height: 100%;
    background: linear-gradient(180deg, $accent 0%, $accent-2 100%);
  }

  &:hover {
    border-color: rgba(99, 102, 241, 0.3);
  }
}

.role-item-left {
  display: flex;
  align-items: flex-start;
  gap: 14px;
  flex: 1;
  min-width: 0;
}

.role-item-icon {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  background: linear-gradient(135deg, $accent 0%, $accent-2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  box-shadow: 0 3px 10px rgba(99, 102, 241, 0.3);
}

.role-item-info {
  flex: 1;
  min-width: 0;
}

.role-item-name {
  font-size: 15px;
  font-weight: 700;
  color: $white;
  line-height: 1.3;
}

.role-item-slug {
  display: inline-flex;
  align-items: center;
  margin-top: 4px;
  background: rgba(99, 102, 241, 0.12);
  border: 1px solid rgba(99, 102, 241, 0.25);
  color: #a5b4fc;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.5px;
  padding: 2px 8px;
  border-radius: 20px;
}

.role-item-desc {
  font-size: 13px;
  color: $muted;
  margin-top: 6px;
  line-height: 1.5;
}

.role-remove-btn {
  color: $muted !important;
  flex-shrink: 0;
  transition: color 0.2s, background 0.2s !important;

  &:hover {
    color: #f87171 !important;
    background: rgba(239, 68, 68, 0.12) !important;
  }
}

// ── Remove dialog ──────────────────────────────────────────────────────────
.remove-role-dialog {
  min-width: min(100vw - 32px, 380px);
  background: $dark-card !important;
  border: 1px solid $border !important;
  border-radius: 20px !important;
  box-shadow: 0 24px 64px rgba(0, 0, 0, 0.5) !important;
}

.dialog-header {
  display: flex !important;
  align-items: center;
  gap: 14px;
  padding: 20px 24px 16px !important;
  background: $dark-elevated !important;
}

.dialog-icon-wrap {
  width: 40px;
  height: 40px;
  border-radius: 11px;
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.dialog-title {
  font-size: 17px;
  font-weight: 800;
  color: $white;
}

.dialog-body {
  padding: 20px 24px !important;
  font-size: 14px;
  color: $muted;
  line-height: 1.6;
}

.dialog-role-name {
  color: $white;
  font-weight: 700;
}

.dialog-actions {
  padding: 12px 20px 20px !important;
  background: $dark-card !important;
  gap: 8px;
}

.dialog-cancel-btn {
  color: $muted !important;
  border-radius: 10px !important;
  text-transform: none !important;
  font-weight: 600 !important;

  &:hover {
    background: rgba(255, 255, 255, 0.06) !important;
    color: $white !important;
  }
}

.dialog-remove-btn {
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
  color: $white !important;
  border-radius: 10px !important;
  text-transform: none !important;
  font-weight: 700 !important;
  padding: 6px 20px !important;
  box-shadow: 0 4px 14px rgba(239, 68, 68, 0.35) !important;
}

// ── Responsive ─────────────────────────────────────────────────────────────
@media (max-width: 768px) {
  .edit-page-container {
    padding: 16px 12px;
  }

  .hero-body {
    padding: 20px;
    flex-direction: column;
    align-items: flex-start;
  }

  .section-card-body {
    padding: 16px !important;
  }

  .section-card-header {
    padding: 16px 18px !important;
  }
}
</style>

<style>
.edit-page-container .section-card {
  background: #1e293b !important;
}

.edit-page-container .section-card .q-card__section {
  background: transparent !important;
  color: #ffffff !important;
}

.edit-page-container .section-card-header {
  background: #273549 !important;
}

.edit-page-container .remove-role-dialog {
  background: #1e293b !important;
}

.edit-page-container .remove-role-dialog .q-card__section {
  background: transparent !important;
  color: #ffffff !important;
}

.remove-role-dialog .dialog-header {
  background: #273549 !important;
}

.remove-role-dialog .dialog-actions {
  background: #1e293b !important;
}
</style>