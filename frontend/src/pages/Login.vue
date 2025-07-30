<template>
  <div class="auth-container">
    <div class="auth-left">
      <img src="http://127.0.0.1:8000/storage/images/hero2.jpg" alt="Fashion" class="auth-image" />
    </div>
    <div class="auth-right">
      <div class="auth-form">
        <h2>INICIA SESIÓN</h2>
        <form @submit.prevent="login">
          <input v-model="email" type="email" placeholder="Correo electrónico" required />
          <input v-model="password" type="password" placeholder="Contraseña" required />

          <router-link to="/forgot-password" class="forgot-link">
            ¿Olvidaste tu contraseña?
          </router-link>

          <button type="submit">INICIAR SESIÓN</button>
        </form>

        <router-link to="/register" class="register-btn">REGÍSTRATE</router-link>
      </div>
    </div>
  </div>
</template>

<script>
import api from '../axios'
import { useAuthStore } from '../store/auth'

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

        const auth = useAuthStore()
        auth.setUser(response.data.user)
        auth.setToken(response.data.token)

        alert('Bienvenido ' + response.data.user.name)

        const role = response.data.user.role
        this.$router.push(role === 'admin' ? '/admin' : '/user')

      } catch (error) {
        alert('Credenciales incorrectas o error al iniciar sesión')
        console.error(error.response?.data || error.message)
      }
    }
  }
}
</script>

<style scoped>
.auth-container {
  display: flex;
  height: 100vh;
}

.auth-left {
  flex: 1;
}

.auth-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.auth-right {
  flex: 1;
  display: flex;
  justify-content: center;
  align-items: center;
  background: white;
}

.auth-form {
  width: 80%;
  max-width: 400px;
}

.auth-form h2 {
  font-size: 1.8rem;
  font-weight: bold;
  margin-bottom: 1.5rem;
  color: #111;
}

input {
  width: 100%;
  padding: 0.8rem;
  margin-bottom: 1rem;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 1rem;
}

button {
  width: 100%;
  padding: 0.8rem;
  background: black;
  color: white;
  font-weight: bold;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}

.register-btn {
  display: block;
  margin-top: 1rem;
  text-align: center;
  padding: 0.8rem;
  border: 1px solid black;
  border-radius: 6px;
  color: black;
  text-decoration: none;
  font-weight: bold;
}

.forgot-link {
  display: block;
  margin-bottom: 1rem;
  text-align: right;
  color: #333;
  font-size: 0.9rem;
}
</style>
