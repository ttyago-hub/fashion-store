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
<<<<<<< HEAD
import { getProductImageUrl } from '../config/api';

=======
>>>>>>> 44172495fe341cb5435355a45143c79aa45e0ca4
export default {
  name: 'ProductCard',
  props: {
    product: { type: Object, required: true }
  },
  computed: {
    // Computar la URL completa de la imagen del producto (o imagen por defecto si no hay)
    productImageUrl() {
<<<<<<< HEAD
      return getProductImageUrl(this.product.image);
=======
      if (this.product.image) {
        return `/storage/products/${this.product.image}`;
      } else {
        // Si no hay imagen, usar una imagen por defecto (asegúrate que default.png existe en public/storage/products)
        return `/storage/products/default.png`;
      }
>>>>>>> 44172495fe341cb5435355a45143c79aa45e0ca4
    },
    // Formatear el precio, asumiendo que product.price es numérico
    formattedPrice() {
      // Ejemplo simple: agregar símbolo de dólar. Puedes adaptarlo a otra moneda/formato.
      return `$${parseFloat(this.product.price).toFixed(2)}`;
    }
  }
}
</script>
