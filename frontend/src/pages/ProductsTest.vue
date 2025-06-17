<template>
  <div class="p-4">
    <h2 class="text-xl font-bold">Productos</h2>
    <ul>
      <li v-for="product in products" :key="product.id">
        {{ product.name }} - Stock: {{ product.stock }}
      </li>
    </ul>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import axios from '../axios'

const products = ref([])

onMounted(async () => {
  try {
    const res = await axios.get('/products')
    products.value = res.data
    console.log('✅ Productos cargados:', res.data)
  } catch (error) {
    console.error('❌ Error al obtener productos:', error)
  }
})
</script>

