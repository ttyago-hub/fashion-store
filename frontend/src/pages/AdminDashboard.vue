<template>
  <div>
    <h2>Gestión de productos (Administrador)</h2>

    <!-- Formulario de creación/edición -->
    <form @submit.prevent="submitForm" enctype="multipart/form-data">
      <input v-model="form.name" placeholder="Nombre del producto" required />
      <input v-model="form.category" placeholder="Categoría" required />
      <input type="number" v-model.number="form.price" placeholder="Precio" step="0.01" required />
      <input type="number" v-model.number="form.stock" placeholder="Stock" required />
      <textarea v-model="form.description" placeholder="Descripción"></textarea>

      <input type="file" @change="handleImage" accept="image/*" />

      <!-- Mostrar imagen actual si existe y no hay imagen nueva -->
      <div v-if="form.currentImage && !form.image" style="margin: 10px 0;">
        <p>Imagen actual:</p>
        <img :src="getImageUrl(form.currentImage)" alt="Imagen actual" width="150" />
      </div>

      <button type="submit">{{ form.id ? 'Actualizar' : 'Crear' }}</button>
      <button v-if="form.id" type="button" @click="cancelEdit">Cancelar</button>
    </form>

    <hr />

    <!-- Lista de productos -->
    <table border="1" cellpadding="8" cellspacing="0" style="width: 100%; margin-top: 20px;">
      <thead>
        <tr>
          <th>Nombre</th>
          <th>Categoría</th>
          <th>Precio</th>
          <th>Stock</th>
          <th>Imagen</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="product in products" :key="product.id">
          <td>{{ product.name }}</td>
          <td>{{ product.category }}</td>
          <td>${{ product.price.toFixed(2) }}</td>
          <td>{{ product.stock }}</td>
          <td>
            <img v-if="product.image" :src="getImageUrl(product.image)" alt="Imagen producto" width="100" />
            <span v-else>No hay imagen</span>
          </td>
          <td>
            <button @click="editProduct(product)">Editar</button>
            <button @click="deleteProduct(product.id)">Eliminar</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import api from '../axios'

export default {
  data() {
    return {
      products: [],
      form: {
        id: null,
        name: '',
        category: '',
        price: 0,
        stock: 0,
        description: '',
        image: null,        // Archivo nuevo
        currentImage: null  // Imagen actual en edición
      }
    }
  },
  methods: {
    async fetchProducts() {
      const token = localStorage.getItem('token')
      try {
        const response = await api.get('/products', {
          headers: {
            Authorization: `Bearer ${token}`
          }
        })
        this.products = response.data
      } catch (error) {
        alert('Error al cargar productos')
        console.error(error)
      }
    },
    handleImage(e) {
      this.form.image = e.target.files[0]
    },
    async submitForm() {
      const token = localStorage.getItem('token')
      const formData = new FormData()

      formData.append('name', this.form.name)
      formData.append('category', this.form.category)
      formData.append('price', this.form.price)
      formData.append('stock', this.form.stock)
      formData.append('description', this.form.description)

      if (this.form.image) {
        formData.append('image', this.form.image)
      }

      try {
        if (this.form.id) {
          // Actualizar producto (usar POST con _method=PUT)
          await api.post(`/products/${this.form.id}?_method=PUT`, formData, {
            headers: {
              Authorization: `Bearer ${token}`,
              'Content-Type': 'multipart/form-data'
            }
          })
          alert('Producto actualizado con imagen')
        } else {
          // Crear nuevo producto
          await api.post('/products', formData, {
            headers: {
              Authorization: `Bearer ${token}`,
              'Content-Type': 'multipart/form-data'
            }
          })
          alert('Producto creado con imagen')
        }

        this.resetForm()
        this.fetchProducts()
      } catch (error) {
        alert('Error al guardar producto con imagen')
        console.error(error.response?.data || error.message)
      }
    },
    editProduct(product) {
      this.form = {
        id: product.id,
        name: product.name,
        category: product.category,
        price: product.price,
        stock: product.stock,
        description: product.description,
        image: null,            // Nuevo archivo vacío al editar
        currentImage: product.image
      }
    },
    cancelEdit() {
      this.resetForm()
    },
    resetForm() {
      this.form = {
        id: null,
        name: '',
        category: '',
        price: 0,
        stock: 0,
        description: '',
        image: null,
        currentImage: null
      }
    },
    async deleteProduct(id) {
      if (!confirm('¿Estás seguro de que deseas eliminar este producto?')) return

      const token = localStorage.getItem('token')
      try {
        await api.delete(`/products/${id}`, {
          headers: { Authorization: `Bearer ${token}` }
        })
        alert('Producto eliminado')
        this.fetchProducts()
      } catch (error) {
        alert('Error al eliminar producto')
        console.error(error.response?.data || error.message)
      }
    },
    getImageUrl(path) {
      return path ? `http://localhost:8000/storage/${path}` : ''
    }
  },
  mounted() {
    this.fetchProducts()
  }
}
</script>

<style scoped>
button {
  margin-right: 5px;
}
</style>
