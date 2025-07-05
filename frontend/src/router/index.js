import { createRouter, createWebHistory } from 'vue-router';
import Home from '../pages/Home.vue';
import Login from '../pages/Login.vue';
import Register from '../pages/Register.vue';
import Products from '../pages/Products.vue';
import Reservation from '../pages/Reservation.vue';
import AdminDashboard from '../pages/AdminDashboard.vue';
import ForgotPassword from '../pages/ForgotPassword.vue';
import ResetPassword from '../pages/ResetPassword.vue';

const routes = [
  { path: '/', component: Home },
  { path: '/login', component: Login },
  { path: '/register', component: Register },
  { path: '/products', component: Products },
  { path: '/reserve', component: Reservation, meta: { requiresAuth: true, role: 'user' } },
  { path: '/admin', component: AdminDashboard, meta: { requiresAuth: true, role: 'admin' } },
  {
    path: '/users',
    component: () => import('../pages/UserManagement.vue'),
    meta: { requiresAuth: true, role: 'admin' }
  },
  {  path: '/forgot-password',  component: ForgotPassword},
  {  path: '/reset-password',  component: ResetPassword}

];


const router = createRouter({
  history: createWebHistory(),
  routes
})

// Guard para proteger rutas según rol y login
router.beforeEach((to, from, next) => {
  const requiresAuth = to.meta.requiresAuth
  const role = to.meta.role

  const token = localStorage.getItem('token')
  const user = localStorage.getItem('user') ? JSON.parse(localStorage.getItem('user')) : null

  if (requiresAuth && !token) {
    // Si ruta requiere login pero no hay token, redirige a login
    return next('/login')
  }

  if (role && user?.role !== role) {
    // Si la ruta requiere rol específico y no coincide, redirige a home o login
    return next('/')
  }

  next()
})

export default router
