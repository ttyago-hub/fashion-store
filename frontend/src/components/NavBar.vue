<template>
  <nav class="navbar">
    <!-- Logo -->
    <div class="logo">
      <router-link to="/" class="logo-link">Fashion Store</router-link>
    </div>

    <!-- Menú -->
    <ul class="nav-menu"> 
      <li><router-link to="/">Inicio</router-link></li>
      <li><router-link to="/products">Productos</router-link></li>
      <li v-if="user && user.role === 'user'">
        <router-link to="/reserve">Mis Reservas</router-link>
      </li>

      <template v-if="user && user.role === 'admin'">
        <li><router-link to="/inventory">Inventario</router-link></li>
        <li><router-link to="/reserve">Mis Reservas</router-link></li>
        <li><router-link to="/admin">Gestión Productos</router-link></li>
        <li><router-link to="/users">Gestión Usuarios</router-link></li>
      </template>

      <li v-if="!user">
        <router-link to="/login">Iniciar sesión</router-link>
      </li>

      <li v-if="!user">
        <router-link to="/register">Registrarse</router-link>
      </li>

      <li v-if="user">
        <button @click="logout" class="logout-btn">Cerrar sesión</button>
      </li>
    </ul>
  </nav>
</template>



<script>
export default {
  data() {
    return {
      user: JSON.parse(localStorage.getItem('user'))
    }
  },
  mounted() {
    window.addEventListener('storage', this.syncUser)
  },
  beforeUnmount() {
    window.removeEventListener('storage', this.syncUser)
  },
  methods: {
    logout() {
      localStorage.removeItem('user')
      localStorage.removeItem('token')
      this.user = null
      this.$router.push('/login')
    },
    syncUser() {
      this.user = JSON.parse(localStorage.getItem('user'))
    }
  }
}
</script>


<style scoped>
.navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background-color: white;
  padding: 1rem 2rem;
  border-bottom: 1px solid #e5e7eb;
  font-family: 'Helvetica Neue', sans-serif;
  font-size: 0.95rem;
  position: sticky;
  top: 0;
  z-index: 50;
}

.logo-link {
  font-size: 1.5rem;
  font-weight: bold;
  color: #111;
  text-decoration: none;
  letter-spacing: 0.05em;
}

.nav-menu {
  display: flex;
  gap: 1.5rem;
  list-style: none;
  padding: 0;
  margin: 0;
  align-items: center;
}

.nav-menu a {
  text-decoration: none;
  color: #111;
  padding: 0.5rem;
  transition: color 0.2s ease;
}

.nav-menu a:hover {
  color: #666;
}

.logout-btn {
  background: none;
  border: none;
  color: #b91c1c;
  font-weight: 500;
  cursor: pointer;
  transition: color 0.2s ease;
}

.logout-btn:hover {
  color: #ef4444;
}

</style>
