<template>
  <div class="inventory-container">
    <h2>Inventario de productos</h2>

    <button @click="printInventory" class="print-btn">🖨️ Imprimir PDF</button>

    <div v-if="products.length" id="print-area">
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
          headers: { Authorization: `Bearer ${token}` }
        })
        this.products = response.data
      } catch (error) {
        alert('Error al obtener el inventario')
        console.error(error.response?.data || error.message)
      }
    },
    printInventory() {
      const originalTitle = document.title
      document.title = "Inventario - Fashion Store"
      window.print()
      document.title = originalTitle
    }
  },
  mounted() {
    this.fetchInventory()
  }
}
</script>

<style scoped>
.inventory-container {
  max-width: 1100px;
  margin: 2rem auto;
  padding: 2rem;
  background-color: #f9fafb;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
  font-family: 'Segoe UI', sans-serif;
}

h2 {
  text-align: center;
  color: #4f46e5;
  margin-bottom: 2rem;
}

.print-btn {
  background-color: #10b981;
  color: white;
  border: none;
  padding: 0.6rem 1.2rem;
  border-radius: 6px;
  cursor: pointer;
  font-weight: bold;
  margin-bottom: 1.5rem;
  display: block;
  margin-left: auto;
}

.print-btn:hover {
  background-color: #059669;
}

.inventory-table {
  width: 100%;
  border-collapse: collapse;
  background-color: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
  font-size: 0.95rem;
}

.inventory-table thead {
  background-color: #4f46e5;
  color: white;
}

.inventory-table th,
.inventory-table td {
  padding: 14px 18px;
  text-align: left;
  border-bottom: 1px solid #e5e7eb;
}

.inventory-table tbody tr:hover {
  background-color: #f3f4f6;
}

.no-products {
  text-align: center;
  color: #6b7280;
  font-size: 1.1rem;
  margin-top: 3rem;
}

/* Estilos para imprimir solo la tabla */
@media print {
  body * {
    visibility: hidden;
  }

  #print-area, #print-area * {
    visibility: visible;
  }

  #print-area {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
  }

  .print-btn {
    display: none;
  }
}
</style>
