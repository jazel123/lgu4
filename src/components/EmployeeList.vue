<template>
  <div class="employee-list">
    <h1>Employee List</h1>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Department</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="employee in employees" :key="employee.id">
          <td>{{ employee.id }}</td>
          <td>{{ employee.name }}</td>
          <td>{{ employee.email }}</td>
          <td>{{ employee.department }}</td>
          <td>
            <button @click="editEmployee(employee.id)">Edit</button>
            <button @click="deleteEmployee(employee.id)">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { API_BASE_URL } from '@/config';

const employees = ref([]);

const fetchEmployees = async () => {
  try {
    const response = await fetch(`${API_BASE_URL}/employees`);
    employees.value = await response.json();
  } catch (error) {
    console.error('Error fetching employees:', error);
  }
};

const editEmployee = (id) => {
  // Implement edit functionality
  console.log('Edit employee:', id);
};

const deleteEmployee = async (id) => {
  try {
    await fetch(`${API_BASE_URL}/employees/${id}`, { method: 'DELETE' });
    employees.value = employees.value.filter(emp => emp.id !== id);
  } catch (error) {
    console.error('Error deleting employee:', error);
  }
};

onMounted(fetchEmployees);
</script>

<style scoped>
.employee-list {
  max-width: 1000px;
  margin: 0 auto;
  padding: 2rem;
}

table {
  width: 100%;
  border-collapse: collapse;
}

th, td {
  border: 1px solid #ddd;
  padding: 8px;
  text-align: left;
}

th {
  background-color: #f2f2f2;
}

button {
  margin-right: 5px;
}
</style>
