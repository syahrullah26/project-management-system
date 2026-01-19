import { createRouter, createWebHistory } from 'vue-router'
import IndexLayouts from '@/layouts/IndexLayouts.vue'
import AuthLayouts from '@/layouts/AuthLayouts.vue'

import Index from '@/views/dashboard/Index.vue'
import Project from '@/views/project/Project.vue'
import Reports from '@/views/reports/Reports.vue'
import Login from '@/views/auth/login.vue'
import Register from '@/views/auth/register.vue'

import { useAuthStore } from '@/stores/auth'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/login',
      component: AuthLayouts,
      meta: { guest: true },
      children: [
        { path: '', name: 'login', component: Login },
        { path: 'register', name: 'register', component: Register },
      ],
    },

    {
      path: '/',
      component: IndexLayouts,
      meta: { requiresAuth: true },
      children: [
        {
          path: '',
          name: 'dashboard',
          component: Index,
        },
        {
          path: 'project',
          name: 'project',
          component: Project,
        },
        {
          path: 'reports',
          name: 'reports',
          component: Reports,
        },
      ],
    },
  ],
})

router.beforeEach((to) => {
  const auth = useAuthStore()

  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return { name: 'login' }
  }

  if (to.meta.guest && auth.isAuthenticated) {
    return { name: 'dashboard' }
  }
})

export default router
