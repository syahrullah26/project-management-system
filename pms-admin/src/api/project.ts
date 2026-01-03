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
