<template>
  <div
    class="flex items-center"
    style="
      height: 40px;
      border: 1px solid #c2c2c2;
      border-radius: 5px;
      padding: 8px;
    "
  >
    <q-breadcrumbs>
      <q-breadcrumbs-el to="/">
        <q-icon name="home" size="sm"></q-icon>
      </q-breadcrumbs-el>
      <q-breadcrumbs-el v-for="(breadCrumb, index) in breadCrumbs" :key="index" :to="breadCrumb.path">{{ breadCrumb.name }} </q-breadcrumbs-el>
    </q-breadcrumbs>
  </div>
</template>

<script lang="ts" setup>

defineProps({
    breadCrumbs: Array<{ name: string, path: string }>
})

</script>
