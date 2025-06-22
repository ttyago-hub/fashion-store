<template>
  <div class="dashboard-container">
    <h1>Bienvenido a tu panel</h1>
    <p>Aquí puedes explorar productos y gestionar tus reservas.</p>
    <router-link to="/products" class="dashboard-link">Ver productos</router-link>
    <router-link to="/reservations" class="dashboard-link">Mis reservas</router-link>
  </div>
</template>

<script>
export default {
  name: 'UserDashboard',
}
</script>

<style scoped>
.dashboard-container {
  max-width: 800px;
  margin: 3rem auto;
  padding: 2rem;
  background-color: #f3f4f6;
  border-radius: 12px;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
  text-align: center;
}

h1 {
  color: #10b981;
  margin-bottom: 1rem;
}

.dashboard-link {
  display: inline-block;
  margin: 1rem;
  padding: 0.75rem 1.5rem;
  background-color: #10b981;
  color: white;
  border-radius: 8px;
  text-decoration: none;
  font-weight: bold;
  transition: background-color 0.3s ease;
}

.dashboard-link:hover {
  background-color: #059669;
}
</style>
