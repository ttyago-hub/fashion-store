<template>
  <div class="home-container">
    <div class="home-card animate-pop">
      <h1>¡Bienvenido a Fashion Store!</h1>
      <p>Explora nuestra colección y aparta tus productos favoritos.</p>
      <router-link to="/products" class="home-button">Ver productos</router-link>
    </div>

    <!-- Vista previa de productos -->
    <div class="preview-products" v-if="products.length">
      <h2 style="margin-top:2rem;">Productos destacados</h2>
      <div class="preview-list">
        <div v-for="product in products.slice(0, 4)" :key="product.id" class="preview-card">
          <img
            v-if="product.image"
            :src="getImageUrl(product.image)"
            alt="Imagen"
            width="300"
            style="display:block;margin:0 auto 1rem;"
          />
          <span v-else class="text-muted">Sin imagen</span>
          <h3>{{ product.name }}</h3>
          <p class="description">{{ product.description }}</p>
          <p><strong>Precio:</strong> ${{ Number(product.price).toFixed(2) }}</p>
          <p><strong>Categoría:</strong> {{ product.category }}</p>
        </div>
      </div>
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
  async mounted() {
    try {
      const res = await api.get('/products')
      this.products = Array.isArray(res.data) ? res.data : res.data.data
    } catch (e) {
      this.products = []
    }
  },
  methods: {
    getImageUrl(filename) {
      return 'http://127.0.0.1:8000/storage/products/${filename}' // Asegúrate de que esta URL sea correcta según tu configuración;
    }
  }
}
</script>

<style scoped>
.home-container {
  background-color: #f3f4f6;
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem;

  /* Fondo con logo */
  background-image: url('public/');  /* Ajusta la ruta si es otra */
  background-repeat: no-repeat;
  background-position: center top; /* o center center según prefieras */
  background-size: 900px 700px; /* tamaño del logo */
  background-attachment: fixed;
}

.home-card {
  background-color: white;
  padding: 3rem 2rem;
  border-radius: 16px;
  box-shadow: 0 12px 25px rgba(0, 0, 0, 0.08);
  text-align: center;
  max-width: 500px;
  width: 100%;
  transition: transform 0.3s ease;
}

h1 {
  color: #4f46e5;
  font-size: 2.2rem;
  margin-bottom: 1rem;
}

p {
  color: #374151;
  font-size: 1.1rem;
  margin-bottom: 2rem;
  line-height: 1.6;
}

.home-button {
  background-color: #4f46e5;
  color: white;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  text-decoration: none;
  font-weight: bold;
  font-size: 1rem;
  box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
  transition: all 0.3s ease;
  display: inline-block;
}

.home-button:hover {
  background-color: #4338ca;
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(67, 56, 202, 0.35);
}

/* Animación de entrada */
@keyframes pop {
  0% {
    transform: scale(0.9);
    opacity: 0;
  }
  100% {
    transform: scale(1);
    opacity: 1;
  }
}

.animate-pop {
  animation: pop 0.4s ease-out;
}

.preview-products {
  margin-top: 2rem;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
  padding: 2rem 1rem;
  max-width: 1100px;
  margin-left: auto;
  margin-right: auto;
}

.preview-list {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 1.5rem;
  margin-top: 1rem;
}

.preview-card {
  background: #f9fafb;
  border-radius: 8px;
  padding: 1rem;
  box-shadow: 0 1px 4px rgba(0,0,0,0.04);
  text-align: left;
}

.preview-card h3 {
  margin: 0 0 0.5rem;
  color: #4f46e5;
}

.preview-card .description {
  color: #6b7280;
  font-size: 0.95rem;
  margin-bottom: 0.5rem;
}
</style>