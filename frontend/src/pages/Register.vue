<template>
  <div>
    <h2>Registro</h2>
    <form @submit.prevent="register">
      <input v-model="name" placeholder="Nombre" />
      <input v-model="email" placeholder="Correo" />
      <input v-model="password" placeholder="Contraseña" type="password" />
      <button type="submit">Registrar</button>
    </form>
  </div>
</template>

<script>
import api from '../axios'

export default {
  data() {
    return {
      name: '',
      email: '',
      password: ''
    }
  },
  methods: {
    async register() {
      try {
        const response = await api.post('/register', {
          name: this.name,
          email: this.email,
          password: this.password
        })
        alert('Usuario registrado correctamente')
        this.$router.push('/login')
      } catch (error) {
        console.error(error.response.data)
        alert('Error al registrar')
      }
    }
  }
}
</script>

