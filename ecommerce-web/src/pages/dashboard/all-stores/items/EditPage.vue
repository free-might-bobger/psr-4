<template>
  <div class="edit-page">
    <div class="edit-container">
      <!-- Header -->
      <div class="page-header">
        <q-btn flat round icon="arrow_back" @click="router.back()" class="back-btn" />
        <h1 class="page-title">Edit Item</h1>
      </div>

      <!-- Form -->
      <div class="form-card">
        <q-form @submit.prevent="onSubmit" @reset="onReset" ref="myForm">
          <!-- Item Details Section -->
          <div class="form-section">
            <div class="section-title">Item Details</div>

            <div class="form-row">
              <div class="form-field">
                <label class="field-label">Item Name</label>
                <q-input
                  v-model="item.name"
                  outlined
                  dense
                  :rules="[(val) => (val && val.length > 0) || 'Name is required.']"
                  hide-bottom-space
                />
              </div>
              <div class="form-field">
                <label class="field-label">Category</label>
                <q-select
                  dense
                  v-model="item.category"
                  :options="categories"
                  hide-bottom-space
                  use-input
                  outlined
                  clearable
                />
              </div>
            </div>

            <div class="form-field">
              <label class="field-label">Description</label>
              <div class="wysiwyg-editor">
                <div class="editor-toolbar">
                  <q-btn flat dense size="sm" @click="execCommand('bold')" icon="format_bold">
                    <q-tooltip>Bold</q-tooltip>
                  </q-btn>
                  <q-btn flat dense size="sm" @click="execCommand('italic')" icon="format_italic">
                    <q-tooltip>Italic</q-tooltip>
                  </q-btn>
                  <q-btn flat dense size="sm" @click="execCommand('underline')" icon="format_underlined">
                    <q-tooltip>Underline</q-tooltip>
                  </q-btn>
                  <q-btn flat dense size="sm" @click="execCommand('insertUnorderedList')" icon="format_list_bulleted">
                    <q-tooltip>Bullet List</q-tooltip>
                  </q-btn>
                  <q-btn flat dense size="sm" @click="execCommand('insertOrderedList')" icon="format_list_numbered">
                    <q-tooltip>Numbered List</q-tooltip>
                  </q-btn>
                </div>
                <div
                  class="editor-content"
                  contenteditable="true"
                  @input="onEditorInput"
                  ref="editorRef"
                  data-placeholder="Enter item description..."
                ></div>
              </div>
              <div v-if="!item.description" class="field-error">Description is required.</div>
            </div>
          </div>

          <!-- Images Section -->
          <div class="form-section">
            <div class="section-title">Item Images</div>

            <!-- Existing Images -->
            <div v-if="item.images && item.images.length > 0" class="images-grid">
              <div v-for="image in item.images" :key="image.id" class="image-item">
                <div class="image-wrapper">
                  <img :src="image.path_thumbnail || image.path_url" class="image-preview" />
                  <q-badge v-if="image.is_primary" color="primary" class="primary-badge">Primary</q-badge>
                </div>
                <div class="image-actions">
                  <q-btn
                    v-if="!image.is_primary"
                    flat
                    dense
                    color="primary"
                    icon="star"
                    size="sm"
                    @click="setPrimaryImage(image)"
                  >
                    <q-tooltip>Set as Primary</q-tooltip>
                  </q-btn>
                  <q-btn
                    flat
                    dense
                    color="negative"
                    icon="delete"
                    size="sm"
                    @click="deleteImage(image)"
                  >
                    <q-tooltip>Delete</q-tooltip>
                  </q-btn>
                </div>
              </div>
            </div>

            <!-- Upload New Images -->
            <div class="upload-area">
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

              <div v-if="previewNewImages.length > 0" class="preview-grid">
                <div v-for="(file, index) in previewNewImages" :key="index" class="preview-item">
                  <img :src="previewUrl(file)" class="preview-image" />
                  <q-btn
                    flat
                    dense
                    round
                    color="negative"
                    icon="close"
                    size="xs"
                    class="remove-btn"
                    @click="removeNewImage(index)"
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="form-actions">
            <q-btn
              label="Update"
              type="submit"
              color="primary"
              unelevated
              :loading="isSubmitting"
              class="submit-btn"
            />
            <q-btn label="Cancel" outline color="grey-7" @click="router.back()" class="cancel-btn" />
          </div>
        </q-form>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
import { ref, onBeforeMount, computed, watch, nextTick } from 'vue';
import { show, get, deleteEntity } from 'src/boot/axios-call';
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
const editorRef = ref<HTMLElement | null>(null);

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

// WYSIWYG Editor Functions
const execCommand = (command: string) => {
    document.execCommand(command, false, undefined);
    editorRef.value?.focus();
};

const onEditorInput = () => {
    if (editorRef.value) {
        item.value.description = editorRef.value.innerHTML;
    }
};

// Watch for item changes to update editor content
watch(() => item.value.description, async (newDescription) => {
    if (editorRef.value && newDescription && editorRef.value.innerHTML !== newDescription) {
        await nextTick();
        editorRef.value.innerHTML = newDescription;
    }
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
        await deleteEntity({
            entity: 'images',
            optimus_id: image.optimus_id,
            label: 'Image',
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
.edit-page {
  background: #f8f9fa;
  min-height: 100vh;
}

.edit-container {
  max-width: 1000px;
  margin: 0 auto;
  padding: 24px;
}

.page-header {
  display: flex;
  align-items: center;
  gap: 16px;
  margin-bottom: 32px;
}

.back-btn {
  background: #ffffff;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;

  &:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  }
}

.page-title {
  font-size: 28px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0;
}

.form-card {
  background: #ffffff;
  border-radius: 16px;
  padding: 32px;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
}

.form-section {
  margin-bottom: 40px;

  &:last-child {
    margin-bottom: 0;
  }
}

.section-title {
  font-size: 18px;
  font-weight: 600;
  color: #1a1a1a;
  margin-bottom: 24px;
  padding-bottom: 12px;
  border-bottom: 2px solid #f0f0f0;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 24px;
  margin-bottom: 24px;
}

.form-field {
  margin-bottom: 24px;

  &:last-child {
    margin-bottom: 0;
  }
}

.field-label {
  display: block;
  font-size: 14px;
  font-weight: 600;
  color: #333;
  margin-bottom: 8px;
}

.wysiwyg-editor {
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  overflow: hidden;
  transition: all 0.3s ease;

  &:focus-within {
    border-color: var(--q-primary);
    box-shadow: 0 0 0 2px rgba(25, 118, 210, 0.1);
  }
}

.editor-toolbar {
  display: flex;
  gap: 4px;
  padding: 8px;
  background: #f5f5f5;
  border-bottom: 1px solid #e0e0e0;
}

.editor-content {
  min-height: 150px;
  padding: 16px;
  outline: none;
  line-height: 1.6;

  &:empty:before {
    content: attr(data-placeholder);
    color: #999;
  }

  ul, ol {
    padding-left: 20px;
  }

  strong {
    font-weight: 700;
  }

  em {
    font-style: italic;
  }

  u {
    text-decoration: underline;
  }
}

.field-error {
  color: #c10015;
  font-size: 12px;
  margin-top: 4px;
}

.images-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
  gap: 20px;
  margin-bottom: 24px;
}

.image-item {
  background: #f5f5f5;
  border-radius: 12px;
  overflow: hidden;
  transition: all 0.3s ease;

  &:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  }
}

.image-wrapper {
  position: relative;
  aspect-ratio: 1;
  overflow: hidden;
}

.image-preview {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.primary-badge {
  position: absolute;
  top: 8px;
  right: 8px;
}

.image-actions {
  display: flex;
  justify-content: center;
  gap: 8px;
  padding: 12px;
  background: #ffffff;
  border-top: 1px solid #e0e0e0;
}

.upload-area {
  border: 2px dashed #e0e0e0;
  border-radius: 12px;
  padding: 24px;
  background: #fafafa;
  transition: all 0.3s ease;

  &:hover {
    border-color: #bdbdbd;
    background: #f5f5f5;
  }
}

.preview-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
  gap: 12px;
  margin-top: 16px;
}

.preview-item {
  position: relative;
  aspect-ratio: 1;
  border-radius: 8px;
  overflow: hidden;
  border: 1px solid #e0e0e0;
}

.preview-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.remove-btn {
  position: absolute;
  top: 4px;
  right: 4px;
  background: rgba(255, 255, 255, 0.95);
}

.form-actions {
  display: flex;
  gap: 12px;
  justify-content: flex-end;
  padding-top: 24px;
  border-top: 1px solid #f0f0f0;
}

.submit-btn {
  height: 48px;
  padding: 0 32px;
  font-size: 16px;
  font-weight: 600;
  border-radius: 10px;
  transition: all 0.3s ease;

  &:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(25, 118, 210, 0.3);
  }
}

.cancel-btn {
  height: 48px;
  padding: 0 32px;
  font-size: 16px;
  font-weight: 600;
  border-radius: 10px;
  transition: all 0.3s ease;
}

@media (max-width: 768px) {
  .edit-container {
    padding: 16px;
  }

  .page-header {
    margin-bottom: 24px;
  }

  .page-title {
    font-size: 24px;
  }

  .form-card {
    padding: 24px;
  }

  .form-row {
    grid-template-columns: 1fr;
    gap: 16px;
  }

  .form-field {
    margin-bottom: 16px;
  }

  .images-grid {
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: 16px;
  }

  .upload-area {
    padding: 16px;
  }

  .form-actions {
    flex-direction: column;
  }

  .submit-btn,
  .cancel-btn {
    width: 100%;
  }
}
</style>
