<script setup lang="ts">
import { ref } from 'vue'
import { useAuthStore } from '@/stores/auth'
import AOS from 'aos'

defineOptions({ name: 'RegisterView' })

const auth = useAuthStore()

const name = ref('')
const email = ref('')
const password = ref('')
const passwordConfirmation = ref('')
const errorMessage = ref('')
const loading = ref(false)

const submitRegister = async () => {
  errorMessage.value = ''
  loading.value = true

  try {
    await auth.register({
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: passwordConfirmation.value,
    })
  } catch (error: any) {
    errorMessage.value = error.response?.data?.message || 'Registrasi gagal'
    AOS.refresh()
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-secondary">
    <div
      class="w-full max-w-md bg-white p-8 rounded-2xl shadow-lg border border-black/30"
      data-aos="fade-left"
    >
      <h1 class="text-2xl font-bold text-center mb-6">Register</h1>

      <form @submit.prevent="submitRegister" class="space-y-4">
        <input v-model="name" type="text" placeholder="Nama" class="form-input" />

        <input v-model="email" type="email" placeholder="Email" class="form-input" />

        <input v-model="password" type="password" placeholder="Password" class="form-input" />

        <input
          v-model="passwordConfirmation"
          type="password"
          placeholder="Konfirmasi Password"
          class="form-input"
        />

        <p v-show="errorMessage" class="text-sm text-red-600 text-center">
          {{ errorMessage }}
        </p>

        <button
          type="submit"
          :disabled="loading"
          class="w-full bg-linear-to-r from-third to-primary border border-black/30 text-text-primary cursor-pointer py-2 rounded-lg font-semibold transition hover:scale-[1.02] disabled:opacity-50"
        >
          {{ loading ? 'Loading...' : 'Register' }}
        </button>
      </form>

      <p class="text-sm text-center mt-4">
        Sudah punya akun?
        <RouterLink to="/login" class="text-text-primary font-semibold hover:underline hover:text-indigo-600">
          Login
        </RouterLink>
      </p>
    </div>
    
  </div>
</template>
