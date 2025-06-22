<script setup>
import NavBar from './components/NavBar.vue'
console.log('✅ App.vue montado correctamente')
</script>

<template>
  <div>
    <!-- 🔽 Aquí se muestra la barra de navegación -->
    <NavBar />

    <header>
      
    </header>

    <!-- Aquí se mostrarán tus páginas -->
    <router-view />
  </div>
</template>


<style scoped>
.navbar {
  background-color: #4f46e5;
  padding: 1rem;
  text-align: center;
  color: white;
  box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}
.brand {
  margin: 0;
  font-size: 2rem;
  font-weight: bold;
}
.main-content {
  padding: 2rem;
  background-color: #f3f4f6;
  min-height: 100vh;
}
</style>