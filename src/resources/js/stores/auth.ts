import { defineStore } from 'pinia';
import http, { ensureCsrfCookie } from '@/services/http';

export interface User {
  id: number;
  name: string;
  email: string;
}

interface AuthState {
  user: User | null;
  loading: boolean;
  error: string | null;
  initialized: boolean;
}

export const useAuthStore = defineStore('auth', {
  state: (): AuthState => ({
    user: null,
    loading: false,
    error: null,
    initialized: false,
  }),
  actions: {
    async fetchUser() {
      this.loading = true; this.error = null;
      try {
        const { data } = await http.get<User>('/user');
        this.user = data;
      } catch (e: any) {
        this.user = null;
        this.error = e?.response?.data?.message ?? null;
      } finally {
        this.loading = false;
        this.initialized = true;
      }
    },
    async login(email: string, password: string) {
      this.loading = true; this.error = null;
      try {
        await ensureCsrfCookie();
        await http.post('/login', { email, password });
        await this.fetchUser();
      } catch (e: any) {
        this.error = e?.response?.data?.message ?? 'Ошибка входа';
        throw e;
      } finally {
        this.loading = false;
      }
    },
    async logout() {
      this.loading = true; this.error = null;
      try {
        await http.post('/logout');
        this.user = null;
      } catch (e: any) {
        this.error = e?.response?.data?.message ?? 'Ошибка выхода';
      } finally {
        this.loading = false;
        this.initialized = true;
      }
    },
    resetError() {
      this.error = null;
    },
  },
});
