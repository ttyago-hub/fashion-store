<template>
  <div class="reset-container">
    <div class="reset-card">
      <h2>Restablecer contraseña</h2>
      <p class="description">Ingresa una nueva contraseña para tu cuenta.</p>

      <form @submit.prevent="submit">
        <input v-model="email" type="email" placeholder="Correo electrónico" required />

        <input
          v-model="password"
          type="password"
          placeholder="Nueva contraseña"
          required
        />

        <input
          v-model="password_confirmation"
          type="password"
          placeholder="Confirmar contraseña"
          required
        />

        <button type="submit">Guardar nueva contraseña</button>
      </form>

      <p v-if="message" class="success">{{ message }}</p>
      <p v-if="error" class="error">{{ error }}</p>
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
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #f3f4f6;
  padding: 1.5rem;
}

.reset-card {
  background-color: white;
  padding: 2.5rem;
  border-radius: 12px;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
  max-width: 460px;
  width: 100%;
  text-align: center;
}

h2 {
  color: #4f46e5;
  margin-bottom: 0.75rem;
}

.description {
  font-size: 0.95rem;
  color: #6b7280;
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
}

button {
  padding: 0.75rem;
  background-color: #4f46e5;
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: bold;
  font-size: 1rem;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

button:hover {
  background-color: #4338ca;
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
