<template>
  <q-form @submit.prevent="submit" ref="myForm">
    <p class="text-h5 text-center">LEARNERS PROFILE FORM</p>

    <my-upload
      field="img"
      @crop-success="cropSuccess"
      v-model="showUpload"
      :width="100"
      :height="100"
      :params="params"
      :headers="headers"
      img-format="png"
      :langExt="langExt"
      noSquare
    ></my-upload>
    <div class="flex flex-center" v-if="route.params.id">
      <q-img
        class="rounded-borders"
        :src="getImageDataIfExist()"
        style="width: 150px"
      />
      <q-btn
        color="primary"
        outline
        style="height: 10px"
        glossy
        @click="toggleShow"
        class="q-mt-md q-ml-md"
        >Change</q-btn
      >
    </div>
    <div class="row q-gutter-x-xs q-gutter-y-sm">
      <div class="col-12">
        <p class="text-body1">Select a schedule<br /></p>
      </div>
      <div class="col-12">
        <q-select
          use-input
          clearable
          outlined
          v-model="admission.schedule"
          :options="schedules"
          dense
          label="Schedule"
          lazy-rules
          :rules="[(val) => validateRequired(val) || 'Schedule is required.']"
        />
      </div>
      <div class="col-12">
        <p class="text-body1">
          1. Web-Based Information System Auto Generated <br />
        </p>
      </div>

      <div class="col-12">
        <q-input
          clearable
          v-model="admission.uli"
          dense
          outlined
          label="Unique Learner Identifier"
        />
      </div>
      <div class="col-3">
        <q-input
          clearable
          outlined
          dense
          v-model="admission.entry_date"
          mask="date"
          :rules="['date']"
          label="Entry Date"
          error-message="Entry Date is required."
        >
          <template v-slot:append>
            <q-icon name="event" class="cursor-pointer">
              <q-popup-proxy
                ref="qDateProxy"
                transition-show="scale"
                transition-hide="scale"
              >
                <q-date v-model="admission.entry_date" />
              </q-popup-proxy>
            </q-icon>
          </template>
        </q-input>
      </div>
    </div>
    <div class="row q-gutter-x-xs q-gutter-y-sm">
      <div class="col-12">
        <br />
        <p class="text-body1">2. Manpower Profile <br /></p>
      </div>
      <div class="col-12">
        <p>2.1 Name:</p>
      </div>
      <div class="col-4">
        <q-input
          v-model="admission.lastname"
          dense
          outlined
          label="Lastname"
          clearable
        />
      </div>
      <div class="col-4">
        <q-input
          clearable
          v-model="admission.firstname"
          dense
          outlined
          label="Firstname"
          lazy-rules
          :rules="[
            (val) => (val && val.length > 0) || 'Firstname is required.',
          ]"
        />
      </div>
      <div class="col-3">
        <q-input
          clearable
          v-model="admission.middlename"
          dense
          outlined
          label="Middlename"
          lazy-rules
          :rules="[
            (val) => (val && val.length > 0) || 'Middlename is required.',
          ]"
        />
      </div>
    </div>
    <br />
    <div class="row q-gutter-x-xs q-gutter-y-sm">
      <div class="col-12">
        <p>2.2 Complete Permanent Mailing Address:</p>
      </div>
      <div class="col-4">
        <q-input
          clearable
          v-model="admission.number_street"
          dense
          outlined
          label="Number, Street"
          lazy-rules
          :rules="[
            (val) =>
              (val && val.length > 0) || 'Number and Street is required.',
          ]"
          hide-bottom-space
        />
      </div>
      <div class="col-4">
        <q-input
          clearable
          v-model="admission.brgy"
          dense
          outlined
          label="Barangay"
          lazy-rules
          :rules="[(val) => (val && val.length > 0) || 'Barangay is required.']"
          hide-bottom-space
        />
      </div>
      <div class="col-3">
        <q-input
          v-model="admission.district"
          dense
          outlined
          label="District"
          clearable
          hide-bottom-space
        />
      </div>
      <div class="col-4">
        <select-province
          outlined
          dense
          :selected-province="admission.permanent_province"
          @change="permanentProvinceChange"
          :required="true"
        />
      </div>
      <div class="col-4">
        <select-city
          outlined
          dense
          :selected-city="admission.permanent_city"
          @change="permanentCityChange"
          :cities="permanentCities"
          :searchCities="permanentSearchCities"
          lazy-rules
          :rules="[(val:any) => validateRequired(val) || 'City is required.']"
        />
      </div>
      <div class="col-3">
        <q-input
          clearable
          v-model="admission.permanent_region"
          dense
          outlined
          label="Region"
        />
      </div>
      <div class="col-4">
        <q-input
          clearable
          v-model="admission.permanent_email"
          dense
          outlined
          label="Email Address/Facebook Account"
        />
      </div>
      <div class="col-4">
        <q-input
          clearable
          v-model="admission.permanent_contact_no"
          dense
          outlined
          label="Contact No:"
          lazy-rules
          :rules="[
            (val) => (val && val.length > 0) || 'Contact No. is required.',
          ]"
        />
      </div>
      <div class="col-3">
        <q-input
          clearable
          v-model="admission.permanent_nationality"
          dense
          outlined
          label="Nationality"
          lazy-rules
          :rules="[
            (val) => (val && val.length > 0) || 'Nationality is required.',
          ]"
        />
      </div>
    </div>
    <br />
    <div class="row q-gutter-x-xs q-gutter-y-sm">
      <div class="col-12">
        <p class="text-body1">3. Personal Information</p>
      </div>
      <div class="col-4">
        <q-select
          clearable
          outlined
          v-model="admission.gender"
          :options="genders"
          dense
          label="3.1 Sex"
          lazy-rules
          :rules="[(val) => validateRequired(val) || 'Gender is required.']"
        />
      </div>
      <div class="col-4">
        <q-select
          outlined
          v-model="admission.civil_status"
          :options="civilStatus"
          dense
          label="3.2 Civil Status"
          clearable
          lazy-rules
          :rules="[
            (val) => validateRequired(val) || 'Civil Status is required.',
          ]"
        />
      </div>
      <div class="col-3">
        <q-select
          outlined
          v-model="admission.employment_status"
          :options="employmentStatus"
          dense
          label="3.3 Employment Status (before training)"
          clearable
          lazy-rules
          :rules="[
            (val) =>
              validateRequired(val) || 'Employment Training is required.',
          ]"
        />
      </div>
      <div class="col-12">
        <br />
        <span>3.4 Birthdate</span>
      </div>
      <div class="col-4">
        <q-input
          clearable
          outlined
          dense
          v-model="admission.birthday"
          mask="date"
          :rules="['date']"
          label="Date of Birth"
          error-message="Date of Birth is required."
        >
          <template v-slot:append>
            <q-icon name="event" class="cursor-pointer">
              <q-popup-proxy
                ref="qDateProxy"
                transition-show="scale"
                transition-hide="scale"
              >
                <q-date v-model="admission.birthday" />
              </q-popup-proxy>
            </q-icon>
          </template>
        </q-input>
      </div>

      <div class="col-4">
        <q-input v-model="admission.age" dense outlined label="Age" disable />
      </div>
      <div class="col-12">
        <span>3.4 Birthplace</span>
      </div>
      <div class="col-4">
        <select-province
          outlined
          :selected-province="admission.birthday_province"
          @change="birthdayProvinceChange"
          :required="true"
        />
      </div>
      <div class="col-4">
        <select-city
          outlined
          dense
          :selected-city="admission.birthday_city"
          @change="birthdayCityChange"
          :cities="birthdayCities"
          :searchCities="birthdaySearchCities"
          lazy-rules
          :rules="[
            (val:any) =>
              validateRequired(val) || 'City is required.',
          ]"
        />
      </div>
      <div class="col-3">
        <q-input
          clearable
          v-model="admission.region"
          dense
          outlined
          label="Region"
          lazy-rules
          :rules="[
            (val) => (val && val.length > 0) || 'Nationality is required.',
          ]"
        />
      </div>
    </div>
    <br />
    <div class="row q-gutter-x-xs q-gutter-y-sm">
      <div class="col-12">
        <p class="text-body1">
          4. Educational Attainment Before the Training (Trainee)
        </p>
      </div>
      <div class="col-6">
        <q-select
          outlined
          v-model="admission.educational_attainment"
          :options="educationalAttainments"
          dense
          label="Educational Attainment"
          clearable
          lazy-rules
          :rules="[
            (val) =>
              validateRequired(val) || 'Educational Attainment is required.',
          ]"
        />
      </div>
    </div>
    <br />
    <div class="row q-gutter-x-xs q-gutter-y-sm">
      <div class="col-12">
        <span class="text-body1"> NAME OF SCHOOL LAST ATTENDED </span>
      </div>
      <div class="col-4">
        <q-input
          clearable
          v-model="admission.elementary_name"
          dense
          outlined
          label="Elementary School Name"
        />
      </div>
      <div class="col-4">
        <q-input
          clearable
          v-model="admission.elementary_address"
          dense
          outlined
          label="Elementary School Address"
        />
      </div>
      <div class="col-3">
        <q-input
          clearable
          v-model="admission.elementary_year"
          dense
          outlined
          label="Elementary Year Graduated"
        />
      </div>
      <div class="col-4">
        <q-input
          clearable
          v-model="admission.highschool_name"
          dense
          outlined
          label="Highschool School Name"
        />
      </div>
      <div class="col-4">
        <q-input
          clearable
          v-model="admission.highschool_address"
          dense
          outlined
          label="Highschool School Address"
        />
      </div>
      <div class="col-3">
        <q-input
          clearable
          v-model="admission.highschool_year"
          dense
          outlined
          label="Highschool Year Graduated"
        />
      </div>
      <div class="col-4">
        <q-input
          clearable
          v-model="admission.college_name"
          dense
          outlined
          label="College School Name"
        />
      </div>
      <div class="col-4">
        <q-input
          clearable
          v-model="admission.college_address"
          dense
          outlined
          label="College School Address"
        />
      </div>
      <div class="col-3">
        <q-input
          clearable
          v-model="admission.college_year"
          dense
          outlined
          label="College Year Graduated"
        />
      </div>
      <div class="col-4">
        <q-input
          clearable
          v-model="admission.vocational_name"
          dense
          outlined
          label="Vocationl School Name"
        />
      </div>
      <div class="col-4">
        <q-input
          clearable
          v-model="admission.vocational_address"
          dense
          outlined
          label="Vocational School Address"
        />
      </div>
      <div class="col-3">
        <q-input
          clearable
          v-model="admission.vocational_year_graduated"
          dense
          outlined
          label="Vocational Year Graduated"
        />
      </div>
    </div>
    <br />
    <div class="row q-gutter-x-xs q-gutter-y-sm">
      <div class="col-12">
        <span class="text-body1"> 6. Name of Course/Qualification </span>
      </div>
      <div class="col-12">
        <q-select
          outlined
          v-model="admission.courses_classification"
          :options="coursesClassification"
          dense
          label="Please select one"
          clearable
          lazy-rules
          :rules="[
            (val) =>
              validateRequired(val) || 'Course Classification is required.',
          ]"
          hide-bottom-space
        />
      </div>
      <div class="col-12">
        <q-input
          clearable
          v-model="admission.program_enrolled"
          dense
          outlined
          label="Program Enrolled"
        />
      </div>
      <div class="col-6">
        <q-input
          clearable
          outlined
          dense
          v-model="admission.start_training_duration"
          mask="date"
          :rules="['date']"
          label="Start Training Duration"
          error-message="Start Training Duration is required."
        >
          <template v-slot:append>
            <q-icon name="event" class="cursor-pointer">
              <q-popup-proxy
                ref="qDateProxy"
                transition-show="scale"
                transition-hide="scale"
              >
                <q-date v-model="admission.start_training_duration" />
              </q-popup-proxy>
            </q-icon>
          </template>
        </q-input>
      </div>
      <div class="col-5">
        <q-input
          clearable
          outlined
          dense
          v-model="admission.end_training_duration"
          mask="date"
          :rules="['date']"
          label="End Training Duration"
          error-message="End Training Duration is required."
        >
          <template v-slot:append>
            <q-icon name="event" class="cursor-pointer">
              <q-popup-proxy
                ref="qDateProxy"
                transition-show="scale"
                transition-hide="scale"
              >
                <q-date v-model="admission.end_training_duration" />
              </q-popup-proxy>
            </q-icon>
          </template>
        </q-input>
      </div>
    </div>
    <br />
    <div class="row q-gutter-x-xs q-gutter-y-sm">
      <div class="col-12">
        <span class="text-body1"> 7. Parent's Information </span>
      </div>
      <div class="col-5">
        <q-input
          clearable
          v-model="admission.mothers_name"
          dense
          outlined
          label="Mother's name"
        />
      </div>
      <div class="col-6">
        <q-input
          clearable
          v-model="admission.mothers_occupation"
          dense
          outlined
          label="Occupation"
        />
      </div>
      <div class="col-5">
        <q-input
          clearable
          v-model="admission.mothers_home_address"
          dense
          outlined
          label="Home Address"
        />
      </div>
      <div class="col-6">
        <q-input
          clearable
          v-model="admission.mothers_contact_number"
          dense
          outlined
          label="Contact Number"
        />
      </div>

      <div class="col-5">
        <q-input
          clearable
          v-model="admission.fathers_name"
          dense
          outlined
          label="Father's name"
        />
      </div>
      <div class="col-6">
        <q-input
          clearable
          v-model="admission.fathers_occupation"
          dense
          outlined
          label="Occupation"
        />
      </div>
      <div class="col-5">
        <q-input
          clearable
          v-model="admission.fathers_home_address"
          dense
          outlined
          label="Home Address"
        />
      </div>
      <div class="col-6">
        <q-input
          clearable
          v-model="admission.fathers_contact_number"
          dense
          outlined
          label="Contact Number"
        />
      </div>

      <br />
      <div class="row q-gutter-x-xs q-gutter-y-sm">
        <div class="col-12">
          <span class="text-body1">
            8. Contact Person in Case of Emergency
          </span>
        </div>
        <div class="col-6">
          <q-input
            clearable
            v-model="admission.name_emergency"
            dense
            outlined
            label="Name"
            lazy-rules
            :rules="[(val) => (val && val.length > 0) || 'Name is required.']"
          />
        </div>
        <div class="col-5">
          <q-input
            clearable
            v-model="admission.address_emergency"
            dense
            outlined
            label="Address"
            lazy-rules
            :rules="[
              (val) => (val && val.length > 0) || 'Address is required.',
            ]"
          />
        </div>
        <div class="col-6">
          <q-input
            clearable
            v-model="admission.relationship_emergency"
            dense
            outlined
            label="Relationship"
            lazy-rules
            :rules="[
              (val) => (val && val.length > 0) || 'Relationship is required.',
            ]"
          />
        </div>
        <div class="col-5">
          <q-input
            clearable
            v-model="admission.contact_number_emergency"
            dense
            outlined
            label="Contact Number"
            lazy-rules
            :rules="[
              (val) => (val && val.length > 0) || 'Contact Number is required.',
            ]"
          />
        </div>
      </div>

      <br />
      <div class="row q-gutter-x-xs q-gutter-y-sm">
        <div class="col-12">
          <span class="text-body1">
            10.Student/Scholar Voucher Number (for Scholar only)
          </span>
        </div>
        <div class="col-6">
          <q-input
            clearable
            v-model="admission.voucher_number"
            dense
            outlined
            label="Voucher Number"
          />
        </div>
        <div class="col-5">
          <q-input
            clearable
            v-model="admission.scholar_package"
            dense
            outlined
            label="Scholar Package (TWSP, PESFA, etc.)"
          />
        </div>
        <div class="col-6">
          <q-select
            outlined
            v-model="admission.voucher_course_qualification"
            :options="coursesClassification"
            dense
            label="Name of Course/Qualification"
            clearable
          />
        </div>
      </div>

      <br />
      <div class="row q-gutter-x-xs q-gutter-y-sm">
        <div class="col-12">
          <span class="text-body1">
            11. Employment Record (after training)
          </span>
        </div>
        <div class="col-4">
          <q-input
            clearable
            v-model="admission.employment_company_name"
            dense
            outlined
            label="Company Name"
          />
        </div>
        <div class="col-4">
          <q-input
            clearable
            v-model="admission.employment_position"
            dense
            outlined
            label="Position"
          />
        </div>
        <div class="col-3">
          <q-input
            clearable
            v-model="admission.employment_address"
            dense
            outlined
            label="Address"
          />
        </div>
        <div class="col-4">
          <q-input
            clearable
            v-model="admission.salary"
            dense
            outlined
            label="Salary"
          />
        </div>
        <div class="col-4">
          <q-input
            clearable
            outlined
            dense
            v-model="admission.employment_date_employed"
            mask="date"
            label="Date employed"
            hide-bottom-space
          >
            <template v-slot:append>
              <q-icon name="event" class="cursor-pointer">
                <q-popup-proxy
                  ref="qDateProxy"
                  transition-show="scale"
                  transition-hide="scale"
                >
                  <q-date v-model="admission.employment_date_employed" />
                </q-popup-proxy>
              </q-icon>
            </template>
          </q-input>
        </div>
        <div class="col-3">
          <q-input
            clearable
            v-model="admission.employment_reason_leaving_job"
            dense
            outlined
            label="Reason Leaving a previous job"
          />
        </div>
        <div class="col-11">
          <q-select
            outlined
            v-model="admission.workers_classification"
            :options="workersClassification"
            dense
            label="Classification of Worker"
            clearable
            use-chips
            use-input
          />
        </div>
      </div>
    </div>
    <div class="flex justify-start q-mt-md">
      <q-btn color="primary" type="submit">{{ props.title }}</q-btn>
    </div>
  </q-form>
</template>
<script setup lang="ts">
import { onMounted, watch, ref } from 'vue';
import { useCommonStore } from 'src/stores/common';
import { storeToRefs } from 'pinia';
import SelectProvince from 'src/components/Address/SelectProvince.vue';
import SelectCity from 'src/components/Address/SelectCity.vue';
import { getListingApi, create, get, update } from 'src/boot/axios-call';
import { getAge } from 'boot/utilities';
import { useRoute } from 'vue-router';
import myUpload from 'vue-image-crop-upload';
import { getPrimaryImageUrl } from 'boot/common';
import { urltoFile } from 'boot/common';
import { uploadImagesToServer } from 'boot/axios-call';

const route = useRoute();

const {
  schedules,
  genders,
  civilStatus,
  employmentStatus,
  educationalAttainments,
  coursesClassification,
  workersClassification,
  admission,
} = storeToRefs(useCommonStore());

const props = defineProps({
  title: String,
});

onMounted(async () => {
  await getListingApi();
});

const permanentCities = ref([]);
const permanentSearchCities = ref([]);
const birthdayCities = ref([]);
const birthdaySearchCities = ref([]);
watch(admission.value, async (newVal) => {
  const { birthday } = newVal;
  if (birthday) {
    admission.value.age = getAge(newVal.birthday);
  }
});

const emits = defineEmits(['update']);
const myForm = ref(null);
const submit = async () => {
  myForm.value.validate().then(async (success) => {
    if (success) {
      if (props.title === 'Update') {
        await update({
          entity: 'register_enrollees',
          optimus_id: route.params.id,
          data: admission.value,
        });
      } else {
        const result = await create({
          entity: 'register_enrollees',
          data: admission.value,
        });
        if (result) {
          admission.value = {
            uli: undefined,
            entry_date: undefined,
            gender: undefined,
            civil_status_id: undefined,
            employment_status_id: undefined,
            permanent_province: undefined,
            permanent_province_id: undefined,
            permanent_city: undefined,
            permanent_city_id: undefined,
            birthday_province_id: undefined,
            birthday_city: undefined,
            birthday: undefined,
            region: undefined,
            educational_attainment: undefined,
            elementary_name: undefined,
            elementary_address: undefined,
            elementary_year: undefined,
            highschool_name: undefined,
            highschool_address: undefined,
            highschool_year: undefined,
            college_name: undefined,
            college_address: undefined,
            college_year: undefined,
            vocational_name: undefined,
            vocational_address: undefined,
            vocational_year_graduated: undefined,
            courses_classification: undefined,
            start_training_duration: undefined,
            end_training_duration: undefined,
            program_enrolled: undefined,
            employment_salary: undefined,
            employment_date_employed: undefined,
            employment_reason_leaving_job: undefined,
            salary: undefined,
            employment_address: undefined,
            employment_position: undefined,
            employment_company_name: undefined,
            voucher_course_qualification: undefined,
            scholar_package: undefined,
            voucher_number: undefined,
            mothers_name: undefined,
            mothers_occupation: undefined,
            mothers_home_address: undefined,
            mothers_contact_number: undefined,
            fathers_contact_number: undefined,
            fathers_home_address: undefined,
            fathers_occupation: undefined,
            fathers_name: undefined,
            name_emergency: undefined,
            address_emergency: undefined,
            relationship_emergency: undefined,
            contact_number_emergency: undefined,
            firstname: undefined,
            lastname: undefined,
            middlename: undefined,
            number_street: undefined,
            brgy: undefined,
            district: undefined,
            permanent_region: undefined,
            permanent_email: undefined,
            permanent_contact_no: undefined,
            permanent_nationality: undefined,
            user: undefined,
            worker_classification_id: undefined,
            birthday_city_id: undefined,
          };
        }
      }
    } else {
      // oh no, user has filled in
      // at least one invalid value
    }
  });
};

const validateRequired = (val: object) => {
  if (val) {
    return true;
  }
  return false;
};

const getCities = async (
  province: object,
  cities: Array<object>,
  searchCities: Array<object>
) => {
  let filters = null;
  if (province) {
    filters = 'province_id:' + province.id;
    const result = await get(
      {
        entity: 'cities',
        query: {
          filters: filters,
          type: 'collection',
        },
        message: '',
      },
      true
    );
    cities.value = result.data.data;
    searchCities.value = result.data.data;
  } else {
    cities.value = [];
    searchCities.value = [];
  }
};

const birthdayProvinceChange = async (v: object) => {
  admission.value.birthday_province = v;
  if (v) {
    await getCities(v, birthdayCities, birthdaySearchCities);
  } else {
    admission.value.birthday_city = null;
  }
};

const permanentProvinceChange = async (v: object) => {
  admission.value.permanent_province = v;
  if (v) {
    await getCities(v, permanentCities, permanentSearchCities);
  } else {
    admission.value.permanent_city = null;
  }
};

const permanentCityChange = (v: object) => {
  admission.value.permanent_city = v;
  admission.value.permanent_city_id = v?.id;
};

const birthdayCityChange = (v: object) => {
  admission.value.birthday_city = v;
  admission.value.birthday_city_id = v?.id;
};

const showUpload = ref(false);
const toggleShow = () => {
  showUpload.value = !showUpload.value;
};

const params = {
  token: '123456798',
  name: 'avatar',
};

const headers = {
  smail: '*_~',
};

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
    save: 'Save',
  },
  error: {
    onlyImg: 'Image only',
    outOfSize: 'Image exceeds size limit: ',
    lowestPx: "Image's size is too low. Expected at least: ",
  },
};
const imgDataUrl = ref(''); // the datebase64 url of created image

const cropSuccess = async (img: string) => {
  imgDataUrl.value = img;

  urltoFile(imgDataUrl.value, 'enrollee.jpg', 'image/png').then(async function (
    file
  ): Promise<void> {
    const fd = new FormData();
    fd.append('images[]', file);
    fd.append('isPrimary', 'true');
    await uploadImagesToServer({
      entity: 'Enrollee',
      optimus_id: admission.value.id,
      fd: fd,
    });
  });
};

const getImageDataIfExist = () => {
  if (imgDataUrl.value) {
    return imgDataUrl.value;
  }
  if (admission.value.images?.length > 0) {
    return admission.value.images[0].path_url;
  }
  return getPrimaryImageUrl();
};
</script>
