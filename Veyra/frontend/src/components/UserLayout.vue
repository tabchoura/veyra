<!-- components/UserLayout.vue -->
<template>
  <div class="user-wrapper">
    <!-- Sidebar -->
    <aside class="sidebar" :class="{ 'sidebar-collapsed': sidebarCollapsed }">
      <div class="sidebar-header">
        <div class="logo-side">
          <div class="logo-icon">V</div>
          <div v-if="!sidebarCollapsed" class="logo-text">
            <span class="logo-name">Veyra<sup>®</sup></span>
            <span class="logo-subtitle">User Console</span>
          </div>
        </div>

        <button class="sidebar-toggle" @click="toggleSidebar" title="Réduire / Agrandir">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="3" y1="12" x2="21" y2="12"></line>
            <line x1="3" y1="6" x2="21" y2="6"></line>
            <line x1="3" y1="18" x2="21" y2="18"></line>
          </svg>
        </button>
      </div>

      <nav class="sidebar-nav">
        <button @click="$router.push('/user/dashboard')" :class="['nav-item', { active: $route.path === '/user/dashboard' }]">
          <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="3" width="7" height="7"></rect>
            <rect x="14" y="3" width="7" height="7"></rect>
            <rect x="14" y="14" width="7" height="7"></rect>
            <rect x="3" y="14" width="7" height="7"></rect>
          </svg>
          <span class="nav-label">Dashboard</span>
        </button>

        <button @click="$router.push('/user/passports')" :class="['nav-item', { active: $route.path === '/user/passports' }]">
          <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
            <polyline points="14 2 14 8 20 8"></polyline>
            <line x1="16" y1="13" x2="8" y2="13"></line>
            <line x1="16" y1="17" x2="8" y2="17"></line>
          </svg>
          <span class="nav-label">Passports</span>
        </button>

        <button @click="$router.push('/user/audit')" :class="['nav-item', { active: $route.path === '/user/audit' }]">
          <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
            <polyline points="14 2 14 8 20 8"></polyline>
            <line x1="9" y1="15" x2="15" y2="15"></line>
          </svg>
          <span class="nav-label">Audit Logs</span>
        </button>

        <button @click="$router.push('/user/settings')" :class="['nav-item', { active: $route.path === '/user/settings' }]">
          <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="3"></circle>
            <path d="M12 1v6m0 6v6m5.5-11.5l-3 3m-3 3l-3 3m11.5-1.5l-6-6m-6 6l-6-6"></path>
          </svg>
          <span class="nav-label">Settings</span>
        </button>
      </nav>

      <div class="sidebar-footer">
        <button class="nav-item" @click="logout">
          <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
            <polyline points="16 17 21 12 16 7"></polyline>
            <line x1="21" y1="12" x2="9" y2="12"></line>
          </svg>
          <span class="nav-label">Logout</span>
        </button>
      </div>
    </aside>

    <!-- Main -->
    <div class="main-layout" :class="{ 'main-collapsed': sidebarCollapsed }">
      <!-- Header -->
      <header class="user-header">
        <div class="user-header-left">
          <div class="logo">Veyra<span>®</span></div>
          <div class="header-divider"></div>
          <div>
            <h1>{{ pageTitle }}</h1>
            <p class="subtitle">{{ pageSubtitle }}</p>
          </div>
        </div>

        <div class="user-header-right">
          <div class="user-info">
            <div class="user-avatar">{{ userInitial }}</div>
            <div class="user-details">
              <span class="user-name">{{ userName }}</span>
              <span class="user-role">Utilisateur</span>
            </div>
          </div>
        </div>
      </header>

      <!-- Content Slot -->
      <div class="content-section">
        <slot></slot>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';

const props = defineProps({
  pageTitle: {
    type: String,
    default: 'Dashboard Utilisateur'
  },
  pageSubtitle: {
    type: String,
    default: 'Vue d\'ensemble de la plateforme Veyra'
  }
});

const router = useRouter();
const sidebarCollapsed = ref(false);

// ✅ Une seule déclaration de userName avec toute la logique
const userName = computed(() => {
  const user = localStorage.getItem("user");
  if (user) {
    const parsed = JSON.parse(user);
    
    // Priorité: first_name + last_name > first_name > last_name > company_name > email
    if (parsed.first_name && parsed.last_name) {
      return `${parsed.first_name} ${parsed.last_name}`;
    } else if (parsed.first_name) {
      return parsed.first_name;
    } else if (parsed.last_name) {
      return parsed.last_name;
    } else if (parsed.company_name) {
      return parsed.company_name;
    } else if (parsed.email) {
      return parsed.email.split("@")[0];
    }
  }
  return "User";
});

const userInitial = computed(() => userName.value.charAt(0).toUpperCase());

const toggleSidebar = () => {
  sidebarCollapsed.value = !sidebarCollapsed.value;
};

const logout = () => {
  localStorage.removeItem("token");
  localStorage.removeItem("user");
  router.push("/login");
};
</script>

<style scoped>
* { box-sizing: border-box; }

.user-wrapper{
  min-height: 100vh;
  background: #f6f9ff;
  font-family: "Inter", system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
}

/* ===== Sidebar (bleu foncé, simple) ===== */
.sidebar{
  width: 280px;
  background: #0b2f5f;
  position: fixed;
  top: 0;
  bottom: 0;
  left: 0;
  z-index: 100;
  display: flex;
  flex-direction: column;
  transition: width .25s ease;
  border-right: 1px solid rgba(255,255,255,.10);
}

.sidebar-collapsed{ width: 80px; }

.sidebar-header{
  padding: 22px 18px;
  display:flex;
  align-items:center;
  justify-content: space-between;
  border-bottom: 1px solid rgba(255,255,255,.12);
  min-height: 78px;
}

.logo-side{
  display:flex;
  align-items:center;
  gap: 12px;
}

.logo-icon{
  width: 42px;
  height: 42px;
  background: #14b8a6;
  border-radius: 12px;
  display:flex;
  align-items:center;
  justify-content:center;
  color:#fff;
  font-weight: 800;
  font-size: 20px;
}

.logo-text{
  display:flex;
  flex-direction: column;
  gap: 2px;
}

.logo-name{
  color:#fff;
  font-size: 20px;
  font-weight: 800;
}

.logo-subtitle{
  color: rgba(255,255,255,.70);
  font-size: 11px;
  font-weight: 500;
}

.sidebar-toggle{
  background: rgba(255,255,255,.10);
  border: 1px solid rgba(255,255,255,.12);
  cursor:pointer;
  color:#fff;
  padding: 8px;
  border-radius: 10px;
  transition: .2s;
}

.sidebar-toggle:hover{
  background: rgba(255,255,255,.16);
}

.sidebar-nav{
  flex: 1;
  padding: 18px 14px;
  display:flex;
  flex-direction: column;
  gap: 10px;
  overflow-y: auto;
}

.nav-item{
  display:flex;
  align-items:center;
  gap: 14px;
  padding: 14px 16px;
  border: none;
  background: transparent;
  border-radius: 12px;
  cursor:pointer;
  color: rgba(255,255,255,.78);
  font-size: 14px;
  font-weight: 600;
  width:100%;
  text-align:left;
  transition: .2s;
  position: relative;
}

.nav-item:hover{
  background: rgba(20,184,166,.12);
  color: #fff;
}

.nav-item.active{
  background: rgba(20,184,166,.18);
  color: #fff;
}

.nav-item::before{
  content:"";
  position:absolute;
  left: 0;
  top: 50%;
  transform: translateY(-50%);
  width: 3px;
  height: 0;
  background: #14b8a6;
  border-radius: 0 6px 6px 0;
  transition: height .2s;
}

.nav-item:hover::before,
.nav-item.active::before{
  height: 60%;
}

.nav-icon{ flex-shrink: 0; }
.nav-label{ white-space: nowrap; overflow:hidden; }

.sidebar-collapsed .logo-text,
.sidebar-collapsed .nav-label{
  display:none;
}

.sidebar-footer{
  padding: 16px;
  border-top: 1px solid rgba(255,255,255,.12);
}

/* ===== Main layout ===== */
.main-layout{
  margin-left: 280px;
  min-height: 100vh;
  display:flex;
  flex-direction: column;
  transition: margin-left .25s ease;
}

.main-layout.main-collapsed{
  margin-left: 80px;
}

/* ===== Header (blanc clean) ===== */
.user-header{
  background: #ffffff;
  padding: 18px 28px;
  border-bottom: 1px solid #e5e7eb;
  display:flex;
  align-items:center;
  justify-content: space-between;
  box-shadow: 0 1px 3px rgba(0,0,0,.05);
  position: sticky;
  top: 0;
  z-index: 10;
}

.user-header-left{
  display:flex;
  align-items:center;
  gap: 18px;
}

.logo{
  font-weight: 900;
  font-size: 22px;
  color: #0b2f5f;
}

.logo span{
  font-size: 12px;
  vertical-align: super;
}

.header-divider{
  width: 1px;
  height: 40px;
  background: #e5e7eb;
}

.user-header-left h1{
  font-size: 18px;
  font-weight: 800;
  color: #0f172a;
  margin: 0;
}

.subtitle{
  font-size: 13px;
  color: #64748b;
}

/* Header right */
.user-info{
  display:flex;
  align-items:center;
  gap: 12px;
  padding: 8px 14px;
  background: #f1f5ff;
  border: 1px solid #e3eaff;
  border-radius: 999px;
}

.user-avatar{
  width: 38px;
  height: 38px;
  border-radius: 999px;
  background: #2563eb;
  display:flex;
  align-items:center;
  justify-content:center;
  color:#fff;
  font-weight: 900;
}

.user-details{
  display:flex;
  flex-direction: column;
}

.user-name{
  font-size: 14px;
  font-weight: 800;
  color: #0f172a;
}

.user-role{
  font-size: 12px;
  color: #64748b;
}

/* ===== Content ===== */
.content-section{
  padding: 24px 40px 40px;
  flex: 1;
}

/* ===== Responsive ===== */
@media (max-width: 768px){
  .user-header{
    padding: 14px 16px;
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
  }

  .content-section{
    padding: 16px;
  }

  .main-layout{
    margin-left: 0;
  }

  .sidebar{
    transform: translateX(-100%);
  }

  .sidebar.sidebar-collapsed{
    transform: translateX(0);
  }
}
</style>