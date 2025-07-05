<template>
  <div class="register-container">
    <h2>Registro de Usuario</h2>
    <form @submit.prevent="register" class="register-form">
      <input v-model="name" placeholder="Nombre completo" required />
      <input v-model="email" placeholder="Correo electrónico" type="email" required />
      <input v-model="password" placeholder="Contraseña" type="password" required />
      <input v-model="password_confirmation" placeholder="Confirmar contraseña" type="password" required />
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
          role: 'user' // fuerza el rol a 'user' desde el frontend (opcional)
        })
        alert('Usuario registrado correctamente')
        this.$router.push('/login')
      } catch (error) {
        if (error.response) {
          alert('Error al registrar: ' + JSON.stringify(error.response.data));
          console.error(error.response.data);
        } else {
          alert('Error al registrar: ' + error.message);
          console.error(error);
        }
      }
    }
  }
}
</script>

<style scoped>
.register-container {
  max-width: 400px;
  margin: 3rem auto;
  padding: 2rem;
  background-color: #f9fafb;
  border-radius: 10px;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
}

h2 {
  text-align: center;
  color: #4f46e5;
  margin-bottom: 1.5rem;
}

.register-form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.register-form input {
  padding: 0.75rem;
  font-size: 1rem;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  background-color: white;
}

.register-form button {
  padding: 0.75rem;
  background-color: #4f46e5;
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: bold;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.register-form button:hover {
  background-color: #4338ca;
}
</style>
