import axios from 'axios';

const axiosClient = axios.create({
//   baseURL: 'http://10.205.151.96:8000/api', // This is the key part
//changed URL BY sachin on 30-06-26
  baseURL: "http://127.0.0.1:8000/api",
// baseURL: "http://localhost:8000/api",
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json'
  }
});
// Add request interceptor for auth token if needed
axiosClient.interceptors.request.use((config) => {
  const token = sessionStorage.getItem('TOKEN');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});
export default axiosClient;


