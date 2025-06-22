<template>
  <div class="products-container">
    <h2>Productos disponibles</h2>

    <div class="filters">
      <input v-model="search" placeholder="Buscar por nombre..." />
      <select v-model="filterCategory">
        <option value="">Todas las categorías</option>
        <option value="camisas">Camisas</option>
        <option value="pantalones">Pantalones</option>
        <option value="zapatos">Zapatos</option>
      </select>
    </div>

    <div v-if="filteredProducts.length" class="product-list">
      <div v-for="product in filteredProducts" :key="product.id" class="product-card">
        <h3>{{ product.name }}</h3>
        <p class="description">{{ product.description }}</p>
        <p><strong>Precio:</strong> ${{ product.price.toFixed(2) }}</p>
        <p><strong>Categoría:</strong> {{ product.category }}</p>
        <button @click="reserve(product)">Apartar</button>
      </div>
    </div>

    <div v-else class="no-products">
      <p>No hay productos disponibles.</p>
    </div>
  </div>
</template>

<script>
import api from '../axios'

export default {
  data() {
    return {
      products: [],
      search: '',
      filterCategory: ''
    }
  },
  computed: {
    filteredProducts() {
      return this.products.filter(p => {
        return (
          p.name.toLowerCase().includes(this.search.toLowerCase()) &&
          (this.filterCategory === '' || p.category === this.filterCategory)
        )
      })
    }
  },
  methods: {
    async fetchProducts() {
      try {
        const response = await api.get('/products')
        this.products = response.data
      } catch (error) {
        console.error(error)
      }
    },
    reserve(product) {
      const user = JSON.parse(localStorage.getItem('user'))
      if (!user) {
        alert('Debes iniciar sesión para apartar un producto.')
        this.$router.push('/login')
        return
      }
      this.$router.push({ path: '/reserve', query: { productId: product.id } })
    }
  },
  mounted() {
    this.fetchProducts()
  }
}
</script>

<style scoped>
.products-container {
  max-width: 1000px;
  margin: 2rem auto;
  padding: 1.5rem;
  background-color: #f9fafb;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

h2 {
  text-align: center;
  color: #4f46e5;
  margin-bottom: 1.5rem;
}

.filters {
  display: flex;
  gap: 1rem;
  margin-bottom: 2rem;
  justify-content: center;
}

.filters input,
.filters select {
  padding: 0.6rem;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 1rem;
  width: 220px;
}

.product-list {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  gap: 1.5rem;
}

.product-card {
  background-color: white;
  padding: 1rem;
  border-radius: 8px;
  border: 1px solid #e5e7eb;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
  transition: transform 0.2s;
}

.product-card:hover {
  transform: translateY(-5px);
}

.product-card h3 {
  margin: 0 0 0.5rem;
  color: #1f2937;
}

.description {
  font-size: 0.9rem;
  color: #6b7280;
  margin-bottom: 0.5rem;
}

button {
  margin-top: 0.7rem;
  padding: 0.6rem 1rem;
  background-color: #4f46e5;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: background-color 0.3s;
  font-weight: bold;
}

button:hover {
  background-color: #4338ca;
}

.no-products {
  text-align: center;
  color: #6b7280;
  margin-top: 2rem;
}
</style>
