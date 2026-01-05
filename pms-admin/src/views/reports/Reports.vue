<script setup lang="ts">
import { onMounted, ref, computed } from 'vue'
import { RouterLink } from 'vue-router'
import { getProject } from '@/api/project'
import type { Project } from '@/api/project'
import type { Reports } from '@/api/reports'
import { getReports, createReports, updateReports, updateReportsStatus } from '@/api/reports'
defineOptions({ name: 'ReportsList' })

const reports = ref<Reports[]>([])
const project = ref<Project[]>([])
const submitting = ref(false)

// STATE FORM
const title = ref('')
const description = ref('')
const projectByID = ref<number | null>(null)
const typeReports = ref<'feature' | 'bug' | null>(null)
const priority = ref<'low' | 'medium' | 'high' | null>(null)
const status = ref<'onprogress' | 'done' | null>(null)

// STATE LOAD AND TOAST
const loadingProject = ref(false)
const loadingReports = ref(false)
const showReportForm = ref(false)

// EDIT SET
const editingReportsID = ref<number | null>(null)
const isEditing = ref(false)

const toast = ref<{ message: string; type: 'success' | 'error' } | null>(null)

const showToast = (message: string, type: 'success' | 'error') => {
  toast.value = { message, type }
  setTimeout(() => (toast.value = null), 3000)
}

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

// LOAD ALL DATA
const loadAllData = async () => {
  loadingProject.value = true
  loadingReports.value = true
  try {
    const [projectData, reportsData] = await Promise.all([getProject(), getReports()])
    project.value = projectData.data
    reports.value = reportsData.data
  } catch {
    showToast('Gagal Mengambil Data Project', 'error')
  } finally {
    loadingProject.value = false
    loadingReports.value = false
  }
}
const editReports = (dataReport: Reports) => {
  showReportForm.value = true
  isEditing.value = true
  editingReportsID.value = dataReport.id

  projectByID.value = dataReport.project.id
  title.value = dataReport.title
  description.value = dataReport.description
  typeReports.value = dataReport.type
  priority.value = dataReport.priority
  status.value = dataReport.status
}

const markAsDone = async (id: number) => {
  try {
    await updateReportsStatus(id, { status: 'done' })
    showToast('Status berhasil diubah', 'success')
    await loadAllData()
  } catch {
    showToast('Gagal mengubah status', 'error')
  }
}

const submitReports = async () => {
  if (
    !title.value ||
    !projectByID.value ||
    !description.value ||
    !typeReports.value ||
    !priority.value ||
    !status.value
  ) {
    showToast('Semua field wajib diisi', 'error')
    return
  }

  submitting.value = true
  try {
    if (isEditing.value && editingReportsID.value) {
      await updateReports(editingReportsID.value, {
        project_id: projectByID.value,
        title: title.value,
        description: description.value,
        type: typeReports.value,
        priority: priority.value,
        status: status.value,
      })
      showToast('Reports berhasil diedit', 'success')
    } else {
      await createReports({
        project_id: projectByID.value,
        title: title.value,
        description: description.value,
        type: typeReports.value,
        priority: priority.value,
        status: status.value,
      })
      showToast('Reports berhasil dibuat', 'success')
    }

    // reset form
    title.value = ''
    description.value = ''
    projectByID.value = null
    typeReports.value = null
    priority.value = null
    status.value = null

    await loadAllData()
  } catch {
    showToast('Gagal membuat reports', 'error')
  } finally {
    submitting.value = false
  }
}

onMounted(loadAllData)
</script>
<template>
  <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <section>
      <div class="gap-4 auto-rows-fr">
        <div
          class="bg-white rounded-2xl p-6 shadow-sm border border-black/5 h-full flex flex-col justify-between"
        >
          <div class="relative flex items-center justify-between">
            <RouterLink
              to="/"
              class="flex items-center gap-2 text-sm shadow-md border border-black/5 px-3 py-2 rounded-md bg-linear-to-r from-third to-secondary hover:from-third hover:to-primary text-text-primary transition"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M15 19l-7-7 7-7"
                />
              </svg>
              Back
            </RouterLink>

            <h1
              class="absolute left-1/2 -translate-x-1/2 text-text-secondary text-xl font-semibold"
            >
              Form Reports
            </h1>

            <button
              @click="showReportForm = !showReportForm"
              class="text-sm px-3 py-2 rounded-md border border-text-secondary text-text-primary bg-linear-to-r from-third to-secondary hover:from-third to-primary transition"
            >
              {{ showReportForm ? 'Hide' : 'Show' }}
            </button>
          </div>
          <hr class="border-text-primary mt-4" />

          <transition
            enter-active-class="transition-all duration-300"
            leave-active-class="transition-all duration-300"
            enter-from-class="opacity-0 max-h-0"
            enter-to-class="opacity-100 max-h-[1000px]"
            leave-from-class="opacity-100 max-h-[1000px]"
            leave-to-class="opacity-0 max-h-0"
          >
            <div v-if="showReportForm">
              <div class="grid grid-cols-2 md:grid-cols-1 gap-6 mt-4">
                <div class="form-group">
                  <label for="title" class="form-label">Title Report</label>
                  <input
                    v-model="title"
                    type="text"
                    class="form-input"
                    placeholder="Masukan Reports (Feature / Bugs)"
                  />
                </div>
                <div class="form-group">
                  <label for="project">Projects</label>
                  <div class="md:col-span-2">
                    <select v-model="projectByID" class="form-input w-full">
                      <option value="" disabled>Pilih Project</option>
                      <option v-for="projects in project" :key="projects.id" :value="projects.id">
                        {{ projects.name }}
                      </option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="form-group mt-4">
                <label for="description">Description</label>
                <textarea
                  v-model="description"
                  class="bg-secondary rounded-2xl border border-black/5"
                  rows="5"
                  cols="15"
                  placeholder="Masukan Deskripsi Reports"
                ></textarea>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
                <div class="form-group">
                  <label>Type Reports</label>
                  <select v-model="typeReports" class="form-input w-full">
                    <option value="" disabled>Pilih Type</option>
                    <option value="feature">Feature</option>
                    <option value="bug">Bug</option>
                  </select>
                </div>

                <div class="form-group">
                  <label>Priority</label>
                  <select v-model="priority" class="form-input w-full">
                    <option value="" disabled>Pilih Priority</option>
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                  </select>
                </div>

                <div class="form-group">
                  <label>Status</label>
                  <select v-model="status" class="form-input w-full">
                    <option value="" disabled>Pilih Status</option>
                    <option value="onprogress">On Progress</option>
                    <option value="done">Done</option>
                  </select>
                </div>
              </div>
              <div class="flex justify-center mt-4">
                <button
                  @click="submitReports"
                  :disabled="submitting"
                  class="inline-flex items-center gap-2 text-sm shadow-md border border-black/5 px-3 py-2 rounded-md bg-linear-to-r from-third to-secondary hover:from-third hover:to-primary text-text-primary transition"
                >
                  {{ submitting ? 'Saving...' : 'Submit' }}
                </button>
                <div
                  v-if="toast"
                  class="fixed bottom-6 right-6 px-4 py-3 rounded-lg shadow-lg text-text-primary"
                  :class="toast.type === 'success' ? 'bg-third' : 'bg-red-600'"
                >
                  {{ toast.message }}
                </div>
              </div>
            </div>
          </transition>
        </div>
      </div>
    </section>
    <!-- TABLE REPORT LIST  -->
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
                  <th class="px-4 py-3 border text-center" colspan="2">Action</th>
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

                  <!-- ACTION -->
                  <td class="px-4 py-3 border text-center">
                    <button
                      @click="editReports(item)"
                      class="text-xs px-3 py-1 rounded-md bg-linear-to-r from-third to-secondary text-text-primary border border-black/5 hover:from-third hover:to-primary transition"
                    >
                      Edit
                    </button>
                  </td>

                  <td class="px-4 py-3 border text-center">
                    <button
                      v-if="item.status === 'onprogress'"
                      @click="markAsDone(item.id)"
                      class="text-xs px-3 py-1 rounded-md bg-linear-to-r from-primary to-secondary text-text-primary border border-black/5 hover:bg-linear-to-r from-primary to-fourth transition"
                    >
                      Mark as Done
                    </button>
                    <span v-else class="text-xs text-gray-400 italic"> — </span>
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
