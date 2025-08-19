<template>
  <div class="products-container">
    <h2 class="title">Productos disponibles</h2>

    <div class="filters">
      <input v-model="search" placeholder="Buscar por nombre o descripción..." />
      <select v-model="filterCategory">
        <option value="">Todas las categorías</option>
        <option value="camisa">Camisas</option>
        <option value="pantalone">Pantalones</option>
        <option value="zapato">Zapatos</option>
      </select>
    </div>

    <div v-if="filteredProducts.length" class="product-grid">
      <div v-for="product in filteredProducts" :key="product.id" class="product-card">
        <img
          v-if="product.image"
          :src="`http://127.0.0.1:8000/storage/products/${product.image}`"
          alt="Producto"
          class="product-image"
        />

        <div class="product-info">
          <h3>{{ product.name }}</h3>
          <p class="description">{{ product.description }}</p>
          <p><strong>Precio:</strong> ${{ Number(product.price).toFixed(2) }}</p>
          <p><strong>Categoría:</strong> {{ product.category }}</p>
          <button @click="reserve(product)">Apartar</button>
        </div>
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
  max-width: 1200px;
  margin: auto;
  padding: 3rem 2rem;
  background-color: #fff;
}

.title {
  text-align: center;
  font-size: 2.5rem;
  font-weight: bold;
  color: #111;
  margin-bottom: 2rem;
}

.filters {
  display: flex;
  justify-content: center;
  flex-wrap: wrap;
  gap: 1rem;
  margin-bottom: 2rem;
}

.filters input,
.filters select {
  padding: 0.8rem 1rem;
  font-size: 1rem;
  border: 1px solid #ccc;
  border-radius: 8px;
  min-width: 240px;
}

.product-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 2rem;
}

.product-card {
  display: flex;
  flex-direction: column;
  background-color: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  overflow: hidden;
  transition: transform 0.2s ease;
}


.product-card:hover {
  transform: translateY(-5px);
}

.product-image {
  width: 100%;
  height: 380px; /* Aumentamos un poco la altura para que entre más prenda */
  object-fit: cover;
  object-position: center 25%;
  border-top-left-radius: 12px;
  border-top-right-radius: 12px;
}



.product-info {
  padding: 1rem;
}

.product-info h3 {
  font-size: 1.2rem;
  font-weight: bold;
  margin-bottom: 0.5rem;
  color: #111;
}

.description {
  font-size: 0.95rem;
  color: #555;
  margin-bottom: 0.5rem;
}

.product-info p {
  margin: 0.2rem 0;
  color: #444;
  font-size: 0.95rem;
}

button {
  margin-top: 1rem;
  width: 100%;
  padding: 0.8rem;
  background-color: #111;
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.3s;
}

button:hover {
  background-color: #333;
}

.no-products {
  text-align: center;
  color: #777;
  font-size: 1.1rem;
  margin-top: 2rem;
<<<<<<< HEAD
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
=======
>>>>>>> 44172495fe341cb5435355a45143c79aa45e0ca4
}
</style>
