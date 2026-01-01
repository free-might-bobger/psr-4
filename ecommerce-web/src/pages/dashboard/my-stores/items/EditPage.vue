<template>
  <div class="q-pa-sm">
    <div>
      <q-btn
        label="Items"
        outline
        class="q-my-sm"
        icon="fa-solid fa-angles-left"
        color="primary"
        @click="router.back()"
      />
    </div>
    <q-form
      @submit.prevent="onSubmit"
      @reset="onReset"
      class="q-gutter-sm"
      ref="myForm"
    >
      <q-input
        v-model="item.name"
        outlined
        dense
        label="Item Name"
        :rules="[(val) => (val && val.length > 0) || 'Name is required.']"
        hide-bottom-space
      />
      <q-select
        dense
        v-model="item.category"
        :options="categories"
        label="Select Category"
        hide-bottom-space
        use-input
        outlined
        clearable
      />
      <q-input
        type="textarea"
        v-model="item.description"
        outlined
        dense
        label="Description"
        :rules="[
          (val) => (val && val.length > 0) || 'Description is required.',
        ]"
      />
      <div>
        <q-uploader
          accept=".png, .jpg, image/*"
          label="Upload Images"
          :multiple="true"
          @added="addImage"
          hide-upload-btn
          ref="uploader"
          @removed="removeImage"
          class="full-width"
          max-files="6"
          color="secondary"
          flat
        >
          <template v-slot:list="scope">
            <q-list separator>
              <q-item v-for="file in scope.files" :key="file.name">
                <q-item-section>
                  <q-item-label class="full-width ellipsis">
                    {{ file.name }}
                  </q-item-label>

                  <q-item-label caption
                    >Status: {{ file.__status }}</q-item-label
                  >

                  <q-item-label caption>
                    {{ file.__sizeLabel }}
                    <!-- / {{ file.__progressLabel }} -->
                  </q-item-label>
                </q-item-section>

                <q-item-section v-if="file.__img" thumbnail>
                  <img :src="file.__img.src" />
                </q-item-section>

                <q-item-section top side>
                  <q-item-label caption>
                    <q-radio
                      v-model="primaryImageName"
                      :val="file.name"
                      style="margin-top: 5px"
                    />
                  </q-item-label>
                  <q-item-label caption>Primary</q-item-label>
                </q-item-section>
                <q-item-section top side>
                  <q-item-label caption>
                    <q-btn
                      class="q-ma-sm"
                      color="negative"
                      flat
                      dense
                      round
                      icon="far fa-trash-alt"
                      @click="scope.removeFile(file)"
                    />
                  </q-item-label>
                  <q-item-label caption>Remove</q-item-label>
                </q-item-section>
              </q-item>
            </q-list>
          </template>
        </q-uploader>
      </div>

      <div class="flex justify-end">
        <q-btn label="Update" type="submit" color="primary" outline />
      </div>
    </q-form>
  </div>
</template>
<script setup lang="ts">
import { ref, onBeforeMount } from 'vue';
import { show, update, get, saveEntityWithImages } from 'src/boot/axios-call';
import { getFileFromUrl, urltoFile, ensureFileExtension } from 'boot/common';
import { useRoute, useRouter } from 'vue-router';
import type { QForm } from 'quasar';
import { ItemInterface } from 'boot/interfaces';

const route = useRoute();
const router = useRouter();
const myForm = ref<QForm | null>(null);

const onReset = () => {
  myForm.value?.resetValidation();
};

const item = ref<ItemInterface>({
  name: '',
  description: '',
  images: [],
  category: undefined,
  unit: undefined,
});
onBeforeMount(async () => {
  await getItem();
  loadItemImage();
  listingApi();
});

const getItem = async () => {
  item.value = await show({
    entity: 'items',
    optimus_id: Number(route.params.itemId),
    query: {
      filters: `store_id:${route.params.id}`,
      with: 'category',
    },
  });
};

const categories = ref([]);

const listingApi = async () => {
  const result = await get(
    {
      entity: 'listing_api',
      query: {
        listingApi: 'categories',
      },
    },
    false
  );
  categories.value = result.data.data.categories;
};

const selectedFiles = ref([]);
const primaryImageName = ref('');
const addImage = (files) => {
  for (var i = 0; i <= files.length; i++) {
    if (files[i] != undefined) {
      selectedFiles.value.push(files[i]);
    }
  }
  let selectedFileArray = JSON.parse(JSON.stringify(selectedFiles.value));
  if (selectedFileArray.length == 1) {
    primaryImageName.value = files[0].name;
  }
};

const deletedFiles = ref([]);
const removeImage = (file: Array<{ id: number; name: string }>) => {
  let newFiles = selectedFiles.value.filter(function (v: { name: string }) {
    if (v !== undefined) {
      return v.name !== file[0].name;
    }
  });
  selectedFiles.value = newFiles;
  if (file[0].id) {
    deletedFiles.value.push(file[0].id);
  }
};

const uploader = ref('uploader');
const loadItemImage = () => {
  item.value.images?.forEach(async (file) => {
    let fileFromUrl = await getFileFromUrl(file.path_url, file.name);
    if (file.is_primary == 1) {
      primaryImageName.value = file.name;
    }
    fileFromUrl = Object.assign(fileFromUrl, {
      __img: {
        src: file.url,
      },
      id: file.id,
    });

    uploader.value.addFiles([fileFromUrl]);
  });
};

const onSubmit = async () => {
  myForm.value?.validate().then(async (success: any) => {
    if (success) {
      const fd = new FormData();

      for (const selectedFile of selectedFiles.value) {
        if (!selectedFile.id) {
          // Append to FormData
          fd.append('files[]', selectedFile);
        }
      }

      for (const deletedFile of deletedFiles.value) {
        fd.append('deletedFiles[]', deletedFile);
      }

      fd.append('primaryImageName', primaryImageName.value);
      fd.append('description', item.value.description);
      fd.append('name', item.value.name);
      fd.append('store_id', route.params.id);
      fd.append('category_id', item.value.category?.id);

      saveEntityWithImages({
        store_id: Number(route.params.id),
        entity: 'item-update',
        optimus_id: Number(route.params.itemId),
        fd: fd,
      });
    }
  });
};

const filterFn = () => {
};
</script>
