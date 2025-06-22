<template>
  <div class="login-container">
    <div class="login-card">
      <h2>Iniciar sesión</h2>
      <form @submit.prevent="login">
        <input v-model="email" type="email" placeholder="Correo electrónico" required />
        <input v-model="password" type="password" placeholder="Contraseña" required />
        <button type="submit">Entrar</button>
      </form>
    </div>
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

        localStorage.setItem('user', JSON.stringify(response.data.user))
        localStorage.setItem('token', response.data.token)

        // Configurar token en axios para futuras peticiones
        api.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`

        alert('Bienvenido ' + response.data.user.name)

        // Redireccionar según rol
        const role = response.data.user.role
        if (role === 'admin') {
          this.$router.push('/admin/dashboard')
        } else {
          this.$router.push('/user/dashboard')
        }

      } catch (error) {
        if (error.response && error.response.status === 401) {
          alert('Credenciales incorrectas')
        } else {
          alert('Error al iniciar sesión')
        }
        console.error(error.response?.data || error.message)
      }
    }
  }
}
</script>

<style scoped>
.login-container {
  min-height: 100vh;
  background-color: #f3f4f6;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem;
}

.login-card {
  background-color: white;
  padding: 2.5rem;
  border-radius: 10px;
  width: 100%;
  max-width: 400px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  text-align: center;
}

h2 {
  margin-bottom: 2rem;
  color: #4f46e5;
}

input {
  width: 100%;
  padding: 0.75rem;
  margin-bottom: 1.2rem;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 1rem;
}

button {
  width: 100%;
  padding: 0.75rem;
  background-color: #4f46e5;
  color: white;
  border: none;
  font-weight: bold;
  border-radius: 6px;
  font-size: 1rem;
  cursor: pointer;
  transition: background-color 0.3s;
}

button:hover {
  background-color: #4338ca;
}
</style>
