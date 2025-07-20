<template>
  <div class="user-dashboard">
    <header class="dashboard-header">
      <h1>Bienvenido, {{ user.name || 'Usuario' }}</h1>
      <p class="welcome-message">Aquí puedes gestionar tus reservas y explorar productos</p>
    </header>

    <div class="dashboard-actions">
      <router-link to="/products" class="action-btn explore-btn">
        <i class="fas fa-box-open"></i> Explorar Productos
      </router-link>
      <router-link to="/reserve" class="action-btn new-reservation-btn">
        <i class="fas fa-plus-circle"></i> Nueva Reserva
      </router-link>
    </div>

    <section class="reservations-section">
      <div class="section-header">
        <h2><i class="fas fa-clipboard-list"></i> Mis Reservas</h2>
        <button 
          @click="fetchReservations" 
          class="refresh-btn"
          :disabled="loading"
        >
          <i class="fas fa-sync-alt" :class="{ 'fa-spin': loading }"></i> Actualizar
        </button>
      </div>

      <!-- Estados de carga y error -->
      <div v-if="loading" class="loading-state">
        <div class="spinner"></div>
        <p>Cargando tus reservas...</p>
      </div>

      <div v-else-if="error" class="error-state">
        <div class="error-icon">
          <i class="fas fa-exclamation-triangle"></i>
        </div>
        <p>{{ error }}</p>
        <button @click="fetchReservations" class="retry-btn">
          <i class="fas fa-redo"></i> Reintentar
        </button>
      </div>

      <!-- Lista de reservas -->
      <div v-else-if="reservations.length > 0" class="reservations-list">
        <div class="reservation-filters">
          <select v-model="statusFilter" @change="filterReservations" class="filter-select">
            <option value="all">Todas las reservas</option>
            <option value="pendiente">Pendientes</option>
            <option value="completado">Completadas</option>
            <option value="cancelado">Canceladas</option>
          </select>
        </div>

        <div class="reservation-cards">
          <div 
            v-for="reservation in filteredReservations" 
            :key="reservation.id" 
            class="reservation-card"
            :class="reservation.status"
          >
            <div class="card-header">
              <h3>{{ reservation.product?.name || 'Producto no disponible' }}</h3>
              <span class="reservation-status" :class="reservation.status">
                {{ formatStatus(reservation.status) }}
              </span>
            </div>

            <div class="card-body">
              <div class="reservation-detail">
                <i class="fas fa-boxes"></i>
                <span>Cantidad: {{ reservation.quantity }}</span>
              </div>
              
              <div class="reservation-detail">
                <i class="fas fa-money-bill-wave"></i>
                <span>Total: ${{ calculateTotal(reservation) }}</span>
              </div>
              
              <div class="reservation-detail">
                <i class="fas fa-truck"></i>
                <span>
                  {{ reservation.delivery_type === 'delivery' ? 'Entrega a domicilio' : 'Recoger en tienda' }}
                </span>
              </div>
              
              <div v-if="reservation.delivery_address" class="reservation-detail">
                <i class="fas fa-map-marker-alt"></i>
                <span>{{ reservation.delivery_address }}</span>
              </div>
              
              <div class="reservation-detail">
                <i class="fas fa-calendar-alt"></i>
                <span>{{ formatDate(reservation.created_at) }}</span>
              </div>
            </div>

            <div class="card-actions">
              <button 
                v-if="reservation.status === 'pendiente'"
                @click="cancelReservation(reservation.id)"
                class="action-btn cancel-btn"
              >
                <i class="fas fa-times-circle"></i> Cancelar
              </button>
              
              <router-link 
                v-if="reservation.product"
                :to="'/products/' + reservation.product.id"
                class="action-btn view-product-btn"
              >
                <i class="fas fa-eye"></i> Ver producto
              </router-link>
            </div>
          </div>
        </div>
      </div>

      <!-- Estado vacío -->
      <div v-else class="empty-state">
        <div class="empty-icon">
          <i class="fas fa-clipboard"></i>
        </div>
        <h3>No tienes reservas</h3>
        <p>Aún no has realizado ninguna reserva en nuestro sistema.</p>
        <router-link to="/products" class="action-btn explore-btn">
          <i class="fas fa-box-open"></i> Explorar Productos
        </router-link>
      </div>
    </section>
  </div>
</template>

<script>
import api from '../axios';

export default {
  name: 'UserDashboard',
  data() {
    return {
      user: {},
      reservations: [],
      filteredReservations: [],
      loading: false,
      error: null,
      statusFilter: 'all'
    }
  },
  computed: {
    userName() {
      return this.user.name || 'Usuario';
    }
  },
  methods: {
    async fetchUserData() {
      const token = localStorage.getItem('token');
      if (!token) {
        this.$router.push('/login');
        return;
      }

      try {
        const response = await api.get('/user', {
          headers: { Authorization: `Bearer ${token}` }
        });
        this.user = response.data;
      } catch (error) {
        console.error('Error al cargar datos del usuario:', error);
      }
    },
    async fetchReservations() {
      const token = localStorage.getItem('token');
      if (!token) return;

      this.loading = true;
      this.error = null;

      try {
        const response = await api.get('/user/reservations', {
          headers: { Authorization: `Bearer ${token}` }
        });
        this.reservations = response.data;
        this.filterReservations();
      } catch (error) {
        this.error = 'Error al cargar tus reservas. Por favor intenta nuevamente.';
        console.error('Error al cargar reservas:', error);
      } finally {
        this.loading = false;
      }
    },
    filterReservations() {
      if (this.statusFilter === 'all') {
        this.filteredReservations = [...this.reservations];
      } else {
        this.filteredReservations = this.reservations.filter(
          res => res.status === this.statusFilter
        );
      }
    },
    async cancelReservation(reservationId) {
      if (!confirm('¿Estás seguro de que deseas cancelar esta reserva?')) return;

      try {
        await api.delete(`/reservations/${reservationId}`, {
          headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
        });
        this.fetchReservations();
      } catch (error) {
        alert('No se pudo cancelar la reserva. Por favor intenta nuevamente.');
        console.error('Error al cancelar reserva:', error);
      }
    },
    calculateTotal(reservation) {
      if (!reservation.product) return '0.00';
      return (reservation.quantity * reservation.product.price).toFixed(2);
    },
    formatDate(dateString) {
      const options = { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      };
      return new Date(dateString).toLocaleDateString('es-ES', options);
    },
    formatStatus(status) {
      const statusMap = {
        'pendiente': 'Pendiente',
        'completado': 'Completado',
        'cancelado': 'Cancelado'
      };
      return statusMap[status] || status;
    }
  },
  mounted() {
    this.fetchUserData();
    this.fetchReservations();
  }
}
</script>

<style scoped>
.user-dashboard {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem 1rem;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.dashboard-header {
  text-align: center;
  margin-bottom: 2rem;
}

.dashboard-header h1 {
  color: #2c3e50;
  font-size: 2.2rem;
  margin-bottom: 0.5rem;
}

.welcome-message {
  color: #6c757d;
  font-size: 1.1rem;
}

.dashboard-actions {
  display: flex;
  justify-content: center;
  gap: 1.5rem;
  margin-bottom: 3rem;
  flex-wrap: wrap;
}

.action-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.3s ease;
  border: none;
  cursor: pointer;
}

.explore-btn {
  background-color: #3498db;
  color: white;
}

.explore-btn:hover {
  background-color: #2980b9;
  transform: translateY(-2px);
}

.new-reservation-btn {
  background-color: #2ecc71;
  color: white;
}

.new-reservation-btn:hover {
  background-color: #27ae60;
  transform: translateY(-2px);
}

.reservations-section {
  background-color: #f8f9fa;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  border-bottom: 1px solid #e0e0e0;
  padding-bottom: 1rem;
}

.section-header h2 {
  color: #2c3e50;
  font-size: 1.5rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.refresh-btn {
  background: none;
  border: none;
  color: #6c757d;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.9rem;
}

.refresh-btn:hover {
  color: #3498db;
}

.refresh-btn:disabled {
  color: #adb5bd;
  cursor: not-allowed;
}

/* Estados */
.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 3rem;
  color: #6c757d;
}

.spinner {
  width: 50px;
  height: 50px;
  border: 5px solid rgba(79, 70, 229, 0.1);
  border-top: 5px solid #4f46e5;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 1rem;
}

.error-state {
  text-align: center;
  padding: 2rem;
  background-color: #fee2e2;
  border-radius: 8px;
  color: #dc2626;
}

.error-icon {
  font-size: 2rem;
  margin-bottom: 1rem;
}

.retry-btn {
  margin-top: 1rem;
  padding: 0.5rem 1rem;
  background-color: #dc2626;
  color: white;
  border: none;
  border-radius: 6px;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
}

/* Filtros */
.reservation-filters {
  margin-bottom: 1.5rem;
}

.filter-select {
  padding: 0.5rem 1rem;
  border: 1px solid #ced4da;
  border-radius: 6px;
  background-color: white;
}

/* Tarjetas de reserva */
.reservation-cards {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.5rem;
}

.reservation-card {
  background-color: white;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  transition: transform 0.3s ease;
}

.reservation-card:hover {
  transform: translateY(-5px);
}

.reservation-card.pendiente {
  border-left: 4px solid #f59e0b;
}

.reservation-card.completado {
  border-left: 4px solid #10b981;
}

.reservation-card.cancelado {
  border-left: 4px solid #ef4444;
}

.card-header {
  padding: 1rem;
  background-color: #f9fafb;
  border-bottom: 1px solid #e5e7eb;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.card-header h3 {
  margin: 0;
  font-size: 1.1rem;
  color: #1f2937;
}

.reservation-status {
  padding: 0.25rem 0.75rem;
  border-radius: 1rem;
  font-size: 0.8rem;
  font-weight: bold;
}

.reservation-status.pendiente {
  background-color: #fef3c7;
  color: #92400e;
}

.reservation-status.completado {
  background-color: #d1fae5;
  color: #065f46;
}

.reservation-status.cancelado {
  background-color: #fee2e2;
  color: #991b1b;
}

.card-body {
  padding: 1rem;
}

.reservation-detail {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 0.75rem;
  font-size: 0.95rem;
  color: #4b5563;
}

.reservation-detail i {
  width: 20px;
  text-align: center;
  color: #6b7280;
}

.card-actions {
  display: flex;
  gap: 0.75rem;
  padding: 0 1rem 1rem 1rem;
  flex-wrap: wrap;
}

.card-actions .action-btn {
  padding: 0.5rem 1rem;
  font-size: 0.9rem;
}

.cancel-btn {
  background-color: #ef4444;
  color: white;
}

.cancel-btn:hover {
  background-color: #dc2626;
}

.view-product-btn {
  background-color: #3b82f6;
  color: white;
}

.view-product-btn:hover {
  background-color: #2563eb;
}

/* Estado vacío */
.empty-state {
  text-align: center;
  padding: 3rem;
  color: #6b7280;
}

.empty-icon {
  font-size: 3rem;
  color: #d1d5db;
  margin-bottom: 1rem;
}

.empty-state h3 {
  color: #374151;
  margin-bottom: 0.5rem;
}

.empty-state p {
  margin-bottom: 1.5rem;
}

/* Animaciones */
@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Responsive */
@media (max-width: 768px) {
  .dashboard-actions {
    flex-direction: column;
    align-items: stretch;
  }
  
  .reservation-cards {
    grid-template-columns: 1fr;
  }
}
</style>