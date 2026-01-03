<script setup lang="ts">
import { onMounted, ref, computed } from 'vue'
import { getProject } from '@/api/project'
import { getReports } from '@/api/reports'
import type { Project } from '@/api/project'
import type { Reports } from '@/api/reports'
defineOptions({ name: 'DashboardView' })

const project = ref<Project[]>([])
const reports = ref<Reports[]>([])
const loading = ref(false)

const loadData = async () => {
  loading.value = true
  try {
    const [projectData, reportsData] = await Promise.all([getProject(), getReports()])

    project.value = projectData.data
    reports.value = reportsData.data
  } catch {
    console.log('error')
  } finally {
    loading.value = false
  }
}

const totalProject = computed(() => project.value.length)
const totalOnprogressReports = computed(
  () => reports.value.filter((m) => m.status === 'onprogress').length,
)
const totalDoneReports = computed(() => reports.value.filter((m) => m.status === 'done').length)

onMounted(loadData)
</script>

<template>
  <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <!-- Header Section  -->
    <section
      class="bg-white rounded-2xl p-6 shadow-sm border border-black/5 flex flex-col sm:flex-row sm:justify-between sm:items-center"
    >
      <h1 class="text-text-secondary text-xl font-semibold">Dashboard</h1>
      <p class="text-text-primary text-sm">Welcome to admin dashboard</p>
    </section>
    <!-- Card Section  -->
    <section>
      <div class="grid grid-cols-1 sm:grid-cols-1 lg:grid-cols-2 gap-4 auto-rows-fr">
        <RouterLink to="/project" class="group block h-full">
          <div
            class="bg-white rounded-2xl p-6 shadow-sm border border-black/5 h-full flex flex-col justify-between transition-all duration-300 group-hover:-translate-y-1 group-hover:shadow-md group-hover:border-fourth/40"
          >
            <h2 class="text-text-primary font-semibold mb-2">Total Project</h2>
            <p class="text-text-secondary text-sm">
              <span class="font-bold text-text-primary">
                {{ totalProject }}
              </span>
              Active Projects
            </p>
          </div>
        </RouterLink>

        <RouterLink to="/reports" class="group block h-full">
          <div
            class="bg-white rounded-2xl p-6 shadow-sm border border-black/5 h-full flex flex-col justify-between transition-all duration-300 group-hover:-translate-y-1 group-hover:shadow-md group-hover:border-fourth/40"
          >
            <h2 class="text-text-primary font-semibold mb-2">Total Reports</h2>
            <p class="text-[#F4B6A8] text-sm">
              <span class="font-bold">{{ totalOnprogressReports }}</span> Reports On Progress
            </p>
            <p class="text-[#8EDFC3] text-sm">
              <span class="font-bold">{{ totalDoneReports }}</span> Reports Done
            </p>
          </div>
        </RouterLink>
      </div>
    </section>
    <!-- Table Section -->
    <section class="bg-white rounded-2xl p-6 shadow-sm border border-black/5">
      <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center">
        <h1 class="font-semibold text-text-secondary">Reports</h1>
      </div>
      <p v-if="loading" class="text-third">Loading...</p>
      <ul v-else class="space-y-2">
        <li
          v-for="report in reports"
          :key="report.id"
          class="bg-zinc-800 p-3 rounded-md text-white"
        >
          {{ report.projects.name }} - {{ report.title }}
        </li>
      </ul>
    </section>
  </main>
</template>
