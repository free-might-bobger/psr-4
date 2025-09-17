<template>
  <div class="q-px-sm">
    <p class="q-my-md" style="font-size: 22px">Store</p>
    <q-form
      @submit.prevent="onSubmit"
      @reset="onReset"
      class="q-gutter-sm"
      ref="myForm"
    >
      <q-input
        v-model="store.name"
        outlined
        dense
        label="Store Name"
        :rules="[(val) => (val && val.length > 0) || 'Name is required.']"
        hide-bottom-space
      />
      <q-input
        type="textarea"
        v-model="store.desc"
        outlined
        dense
        label="Description"
        :rules="[
          (val) => (val && val.length > 0) || 'Description is required.',
        ]"
      />

      <div class="flex justify-end">
        <q-btn
          label="Show Map"
          color="primary"
          @click="showGoogleMap = true"
          outline
          class="q-mr-sm"
        />
        <q-btn label="Update" type="submit" color="primary" outline/>
      </div>
    </q-form>
    <q-dialog v-model="showGoogleMap" persistent>
      <q-card>
        <q-bar class="bg-primary text-white">
          <div>Drag the red marker then update</div>
          <q-space />

          <q-btn dense flat icon="close" v-close-popup>
            <q-tooltip>Close</q-tooltip>
          </q-btn>
        </q-bar>

        <q-card-section>
          <GoogleMap
            :api-key="GOOGLE_MAP_API_KEY"
            :map-id="GOOGLE_MAP_ID"
            style="height: 350px; width: 500px"
            :center="{ lat: store.latitude, lng: store.longitude }"
            :zoom="15"
            :draggable="true"
            :clickable-icons="false"
          >
            <AdvancedMarker
              :options="{
                position: { lat: store.latitude, lng: store.longitude },
                gmpDraggable: true,
                title: 'MARKER',
              }"
              @drag="markerDrag"
            >
              <InfoWindow
                v-model="showInfoWindow"
                :options="{
                  position: { lat: store.latitude, lng: store.longitude },
                  headerContent: 'My Location',
                }"
              >
              </InfoWindow>
            </AdvancedMarker>
          </GoogleMap>
          <br />
          <div class="flex justify-end">
            <q-btn label="update" color="primary" @click="updateStoreMap" outline />
          </div>
        </q-card-section>
      </q-card>
    </q-dialog>
  </div>
</template>
<script setup lang="ts">
import { GOOGLE_MAP_API_KEY, GOOGLE_MAP_ID } from 'src/boot/constant';
import { GoogleMap, AdvancedMarker, InfoWindow } from 'vue3-google-map';
import { ref, onBeforeMount } from 'vue';
import { show, update } from 'src/boot/axios-call';
import { useRoute } from 'vue-router';
import type { QForm } from 'quasar';

const showGoogleMap = ref(false);
const route = useRoute();
const showInfoWindow = ref(true);

const myForm = ref<QForm | null>(null);

const onSubmit = async () => {
  myForm.value?.validate().then(async (success: any) => {
    if (success) {
      await update({
        entity: 'my-stores',
        optimus_id: route.params.id,
        data: {
          name: store.value.name,
          desc: store.value.desc
        }
      });
    }
  });
};

const onReset = () => {
  myForm.value?.resetValidation();
};

const store = ref({
  name: '',
  desc: '',
  latitude: 0,
  longitude: 0,
});
onBeforeMount(async () => {
  store.value = await show({
    entity: 'my-stores',
    optimus_id: Number(route.params.id),
  });
});

const markerDrag = (e: { latLng: google.maps.LatLng }) => {
  store.value.latitude = e.latLng.lat();
  store.value.longitude = e.latLng.lng();
};

const updateStoreMap = async () => {
  await update({
    entity: 'my-stores',
    optimus_id: route.params.id,
    data: {
      latitude: store.value.latitude,
      longitude: store.value.longitude
    },
  });
};
</script>
