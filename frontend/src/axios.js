import axios from 'axios'

const apiClient = axios.create({
  baseURL: 'http://127.0.0.1:8000/api',
  timeout: 5000,
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
  },
  error => Promise.reject(error)
)

apiClient.interceptors.response.use(
  response => response,
  error => {
    console.error('Error en la respuesta de la API:', error)
    
    // Manejar errores de autenticación automáticamente
    if (error.response?.status === 401) {
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      window.location.href = '/login'
    }
    
    return Promise.reject(error)
  }
)

export default apiClient
