import axios from "axios";
import store from "./store";
import router from "./router";

const axiosClient = axios.create({
  baseURL: `${import.meta.env.VITE_API_BASE_URL}/api`,
});

// Add Authorization header with token on every request
axiosClient.interceptors.request.use((config) => {
  const token = store.state.user.token;
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Handle response errors globally
axiosClient.interceptors.response.use(
  (response) => {
    return response;
  },
  (error) => {
    if (error.response?.status === 401) {
      // Clear token and redirect to login page on unauthorized
      sessionStorage.removeItem("TOKEN");
      router.push({ name: "UserLogin" }); // Make sure route name matches
    }
    return Promise.reject(error);
  }
);

export default axiosClient;
