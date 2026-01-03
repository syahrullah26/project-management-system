import { createRouter, createWebHistory } from 'vue-router'
import IndexLayouts from '@/layouts/IndexLayouts.vue'
import Index from '@/views/dashboard/Index.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      component: IndexLayouts,
      children: [
        {
          path: '',
          name: 'dashboard',
          component: Index,
        },
      ],
    },
  ],
})

export default router
