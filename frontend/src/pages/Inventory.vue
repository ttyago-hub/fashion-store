<template>
  <div class="inventory-container">
    <h2>Inventario de productos</h2>

    <div v-if="products.length">
      <table class="inventory-table">
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
            <td>${{ product.price.toFixed(2) }}</td>
            <td>{{ product.stock }}</td>
            <td>{{ new Date(product.updated_at).toLocaleString() }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-else class="no-products">
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

<style scoped>
.inventory-container {
  max-width: 1000px;
  margin: 2rem auto;
  padding: 1rem;
  background-color: #f9fafb;
  border-radius: 8px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
}

h2 {
  text-align: center;
  color: #4f46e5;
  margin-bottom: 1.5rem;
}

.inventory-table {
  width: 100%;
  border-collapse: collapse;
  background-color: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
}

.inventory-table th,
.inventory-table td {
  padding: 12px 15px;
  text-align: left;
  border-bottom: 1px solid #e5e7eb;
}

.inventory-table thead {
  background-color: #4f46e5;
  color: white;
}

.inventory-table tr:hover {
  background-color: #f3f4f6;
}

.no-products {
  text-align: center;
  color: #6b7280;
  font-size: 1.1rem;
  margin-top: 2rem;
}
</style>
