<template>
  <q-layout view="lHh Lpr fff" class="no-shadow no-borders">
    <q-header
      style="border-bottom: 1px solid #d3d3d3; height: 55px"
      class="bg-white"
    >
      <q-toolbar class="bg-white text-grey-8">
        <router-link to="/">
          <q-avatar size="45px">
            <img src="/logo.png" />
          </q-avatar>
        </router-link>
        <q-space />
        <q-btn to="/cart" icon="fas fa-shopping-cart" flat>
          <q-badge
            color="red"
            text-color="white"
            floating
            v-if="countTotalItems > 0"
          >
            {{ countTotalItems }}
          </q-badge>
        </q-btn>
        <q-btn
          to="/login"
          icon="fa-solid fa-user-lock"
          flat
          v-if="!profile.token"
        ></q-btn>
        <q-btn icon="fa-solid fa-user-gear" v-if="profile.token" flat>
          <q-menu>
            <q-list padding style="width: 200px">
              <q-item clickable v-for="menu in profile.userMenu" :key="menu.id" :to="menu.path">
                <q-item-section avatar>
                  <q-avatar :icon="menu.icon" />
                </q-item-section>
                <q-item-section>
                  <q-item-label>{{ menu.name }}</q-item-label>
                </q-item-section>
              </q-item>
              <q-item>
                <q-item-section avatar>
                  <q-avatar icon="fa-solid fa-power-off" />
                </q-item-section>
                <q-item-section @click="logout">
                  <q-item-label> Logout </q-item-label>
                </q-item-section>
              </q-item>
            </q-list>
          </q-menu>
        </q-btn>
      </q-toolbar>
    </q-header>

    <q-page-container>
      <q-page>
        <router-view style="margin-top: 5px" />
      </q-page>
    </q-page-container>

    <q-footer class="bg-white text-grey-6">
      <div class="row q-ma-sm">
        <div class="col-12">
          <br />
          <p class="text-center">© 2023 Rocee Shop. All rights reserved.</p>
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

<style>
.footer_menu a {
  color: white;
}
</style>
