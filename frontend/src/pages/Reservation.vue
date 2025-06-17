<template>
  <div class="max-w-xl mx-auto p-4">
    <h2 class="text-2xl font-bold mb-4">Apartar producto</h2>

    <div class="mb-4">
      <label class="block mb-1">Producto:</label>
      <select v-model="reservation.product_id" class="w-full p-2 border">
        <option disabled value="">-- Selecciona un producto --</option>
        <option v-for="product in products" :key="product.id" :value="product.id">
          {{ product.name }} (Stock: {{ product.stock }})
        </option>
      </select>
    </div>

    <div class="mb-4">
      <label class="block mb-1">Cantidad:</label>
      <input v-model.number="reservation.quantity" type="number" min="1" class="w-full p-2 border" />
    </div>

    <div class="mb-4">
      <label class="block mb-1">Tipo de entrega:</label>
      <select v-model="reservation.type" class="w-full p-2 border">
        <option value="retiro">Retiro en tienda</option>
        <option value="domicilio">Envío a domicilio</option>
      </select>
    </div>

    <div v-if="reservation.type === 'domicilio'" class="mb-4">
      <label class="block mb-1">Dirección de envío:</label>
      <textarea v-model="reservation.delivery_address" class="w-full p-2 border"></textarea>
    </div>

    <button @click="submitReservation" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
      Confirmar Apartado
    </button>

    <div v-if="responseMessage" class="mt-4 p-4 bg-green-100 text-green-700 rounded">
      {{ responseMessage }}
      <div v-if="pickupCode">
        <strong>Código de retiro:</strong> {{ pickupCode }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from '../axios';
import { useAuthStore } from '../store/auth';

const auth = useAuthStore();

const products = ref([]);
const reservation = ref({
  product_id: '',
  quantity: 1,
  type: 'retiro',
  delivery_address: ''
});

const responseMessage = ref('');
const pickupCode = ref('');

const fetchProducts = async () => {
  const res = await axios.get('/products');
  products.value = res.data;
};

const submitReservation = async () => {
  try {
    const res = await axios.post('/reserve', reservation.value, {
      headers: { Authorization: `Bearer ${auth.token}` }
    });
    responseMessage.value = res.data.message;
    pickupCode.value = res.data.codigo || '';
  } catch (error) {
    responseMessage.value = 'Error al apartar el producto.';
  }
};

onMounted(() => {
  fetchProducts();
});
</script>

