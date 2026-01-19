import { defineStore } from 'pinia'
import {nextTick} from 'vue'
import { loginRequest, registerRequest, logoutRequest } from '@/api/authService'
import type { LoginPayload, RegisterPayload } from '@/api/authService'
import router from '@/router'

function safeParse<T>(value: string | null): T | null {
  if (!value || value === 'undefined') return null

  try {
    return JSON.parse(value) as T
  } catch {
    return null
  }
}

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: safeParse<any>(localStorage.getItem('user')),
    token: localStorage.getItem('token'),
  }),

  getters: {
    isAuthenticated: (state) => Boolean(state.token),
  },

  actions: {
    async login(payload: LoginPayload) {
      try {
        const res = await loginRequest(payload)

        this.user = res.user
        this.token = res.token

        localStorage.setItem('user', JSON.stringify(res.user))
        localStorage.setItem('token', res.token)

        router.push({ name: 'dashboard' })
      } catch (error) {
        throw error
      }
    },

    async register(payload: RegisterPayload) {
      try {
        const res = await registerRequest(payload)

        this.user = res.user
        this.token = res.token

        localStorage.setItem('user', JSON.stringify(res.user))
        localStorage.setItem('token', res.token)

        await nextTick()
        router.push({ name: 'dashboard' })
      } catch (error) {
        throw error
      }
    },

    async logout() {
      try {
        await logoutRequest()
      } catch (error) {
        console.warn('Logout API error, clearing local data anyway')
        console.warn(error)
      } finally {
        this.user = null
        this.token = null

        localStorage.removeItem('user')
        localStorage.removeItem('token')

        router.push({ name: 'login' })
      }
    },
  },
})
