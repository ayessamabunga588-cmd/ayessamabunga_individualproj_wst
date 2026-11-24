import axios from 'axios';

export const API_ORIGIN = 'http://localhost:8000';

const api = axios.create({
  baseURL: `${API_ORIGIN}/api`,
  withCredentials: true,
  // Required for cross-origin requests so axios forwards the XSRF-TOKEN cookie
  // back as the X-XSRF-TOKEN header (Sanctum CSRF protection).
  withXSRFToken: true,
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
});

export default api;
