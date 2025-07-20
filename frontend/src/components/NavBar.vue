<template>
  <nav class="navbar">
    <ul class="nav-left">
      <li><router-link to="/">Inicio</router-link></li>

      <li v-if="user && user.role === 'user'">
        <router-link to="/reserve">Mis Reservas</router-link>
      </li>

      <li v-if="user && user.role === 'admin'">
        <router-link to="/inventory">Inventario</router-link>
        <router-link to="/reserve">Mis Reservas</router-link>
        <router-link to="/admin">Gestión Productos</router-link>
        <router-link to="/users">Gestión Usuarios</router-link>
      </li>

      <li v-if="!user">
        <router-link to="/login">Iniciar sesión</router-link>
        <router-link to="/register">Registrarse</router-link>
      </li>
    </ul>

    <div class="nav-right" v-if="user">
      <button @click="logout" class="logout-btn">Cerrar sesión</button>
    </div>
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
  background-color: #f3f4f6;
  padding: 1rem 2rem;
  box-shadow: 0 2px 4px rgba(0,0,0,0.08);
  font-family: 'Segoe UI', sans-serif;
  flex-wrap: wrap;
}

.nav-left {
  list-style: none;
  display: flex;
  gap: 16px;
  padding: 0;
  margin: 0;
  flex-wrap: wrap;
}

.nav-left li {
  display: flex;
}

.nav-left a {
  text-decoration: none;
  color: #1f2937;
  font-weight: 500;
  padding: 0.4rem 0.6rem;
  border-radius: 4px;
  transition: background-color 0.3s, color 0.3s;
}

.nav-left a:hover {
  background-color: #e0e7ff;
  color: #4f46e5;
}

.nav-right {
  margin-left: auto;
}

.logout-btn {
  background: none;
  border: none;
  color: #ef4444;
  font-weight: bold;
  cursor: pointer;
  padding: 0.4rem 0.8rem;
  border-radius: 4px;
  transition: background-color 0.3s;
}

.logout-btn:hover {
  background-color: #fee2e2;
}

</style>
