import { createRouter, createWebHistory } from 'vue-router'
import Login from '@/components/Login.vue'
import Register from '@/components/Register.vue'
import DashboardAdminUsers from '@/components/AdminDashboard.vue'
import LoginAdmin from '@/components/LoginAdmin.vue'
import Forgotpassword from '@/components/Forgotpassword.vue'
import ResetPassword from '@/components/ResetPassword.vue'
const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
   
     {
      path: '/login',
      name: 'login',
      component: Login,
    },
     {
      path: '/register',
      name: 'register',
      component: Register,
    },
    {
    path: "/dashboard/admin",
    name: "DashboardAdminUsers",
    component: DashboardAdminUsers,
    // meta: { requiresAuth: true, requiresAdmin: true } // si tu as une guard
  },
   {
    path: "/login/admin",
    name: "LoginAdmin",
    component: LoginAdmin,
    // meta: { requiresAuth: true, requiresAdmin: true } // si tu as une guard
  },
  {
    path: '/forgot-password',
    name: 'Forgotpassword',
    component: Forgotpassword,
  },
  {
    path: '/reset-password',
    name: 'ResetPassword',
    component: ResetPassword,
  }
  ],
})

export default router
