<template>
  <div class="py-3">
    <div class="d-flex flex-wrap gap-3 align-items-center justify-content-between mb-3">
      <h1 class="h4 mb-0">История операций</h1>
      <div class="d-flex align-items-center gap-2">
        <input
          v-model="search"
          type="search"
          class="form-control form-control-sm"
          placeholder="Поиск по описанию"
        />
        <button class="btn btn-sm btn-outline-secondary" :disabled="operations.loading" @click="refresh">Обновить</button>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <div class="text-muted">Сортировка</div>
          <div class="btn-group">
            <button class="btn btn-sm" :class="dir === 'asc' ? 'btn-primary' : 'btn-outline-primary'" @click="setDir('asc')">По возрастанию</button>
            <button class="btn btn-sm" :class="dir === 'desc' ? 'btn-primary' : 'btn-outline-primary'" @click="setDir('desc')">По убыванию</button>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table align-middle">
            <thead>
              <tr>
                <th style="width: 180px;">Дата</th>
                <th>Описание</th>
                <th style="width: 140px;" class="text-end">Сумма</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="operations.loading">
                <td colspan="3" class="text-center py-3">
                  <div class="spinner-border spinner-border-sm"></div>
                </td>
              </tr>
              <tr v-else-if="operations.error">
                <td colspan="3" class="text-danger">{{ operations.error }}</td>
              </tr>
              <tr v-else-if="!operations.list.length">
                <td colspan="3" class="text-muted">Нет данных</td>
              </tr>
              <tr v-else v-for="op in operations.list" :key="op.id">
                <td>{{ formatDate(op.created_at) }}</td>
                <td>{{ op.description }}</td>
                <td class="text-end" :class="op.amount.startsWith('-') ? 'text-danger' : 'text-success'">{{ op.amount }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="d-flex align-items-center justify-content-between mt-2 flex-wrap gap-2">
          <div class="small text-muted">Всего: {{ operations.total }}</div>
          <nav>
            <ul class="pagination pagination-sm mb-0">
              <li class="page-item" :class="{ disabled: page <= 1 }">
                <button class="page-link" @click="goTo(page - 1)" :disabled="page <= 1">Назад</button>
              </li>
              <li class="page-item disabled"><span class="page-link">{{ page }}</span></li>
              <li class="page-item" :class="{ disabled: !hasMore }">
                <button class="page-link" @click="goTo(page + 1)" :disabled="!hasMore">Вперед</button>
              </li>
            </ul>
          </nav>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue';
import { useOperationsStore } from '@/stores/operations';
import { debounce } from '@/utils/debounce';

const operations = useOperationsStore();
const search = ref('');
const dir = ref<'asc' | 'desc'>('desc');
const page = computed(() => operations.page);
const hasMore = computed(() => operations.list.length && operations.list.length === operations.perPage && operations.total > operations.list.length && operations.page * operations.perPage < operations.total);

function formatDate(dateStr: string) {
  return new Date(dateStr).toLocaleString();
}

async function refresh(newPage?: number) {
  await operations.fetchList({
    page: newPage ?? operations.page,
    dir: dir.value,
    sort: 'date',
    q: search.value.trim() || undefined,
  });
}

function goTo(p: number) {
  if (p < 1) return;
  refresh(p);
}

function setDir(next: 'asc' | 'desc') {
  if (dir.value === next) return;
  dir.value = next;
  refresh(1);
}

const debouncedSearch = debounce(() => refresh(1), 300);

watch(search, () => {
  debouncedSearch();
});

onMounted(async () => {
  await refresh(1);
});
</script>
