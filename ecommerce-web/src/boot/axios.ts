import axios from 'axios';
import { useUserStore } from 'stores/user';
import { storeToRefs } from 'pinia';

const useUser = useUserStore();
const { profile } = storeToRefs(useUser);
const checkDevProduction = (): string => {
  const live = 'https://shaischool.net/api/';
  const test = 'http://127.0.0.1:8080/api/';
  if (window.location.href.includes('shaischool')) {
    return live;
  }
  return test;
};

axios.defaults.baseURL = checkDevProduction();

const intercept = (isIntercept: boolean) => {
  if (!isIntercept) {
    return;
  }
  axios.interceptors.request.use(
    (request) => {
      if (profile.value.token) {
        request.headers['Authorization'] = `Bearer ${profile.value.token}`;
      }
      return request;
    },
    (error) => {
      return Promise.reject(error);
    }
  );
};

intercept(true);

export { axios };
