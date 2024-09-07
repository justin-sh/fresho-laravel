import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

axios.interceptors.response.use(null, (error) => {
    if (error.response.status === 401) {
        //no login
        let cb = location.pathname ?? '/'
        window.location.href = '/login?t=' + cb
        return
    }
    return Promise.reject(error);
})
