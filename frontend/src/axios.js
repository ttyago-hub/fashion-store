import axios from 'axios';

const apiClient = axios.create({
  baseURL: 'http://127.0.0.1:8000/api',
  timeout: 5000,
  // withCredentials: true // Solo activa si usas cookies con Sanctum
});

// Interceptor para agregar el token de Authorization a cada petición
apiClient.interceptors.request.use(
  config => {
    const token = localStorage.getItem('token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`; // Usa backticks aquí
    }
    return config;
  },
  error => {
    return Promise.reject(error);
  }
);

apiClient.interceptors.response.use(
  response => response,
  error => {
    if (error.response?.status === 401) {
      // Manejar específicamente el error 401 (token inválido/vencido)
      console.error('Error de autenticación - Redirigir a login');
      // Opcional: limpiar el token inválido
      localStorage.removeItem('token');
      // Opcional: redirigir a página de login
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);

export default apiClient;