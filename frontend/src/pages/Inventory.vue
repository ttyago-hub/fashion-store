<template>
  <div>
    <h2>Inventario de productos</h2>

    <div v-if="products.length">
      <table border="1" cellpadding="8">
        <thead>
          <tr>
            <th>Nombre</th>
            <th>Categoría</th>
            <th>Precio</th>
            <th>Stock</th>
            <th>Última actualización</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="product in products" :key="product.id">
            <td>{{ product.name }}</td>
            <td>{{ product.category }}</td>
            <td>${{ product.price }}</td>
            <td>{{ product.stock }}</td>
            <td>{{ new Date(product.updated_at).toLocaleString() }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-else>
      <p>No hay productos en el inventario.</p>
    </div>
  </div>
</template>

<script>
import api from '../axios'

export default {
  data() {
    return {
      products: []
    }
  },
  methods: {
    async fetchInventory() {
      const token = localStorage.getItem('token')
      try {
        const response = await api.get('/inventory', {
          headers: {
            Authorization: `Bearer ${token}`
          }
        })
        this.products = response.data
      } catch (error) {
        alert('Error al obtener el inventario')
        console.error(error.response?.data || error.message)
      }
    }
  },
  mounted() {
    this.fetchInventory()
  }
}
</script>
