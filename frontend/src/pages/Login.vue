<template>
  <div class="auth-container">
    <div class="auth-left">
      <img src="http://127.0.0.1:8000/storage/images/hero2.jpg" alt="Fashion" class="auth-image" />
    </div>
    <div class="auth-right">
      <div class="auth-form">
        <h2>INICIA SESIÓN</h2>
<<<<<<< HEAD
        
        <!-- Mensaje de estado -->
        <div v-if="message" :class="['message', messageType]">
          {{ message }}
        </div>
        
        <form @submit.prevent="login">
          <input 
            v-model="email" 
            type="email" 
            placeholder="Correo electrónico" 
            :disabled="isLoading"
            required 
          />
          <input 
            v-model="password" 
            type="password" 
            placeholder="Contraseña" 
            :disabled="isLoading"
            required 
          />
=======
        <form @submit.prevent="login">
          <input v-model="email" type="email" placeholder="Correo electrónico" required />
          <input v-model="password" type="password" placeholder="Contraseña" required />
>>>>>>> 44172495fe341cb5435355a45143c79aa45e0ca4

          <router-link to="/forgot-password" class="forgot-link">
            ¿Olvidaste tu contraseña?
          </router-link>

<<<<<<< HEAD
          <button type="submit" :disabled="isLoading">
            <span v-if="isLoading">Iniciando sesión... ⏳</span>
            <span v-else>INICIAR SESIÓN</span>
          </button>
=======
          <button type="submit">INICIAR SESIÓN</button>
>>>>>>> 44172495fe341cb5435355a45143c79aa45e0ca4
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
      password: '',
      isLoading: false,
      message: '',
      messageType: '' // 'success' o 'error'
    }
  },
  methods: {
    async login() {
      // Iniciar carga y limpiar mensajes
      this.isLoading = true
      this.message = ''
      
      try {
        // 🔑 Solicita el CSRF cookie antes de hacer login (ruta sin /api)
        await api.get('http://127.0.0.1:8000/sanctum/csrf-cookie', { withCredentials: true });
        const response = await api.post('/login', {
          email: this.email,
          password: this.password
        })

        // 🔧 DEBUG: Ver qué token viene del servidor
        console.log('=== DEBUG LOGIN ===')
        console.log('Respuesta completa del servidor:', response.data)
        console.log('Token recibido del servidor:', response.data.token)

        const auth = useAuthStore()
        auth.setUser(response.data.user)
        auth.setToken(response.data.token)

        localStorage.setItem('token', response.data.token)
        localStorage.setItem('user', JSON.stringify(response.data.user))

<<<<<<< HEAD
        // ✅ LOGIN EXITOSO - Mostrar mensaje de éxito
        this.message = `¡Bienvenido ${response.data.user.name}! Redirigiendo a tu panel... 🎉`
        this.messageType = 'success'
        
        // Redirigir automáticamente al dashboard correspondiente
        setTimeout(() => {
          this.$router.push('/dashboard')
        }, 1500)

      } catch (error) {
        // ❌ LOGIN FALLIDO - Mostrar mensaje de error
        console.error('Error de login:', error.response?.data || error.message)
        
        this.message = '❌ Credenciales incorrectas. Te redirigiremos al registro en 3 segundos...'
        this.messageType = 'error'
        
        // Limpiar campos
        this.email = ''
        this.password = ''
        
        // Redirigir al registro después de 3 segundos
        setTimeout(() => {
          this.$router.push('/register')
        }, 3000)
        
      } finally {
        // Detener carga
        this.isLoading = false
=======
        const role = response.data.user.role
        this.$router.push(role === 'admin' ? '/admin' : '/user')

      } catch (error) {
        alert('Credenciales incorrectas o error al iniciar sesión')
        console.error(error.response?.data || error.message)
>>>>>>> 44172495fe341cb5435355a45143c79aa45e0ca4
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

input:disabled {
  background-color: #f5f5f5;
  cursor: not-allowed;
  opacity: 0.7;
}

/* Mensajes de estado */
.message {
  padding: 0.8rem;
  margin-bottom: 1rem;
  border-radius: 6px;
  font-weight: 500;
  text-align: center;
}

.message.success {
  background-color: #d4edda;
  border: 1px solid #c3e6cb;
  color: #155724;
}

.message.error {
  background-color: #f8d7da;
  border: 1px solid #f5c6cb;
  color: #721c24;
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
<<<<<<< HEAD
  transition: opacity 0.3s ease;
}

button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

=======
}

>>>>>>> 44172495fe341cb5435355a45143c79aa45e0ca4
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
<<<<<<< HEAD
}

@media (max-width: 900px) {
  .auth-container {
    flex-direction: column;
    height: auto;
  }
  .auth-left, .auth-right {
    flex: none;
    width: 100%;
    height: auto;
  }
  .auth-image {
    height: 200px;
    object-fit: cover;
  }
}

@media (max-width: 600px) {
  .auth-form {
    width: 95%;
    max-width: 100%;
    padding: 1rem;
  }
  .auth-image {
    height: 120px;
  }
  h2 {
    font-size: 1.2rem;
  }
  input, button, .register-btn {
    font-size: 1rem;
    padding: 0.7rem;
  }
=======
>>>>>>> 44172495fe341cb5435355a45143c79aa45e0ca4
}
</style>
