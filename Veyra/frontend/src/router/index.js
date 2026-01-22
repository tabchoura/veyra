import { createRouter, createWebHistory } from "vue-router";
import Login from "@/components/Login.vue";
import Register from "@/components/Register.vue";
import Users from "@/components/Users.vue";
import LoginAdmin from "@/components/LoginAdmin.vue";
import Forgotpassword from "@/components/Forgotpassword.vue";
import ResetPassword from "@/components/ResetPassword.vue";
import RegisterSuccess from "@/components/RegisterSuccess.vue";
import AdminDashboard from "@/components/Admin.vue";
import UserDashboard from "@/components/UserDashboard.vue";

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: "/login", component: Login },
    { path: "/register", component: Register },
        { path: "/register-success", component: RegisterSuccess },

    { path: "/users", component: Users },
        { path: "/admin", component:AdminDashboard  },

    { path: "/login/admin", component: LoginAdmin },
    { path: "/forgot-password", component: Forgotpassword },
    { path: "/reset-password", component: ResetPassword },
     { path: "/user/dashboard", component:UserDashboard  },

  ],
});

export default router;
