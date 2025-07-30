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

            <button type="submit">CONFIRMAR APARTADO</button>
          </form>
        </div>

        <!-- Cargando -->
        <div v-else-if="loading">
          <p>Cargando información del producto...</p>
        </div>

        <!-- Error -->
        <div v-else>
          <p class="error">Error al cargar el producto. Verifica la URL o intenta de nuevo.</p>
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
      loading: true
    }
  },
  async mounted() {
    const productId = this.$route.query.productId
    if (productId) {
      try {
        const res = await api.get(`/products/${productId}`)
        this.product = res.data
      } catch (error) {
        this.product = null
        console.error("Error al cargar producto:", error.response?.data || error)
      } finally {
        this.loading = false
      }
    } else {
      this.loading = false
    }
  },
  methods: {
    async submitReservation() {
      if (!this.deliveryType) {
        alert('Por favor selecciona un tipo de entrega.')
        return
      }

      const data = {
        product_id: this.product.id,
        quantity: this.quantity,
        delivery_type: this.deliveryType,
        delivery_address: this.deliveryType === 'Entrega a domicilio' ? this.deliveryAddress : null
      }

      try {
        await api.post('/reservations', data)
        alert('¡Producto apartado correctamente!')
        this.$router.push('/mis-reservas')
      } catch (error) {
        alert('Error al apartar: ' + (error.response?.data?.message || 'Error desconocido'))
        console.error('Error al apartar:', error.response?.data || error)
      }
    }
  }
}
</script>

<style scoped>
.reservation-container {
  min-height: 100vh;
  background-image: url('http://127.0.0.1:8000/storage/images/hero5.jpg');
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

.error {
  color: red;
  font-weight: 500;
  margin-top: 1rem;
}
</style>
