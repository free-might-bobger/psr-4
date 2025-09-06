<template>
  <q-list class="bg-primary text-white rounded-borders" bordered separator>
    <template v-for="(menu, i) in menus">
      <q-expansion-item
        expand-separator
        :label="menu.name"
        :key="i"
        v-if="menuAllChildren(menu)"
        :icon="menu.icon"
        expand-icon-class="text-white"
      >
        <template v-for="(subCat, subCatIndex) in menu.all_children">
          <q-expansion-item
            :header-inset-level="0.5"
            expand-separator
            :label="subCat.name"
            :key="subCatIndex"
            v-if="subCat.all_children.length > 0"
            v-show="hasAccessToMenu(subCat.id)"
            :icon="subCat.icon"
            exact
            class="text-white"
            expand-icon-class="text-white"
          >
            <template
              v-for="(furtherCat, furtherCatIndex) in subCat.all_children"
            >
              <q-expansion-item
                switch-toggle-side
                dense-toggle
                :label="furtherCat.name"
                :header-inset-level="1"
                :content-inset-level="1.2"
                :key="furtherCatIndex"
                v-if="furtherCat.all_children.length > 0"
                v-show="hasAccessToMenu(furtherCat.id)"
              >
              </q-expansion-item>
              <q-item
                clickable
                v-ripple
                v-else
                :key="'furtherCat' + furtherCatIndex"
                side
                :inset-level="1.7"
                :to="`${furtherCat.path}`"
                v-show="hasAccessToMenu(furtherCat.id)"
              >
                <q-item-section>{{ furtherCat.name }}</q-item-section>
              </q-item>
            </template>
          </q-expansion-item>
          <q-item
            clickable
            v-ripple
            v-else
            :key="'subCat' + subCatIndex"
            side
            :inset-level="0.5"
            v-show="hasAccessToMenu(subCat.id)"
            :to="`${subCat.path}`"
            :icon="subCat.icon"
            exact
          >
            <q-item-section>{{ subCat.name }}</q-item-section>
          </q-item>
        </template>
      </q-expansion-item>
      <template v-else>
        <q-item
          clickable
          v-ripple
          :key="'menu' + i"
          :to="`${menu.path}`"
          exact
          active-class="text-secondary"
        >
          <q-item-section>{{ menu.name }}</q-item-section>
        </q-item>
      </template>
    </template>
  </q-list>
</template>

<script setup lang="ts">
import { onMounted } from 'vue';
import { getUserMenus } from '../../boot/axios-call';
import { menuAllChildren, hasAccessToMenu } from '../../boot/common';
import { useUserStore } from '../../stores/user';
import { storeToRefs } from 'pinia';

const { menus } = storeToRefs(useUserStore());

onMounted(async () => {
  await getUserMenus()
});
</script>
