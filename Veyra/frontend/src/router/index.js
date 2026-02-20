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
import PassportsList from "@/components/PasseporstList.vue";
import CreatePasseport from "@/components/CreatePasseport.vue";
import PasseportProgress from "@/components/PasseportProgress.vue";
import ProductType from "@/components/Steps/ProductType.vue";
import FibersComposition from "@/components/Steps/FibersComposition.vue";
import Yarn from "@/components/Steps/YarnInformation.vue";
import Fabric from "@/components/Steps/FabricInformation.vue";
import Manufacturing from "@/components/Steps/ManufacturingDetails.vue";
 import Accessories from "@/components/Steps/Accessories.vue";
import Usage from "@/components/Steps/Usage.vue";
import EndOfLife from "@/components/Steps/EndOfLife.vue";
import BawearScore from "@/components/Steps/BawearScore.vue";
import EcoBalys from "@/components/Steps/EcoBalys.vue";
  import EnvironmentalSummary from "@/components/Steps/EnvironmentalSummary.vue";
import GeneratePassport from "@/components/Steps/GeneratePassport.vue";
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
  { path: "/user/passports", component: PassportsList },
    { path: "/user/passports/createpasseport", component: CreatePasseport },
    { path: "/user/passports/producttype", component: ProductType },
    { path: "/user/passports/fiberscomposition", component: FibersComposition },
    { path: "/user/passports/yarn", component: Yarn },
    { path: "/user/passports/fabric", component: Fabric },
    { path: "/user/passports/manufacturing", component: Manufacturing },
     { path: "/user/passports/accessories", component: Accessories },
     { path: "/user/passports/usage", component: Usage },
     { path: "/user/passports/endoflife", component: EndOfLife },
     { path: "/user/passports/bawearscore", component: BawearScore },
      {path :"/user/passports/ecobalys",component : EcoBalys},
    {path :"/user/passports/environmentalsummary",component : EnvironmentalSummary},
    {path :"/user/passports/generatepassport",component : GeneratePassport},

  ],
});

export default router;
