import axios from 'axios'

const apiClient = axios.create({
  baseURL: 'http://127.0.0.1:8000/api',
  timeout: 5000,
  withCredentials: true, // 🔐 ¡Esto es clave para Sanctum y sesiones!
})

apiClient.interceptors.request.use(
  config => {
    const token = localStorage.getItem('token');

    // 🔒 Verifica que el token no esté vacío ni inválido
    if (token && token !== 'null' && token !== 'undefined' && token.trim() !== '') {
      config.headers.Authorization = `Bearer ${token}`;
    } else {
      delete config.headers.Authorization;
    }

    return config;
  },
  error => Promise.reject(error)
)

apiClient.interceptors.response.use(
  response => response,
  error => {
    console.error('Error en la respuesta de la API:', error)
    return Promise.reject(error)
  }
)

export default apiClient
