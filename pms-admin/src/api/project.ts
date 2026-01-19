import axios from './axios'

export interface Project {
  id: number
  name: string
}

export interface PaginatedProject {
  data: Project[]
  current_page: number
  last_page: number
}

export const getProject = async (page = 1): Promise<PaginatedProject> => {
  const res = await axios.get('/projects', {
    params: { page },
  })
  return res.data
}

export const createProject = async (data: FormData): Promise<Project> => {
  const res = await axios.post('/projects', data, {
    headers: { 'Content-Type': 'multipart/form-data' },
  })
  return res.data.data
}

export const updateProject = async (id: number, data: { name: string }): Promise<Project> => {
  const res = await axios.put(`/projects/${id}`, data)
  return res.data.data
}
