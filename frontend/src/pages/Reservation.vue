<template>
  <div>
    <h2>Apartar producto</h2>

    <div v-if="product">
      <p><strong>Producto:</strong> {{ product.name }}</p>
      <p><strong>Precio:</strong> ${{ product.price }}</p>
      <p><strong>Stock disponible:</strong> {{ product.stock }}</p>

      <form @submit.prevent="submitReservation">
        <label>Cantidad:</label>
        <input type="number" v-model.number="quantity" min="1" :max="product.stock" required />

        <label>Tipo de entrega:</label>
        <select v-model="deliveryType" required>
          <option value="">Selecciona una opción</option>
          <option value="pickup">Retiro en tienda</option>
          <option value="delivery">Entrega a domicilio</option>
        </select>

        <div v-if="deliveryType === 'delivery'">
          <label>Dirección de entrega:</label>
          <input v-model="deliveryAddress" placeholder="Calle principal, número, ciudad..." required />
        </div>

        <button type="submit">Confirmar apartado</button>
      </form>
    </div>
    <div v-else>
      <p>Cargando información del producto...</p>
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
      deliveryAddress: ''
    }
  },
  methods: {
    async fetchProduct() {
      const productId = this.$route.query.productId
      try {
        const response = await api.get(`/products/${productId}`)
        this.product = response.data
      } catch (error) {
        console.error('Error cargando producto:', error)
      }
    },
    async submitReservation() {
      const token = localStorage.getItem('token')
      if (!token) {
        alert('Debes iniciar sesión para apartar productos')
        this.$router.push('/login')
        return
      }

      try {
        const response = await api.post(
          '/reserve',
          {
            product_id: this.product.id,
            quantity: this.quantity,
            delivery_type: this.deliveryType,
            delivery_address: this.deliveryType === 'delivery' ? this.deliveryAddress : null
          },
          {
            headers: {
              Authorization: `Bearer ${token}`
            }
          }
        )

        if (this.deliveryType === 'pickup') {
          alert(`Reserva confirmada. Tu código de retiro es: ${response.data.delivery_code}`)
        } else {
          alert('Reserva confirmada. Recibirás los productos en tu domicilio.')
        }

        this.$router.push('/')
      } catch (error) {
        console.error(error.response?.data || error.message)
        alert('Error al realizar la reserva')
      }
    }
  },
  mounted() {
    this.fetchProduct()
  }
}
</script>
