<template>
  <div class="products-container">
    <h2>Productos disponibles</h2>

    <div class="filters">
      <input v-model="search" placeholder="Buscar por nombre o descripción..." />
      <select v-model="filterCategory">
        <option value="">Todas las categorías</option>
        <option value="camisa">Camisas</option>
        <option value="pantalone">Pantalones</option>
        <option value="zapato">Zapatos</option>
      </select>
    </div>

    <div v-if="filteredProducts.length" class="product-list">
      <div
        v-for="product in filteredProducts"
        :key="product.id"
        class="product-card"
      >
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
      const searchLower = this.search.toLowerCase()
      const categoryLower = this.filterCategory.toLowerCase()

      return this.products.filter(p => {
        const nameMatch = p.name.toLowerCase().includes(searchLower)
        const descriptionMatch = p.description.toLowerCase().includes(searchLower)
        const categoryMatch =
          this.filterCategory === '' || p.category.toLowerCase() === categoryLower

        return (nameMatch || descriptionMatch) && categoryMatch
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
  max-width: 1100px;
  margin: 2rem auto;
  padding: 2rem;
  background-color: #f9fafb;
  border-radius: 12px;
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.06);
   /* Fondo con logo */
  background-image: url('public/descarga (1).jpeg');  /* Ajusta la ruta si es otra */
  background-repeat: no-repeat;
  background-position: center top; /* o center center según prefieras */
  background-size: 900px 700px; /* tamaño del logo */
  background-attachment: fixed;
}

h2 {
  text-align: center;
  color: #4f46e5;
  font-size: 2rem;
  margin-bottom: 2rem;
}

.filters {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  justify-content: center;
  margin-bottom: 2rem;
}

.filters input,
.filters select {
  padding: 0.7rem 1rem;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 1rem;
  background-color: white;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
  transition: border-color 0.3s;
}

.filters input:focus,
.filters select:focus {
  border-color: #4f46e5;
  outline: none;
}

.product-list {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  gap: 2rem;
}

.product-card {
  background-color: white;
  padding: 1.5rem;
  border-radius: 10px;
  border: 1px solid #e5e7eb;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.product-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
}

.product-card h3 {
  margin: 0 0 0.5rem;
  font-size: 1.2rem;
  color: #111827;
}

.description {
  font-size: 0.95rem;
  color: #6b7280;
  margin-bottom: 0.8rem;
}

.product-card p {
  margin: 0.2rem 0;
  font-size: 0.95rem;
  color: #374151;
}

.product-card p strong {
  color: #1f2937;
}

button {
  margin-top: 1rem;
  padding: 0.7rem 1.2rem;
  background-color: #4f46e5;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-weight: bold;
  font-size: 0.95rem;
  box-shadow: 0 2px 5px rgba(79, 70, 229, 0.2);
  transition: background-color 0.3s, transform 0.2s;
}

button:hover {
  background-color: #4338ca;
  transform: scale(1.03);
}

.no-products {
  text-align: center;
  color: #6b7280;
  font-size: 1rem;
  margin-top: 3rem;
}
</style>
