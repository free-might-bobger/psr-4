<template>
  <q-layout view="lHh Lpr fff" class="no-shadow no-borders">
    <q-header class="main-header bg-white">
      <q-toolbar class="main-toolbar bg-white text-grey-8">
        <!-- Logo Section -->
        <router-link to="/" class="logo-link">
          <div class="logo-container">
            <BiliscartLogo :size="48" />
          </div>
          <div class="logo-text">
            <span class="logo-brand-name">My Near Shops</span>
            <span class="logo-tagline">Your Community Marketplace</span>
          </div>
        </router-link>

        <q-space />

        <!-- Action Buttons -->
        <div class="header-actions">
          <!-- Cart Button -->
          <q-btn to="/cart" icon="shopping_cart" flat class="header-btn cart-btn" size="md">
            <span class="btn-label">Cart</span>
            <q-badge v-if="countTotalItems > 0" color="negative" text-color="white" floating rounded class="cart-badge">
              {{ countTotalItems }}
            </q-badge>
          </q-btn>

          <!-- Login Button -->
          <q-btn v-if="!profile.token" to="/login" icon="login" flat class="header-btn" size="md">
            <span class="btn-label">Login</span>
          </q-btn>

          <!-- User Menu -->
          <q-btn v-if="profile.token" icon="account_circle" flat class="header-btn user-btn" size="md">
            <span class="btn-label">Account</span>
            <q-menu class="user-menu" anchor="bottom right" self="top right" :offset="[0, 8]">
              <q-list class="user-menu-list" padding>
                <q-item-label header class="user-menu-header">
                  <q-icon name="account_circle" class="q-mr-sm" />
                  Account Menu
                </q-item-label>
                <q-separator />
                <q-item clickable v-for="menu in profile.userMenu" :key="menu.id" :to="menu.path" class="user-menu-item"
                  v-close-popup>
                  <q-item-section avatar>
                    <q-icon :name="menu.icon" size="sm" />
                  </q-item-section>
                  <q-item-section>
                    <q-item-label>{{ menu.name }}</q-item-label>
                  </q-item-section>
                </q-item>
                <q-separator />
                <q-item clickable class="user-menu-item user-menu-logout" @click="logout" v-close-popup>
                  <q-item-section avatar>
                    <q-icon name="logout" size="sm" color="negative" />
                  </q-item-section>
                  <q-item-section>
                    <q-item-label class="text-negative">Logout</q-item-label>
                  </q-item-section>
                </q-item>
              </q-list>
            </q-menu>
          </q-btn>
        </div>
      </q-toolbar>
    </q-header>

    <q-page-container class="main-page-container">
      <q-page class="main-page">
        <router-view />
      </q-page>
    </q-page-container>

    <!-- Footer -->
    <AppFooter />
  </q-layout>
</template>

<script setup lang="ts">
import { useUserStore } from 'src/stores/user';
import { storeToRefs } from 'pinia';
import { useUserCartStore } from 'src/stores/userCart';
import { logout } from 'src/boot/axios-call';
import AppFooter from 'src/components/AppFooter.vue';
import BiliscartLogo from 'src/components/BiliscartLogo.vue';

const { profile } = storeToRefs(useUserStore());
const { countTotalItems } = storeToRefs(useUserCartStore());
</script>

<style scoped lang="scss">
.main-header {
  border-bottom: 1px solid #e0e0e0;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
  height: 64px;
  transition: all 0.3s ease;
}

.main-toolbar {
  min-height: 64px;
  padding: 0 16px;
}

.logo-link {
  text-decoration: none;
  display: flex;
  align-items: center;
  gap: 12px;
  transition: transform 0.2s ease;
  color: inherit;

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
  flex-shrink: 0;
}

.logo-link:hover .logo-container {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  transform: scale(1.05);
}

.logo-text {
  display: flex;
  flex-direction: column;
}

.logo-brand-name {
  font-size: 22px;
  font-weight: 600;
  color: #424242;
  line-height: 1.3;
  letter-spacing: -0.3px;
}

.logo-tagline {
  font-size: 12px;
  color: #757575;
  line-height: 1.2;
  margin-top: 4px;
  font-weight: 400;
  letter-spacing: 0.1px;
}

.logo-avatar {
  border: 2px solid #f5f5f5;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;

  &:hover {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
  }
}

.header-actions {
  display: flex;
  align-items: center;
  gap: 12px;
}

.header-btn {
  position: relative;
  transition: all 0.3s ease;
  padding: 8px 16px;
  border-radius: 8px;
  font-weight: 500;
  min-width: 100px;
  display: flex;
  align-items: center;
  gap: 8px;

  &:hover {
    background-color: #f5f5f5;
    transform: translateY(-1px);
  }

  &:active {
    transform: translateY(0);
  }
}

.btn-label {
  font-size: 14px;
}

.cart-btn {
  position: relative;
}

.cart-badge {
  font-size: 11px;
  font-weight: 600;
  min-width: 20px;
  height: 20px;
  padding: 0 6px;
  animation: pulse 2s infinite;
}

@keyframes pulse {

  0%,
  100% {
    transform: scale(1);
  }

  50% {
    transform: scale(1.1);
  }
}

.user-menu {
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  overflow: hidden;
  margin-top: 8px;
}

.user-menu-list {
  min-width: 220px;
  padding: 8px 0;
}

.user-menu-header {
  padding: 12px 16px;
  font-weight: 600;
  color: #1a1a1a;
  font-size: 14px;
  display: flex;
  align-items: center;
}

.user-menu-item {
  padding: 10px 16px;
  transition: background-color 0.2s ease;
  min-height: 48px;

  &:hover {
    background-color: #f5f5f5;
  }
}

.user-menu-logout {
  &:hover {
    background-color: #ffebee;
  }
}

.main-page-container {
  background: #fafafa;
  min-height: calc(100vh - 64px);
}

.main-page {
  padding: 0;
}

@media (max-width: 600px) {
  .main-toolbar {
    padding: 0 8px;
  }

  .header-btn {
    margin: 0 2px;
  }

  .user-menu-list {
    min-width: 200px;
  }
}
</style>

<style>
.footer_menu a {
  color: white;
}
</style>
