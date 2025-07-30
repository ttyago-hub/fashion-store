<template>
  <div class="reset-container">
    <div class="overlay">
      <div class="reset-card">
        <h2>REESTABLECER CONTRASEÑA</h2>
        <p class="description">Ingresa tu nueva contraseña para continuar.</p>

        <form @submit.prevent="submit">
          <input v-model="email" type="email" placeholder="Correo electrónico" required />
          <input v-model="password" type="password" placeholder="Nueva contraseña" required />
          <input v-model="password_confirmation" type="password" placeholder="Confirmar contraseña" required />
          <button type="submit">GUARDAR</button>
        </form>

        <p v-if="message" class="success">{{ message }}</p>
        <p v-if="error" class="error">{{ error }}</p>
      </div>
    </div>
  </div>
</template>

<script>
import api from '../axios'

export default {
  data() {
    return {
      email: '',
      password: '',
      password_confirmation: '',
      token: '',
      message: '',
      error: ''
    }
  },
  mounted() {
    this.token = this.$route.query.token
    this.email = this.$route.query.email || ''
  },
  methods: {
    async submit() {
      try {
        const res = await api.post('/reset-password', {
          email: this.email,
          password: this.password,
          password_confirmation: this.password_confirmation,
          token: this.token
        })
        this.message = res.data.message
        this.error = ''
      } catch (err) {
        this.error = err.response?.data?.error || 'Error al restablecer contraseña'
        this.message = ''
      }
    }
  }
}
</script>

<style scoped>
.reset-container {
  min-height: 100vh;
  background-image: url('http://127.0.0.1:8000/storage/images/hero5.jpg');
  background-size: cover;
  background-position: center;
  display: flex;
  align-items: center;
  justify-content: center;
}

.overlay {
  width: 100%;
  height: 100%;
  backdrop-filter: brightness(0.6) blur(3px);
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 2rem;
}

.reset-card {
  background-color: rgba(255, 255, 255, 0.9);
  padding: 2.5rem;
  border-radius: 12px;
  max-width: 460px;
  width: 100%;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
  text-align: center;
}

h2 {
  font-size: 1.8rem;
  color: #111;
  margin-bottom: 0.8rem;
}

.description {
  color: #555;
  font-size: 0.95rem;
  margin-bottom: 1.5rem;
}

form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

input {
  padding: 0.75rem;
  font-size: 1rem;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  background-color: #fff;
}

button {
  padding: 0.75rem;
  background-color: black;
  color: white;
  font-weight: bold;
  font-size: 1rem;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

button:hover {
  background-color: #222;
}

.success {
  margin-top: 1rem;
  color: green;
  font-size: 0.95rem;
}

.error {
  margin-top: 1rem;
  color: red;
  font-size: 0.95rem;
}
</style>
