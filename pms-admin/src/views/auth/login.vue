<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import AOS from 'aos'
import 'aos/dist/aos.css'

defineOptions({ name: 'LoginView' })

const router = useRouter()
const auth = useAuthStore()

const email = ref('')
const password = ref('')
const loading = ref(false)
const errorMessage = ref('')

onMounted(() => {
  AOS.init({ duration: 1000, once: true })
})

const submitLogin = async () => {
  loading.value = true
  errorMessage.value = ''

  try {
    await auth.login({
      email: email.value,
      password: password.value,
    })

    router.push('/')
  } catch (error: any) {
    errorMessage.value = error.response?.data?.message || 'Login gagal'
    console.log(error)
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-secondary">
    <div
      class="bg-white rounded-2xl border border-black/30 shadow-xl w-full max-w-md p-8"
      data-aos="fade-right"
    >
      <h2 class="text-2xl font-bold text-gray-800 text-center mb-6">Login ke PMS</h2>

      <form @submit.prevent="submitLogin" class="space-y-5">
        <div>
          <label class="form-label"> Email </label>
          <input
            v-model="email"
            type="email"
            required
            class="form-input"
            placeholder="you@email.com"
          />
        </div>

        <div>
          <label class="form-label"> Password </label>
          <input
            v-model="password"
            type="password"
            required
            class="form-input"
            placeholder="••••••••"
          />
        </div>

        <p v-if="errorMessage" class="text-sm text-red-600 text-center" data-aos="fade-in">
          {{ errorMessage }}
        </p>

        <button
          type="submit"
          :disabled="loading"
          class="w-full bg-linear-to-r from-third to-fourth text-white py-2 rounded-lg border border-black/30 font-semibold transition-all duration-300 ease-out hover:brightness-110 hover:shadow-lg active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed"
        >
          {{ loading ? 'Loading...' : 'Login' }}
        </button>
        <p class="text-sm text-text-primary text-center mt-6">
          Belum Punya Akun?
          <RouterLink :to="{ name: 'register' }" class="text-text-secondary font-semibold cursor-pointer hover:underline hover:text-indigo-600">
            Daftar
          </RouterLink>
        </p>
      </form>

      <p class="text-xs text-gray-400 text-center mt-6">© 2026 PMS System</p>
    </div>
  </div>
</template>
