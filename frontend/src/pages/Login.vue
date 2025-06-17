<template>
  <div class="p-4">
    <h2 class="text-xl font-bold">Login</h2>
    <form @submit.prevent="login">
      <input v-model="email" placeholder="Email" class="border p-2 mb-2 block" />
      <input v-model="password" type="password" placeholder="Password" class="border p-2 mb-2 block" />
      <button class="bg-blue-500 text-white px-4 py-2">Entrar</button>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from '../axios'
import { useRouter } from 'vue-router'

const email = ref('')
const password = ref('')
const router = useRouter()

const login = async () => {
  try {
    const res = await axios.post('/login', {
      email: email.value,
      password: password.value
    })
    localStorage.setItem('token', res.data.token)
    console.log('✅ Login correcto', res.data)
    router.push('/products')
  } catch (err) {
    console.error('❌ Error de login', err)
  }
}
</script>
