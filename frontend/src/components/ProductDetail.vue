<template>
  <div class="container mx-auto px-4 py-8">
    <div class="md:flex md:space-x-8">
      <!-- Imagen grande del producto -->
      <div class="md:w-1/2">
        <img :src="productImageUrl" :alt="product.name" class="w-full rounded-lg shadow">
      </div>
      <!-- Detalles del producto -->
      <div class="md:w-1/2 mt-6 md:mt-0">
        <h2 class="text-3xl font-bold mb-4">{{ product.name }}</h2>
        <p class="text-2xl text-gray-800 font-semibold mb-4">{{ formattedPrice }}</p>
        <p class="text-gray-700 mb-6">{{ product.description }}</p>
        <!-- Botón de agregar al carrito (funcionalidad a implementar) -->
        <button class="bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded">
          Agregar al carrito
        </button>
        <!-- En una app real, aquí podrías manejar la lógica de añadir al carrito -->
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ProductDetail',
  props: {
    product: { type: Object, required: true }
  },
  computed: {
    productImageUrl() {
      if (this.product.image) {
        return `/storage/products/${this.product.image}`;
      } else {
        return `/storage/products/default.png`;
      }
    },
    formattedPrice() {
      return `$${parseFloat(this.product.price).toFixed(2)}`;
    }
  }
}
</script>
