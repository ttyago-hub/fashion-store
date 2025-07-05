<template>
  <div class="home-container">
    <div class="home-card animate-pop">
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

  /* Fondo con logo */
  background-image: url('public/descarga (1).jpeg');  /* Ajusta la ruta si es otra */
  background-repeat: no-repeat;
  background-position: center top; /* o center center según prefieras */
  background-size: 900px 700px; /* tamaño del logo */
  background-attachment: fixed;
}

.home-card {
  background-color: white;
  padding: 3rem 2rem;
  border-radius: 16px;
  box-shadow: 0 12px 25px rgba(0, 0, 0, 0.08);
  text-align: center;
  max-width: 500px;
  width: 100%;
  transition: transform 0.3s ease;
}

h1 {
  color: #4f46e5;
  font-size: 2.2rem;
  margin-bottom: 1rem;
}

p {
  color: #374151;
  font-size: 1.1rem;
  margin-bottom: 2rem;
  line-height: 1.6;
}

.home-button {
  background-color: #4f46e5;
  color: white;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  text-decoration: none;
  font-weight: bold;
  font-size: 1rem;
  box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
  transition: all 0.3s ease;
  display: inline-block;
}

.home-button:hover {
  background-color: #4338ca;
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(67, 56, 202, 0.35);
}

/* Animación de entrada */
@keyframes pop {
  0% {
    transform: scale(0.9);
    opacity: 0;
  }
  100% {
    transform: scale(1);
    opacity: 1;
  }
}

.animate-pop {
  animation: pop 0.4s ease-out;
}

</style>
