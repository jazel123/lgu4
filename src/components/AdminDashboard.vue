<template>
  <div class="admin-dashboard">
    <SidebarMenu :user-name="userName" :user-role="userRole" :user-avatar="userAvatar" />
    <main class="main-content">
      <header class="dashboard-header">
        <h1>Admin Dashboard</h1>
        <div class="user-info">
          <img :src="userAvatar" alt="User Avatar" class="user-avatar" />
          <span>{{ userName }} ({{ userRole }})</span>
        </div>
      </header>
      <router-view></router-view>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import SidebarMenu from '@/components/SidebarMenu.vue';

const userName = ref('');
const userRole = ref('Administrator');
const userAvatar = ref('');

onMounted(() => {
  const user = JSON.parse(localStorage.getItem('user') || '{}');
  userName.value = user.firstName && user.lastName ? `${user.firstName} ${user.lastName}` : 'Admin User';
  userAvatar.value = user.avatar || 'https://i.pravatar.cc/150?img=68';
});
</script>

<style scoped>
.admin-dashboard {
  display: flex; 
}

.main-content {
  flex-grow: 1;
  padding: 2rem;
  margin-left: 250px;
}

.dashboard-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.user-info {
  display: flex;
  align-items: center;
}

.user-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  margin-right: 1rem;
}

.dashboard-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
}

.dashboard-card {
  background-color: white;
  border-radius: 8px;
  padding: 1.5rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.dashboard-card h2 {
  margin-bottom: 1rem;
}

.dashboard-link {
  display: inline-block;
  background-color: #3498db;
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  text-decoration: none;
  transition: background-color 0.3s;
}

.dashboard-link:hover {
  background-color: #2980b9;
}

@media (max-width: 768px) {
  .main-content {
    margin-left: 0;
    padding-left: 60px;
  }
}
</style>
