import { defineStore } from 'pinia';
import http from '@/services/http';

interface BalanceState {
  value: string | null;
  loading: boolean;
  error: string | null;
}

export const useBalanceStore = defineStore('balance', {
  state: (): BalanceState => ({
    value: null,
    loading: false,
    error: null,
  }),
  actions: {
    async fetchBalance() {
      this.loading = true; this.error = null;
      try {
        const { data } = await http.get<{ balance: string }>('/api/balance');
        this.value = data.balance;
      } catch (e: any) {
        this.error = e?.response?.data?.message ?? 'Не удалось получить баланс';
      } finally {
        this.loading = false;
      }
    },
    reset() {
      this.value = null;
      this.error = null;
      this.loading = false;
    },
  },
});
