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
  { 
    path: '/', 
    component: Home,
    meta: { allowAutoRedirect: true } // Permitir redirección automática desde inicio
  },
  { path: '/login', component: Login },
  { path: '/register', component: Register },
  { path: '/products', component: Products },
  { 
    path: '/products/:id',
    name: 'product-detail',
    component: () => import('../components/ProductDetail.vue'),
    props: true
  },
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
    path: '/user',  // ✅ Dashboard de usuario
    component: UserDashboard, 
    meta: { requiresAuth: true, role: 'user' } 
  },
  {
    path: '/users',
    component: () => import('../pages/UserManagement.vue'),
    meta: { requiresAuth: true, role: 'admin' }
  },
  { path: '/forgot-password', component: ForgotPassword },
  { path: '/reset-password', component: ResetPassword },
  {
    // Ruta de dashboard automático que redirige según el rol
    path: '/dashboard',
    redirect: (to) => {
      const user = localStorage.getItem('user') 
        ? JSON.parse(localStorage.getItem('user')) 
        : null;
      
      if (!user) return '/login';
      return user.role === 'admin' ? '/admin' : '/user';
    }
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

// ✅ Guard mejorado para proteger rutas, roles y redirección automática
router.beforeEach((to, from, next) => {
  const requiresAuth = to.meta.requiresAuth;
  const requiredRole = to.meta.role;
  const allowAutoRedirect = to.meta.allowAutoRedirect;

  const token = localStorage.getItem('token');
  const user = localStorage.getItem('user') 
    ? JSON.parse(localStorage.getItem('user')) 
    : null;

  console.log('🔄 Router Guard:', {
    to: to.path,
    user: user?.name,
    role: user?.role,
    hasToken: !!token,
    requiresAuth,
    requiredRole,
    allowAutoRedirect
  });

  // 🚫 Si la ruta requiere autenticación y no hay token
  if (requiresAuth && !token) {
    console.log('❌ No autenticado, redirigiendo a login');
    return next('/login');
  }

  // 🚫 Si la ruta requiere un rol específico y el usuario no lo tiene
  if (requiredRole && user?.role !== requiredRole) {
    console.log(`❌ Rol incorrecto. Requiere: ${requiredRole}, Usuario tiene: ${user?.role}`);
    // Redirige al dashboard según el rol del usuario
    const dashboardRoute = user?.role === 'admin' ? '/admin' : '/user';
    return next(dashboardRoute);
  }

  // 🎯 REDIRECCIÓN AUTOMÁTICA: Si el usuario está autenticado y va al inicio
  if (allowAutoRedirect && token && user) {
    console.log('🎯 Usuario autenticado detectado, redirigiendo automáticamente...');
    
    // Redirigir automáticamente al dashboard correspondiente
    const dashboardRoute = user.role === 'admin' ? '/admin' : '/user';
    
    // Evitar redirección infinita si ya está en su dashboard
    if (to.path !== dashboardRoute) {
      console.log(`✅ Redirigiendo ${user.role} a ${dashboardRoute}`);
      return next(dashboardRoute);
    }
  }

  // ✅ Permitir acceso
  console.log('✅ Acceso permitido a', to.path);
  next();
});

export default router;
