<template>
  <div class="reservation-container">
    <div class="overlay">
      <div class="reservation-card">
        <h2>APARTAR PRODUCTO</h2>

        <!-- Producto cargado -->
        <div v-if="product" class="product-info">
          <p><strong>Producto:</strong> {{ product.name }}</p>
          <p><strong>Precio:</strong> ${{ Number(product.price).toFixed(2) }}</p>
          <p><strong>Stock disponible:</strong> {{ product.stock }}</p>

          <form @submit.prevent="submitReservation" class="reservation-form">
            <label for="quantity">Cantidad:</label>
            <input
              id="quantity"
              type="number"
              v-model.number="quantity"
              min="1"
              :max="product.stock"
              required
            />

            <label for="deliveryType">Tipo de entrega:</label>
            <select id="deliveryType" v-model="deliveryType" required>
              <option value="" disabled>Selecciona una opción</option>
              <option value="Retiro en tienda">Retiro en tienda</option>
              <option value="Entrega a domicilio">Entrega a domicilio</option>
            </select>

            <div v-if="deliveryType === 'Entrega a domicilio'">
              <label for="address">Dirección de entrega:</label>
              <input
                id="address"
                v-model="deliveryAddress"
                placeholder="Calle principal, número, ciudad..."
                required
              />
            </div>

            <button type="submit" :disabled="submitting">
              {{ submitting ? 'PROCESANDO...' : 'CONFIRMAR APARTADO' }}
            </button>
          </form>
        </div>

        <!-- Cargando -->
        <div v-else-if="loading">
          <p>Cargando información del producto...</p>
        </div>

        <!-- Error -->
        <div v-else-if="errorMessage">
          <p class="error">{{ errorMessage }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import api from '../axios'

export default {
  data() {
    return {
      product: null,
      quantity: 1,
      deliveryType: '',
      deliveryAddress: '',
      loading: true,
      errorMessage: '',
      submitting: false // Para evitar múltiples envíos
    }
  },
  async mounted() {
    const productId = this.$route.query.productId
    if (!productId) {
      this.loading = false
      this.errorMessage = 'No se ha proporcionado un producto para apartar.'
      return
    }

    try {
      const res = await api.get(`/products/${productId}`)
      if (res.data) {
        this.product = res.data
      } else {
        this.errorMessage = 'Producto no encontrado en la base de datos.'
      }
    } catch (error) {
      this.errorMessage = 'Error al cargar el producto. Verifica la URL o intenta de nuevo.'
      console.error("Error al cargar producto:", error.response?.data || error)
    } finally {
      this.loading = false
    }
  },
  methods: {
    // Función de prueba para verificar la autenticación
    async testAuth() {
      try {
        const response = await api.get('/user')
        console.log('Usuario autenticado:', response.data)
        return response.data
      } catch (error) {
        console.error('Error de autenticación:', error)
        return null
      }
    },

    async submitReservation() {
      // Evitar envíos múltiples
      if (this.submitting) return
      
      if (!this.deliveryType) {
        alert('Por favor selecciona un tipo de entrega.')
        return
      }

      // Verificar si el usuario está autenticado
      const token = localStorage.getItem('token')
      if (!token) {
        alert('Debes iniciar sesión para realizar una reserva.')
        this.$router.push('/login')
        return
      }

      // Verificar formato del token
      if (!token.includes('|')) {
        alert('Tu sesión parece estar corrompida. Por favor inicia sesión nuevamente.')
        localStorage.removeItem('token')
        localStorage.removeItem('user')
        this.$router.push('/login')
        return
      }

      // Validar cantidad disponible
      if (this.quantity > this.product.stock) {
        alert('La cantidad solicitada excede el stock disponible.')
        return
      }

      // Validar dirección si es entrega a domicilio
      if (this.deliveryType === 'Entrega a domicilio' && !this.deliveryAddress.trim()) {
        alert('Por favor ingresa una dirección de entrega válida.')
        return
      }

      this.submitting = true

      const data = {
        product_id: this.product.id,
        quantity: this.quantity,
        delivery_type: this.deliveryType,
        delivery_address: this.deliveryType === 'Entrega a domicilio' ? this.deliveryAddress.trim() : null
      }

      try {
        // Simplificamos la petición (el interceptor agrega automáticamente el token)
        const response = await api.post('/reservations', data)
        
        alert('¡Producto apartado correctamente!')
        console.log('Reserva creada:', response.data)
        
        // Redirigir al dashboard del usuario donde puede ver sus reservas
        this.$router.push('/user')
      } catch (error) {
        console.error('Error al crear reserva:', error)
        
        if (error.response?.status === 401) {
          alert('Tu sesión ha expirado. Por favor inicia sesión nuevamente.')
          localStorage.removeItem('token')
          localStorage.removeItem('user')
          this.$router.push('/login')
        } else if (error.response?.status === 403) {
          alert('No tienes permisos para realizar esta acción. Verifica que tengas el rol de usuario.')
        } else if (error.response?.status === 422) {
          // Errores de validación
          const errors = error.response.data.errors
          if (errors) {
            const errorMessages = Object.values(errors).flat().join('\n')
            alert('Error de validación:\n' + errorMessages)
          } else {
            alert('Error de validación: ' + (error.response.data.message || 'Datos inválidos'))
          }
        } else {
          const message = error.response?.data?.message || 'Error desconocido al procesar la reserva'
          alert('Error al apartar: ' + message)
        }
      } finally {
        this.submitting = false
      }
    }
  }
}
</script>

<style scoped>
.reservation-container {
  min-height: 100vh;
  /* 🔹 Usa un placeholder si no tienes imagen */
  background-image: url('/storage/images/hero5.jpg');
  background-size: cover;
  background-position: center;
  display: flex;
  justify-content: center;
  align-items: center;
}

.overlay {
  width: 100%;
  height: 100%;
  backdrop-filter: brightness(0.6) blur(2px);
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 2rem;
}

.reservation-card {
  background-color: rgba(255, 255, 255, 0.95);
  padding: 2.5rem;
  border-radius: 12px;
  width: 100%;
  max-width: 520px;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
}

h2 {
  color: #111;
  text-align: center;
  margin-bottom: 1.5rem;
  font-size: 1.8rem;
}

.product-info p {
  margin: 0.3rem 0;
  font-size: 1rem;
  color: #333;
}

.reservation-form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  margin-top: 1.5rem;
}

label {
  font-weight: bold;
  color: #374151;
}

input,
select {
  padding: 0.75rem;
  font-size: 1rem;
  border: 1px solid #d1d5db;
  border-radius: 8px;
}

button {
  padding: 0.9rem;
  background-color: black;
  color: white;
  font-weight: bold;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  cursor: pointer;
}

button:hover {
  background-color: #222;
}

button:disabled {
  background-color: #666;
  cursor: not-allowed;
  opacity: 0.7;
}

.error {
  color: red;
  font-weight: 500;
  margin-top: 1rem;
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
