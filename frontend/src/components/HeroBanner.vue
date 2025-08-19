<template>
  <!-- Sección Hero con imagen de fondo -->
  <div class="relative bg-cover bg-center h-96" :style="{'background-image': `url(${bannerImage})`}">
    <!-- Capa oscura superpuesta para resaltar el texto -->
    <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
      <div class="text-center text-white px-4">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Bienvenido a <span class="text-yellow-300">Fashion Store</span></h1>
        <p class="text-lg md:text-2xl mb-6">Lo último en moda a tu alcance</p>
        <!-- Botón de llamada a la acción que lleva a la sección de productos -->
        <a href="#productos" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded">
          Ver Productos
        </a>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'HeroBanner',
  props: {
    // Prop para la ruta de la imagen de fondo, por si se quiere hacer dinámico
    bannerImage: {
      type: String,
      default: '/images/hero.jpg'  // Ruta por defecto de la imagen del banner
    }
  }
}
</script>
