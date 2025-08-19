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

