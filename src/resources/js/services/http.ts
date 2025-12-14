import axios, { AxiosError, AxiosRequestConfig } from 'axios';

// Axios instance configured for Laravel Sanctum / cookie-based auth
export const http = axios.create({
  baseURL: window.location.origin,
  withCredentials: true,
  xsrfCookieName: 'XSRF-TOKEN',
  xsrfHeaderName: 'X-XSRF-TOKEN',
});

// Bootstrap CSRF cookie before auth-protected requests
export async function ensureCsrfCookie(): Promise<void> {
  await http.get('/sanctum/csrf-cookie');
}

// Interceptors
http.interceptors.response.use(
  (response) => response,
  async (error: AxiosError) => {
    const status = error.response?.status;
    const config = error.config as (AxiosRequestConfig & { _retry?: boolean });

    // 419: CSRF token mismatch/expired — refresh token and retry once
    if (status === 419 && !config?._retry) {
      try {
        await ensureCsrfCookie();
        config._retry = true;
        return http.request(config);
      } catch (e) {
        // fallthrough to redirect
      }
    }

    // 401: unauthorized — redirect to login
    if (status === 401) {
      const currentPath = window.location.pathname + window.location.search;
      if (!currentPath.startsWith('/login')) {
        const redirect = encodeURIComponent(currentPath);
        window.location.href = `/login?redirect=${redirect}`;
      }
    }

    return Promise.reject(error);
  },
);

export default http;
