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

const search = ref('')
const currentPage = ref(1)
const perPage = 5

const filteredData = computed(() =>
  reports.value.filter((r) => r.title.toLowerCase().includes(search.value.toLowerCase())),
)

const paginatedData = computed(() => {
  const start = (currentPage.value - 1) * perPage
  return filteredData.value.slice(start, start + perPage)
})

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
    <section>
      <div class="gap-4 auto-rows-fr">
        <div
          class="bg-white rounded-2xl p-6 shadow-sm border border-black/5 h-full flex flex-col justify-between"
        >
          <div class="flex justify-center">
            <h1 class="text-text-secondary text-xl font-bold">Reports Data</h1>
          </div>
          <hr class="border-text-primary mt-4 mb-4" />
          <div class="grid grid-cols-3 mb-3">
            <input v-model="search" placeholder="Search..." class="form-input mb-2" />
          </div>
          <div class="relative overflow-x-auto rounded-xl border border-black/10 shadow-sm">
            <table class="min-w-full text-sm text-left">
              <!-- HEADER -->
              <thead class="bg-secondary text-text-secondary sticky top-0 z-10">
                <tr>
                  <th class="px-4 py-3 border">No</th>
                  <th class="px-4 py-3 border">Title</th>
                  <th class="px-4 py-3 border">Project</th>
                  <th class="px-4 py-3 border">Description</th>
                  <th class="px-4 py-3 border">Type</th>
                  <th class="px-4 py-3 border">Priority</th>
                  <th class="px-4 py-3 border">Status</th>
                </tr>
              </thead>

              <!-- BODY -->
              <tbody>
                <tr
                  v-for="(item, index) in paginatedData"
                  :key="item.id"
                  class="odd:bg-white even:bg-secondary/40 hover:bg-secondary/70 transition"
                >
                  <td class="px-4 py-3 border font-medium">
                    {{ index + 1 }}
                  </td>

                  <td class="px-4 py-3 border">
                    {{ item.title }}
                  </td>

                  <td class="px-4 py-3 border">
                    {{ item.project.name ?? '-' }}
                  </td>

                  <td class="px-4 py-3 border max-w-xs truncate">
                    {{ item.description }}
                  </td>

                  <td class="px-4 py-3 border capitalize">
                    {{ item.type }}
                  </td>

                  <td class="px-4 py-3 border capitalize">
                    {{ item.priority }}
                  </td>

                  <!-- STATUS BADGE -->
                  <td class="px-4 py-3 border">
                    <span
                      v-if="item.status === 'onprogress'"
                      class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-700"
                    >
                      On Progress
                    </span>
                    <span
                      v-else
                      class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700"
                    >
                      Done
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="flex items-center justify-center gap-3 mt-6">
            <button
              @click="currentPage--"
              :disabled="currentPage === 1"
              class="px-4 py-2 text-sm font-medium rounded-lg border bg-white text-gray-700 hover:bg-gray-100 hover:text-gray-900 disabled:opacity-40 disabled:cursor-not-allowed transition"
            >
              ◀ Prev
            </button>

            <span class="px-4 py-2 text-sm font-semibold text-gray-600">
              Page {{ currentPage }}
            </span>

            <button
              @click="currentPage++"
              :disabled="currentPage * perPage >= filteredData.length"
              class="px-4 py-2 text-sm font-medium rounded-lg border bg-white text-gray-700 hover:bg-gray-100 hover:text-gray-900 disabled:opacity-40 disabled:cursor-not-allowed transition"
            >
              Next ▶
            </button>
          </div>
        </div>
      </div>
    </section>
  </main>
</template>
