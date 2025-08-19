import axios from 'axios'

const apiClient = axios.create({
  baseURL: 'http://127.0.0.1:8000/api',
  timeout: 5000,
<<<<<<< HEAD
  withCredentials: true, // ✅ Necesario para Sanctum
})

// Interceptor para incluir automáticamente el token de autorización
apiClient.interceptors.request.use(
  config => {
    const token = localStorage.getItem('token')
    console.log('🔧 INTERCEPTOR - Token en localStorage:', !!token)
    console.log('🔧 INTERCEPTOR - Token parcial:', token ? token.substring(0, 30) + '...' : 'No token')
    
    if (token) {
      // 🔧 VERIFICACIÓN ADICIONAL: Asegurarse que el token tenga el formato correcto
      if (!token.includes('|')) {
        console.error('❌ Token parece no tener el formato Laravel Sanctum (ID|hash)')
        console.log('Token actual:', token)
      }
      
      config.headers.Authorization = `Bearer ${token}`
      console.log('🔧 INTERCEPTOR - Header Authorization agregado')
      console.log('🔧 INTERCEPTOR - Authorization header:', `Bearer ${token.substring(0, 20)}...`)
    } else {
      console.log('❌ INTERCEPTOR - No hay token disponible')
    }
    
    console.log('🔧 INTERCEPTOR - Headers finales (parciales):', {
      'Authorization': config.headers.Authorization ? config.headers.Authorization.substring(0, 30) + '...' : 'No auth',
      'Content-Type': config.headers['Content-Type']
    })
    return config
=======
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
>>>>>>> 44172495fe341cb5435355a45143c79aa45e0ca4
  },
  error => Promise.reject(error)
)

apiClient.interceptors.response.use(
  response => response,
  error => {
    console.error('Error en la respuesta de la API:', error)
<<<<<<< HEAD
    
    // Manejar errores de autenticación automáticamente
    if (error.response?.status === 401) {
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      window.location.href = '/login'
    }
    
=======
>>>>>>> 44172495fe341cb5435355a45143c79aa45e0ca4
    return Promise.reject(error)
  }
)

export default apiClient
