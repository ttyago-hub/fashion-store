<template>
  <div class="home-container">
    <!-- Indicador de redirección automática -->
    <div v-if="isRedirecting" class="redirect-overlay">
      <div class="redirect-content">
        <div class="spinner"></div>
        <h2>¡Hola {{ userName }}! 👋</h2>
        <p>Redirigiendo a tu panel de {{ userRole }}...</p>
      </div>
    </div>

    <!-- Contenido normal del home -->
    <div v-else class="normal-content">
      <!-- Hero principal -->
      <section class="hero-section">
        <div class="hero-overlay">
          <div class="hero-content">
            <h1 class="hero-title">Nueva Colección 2025</h1>
            <p class="hero-subtitle">Explora tu estilo con elegancia y actitud</p>
            <router-link to="/products" class="hero-button">Comprar Ahora</router-link>
          </div>
        </div>
      </section>

      <!-- Productos destacados -->
      <section class="featured-products" v-if="products && products.length > 0">
        <h2 class="title">Productos Destacados</h2>
        <div class="product-grid">
          <div v-for="product in featuredProducts" :key="product.id" class="product-card">
            <div class="product-info">
              <h3>{{ product.name || 'Sin nombre' }}</h3>
              <p class="description">{{ product.description || 'Sin descripción' }}</p>
              <p><strong>$ {{ product.price ? Number(product.price).toFixed(2) : '0.00' }}</strong></p>
              <p>{{ product.category || 'Sin categoría' }}</p>
            </div>
          </div>
        </div>
      </section>

      <!-- Mensaje si no hay productos -->
      <section v-else class="no-products">
        <h2>Cargando productos...</h2>
      </section>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Home',
  data() {
    return {
      products: [],
      isRedirecting: false,
      userName: '',
      userRole: '',
      error: null
    }
  },
  computed: {
    featuredProducts() {
      if (!this.products || !Array.isArray(this.products)) {
        return []
      }
      return this.products.slice(0, 4)
    }
  },
  async mounted() {
    console.log('🏠 Home component mounted')
    
    // Verificar si hay usuario autenticado
    this.checkAutoRedirect()
    
    // Cargar productos si no se está redirigiendo
    if (!this.isRedirecting) {
      await this.loadProducts()
    }
  },
  methods: {
    checkAutoRedirect() {
      try {
        const token = localStorage.getItem('token')
        const userStr = localStorage.getItem('user')
        const user = userStr ? JSON.parse(userStr) : null

        console.log('🔍 Checking auth:', { hasToken: !!token, user: user?.name })

        if (token && user) {
          console.log('🔄 Usuario autenticado detectado, preparando redirección...')
          this.isRedirecting = true
          this.userName = user.name || 'Usuario'
          this.userRole = user.role === 'admin' ? 'administrador' : 'usuario'
          
          // Redirección con timeout
          setTimeout(() => {
            const dashboardRoute = user.role === 'admin' ? '/admin' : '/user'
            console.log('➡️ Redirigiendo a:', dashboardRoute)
            this.$router.push(dashboardRoute)
          }, 2000)
        }
      } catch (error) {
        console.error('❌ Error en checkAutoRedirect:', error)
        this.isRedirecting = false
      }
    },
    
    async loadProducts() {
      try {
        console.log('📦 Cargando productos...')
        
        // Simulamos productos por ahora para evitar errores de axios
        this.products = [
          {
            id: 1,
            name: 'Producto de ejemplo',
            description: 'Descripción del producto',
            price: 29.99,
            category: 'Ropa'
          }
        ]
        
        console.log('✅ Productos cargados:', this.products.length)
      } catch (error) {
        console.error('❌ Error cargando productos:', error)
        this.products = []
        this.error = 'No se pudieron cargar los productos'
      }
    }
  }
}
</script>

<style scoped>
.home-container {
  min-height: 100vh;
}

/* Pantalla de redirección */
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

/* Spinner */
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

/* Hero section */
.hero-section {
  height: 60vh;
  background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  text-align: center;
}

.hero-overlay {
  background: rgba(0, 0, 0, 0.3);
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.hero-content {
  max-width: 600px;
  padding: 2rem;
}

.hero-title {
  font-size: 3rem;
  font-weight: 700;
  margin-bottom: 1rem;
}

.hero-subtitle {
  font-size: 1.3rem;
  margin-bottom: 2rem;
  opacity: 0.9;
}

.hero-button {
  display: inline-block;
  padding: 1rem 2rem;
  background: #ff6b6b;
  color: white;
  text-decoration: none;
  border-radius: 8px;
  font-weight: 600;
  transition: all 0.3s ease;
}

.hero-button:hover {
  background: #ff5252;
  transform: translateY(-2px);
}

/* Products section */
.featured-products {
  padding: 4rem 2rem;
  max-width: 1200px;
  margin: 0 auto;
}

.title {
  text-align: center;
  font-size: 2.5rem;
  margin-bottom: 3rem;
  color: #333;
}

.product-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 2rem;
}

.product-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  transition: all 0.3s ease;
}

.product-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.product-info {
  padding: 1.5rem;
}

.product-info h3 {
  font-size: 1.3rem;
  margin-bottom: 0.5rem;
  color: #333;
}

.description {
  color: #666;
  margin-bottom: 1rem;
  font-size: 0.95rem;
}

.no-products {
  text-align: center;
  padding: 4rem 2rem;
  color: #666;
}

/* Responsive */
@media (max-width: 768px) {
  .hero-title {
    font-size: 2rem;
  }
  
  .hero-subtitle {
    font-size: 1.1rem;
  }
  
  .product-grid {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }
}
</style>
