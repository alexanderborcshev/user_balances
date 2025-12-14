<template>
  <div class="app">
    <nav class="navbar navbar-expand-lg navbar-light bg-light mb-3 border-bottom">
      <div class="container-fluid">
        <RouterLink class="navbar-brand" to="/">User Balances</RouterLink>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav me-auto">
            <li class="nav-item"><RouterLink class="nav-link" to="/">Главная</RouterLink></li>
            <li class="nav-item"><RouterLink class="nav-link" to="/history">История</RouterLink></li>
          </ul>
          <ul class="navbar-nav">
            <li v-if="auth.loading && !auth.user" class="nav-item"><span class="nav-link">Загрузка…</span></li>
            <template v-else-if="auth.user">
              <li class="nav-item d-flex align-items-center px-2 text-muted small">{{ auth.user.email }}</li>
              <li class="nav-item">
                <button class="btn btn-outline-secondary btn-sm" :disabled="auth.loading" @click="logout">Выйти</button>
              </li>
            </template>
            <li v-else class="nav-item"><RouterLink class="nav-link" to="/login">Логин</RouterLink></li>
          </ul>
        </div>
      </div>
    </nav>
    <main class="container">
      <RouterView />
    </main>
  </div>
</template>

<script setup lang="ts">
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router';

const auth = useAuthStore();
const router = useRouter();

async function logout() {
  await auth.logout();
  await router.push({ name: 'login' });
}
</script>

<style scoped>
.app { min-height: 100vh; }
</style>
