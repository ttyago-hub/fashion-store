<template>
  <div class="forgot-container">
    <div class="overlay">
      <div class="forgot-card">
        <h2>¿OLVIDASTE TU CONTRASEÑA?</h2>
        <p class="description">
          Ingresa tu correo y te enviaremos un enlace para recuperar el acceso.
        </p>

        <form @submit.prevent="submit">
          <input
            v-model="email"
            type="email"
            placeholder="Correo electrónico"
            required
          />
          <button type="submit">ENVIAR ENLACE</button>
        </form>

        <p v-if="message" class="success">{{ message }}</p>
        <p v-if="error" class="error">{{ error }}</p>

        <router-link to="/login" class="back-link">← Volver a inicio</router-link>
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
      message: '',
      error: ''
    }
  },
  methods: {
    async submit() {
      try {
        const res = await api.post('/forgot-password', { email: this.email })
        this.message = res.data.message
        this.error = ''
      } catch (err) {
        this.error = err.response?.data?.error || 'Error al enviar enlace'
        this.message = ''
      }
    }
  }
}
</script>

<style scoped>
.forgot-container {
  min-height: 100vh;
  background-image: url('http://127.0.0.1:8000/storage/images/hero4.jpg');
  background-size: cover;
  background-position: center;
  display: flex;
  justify-content: center;
  align-items: center;
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

.forgot-card {
  background-color: rgba(255, 255, 255, 0.9);
  padding: 2.5rem;
  border-radius: 12px;
  max-width: 420px;
  width: 100%;
  text-align: center;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
}

h2 {
  color: #111;
  font-size: 1.7rem;
  margin-bottom: 0.8rem;
}

.description {
  font-size: 0.95rem;
  color: #555;
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
  border: 1px solid #ccc;
  border-radius: 8px;
}

button {
  padding: 0.75rem;
  background-color: black;
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: bold;
  font-size: 1rem;
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

.back-link {
  display: block;
  margin-top: 1.2rem;
  font-size: 0.9rem;
  color: #333;
  text-decoration: none;
  font-weight: 500;
}
</style>
