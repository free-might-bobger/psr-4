<template>
  <q-select
    dense
    v-model="localSelectedProvince"
    :options="provinces"
    label="Select Province"
    @filter="filterFn"
    use-input
    hide-bottom-space
    :rules="[ (val) =>  validate(val) || 'Province is required.']"
  />
</template>

<script setup lang="ts">
import { useCommonStore } from 'src/stores/common';
import { storeToRefs } from 'pinia';
import { get } from 'boot/axios-call';
import { computed  } from 'vue';

const useCommon = useCommonStore();
const { provinces, searchProvinces } = storeToRefs(useCommon);

const props = defineProps({
  selectedProvince: {
    type: Object as () => Record<string, any> | null,
    default: null
  },
  required: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['change']);

const localSelectedProvince = computed({
  get: () => props.selectedProvince,
  set: (v: object) => emit('change', v),
})


const getProvinces = async () => {
  const result = await get(
    {
      message: 'Getting Provinces...',
      entity: 'provinces',
      query: {
        type: 'collection',
        orderBy: 'name:asc',
        limit: 100
      },
    },
    true
  );

 provinces.value = result.data.data
 searchProvinces.value = result.data.data
};

const filterFn = (val: object, update: (fn: () => void) => void)  => {
  setTimeout(() => {
    update(() => {
      const needle = val.toLowerCase();
      provinces.value = searchProvinces.value.filter(
        (v) => v.label.toLowerCase().indexOf(needle) > -1
      );
    });
  }, 100);
};

const validate = (val:object) => {
  if(val || props.required === false){
    return true
  }
  return false
}

getProvinces();

</script>
