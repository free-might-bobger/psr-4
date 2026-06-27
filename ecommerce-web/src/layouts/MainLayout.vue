<template>
  <q-layout view="lHh Lpr fff" class="no-shadow no-borders">
    <q-header class="main-header">
      <q-toolbar class="main-toolbar">
        <!-- Logo Section -->
        <router-link to="/" class="logo-link">
          <div class="logo-container">
            <BiliscartLogo :size="40" />
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
          <q-btn to="/cart" flat class="header-btn cart-btn" no-caps>
            <div class="btn-inner">
              <q-icon name="shopping_cart" size="20px" />
              <span class="btn-label gt-xs">Cart</span>
            </div>
            <q-badge v-if="countTotalItems > 0" color="negative" text-color="white" floating rounded class="cart-badge">
              {{ countTotalItems }}
            </q-badge>
          </q-btn>

          <!-- Apply My Store Button -->
          <q-btn to="/apply-store" unelevated no-caps class="apply-store-btn">
            <div class="btn-inner">
              <q-icon name="store" size="18px" />
              <span class="btn-label gt-xs">Create A Store</span>
            </div>
          </q-btn>

          <!-- Login Button -->
          <q-btn v-if="!profile.token" to="/login" flat no-caps class="header-btn login-btn">
            <div class="btn-inner">
              <q-icon name="login" size="20px" />
              <span class="btn-label gt-xs">Login</span>
            </div>
          </q-btn>

          <!-- Register Button -->
          <q-btn v-if="!profile.token" to="/register" unelevated no-caps class="register-btn">
            <div class="btn-inner">
              <q-icon name="person_add" size="18px" />
              <span>Sign Up</span>
            </div>
          </q-btn>

          <!-- User Menu -->
          <q-btn v-if="profile.token" flat no-caps class="header-btn user-btn">
            <div class="user-btn-inner">
              <div class="user-avatar-wrap">
                <q-icon name="person" size="18px" color="white" />
              </div>
              <span class="btn-label gt-xs">{{ profile.name || 'Account' }}</span>
              <q-icon name="expand_more" size="16px" class="gt-xs" />
            </div>
            <q-menu class="user-menu" anchor="bottom right" self="top right" :offset="[0, 10]">
              <div class="user-menu-profile">
                <div class="menu-profile-avatar">
                  <q-icon name="person" size="28px" color="white" />
                </div>
                <div class="menu-profile-info">
                  <div class="menu-profile-name">{{ profile.name || 'User' }}</div>
                  <div class="menu-profile-role">Member</div>
                </div>
              </div>
              <q-separator />
              <q-list class="user-menu-list" padding>
                <q-item clickable v-for="menu in profile.userMenu" :key="menu.id" :to="menu.path" class="user-menu-item"
                  v-close-popup>
                  <q-item-section avatar class="menu-item-icon">
                    <div class="menu-icon-pill">
                      <q-icon :name="menu.icon" size="16px" />
                    </div>
                  </q-item-section>
                  <q-item-section>
                    <q-item-label class="menu-item-label">{{ menu.name }}</q-item-label>
                  </q-item-section>
                  <q-item-section side>
                    <q-icon name="chevron_right" size="14px" color="grey-4" />
                  </q-item-section>
                </q-item>
              </q-list>
              <q-separator />
              <div class="menu-logout-area">
                <q-item clickable class="user-menu-logout" @click="logout" v-close-popup>
                  <q-item-section avatar class="menu-item-icon">
                    <div class="menu-icon-pill logout-pill">
                      <q-icon name="logout" size="16px" />
                    </div>
                  </q-item-section>
                  <q-item-section>
                    <q-item-label class="logout-label">Sign Out</q-item-label>
                  </q-item-section>
                </q-item>
              </div>
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
  background: rgba(255, 255, 255, 0.92);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border-bottom: 1px solid rgba(99, 102, 241, 0.1);
  box-shadow: 0 1px 24px rgba(30, 27, 75, 0.08);
  height: 68px;
}

.main-toolbar {
  min-height: 68px;
  padding: 0 24px;
  max-width: 1400px;
  margin: 0 auto;
  width: 100%;
}

// ── Logo ─────────────────────────────────────────────────────────────────────
.logo-link {
  text-decoration: none;
  display: flex;
  align-items: center;
  gap: 12px;
  transition: opacity 0.2s ease;
  color: inherit;

  &:hover {
    opacity: 0.85;

    .logo-container {
      box-shadow: 0 6px 18px rgba(99, 102, 241, 0.25);
      transform: translateY(-1px);
    }
  }
}

.logo-container {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 44px;
  height: 44px;
  border-radius: 12px;
  background: linear-gradient(135deg, #312e81 0%, #4c1d95 100%);
  box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  flex-shrink: 0;
  padding: 4px;
}

.logo-text {
  display: flex;
  flex-direction: column;
  gap: 1px;
}

.logo-brand-name {
  font-size: 18px;
  font-weight: 800;
  background: linear-gradient(135deg, #1e1b4b 0%, #4c1d95 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  line-height: 1.2;
  letter-spacing: -0.4px;
}

.logo-tagline {
  font-size: 11px;
  color: #9ca3af;
  line-height: 1;
  font-weight: 500;
  letter-spacing: 0.2px;
}

// ── Actions ───────────────────────────────────────────────────────────────────
.header-actions {
  display: flex;
  align-items: center;
  gap: 8px;
}

.header-btn {
  position: relative;
  border-radius: 10px;
  transition: all 0.25s ease;
  color: #374151;
  padding: 0 14px;
  height: 40px;

  &:hover {
    background: rgba(99, 102, 241, 0.07);
    color: #4c1d95;
    transform: translateY(-1px);
  }
}

.btn-inner {
  display: flex;
  align-items: center;
  gap: 6px;
  font-weight: 600;
  font-size: 14px;
}

.btn-label {
  font-size: 14px;
  font-weight: 600;
}

.cart-btn {
  position: relative;
}

.cart-badge {
  font-size: 10px;
  font-weight: 700;
  min-width: 18px;
  height: 18px;
  padding: 0 4px;
  animation: badgePop 2.5s ease-in-out infinite;
}

@keyframes badgePop {

  0%,
  100% {
    transform: scale(1);
  }

  50% {
    transform: scale(1.15);
  }
}

.login-btn {
  border: 1.5px solid rgba(99, 102, 241, 0.25);

  &:hover {
    border-color: rgba(99, 102, 241, 0.5);
    background: rgba(99, 102, 241, 0.05);
  }
}

.register-btn {
  height: 40px;
  padding: 0 20px;
  border-radius: 10px;
  background: linear-gradient(135deg, #312e81 0%, #6d28d9 100%);
  color: white;
  font-weight: 700;
  font-size: 14px;
  box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
  transition: all 0.25s ease;

  .btn-inner {
    gap: 6px;
  }

  &:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(79, 70, 229, 0.4);
  }
}

.apply-store-btn {
  height: 40px;
  padding: 0 16px;
  border-radius: 10px;
  background: linear-gradient(135deg, #059669 0%, #047857 100%);
  color: white;
  font-weight: 700;
  font-size: 14px;
  box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
  transition: all 0.25s ease;

  .btn-inner {
    gap: 6px;
  }

  &:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(5, 150, 105, 0.4);
  }
}

.user-btn {
  padding: 0 12px;
  height: 44px;
  border-radius: 22px;
  border: 1.5px solid rgba(99, 102, 241, 0.2);
  background: rgba(99, 102, 241, 0.04);

  &:hover {
    background: rgba(99, 102, 241, 0.1);
    border-color: rgba(99, 102, 241, 0.4);
  }
}

.user-btn-inner {
  display: flex;
  align-items: center;
  gap: 8px;
  font-weight: 600;
  font-size: 14px;
  color: #1e1b4b;
}

.user-avatar-wrap {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background: linear-gradient(135deg, #312e81 0%, #6d28d9 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

// ── Dropdown Menu ─────────────────────────────────────────────────────────────
.user-menu {
  border-radius: 16px !important;
  box-shadow: 0 16px 48px rgba(0, 0, 0, 0.4), 0 2px 8px rgba(0, 0, 0, 0.2) !important;
  overflow: hidden;
  border: 1px solid rgba(255, 255, 255, 0.08);
  background: #1e293b !important;
}

.user-menu-profile {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 20px 20px 16px;
  background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
}

.menu-profile-avatar {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.15);
  border: 2px solid rgba(255, 255, 255, 0.25);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.menu-profile-name {
  font-size: 15px;
  font-weight: 700;
  color: white;
  line-height: 1.2;
}

.menu-profile-role {
  font-size: 12px;
  color: rgba(255, 255, 255, 0.55);
  margin-top: 2px;
  font-weight: 500;
}

.user-menu-list {
  min-width: 240px;
  padding: 8px 0;
}

.user-menu-item {
  margin: 2px 8px;
  border-radius: 10px;
  transition: all 0.2s ease;
  min-height: 46px;

  &:hover {
    background: rgba(255, 255, 255, 0.08);

    .menu-icon-pill {
      background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
      color: white;
    }

    .menu-item-label {
      color: #ffffff;
    }
  }
}

.menu-item-icon {
  min-width: 40px;
}

.menu-icon-pill {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  background: rgba(255, 255, 255, 0.1);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #ffffff;
  transition: all 0.2s ease;
}

.menu-item-label {
  font-size: 14px;
  font-weight: 600;
  color: #ffffff;
  transition: color 0.2s ease;
}

.menu-logout-area {
  padding: 4px 0 8px;
}

.user-menu-logout {
  margin: 2px 8px;
  border-radius: 10px;
  transition: all 0.2s ease;
  min-height: 46px;

  &:hover {
    background: rgba(239, 68, 68, 0.15);

    .logout-pill {
      background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
      color: white;
    }

    .logout-label {
      color: #ffffff;
    }
  }
}

.logout-pill {
  color: #ffffff;
}

.logout-label {
  font-size: 14px;
  font-weight: 600;
  color: #ffffff;
  transition: color 0.2s ease;
}

// ── Page ─────────────────────────────────────────────────────────────────────
.main-page-container {
  background: #ffffff;
  min-height: calc(100vh - 68px);
}

.main-page {
  padding: 0;
}

// ── Responsive ────────────────────────────────────────────────────────────────
@media (max-width: 768px) {
  .main-toolbar {
    padding: 0 16px;
  }

  .header-actions {
    gap: 4px;
  }

  .register-btn {
    padding: 0 14px;
    font-size: 13px;
  }

  .user-btn {
    padding: 0 10px;
  }
}

@media (max-width: 480px) {
  .main-toolbar {
    padding: 0 12px;
  }

  .logo-brand-name {
    font-size: 16px;
  }

  .logo-tagline {
    display: none;
  }

  .header-btn {
    padding: 0 10px;
  }

  .register-btn span {
    display: none;
  }

  .register-btn {
    padding: 0 12px;
    min-width: 40px;
  }
}
</style>

<style>
.footer_menu a {
  color: white;
}

.user-menu {
  background: #1e293b !important;
}

.user-menu .q-item {
  color: #ffffff !important;
}

.user-menu .q-item:hover {
  background: rgba(255, 255, 255, 0.08) !important;
}

.user-menu .menu-item-label,
.user-menu .logout-label {
  color: #ffffff !important;
}

.user-menu .menu-icon-pill,
.user-menu .logout-pill {
  color: #ffffff !important;
  background: rgba(255, 255, 255, 0.1) !important;
}

.user-menu .q-separator {
  background: rgba(255, 255, 255, 0.08);
}

.user-menu .menu-logout-area {
  padding: 4px 0 8px;
  display: block;
}

.user-menu .user-menu-logout {
  margin: 2px 8px;
  border-radius: 10px;
  transition: all 0.2s ease;
  min-height: 46px;
}

.user-menu .user-menu-logout:hover {
  background: rgba(239, 68, 68, 0.15) !important;
}

.user-menu .user-menu-logout:hover .logout-pill {
  background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%) !important;
  color: white !important;
}

.user-menu .user-menu-logout:hover .logout-label {
  color: #ffffff !important;
}

.user-menu .logout-label {
  font-size: 14px;
  font-weight: 600;
  color: #ffffff !important;
}
</style>
