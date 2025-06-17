<template>
  <div class="p-4">
    <h2 class="text-xl font-bold">Productos</h2>
    <ul>
      <li v-for="p in products" :key="p.id">{{ p.name }} - ${{ p.price }}</li>
    </ul>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from '../axios'

const products = ref([])

onMounted(async () => {
  const res = await axios.get('/products')
  products.value = res.data
  console.log('✅ Productos:', res.data)
})
</script>
