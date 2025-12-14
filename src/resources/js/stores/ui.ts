import { defineStore } from 'pinia';

const defaultPollInterval = (() => {
  const content = document.querySelector('meta[name="spa-poll-interval"]')?.getAttribute('content');
  const parsed = Number(content);
  return Number.isFinite(parsed) && parsed > 0 ? Math.floor(parsed) : 10;
})();

interface UiState {
  pollIntervalSec: number;
}

export const useUiStore = defineStore('ui', {
  state: (): UiState => ({
    pollIntervalSec: defaultPollInterval,
  }),
  actions: {
    setPollIntervalSec(v: number) {
      this.pollIntervalSec = Math.max(1, Math.floor(v));
    },
  },
});
