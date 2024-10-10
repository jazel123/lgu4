import { createApp } from 'vue'; // Correct import for Vue 3
import App from './App.vue';
import router from './router/index.js'; // Updated import for the router

createApp(App).use(router).mount('#app');
