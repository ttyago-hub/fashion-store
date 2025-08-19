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
        <router-link to="/user">Mis Reservas</router-link>
      </li>

      <template v-if="user && user.role === 'admin'">
        <li><router-link to="/inventory">Inventario</router-link></li>
<<<<<<< HEAD
        <li><router-link to="/user">Mis Reservas</router-link></li>
=======
        <li><router-link to="/reserve">Mis Reservas</router-link></li>
>>>>>>> 44172495fe341cb5435355a45143c79aa45e0ca4
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
<<<<<<< HEAD
        <span class="user-info">
          <span class="user-greeting">Hola, {{ user.name }}</span>
          <span class="user-role" :class="user.role">
            {{ user.role === 'admin' ? '👑 Admin' : '👤 Usuario' }}
          </span>
        </span>
      </li>

      <li v-if="user">
=======
>>>>>>> 44172495fe341cb5435355a45143c79aa45e0ca4
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
<<<<<<< HEAD
}
@media (max-width: 900px) {
  .main-container {
    flex-direction: column;
    height: auto;
  }
  .left-section, .right-section {
    flex: none;
    width: 100%;
    height: auto;
  }
  .main-image {
    height: 200px;
    object-fit: cover;
  }
=======
>>>>>>> 44172495fe341cb5435355a45143c79aa45e0ca4
}

@media (max-width: 600px) {
  .form-container {
    width: 95%;
    max-width: 100%;
    padding: 1rem;
  }
  .main-image {
    height: 120px;
  }
  h2 {
    font-size: 1.2rem;
  }
  input, button, .register-btn {
    font-size: 1rem;
    padding: 0.7rem;
  }
}

/* Estilos para información del usuario en navbar */
.user-info {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 0.5rem;
  margin-right: 1rem;
}

.user-greeting {
  font-size: 0.9rem;
  color: #666;
  margin-bottom: 0.2rem;
}

.user-role {
  font-size: 0.8rem;
  padding: 0.2rem 0.6rem;
  border-radius: 12px;
  font-weight: 600;
}

.user-role.admin {
  background: linear-gradient(135deg, #ffd700, #ffb347);
  color: #8b4513;
}

.user-role.user {
  background: linear-gradient(135deg, #87ceeb, #4682b4);
  color: white;
}

.logout-btn {
  background: #ef4444;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.3s ease;
}

.logout-btn:hover {
  background: #dc2626;
  transform: translateY(-1px);
}
</style>
