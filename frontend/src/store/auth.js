import { defineStore } from 'pinia';
import axios from '../axios';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token') || null,
  }),
  actions: {
    async login(data) {
      const res = await axios.post('/login', data);
      this.token = res.data.token;
      localStorage.setItem('token', this.token);
      await this.getUser();
    },
    async register(data) {
      await axios.post('/register', data);
    },
    async getUser() {
      const res = await axios.get('/user', {
        headers: { Authorization: `Bearer ${this.token}` }
      });
      this.user = res.data;
    },
    logout() {
      this.token = null;
      this.user = null;
      localStorage.removeItem('token');
    }
  }
});
