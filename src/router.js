import { createRouter, createWebHistory } from 'vue-router'
// UserLayout from '@/components/UserLayout.vue'
import UserDashboard from '@/components/UserDashboard.vue'
import UserProfile from '@/components/UserProfile.vue'
import UserLeave from '@/components/UserLeave.vue'
import UserAttendance from '@/components/UserAttendance.vue'
import RequestLeave from '@/components/RequestLeave.vue'
import UserLogout from '@/components/UserLogout.vue'
import { API_BASE_URL } from '@/config'
import UserLogin from '@/components/UserLogin.vue'

const routes = [
  { path: '/', name: 'UserLogin', component: UserLogin },
  { path: '/dashboard', component: UserDashboard },
  { path: '/profile', component: UserProfile },
  { path: '/leave', component: UserLeave },
  { path: '/attendance', component: UserAttendance },
  { path: '/request-leave', component: RequestLeave },
  { path: '/logout', component: UserLogout },
]

  // Other routes...


const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach(async (to, from, next) => {
  const token = localStorage.getItem('authToken');
  const publicRoutes = ['/', '/register'];
  
  if (publicRoutes.includes(to.path)) {
    next();
    return;
  }

  if (!token) {
    next('/');
    return;
  }

  try {
    const response = await fetch(`${API_BASE_URL}/verify-token`, {
      method: 'GET',
      headers: {
        'Authorization': `Bearer ${token}`,
      },
      credentials: 'include',
    });

    if (!response.ok) {
      throw new Error('Invalid token');
    }

    const data = await response.json();
    if (to.meta.requiresAdmin && data.user.role !== 'Administrator') {
      next('/user-dashboard');
    } else {
      next();
    }
  } catch (error) {
    console.error('Token verification failed:', error);
    localStorage.removeItem('authToken');
    localStorage.removeItem('refreshToken');
    localStorage.removeItem('user');
    next('/');
  }
})

export default router
