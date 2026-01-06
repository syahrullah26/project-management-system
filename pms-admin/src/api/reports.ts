import axios from './axios'

export interface Reports {
  id: number
  project: {
    id: number
    name: string
  }
  title: string
  description: string
  type: 'feature' | 'bug'
  status: 'onprogress' | 'done'
  priority: 'low' | 'medium' | 'high'
}

export interface CreateReportsPayload {
  project_id: number
  title: string
  description: string
  type: 'feature' | 'bug'
  status: 'onprogress' | 'done'
  priority: 'low' | 'medium' | 'high'
}

export interface UpdateReportStatusPayload {
  status: 'onprogress' | 'done'
}

export type ReportStatus = 'onprogress' | 'done' | null

export interface PaginatedReports {
  data: Reports[]
  current_page: number
  last_page: number
}

export const getReports = async (page = 1, status?: ReportStatus): Promise<PaginatedReports> => {
  const res = await axios.get('/reports', {
    params: {
      page,
      ...(status && { status }),
    },
  })

  return res.data
}

export const createReports = async (payload: CreateReportsPayload): Promise<Reports> => {
  const res = await axios.post('/reports', payload)
  return res.data.data
}

export const updateReports = async (
  id: number,
  payload: CreateReportsPayload,
): Promise<Reports> => {
  const res = await axios.put(`/reports/${id}`, payload)
  return res.data
}

export const updateReportsStatus = async (
  id: number,
  payload: UpdateReportStatusPayload,
): Promise<Reports> => {
  const res = await axios.patch(`/reports/${id}/status`, payload)
  return res.data.data
}
