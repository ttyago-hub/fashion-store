<template>
  <div class="home-container">
<<<<<<< HEAD
    <!-- Indicador de redirección automática -->
    <div v-if="isRedirecting" class="redirect-overlay">
      <div class="redirect-content">
        <div class="spinner"></div>
        <h2>¡Hola {{ userName }}! 👋</h2>
        <p>Redirigiendo a tu panel de {{ userRole }}...</p>
      </div>
    </div>

    <!-- Contenido normal del home -->
    <template v-else>
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
              :src="getProductImageUrl(product.image)"
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
    </template>
=======
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
>>>>>>> 44172495fe341cb5435355a45143c79aa45e0ca4
  </div>
</template>

<script>
import api from '../axios'
import { getProductImageUrl } from '../config/api'

export default {
  data() {
    return {
      products: [],
      isRedirecting: false,
      userName: '',
      userRole: ''
    }
  },
  computed: {
    featuredProducts() {
      // Cambia esto si tienes un campo específico que indique si es destacado
      return this.products.slice(0, 4)
    }
  },
  computed: {
    featuredProducts() {
      // Cambia esto si tienes un campo específico que indique si es destacado
      return this.products.slice(0, 4)
    }
  },
  async mounted() {
<<<<<<< HEAD
    // Verificar si hay usuario autenticado para redirección automática
    this.checkAutoRedirect();
    
    // Cargar productos solo si no se está redirigiendo
    if (!this.isRedirecting) {
      await this.loadProducts();
    }
  },
  methods: {
    getProductImageUrl,
    
    checkAutoRedirect() {
      const token = localStorage.getItem('token');
      const user = localStorage.getItem('user') 
        ? JSON.parse(localStorage.getItem('user')) 
        : null;

      if (token && user) {
        console.log('🔄 Usuario autenticado detectado en Home, preparando redirección...');
        this.isRedirecting = true;
        this.userName = user.name;
        this.userRole = user.role === 'admin' ? 'administrador' : 'usuario';
        
        // El router guard se encargará de la redirección automática
        // Pero por si acaso, agregamos un backup
        setTimeout(() => {
          const dashboardRoute = user.role === 'admin' ? '/admin' : '/user';
          this.$router.push(dashboardRoute);
        }, 2000);
      }
    },
    
    async loadProducts() {
      try {
        const res = await api.get('/products')
        this.products = Array.isArray(res.data) ? res.data : res.data.data
      } catch (e) {
        this.products = []
        this.error = 'No se pudieron cargar los productos. Intenta más tarde.'
      }
=======
    try {
      const res = await api.get('/products')
      this.products = Array.isArray(res.data) ? res.data : res.data.data
    } catch (e) {
      this.products = []
      this.error = 'No se pudieron cargar los productos. Intenta más tarde.'
>>>>>>> 44172495fe341cb5435355a45143c79aa45e0ca4
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
<<<<<<< HEAD
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
}

/* Estilos para la pantalla de redirección automática */
.redirect-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
}

.redirect-content {
  text-align: center;
  color: white;
  padding: 2rem;
}

.redirect-content h2 {
  font-size: 2rem;
  margin-bottom: 1rem;
  font-weight: 600;
}

.redirect-content p {
  font-size: 1.2rem;
  opacity: 0.9;
  margin-bottom: 2rem;
}

/* Spinner de carga */
.spinner {
  width: 60px;
  height: 60px;
  border: 4px solid rgba(255, 255, 255, 0.3);
  border-top: 4px solid white;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 2rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
=======
>>>>>>> 44172495fe341cb5435355a45143c79aa45e0ca4
</style>
