import { createRouter, createWebHistory } from 'vue-router';
import UserLogin from '@/components/UserLogin.vue';
import UserRegister from '@/components/UserRegister.vue';
import UserDashboard from '@/components/UserDashboard.vue';
import AdminDashboard from '@/components/AdminDashboard.vue';
import AuthenticatedLayout from '@/components/AuthenticatedLayout.vue';
import OvertimeRequest from '@/components/OvertimeRequest.vue';
// Remove the following line:
// import { API_BASE_URL } from '@/config';

const routes = [
  { path: '/', name: 'UserLogin', component: UserLogin },
  { path: '/register', name: 'UserRegister', component: UserRegister },
  {
    path: '/',
    component: AuthenticatedLayout,
    meta: { requiresAuth: true },
    children: [
      { 
        path: 'user-dashboard', 
        name: 'UserDashboard', 
        component: UserDashboard
      },
      { 
        path: 'admin-dashboard', 
        name: 'AdminDashboard', 
        component: AdminDashboard, 
        meta: { requiresAdmin: true } 
      },
      { 
        path: 'admin-user', 
        component: () => import('@/components/AdminUser.vue'), 
        meta: { requiresAdmin: true } 
      },
      { 
        path: 'attendance-management', 
        component: () => import('@/components/AttendanceManagement.vue'), 
        meta: { requiresAdmin: true } 
      },
      { 
        path: 'department-list', 
        component: () => import('@/components/DepartmentList.vue'), 
        meta: { requiresAdmin: true } 
      },
      { 
        path: 'employee-attendance', 
        component: () => import('@/components/EmployeeAttendance.vue'), 
        meta: { requiresAdmin: true } 
      },
      { 
        path: 'leave-management', 
        component: () => import('@/components/LeaveManagement.vue'), 
        meta: { requiresAdmin: true } 
      },
      { path: 'profile', component: () => import('@/components/UserProfile.vue') },
      { path: 'leave', component: () => import('@/components/UserLeave.vue') },
      { path: 'attendance', component: () => import('@/components/UserAttendance.vue') },
      { path: 'request-leave', component: () => import('@/components/RequestLeave.vue') },
      { path: 'logout', component: () => import('@/components/UserLogout.vue') },
    ]
  },
  {
    path: '/login',
    name: 'Login',
    component: UserLogin
  },
  {
    path: '/user-dashboard',
    name: 'UserDashboard',
    component: UserDashboard
  },
  {
    path: '/overtime-request',
    name: 'OvertimeRequest',
    component: OvertimeRequest
  },
  {
    path: '/admin-dashboard',
    component: AdminDashboard,
    meta: { requiresAdmin: true }
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

router.beforeEach(async (to, from, next) => {
  const token = localStorage.getItem('authToken');
  const publicRoutes = ['/', '/register', '/login'];
  
  if (publicRoutes.includes(to.path)) {
    next();
    return;
  }

  if (!token) {
    next('/login');
    return;
  }

  try {
    // Remove the token verification request for now
    const user = JSON.parse(localStorage.getItem('user') || '{}');

    if (to.meta.requiresAdmin && user.role !== 'Administrator') {
      next('/user-dashboard');
    } else if (to.path === '/admin-dashboard' && user.role !== 'Administrator') {
      next('/user-dashboard');
    } else if (to.path === '/user-dashboard' && user.role === 'Administrator') {
      next('/admin-dashboard');
    } else {
      next();
    }
  } catch (error) {
    console.error('Navigation error:', error);
    next('/login');
  }
});

export default router;

// Keep the existing beforeEach navigation guard
