// SPA entry (Vue 3 + TS)
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import router from '@/router';
import App from '@/App.vue';
import { useAuthStore } from '@/stores/auth';
// Bootstrap JS (for navbar toggler, modals, etc.)
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

const app = createApp(App);

// Install stores
const pinia = createPinia();
app.use(pinia);

// Configure auth guard after pinia is active
const auth = useAuthStore();

router.beforeEach(async (to) => {
  if (!auth.initialized && !auth.loading) {
    try {
      await auth.fetchUser();
    } catch (_) {
      // silently ignore, handled by store
    }
  }

  const isPublic = to.meta?.public === true;

  if (!isPublic && !auth.user) {
    return { name: 'login', query: { redirect: to.fullPath } };
  }

  if (to.name === 'login' && auth.user) {
    return { name: 'home' };
  }

  auth.resetError();
  return true;
});

// Install router
app.use(router);

// Mount
app.mount('#app');
