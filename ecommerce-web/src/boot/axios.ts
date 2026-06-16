import axios from 'axios';
import { useUserStore } from 'stores/user';
import { storeToRefs } from 'pinia';

const checkDevProduction = (): string => {
  const live = 'https://api.mynearshops.com/';
  const test = 'http://localhost:8081/';
  if (window.location.href.includes('mynearshops')) {
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
      // IMPORTANT: do not access Pinia stores at module top-level.
      // In production builds, boot module import order can cause Pinia to be unavailable early.
      const { profile } = storeToRefs(useUserStore());
      if (profile.value?.token) request.headers['Authorization'] = `Bearer ${profile.value.token}`;
      return request;
    },
    (error) => {
      return Promise.reject(error);
    }
  );
};

intercept(true);

export { axios };
