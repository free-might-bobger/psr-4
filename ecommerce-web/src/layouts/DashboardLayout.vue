<template>
  <q-layout view="lHh Lpr lFf" class="dashboard-layout bg-grey-1">
    <!-- Header -->
    <q-header class="dashboard-header bg-white">
      <q-toolbar class="dashboard-toolbar">
        <q-btn flat dense round @click="toggleLeftDrawer" aria-label="Menu" icon="menu" class="menu-toggle-btn"
          color="grey-8" />
        <router-link to="/" class="logo-section">
          <div class="logo-container">
            <BiliscartLogo :size="48" />
          </div>
          <q-toolbar-title class="dashboard-title">
            My Near Shops
          </q-toolbar-title>
        </router-link>

        <q-space />

        <!-- User Info -->
        <q-btn flat dense no-caps class="user-info-section" padding="8px 12px">
          <q-avatar size="36px" class="user-avatar">
            <q-icon name="account_circle" size="36px" color="primary" />
          </q-avatar>
          <div class="user-details q-ml-sm">
            <div class="text-body2 text-grey-8 text-weight-medium">{{ profile.name || 'User' }}</div>
            <div class="text-caption text-grey-6">{{ profile.mobile || '' }}</div>
          </div>
          <q-menu anchor="bottom right" self="top right">
            <q-list style="min-width: 160px">
              <q-item clickable v-close-popup @click="logoutNow">
                <q-item-section avatar>
                  <q-icon name="logout" />
                </q-item-section>
                <q-item-section>Logout</q-item-section>
              </q-item>
            </q-list>
          </q-menu>
        </q-btn>
      </q-toolbar>
    </q-header>

    <!-- Sidebar Drawer -->
    <q-drawer v-model="leftDrawerOpen" show-if-above bordered class="dashboard-drawer bg-white" :width="280">
      <!-- User Profile Section -->
      <div class="drawer-header">
        <div class="drawer-user-profile">
          <q-avatar size="64px" class="drawer-avatar">
            <q-icon name="account_circle" size="64px" color="primary" />
          </q-avatar>
          <div class="drawer-user-info q-mt-sm">
            <div class="text-h6 text-weight-bold">{{ profile.name || 'User' }}</div>
            <div class="text-body2 text-grey-6">{{ profile.mobile || '' }}</div>
          </div>
        </div>
      </div>

      <q-separator />

      <!-- Navigation Menu -->
      <q-list class="dashboard-menu" padding>
        <q-item-label header class="menu-section-header">
          <q-icon name="apps" class="q-mr-sm" size="18px" />
          Navigation
        </q-item-label>
        <q-item clickable :to="menu.path" v-for="menu in profile.userMenu" :key="menu.id" class="menu-item"
          active-class="menu-item-active" v-ripple>
          <q-item-section avatar class="menu-icon-section">
            <div class="menu-icon-wrapper">
              <q-icon :name="menu.icon" size="20px" />
            </div>
          </q-item-section>
          <q-item-section>
            <q-item-label class="menu-item-label">{{ menu.name }}</q-item-label>
          </q-item-section>
          <q-item-section side class="menu-arrow-section">
            <q-icon name="chevron_right" size="16px" class="menu-arrow" />
          </q-item-section>
        </q-item>
      </q-list>

      <!-- Footer Section -->
      <div class="drawer-footer">
        <q-separator />
        <div class="drawer-footer-content">
          <q-btn flat icon="logout" label="Logout" color="negative" class="logout-btn" @click="handleLogout" />
        </div>
      </div>
    </q-drawer>

    <!-- Main Content -->
    <q-page-container class="dashboard-content">
      <router-view />
    </q-page-container>

    <!-- Logout Confirmation Dialog -->
    <q-dialog v-model="showLogoutDialog" persistent>
      <q-card class="logout-dialog-card">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-h6 text-weight-bold">Confirm Logout</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>

        <q-card-section class="q-pt-md">
          <div class="logout-dialog-content">
            <q-icon name="logout" size="48px" color="negative" class="q-mb-md" />
            <div class="text-body1 text-grey-8">
              Are you sure you want to logout?
            </div>
          </div>
        </q-card-section>

        <q-card-actions align="right" class="q-pa-md">
          <q-btn flat label="Cancel" color="grey-7" v-close-popup />
          <q-btn flat label="Logout" color="negative" @click="confirmLogout" />
        </q-card-actions>
      </q-card>
    </q-dialog>

  </q-layout>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useUserStore } from 'src/stores/user';
import { storeToRefs } from 'pinia';
import { logout } from 'src/boot/axios-call';
import { useRouter } from 'vue-router';
import { useQuasar } from 'quasar';
import BiliscartLogo from 'src/components/BiliscartLogo.vue';

const router = useRouter();
const $q = useQuasar();
const { profile } = storeToRefs(useUserStore());
const leftDrawerOpen = ref(false);
const showLogoutDialog = ref(false);

const toggleLeftDrawer = () => {
  leftDrawerOpen.value = !leftDrawerOpen.value;
};

const handleLogout = () => {
  showLogoutDialog.value = true;
};

const confirmLogout = async () => {
  if (!showLogoutDialog.value) {
    showLogoutDialog.value = true;
    return;
  }

  try {
    showLogoutDialog.value = false;
    await logout();
    $q.notify({
      message: 'You have been logged out successfully.',
      type: 'positive',
      position: 'top',
    });
    router.push('/');
  } catch (error) {
    $q.notify({
      message: 'An error occurred during logout.',
      type: 'negative',
      position: 'top',
    });
  }
};

const logoutNow = async () => {
  try {
    await logout();
    $q.notify({
      message: 'You have been logged out successfully.',
      type: 'positive',
      position: 'top',
    });
    router.push('/');
  } catch (error) {
    $q.notify({
      message: 'An error occurred during logout.',
      type: 'negative',
      position: 'top',
    });
  }
};

</script>

<style scoped lang="scss">
.dashboard-layout {
  min-height: 100vh;
}

.dashboard-header {
  border-bottom: 1px solid #e0e0e0;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
  height: 64px;
}

.dashboard-toolbar {
  min-height: 64px;
  padding: 0 16px;
}

.menu-toggle-btn {
  margin-right: 8px;
  transition: all 0.3s ease;

  &:hover {
    background-color: #f5f5f5;
    transform: scale(1.1);
  }
}

.logo-section {
  display: flex;
  align-items: center;
  text-decoration: none;
  color: inherit;
  transition: transform 0.2s ease;
  gap: 12px;

  &:hover {
    transform: scale(1.02);
  }
}

.logo-container {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 48px;
  height: 48px;
  border-radius: 10px;
  background: linear-gradient(135deg, #f5f7fa 0%, #ffffff 100%);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
  padding: 4px;
}

.logo-section:hover .logo-container {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  transform: scale(1.05);
}

.dashboard-title {
  font-size: 20px;
  font-weight: 600;
  color: #1a1a1a;
}

.user-info-section {
  display: flex;
  align-items: center;
  padding: 8px 12px;
  border-radius: 24px;
  background: #f5f5f5;
  transition: background-color 0.3s ease;
  cursor: pointer;

  &:hover {
    background: #eeeeee;
  }
}

.user-avatar {
  border: 2px solid #e0e0e0;
}

.user-details {
  display: flex;
  flex-direction: column;
}

.dashboard-drawer {
  box-shadow: 2px 0 8px rgba(0, 0, 0, 0.08);
  display: flex;
  flex-direction: column;
  height: 100vh;
}

.drawer-header {
  padding: 24px 16px;
  background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
  border-bottom: 1px solid #e0e0e0;
  flex-shrink: 0;
}

.drawer-user-profile {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
}

.drawer-avatar {
  border: 3px solid var(--q-primary);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.drawer-user-info {
  margin-top: 12px;
}

.dashboard-menu {
  padding: 8px 0;
  flex: 1;
  overflow-y: auto;
}

.menu-section-header {
  padding: 16px 20px 12px;
  font-weight: 700;
  color: #8b92a8;
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 1px;
  display: flex;
  align-items: center;
  background: linear-gradient(90deg, transparent 0%, rgba(0, 0, 0, 0.02) 50%, transparent 100%);
}

.menu-item {
  margin: 6px 12px;
  border-radius: 12px;
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
  min-height: 52px;
  position: relative;
  overflow: hidden;

  &::before {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 3px;
    height: 0;
    background: var(--q-primary);
    border-radius: 0 2px 2px 0;
    transition: height 0.25s ease;
  }

  &:hover {
    background: linear-gradient(135deg, #f8f9ff 0%, #f0f4ff 100%);
    transform: translateX(4px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);

    .menu-icon-wrapper {
      background: var(--q-primary);
      color: white;
      transform: scale(1.1);
    }

    .menu-arrow {
      opacity: 1;
      transform: translateX(0);
    }
  }
}

.menu-item-active {
  background: var(--q-primary);
  color: white;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  position: relative;

  &::before {
    height: 60%;
  }

  &::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.15) 0%, rgba(0, 0, 0, 0.1) 100%);
    border-radius: inherit;
    pointer-events: none;
  }

  .menu-icon-wrapper {
    background: rgba(255, 255, 255, 0.2);
    color: white;
  }

  .menu-item-label {
    color: white;
    font-weight: 600;
  }

  .menu-arrow {
    color: white;
    opacity: 1;
    transform: translateX(0);
  }

  &:hover {
    background: var(--q-primary);
    transform: translateX(4px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);

    .menu-icon-wrapper {
      background: rgba(255, 255, 255, 0.3);
      transform: scale(1.1);
    }
  }
}

.menu-icon-section {
  padding-right: 12px;
}

.menu-icon-wrapper {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  background: #f0f2f5;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.25s ease;
  color: #6b7280;
}

.menu-item-label {
  font-weight: 500;
  font-size: 14px;
  color: #374151;
  transition: color 0.25s ease;
}

.menu-arrow-section {
  padding-left: 8px;
}

.menu-arrow {
  color: #9ca3af;
  opacity: 0;
  transform: translateX(-4px);
  transition: all 0.25s ease;
}

.drawer-footer {
  flex-shrink: 0;
  background: white;
}

.drawer-footer-content {
  padding: 16px;
}

.logout-dialog-card {
  min-width: 400px;
  border-radius: 12px;
}

.logout-dialog-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  padding: 16px 0;
}

@media (max-width: 600px) {
  .logout-dialog-card {
    min-width: 300px;
  }
}

.logout-btn {
  width: 100%;
  border-radius: 8px;
  transition: all 0.3s ease;

  &:hover {
    background-color: #ffebee;
    transform: translateY(-2px);
  }
}

.dashboard-content {
  background: #fafafa;
  min-height: calc(100vh - 64px);
}

@media (max-width: 600px) {
  .dashboard-toolbar {
    padding: 0 8px;
  }

  .user-info-section {
    .user-details {
      display: none;
    }
  }

  .dashboard-drawer {
    width: 260px !important;
  }

  .dashboard-title {
    font-size: 18px;
  }
}
</style>
