import { createRouter, createWebHistory } from 'vue-router'
import Login from '@/components/Login.vue'
import Register from '@/components/Register.vue'
import DashboardAdminUsers from '@/components/AdminDashboard.vue'
import LoginAdmin from '@/components/LoginAdmin.vue'

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
,
  ],
})

export default router
