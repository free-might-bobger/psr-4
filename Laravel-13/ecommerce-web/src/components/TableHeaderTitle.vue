<template>
  <div>
    <br />
    <div class="row">
      <div class="col text-left">
        <span class="table-header-title">{{  headerTitle }}</span>
      </div>
      <div class="col-8">
        <q-input
          outlined
          dense
          debounce="300"
          v-model="localSearch"
          :placeholder="`Search ${placeHolderTitle}...`"
          class="full-width"
          clearable
        >
          <template v-slot:append>
            <q-icon name="search" />
          </template>
        </q-input>
      </div>
    </div>
    <br />
  </div>
</template>

<script lang="ts" setup>
import { ref, watch } from 'vue'
const localSearch = ref('')

defineProps({
    placeHolderTitle: String,
    headerTitle: String
});

const emit = defineEmits(['searchTable']);

watch(localSearch, (newLocalSearch) => {
    if(newLocalSearch){
        emit('searchTable', newLocalSearch)
    }
})

</script>
