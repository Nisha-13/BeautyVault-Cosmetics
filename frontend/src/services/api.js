import axios from 'axios';

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000/api/v1',
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  },
});

// Attach Authorization Bearer token and Guest Session ID
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('beautyvault_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }

  let sessionId = localStorage.getItem('beautyvault_session_id');
  if (!sessionId) {
    sessionId = 'sess_' + Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
    localStorage.setItem('beautyvault_session_id', sessionId);
  }
  config.headers['X-Session-ID'] = sessionId;

  return config;
});

// Response interceptor to auto-clear revoked tokens on 401 Unauthorized
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401 && !error.config?.url?.includes('/auth/login')) {
      localStorage.removeItem('beautyvault_token');
      localStorage.removeItem('beautyvault_user');
      window.dispatchEvent(new CustomEvent('beautyvault:unauthorized'));
    }
    return Promise.reject(error);
  }
);

export default api;
