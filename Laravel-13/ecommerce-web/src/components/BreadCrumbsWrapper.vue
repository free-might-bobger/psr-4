<template>

  <q-breadcrumbs>
    <q-breadcrumbs-el to="/">
      <q-icon name="home" size="sm"></q-icon>
    </q-breadcrumbs-el>
    <q-breadcrumbs-el v-for="(breadCrumb, index) in breadCrumbs" :key="index" :to="breadCrumb.path"
      :class="{ 'breadcrumb-last': breadCrumbs && index === breadCrumbs.length - 1 }">
      {{ breadCrumb.name }}
    </q-breadcrumbs-el>
  </q-breadcrumbs>
</template>

<script lang="ts" setup>

defineProps({
  breadCrumbs: Array<{ name: string, path: string }>
})

</script>

<style scoped lang="scss">
.breadcrumb-last {
  max-width: 200px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  display: inline-block;
}

@media (max-width: 600px) {
  .breadcrumb-last {
    max-width: 120px;
  }
}
</style>
