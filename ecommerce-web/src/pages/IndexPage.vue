<template>
  <div style="margin: 5px">
    <div class="row">
      <div class="col-12">
        <q-input
          :model-value="searchString()"
          outlined
          readonly
          input-style="text-align: center"
        >
        <template v-slot:prepend>
          <q-btn dense flat icon="fa-regular fa-square-minus" @click="decreaseRadius" />
        </template>
          <template v-slot:append>
            <q-btn
              dense
              flat
              icon="fa-regular fa-square-plus"
              @click="increaseRadius"
            />
            <q-btn flat label="Search nearest store" @click="getNearestStore" />
          </template>
        </q-input>
      </div>
      <div class="col-12">
        <q-card class="q-pa-sm q-mt-sm" flat bordered v-for="store in stores" :key="store.id" @click="toPublicStore(store.optimus_id)">
          <q-card-section class="no-padding">
            <div class="row no-wrap items-center">
              <div class="col text-h6 ellipsis">{{  store.name }}</div>
              <div
                class="col-auto text-grey text-caption q-pt-sm row no-wrap items-center"
              >
                <q-icon name="place" />
                {{ store.distance }} KM
              </div>
            </div>
            <q-rating v-model="stars" :max="5" size="18px" />
          </q-card-section>
          <q-card-section >
            <div class="text-caption text-grey">
              {{ store.desc }}
            </div>
          </q-card-section>
        </q-card>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { get } from 'src/boot/axios-call';
import { ref } from 'vue';
import { MAX_RADIUS } from 'src/boot/constant';
import { useRouter } from 'vue-router';
import { useCommonStore } from 'src/stores/common';
import { storeToRefs } from 'pinia';

const router = useRouter();
const useCommon = useCommonStore();

const { lat, lng } = storeToRefs(useCommon);
const radius = ref(1);
const stars = ref(4);
const searchString = () => {
  return `${radius.value} KM`;
};
const decreaseRadius = () => {
  if (radius.value >= 2) {
    radius.value--;
    searchString();
  }
};

const toPublicStore = (optimusId: number) => {
  router.push({
    path: `/public_stores/${optimusId}`,
  });
}

const increaseRadius = () => {
  if (radius.value <= MAX_RADIUS) {
    radius.value++;
    searchString();
  }
};

const stores = ref([])
const getNearestStore = async () => {
  // showMap.value = true;
  // const position = await getLocation();
  // lat.value = position.coords.latitude;
  // lng.value = position.coords.longitude;

  lat.value = 10.339219239338211
  lng.value = 123.92509674167226

  const result = await get(
    {
      message: 'Searching nearest store in ' + searchString(),
      entity: 'public_stores',
      query: {
        orderBy: 'name:asc',
        columns: 'id,name',
        latitude: 10.339219239338211,
        longitude: 123.92509674167226,
        radius: radius.value,
      },
    },
    true
  );

  stores.value = result.data.data
};
</script>
