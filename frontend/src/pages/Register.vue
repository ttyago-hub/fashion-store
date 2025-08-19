<template>
  <div class="auth-container">
    <div class="auth-left">
      <div class="auth-form">
        <h2>CREA TU CUENTA</h2>
        <form @submit.prevent="register">
          <input v-model="name" placeholder="Nombre completo" required />
          <input v-model="email" placeholder="Correo electrónico" type="email" required />
          <input v-model="password" placeholder="Contraseña" type="password" required />
          <input v-model="password_confirmation" placeholder="Confirmar contraseña" type="password" required />

          <button type="submit">REGISTRARME</button>
        </form>

        <router-link to="/login" class="login-link">¿YA TIENES CUENTA? INICIA SESIÓN</router-link>
      </div>
    </div>

    <div class="auth-right">
      <img src="http://127.0.0.1:8000/storage/images/hero3.jpg" alt="Fashion" class="auth-image" />
    </div>
  </div>
</template>

<script>
import api from '../axios'

export default {
  data() {
    return {
      name: '',
      email: '',
      password: '',
      password_confirmation: ''
    }
  },
  methods: {
    async register() {
      try {
        const response = await api.post('/register', {
          name: this.name,
          email: this.email,
          password: this.password,
          password_confirmation: this.password_confirmation,
          role: 'user'
        })
        alert('Usuario registrado correctamente')
        this.$router.push('/login')
      } catch (error) {
        alert('Error al registrar')
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
  display: flex;
  justify-content: center;
  align-items: center;
  background: white;
}

.auth-right {
  flex: 1;
}

.auth-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
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

.login-link {
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
</style>
