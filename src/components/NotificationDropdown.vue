<template>
  <div class="notification-dropdown">
    <div class="notification-header">
      <h3>Notifications</h3>
      <button @click="$emit('close')" class="close-button">&times;</button>
    </div>
    <ul v-if="notifications.length" class="notification-list">
      <li v-for="notification in notifications" :key="notification.id" class="notification-item">
        <i :class="getNotificationIcon(notification.type)"></i>
        <div class="notification-content">
          <p class="notification-message">{{ notification.message }}</p>
          <p class="notification-time">{{ formatTime(notification.time) }}</p>
        </div>
      </li>
    </ul>
    <p v-else class="no-notifications">No new notifications</p>
  </div>
</template>

<script setup>
import { defineEmits } from 'vue';

defineEmits(['close']);

const getNotificationIcon = (type) => {
  switch (type) {
    case 'leave': return 'fas fa-calendar-minus';
    case 'attendance': return 'fas fa-calendar-check';
    case 'reminder': return 'fas fa-bell';
    default: return 'fas fa-info-circle';
  }
};

const formatTime = (time) => {
  // Implement time formatting logic here
  return time;
};
</script>

<style scoped>
.notification-dropdown {
  position: absolute;
  top: 60px;
  right: 20px;
  width: 300px;
  background-color: #ffffff;
  border-radius: 10px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  z-index: 1000;
}

.notification-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
  border-bottom: 1px solid #ecf0f1;
}

.notification-header h3 {
  margin: 0;
}

.close-button {
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
}

.notification-list {
  list-style-type: none;
  padding: 0;
  margin: 0;
  max-height: 300px;
  overflow-y: auto;
}

.notification-item {
  display: flex;
  align-items: center;
  padding: 1rem;
  border-bottom: 1px solid #ecf0f1;
}

.notification-item:last-child {
  border-bottom: none;
}

.notification-item i {
  font-size: 1.2rem;
  margin-right: 1rem;
  color: #3498db;
}

.notification-content {
  flex-grow: 1;
}

.notification-message {
  margin: 0;
  font-size: 0.9rem;
}

.notification-time {
  margin: 0;
  font-size: 0.8rem;
  color: #7f8c8d;
}

.no-notifications {
  padding: 1rem;
  text-align: center;
  color: #7f8c8d;
}
</style>