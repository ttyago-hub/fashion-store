<template>
  <div class="border rounded-lg overflow-hidden hover:shadow-lg flex flex-col">
    <!-- Imagen del producto -->
    <img :src="productImageUrl" alt="Imagen de {{ product.name }}" class="h-48 w-full object-cover">
    <!-- Detalles del producto -->
    <div class="p-4 flex flex-col flex-grow">
      <h3 class="font-semibold text-lg mb-1">{{ product.name }}</h3>
      <p class="text-gray-700 mb-2">{{ formattedPrice }}</p>
      <!-- Botón para ver detalles del producto -->
      <router-link v-if="$router" :to="{ name: 'ProductDetail', params: { id: product.id } }" class="mt-auto text-center bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700">
        Ver Detalle
      </router-link>
      <!-- Si no se usa Vue Router, se puede usar un enlace normal: -->
      <a v-else :href="`/producto/${product.id}`" class="mt-auto text-center bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700">
        Ver Detalle
      </a>
    </div>
  </div>
</template>

<script>
import { getProductImageUrl } from '../config/api';

export default {
  name: 'ProductCard',
  props: {
    product: { type: Object, required: true }
  },
  computed: {
    // Computar la URL completa de la imagen del producto (o imagen por defecto si no hay)
    productImageUrl() {
      return getProductImageUrl(this.product.image);
    },
    // Formatear el precio, asumiendo que product.price es numérico
    formattedPrice() {
      // Ejemplo simple: agregar símbolo de dólar. Puedes adaptarlo a otra moneda/formato.
      return `$${parseFloat(this.product.price).toFixed(2)}`;
    }
  }
}
</script>
