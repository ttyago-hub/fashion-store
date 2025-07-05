import axios from 'axios';

const apiClient = axios.create({
  baseURL: 'http://127.0.0.1:8000/api',
  timeout: 5000
  // withCredentials: true // Elimina o comenta esta línea si no usas cookies
});

apiClient.interceptors.response.use(
  response => response,
  error => {
    console.error('Error en la respuesta de la API:', error);
    return Promise.reject(error);
  }
);

export default apiClient;
