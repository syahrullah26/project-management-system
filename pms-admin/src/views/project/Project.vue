<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { createProject, getProject, updateProject } from '@/api/project'
import type {  Project } from '@/api/project'
import { RouterLink } from 'vue-router'
defineOptions({ name: 'ProjectList' })

const project = ref<Project[]>([])
const submitting = ref(false)
const loadingProject = ref(false)
const showProjectForm = ref(false)

const toast = ref<{ message: string; type: 'success' | 'error' } | null>(null)

const showToast = (message: string, type: 'success' | 'error') => {
  toast.value = { message, type }
  setTimeout(() => (toast.value = null), 3000)
}

// state form
const projectID = ref<number | null>(null)
const namaProject = ref('')

// EDIT MODE

const isEditting = ref(false)

const editProject = (dataProject: Project) => {
  showProjectForm.value = true
  isEditting.value = true
  projectID.value = dataProject.id
  namaProject.value = dataProject.name
}

const loadProject = async () => {
  loadingProject.value = true
  try {
    const [projectData] = await Promise.all([getProject()])
    project.value = projectData.data
  } catch {
    showToast('Gagal Mengambil Data Project', 'error')
  } finally {
    loadingProject.value = false
  }
}

const submitProject = async () => {
  if (!namaProject.value) {
    showToast('Semua field wajib disis', 'error')
    return
  }

  submitting.value = true
  try {
    if (isEditting.value && projectID.value) {
      const formData = new FormData()
      formData.append('name', namaProject.value)
      await updateProject(projectID.value, {
        name : namaProject.value
      })
      showToast('Project berhasil diedit', 'success')
    } else {
      const formData = new FormData()
      formData.append('name', namaProject.value)
      await createProject(formData)
      showToast('Project berhasil dibuat', 'success')
    }
    resetForm()

    await loadProject()
  } catch {
    showToast('Gagal membuat project', 'error')
  } finally {
    submitting.value = false
  }
}

onMounted(loadProject)
// const cancelEdit = () => resetForm()
const resetForm = () => {
  namaProject.value = ''
}
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
              Form Project
            </h1>

            <button
              @click="showProjectForm = !showProjectForm"
              class="text-sm px-3 py-2 rounded-md border border-text-secondary text-text-primary bg-linear-to-r from-third to-secondary hover:from-third to-primary transition"
            >
              {{ showProjectForm ? 'Hide' : 'Show' }}
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
            <div v-if="showProjectForm" class="space-y-6 overflow-hidden mt-4">
              <div class="flex justify-center">
                <div class="w-full max-w-3xl">
                  <div class="form-group">
                    <label for="name" class="form-label">Nama Project</label>
                    <input
                      type="text"
                      class="form-input"
                      placeholder="Masukan Nama Project"
                      id="name"
                      v-model="namaProject"
                      required
                    />
                  </div>
                  <div class="flex justify-center mt-4">
                    <button
                      @click="submitProject"
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
              </div>
            </div>
          </transition>
        </div>
      </div>
    </section>

    <section>
      <div
        class="bg-white rounded-2xl p-6 shadow-sm border border-black/5 h-full flex flex-col justify-between"
      >
        <div class="flex justify-center">
          <h1 class="text-text-secondary font-bold text-lg">Project List</h1>
        </div>
        <hr class="border-text-secondary space-y-4 mt-4 mb-4" />
        <p v-if="loadingProject" class="text-third">Loading...</p>
        <div class="relative overflow-x-auto rounded-xl border border-black/10 shadow-sm">
          <table class="min-w-full text-sm text-left">
            <!-- HEADER -->
            <thead class="bg-secondary text-text-secondary sticky top-0 z-10">
              <th class="border px-4 py-4">No</th>
              <th class="border px-4 py-4">Nama Project</th>
              <th class="border px-4 py-4">Action</th>
            </thead>
            <tr v-for="(data, index) in project" :key="data.id">
              <td class="border px-4 py-4">{{ index + 1 }}</td>
              <td class="border px-4 py-4">{{ data.name }}</td>
              <td class="border px-4 py-4">
                <button
                  @click="editProject(data)"
                  class="text-xs px-3 py-1 rounded-md bg-linear-to-r from-third to-secondary text-text-primary border border-black/5 hover:from-third hover:to-primary transition"
                >
                  Edit
                </button>
              </td>
            </tr>
          </table>
        </div>
      </div>
    </section>
  </main>
</template>
