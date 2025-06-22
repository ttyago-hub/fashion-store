<template>
  <div class="home-container">
    <div class="home-card">
      <h1>¡Bienvenido a Fashion Store!</h1>
      <p>Explora nuestra colección y aparta tus productos favoritos.</p>
      <router-link to="/products" class="home-button">Ver productos</router-link>
    </div>
  </div>
</template>

<style scoped>
.home-container {
  background-color: #f3f4f6;
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem;
}

.home-card {
  background-color: white;
  padding: 3rem;
  border-radius: 12px;
  box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
  text-align: center;
  max-width: 500px;
  width: 100%;
}

h1 {
  color: #4f46e5;
  font-size: 2rem;
  margin-bottom: 1rem;
}

p {
  color: #4b5563;
  font-size: 1.1rem;
  margin-bottom: 2rem;
}

.home-button {
  background-color: #4f46e5;
  color: white;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  text-decoration: none;
  font-weight: bold;
  transition: background-color 0.3s ease;
}

.home-button:hover {
  background-color: #4338ca;
}
</style>
