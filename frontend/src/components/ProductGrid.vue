<template>
  <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
    <!-- Iterar sobre la lista de productos y renderizar una tarjeta por cada uno -->
    <product-card v-for="prod in products" :key="prod.id" :product="prod" />
  </div>
</template>

<script>
import ProductCard from './ProductCard.vue';

export default {
  name: 'ProductGrid',
  components: { ProductCard },
  props: {
    products: { type: Array, required: true }
  }
}
</script>
