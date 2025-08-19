<template>
  <div class="admin-dashboard">
    <!-- Pestañas para navegar entre secciones -->
    <div class="tabs">
      <button 
        @click="activeTab = 'products'"
        :class="{ active: activeTab === 'products' }"
      >
        Gestión de Productos
      </button>
      <button 
        @click="activeTab = 'reservations'"
        :class="{ active: activeTab === 'reservations' }"
      >
        Gestión de Reservas
      </button>
    </div>

    <!-- Sección de Productos -->
    <div v-if="activeTab === 'products'" class="products-section">
      <h2>Gestión de Productos</h2>

      <!-- Formulario de creación/edición -->
      <form @submit.prevent="submitProductForm" class="product-form">
        <input v-model="productForm.name" placeholder="Nombre del producto" required />
        <input v-model="productForm.category" placeholder="Categoría" required />
        <input v-model.number="productForm.price" type="number" step="0.01" placeholder="Precio" required />
        <input v-model.number="productForm.stock" type="number" placeholder="Stock" required />
        <textarea v-model="productForm.description" placeholder="Descripción"></textarea>

        <input type="file" @change="handleProductImage" accept="image/*" class="file-input" required />

        <div v-if="productForm.currentImage && !productForm.image" class="image-preview">
          <p>Imagen actual:</p>
          <img :src="getImageUrl(productForm.currentImage)" alt="Imagen actual" width="150" />
        </div>

        <div class="button-group">
          <button type="submit" class="btn primary">
            {{ productForm.id ? 'Actualizar 📝' : 'Crear ➕' }}
          </button>
          <button v-if="productForm.id" type="button" @click="cancelProductEdit" class="btn secondary">
            Cancelar ❌
          </button>
        </div>
      </form>

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
              <td>${{ Number(product.price).toFixed(2) }}</td>
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

    <!-- Sección de Reservas -->
    <div v-if="activeTab === 'reservations'" class="reservations-section">
      <div class="section-header">
        <h2>Gestión de Reservas</h2>
        <div class="header-buttons">
          <button @click="fetchReservations" class="btn primary refresh-btn">
            🔄 Actualizar
          </button>
        </div>
      </div>

      <!-- Filtros para reservas -->
      <div class="filters">
        <select v-model="reservationFilter.status" @change="fetchReservations">
          <option value="">Todas las reservas</option>
          <option value="apartado">Apartado</option>
          <option value="pending">Pendientes</option>
          <option value="confirmed">Confirmadas</option>
          <option value="completed">Completadas</option>
          <option value="cancelled">Canceladas</option>
        </select>

        <input
          type="text"
          v-model="reservationFilter.search"
          placeholder="Buscar por usuario o producto"
          @input="debouncedSearch"
        />
      </div>

      <!-- Tabla de reservas -->
      <div class="table-responsive">
        <table class="reservation-table">
          <thead>
            <tr>
              <th>Usuario</th>
              <th>Producto</th>
              <th>Cantidad</th>
              <th>Total</th>
              <th>Fecha</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="reservation in reservations" :key="reservation.id">
              <td>{{ reservation.user.name }}</td>
              <td>{{ reservation.product.name }}</td>
              <td>{{ reservation.quantity }}</td>
              <td>${{ (reservation.quantity * reservation.product.price).toFixed(2) }}</td>
              <td>{{ formatDate(reservation.created_at) }}</td>
              <td>
                <span class="status-badge" :class="reservation.status">
                  {{ formatStatus(reservation.status) }}
                </span>
              </td>
              <td>
                <select
                  :value="reservation.status"
                  @change="updateReservationStatusFromSelect(reservation, $event)"
                  class="status-select"
                >
                  <option value="apartado">Apartado</option>
                  <option value="pending">Pendiente</option>
                  <option value="confirmed">Confirmada</option>
                  <option value="completed">Completada</option>
                  <option value="cancelled">Cancelada</option>
                </select>
              </td>
            </tr>
          </tbody>
        </table>

        <div v-if="reservations.length === 0" class="no-reservations">
          No se encontraron reservas
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import api from '../axios';
import { getProductImageUrl } from '../config/api';
import axios from 'axios';

export default {
  data() {
    return {
      activeTab: 'products',
      // Datos para productos
      products: [],
      productForm: {
        id: null,
        name: '',
        category: '',
        price: 0,
        stock: 0,
        description: '',
        image: null,
        currentImage: null
      },
      // Datos para reservas
      reservations: [],
      reservationFilter: {
        status: '',
        search: ''
      },
      searchTimeout: null
    };
  },
  methods: {
    // Métodos para productos
    async fetchProducts() {
      try {
        const response = await api.get('/products');
        this.products = response.data;
      } catch (error) {
        console.error('Error al obtener productos:', error);
      }
    },
    handleProductImage(event) {
      this.productForm.image = event.target.files[0];
    },
    async submitProductForm() {
      const formData = new FormData();
      formData.append('name', this.productForm.name);
      formData.append('category', this.productForm.category);
      formData.append('price', this.productForm.price);
      formData.append('stock', this.productForm.stock);
      formData.append('description', this.productForm.description);

      if (this.productForm.image) {
        formData.append('image', this.productForm.image);
      }

      const token = localStorage.getItem('token');
      try {
        if (this.productForm.id) {
          await api.put(`/products/${this.productForm.id}`, formData, {
            headers: { 
              Authorization: `Bearer ${token}`,
              'Content-Type': 'multipart/form-data'
            }
          });
        } else {
          await api.post('/products', formData, {
            headers: { 
              Authorization: `Bearer ${token}`,
              'Content-Type': 'multipart/form-data'
            }
          });
        }
        this.resetProductForm();
        this.fetchProducts();
      } catch (error) {
        console.error('Error al guardar producto:', error);
      }
    },
    editProduct(product) {
      this.productForm = {
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
    cancelProductEdit() {
      this.resetProductForm();
    },
    resetProductForm() {
      this.productForm = {
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
      if (confirm('¿Estás seguro de eliminar este producto?')) {
        const token = localStorage.getItem('token');
        try {
          await api.delete(`/products/${id}`, {
            headers: { Authorization: `Bearer ${token}` }
          });
          this.fetchProducts();
        } catch (error) {
          console.error('Error al eliminar producto:', error);
        }
      }
    },
    getImageUrl(filename) {
      return getProductImageUrl(filename);
    },

    // Métodos para reservas
    async fetchReservations() {
      console.log('🔄 Admin: Cargando reservas...')
      try {
        const params = {};
        if (this.reservationFilter.status) params.status = this.reservationFilter.status;
        if (this.reservationFilter.search) params.search = this.reservationFilter.search;

        console.log('📡 Admin: Haciendo petición a /admin/reservations...')
        const response = await api.get('/admin/reservations', { params });
        console.log('✅ Admin: Reservas recibidas:', response.data)
        console.log('📊 Admin: Número de reservas:', response.data?.length || 0)
        
        this.reservations = response.data;
      } catch (error) {
        console.error('❌ Admin: Error al obtener reservas:', error);
        console.error('❌ Admin: Status:', error.response?.status);
        console.error('❌ Admin: Data:', error.response?.data);
        
        if (error.response?.status === 403) {
          alert('No tienes permisos de administrador para ver las reservas.');
        } else if (error.response?.status === 401) {
          alert('Tu sesión ha expirado. Por favor inicia sesión nuevamente.');
          this.$router.push('/login');
        } else {
          alert('Error al cargar las reservas. Verifica tu conexión.');
        }
      }
    },
    async updateReservationStatus(reservation) {
      console.log('🔄 Admin: Actualizando estado de reserva...')
      console.log('📋 Reserva completa:', reservation)
      console.log('🎯 ID de reserva:', reservation.id)
      console.log('📝 Nuevo estado seleccionado:', reservation.status)
      
      // IMPORTANTE: Guardar el estado anterior ANTES de que Vue lo cambie
      const reservationIndex = this.reservations.findIndex(r => r.id === reservation.id)
      const previousStatus = this.reservations[reservationIndex]?.status
      console.log('📋 Estado anterior encontrado:', previousStatus)
      console.log('📍 Índice de reserva:', reservationIndex)
      
      // Si no encontramos el estado anterior, tomamos una copia de seguridad
      if (!previousStatus) {
        console.warn('⚠️ No se pudo encontrar el estado anterior, usando el estado actual como respaldo')
      }
      
      try {
        const requestData = {
          status: reservation.status
        }
        
        console.log('📡 Enviando petición a ruta API:', `/admin/reservations/${reservation.id}/status`)
        console.log('📄 Datos a enviar:', requestData)
        console.log('🌐 URL completa:', `http://127.0.0.1:8000/api/admin/reservations/${reservation.id}/status`)
        
        const response = await api.put(`/admin/reservations/${reservation.id}/status`, requestData)
        
        console.log('✅ Admin: Respuesta del servidor:', response)
        console.log('✅ Admin: Estado actualizado exitosamente')
        console.log('📊 Datos de respuesta:', response.data)
        
        alert('Estado de reserva actualizado correctamente');
        
        // Recargar reservas para mostrar cambios actualizados desde el servidor
        await this.fetchReservations();
        
      } catch (error) {
        console.error('❌ Admin: Error al actualizar reserva:', error);
        console.error('❌ Admin: Status:', error.response?.status);
        console.error('❌ Admin: Data:', error.response?.data);
        console.error('❌ Admin: Headers:', error.response?.headers);
        console.error('❌ Admin: Config:', error.config);
        
        // REVERTIR el cambio en la UI inmediatamente
        if (reservationIndex !== -1 && previousStatus) {
          console.log('🔄 Revirtiendo estado de UI a:', previousStatus)
          this.reservations[reservationIndex].status = previousStatus
          
          // Forzar actualización de Vue
          this.$forceUpdate();
        }
        
        if (error.response?.status === 403) {
          alert('No tienes permisos para cambiar el estado de las reservas.');
        } else if (error.response?.status === 404) {
          alert('La ruta para actualizar el estado no fue encontrada. Verifica la configuración del servidor.');
        } else if (error.response?.status === 422) {
          alert('Error de validación: ' + (error.response?.data?.message || 'Datos inválidos'));
        } else {
          alert('Error al actualizar el estado: ' + (error.response?.data?.message || error.message));
        }
      }
    },
    async updateReservationStatusFromSelect(reservation, event) {
      const newStatus = event.target.value;
      const oldStatus = reservation.status;
      
      console.log('🔄 Admin: Estado cambiado desde select...')
      console.log('🎯 ID de reserva:', reservation.id)
      console.log('📝 Estado anterior:', oldStatus)
      console.log('📝 Estado nuevo:', newStatus)
      
      if (oldStatus === newStatus) {
        console.log('⚠️ El estado es igual, no hay cambio necesario');
        return;
      }
      
      try {
        const requestData = { status: newStatus }
        
        console.log('📡 Enviando petición:', `/admin/reservations/${reservation.id}/status`)
        console.log('📄 Datos:', requestData)
        console.log('🔑 Token presente:', !!localStorage.getItem('token'))
        
        const response = await api.put(`/admin/reservations/${reservation.id}/status`, requestData)
        
        console.log('✅ RESPUESTA COMPLETA del servidor:', response)
        console.log('📊 Status HTTP:', response.status)
        console.log('📋 Datos de respuesta:', response.data)
        console.log('📌 Headers de respuesta:', response.headers)
        
        // SOLO actualizar la UI si el servidor confirmó el cambio
        const reservationIndex = this.reservations.findIndex(r => r.id === reservation.id)
        if (reservationIndex !== -1) {
          console.log('🔄 Actualizando UI - Índice:', reservationIndex)
          console.log('🔄 Estado anterior en array:', this.reservations[reservationIndex].status)
          
          this.reservations[reservationIndex].status = newStatus;
          console.log('🔄 Estado nuevo en array:', this.reservations[reservationIndex].status)
          
          this.$forceUpdate(); // Forzar re-render
        }
        
        alert(`✅ Estado actualizado de "${oldStatus}" a "${newStatus}"`);
        
        // VERIFICAR que se guardó correctamente recargando desde el servidor
        console.log('🔍 Verificando cambio recargando desde servidor...')
        await this.fetchReservations();
        
        // Verificar si realmente cambió
        const updatedReservation = this.reservations.find(r => r.id === reservation.id);
        if (updatedReservation && updatedReservation.status === newStatus) {
          console.log('✅ CONFIRMADO: Estado se guardó correctamente en la base de datos');
        } else {
          console.error('❌ ERROR: Estado NO se guardó en la base de datos');
          console.error('Estado esperado:', newStatus);
          console.error('Estado actual:', updatedReservation?.status);
        }
        
      } catch (error) {
        console.error('❌ ERROR COMPLETO:', error);
        console.error('📡 Response:', error.response);
        console.error('⚙️ Config:', error.config);
        console.error('🔧 Request:', error.request);
        console.error('📊 Status:', error.response?.status);
        console.error('📋 Error data:', error.response?.data);
        
        alert(`❌ Error al actualizar: ${error.response?.data?.message || error.message}`);
      }
    },
    debouncedSearch() {
      clearTimeout(this.searchTimeout);
      this.searchTimeout = setTimeout(() => {
        this.fetchReservations();
      }, 500);
    },
    formatDate(dateString) {
      return new Date(dateString).toLocaleString('es-ES');
    },
    formatStatus(status) {
      const statusMap = {
        'apartado': 'Apartado',
        'pending': 'Pendiente',
        'confirmed': 'Confirmada', 
        'completed': 'Completada',
        'cancelled': 'Cancelada'
      };
      return statusMap[status] || status;
    }
  },
  mounted() {
    this.fetchProducts();
    this.fetchReservations();
  },
  watch: {
    activeTab() {
      // Puedes agregar lógica adicional cuando cambia la pestaña
    }
  }
};
</script>

<style scoped>
.admin-dashboard {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
  background-color: #f8f9fa;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.tabs {
  display: flex;
  margin-bottom: 2rem;
  border-bottom: 1px solid #dee2e6;
}

.tabs button {
  padding: 0.75rem 1.5rem;
  background: none;
  border: none;
  border-bottom: 3px solid transparent;
  font-size: 1rem;
  font-weight: 600;
  color: #495057;
  cursor: pointer;
  transition: all 0.3s;
}

.tabs button.active {
  color: #0d6efd;
  border-bottom-color: #0d6efd;
}

.tabs button:hover:not(.active) {
  color: #212529;
  border-bottom-color: #adb5bd;
}

/* Estilos para la sección de productos (mantenidos de tu código original) */
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

/* Estilos para la sección de reservas */
.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.header-buttons {
  display: flex;
  gap: 0.5rem;
}

.refresh-btn,
.debug-btn {
  padding: 0.5rem 1rem;
  font-size: 0.9rem;
}

.warning {
  background-color: #fd7e14;
  color: white;
}

.warning:hover {
  background-color: #e8590c;
}

.success {
  background-color: #198754;
  color: white;
}

.success:hover {
  background-color: #157347;
}

.filters {
  display: flex;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.filters select,
.filters input {
  padding: 0.7rem;
  border: 1px solid #ced4da;
  border-radius: 5px;
  font-size: 0.95rem;
}

.filters input {
  flex-grow: 1;
}

.reservation-table {
  width: 100%;
  border-collapse: collapse;
  background-color: white;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
  border-radius: 5px;
  overflow: hidden;
}

.reservation-table th,
.reservation-table td {
  padding: 1rem;
  border-bottom: 1px solid #dee2e6;
  text-align: left;
  font-size: 0.95rem;
}

.reservation-table th {
  background-color: #343a40;
  color: white;
  text-transform: uppercase;
}

.status-badge {
  padding: 0.3rem 0.6rem;
  border-radius: 1rem;
  font-size: 0.85rem;
  font-weight: 600;
}

.status-badge.apartado {
  background-color: #e7f3ff;
  color: #0066cc;
}

.status-badge.pending {
  background-color: #fff3cd;
  color: #856404;
}

.status-badge.confirmed {
  background-color: #cff4fc;
  color: #055160;
}

.status-badge.completed {
  background-color: #d4edda;
  color: #155724;
}

.status-badge.cancelled {
  background-color: #f8d7da;
  color: #721c24;
}

.status-select {
  padding: 0.5rem;
  border-radius: 5px;
  border: 1px solid #ced4da;
}

.no-reservations {
  text-align: center;
  padding: 2rem;
  color: #6c757d;
  font-style: italic;
}

.table-responsive {
  overflow-x: auto;
  margin-bottom: 2rem;
}

.text-muted {
  color: #6c757d;
  font-style: italic;
}

h2 {
  font-size: 1.8rem;
  color: #343a40;
  margin-bottom: 1.5rem;
}
@media (max-width: 900px) {
  .main-container {
    flex-direction: column;
    height: auto;
  }
  .left-section, .right-section {
    flex: none;
    width: 100%;
    height: auto;
  }
  .main-image {
    height: 200px;
    object-fit: cover;
  }
}

@media (max-width: 600px) {
  .form-container {
    width: 95%;
    max-width: 100%;
    padding: 1rem;
  }
  .main-image {
    height: 120px;
  }
  h2 {
    font-size: 1.2rem;
  }
  input, button, .register-btn {
    font-size: 1rem;
    padding: 0.7rem;
  }
}
</style>