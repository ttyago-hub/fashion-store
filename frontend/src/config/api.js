// Configuración centralizada de rutas del API
export const API_CONFIG = {
  // URL base del backend
  BASE_URL: 'http://127.0.0.1:8000',
  
  // Endpoints del API
  ENDPOINTS: {
    PRODUCTS: '/api/products',
    RESERVATIONS: '/api/reservations',
    AUTH: '/api/auth',
    USERS: '/api/users'
  },
  
  // Rutas de archivos estáticos
  STORAGE: {
    // URL base para archivos de storage
    BASE_URL: 'http://127.0.0.1:8000/storage',
    
    // Rutas específicas
    PRODUCTS: 'http://127.0.0.1:8000/storage/products',
    IMAGES: 'http://127.0.0.1:8000/storage/images'
  }
};

// Función helper para construir URLs de imágenes de productos
export const getProductImageUrl = (filename) => {
  if (!filename) {
    return `${API_CONFIG.STORAGE.PRODUCTS}/default.png`;
  }
  return `${API_CONFIG.STORAGE.PRODUCTS}/${filename}`;
};

// Función helper para construir URLs de imágenes generales
export const getImageUrl = (path, filename) => {
  return `${API_CONFIG.STORAGE.BASE_URL}/${path}/${filename}`;
};
