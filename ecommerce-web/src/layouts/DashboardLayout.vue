<template>
  <q-layout view="lHh Lpr lFf" class="dashboard-layout">
    <!-- Header -->
    <q-header class="dashboard-header" reveal>
      <q-toolbar class="dashboard-toolbar">
        <q-btn flat dense round @click="toggleLeftDrawer" aria-label="Menu" icon="menu" class="menu-toggle-btn"
          color="grey-7" />

        <router-link to="/" class="logo-section">
          <div class="logo-container">
            <BiliscartLogo :size="42" />
          </div>
          <div class="logo-text">
            <q-toolbar-title class="dashboard-title">My Near Shops</q-toolbar-title>
            <span class="dashboard-tagline">Dashboard</span>
          </div>
        </router-link>

        <q-space />

        <!-- Header Actions -->
        <div class="header-actions">
          <q-btn flat round dense class="header-action-btn" icon="notifications" color="grey-6">
            <q-badge floating color="negative" rounded class="notification-badge">2</q-badge>
          </q-btn>
          <q-btn flat round dense class="header-action-btn" icon="settings" color="grey-6" />
        </div>

        <!-- User Info -->
        <q-btn flat no-caps class="user-info-section" padding="6px 8px 6px 14px">
          <q-avatar size="38px" class="user-avatar">
            <q-icon name="account_circle" size="38px" color="white" />
          </q-avatar>
          <div class="user-details q-ml-sm">
            <div class="user-name">{{ profile.name || 'User' }}</div>
            <div class="user-role">Administrator</div>
          </div>
          <q-icon name="expand_more" size="18px" color="grey-5" class="q-ml-sm" />

          <q-menu anchor="bottom right" self="top right" class="user-menu" :offset="[0, 10]">
            <div class="user-menu-header">
              <q-avatar size="48px" class="user-menu-avatar">
                <q-icon name="account_circle" size="48px" color="white" />
              </q-avatar>
              <div class="user-menu-info">
                <div class="user-menu-name">{{ profile.name || 'User' }}</div>
                <div class="user-menu-email">{{ profile.email || profile.mobile || '' }}</div>
              </div>
            </div>
            <q-separator class="user-menu-divider" />
            <q-list class="user-menu-list">
              <q-item clickable v-close-popup class="user-menu-item">
                <q-item-section avatar>
                  <q-icon name="person" size="20px" />
                </q-item-section>
                <q-item-section>My Profile</q-item-section>
              </q-item>
              <q-item clickable v-close-popup class="user-menu-item">
                <q-item-section avatar>
                  <q-icon name="settings" size="20px" />
                </q-item-section>
                <q-item-section>Settings</q-item-section>
              </q-item>
              <q-separator class="user-menu-divider" />
              <q-item clickable v-close-popup class="user-menu-item logout-item" @click="logoutNow">
                <q-item-section avatar>
                  <q-icon name="logout" size="20px" />
                </q-item-section>
                <q-item-section>Logout</q-item-section>
              </q-item>
            </q-list>
          </q-menu>
        </q-btn>
      </q-toolbar>
    </q-header>

    <!-- Sidebar Drawer -->
    <q-drawer v-model="leftDrawerOpen" show-if-above class="dashboard-drawer" :width="290">
      <!-- Ambient background -->
      <div class="drawer-bg">
        <div class="drawer-orb orb-1"></div>
        <div class="drawer-orb orb-2"></div>
        <div class="drawer-grid"></div>
      </div>

      <!-- User Profile Section -->
      <div class="drawer-header">
        <div class="drawer-user-card">
          <div class="drawer-avatar-ring">
            <q-avatar size="72px" class="drawer-avatar">
              <q-icon name="account_circle" size="72px" color="white" />
            </q-avatar>
            <div class="online-indicator"></div>
          </div>
          <div class="drawer-user-info">
            <div class="drawer-user-name">{{ profile.name || 'User' }}</div>
            <div class="drawer-user-role">Administrator</div>
          </div>
          <div class="drawer-user-meta">
            <q-icon name="phone_iphone" size="14px" />
            <span>{{ profile.mobile || '' }}</span>
          </div>
        </div>
      </div>

      <!-- Navigation Menu -->
      <q-list class="dashboard-menu" padding>
        <q-item-label header class="menu-section-header">
          <span class="menu-section-line"></span>
          <span class="menu-section-text">Navigation</span>
          <span class="menu-section-line"></span>
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
            <div class="menu-arrow-pill">
              <q-icon name="chevron_right" size="14px" class="menu-arrow" />
            </div>
          </q-item-section>
        </q-item>
      </q-list>

      <!-- Footer Section -->
      <div class="drawer-footer">
        <div class="drawer-footer-card">
          <div class="footer-card-content">
            <q-icon name="lightbulb" size="20px" color="warning" />
            <div class="footer-card-text">
              <div class="footer-card-title">Need help?</div>
              <div class="footer-card-subtitle">Check our docs</div>
            </div>
          </div>
          <q-btn flat round dense size="sm" icon="arrow_forward" color="white" class="footer-card-btn" />
        </div>
        <q-btn unelevated no-caps class="logout-btn" @click="handleLogout">
          <q-icon name="logout" size="18px" class="q-mr-sm" />
          Sign Out
        </q-btn>
      </div>
    </q-drawer>

    <!-- Main Content -->
    <q-page-container class="dashboard-content">
      <div class="content-bg">
        <div class="content-orb orb-1"></div>
        <div class="content-orb orb-2"></div>
      </div>
      <router-view />
    </q-page-container>

    <!-- Logout Confirmation Dialog -->
    <q-dialog v-model="showLogoutDialog" persistent>
      <q-card class="logout-dialog-card">
        <div class="logout-dialog-glow"></div>
        <q-card-section class="logout-dialog-header">
          <div class="logout-dialog-icon">
            <q-icon name="logout" size="32px" color="white" />
          </div>
          <div class="text-h6 text-weight-bold">Sign Out?</div>
          <q-btn icon="close" flat round dense v-close-popup class="logout-dialog-close" />
        </q-card-section>

        <q-card-section class="logout-dialog-body">
          <div class="text-body1 text-grey-7">
            Are you sure you want to sign out of your account?
          </div>
        </q-card-section>

        <q-card-actions align="right" class="logout-dialog-actions">
          <q-btn unelevated no-caps label="Cancel" color="grey-3" text-color="grey-8" v-close-popup
            class="cancel-btn" />
          <q-btn unelevated no-caps label="Sign Out" color="negative" @click="confirmLogout" class="confirm-btn" />
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
.dashboard-layout,
.dashboard-layout :deep(.q-layout),
.dashboard-layout :deep(.q-drawer),
.dashboard-layout :deep(.q-drawer__content),
.dashboard-layout :deep(.q-page-container),
.dashboard-layout :deep(.q-page) {
  background: #0f172a !important;
}

.dashboard-layout {
  min-height: 100vh;
  color: #f8fafc;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* Header */
.dashboard-header {
  background: rgba(15, 23, 42, 0.92);
  backdrop-filter: blur(16px);
  border-bottom: 1px solid rgba(255, 255, 255, 0.08);
  box-shadow: 0 4px 30px rgba(0, 0, 0, 0.2);
  height: 72px;
  z-index: 1000;
}

.dashboard-toolbar {
  min-height: 72px;
  padding: 0 24px;
}

.menu-toggle-btn {
  margin-right: 14px;
  width: 44px;
  height: 44px;
  border-radius: 14px;
  transition: all 0.25s ease;
  color: #e2e8f0 !important;

  &:hover {
    background: rgba(255, 255, 255, 0.08);
    transform: scale(1.05);
  }
}

.logo-section {
  display: flex;
  align-items: center;
  gap: 14px;
  text-decoration: none;
  color: inherit;

  &:hover {
    transform: scale(1.02);
  }
}

.logo-container {
  width: 46px;
  height: 46px;
  border-radius: 14px;
  background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
  box-shadow: 0 4px 14px rgba(0, 0, 0, 0.3);
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
  padding: 4px;
}

.logo-section:hover .logo-container {
  box-shadow: 0 8px 24px rgba(99, 102, 241, 0.25);
  transform: scale(1.05);
}

.logo-text {
  display: flex;
  flex-direction: column;
  line-height: 1.1;
}

.dashboard-title {
  font-size: 20px;
  font-weight: 800;
  color: #f8fafc;
  padding: 0;
  letter-spacing: -0.4px;
}

.dashboard-tagline {
  font-size: 11px;
  font-weight: 800;
  color: #818cf8;
  text-transform: uppercase;
  letter-spacing: 0.8px;
}

.header-actions {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-right: 18px;
}

.header-action-btn {
  width: 42px;
  height: 42px;
  border-radius: 14px;
  background: rgba(255, 255, 255, 0.06);
  color: #cbd5e1 !important;
  transition: all 0.25s ease;
  position: relative;

  &:hover {
    background: rgba(255, 255, 255, 0.12);
    color: #f8fafc !important;
    transform: translateY(-2px);
  }
}

.notification-badge {
  top: 2px;
  right: 2px;
  font-size: 10px;
  min-width: 16px;
  height: 16px;
  padding: 0 4px;
  font-weight: 800;
}

.user-info-section {
  display: flex;
  align-items: center;
  border-radius: 16px;
  background: #1e293b;
  border: 1px solid rgba(255, 255, 255, 0.1);
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
  transition: all 0.3s ease;
  cursor: pointer;

  &:hover {
    background: #334155;
    border-color: rgba(255, 255, 255, 0.14);
    transform: translateY(-1px);
  }

  >.q-icon {
    color: #ffffff !important;
  }
}

.user-avatar {
  background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
  border: 2px solid rgba(255, 255, 255, 0.9);
  box-shadow: 0 2px 10px rgba(79, 70, 229, 0.3);
}

.user-details {
  display: flex;
  flex-direction: column;
  text-align: left;
}

.user-name {
  font-size: 14px;
  font-weight: 700;
  color: #ffffff;
  line-height: 1.3;
}

.user-role {
  font-size: 11px;
  font-weight: 700;
  color: #ffffff;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.user-menu {
  border-radius: 18px;
  box-shadow: 0 24px 60px rgba(0, 0, 0, 0.35);
  background: #1e293b;
  overflow: hidden;
  min-width: 230px;
}

.user-menu-header {
  padding: 20px;
  background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
  display: flex;
  align-items: center;
  gap: 14px;
}

.user-menu-avatar {
  background: rgba(255, 255, 255, 0.22);
  border: 2px solid rgba(255, 255, 255, 0.45);
}

.user-menu-info {
  display: flex;
  flex-direction: column;
}

.user-menu-name {
  font-size: 15px;
  font-weight: 800;
  color: white;
}

.user-menu-email {
  font-size: 12px;
  color: rgba(255, 255, 255, 0.8);
  margin-top: 2px;
}

.user-menu-divider {
  background: rgba(255, 255, 255, 0.08);
}

.user-menu-list {
  padding: 10px;
}

.user-menu-item {
  border-radius: 12px;
  min-height: 46px;
  transition: all 0.2s ease;
  color: #ffffff;
  font-weight: 600;
  font-size: 14px;

  &:hover {
    background: rgba(255, 255, 255, 0.08);
  }

  &.logout-item:hover {
    background: rgba(239, 68, 68, 0.12);
  }
}

/* Sidebar */
.dashboard-drawer {
  box-shadow: 8px 0 40px rgba(0, 0, 0, 0.3) !important;
  border-right: 1px solid rgba(255, 255, 255, 0.06) !important;
  overflow: hidden;
}

.dashboard-drawer .q-drawer__content {
  background: #0f172a !important;
  display: flex;
  flex-direction: column;
  height: 100vh;
}

.drawer-bg {
  display: none;
}

.drawer-header {
  padding: 32px 24px 24px;
  flex-shrink: 0;
  position: relative;
  z-index: 1;
}

.drawer-user-card {
  background: #1e293b;
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 20px;
  padding: 24px;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  box-shadow: 0 16px 40px rgba(0, 0, 0, 0.25);
  transition: all 0.3s ease;

  &:hover {
    background: #334155;
    border-color: rgba(255, 255, 255, 0.14);
  }
}

.drawer-avatar-ring {
  position: relative;
  padding: 4px;
  border-radius: 50%;
  background: conic-gradient(from 0deg, #fbbf24, #a855f7, #6366f1, #fbbf24);
  box-shadow: 0 6px 20px rgba(168, 85, 247, 0.4);
}

.drawer-avatar {
  background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
  border: 3px solid #0f172a;
}

.online-indicator {
  position: absolute;
  bottom: 6px;
  right: 6px;
  width: 14px;
  height: 14px;
  border-radius: 50%;
  background: #22c55e;
  border: 2px solid #0f172a;
  box-shadow: 0 0 10px rgba(34, 197, 94, 0.55);
}

.drawer-user-info {
  margin-top: 16px;
}

.drawer-user-name {
  font-size: 17px;
  font-weight: 800;
  color: #ffffff;
  letter-spacing: -0.2px;
}

.drawer-user-role {
  font-size: 11px;
  font-weight: 700;
  color: #ffffff;
  text-transform: uppercase;
  letter-spacing: 0.8px;
  margin-top: 4px;
}

.drawer-user-meta {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  margin-top: 12px;
  padding: 6px 12px;
  border-radius: 20px;
  background: rgba(255, 255, 255, 0.1);
  font-size: 12px;
  font-weight: 600;
  color: #ffffff;
}

/* Navigation */
.dashboard-menu {
  padding: 8px 16px;
  flex: 1;
  overflow-y: auto;
  position: relative;
  z-index: 1;
}

.menu-section-header {
  padding: 18px 8px 14px;
  font-weight: 800;
  color: #ffffff;
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 1.4px;
  display: flex;
  align-items: center;
  gap: 10px;
}

.menu-section-line {
  flex: 1;
  height: 1px;
  background: linear-gradient(90deg, transparent 0%, rgba(255, 255, 255, 0.18) 50%, transparent 100%);
}

.menu-section-text {
  flex-shrink: 0;
}

.menu-item {
  margin: 8px 0;
  border-radius: 16px;
  min-height: 56px;
  transition: all 0.25s ease;
  position: relative;
  overflow: hidden;
  color: #ffffff;
  background: #1e293b;
  border: 1px solid rgba(255, 255, 255, 0.08);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);

  &::before {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 4px;
    height: 0;
    background: #fbbf24;
    border-radius: 0 3px 3px 0;
    transition: height 0.3s ease;
  }

  &:hover {
    background: #334155;
    color: #ffffff;
    transform: translateX(6px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);

    .menu-icon-wrapper {
      background: linear-gradient(135deg, #a855f7 0%, #6366f1 100%);
      color: white;
      transform: scale(1.05);
    }

    .menu-item-label {
      color: #ffffff;
      font-weight: 700;
    }

    .menu-arrow-pill {
      background: rgba(255, 255, 255, 0.12);
      transform: translateX(0);
      opacity: 1;
    }
  }
}

.menu-item-active {
  background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
  color: #ffffff;
  box-shadow: 0 10px 28px rgba(79, 70, 229, 0.35);
  border: 1px solid rgba(255, 255, 255, 0.18);

  &::before {
    height: 60%;
  }

  .menu-icon-wrapper {
    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
    color: #0f172a;
    box-shadow: 0 4px 16px rgba(251, 191, 36, 0.4);
  }

  .menu-item-label {
    color: #ffffff;
    font-weight: 700;
  }

  .menu-arrow-pill {
    background: rgba(255, 255, 255, 0.22);
    opacity: 1;
    transform: translateX(0);
  }

  &:hover {
    background: linear-gradient(135deg, #4338ca 0%, #6d28d9 100%);
    color: #ffffff;
    transform: translateX(6px);
  }
}

.menu-icon-section {
  padding-right: 12px;
}

.menu-icon-wrapper {
  width: 40px;
  height: 40px;
  border-radius: 12px;
  background: rgba(255, 255, 255, 0.08);
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
  color: #ffffff;
}

.menu-item-label {
  font-weight: 600;
  font-size: 14px;
  transition: color 0.3s ease;
}

.menu-arrow-section {
  padding-left: 8px;
}

.menu-arrow-pill {
  width: 26px;
  height: 26px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.06);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transform: translateX(-8px);
  transition: all 0.3s ease;
}

.menu-arrow {
  color: #ffffff;
}

.menu-item-active .menu-arrow {
  color: #ffffff;
}

/* Drawer footer */
.drawer-footer {
  flex-shrink: 0;
  padding: 20px;
  position: relative;
  z-index: 1;
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.drawer-footer-card {
  background: #1e293b;
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 16px;
  padding: 16px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  transition: all 0.3s ease;
  cursor: pointer;

  &:hover {
    background: #334155;
    box-shadow: 0 8px 28px rgba(0, 0, 0, 0.25);
  }
}

.footer-card-content {
  display: flex;
  align-items: center;
  gap: 12px;
}

.footer-card-text {
  display: flex;
  flex-direction: column;
}

.footer-card-title {
  font-size: 14px;
  font-weight: 700;
  color: #ffffff;
}

.footer-card-subtitle {
  font-size: 12px;
  color: #ffffff;
}

.footer-card-btn {
  transition: all 0.2s ease;

  &:hover {
    transform: translateX(3px);
  }
}

.logout-btn {
  width: 100%;
  height: 48px;
  border-radius: 14px;
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.12);
  color: #ffffff;
  font-weight: 700;
  font-size: 14px;
  transition: all 0.3s ease;

  &:hover {
    background: rgba(239, 68, 68, 0.2);
    color: #ffffff;
    border-color: rgba(239, 68, 68, 0.4);
    transform: translateY(-2px);
  }
}

/* Content */
.dashboard-content {
  background: #0f172a;
  min-height: calc(100vh - 72px);
  position: relative;
  overflow: hidden;
}

.content-bg {
  position: absolute;
  inset: 0;
  pointer-events: none;
}

.content-orb {
  position: absolute;
  border-radius: 50%;
  filter: blur(100px);
  opacity: 0.18;

  &.orb-1 {
    width: 600px;
    height: 600px;
    background: rgba(99, 102, 241, 0.35);
    top: -220px;
    right: -160px;
  }

  &.orb-2 {
    width: 500px;
    height: 500px;
    background: rgba(168, 85, 247, 0.25);
    bottom: -160px;
    left: -120px;
  }
}

/* Logout dialog */
.logout-dialog-card {
  min-width: 420px;
  border-radius: 24px;
  overflow: hidden;
  position: relative;
  background: #1e293b;
  border: 1px solid rgba(255, 255, 255, 0.08);
  box-shadow: 0 28px 70px rgba(0, 0, 0, 0.35);
}

.logout-dialog-glow {
  position: absolute;
  top: -80px;
  right: -80px;
  width: 260px;
  height: 260px;
  border-radius: 50%;
  background: rgba(239, 68, 68, 0.18);
  filter: blur(60px);
  pointer-events: none;
}

.logout-dialog-header {
  padding: 28px 24px 0;
  display: flex;
  align-items: center;
  gap: 16px;
  position: relative;
}

.logout-dialog-header .text-h6 {
  color: #f8fafc;
}

.logout-dialog-icon {
  width: 56px;
  height: 56px;
  border-radius: 16px;
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 12px 28px rgba(239, 68, 68, 0.35);
}

.logout-dialog-close {
  position: absolute;
  top: 20px;
  right: 20px;
  color: #94a3b8;
}

.logout-dialog-body {
  padding: 16px 24px 8px;
}

.logout-dialog-body .text-body1 {
  color: #cbd5e1;
  font-weight: 500;
}

.logout-dialog-actions {
  padding: 20px 24px 24px;
  gap: 12px;
}

.cancel-btn {
  height: 44px;
  border-radius: 12px;
  font-weight: 700;
  font-size: 14px;
  padding: 0 22px;
  transition: all 0.2s ease;
  background: #334155;
  color: #e2e8f0;

  &:hover {
    background: #475569;
  }
}

.confirm-btn {
  height: 44px;
  border-radius: 12px;
  font-weight: 700;
  font-size: 14px;
  padding: 0 26px;
  transition: all 0.2s ease;

  &:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(239, 68, 68, 0.35);
  }
}

/* Responsive */
@media (max-width: 600px) {
  .dashboard-toolbar {
    padding: 0 12px;
  }

  .user-info-section {
    padding: 4px;
    border-radius: 50%;
  }

  .user-details,
  .dashboard-tagline {
    display: none;
  }

  .dashboard-drawer {
    width: 260px !important;
  }

  .dashboard-title {
    font-size: 17px;
  }

  .header-actions {
    margin-right: 8px;
  }

  .logout-dialog-card {
    min-width: 300px;
  }
}
</style>

<style>
.user-menu {
  background: #1e293b !important;
  border-radius: 18px;
  overflow: hidden;
  min-width: 230px;
}

.user-menu-header {
  background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
  padding: 20px;
  display: flex;
  align-items: center;
  gap: 14px;
}

.user-menu-name {
  font-size: 15px;
  font-weight: 800;
  color: #ffffff;
}

.user-menu-email {
  font-size: 12px;
  color: rgba(255, 255, 255, 0.8);
  margin-top: 2px;
}

.user-menu-list {
  padding: 10px;
}

.user-menu .q-item {
  border-radius: 12px;
  color: #ffffff !important;
  min-height: 46px;
  font-weight: 600;
  font-size: 14px;
}

.user-menu .q-item:hover {
  background: rgba(255, 255, 255, 0.08);
}

.user-menu .q-item.logout-item:hover {
  background: rgba(239, 68, 68, 0.12) !important;
}

.user-menu .q-separator {
  background: rgba(255, 255, 255, 0.08);
}

.logout-dialog-card {
  background: #1e293b !important;
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 24px;
  min-width: 420px;
}

.logout-dialog-card .text-h6 {
  color: #ffffff !important;
}

.logout-dialog-card .text-body1 {
  color: #cbd5e1 !important;
}

.logout-dialog-card .cancel-btn {
  background: #334155 !important;
  color: #ffffff !important;
}
</style>
