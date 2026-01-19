import axios from './axios'

export interface LoginPayload {
  email: string
  password: string
}

export interface RegisterPayload {
  name: string
  email: string
  password: string
  password_confirmation: string
}


export const loginRequest = async (payload: { email: string; password: string }) => {
  const { data } = await axios.post('/login', payload)
  return data
}

export const registerRequest = async (payload: RegisterPayload) => {
  const res = await axios.post('/register', payload)
  return res.data
}

export const logoutRequest = async () => {
  await axios.post('/logout')
}
