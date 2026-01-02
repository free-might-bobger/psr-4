<template>
  <q-layout view="lHh Lpr lFf" class="dashboard-layout bg-grey-1">
    <!-- Header -->
    <q-header class="dashboard-header bg-white">
      <q-toolbar class="dashboard-toolbar">
        <q-btn flat dense round @click="toggleLeftDrawer" aria-label="Menu" icon="menu" class="menu-toggle-btn"
          color="grey-8" />
        <router-link to="/" class="logo-section">
          <div class="logo-container">
            <ZentenpoLogo :size="48" />
          </div>
          <q-toolbar-title class="dashboard-title">
            Zentenpo
          </q-toolbar-title>
        </router-link>

        <q-space />

        <!-- User Info -->
        <div class="user-info-section">
          <q-avatar size="36px" class="user-avatar">
            <q-icon name="account_circle" size="36px" color="primary" />
          </q-avatar>
          <div class="user-details q-ml-sm">
            <div class="text-body2 text-grey-8 text-weight-medium">{{ profile.name || 'User' }}</div>
            <div class="text-caption text-grey-6">{{ profile.mobile || '' }}</div>
          </div>
        </div>
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
          <q-icon name="settings" class="q-mr-sm" />
          Menu
        </q-item-label>
        <q-item clickable :to="menu.path" v-for="menu in profile.userMenu" :key="menu.id" class="menu-item"
          active-class="menu-item-active" v-ripple>
          <q-item-section avatar>
            <q-icon :name="menu.icon" size="sm" />
          </q-item-section>
          <q-item-section>
            <q-item-label>{{ menu.name }}</q-item-label>
          </q-item-section>
          <q-item-section side>
            <q-icon name="chevron_right" size="xs" color="grey-5" />
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

  </q-layout>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useUserStore } from 'src/stores/user';
import { storeToRefs } from 'pinia';
import { logout } from 'src/boot/axios-call';
import { useRouter } from 'vue-router';
import { useQuasar } from 'quasar';
import ZentenpoLogo from 'src/components/ZentenpoLogo.vue';
import AppFooter from 'src/components/AppFooter.vue';

const $q = useQuasar();
const router = useRouter();
const { profile } = storeToRefs(useUserStore());
const leftDrawerOpen = ref(false);

const toggleLeftDrawer = () => {
  leftDrawerOpen.value = !leftDrawerOpen.value;
};

const handleLogout = async () => {
  $q.dialog({
    title: 'Confirm Logout',
    message: 'Are you sure you want to logout?',
    cancel: true,
    persistent: true
  }).onOk(async () => {
    await logout();
    router.push('/');
  });
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
}

.drawer-header {
  padding: 24px 16px;
  background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
  border-bottom: 1px solid #e0e0e0;
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
}

.menu-section-header {
  padding: 12px 16px;
  font-weight: 600;
  color: #666;
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  display: flex;
  align-items: center;
}

.menu-item {
  margin: 4px 8px;
  border-radius: 8px;
  transition: all 0.3s ease;
  min-height: 48px;

  &:hover {
    background-color: #f5f5f5;
    transform: translateX(4px);
  }
}

.menu-item-active {
  background-color: var(--q-primary);
  color: white;

  .q-icon {
    color: white;
  }

  .q-item__label {
    color: white;
    font-weight: 600;
  }

  &:hover {
    background-color: var(--q-primary);
    opacity: 0.9;
    transform: translateX(4px);
  }
}

.drawer-footer {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background: white;
}

.drawer-footer-content {
  padding: 16px;
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
