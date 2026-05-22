<template>
  <div class="item-edit-container">
    <!-- Header Section -->
    <div class="item-edit-header-section q-mb-lg">
      <div class="row items-center">
        <div class="col">
          <div class="text-h4 text-weight-bold">
            <q-icon name="inventory_2" color="primary" class="q-mr-sm" />
            Edit Item
          </div>
          <div class="text-body2 text-grey-7 q-mt-xs">
            Update item details, category, and images
          </div>
        </div>
        <q-btn outline color="primary" icon="arrow_back" label="Back" @click="router.back()" />
      </div>
    </div>

    <!-- Edit Form Card -->
    <q-card flat bordered class="item-edit-card">
      <q-card-section class="item-edit-header">
        <div class="row items-center justify-between">
          <div class="col-auto">
            <div class="text-h6 text-weight-bold q-mb-xs">
              <q-icon name="edit" color="primary" class="q-mr-sm" />
              Item Information
            </div>
            <div class="text-body2 text-grey-7">
              Update the fields below and save changes
            </div>
          </div>
        </div>
      </q-card-section>

      <q-separator />

      <q-card-section class="item-edit-content">
        <q-form @submit.prevent="onSubmit" @reset="onReset" class="q-gutter-md" ref="myForm">
          <div class="info-group">
            <div class="text-subtitle2 text-weight-bold text-grey-8 q-mb-md">
              <q-icon name="info" size="sm" class="q-mr-xs" />
              Item Details
            </div>

            <div class="form-grid">
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
            </div>

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
          </div>

          <div class="info-group q-mt-lg">
            <div class="text-subtitle2 text-weight-bold text-grey-8 q-mb-md">
              <q-icon name="photo_library" size="sm" class="q-mr-xs" />
              Item Images
            </div>

            <!-- Existing Images Grid -->
            <div v-if="item.images && item.images.length > 0" class="images-grid q-mb-md">
              <div v-for="image in item.images" :key="image.id" class="image-card">
                <q-img
                  :src="image.path_thumbnail || image.path_url"
                  class="image-preview"
                  fit="cover"
                >
                  <div v-if="image.is_primary" class="absolute-top-right">
                    <q-badge color="primary" label="Primary" />
                  </div>
                </q-img>
                <div class="image-actions">
                  <q-btn
                    v-if="!image.is_primary"
                    unelevated
                    dense
                    color="primary"
                    icon="star"
                    label="Set Primary"
                    size="sm"
                    @click="setPrimaryImage(image)"
                  />
                  <q-btn
                    unelevated
                    dense
                    color="negative"
                    icon="delete"
                    size="sm"
                    @click="deleteImage(image)"
                  />
                </div>
              </div>
            </div>

            <!-- Upload New Images -->
            <div class="upload-section">
              <q-file
                v-model="newImages"
                label="Add new images"
                outlined
                dense
                multiple
                accept="image/*"
                class="full-width"
              >
                <template v-slot:prepend>
                  <q-icon name="cloud_upload" />
                </template>
              </q-file>

              <div v-if="previewNewImages.length > 0" class="preview-grid q-mt-md">
                <div v-for="(file, index) in previewNewImages" :key="index" class="preview-item">
                  <q-img
                    :src="previewUrl(file)"
                    class="preview-image"
                    fit="cover"
                  />
                  <q-btn
                    unelevated
                    dense
                    round
                    color="negative"
                    icon="close"
                    size="xs"
                    class="absolute-top-right"
                    @click="removeNewImage(index)"
                  />
                </div>
              </div>
            </div>
          </div>

          <div class="form-actions">
            <q-btn
              label="Update"
              type="submit"
              color="primary"
              unelevated
              icon="save"
              :loading="isSubmitting"
            />
            <q-btn label="Cancel" outline color="grey-8" icon="cancel" @click="router.back()" class="q-ml-sm"/>
          </div>
        </q-form>
      </q-card-section>
    </q-card>
  </div>
</template>
<script setup lang="ts">
import { ref, onBeforeMount, computed } from 'vue';
import { show, get } from 'src/boot/axios-call';
import { axios } from 'src/boot/axios';
import { useRoute, useRouter } from 'vue-router';
import type { QForm } from 'quasar';
import { Notify, Loading } from 'quasar';

interface ItemImage {
  id: number;
  name: string;
  path: string;
  path_thumbnail?: string;
  path_url?: string;
  is_primary: boolean;
}

interface Category {
  id: number;
  name: string;
}

interface Item {
  id: number;
  optimus_id: number;
  name: string;
  description: string;
  images: ItemImage[];
  category?: Category;
  unit?: any;
}

interface ApiResponse<T> {
  data: T;
}

const route = useRoute();
const router = useRouter();
const myForm = ref<QForm | null>(null);
const isSubmitting = ref(false);

const onReset = () => {
    myForm.value?.resetValidation();
};

const item = ref<Item>({
    id: 0,
    optimus_id: 0,
    name: '',
    description: '',
    images: [],
    category: undefined,
    unit: undefined,
});

const newImages = ref<File[]>([]);
const previewNewImages = computed(() => newImages.value);

const previewUrl = (file: File) => {
    return URL.createObjectURL(file);
};

onBeforeMount(async () => {
    await getItem();
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

const categories = ref<Category[]>([]);

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
    if (result && typeof result === 'object' && 'data' in result) {
        const apiResponse = result as ApiResponse<{ categories: Category[] }>;
        if (apiResponse.data) {
            categories.value = apiResponse.data.categories;
        }
    }
};

const removeNewImage = (index: number) => {
    newImages.value.splice(index, 1);
};

const setPrimaryImage = async (image: ItemImage) => {
    try {
        Loading.show({ message: 'Setting primary image...' });
        await axios.post('update-primary-image', {
            entity: 'Item',
            id: item.value.id,
            primaryName: image.name,
        });
        await getItem();
        Loading.hide();
        Notify.create({
            position: 'bottom',
            type: 'positive',
            message: 'Primary image set successfully.',
        });
    } catch (e: unknown) {
        Loading.hide();
        const msg =
            (e as { response?: { data?: { message?: string } } })?.response?.data?.message ||
            'Failed to set primary image.';
        Notify.create({ position: 'bottom', type: 'negative', message: msg });
    }
};

const deleteImage = async (image: ItemImage) => {
    try {
        Loading.show({ message: 'Deleting image...' });
        await axios.post('images', {
            entity: 'items',
            id: item.value.id,
            deletedFiles: image.id,
        });
        await getItem();
        Loading.hide();
        Notify.create({
            position: 'bottom',
            type: 'positive',
            message: 'Image deleted successfully.',
        });
    } catch (e: unknown) {
        Loading.hide();
        const msg =
            (e as { response?: { data?: { message?: string } } })?.response?.data?.message ||
            'Failed to delete image.';
        Notify.create({ position: 'bottom', type: 'negative', message: msg });
    }
};

const onSubmit = async () => {
    myForm.value?.validate().then(async (success: boolean) => {
        if (!success) {
            return;
        }
        isSubmitting.value = true;
        Loading.show({ message: 'Updating item...' });
        try {
            const formData = new FormData();
            formData.append('store_id', route.params.id as string);
            formData.append('name', item.value.name);
            formData.append('description', item.value.description);
            if (item.value.category) {
                formData.append('category_id', String(item.value.category.id));
            }
            if (newImages.value.length > 0) {
                newImages.value.forEach((file) => {
                    formData.append('images[]', file);
                });
            }
            await axios.post(
                `item-update/${item.value.optimus_id}`,
                formData,
                {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    },
                }
            );
            Loading.hide();
            Notify.create({
                position: 'bottom',
                type: 'positive',
                message: 'Item updated successfully.',
            });
            await getItem();
            newImages.value = [];
        } catch (e: unknown) {
            Loading.hide();
            const msg =
                (e as { response?: { data?: { message?: string } } })?.response?.data?.message ||
                'Failed to update item.';
            Notify.create({ position: 'bottom', type: 'negative', message: msg });
        } finally {
            isSubmitting.value = false;
        }
    });
};
</script>

<style scoped lang="scss">
.item-edit-container {
  padding: 24px;
  max-width: 1200px;
  margin: 0 auto;
}

.item-edit-header-section {
  padding: 16px 0;
}

.item-edit-card {
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.item-edit-header {
  background: linear-gradient(135deg, #f5f7fa 0%, #ffffff 100%);
  padding: 24px;
}

.item-edit-content {
  padding: 24px;
}

.info-group {
  background: #f8f9fa;
  border-radius: 8px;
  padding: 20px;
}

.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
  margin-bottom: 16px;
}

.images-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
  gap: 16px;
}

.image-card {
  position: relative;
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  overflow: hidden;
}

.image-preview {
  width: 100%;
  height: 150px;
}

.image-actions {
  display: flex;
  gap: 8px;
  padding: 8px;
  justify-content: space-between;
}

.upload-section {
  border: 2px dashed #e0e0e0;
  border-radius: 8px;
  padding: 16px;
}

.preview-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
  gap: 12px;
}

.preview-item {
  position: relative;
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  overflow: hidden;
}

.preview-image {
  width: 100%;
  height: 100px;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
}

.absolute-top-right {
  position: absolute;
  top: 4px;
  right: 4px;
}

@media (max-width: 768px) {
  .item-edit-container {
    padding: 16px;
  }

  .item-edit-header {
    padding: 16px;
  }

  .item-edit-content {
    padding: 16px;
  }

  .info-group {
    padding: 16px;
  }

  .form-grid {
    grid-template-columns: 1fr;
  }

  .form-actions {
    flex-direction: column;
    align-items: stretch;
  }
}
</style>
