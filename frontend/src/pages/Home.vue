<template>
  <div class="home-container">
    <!-- Hero principal con imagen -->
    <section
      class="hero-section"
      :style="{
        backgroundImage: `url('http://127.0.0.1:8000/storage/images/hero1.jpg')`,
      }"
    >
      <div class="hero-overlay">
        <div class="hero-content">
          <h1 class="hero-title">Nueva Colección 2025</h1>
          <p class="hero-subtitle">Explora tu estilo con elegancia y actitud</p>
          <router-link to="/products" class="hero-button">Comprar Ahora</router-link>
        </div>
      </div>
    </section>

    <!-- Productos destacados -->
    <section class="featured-products" v-if="products.length">
      <h2 class="title">Productos Destacados</h2>

      <div class="product-grid">
        <div v-for="product in featuredProducts" :key="product.id" class="product-card">
          <img
            v-if="product.image"
            :src="`http://127.0.0.1:8000/storage/products/${product.image}`"
            alt="Producto"
            class="product-image"
          />
          <div class="product-info">
            <h3>{{ product.name }}</h3>
            <p class="description">{{ product.description }}</p>
            <p><strong>$ {{ Number(product.price).toFixed(2) }}</strong></p>
            <p>{{ product.category }}</p>
          </div>
        </div>
      </div>
    </section>
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
  computed: {
    featuredProducts() {
      // Cambia esto si tienes un campo específico que indique si es destacado
      return this.products.slice(0, 4)
    }
  },
  async mounted() {
    try {
      const res = await api.get('/products')
      this.products = Array.isArray(res.data) ? res.data : res.data.data
    } catch (e) {
      this.products = []
      this.error = 'No se pudieron cargar los productos. Intenta más tarde.'
    }
  }
}
</script>

<style scoped>
/* Hero */
.hero-section {
  height: 100vh;
  background-size: cover;
  background-position: center;
  position: relative;
}

.hero-overlay {
  background-color: rgba(0, 0, 0, 0.4);
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
}

.hero-content {
  text-align: center;
  color: white;
}

.hero-title {
  font-size: 3rem;
  font-weight: bold;
  margin-bottom: 1rem;
}

.hero-subtitle {
  font-size: 1.2rem;
  margin-bottom: 2rem;
}

.hero-button {
  background-color: white;
  color: black;
  padding: 0.75rem 1.5rem;
  text-decoration: none;
  font-weight: 600;
  border-radius: 6px;
  transition: background 0.3s;
}

.hero-button:hover {
  background-color: #f3f3f3;
}

/* Productos destacados */
.featured-products {
  padding: 4rem 2rem;
  max-width: 1200px;
  margin: 0 auto;
  text-align: center;
}

.featured-products h2 {
  font-size: 2rem;
  margin-bottom: 2rem;
  color: #111;
}

.product-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  gap: 2rem;
}

.product-card {
  background: #fff;
  border: 1px solid #eee;
  border-radius: 12px;
  text-align: left;
  transition: transform 0.2s ease;
  overflow: hidden;
}

.product-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.08);
}

.product-image {
  width: 100%;
  height: 380px;
  object-fit: cover;
  object-position: center 25%;
  background-color: #f0f0f0;
  border-top-left-radius: 12px;
  border-top-right-radius: 12px;
}

.product-info {
  padding: 1rem;
}

.product-info h3 {
  font-size: 1.2rem;
  font-weight: bold;
  color: #111;
  margin-bottom: 0.4rem;
}

.description {
  color: #555;
  font-size: 0.95rem;
  margin-bottom: 0.4rem;
}

.product-info p {
  font-size: 0.95rem;
  color: #333;
}
</style>
