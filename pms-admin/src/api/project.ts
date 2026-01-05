import axios from './axios'

export interface Project {
    id : number
    name : string
}

export interface PaginatedProject {
    data : Project[]
    current_page : number
    last_page : number 
}


export const getProject = async(page = 1): Promise<PaginatedProject> => {
    const res = await axios.get('/project', {
        params: {page},
    })
    return res.data
}

export const createProject = async(data: FormData): Promise<Project> =>{
    const res = await axios.post('/project', data,{
        headers: {"Content-Type":"multipart/form-data"},
    })
    return res.data.data
}