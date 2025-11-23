<template>
  
    <q-breadcrumbs>
      <q-breadcrumbs-el to="/">
        <q-icon name="home" size="sm"></q-icon>
      </q-breadcrumbs-el>
      <q-breadcrumbs-el v-for="(breadCrumb, index) in breadCrumbs" :key="index" :to="breadCrumb.path">{{ breadCrumb.name }} </q-breadcrumbs-el>
    </q-breadcrumbs>
</template>

<script lang="ts" setup>

defineProps({
    breadCrumbs: Array<{ name: string, path: string }>
})

</script>
