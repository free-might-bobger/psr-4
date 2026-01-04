import { RouteRecordRaw } from 'vue-router';
import Dashboard from './dashboard';
const routes: RouteRecordRaw[] = [
  ...Dashboard,
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      { path: '', component: () => import('pages/IndexPage.vue') },
    ],
  },
  {
    path: '/find-store',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      { path: '', component: () => import('pages/FindStorePage.vue') },
    ],
  },
  {
    path: '/cart',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      { path: '', component: () => import('pages/cart/IndexPage.vue') },
      { path: 'checkout', component: () => import('src/pages/cart/CheckoutPage.vue') ,
       meta: { requiresAuth: true }
      }
    ],
  },
  {
    path: '/public_stores',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      { path: ':id', component: () => import('pages/public_stores/IndexPage.vue') },
      { path: ':id/item/:item_id', component: () => import('pages/public_stores/item/IndexPage.vue')
      }
    ],
  },
  {
    path: '/login',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      { path: '', component: () => import('pages/LoginPage.vue') }
    ],
  },
  // {
  //   path: '/',
  //   component: () => import('layouts/MainLayout.vue'),
  //   children: [
  //     { path: '', component: () => import('pages/IndexPage.vue') },
  //     { path: 'cart', component: () => import('pages/cart/IndexPage.vue') },
  //     { path: 'cart/checkout', component: () => import('pages/cart/CheckoutPage.vue') },
  //     { path: 'about-us', component: () => import('pages/AboutUs.vue') },
  //     { path: 'contact-us', component: () => import('pages/ContactUs.vue') },
  //     { path: 'activation_code/:id', component: () => import('pages/ActivationCodePage.vue') },
  //     { path: 'recover_password/:id', component: () => import('pages/PasswordResetPage.vue') },
  //     { path: 'public_stores/:id', component: () => import('pages/public_stores/IndexPage.vue') },
  //     { path: 'public_stores/:id/item/:item_id', component: () => import('pages/public_stores/item/IndexPage.vue') },
  //   ],
  // },

  // Always leave this as last one,
  // but you can also remove it
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue'),
  },
];

export default routes;
