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
    path: '/find-shops',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      { path: '', component: () => import('pages/FindStorePage.vue') },
    ],
  },
  {
    path: '/find-items',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      { path: '', component: () => import('pages/FindItemPage.vue') },
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
  {
    path: '/register',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      { path: '', component: () => import('pages/RegisterPage.vue') },
    ],
  },
  {
    path: '/forgot-password',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      { path: '', component: () => import('pages/ForgotPasswordPage.vue') },
    ],
  },
  {
    path: '/reset-password/:code',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      { path: '', component: () => import('pages/ResetPasswordPage.vue') },
    ],
  },
  {
    path: '/apply-store',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      {
        path: '',
        component: () => import('pages/ApplyStorePage.vue'),
        meta: { requiresAuth: true },
      },
    ],
  },

  // Always leave this as last one,
  // but you can also remove it
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue'),
  },
];

export default routes;
