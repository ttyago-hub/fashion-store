<template>
  <div class="dashboard-container">
    <h2>Gestión de productos (Administrador)</h2>

    <!-- Formulario de creación/edición -->
    <form @submit.prevent="submitForm" enctype="multipart/form-data" class="product-form">
      <input v-model="form.name" placeholder="Nombre del producto" required />
      <input v-model="form.category" placeholder="Categoría" required />
      <input type="number" v-model.number="form.price" placeholder="Precio" step="0.01" required />
      <input type="number" v-model.number="form.stock" placeholder="Stock" required />
      <textarea v-model="form.description" placeholder="Descripción"></textarea>

      <input type="file" @change="handleImage" accept="image/*" />

      <div v-if="form.currentImage && !form.image" class="image-preview">
        <p>Imagen actual:</p>
        <img :src="getImageUrl(form.currentImage)" alt="Imagen actual" width="150" />
      </div>

      <div class="button-group">
        <button type="submit">{{ form.id ? 'Actualizar' : 'Crear' }}</button>
        <button v-if="form.id" type="button" @click="cancelEdit" class="cancel-btn">Cancelar</button>
      </div>
    </form>

    <hr />

    <!-- Lista de productos -->
    <table class="product-table">
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
            <img v-if="product.image" :src="getImageUrl(product.image)" alt="Imagen producto" width="80" />
            <span v-else>No hay imagen</span>
          </td>
          <td>
            <button @click="editProduct(product)">Editar</button>
            <button @click="deleteProduct(product.id)" class="delete-btn">Eliminar</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
<script>
import api from '../axios';

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
        image: null,
        currentImage: null
      }
    };
  },
  methods: {
    async fetchProducts() {
      try {
        const response = await api.get('/products');
        this.products = response.data;
      } catch (error) {
        console.error('Error al obtener productos:', error);
      }
    },
    handleImage(event) {
      this.form.image = event.target.files[0];
    },
    async submitForm() {
      const formData = new FormData();
      formData.append('name', this.form.name);
      formData.append('category', this.form.category);
      formData.append('price', this.form.price);
      formData.append('stock', this.form.stock);
      formData.append('description', this.form.description);
      if (this.form.image) {
        formData.append('image', this.form.image);
      }

      try {
        if (this.form.id) {
          // Actualizar producto
          await api.post(`/products/${this.form.id}?_method=PUT`, formData);
        } else {
          // Crear producto
          await api.post('/products', formData);
        }
        this.resetForm();
        this.fetchProducts();
      } catch (error) {
        console.error('Error al guardar el producto:', error);
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
        image: null,
        currentImage: product.image
      };
    },
    cancelEdit() {
      this.resetForm();
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
      };
    },
    async deleteProduct(id) {
      if (confirm('¿Estás seguro de que deseas eliminar este producto?')) {
        try {
          await api.delete(`/products/${id}`);
          this.fetchProducts();
        } catch (error) {
          console.error('Error al eliminar producto:', error);
        }
      }
    },
    getImageUrl(filename) {
      return `http://127.0.0.1:8000/storage/products/${filename}`;
    }
  },
  mounted() {
    this.fetchProducts();
  }
};
</script>

<style scoped>
.dashboard-container {
  max-width: 1100px;
  margin: 0 auto;
  padding: 2rem;
  background-color: #f3f4f6;
}

h2 {
  color: #4f46e5;
  margin-bottom: 1.5rem;
}

.product-form {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 1rem;
  background-color: white;
  padding: 1.5rem;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  margin-bottom: 2rem;
}

.product-form input,
.product-form textarea {
  padding: 0.6rem;
  border: 1px solid #ccc;
  border-radius: 5px;
  font-size: 0.95rem;
}

textarea {
  grid-column: span 2;
  resize: vertical;
  min-height: 80px;
}

.image-preview {
  grid-column: span 2;
  margin-top: 1rem;
}

.button-group {
  grid-column: span 2;
  display: flex;
  gap: 1rem;
  margin-top: 1rem;
}

button {
  padding: 0.6rem 1.2rem;
  border: none;
  border-radius: 5px;
  font-weight: bold;
  cursor: pointer;
  background-color: #4f46e5;
  color: white;
  transition: background-color 0.3s;
}

button:hover {
  background-color: #4338ca;
}

.cancel-btn {
  background-color: #9ca3af;
}

.cancel-btn:hover {
  background-color: #6b7280;
}

.delete-btn {
  background-color: #ef4444;
}

.delete-btn:hover {
  background-color: #dc2626;
}

.product-table {
  width: 100%;
  border-collapse: collapse;
  background-color: white;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.product-table th,
.product-table td {
  padding: 0.9rem;
  border: 1px solid #e5e7eb;
  text-align: center;
}

.product-table th {
  background-color: #4f46e5;
  color: white;
}
</style>
