import { createRouter, createWebHistory } from 'vue-router';
import Home from '../pages/Home.vue';
import Login from '../pages/Login.vue';
import Register from '../pages/Register.vue';
import Products from '../pages/Products.vue';
import Reservation from '../pages/Reservation.vue';
import AdminDashboard from '../pages/AdminDashboard.vue';
import ProductsTest from '../pages/ProductsTest.vue'
const routes = [
  { path: '/', component: Home },
  { path: '/login', component: Login },
  { path: '/register', component: Register },
  { path: '/products', component: Products },
  { path: '/reserve', component: Reservation },
  { path: '/admin', component: AdminDashboard },
  { path: '/test-products', component: ProductsTest }
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
