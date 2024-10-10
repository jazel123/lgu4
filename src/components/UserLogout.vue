<script setup>
import { onMounted } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();

onMounted(() => {
  localStorage.removeItem('authToken');
  localStorage.removeItem('user');
  router.push('/');
});
</script>

<template>
  <div>Logging out...</div>
</template>
