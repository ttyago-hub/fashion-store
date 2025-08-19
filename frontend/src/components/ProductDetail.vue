<template>
  <div class="container mx-auto px-4 py-8">
    <!-- Estados de carga -->
    <div v-if="loading" class="loading-state">
      <div class="spinner"></div>
      <p>Cargando producto...</p>
    </div>

    <div v-else-if="error" class="error-state">
      <div class="error-icon">❌</div>
      <p>{{ error }}</p>
      <button @click="loadProduct" class="retry-btn">Reintentar</button>
    </div>

    <!-- Contenido del producto -->
    <div v-else-if="product" class="md:flex md:space-x-8">
      <!-- Imagen grande del producto -->
      <div class="md:w-1/2">
        <img :src="productImageUrl" :alt="product.name" class="w-full rounded-lg shadow">
      </div>
      <!-- Detalles del producto -->
      <div class="md:w-1/2 mt-6 md:mt-0">
        <h2 class="text-3xl font-bold mb-4">{{ product.name }}</h2>
        <p class="text-sm text-gray-600 mb-2">{{ product.category }}</p>
        <p class="text-2xl text-gray-800 font-semibold mb-4">{{ formattedPrice }}</p>
        <p class="text-gray-700 mb-6">{{ product.description }}</p>
        <p class="text-sm text-gray-600 mb-4">Stock disponible: {{ product.stock }}</p>
        
        <!-- Botones de acción -->
        <div class="flex gap-4">
          <router-link 
            :to="{ name: 'reservation', params: { productId: product.id } }"
            class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-lg transition-colors"
          >
            🛒 Reservar Producto
          </router-link>
          
          <router-link 
            to="/products" 
            class="bg-gray-600 hover:bg-gray-700 text-white font-semibold px-6 py-3 rounded-lg transition-colors"
          >
            ← Volver a Productos
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import api from '../axios';
import { getProductImageUrl } from '../config/api';

export default {
  name: 'ProductDetail',
  props: {
    id: { type: String, required: true }
  },
  data() {
    return {
      product: null,
      loading: true,
      error: null
    };
  },
  computed: {
    productImageUrl() {
      return this.product ? getProductImageUrl(this.product.image) : '';
    },
    formattedPrice() {
      return this.product ? `$${parseFloat(this.product.price).toFixed(2)}` : '$0.00';
    }
  },
  async mounted() {
    await this.loadProduct();
  },
  methods: {
    async loadProduct() {
      this.loading = true;
      this.error = null;
      
      try {
        const response = await api.get(`/products/${this.id}`);
        this.product = response.data;
      } catch (error) {
        console.error('Error loading product:', error);
        this.error = 'No se pudo cargar el producto. Por favor intenta nuevamente.';
      } finally {
        this.loading = false;
      }
    }
  }
}
</script>

<style scoped>
.loading-state, .error-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 4rem 2rem;
  text-align: center;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #f3f3f3;
  border-top: 4px solid #3498db;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.error-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
}

.retry-btn {
  background-color: #3498db;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  margin-top: 1rem;
}

.retry-btn:hover {
  background-color: #2980b9;
}

.container {
  max-width: 1200px;
}
</style>
