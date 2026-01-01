<template>
  <q-layout view="lHh Lpr fff" class="no-shadow no-borders">
    <q-header class="main-header bg-white">
      <q-toolbar class="main-toolbar bg-white text-grey-8">
        <!-- Logo Section -->
        <router-link to="/" class="logo-link">
          <q-avatar size="48px" class="logo-avatar">
            <img src="/logo.png" alt="Logo" />
          </q-avatar>
          <div class="logo-text">
            <span class="logo-brand-name">Zentenpo</span>
            <span class="logo-tagline">Your Trusted Shop</span>
          </div>
        </router-link>

        <q-space />

        <!-- Action Buttons -->
        <div class="header-actions">
          <!-- Cart Button -->
          <q-btn to="/cart" icon="shopping_cart" flat round class="header-btn cart-btn" size="md">
            <q-badge v-if="countTotalItems > 0" color="negative" text-color="white" floating rounded class="cart-badge">
              {{ countTotalItems }}
            </q-badge>
            <q-tooltip>View Cart</q-tooltip>
          </q-btn>

          <!-- Login Button -->
          <q-btn v-if="!profile.token" to="/login" icon="login" flat round class="header-btn" size="md">
            <q-tooltip>Login</q-tooltip>
          </q-btn>

          <!-- User Menu -->
          <q-btn v-if="profile.token" icon="account_circle" flat round class="header-btn user-btn" size="md">
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
            <q-tooltip>Account Menu</q-tooltip>
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
    <q-footer class="main-footer bg-white">
      <div class="footer-content">
        <div class="footer-section">
          <!-- Brand Section -->
          <div class="footer-brand">
            <div class="footer-logo-section">
              <q-avatar size="40px" class="footer-logo">
                <img src="/logo.png" alt="Logo" />
              </q-avatar>
              <div class="footer-brand-text">
                <div class="footer-brand-name">Zentenpo</div>
                <div class="footer-brand-tagline">Your Trusted Online Shop</div>
              </div>
            </div>
            <p class="footer-description">
              We provide quality products with excellent customer service.
              Shop with confidence and enjoy fast, reliable delivery.
            </p>
          </div>

          <!-- Quick Links -->
          <div class="footer-links">
            <div class="footer-link-group">
              <div class="footer-link-title">Quick Links</div>
              <router-link to="/" class="footer-link">Home</router-link>
              <router-link to="/cart" class="footer-link">Shopping Cart</router-link>
              <router-link to="/login" class="footer-link" v-if="!profile.token">Login</router-link>
            </div>

          </div>

          <!-- Contact Info -->
          <div class="footer-contact">
            <div class="footer-link-title">Contact Us</div>
            <div class="footer-contact-item">
              <q-icon name="phone" size="sm" class="q-mr-sm" />
              <span>+63 XXX XXX XXXX</span>
            </div>
            <div class="footer-contact-item">
              <q-icon name="email" size="sm" class="q-mr-sm" />
              <span>support@zentenpo.com</span>
            </div>
            <div class="footer-contact-item">
              <q-icon name="location_on" size="sm" class="q-mr-sm" />
              <span>Philippines</span>
            </div>
          </div>
        </div>

        <!-- Footer Bottom -->
        <q-separator class="footer-separator" />
        <div class="footer-bottom">
          <div class="footer-copyright">
            <q-icon name="copyright" size="sm" class="q-mr-xs" />
            <span>2024 Zentenpo. All rights reserved.</span>
          </div>
          <div class="footer-social">
            <q-btn flat round icon="facebook" size="sm" class="social-btn" />
            <q-btn flat round icon="twitter" size="sm" class="social-btn" />
            <q-btn flat round icon="instagram" size="sm" class="social-btn" />
          </div>
        </div>
      </div>
    </q-footer>
  </q-layout>
</template>

<script setup lang="ts">
import { useUserStore } from 'src/stores/user';
import { storeToRefs } from 'pinia';
import { useUserCartStore } from 'src/stores/userCart';
import { logout } from 'src/boot/axios-call';
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

.logo-text {
  display: flex;
  flex-direction: column;
}

.logo-brand-name {
  font-size: 20px;
  font-weight: 700;
  color: #1a1a1a;
  line-height: 1.2;
  letter-spacing: -0.5px;
}

.logo-tagline {
  font-size: 11px;
  color: #666;
  line-height: 1;
  margin-top: 2px;
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
  gap: 8px;
}

.header-btn {
  position: relative;
  transition: all 0.3s ease;
  margin: 0 4px;

  &:hover {
    background-color: #f5f5f5;
    transform: scale(1.1);
  }

  &:active {
    transform: scale(0.95);
  }
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
  top: 4px;
  right: 4px;
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

.main-footer {
  border-top: 1px solid #e0e0e0;
  box-shadow: 0 -2px 4px rgba(0, 0, 0, 0.05);
}

.footer-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 40px 16px 24px;
}

.footer-section {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr;
  gap: 40px;
  margin-bottom: 32px;
}

.footer-brand {
  .footer-logo-section {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
  }

  .footer-logo {
    border: 2px solid #f5f5f5;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }

  .footer-brand-text {
    display: flex;
    flex-direction: column;
  }

  .footer-brand-name {
    font-size: 20px;
    font-weight: 700;
    color: #1a1a1a;
    line-height: 1.2;
  }

  .footer-brand-tagline {
    font-size: 12px;
    color: #666;
    line-height: 1;
    margin-top: 2px;
  }

  .footer-description {
    color: #666;
    font-size: 14px;
    line-height: 1.6;
    margin: 0;
  }
}

.footer-links {
  display: flex;
  flex-direction: column;
}

.footer-link-group {
  display: flex;
  flex-direction: column;
}

.footer-link-title {
  font-size: 16px;
  font-weight: 600;
  color: #1a1a1a;
  margin-bottom: 16px;
}

.footer-link {
  color: #666;
  text-decoration: none;
  font-size: 14px;
  padding: 8px 0;
  transition: all 0.2s ease;
  display: inline-block;

  &:hover {
    color: var(--q-primary);
    transform: translateX(4px);
  }
}

.footer-contact {
  display: flex;
  flex-direction: column;
}

.footer-contact-item {
  display: flex;
  align-items: center;
  color: #666;
  font-size: 14px;
  padding: 8px 0;
  transition: color 0.2s ease;

  &:hover {
    color: var(--q-primary);
  }
}

.footer-separator {
  margin: 24px 0;
  background: #e0e0e0;
}

.footer-bottom {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 16px;
}

.footer-copyright {
  display: flex;
  align-items: center;
  color: #666;
  font-size: 14px;
}

.footer-social {
  display: flex;
  gap: 8px;
}

.social-btn {
  transition: all 0.3s ease;

  &:hover {
    background-color: #f5f5f5;
    transform: scale(1.1);
  }
}

@media (max-width: 900px) {
  .footer-section {
    grid-template-columns: 1fr 1fr;
    gap: 32px;
  }

  .footer-contact {
    grid-column: 1 / -1;
  }
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

  .logo-text {
    display: none;
  }

  .footer-content {
    padding: 32px 16px 20px;
  }

  .footer-section {
    grid-template-columns: 1fr;
    gap: 24px;
  }

  .footer-bottom {
    flex-direction: column;
    gap: 16px;
    text-align: center;
  }

  .footer-social {
    justify-content: center;
  }
}
</style>

<style>
.footer_menu a {
  color: white;
}
</style>
