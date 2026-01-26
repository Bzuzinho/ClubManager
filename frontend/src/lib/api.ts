import axios, { AxiosError } from 'axios';
import { captureException, addBreadcrumb } from './sentry';
import { logger } from './logger';

// Create axios instance
const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000/api',
  timeout: 30000,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

// Request interceptor
api.interceptors.request.use(
  config => {
    const startTime = performance.now();
    (config as any).metadata = { startTime };
    
    // Add auth token
    const token = localStorage.getItem('auth_token') || localStorage.getItem('token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    
    // Add active club ID
    const activeClubId = localStorage.getItem('active_club_id');
    if (activeClubId) {
      config.headers['X-Club-Id'] = activeClubId;
    }
    
    // Add breadcrumb
    addBreadcrumb(
      `${config.method?.toUpperCase()} ${config.url}`,
      'http',
      'info'
    );
    
    logger.debug(`API Request: ${config.method?.toUpperCase()} ${config.url}`);
    
    return config;
  },
  error => {
    captureException(error, { context: 'request_interceptor' });
    return Promise.reject(error);
  }
);

// Response interceptor
api.interceptors.response.use(
  response => {
    const { config } = response;
    const endTime = performance.now();
    const duration = endTime - ((config as any).metadata?.startTime || endTime);
    
    // Log API request
    logger.apiRequest(
      config.method?.toUpperCase() || 'GET',
      config.url || '',
      response.status,
      duration
    );
    
    addBreadcrumb(
      `Response ${response.status} from ${config.url}`,
      'http',
      'info'
    );
    
    return response;
  },
  (error: AxiosError) => {
    const { response, config } = error;
    
    // Log error
    addBreadcrumb(
      `Error ${response?.status || 'NETWORK'} from ${config?.url}`,
      'http',
      'error'
    );
    
    logger.error(
      `API Error: ${config?.method?.toUpperCase()} ${config?.url}`,
      error,
      { status: response?.status, data: response?.data }
    );
    
    // Handle authentication errors
    if (response?.status === 401) {
      localStorage.removeItem('auth_token');
      localStorage.removeItem('token');
      localStorage.removeItem('user');
      window.location.href = '/login';
      return Promise.reject(error);
    }
    
    // Handle authorization errors
    if (response?.status === 403) {
      logger.warn('Access forbidden', { url: config?.url });
      return Promise.reject(error);
    }
    
    // Capture unexpected errors
    if (response && response.status >= 500) {
      captureException(error, {
        url: config?.url,
        method: config?.method,
        status: response.status,
        data: response.data,
      });
    }
    
    return Promise.reject(error);
  }
);

// Type-safe API methods
export const apiClient = {
  get: <T>(url: string, config?: any) => api.get<T>(url, config),
  post: <T>(url: string, data?: any, config?: any) => api.post<T>(url, data, config),
  put: <T>(url: string, data?: any, config?: any) => api.put<T>(url, data, config),
  delete: <T>(url: string, config?: any) => api.delete<T>(url, config),
  patch: <T>(url: string, data?: any, config?: any) => api.patch<T>(url, data, config),
};

export default api;
