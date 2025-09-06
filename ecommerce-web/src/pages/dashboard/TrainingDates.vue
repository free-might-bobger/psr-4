<template>
    <div class="q-ml-sm text-subtitle1">
      <q-card class="my-card" flat bordered>
        <q-card-section :horizontal="$q.platform.is.desktop">
          <q-card-section class="q-pt-xs">
            <div class="text-overline">TRAINEES NAME: {{ profile.name }}</div>
            <div class="text-h5 q-mt-sm q-mb-xs">
              Training Date: January 1-18, 2024
            </div>
            <div class="text-grey">
              <div class="flex justify-start">
                <div>COURSE/PROGRAM:</div>
                <div class="q-ml-sm">COURSE/PROGRAM</div>
              </div>
              <div class="flex justify-start">
                <div>DURATION:</div>
                <div class="q-ml-sm">144 HOURS/DAYS</div>
              </div>
              <div class="flex justify-start">
                <div>TRAINER'S NAME:</div>
                <div class="q-ml-sm">144 HOURS/DAYS</div>
              </div>
              <div class="flex justify-start">
                <div>BATCH:</div>
                <div class="q-ml-sm">144 HOURS/DAYS</div>
              </div>
            </div>
          </q-card-section>
          
          <q-card-section class="col-5 flex flex-center gt-xs">
            <q-img
              class="rounded-borders"
              :src="getPrimaryImageUrl()"
            />
            <q-btn outline style="height: 10px;" color="primary" glossy @click="toggleShow" class="q-mt-md q-ml-sm">Change</q-btn>
          </q-card-section>
        </q-card-section>
  
        <q-separator />
      </q-card>
      <my-upload field="img" @crop-success="cropSuccess" 
         v-model="showUpload" :width="100" :height="100" :params="params"
        :headers="headers" img-format="png" :langExt="langExt" noSquare></my-upload>
    </div>
  </template>
  <script setup lang="ts">
  
  import { useUserStore } from 'src/stores/user';
  import { storeToRefs } from 'pinia';
  import myUpload from 'vue-image-crop-upload';
  import { ref } from 'vue'
  import { urltoFile } from 'boot/common'
  import { uploadImagesToServer } from 'boot/axios-call'
  import { getPrimaryImageUrl } from 'boot/common';
  
  const { profile } = storeToRefs(useUserStore());
  const showUpload = ref(false)
  const toggleShow = () => {
    showUpload.value = !showUpload.value;
  }
  
  const params = {
    token: '123456798',
    name: 'avatar'
  }
  const headers = {
    smail: '*_~'
  }
  
  
  const langExt = {
    hint: 'Click or drag the file here to upload',
    loading: 'Uploading…',
    noSupported: 'Browser is not supported, please use IE10+ or other browsers',
    success: 'Upload success',
    fail: 'Upload failed',
    preview: 'Preview',
    btn: {
      off: 'Cancel',
      close: 'Close',
      back: 'Back',
      save: 'Save'
    },
    error: {
      onlyImg: 'Image only',
      outOfSize: 'Image exceeds size limit: ',
      lowestPx: 'Image\'s size is too low. Expected at least: '
    }
  }
  const imgDataUrl = ref('') // the datebase64 url of created image
  const cropSuccess = async (img: string) => {
  
  imgDataUrl.value = img;
  
  urltoFile(imgDataUrl.value, 'profile.jpg', 'image/png')
    .then(async function (file):Promise<void> {
      const fd = new FormData();
      fd.append('images[]', file)
      fd.append('isPrimary', 'true');
      await uploadImagesToServer({
          entity: 'User',
          optimus_id: 5,
          fd: fd
        });
    
    });
  }
  
  </script>
  