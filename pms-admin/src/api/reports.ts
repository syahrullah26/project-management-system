import axios from './axios'

export interface Reports {
  id: number
  projects: {
    id: number
    name: string
  }
  title : string
  description : string
  type : 'feature' | 'bug'
  status : 'onprogress' | 'done'
  priority : 'low' | 'medium' | 'high'

}

export interface PaginatedReports {
    data : Reports[]
    current_page : number
    last_page : number 
}

export const getReports = async(page = 1): Promise<PaginatedReports> =>{
    const res = await axios.get('/reports', {
        params : {page},
    })
    return res.data
}
