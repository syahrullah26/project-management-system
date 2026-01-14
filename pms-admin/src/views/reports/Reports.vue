<script setup lang="ts">
import { onMounted, ref, computed } from 'vue'
import { RouterLink } from 'vue-router'
import { getProject } from '@/api/project'
import type { Project } from '@/api/project'
import type { Reports } from '@/api/reports'
import { getReports, createReports, updateReports, updateReportsStatus } from '@/api/reports'
import DetailModal from '@/components/DetailModal.vue'
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

//FILTER DATA
const statusFilter = ref<'onprogress' | 'done' | null>(null)

// STATE MODAL
const showDetailModal = ref(false)
const selectedReport = ref<any>(null)

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

const filteredData = computed(() => {
  return reports.value
    .filter((r) => r.title.toLowerCase().includes(search.value.toLowerCase()))
    .filter((r) => {
      if (statusFilter.value === null) return true
      return r.status === statusFilter.value
    })
})

const paginatedData = computed(() => {
  const start = (currentPage.value - 1) * perPage
  return filteredData.value.slice(start, start + perPage)
})

const openDetail = (report: any) => {
  selectedReport.value = report
  showDetailModal.value = true
}

// LOAD ALL DATA
const loadAllData = async () => {
  loadingProject.value = true
  loadingReports.value = true
  try {
    const [projectData, reportsData] = await Promise.all([
      getProject(),
      getReports(currentPage.value, statusFilter.value),
    ])
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
    <section data-aos="fade-left" data-aos-duration="1000">
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
            <div v-if="showReportForm" data-aos="fade-down" data-aos-duration="600">
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
    <section data-aos="fade-up" data-aos-duration="1000">
      <div class="gap-4 auto-rows-fr">
        <div
          class="bg-white rounded-2xl p-6 shadow-sm border border-black/5 h-full flex flex-col justify-between"
        >
          <div class="flex justify-center">
            <h1 class="text-text-secondary text-xl font-bold">Reports Data</h1>
          </div>
          <hr class="border-text-primary mt-4 mb-4" />
          <div class="flex gap-3 mb-4">
            <button
              @click="statusFilter = null"
              :class="statusFilter === null ? 'bg-linear-to-r from-third to-secondary text-text-secondary' : 'bg-secondary'"
              class="px-3 py-1 rounded-md text-sm border transition cursor-pointer hover:bg-third transition-all"
            >
              All
            </button>

            <button
              @click="statusFilter = 'onprogress'"
              :class="statusFilter === 'onprogress' ? 'bg-linear-to-r from-orange-200 to-secondary text-text-secondary' : 'bg-secondary'"
              class="px-3 py-1 rounded-md text-sm border transition cursor-pointer hover:bg-orange-200 transition-all"
            >
              On Progress
            </button>

            <button
              @click="statusFilter = 'done'"
              :class="statusFilter === 'done' ? 'bg-linear-to-r from-green-200 to-secondary text-text-secondary' : 'bg-secondary'"
              class="px-3 py-1 rounded-md text-sm border transition cursor-pointer hover:bg-green-200 transition-all"
            >
              Done
            </button>
          </div>

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
                  <th class="px-4 py-3 border text-center" colspan="3">Action</th>
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
                      @click="openDetail(item)"
                      class="rounded-md bg-gradient-to-r from-[#3F66D6] to-secondary border border-black/5 px-3 py-1 text-xs text-text-primary hover:from-[#2F55C6] hover:to-primary transition-all duration-300 cursor-pointer"
                    >
                      Detail
                    </button>
                  </td>
                  <td class="px-4 py-3 border text-center">
                    <button
                      v-if="item.status === 'onprogress'"
                      @click="markAsDone(item.id)"
                      class="text-xs px-3 py-1 rounded-md bg-linear-to-r from-primary to-secondary text-text-primary border border-black/5 hover:from-fourth hover:to-third transition-all duration-300 cursor-pointer"
                    >
                      Mark as Done
                    </button>
                    <span v-else class="text-xs text-gray-400 italic"> — </span>
                  </td>
                  <td class="px-4 py-3 border text-center">
                    <button
                      @click="editReports(item)"
                      class="text-xs px-3 py-1 rounded-md bg-linear-to-r from-third to-secondary text-text-primary border border-black/5 hover:from-third hover:to-primary transition cursor-pointer"
                    >
                      Edit
                    </button>
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
  <DetailModal v-model="showDetailModal">
    <div v-if="selectedReport" class="space-y-6 text-sm text-gray-700">
      <div class="space-y-2">
        <h4 class="text-lg font-semibold leading-tight text-text-secondary">
          {{ selectedReport.title }}
        </h4>
        <div class="flex items-center gap-2 text-xs text-gray-500">
          <span>Project :</span>
          <span
            class="max-w-full truncate rounded-full bg-secondary px-3 py-1 font-semibold text-text-secondary"
          >
            {{ selectedReport.project.name }}
          </span>
        </div>
      </div>

      <div class="rounded-2xl border border-black/5 bg-secondary/30 p-4">
        <p class="mb-2 text-xs font-semibold text-gray-500">Description</p>
        <p class="leading-relaxed">
          {{ selectedReport.description }}
        </p>
      </div>

      <div class="grid grid-cols-3 gap-4">
        <div class="rounded-xl border border-black/5 bg-white p-3 shadow-sm">
          <p class="mb-2 text-xs text-gray-500">Type</p>
          <span
            class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold"
            :class="
              selectedReport.type === 'feature'
                ? 'bg-blue-100 text-blue-700'
                : 'bg-red-100 text-red-700'
            "
          >
            {{ selectedReport.type }}
          </span>
        </div>

        <div class="rounded-xl border border-black/5 bg-white p-3 shadow-sm">
          <p class="mb-2 text-xs text-gray-500">Priority</p>
          <span
            class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold"
            :class="{
              'bg-green-100 text-green-700': selectedReport.priority === 'low',
              'bg-yellow-100 text-yellow-700': selectedReport.priority === 'medium',
              'bg-red-100 text-red-700': selectedReport.priority === 'high',
            }"
          >
            {{ selectedReport.priority }}
          </span>
        </div>

        <div class="rounded-xl border border-black/5 bg-white p-3 shadow-sm">
          <p class="mb-2 text-xs text-gray-500">Status</p>
          <span
            class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold"
            :class="
              selectedReport.status === 'onprogress'
                ? 'bg-orange-100 text-orange-700'
                : 'bg-green-100 text-green-700'
            "
          >
            {{ selectedReport.status === 'onprogress' ? 'On Progress' : 'Done' }}
          </span>
        </div>
      </div>
    </div>
  </DetailModal>
</template>
