<template>
  <div class="row justify-content-center py-4">
    <div class="col-md-6 col-lg-4">
      <h1 class="h4 mb-3 text-center">Вход</h1>
      <form @submit.prevent="onSubmit">
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input v-model="email" type="email" class="form-control" required />
        </div>
        <div class="mb-3">
          <label class="form-label">Пароль</label>
          <input v-model="password" type="password" class="form-control" required />
        </div>
        <div class="d-grid gap-2">
          <button :disabled="auth.loading" type="submit" class="btn btn-primary">Войти</button>
        </div>
        <p v-if="auth.error" class="text-danger small mt-2">{{ auth.error }}</p>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const router = useRouter();
const route = useRoute();
const auth = useAuthStore();
const email = ref('');
const password = ref('');

async function onSubmit() {
  try {
    await auth.login(email.value, password.value);
    const redirect = (route.query.redirect as string) || '/';
    await router.push(redirect);
  } catch (_) {
    // error state already set in store
  }
}
</script>
