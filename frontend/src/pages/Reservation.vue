<template>
  <div class="reservation-container">
    <h2>Apartar producto</h2>

    <div v-if="product" class="product-info">
      <p><strong>Producto:</strong> {{ product.name }}</p>
      <p><strong>Precio:</strong> ${{ product.price.toFixed(2) }}</p>
      <p><strong>Stock disponible:</strong> {{ product.stock }}</p>

      <form @submit.prevent="submitReservation" class="reservation-form">
        <label for="quantity">Cantidad:</label>
        <input
          id="quantity"
          type="number"
          v-model.number="quantity"
          min="1"
          :max="product.stock"
          required
        />

        <label for="deliveryType">Tipo de entrega:</label>
        <select id="deliveryType" v-model="deliveryType" required>
          <option value="">Selecciona una opción</option>
          <option value="pickup">Retiro en tienda</option>
          <option value="delivery">Entrega a domicilio</option>
        </select>

        <div v-if="deliveryType === 'delivery'">
          <label for="address">Dirección de entrega:</label>
          <input
            id="address"
            v-model="deliveryAddress"
            placeholder="Calle principal, número, ciudad..."
            required
          />
        </div>

        <button type="submit">Confirmar apartado</button>
      </form>
    </div>

    <div v-else>
      <p>Cargando información del producto...</p>
    </div>
  </div>
</template>

<style scoped>
.reservation-container {
  max-width: 600px;
  margin: 2rem auto;
  padding: 2rem;
  background-color: #f9fafb;
  border-radius: 8px;
  box-shadow: 0 0 10px rgba(0,0,0,0.05);
}

h2 {
  color: #4f46e5;
  margin-bottom: 1.5rem;
  text-align: center;
}

.product-info p {
  margin: 0.3rem 0;
  font-size: 1rem;
}

.reservation-form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  margin-top: 1.5rem;
}

label {
  font-weight: bold;
  color: #374151;
}

input,
select {
  padding: 0.6rem;
  font-size: 1rem;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  outline: none;
}

input:focus,
select:focus {
  border-color: #4f46e5;
  box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2);
}

button {
  padding: 0.75rem;
  background-color: #4f46e5;
  color: white;
  border: none;
  border-radius: 6px;
  font-weight: bold;
  font-size: 1rem;
  cursor: pointer;
  transition: background-color 0.3s;
}

button:hover {
  background-color: #4338ca;
}
</style>
