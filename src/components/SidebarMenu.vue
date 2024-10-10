<template>
  <aside class="sidebar-menu">
    <div class="user-info">
      <img :src="userAvatar" alt="User Avatar" class="user-avatar" />
      <h3>{{ userName }}</h3>
      <p>{{ userRole }}</p>
    </div>
    <nav>
      <ul>
        <template v-if="userRole === 'Administrator'">
          <li><router-link to="/admin-dashboard">Admin Dashboard</router-link></li>
          <li><router-link to="/admin-user">User Management</router-link></li>
          <li><router-link to="/attendance-management">Attendance Management</router-link></li>
          <li><router-link to="/department-list">Department List</router-link></li>
          <li><router-link to="/employee-attendance">Employee Attendance</router-link></li>
          <li><router-link to="/leave-management">Leave Management</router-link></li>
        </template>
        <template v-else>
          <li><router-link to="/user-dashboard">Dashboard</router-link></li>
          <li><router-link to="/profile">Profile</router-link></li>
          <li><router-link to="/leave">Leave Management</router-link></li>
          <li><router-link to="/attendance">Attendance</router-link></li>
          <li><router-link to="/request-leave">Request Leave</router-link></li>
          <li><router-link to="/overtime-request">Request Overtime</router-link></li>
        </template>
        <li><router-link to="/logout">Logout</router-link></li>
      </ul>
    </nav>
  </aside>
</template>

<script setup>
import { defineProps } from 'vue';

defineProps({
  userName: String,
  userRole: String,
  userAvatar: String
});
</script>

<style scoped>
.sidebar-menu {
  width: 250px;
  height: 100vh;
  position: fixed;
  top: 0;
  left: 0;
  background-color: #2c3e50;
  color: white;
  padding: 1rem;
}

.user-info {
  text-align: center;
  margin-bottom: 2rem;
}

.user-avatar {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  margin-bottom: 1rem;
}

nav ul {
  list-style-type: none;
  padding: 0;
}

nav ul li {
  margin-bottom: 1rem;
}

nav ul li a {
  color: white;
  text-decoration: none;
  font-size: 1.1rem;
  transition: color 0.3s ease;
}

nav ul li a:hover {
  color: #3498db;
}
</style>
