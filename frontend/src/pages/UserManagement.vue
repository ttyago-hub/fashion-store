<template>
  <div>
    <h2>Gestión de Usuarios</h2>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Email</th>
          <th>Rol</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="user in users" :key="user.id">
          <td>{{ user.id }}</td>
          <td>
            <input v-if="editId === user.id" v-model="editName" />
            <span v-else>{{ user.name }}</span>
          </td>
          <td>
            <input v-if="editId === user.id" v-model="editEmail" />
            <span v-else>{{ user.email }}</span>
          </td>
          <td>
            <select v-if="editId === user.id" v-model="editRole">
              <option value="user">Usuario</option>
              <option value="admin">Administrador</option>
            </select>
            <span v-else>{{ user.role }}</span>
          </td>
          <td>
            <button v-if="editId !== user.id" @click="startEdit(user)">Editar</button>
            <button v-if="editId === user.id" @click="updateUser(user.id)">Guardar</button>
            <button v-if="editId === user.id" @click="cancelEdit">Cancelar</button>
            <button @click="deleteUser(user.id)">Eliminar</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import api from '../axios'

export default {
  data() {
    return {
      users: [],
      editId: null,
      editName: '',
      editEmail: '',
      editRole: ''
    }
  },
  mounted() {
    this.fetchUsers()
  },
  methods: {
    async fetchUsers() {
      try {
        const token = localStorage.getItem('token')
        const res = await api.get('/users', {
          headers: { Authorization: `Bearer ${token}` }
        })
        this.users = res.data
      } catch (e) {
        alert('No se pudieron cargar los usuarios')
      }
    },
    startEdit(user) {
      this.editId = user.id
      this.editName = user.name
      this.editEmail = user.email
      this.editRole = user.role
    },
    cancelEdit() {
      this.editId = null
      this.editName = ''
      this.editEmail = ''
      this.editRole = ''
    },
    async updateUser(id) {
      try {
        const token = localStorage.getItem('token')
        await api.put(`/users/${id}`, {
          name: this.editName,
          email: this.editEmail,
          role: this.editRole
        }, {
          headers: { Authorization: `Bearer ${token}` }
        })
        alert('Usuario actualizado')
        this.cancelEdit()
        this.fetchUsers()
      } catch (e) {
        alert('Error al actualizar usuario')
      }
    },
    async deleteUser(id) {
      if (!confirm('¿Seguro que deseas eliminar este usuario?')) return
      try {
        const token = localStorage.getItem('token')
        await api.delete(`/users/${id}`, {
          headers: { Authorization: `Bearer ${token}` }
        })
        alert('Usuario eliminado')
        this.fetchUsers()
      } catch (e) {
        alert('Error al eliminar usuario')
      }
    }
  }
}
</script>
<style scoped>
h2 {
  text-align: center;
  color: #4f46e5;
  margin: 1.5rem 0;
  font-size: 1.8rem;
}

table {
  width: 100%;
  max-width: 1000px;
  margin: 0 auto;
  border-collapse: collapse;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  border-radius: 8px;
  overflow: hidden;
  background-color: #fff;
}

thead {
  background-color: #4f46e5;
  color: white;
}

th, td {
  padding: 1rem;
  text-align: left;
  border-bottom: 1px solid #e5e7eb;
  font-size: 0.95rem;
}

td input,
td select {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #d1d5db;
  border-radius: 5px;
  font-size: 0.9rem;
}

td input:focus,
td select:focus {
  outline: none;
  border-color: #4f46e5;
  box-shadow: 0 0 5px rgba(79, 70, 229, 0.4);
}

button {
  margin: 2px;
  padding: 0.5rem 0.8rem;
  font-size: 0.85rem;
  font-weight: 600;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

button:hover {
  opacity: 0.9;
}

button:nth-child(1) {
  background-color: #4f46e5;
  color: white;
}

button:nth-child(2) {
  background-color: #16a34a;
  color: white;
}

button:nth-child(3) {
  background-color: #6b7280;
  color: white;
}

button:nth-child(4) {
  background-color: #dc2626;
  color: white;
}

@media (max-width: 768px) {
  table, thead, tbody, th, td, tr {
    display: block;
  }

  thead {
    display: none;
  }

  tr {
    margin-bottom: 1rem;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    padding: 0.5rem;
    background-color: #f9fafb;
  }

  td {
    display: flex;
    justify-content: space-between;
    padding: 0.75rem 0.5rem;
    border: none;
    border-bottom: 1px solid #e5e7eb;
    font-size: 0.9rem;
  }

  td::before {
    content: attr(data-label);
    font-weight: bold;
    flex-basis: 40%;
  }

  td:last-child {
    border-bottom: none;
  }
}
@media (max-width: 900px) {
  .main-container {
    flex-direction: column;
    height: auto;
  }
  .left-section, .right-section {
    flex: none;
    width: 100%;
    height: auto;
  }
  .main-image {
    height: 200px;
    object-fit: cover;
  }
}

@media (max-width: 600px) {
  .form-container {
    width: 95%;
    max-width: 100%;
    padding: 1rem;
  }
  .main-image {
    height: 120px;
  }
  h2 {
    font-size: 1.2rem;
  }
  input, button, .register-btn {
    font-size: 1rem;
    padding: 0.7rem;
  }
}
</style>
