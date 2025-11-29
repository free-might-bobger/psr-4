import { axios } from 'src/boot/axios';
import { errorToString } from 'boot/common';
import { Notify, Loading } from 'quasar';
import { useUserStore } from 'src/stores/user';
import { storeToRefs } from 'pinia';
import { queryString, removeEmptyValue } from './common';
import { useCommonStore } from 'src/stores/common';
import { AxiosResponse } from 'axios';
import { GetParams, CreateData, LoginInterface, ShowInterface, DeleteInterface, UpdateInterface} from 'boot/interfaces';
import { ERROR_CODE } from './constant';
const useCommon = useCommonStore();
const useUser = useUserStore()
const { profile, user } = storeToRefs(useUser);


export const login = async (attributes: LoginInterface): Promise<void | boolean> => {
  Loading.show();
  return await axios
    .post('login', attributes)
    .then((res) => {
      Loading.hide();
      const data = res.data.data;
      profile.value = data;
      user.value = data;
      return true;
    })
    .catch((err) => {
      Loading.hide();
      Notify.create({
        position: 'bottom',
        type: 'negative',
        message: err.response.data?.message,
      });
    });
};

export const register = async (params: object): Promise<void | boolean> => {
  Loading.show();
  return await axios
    .post('user-register', params)
    .then(() => {
      Loading.hide();

      Notify.create({
        timeout: 5000,
        position: 'bottom',
        type: 'positive',
        message: 'Please check your email for confirmation.',
      });
      return true;
    })
    .catch((err) => {
      Loading.hide();
      Notify.create({
        position: 'bottom',
        type: 'negative',
        message: errorToString(err.response.data.errors),
      });
    });
};

export const logout = async (): Promise<object | void> => {
  Loading.show();
  return await axios
    .post('/logout')
    .then(() => {
      Loading.hide();
      useUser.resetProfile()

    })
    .catch(() => {
      Loading.hide();
      useUser.resetProfile()
    });
};

export const getUserMenus = async (): Promise<void> => {
  return await axios
    .get('menus?type=collection')
    .then((res) => {
      const data = res.data.data;
    })
    .catch((err) => err);
};

/**
 * 
 * Used by updating item with images
 * if there's another use, please list them here.
 * 
 */
export async function saveEntityWithImages(params: { store_id: number, entity: string, optimus_id: number, fd: FormData}): Promise<boolean> {
  Loading.show({
    message: 'Updating ....',
  });

  return await axios
    .post(
      '/' + params.entity + '/' + params.optimus_id + '?filters=store_id:' + params.store_id,
      params.fd,
      {
        headers: { 'Content-Type': 'multipart/form-data' },
      }
    )
    .then(() => {
      Notify.create({
        position: 'bottom',
        type: 'positive',
        message: 'You have uploaded the image succesfully.',
      });
      Loading.hide();
      return true;
    })
    .catch(() => {
      Loading.hide();
      return false;
    });
}

export async function get(
  params: GetParams,
  showLoader: boolean
): Promise<object | void | string> {

  let message = 'Getting information...'
  if(params.message){
    message = params.message
  }
  if (showLoader) {
    Loading.show({
      message: message,
    });
  }

  return axios
    .get(`/${params.entity}?${queryString(params.query)}`)
    .then((res): AxiosResponse<object | void> => {
      Loading.hide();
      return res;
    })
    .catch((err) => {
      // if(err.status === ERROR_CODE.UNAUTHORIZED){
      //   logout()
      //   window.location.href = '/'
      // }
      // return err;
    });
}

export async function updateProfile($payload: object): Promise<void | object> {
  Loading.show({
    message: 'Updating profile',
  });
  return axios
    .patch('profile', $payload)
    .then(() => {
      Loading.hide();
      Notify.create({
        position: 'bottom',
        type: 'positive',
        message: 'You have successfully updated your profile.',
      });
    })
    .catch((err) => {
      return err;
    });
}

export async function changePassword(payload: { id: number }): Promise<void | object> {
  Loading.show({
    message: 'Changing password...',
  });
  return axios
    .post(`change-password/${payload.id}`, payload)
    .then(() => {
      Loading.hide();
      Notify.create({
        position: 'bottom',
        type: 'positive',
        message: 'Password is successfully changed.',
      });
    })
    .catch((err) => {
      return err;
    });
}

export async function update(params: UpdateInterface, loading = true, notify = true) {
  if (loading) {
    Loading.show({
      message: 'Updating...',
    });
  }
  const newParams = removeEmptyValue(params.data);
  return await axios
    .patch(`/${params.entity}/${params.optimus_id}`, newParams)
    .then((response) => {
      if (notify) {
        Notify.create({
          position: 'bottom',
          type: 'positive',
          message: 'Updated successfully.',
        });
      }
      Loading.hide();
      return response.data.data;
    })
    .catch((err) => {
      Loading.hide();
      if (err.response.status == 403) {
        Notify.create({
          position: 'bottom',
          type: 'negative',
          message: err.response.data.message,
        });
      }
    });
}

export async function create(
  params: CreateData,
  isNotify = true,
  loadingMessage = '',
  successMessage = ''
) {
  if (loadingMessage == '') {
    loadingMessage = `Creating ${params.entity}...`;
  }
  if (successMessage == '') {
    successMessage = 'Data successfully created.';
  }
  if (isNotify) {
    Loading.show({
      message: loadingMessage,
    });
  }

  return await axios
    .post(`/${params.entity}`, params.data)
    .then((response) => {
      Loading.hide();
      if (isNotify) {
        Notify.create({
          position: 'bottom',
          type: 'positive',
          message: successMessage,
        });
      }

      return response.data.data;
    })
    .catch(() => {
      Loading.hide();
      Notify.create({
        position: 'bottom',
        type: 'negative',
        message: 'An error has occured in creating ' + params.entity,
      });
    });
}

export async function onRequest(
  params: GetParams,
  showLoader?: boolean
): Promise<void> {
  await useCommon.setResultPagination(params, showLoader);
}

export const nextPage = (entityQuery: GetParams) => {
  console.log('entityQuery');
  if(entityQuery.query.page){
    entityQuery.query.page++;
  }
  onRequest(entityQuery);
};

export const previousPage = (entityQuery: GetParams) => {
  if(entityQuery.query.page){
    entityQuery.query.page--
  }
  onRequest(entityQuery);
};

export const firstPage = (entityQuery: GetParams) => {
  entityQuery.query.page = 1;
  onRequest(entityQuery);
};

export const lastPage = (entityQuery: GetParams, pagination: { lastPage: number}) => {
  entityQuery.query.page = pagination.lastPage;
  onRequest(entityQuery);
};


/**
 * 
 * @param params ShowInterface
 * @returns 
 */
export async function show<T>(params: ShowInterface, hasLoader = false): Promise<T> {
  if(hasLoader){
    Loading.show({
      message: `Getting information...`,
    });
  }
  return axios
    .get(`/${params.entity}/${params.optimus_id}?${queryString(params.query)}`)
    .then((response) => {
      Loading.hide();
      return response.data.data;
    })
    .catch((err) => {
      Loading.hide();
      if (err.response.status == 403) {
        Notify.create({
          position: 'bottom',
          type: 'negative',
          message: err.response.data.message,
        });
      }
    });
}

export async function deleteEntity(params: DeleteInterface) {
  Loading.show();
  return await axios
    .delete(`/${params.entity}/${params.optimus_id}`)
    .then(() => {
      Notify.create({
        position: 'bottom',
        type: 'positive',
        message: 'Successfully deleted.',
      });
      return true;
    })
    .catch((err) => {
      Loading.hide();
      if (err.response.status == 403) {
        Notify.create({
          position: 'bottom',
          type: 'negative',
          message: err.response.data.message,
        });
      }
    });
}

export async function recoverPassword(
  params = {},
  loading = true,
  notify = true
) {
  if (loading) {
    Loading.show({
      message: 'Sending email...',
    });
  }
  return await axios
    .post('/recover_password', params)
    .then((response) => {
      if (notify) {
        Notify.create({
          position: 'bottom',
          type: 'positive',
          message: response.data.message,
        });
      }
      Loading.hide();
      return true;
    })
    .catch((err) => {
      Loading.hide();
      if (err.response.status == 403) {
        Notify.create({
          position: 'bottom',
          type: 'negative',
          message: err.response.data.message,
        });
      }
    });
}

export async function googleApiTextSearch(
  value: string,
  loading = true
): Promise<void | object> {
  if (loading) {
    Loading.show({
      message: 'Getting results...',
    });
  }
  return await axios
    .get('/google-api-text-search', {
      params: {
        query: value,
      },
    })
    .then((response) => {
      Loading.hide();
      return response;
    })
    .catch(() => {
      Loading.hide();
    });
}

export async function googleNearBySearch(
  loading = true
): Promise<void | object> {
  if (loading) {
    Loading.show({
      message: 'Getting results...',
    });
  }
  return await axios
    .get('/google-api-nearby-search')
    .then((response) => {
      Loading.hide();
      return response;
    })
    .catch(() => {
      Loading.hide();
    });
}

export const isEmailUnique = async (param: object): Promise<boolean> => {
  return await axios
    .get(`/user/validator/email?${queryString(param)}`)
    .then(() => true)
    .catch(() => false);
};

export const isMobileExist = async (param: object): Promise<boolean> => {
  return await axios
    .get(`/user/is-mobile-exist?${queryString(param)}`)
    .then(() => true)
    .catch(() => false);
};
