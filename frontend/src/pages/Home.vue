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
      <h2>Productos Destacados</h2>
      <div class="product-grid">
        <div
          v-for="product in products.slice(0, 4)"
          :key="product.id"
          class="product-card"
        >
          <img
            v-if="product.image"
            :src="getImageUrl(product.image)"
            alt="Producto"
          />
          <span v-else class="no-image">Sin imagen</span>
          <h3>{{ product.name }}</h3>
          <p class="description">{{ product.description }}</p>
          <p class="price">$ {{ Number(product.price).toFixed(2) }}</p>
          <p class="category">{{ product.category }}</p>
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
  async mounted() {
  try {
    const res = await api.get('/products')
    this.products = Array.isArray(res.data) ? res.data : res.data.data
  } catch (e) {
  this.products = []
  this.error = 'No se pudieron cargar los productos. Intenta más tarde.'
}

}
,
  methods: {
    getImageUrl(filename) {
      return `http://127.0.0.1:8000/storage/products/${filename}`
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
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 2rem;
}

.product-card {
  background: #fff;
  border: 1px solid #eee;
  border-radius: 12px;
  padding: 1rem;
  text-align: left;
  transition: transform 0.2s ease;
}

.product-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 6px 12px rgba(0,0,0,0.08);
}

.product-card img {
  width: 100%;
  height: 280px;
  object-fit: cover;
  border-radius: 8px;
  margin-bottom: 0.8rem;
}

.description {
  color: #555;
  font-size: 0.9rem;
  margin-bottom: 0.5rem;
}

.price {
  color: #000;
  font-weight: bold;
}

.category {
  font-size: 0.85rem;
  color: #666;
}

.no-image {
  display: block;
  width: 100%;
  height: 280px;
  background: #f0f0f0;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #999;
}
</style>
