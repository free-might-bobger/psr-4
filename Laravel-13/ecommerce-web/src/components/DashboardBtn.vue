<template>
  <button class="dashboardBtn no-border">
    {{ content }}
    <div>{{ secondContent }}</div>
  </button>
</template>
<script setup>
const props = defineProps({
  content: String,
  secondContent: String
});
</script>

<style>
.dashboardBtn {
  background-color: #344c8a;
  color: white;
  border-radius: 20px;
  padding-left: 50px;
  padding-right: 50px;
  padding-top: 3px;
  padding-bottom: 3px;
  cursor: pointer;
}
</style>
