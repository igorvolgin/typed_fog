import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    // Pages
    {
      path: '/',
      redirect: '/countries',
    },
    {
      path: '/countries',
      component: () => import('@/views/pages/CountriesView.vue'),
    },
    {
      path: '/countries/:code',
      component: () => import('@/views/pages/CountryDetailView.vue'),
    },
    {
      path: '/health',
      component: () => import('@/views/pages/HealthView.vue'),
    },

    // Auth
    {
      path: '/login',
      component: () => import('@/views/auth/LoginView.vue'),
    },
    {
      path: '/logout',
      component: () => import('@/views/auth/LogoutView.vue'),
    },
    {
      path: '/callback',
      component: () => import('@/views/auth/CallbackView.vue'),
    },
  ],
})

export default router
