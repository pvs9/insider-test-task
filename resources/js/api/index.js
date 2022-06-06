import axios from 'axios';
import router from "../router";

const api = axios.create({
    'baseURL': process.env.VUE_APP_BASE_URL || 'http://localhost/api',
    headers: {
        Accept: "application/json",
        "Content-Type": "application/json"
    }
});

api.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

api.interceptors.response.use(null, error => {
    if (error.response.status === 404) {
        router.push('/404')
    }

    return Promise.reject(error);
});

export default api;
