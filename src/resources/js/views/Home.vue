<template>
  <div class="py-3">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <h1 class="h4 mb-0">Главная</h1>
      <div class="d-flex align-items-center gap-2">
        <label class="form-label mb-0 small text-muted" for="pollInterval">Обновлять каждые</label>
        <input id="pollInterval" type="number" min="1" class="form-control form-control-sm" style="width: 90px" v-model.number="pollIntervalInput" @change="updateInterval" />
        <span class="small text-muted">сек.</span>
      </div>
    </div>

    <div class="row g-3">
      <div class="col-lg-4">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="text-muted">Текущий баланс</span>
              <button class="btn btn-sm btn-outline-secondary" :disabled="balance.loading" @click="refresh">
                <span v-if="balance.loading" class="spinner-border spinner-border-sm"></span>
                <span v-else>Обновить</span>
              </button>
            </div>
            <div v-if="balance.error" class="text-danger small">{{ balance.error }}</div>
            <div v-else class="display-6">{{ balance.value ?? '—' }}</div>
          </div>
        </div>
      </div>

      <div class="col-lg-8">
        <div class="card h-100">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="text-muted">Последние операции</span>
              <button class="btn btn-sm btn-outline-secondary" :disabled="ops.loading" @click="refresh">
                <span v-if="ops.loading" class="spinner-border spinner-border-sm"></span>
                <span v-else>Обновить</span>
              </button>
            </div>
            <div v-if="ops.error" class="text-danger small">{{ ops.error }}</div>
            <div v-else>
              <ul class="list-group list-group-flush" v-if="ops.latest.length">
                <li class="list-group-item" v-for="op in ops.latest" :key="op.id">
                  <div class="d-flex justify-content-between align-items-center">
                    <div>
                      <div class="fw-semibold">{{ op.description }}</div>
                      <div class="text-muted small">{{ formatDate(op.created_at) }}</div>
                    </div>
                    <div :class="['fw-semibold', op.amount.startsWith('-') ? 'text-danger' : 'text-success']">
                      {{ op.amount }}
                    </div>
                  </div>
                </li>
              </ul>
              <p v-else class="text-muted">Нет операций.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted, onUnmounted, ref, watch } from 'vue';
import { useBalanceStore } from '@/stores/balance';
import { useOperationsStore } from '@/stores/operations';
import { useUiStore } from '@/stores/ui';

const balance = useBalanceStore();
const ops = useOperationsStore();
const ui = useUiStore();
const pollIntervalInput = ref(ui.pollIntervalSec);

let timer: number | undefined;

function formatDate(dateStr: string) {
  return new Date(dateStr).toLocaleString();
}

function clearTimer() {
  if (timer) {
    window.clearInterval(timer);
    timer = undefined;
  }
}

function setupTimer() {
  clearTimer();
  timer = window.setInterval(() => {
    refresh();
  }, ui.pollIntervalSec * 1000);
}

async function refresh() {
  await Promise.all([
    balance.fetchBalance(),
    ops.fetchLatest(5),
  ]);
}

function updateInterval() {
  ui.setPollIntervalSec(pollIntervalInput.value || 1);
}

watch(() => ui.pollIntervalSec, () => {
  setupTimer();
});

onMounted(async () => {
  await refresh();
  setupTimer();
});

onUnmounted(() => {
  clearTimer();
});
</script>
