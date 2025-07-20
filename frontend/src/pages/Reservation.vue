<template>
  <div class="reservation-container">
    <h2>Apartar producto</h2>

    <!-- Mostrar información del producto -->
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

        <button type="submit">Confirmar apartado</button>
      </form>
    </div>

    <!-- Mientras se carga -->
    <div v-else-if="loading">
      <p>Cargando información del producto...</p>
    </div>

    <!-- Si hubo un error -->
    <div v-else>
      <p style="color: red;">Error al cargar el producto. Verifica la URL o intenta de nuevo.</p>
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
    console.log("ID recibido desde la URL:", productId)

    if (productId) {
      try {
        const res = await api.get(`/products/${productId}`)
        this.product = res.data
        console.log("Producto cargado:", res.data)
      } catch (error) {
        console.error("Error al cargar producto:", error.response?.data || error)
        this.product = null
      } finally {
        this.loading = false
      }
    } else {
      console.warn("No se proporcionó productId en la URL.")
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
        alert('Error al apartar el producto: ' + (error.response?.data?.message || 'Error desconocido'))
        console.error('Error al apartar:', error.response?.data || error)
      }
    }
  }
}
</script>

<style scoped>
.reservation-container {
  max-width: 600px;
  margin: 2rem auto;
  padding: 2rem;
  background-color: #f9fafb;
  border-radius: 8px;
  box-shadow: 0 0 10px rgba(0,0,0,0.05);
}

h2 {
  color: #4f46e5;
  margin-bottom: 1.5rem;
  text-align: center;
}

.product-info p {
  margin: 0.3rem 0;
  font-size: 1rem;
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
  padding: 0.6rem;
  font-size: 1rem;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  outline: none;
}

input:focus,
select:focus {
  border-color: #4f46e5;
  box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2);
}

button {
  padding: 0.75rem;
  background-color: #4f46e5;
  color: white;
  border: none;
  border-radius: 6px;
  font-weight: bold;
  font-size: 1rem;
  cursor: pointer;
  transition: background-color 0.3s;
}

button:hover {
  background-color: #4338ca;
}
</style>
