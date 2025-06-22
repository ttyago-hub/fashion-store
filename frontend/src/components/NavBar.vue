<template>
  <nav>
    <ul style="list-style: none; display: flex; gap: 10px">
      <li><router-link to="/">Inicio</router-link></li>

      <li v-if="user && user.role === 'user'">
        <router-link to="/reservation">Mis Reservas</router-link>
      </li>

      <li v-if="user && user.role === 'admin'">
        <router-link to="/inventory">Inventario</router-link>
        <router-link to="/admin">Gestión Productos</router-link>
      </li>

      <li v-if="!user">
        <router-link to="/login"> Iniciar sesión</router-link>
        <router-link to="/register"> Registrarse</router-link>
      </li>

      <li v-if="user">
        <span>Hola, {{ user.name }}</span>
        <button @click="logout">Cerrar sesión</button>
      </li>
    </ul>
  </nav>
</template>

<script>
import api from '../axios'

export default {
  data() {
    return {
      user: null
    }
  },
  created() {
    const localUser = localStorage.getItem('user')
    if (localUser) {
      this.user = JSON.parse(localUser)
    }
  },
  methods: {
    async logout() {
      const token = localStorage.getItem('token')
      try {
        await api.post('/logout', {}, {
          headers: {
            Authorization: `Bearer ${token}`
          }
        })
      } catch (error) {
        console.warn('Error cerrando sesión (ignorado):', error.message)
      }
      localStorage.removeItem('user')
      localStorage.removeItem('token')
      this.user = null
      this.$router.push('/login')
    }
  }
}
</script>
