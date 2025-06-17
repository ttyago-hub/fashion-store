import axios from 'axios';

const apiClient = axios.create({
  baseURL: 'http://localhost:8000/api', // Usa 'localhost' si tu CORS permite localhost
  headers: {
    'Content-Type': 'application/json',
  },
  timeout: 5000,
  withCredentials: true // Necesario para que Sanctum funcione correctamente
});

apiClient.interceptors.response.use(
  response => response,
  error => {
    console.error('Error en la respuesta de la API:', error);
    return Promise.reject(error);
  }
);

export default apiClient;
