import { createRouter, createWebHistory } from "vue-router";
import Login from "@/components/Login.vue";
import Register from "@/components/Register.vue";
import DashboardAdminUsers from "@/components/AdminDashboard.vue";
import LoginAdmin from "@/components/LoginAdmin.vue";
import Forgotpassword from "@/components/Forgotpassword.vue";
import ResetPassword from "@/components/ResetPassword.vue";
import RegisterSuccess from "@/components/RegisterSuccess.vue";

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: "/login", component: Login },
    { path: "/register", component: Register },
        { path: "/register-success", component: RegisterSuccess },

    { path: "/dashboard/admin", component: DashboardAdminUsers },
    { path: "/login/admin", component: LoginAdmin },
    { path: "/forgot-password", component: Forgotpassword },
    { path: "/reset-password", component: ResetPassword },
  ],
});

export default router;
