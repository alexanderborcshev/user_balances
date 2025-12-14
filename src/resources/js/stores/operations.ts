import { defineStore } from 'pinia';
import http from '@/services/http';

export interface Operation {
  id: number;
  amount: string; // decimal as string
  description: string;
  created_at: string;
}

interface ListParams {
  page?: number;
  per_page?: number;
  sort?: 'date';
  dir?: 'asc' | 'desc';
  q?: string;
}

interface OperationsState {
  latest: Operation[];
  list: Operation[];
  total: number;
  page: number;
  perPage: number;
  dir: 'asc' | 'desc';
  q: string;
  loading: boolean;
  error: string | null;
}

export const useOperationsStore = defineStore('operations', {
  state: (): OperationsState => ({
    latest: [],
    list: [],
    total: 0,
    page: 1,
    perPage: 10,
    dir: 'desc',
    q: '',
    loading: false,
    error: null,
  }),
  actions: {
    async fetchLatest(limit = 5) {
      this.loading = true; this.error = null;
      try {
        const { data } = await http.get<Operation[]>(`/api/operations/latest`, { params: { limit } });
        this.latest = data;
      } catch (e: any) {
        this.error = e?.response?.data?.message ?? 'Не удалось получить последние операции';
      } finally {
        this.loading = false;
      }
    },
    async fetchList(params: ListParams = {}) {
      this.loading = true; this.error = null;
      try {
        const query = {
          page: params.page ?? this.page,
          per_page: params.per_page ?? this.perPage,
          sort: params.sort ?? 'date',
          dir: params.dir ?? this.dir,
          q: (params.q ?? this.q) || undefined,
        };
        const { data } = await http.get<{ data: Operation[]; total: number; current_page: number; per_page: number }>(`/api/operations`, { params: query });
        this.list = data.data;
        this.total = data.total;
        this.page = data.current_page;
        this.perPage = Number(data.per_page);
        this.dir = (query.dir as 'asc' | 'desc') ?? this.dir;
        this.q = query.q ?? '';
      } catch (e: any) {
        this.error = e?.response?.data?.message ?? 'Не удалось получить операции';
      } finally {
        this.loading = false;
      }
    },
  },
});
