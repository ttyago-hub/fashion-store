<template>
  <div>
    <h2>Productos disponibles</h2>

    <input v-model="search" placeholder="Buscar por nombre..." />
    <select v-model="filterCategory">
      <option value="">Todas las categorías</option>
      <option value="camisas">Camisas</option>
      <option value="pantalones">Pantalones</option>
      <option value="zapatos">Zapatos</option>
    </select>

    <div v-if="products.length">
      <div v-for="product in filteredProducts" :key="product.id">
        <h3>{{ product.name }}</h3>
        <p>{{ product.description }}</p>
        <p>Precio: ${{ product.price }}</p>
        <p>Categoría: {{ product.category }}</p>
        <button @click="reserve(product)">Apartar</button>
      </div>
    </div>
    <div v-else>
      <p>No hay productos.</p>
    </div>
  </div>
</template>

<script>
import api from '../axios'

export default {
  data() {
    return {
      products: [],
      search: '',
      filterCategory: ''
    }
  },
  computed: {
    filteredProducts() {
      return this.products.filter(p => {
        return (
          p.name.toLowerCase().includes(this.search.toLowerCase()) &&
          (this.filterCategory === '' || p.category === this.filterCategory)
        )
      })
    }
  },
  methods: {
    async fetchProducts() {
      try {
        const response = await api.get('/products')
        this.products = response.data
      } catch (error) {
        console.error(error)
      }
    },
    reserve(product) {
      const user = JSON.parse(localStorage.getItem('user'))
      if (!user) {
        alert('Debes iniciar sesión para apartar un producto.')
        this.$router.push('/login')
        return
      }
      this.$router.push({ path: '/reserve', query: { productId: product.id } })
    }
  },
  mounted() {
    this.fetchProducts()
  }
}
</script>
