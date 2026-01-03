import { createRouter, createWebHistory } from 'vue-router'
import IndexLayouts from '@/layouts/IndexLayouts.vue'
import Index from '@/views/dashboard/Index.vue'
import project from '@/views/project/project.vue'
import reports from '@/views/reports/reports.vue'


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
          component: project,
        },
        {
          path : '/reports',
          name : 'reports',
          component : reports,
        },
      ],
    },
  ],
})

export default router
