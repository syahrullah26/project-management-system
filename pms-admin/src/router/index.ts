import { createRouter, createWebHistory } from 'vue-router'
import IndexLayouts from '@/layouts/IndexLayouts.vue'
import Index from '@/views/dashboard/Index.vue'
import Project from '@/views/project/Project.vue'
import Reports from '@/views/reports/Reports.vue'


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
        {
          path : '/project',
          name : 'project',
          component: Project,
        },
        {
          path : '/reports',
          name : 'reports',
          component : Reports,
        },
      ],
    },
  ],
})

export default router
