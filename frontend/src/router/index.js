import { createRouter, createWebHistory } from 'vue-router';
import Home from '../pages/Home.vue';
import Login from '../pages/Login.vue';
import Register from '../pages/Register.vue';
import Products from '../pages/Products.vue';
import Reservation from '../pages/Reservation.vue';
import AdminDashboard from '../pages/AdminDashboard.vue';
import ForgotPassword from '../pages/ForgotPassword.vue';
import ResetPassword from '../pages/ResetPassword.vue';
import Inventory from '../pages/Inventory.vue';
import UserDashboard from '../pages/UserDashboard.vue';

const routes = [
  { path: '/', component: Home },
  { path: '/login', component: Login },
  { path: '/register', component: Register },
  { path: '/products', component: Products },
  { path: '/inventory', component: Inventory },
  { 
    path: '/reserve/:productId?', // Parámetro opcional
    name: 'reservation',
    component: Reservation,
    meta: { requiresAuth: true, role: 'user' },
    props: true // Pasa parámetros como props
  },
  { 
    path: '/admin', 
    component: AdminDashboard, 
    meta: { requiresAuth: true, role: 'admin' } 
  },
  {
    path: '/user-dashboard', 
    component: UserDashboard, 
    meta: { requiresAuth: true, role: 'user' } 
  },
  {
    path: '/users',
    component: () => import('../pages/UserManagement.vue'),
    meta: { requiresAuth: true, role: 'admin' }
  },
  { path: '/forgot-password', component: ForgotPassword },
  { path: '/reset-password', component: ResetPassword }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

// Guard para proteger rutas
router.beforeEach((to, from, next) => {
  const requiresAuth = to.meta.requiresAuth;
  const requiredRole = to.meta.role;

  const token = localStorage.getItem('token');
  const user = localStorage.getItem('user') 
    ? JSON.parse(localStorage.getItem('user')) 
    : null;

  if (requiresAuth && !token) {
    return next('/login');
  }

  if (requiredRole && user?.role !== requiredRole) {
    // Redirige al dashboard según el rol del usuario
    return next(user?.role === 'admin' ? '/admin' : '/user-dashboard');
  }

  next();
});

export default router;