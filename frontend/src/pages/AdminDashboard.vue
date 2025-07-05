<template>
  <div class="dashboard-container">
    <h2>Gestión de Productos (Administrador)</h2>

    <!-- Formulario de creación/edición -->
    <form @submit.prevent="submitForm" class="product-form">
      <input v-model="form.name" placeholder="Nombre del producto" required />
      <input v-model="form.category" placeholder="Categoría" required />
      <input v-model.number="form.price" type="number" step="0.01" placeholder="Precio" required />
      <input v-model.number="form.stock" type="number" placeholder="Stock" required />
      <textarea v-model="form.description" placeholder="Descripción"></textarea>

      <input type="file" @change="handleImage" accept="image/*" class="file-input" required />

      <div v-if="form.currentImage && !form.image" class="image-preview">
        <p>Imagen actual:</p>
        <img :src="getImageUrl(form.currentImage)" alt="Imagen actual" width="150" />
      </div>

      <div class="button-group">
        <button type="submit" class="btn primary">{{ form.id ? 'Actualizar 📝' : 'Crear ➕' }}</button>
        <button v-if="form.id" type="button" @click="cancelEdit" class="btn secondary">Cancelar ❌</button>
      </div>
    </form>

    <hr />

    <!-- Lista de productos -->
    <div class="table-responsive">
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
              <img v-if="product.image" :src="getImageUrl(product.image)" alt="Imagen" width="80" />
              <span v-else class="text-muted">Sin imagen</span>
            </td>
            <td>
              <button @click="editProduct(product)" class="btn edit">Editar ✏️</button>
              <button @click="deleteProduct(product.id)" class="btn delete">Eliminar 🗑️</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import api from '../axios';

export default {
  data() {
    return {
      form: {
        id: null,
        name: '',
        category: '',
        price: 0,
        stock: 0,
        description: '',
        image: null,
        currentImage: null
      },
      products: []
    }
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
      formData.append('price', this.form.price); // Debe ser 5.00, no 5,00
      formData.append('stock', this.form.stock);
      formData.append('description', this.form.description);

      if (this.form.image) {
        formData.append('image', this.form.image);
      }

      // Debug: muestra el contenido del FormData
      for (let pair of formData.entries()) {
        console.log(pair[0]+ ', ', pair[1]);
      }
      console.log('form:', this.form);

      const token = localStorage.getItem('token')
      try {
        await api.post('/products', formData, {
          headers: { Authorization: `Bearer ${token}` }
        })
        this.resetForm();
        this.fetchProducts();
      } catch (error) {
        if (error.response) {
          alert('Error al guardar el producto: ' + JSON.stringify(error.response.data));
          console.error('Error al guardar el producto:', error.response.data);
        } else if (error.request) {
          alert('Error de red o el backend no responde');
          console.error('Error de red:', error.request);
        } else {
          alert('Error desconocido: ' + error.message);
          console.error('Error:', error.message);
        }
      }
    },
    editProduct(product) {
      this.form.id = product.id;
      this.form.name = product.name;
      this.form.category = product.category;
      this.form.price = product.price;
      this.form.stock = product.stock;
      this.form.description = product.description;
      this.form.image = null;
      this.form.currentImage = product.image;
    },
    cancelEdit() {
      this.resetForm();
    },
    resetForm() {
      this.form.id = null;
      this.form.name = '';
      this.form.category = '';
      this.form.price = 0;
      this.form.stock = 0;
      this.form.description = '';
      this.form.image = null;
      this.form.currentImage = null;
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
  background-color: #f8f9fa;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

h2 {
  font-size: 1.8rem;
  color: #343a40;
  margin-bottom: 1.5rem;
}

.product-form {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 1rem;
  background-color: #fff;
  padding: 1.5rem;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
  margin-bottom: 2rem;
}

.product-form input,
.product-form textarea {
  padding: 0.7rem;
  border: 1px solid #ced4da;
  border-radius: 5px;
  font-size: 0.95rem;
  background-color: #fefefe;
}

.file-input {
  grid-column: span 2;
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

.btn {
  padding: 0.6rem 1.2rem;
  border: none;
  border-radius: 5px;
  font-weight: 600;
  font-size: 0.95rem;
  cursor: pointer;
  transition: all 0.3s ease;
}

.primary {
  background-color: #0d6efd;
  color: white;
}

.primary:hover {
  background-color: #0b5ed7;
}

.secondary {
  background-color: #adb5bd;
  color: white;
}

.secondary:hover {
  background-color: #6c757d;
}

.edit {
  background-color: #ffc107;
  color: black;
}

.edit:hover {
  background-color: #e0a800;
}

.delete {
  background-color: #dc3545;
  color: white;
}

.delete:hover {
  background-color: #c82333;
}

.product-table {
  width: 100%;
  border-collapse: collapse;
  background-color: white;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
  border-radius: 5px;
  overflow: hidden;
}

.product-table th,
.product-table td {
  padding: 1rem;
  border-bottom: 1px solid #dee2e6;
  text-align: center;
  font-size: 0.95rem;
}

.product-table th {
  background-color: #343a40;
  color: white;
  text-transform: uppercase;
}

.table-responsive {
  overflow-x: auto;
}

.text-muted {
  color: #6c757d;
  font-style: italic;
}

</style>
