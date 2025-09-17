<template>
  <q-select
    dense
    v-model="localSelectedCity"
    :options="localCities"
    label="Select City"
    @filter="filterFn"
    hide-bottom-space
    use-input
    :rules="[(val) => validate(val) || 'Province is required.']"
  />
</template>

<script setup lang="ts">
import { computed, watch, ref } from 'vue';

const props = defineProps({
  cities: {
    type: Array<object>,
    default: [],
  },
  searchCities: {
    type: Array<object>,
    default: [],
  },
  selectedCity: {
    type: Object as () => Record<string, any> | null,
    default: null
  },
  required: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(['change']);
const localCities =  ref<object[]>([])
watch(props.cities, (newValue) => {
  localCities.value = newValue;
});

const localSelectedCity = computed({
  get: () => props.selectedCity,
  set: (v) => emit('change', v),
});

const filterFn = (val: string, update: any) => {
  setTimeout(() => {
    update(() => {
      const needle = val.toLowerCase();
      localCities.value = props.searchCities.filter(
        (v:object) => v.label.toLowerCase().indexOf(needle) > -1
      );
    });
  }, 100);
};

const validate = (val: object) => {
  if (val || props.required === false) {
    return true;
  }
  return false;
};
</script>
