<template>
  <q-select
    dense
    clearable
    v-model="selectedBrgy"
    :options="brgys"
    label="Select Barangay"
    @filter="filterFn"
    hide-bottom-space
    use-input
  />
</template>

<script setup lang="ts">
import { watch, onMounted, ref } from 'vue';
import { get } from 'boot/axios-call';
import { storeToRefs } from 'pinia';

const props = defineProps({
  brgys: {
    type: Array<object>,
    default: [],
  },
  searchBrgys: {
    type: Array<object>,
    default: [],
  },
  selectedCity: {
    type: Object as () => Record<string, any> | null,
    default: null,
  },
  selectedBrgy: {
    type: Object as () => Record<string, any> | null,
    default: null,
  },
  required: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(['change']);
const localBrgys = ref<object[]>([]);
watch(props.brgys, (newValue) => {
  localBrgys.value = newValue;
});

const getBrgys = async () => {
  let filters = null;
  if (props.selectedCity) {
    filters = 'citymunCode:' + selectedCity.value.citymunCode;
    await get(
      {
        entity: 'brgys',
        query: {
          filters: filters,
          type: 'collection',
        },
        message: '',
      },
      {
        collection: brgys,
        searchCollection: searchBrgys,
      },
      false
    );
  } else {
    brgys.value = [];
    searchBrgys.value = [];
  }
};

const filterFn = (val, update, abort) => {
  // call abort() at any time if you can't retrieve data somehow
  setTimeout(() => {
    update(() => {
      const needle = val.toLowerCase();
      brgys.value = searchBrgys.value.filter(
        (v) => v.label.toLowerCase().indexOf(needle) > -1
      );
    });
  }, 100);
};

onMounted(async () => {
  await getBrgy();
});

watch(selectedCity, (newValue, oldValue) => {
  selectedBrgy.value = null;
  brgys.value = [];
  searchBrgys.value = [];
  if (selectedCity.value) {
    getBrgy();
  }
});
</script>
