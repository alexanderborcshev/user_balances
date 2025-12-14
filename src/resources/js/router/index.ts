import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router';

// Lazy views
const Home = () => import('@/views/Home.vue');
const Login = () => import('@/views/Login.vue');
const History = () => import('@/views/History.vue');

const routes: RouteRecordRaw[] = [
  { path: '/login', name: 'login', component: Login, meta: { public: true } },
  { path: '/', name: 'home', component: Home },
  { path: '/history', name: 'history', component: History },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior() {
    return { top: 0 };
  },
});

export default router;
