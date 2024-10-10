<template>
  <div class="user-dashboard">
    <SidebarMenu :user-name="fullName" :user-role="'User'" :user-avatar="userAvatar" />
    <div class="dashboard-content">
      <div v-if="isLoading" class="loading">Loading...</div>
      <div v-else-if="error" class="error-message">{{ error }}</div>
      <div v-else>
        <div class="top-bar">
          <h1>Welcome, {{ firstName }}!</h1>
          <div class="notification-bell">
            <i class="fas fa-bell"></i>
            <span class="notification-count" v-if="notificationCount > 0">{{ notificationCount }}</span>
          </div>
        </div>
        <!-- Add your main dashboard content here -->
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import { API_BASE_URL } from '@/config';
import SidebarMenu from '@/components/SidebarMenu.vue';

const router = useRouter();

const firstName = ref('');
const lastName = ref('');
const userAvatar = ref('');
const notificationCount = ref(0);
const error = ref(null);
const isLoading = ref(true);

const fullName = computed(() => {
  if (firstName.value && lastName.value) {
    return `${firstName.value} ${lastName.value}`;
  }
  return 'User';
});

const fetchDashboardData = async () => {
  try {
    console.log('Fetching dashboard data...');
    let token = localStorage.getItem('authToken');
    if (!token) {
      throw new Error('No auth token found');
    }

    // Get user data from localStorage
    const userData = JSON.parse(localStorage.getItem('user') || '{}');
    firstName.value = userData.firstName || 'User';
    lastName.value = userData.lastName || '';

    const response = await fetch(`${API_BASE_URL}/user-dashboard-data`, {
      method: 'GET',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
      },
      credentials: 'include',
    });

    console.log('Response status:', response.status);

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const result = await response.json();
    console.log('Received dashboard data:', result);

    if (!result.success) {
      throw new Error(result.message || 'Failed to fetch dashboard data');
    }

    const data = result.data;
    userAvatar.value = data.userAvatar || '';
    notificationCount.value = data.notificationCount || 0;
  } catch (err) {
    console.error('Error fetching dashboard data:', err);
    if (err.message.includes('Unauthorized') || err.message === 'No auth token found') {
      router.push('/login');
    } else {
      error.value = `Failed to load dashboard data: ${err.message}. Please try again later or contact support if the problem persists.`;
    }
  } finally {
    isLoading.value = false;
  }
};

onMounted(fetchDashboardData);
</script>

<style scoped>
.user-dashboard {
  display: flex;
}

.dashboard-content {
  flex-grow: 1;
  margin-left: 250px; /* Width of the sidebar */
  padding: 2rem;
}

.top-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.notification-bell {
  position: relative;
  font-size: 1.5rem;
  cursor: pointer;
}

.notification-count {
  position: absolute;
  top: -8px;
  right: -8px;
  background-color: red;
  color: white;
  border-radius: 50%;
  padding: 2px 6px;
  font-size: 0.8rem;
}

.error-message {
  color: red;
  margin-bottom: 1rem;
}

.loading, .error-message {
  text-align: center;
  padding: 20px;
  font-size: 18px;
}

.error-message {
  color: red;
}
</style>
