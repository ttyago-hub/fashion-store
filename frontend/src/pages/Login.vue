<template>
  <div>
    <h2>Iniciar sesión</h2>
    <form @submit.prevent="login">
      <input v-model="email" type="email" placeholder="Correo electrónico" />
      <input v-model="password" type="password" placeholder="Contraseña" />
      <button type="submit">Iniciar sesión</button>
    </form>
  </div>
</template>

<script>
import api from '../axios'

export default {
  data() {
    return {
      email: '',
      password: ''
    }
  },
  methods: {
    async login() {
      try {
        const response = await api.post('/login', {
          email: this.email,
          password: this.password
        })

        // Guardar token o datos en localStorage si es necesario
        localStorage.setItem('user', JSON.stringify(response.data.user))
        localStorage.setItem('token', response.data.token)

        alert('Bienvenido ' + response.data.user.name)

        if (response.data.user.role === 'admin') {
          this.$router.push('/inventory')
        } else {
          this.$router.push('/')
        }

      } catch (error) {
        alert('Error al iniciar sesión')
        console.error(error.response?.data || error.message)
      }
    }
  }
}
</script>
