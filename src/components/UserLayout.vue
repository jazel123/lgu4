<template>
  <div class="user-layout">
    <SidebarMenu :user-name="userName" :user-role="userRole" :user-avatar="userAvatar" />
    <div class="main-content">
      <router-view></router-view>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import SidebarMenu from '@/components/SidebarMenu.vue';

const userName = ref('');
const userRole = ref('');
const userAvatar = ref('');

onMounted(() => {
  const user = JSON.parse(localStorage.getItem('user') || '{}');
  userName.value = user.firstName && user.lastName ? `${user.firstName} ${user.lastName}` : 'User';
  userRole.value = user.role || 'Employee';
  userAvatar.value = user.avatar || 'https://i.pravatar.cc/150?img=68';
});
</script>

<style scoped>
.user-layout {
  display: flex;
}

.main-content {
  flex-grow: 1;
  margin-left: 250px;
  padding: 2rem;
}
</style>