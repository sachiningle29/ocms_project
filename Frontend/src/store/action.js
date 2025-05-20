import axiosClient from "../axios";

export async function Adminlogin({ commit }, credentials) {
  try {
    const { data } = await axiosClient.post('/login/admin', credentials);
    commit('setUser', data.user);
    commit('setToken', data.token);
    
    // Store remember me state if needed
    if (credentials.remember) {
      localStorage.setItem('REMEMBER_ME', 'true');
    } else {
      sessionStorage.setItem('REMEMBER_ME', 'false');
    }
    
    return data;
  } catch (error) {
    // Transform error for consistent handling
    throw new Error(error.response?.data?.message || 'Login failed');
  }
}

export async function Userlogin({ commit }, credentials) {
  try {
    const { data } = await axiosClient.post('/login/user', credentials);
    commit('setUser', data.user);
    commit('setToken', data.token);
    
    // Store remember me state if needed
    if (credentials.remember) {
      localStorage.setItem('REMEMBER_ME', 'true');
    } else {
      sessionStorage.setItem('REMEMBER_ME', 'false');
    }
    
    return data;
  } catch (error) {
    // Transform error for consistent handling
    throw new Error(error.response?.data?.message || 'Login failed');
  }
}

export async function logout({ commit }) {
  try {
    await axiosClient.post('/logout');
    commit('setToken', null);
    commit('setUser', null);  // Clear user data
    
    // Clear storage based on remember me
    if (localStorage.getItem('REMEMBER_ME') === 'true') {
      localStorage.removeItem('REMEMBER_ME');
    } else {
      sessionStorage.clear();
    }
    
    return true;
  } catch (error) {
    console.error('Logout failed:', error);
    throw error;
  }
}