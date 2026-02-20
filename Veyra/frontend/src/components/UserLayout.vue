<!-- components/UserLayout.vue -->
<template>
  <div class="user-wrapper">
    <aside class="sidebar" :class="{ 'sidebar-collapsed': sidebarCollapsed }">
      <div class="sidebar-header">
        <div class="logo-side">
          <div class="logo-icon">V</div>
          <div v-if="!sidebarCollapsed" class="logo-text">
            <span class="logo-name">Veyra<sup>®</sup></span>
            <span class="logo-subtitle">User Console</span>
          </div>
        </div>
        <button class="sidebar-toggle" @click="toggleSidebar">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <line x1="3" y1="7" x2="21" y2="7"/>
            <line x1="3" y1="12" x2="21" y2="12"/>
            <line x1="3" y1="17" x2="21" y2="17"/>
          </svg>
        </button>
      </div>

      <nav class="sidebar-nav">
        <div class="nav-group-label" v-if="!sidebarCollapsed">Principal</div>

        <button @click="$router.push('/user/dashboard')" :class="['nav-item', { active: $route.path === '/user/dashboard' }]">
          <span class="nav-icon">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="3" width="7" height="7" rx="1"/>
              <rect x="14" y="3" width="7" height="7" rx="1"/>
              <rect x="14" y="14" width="7" height="7" rx="1"/>
              <rect x="3" y="14" width="7" height="7" rx="1"/>
            </svg>
          </span>
          <span class="nav-label">Dashboard</span>
        </button>

        <button @click="$router.push('/user/passports')" :class="['nav-item', { active: $route.path.startsWith('/user/passports') }]">
          <span class="nav-icon">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
              <polyline points="14 2 14 8 20 8"/>
              <line x1="16" y1="13" x2="8" y2="13"/>
              <line x1="16" y1="17" x2="8" y2="17"/>
            </svg>
          </span>
          <span class="nav-label">Passports</span>
        </button>

        <button @click="$router.push('/user/audit')" :class="['nav-item', { active: $route.path === '/user/audit' }]">
          <span class="nav-icon">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
            </svg>
          </span>
          <span class="nav-label">Audit Logs</span>
        </button>

        <div class="nav-divider" v-if="!sidebarCollapsed"></div>
        <div class="nav-group-label" v-if="!sidebarCollapsed">Compte</div>

        <button @click="$router.push('/user/settings')" :class="['nav-item', { active: $route.path === '/user/settings' }]">
          <span class="nav-icon">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="3"/>
              <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
            </svg>
          </span>
          <span class="nav-label">Settings</span>
        </button>
      </nav>

      <div class="sidebar-footer">
        <div class="user-card" v-if="!sidebarCollapsed">
          <div class="user-avatar-sm">{{ userInitial }}</div>
          <div class="user-card-info">
            <span class="user-card-name">{{ userName }}</span>
            <span class="user-card-role">Utilisateur</span>
          </div>
        </div>
        <button class="nav-item logout-btn" @click="logout">
          <span class="nav-icon">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
              <polyline points="16 17 21 12 16 7"/>
              <line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
          </span>
          <span class="nav-label">Logout</span>
        </button>
      </div>
    </aside>

    <div class="main-layout" :class="{ 'main-collapsed': sidebarCollapsed }">
      <header class="user-header">
        <div class="header-left">
          <div class="page-info">
            <h1 class="page-title">{{ pageTitle }}</h1>
            <p class="page-subtitle">{{ pageSubtitle }}</p>
          </div>
        </div>
        <div class="header-right">
          <div class="header-avatar">{{ userInitial }}</div>
          <div class="header-user">
            <span class="header-name">{{ userName }}</span>
            <span class="header-role">Utilisateur</span>
          </div>
        </div>
      </header>

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
  pageTitle: { type: String, default: 'Dashboard' },
  pageSubtitle: { type: String, default: "Vue d'ensemble de la plateforme Veyra" }
});

const router = useRouter();
const sidebarCollapsed = ref(false);

const userName = computed(() => {
  const u = localStorage.getItem("user");
  if (!u) return "User";
  const p = JSON.parse(u);
  if (p.first_name && p.last_name) return `${p.first_name} ${p.last_name}`;
  if (p.first_name) return p.first_name;
  if (p.last_name) return p.last_name;
  if (p.company_name) return p.company_name;
  if (p.email) return p.email.split("@")[0];
  return "User";
});

const userInitial = computed(() => userName.value.charAt(0).toUpperCase());
const toggleSidebar = () => { sidebarCollapsed.value = !sidebarCollapsed.value; };
const logout = () => {
  localStorage.removeItem("token");
  localStorage.removeItem("user");
  router.push("/login");
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap');

* { box-sizing: border-box; margin: 0; padding: 0; }

.user-wrapper {
  min-height: 100vh;
  background: #f5f5f3;
  font-family: 'DM Sans', sans-serif;
  display: flex;
}

/* =====================
   SIDEBAR — Bleu Marine Pro
   ===================== */
.sidebar {
  width: 248px;
  background: #01275a;
  position: fixed;
  top: 0; bottom: 0; left: 0;
  z-index: 100;
  display: flex;
  flex-direction: column;
  transition: width 0.25s cubic-bezier(0.4, 0, 0.2, 1);
  border-right: 1px solid #011f47;
}

.sidebar-collapsed { width: 68px; }

.sidebar-header {
  padding: 20px 16px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  min-height: 72px;
  border-bottom: 1px solid rgba(255,255,255,0.08);
}

.logo-side {
  display: flex;
  align-items: center;
  gap: 11px;
  overflow: hidden;
}

.logo-icon {
  width: 36px;
  height: 36px;
  background: rgba(255,255,255,0.15);
  border: 1px solid rgba(255,255,255,0.2);
  border-radius: 9px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 800;
  font-size: 17px;
  flex-shrink: 0;
}

.logo-text {
  display: flex;
  flex-direction: column;
  gap: 1px;
  white-space: nowrap;
}

.logo-name {
  color: #ffffff;
  font-size: 16px;
  font-weight: 700;
  letter-spacing: -0.02em;
}

.logo-name sup { font-size: 9px; opacity: 0.6; }

.logo-subtitle {
  color: rgba(255,255,255,0.35);
  font-size: 10px;
  font-weight: 500;
  letter-spacing: 0.08em;
  text-transform: uppercase;
}

.sidebar-toggle {
  background: rgba(255,255,255,0.08);
  border: 1px solid rgba(255,255,255,0.1);
  cursor: pointer;
  color: rgba(255,255,255,0.45);
  padding: 7px;
  border-radius: 7px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.15s ease;
  flex-shrink: 0;
}

.sidebar-toggle:hover {
  background: rgba(255,255,255,0.14);
  color: rgba(255,255,255,0.9);
}

.sidebar-nav {
  flex: 1;
  padding: 16px 12px;
  display: flex;
  flex-direction: column;
  gap: 2px;
  overflow-y: auto;
}

.sidebar-nav::-webkit-scrollbar { width: 3px; }
.sidebar-nav::-webkit-scrollbar-thumb {
  background: rgba(255,255,255,0.1);
  border-radius: 10px;
}

.nav-group-label {
  font-size: 10px;
  font-weight: 700;
  color: rgba(255,255,255,0.22);
  text-transform: uppercase;
  letter-spacing: 0.1em;
  padding: 0 8px;
  margin: 8px 0 4px;
}

.nav-divider {
  height: 1px;
  background: rgba(255,255,255,0.07);
  margin: 10px 0 4px;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 11px;
  padding: 10px 10px;
  border: none;
  background: transparent;
  border-radius: 8px;
  cursor: pointer;
  color: rgba(255,255,255,0.45);
  font-size: 13.5px;
  font-weight: 500;
  width: 100%;
  text-align: left;
  transition: all 0.15s ease;
  font-family: 'DM Sans', sans-serif;
}

.nav-item:hover {
  background: rgba(255,255,255,0.08);
  color: rgba(255,255,255,0.9);
}

.nav-item.active {
  background: rgba(255,255,255,0.12);
  color: #ffffff;
  font-weight: 600;
}

.nav-item.active .nav-icon { color: #7eb8ff; }

.nav-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  width: 20px;
  height: 20px;
  transition: color 0.15s ease;
}

.nav-label { white-space: nowrap; overflow: hidden; }

.sidebar-collapsed .logo-text,
.sidebar-collapsed .nav-label,
.sidebar-collapsed .nav-group-label,
.sidebar-collapsed .nav-divider,
.sidebar-collapsed .user-card { display: none; }

.sidebar-footer {
  padding: 12px;
  border-top: 1px solid rgba(255,255,255,0.07);
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.user-card {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px;
  border-radius: 8px;
  background: rgba(255,255,255,0.07);
  margin-bottom: 4px;
}

.user-avatar-sm {
  width: 30px;
  height: 30px;
  border-radius: 7px;
  background: rgba(255,255,255,0.15);
  border: 1px solid rgba(255,255,255,0.18);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 700;
  font-size: 13px;
  flex-shrink: 0;
}

.user-card-info {
  display: flex;
  flex-direction: column;
  gap: 1px;
  overflow: hidden;
}

.user-card-name {
  font-size: 12.5px;
  font-weight: 600;
  color: rgba(255,255,255,0.85);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.user-card-role {
  font-size: 11px;
  color: rgba(255,255,255,0.28);
  font-weight: 400;
}

.logout-btn { color: rgba(255,255,255,0.35); }
.logout-btn:hover {
  background: rgba(239, 68, 68, 0.12);
  color: #fca5a5;
}

/* =====================
   MAIN
   ===================== */
.main-layout {
  margin-left: 248px;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  flex: 1;
  transition: margin-left 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

.main-layout.main-collapsed { margin-left: 68px; }

.user-header {
  background: #ffffff;
  padding: 0 32px;
  height: 64px;
  border-bottom: 1px solid #e8e8e5;
  display: flex;
  align-items: center;
  justify-content: space-between;
  position: sticky;
  top: 0;
  z-index: 50;
}

.header-left { display: flex; align-items: center; }

.page-title {
  font-size: 16px;
  font-weight: 700;
  color: #1a1a18;
  letter-spacing: -0.02em;
  margin: 0;
}

.page-subtitle {
  font-size: 12px;
  color: #a0a09a;
  font-weight: 400;
  margin: 2px 0 0;
}

.header-right {
  display: flex;
  align-items: center;
  gap: 10px;
}

.header-avatar {
  width: 34px;
  height: 34px;
  border-radius: 8px;
  background: #01275a;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 700;
  font-size: 13px;
  font-family: 'DM Mono', monospace;
}

.header-user { display: flex; flex-direction: column; gap: 1px; }

.header-name {
  font-size: 13px;
  font-weight: 600;
  color: #1a1a18;
}

.header-role {
  font-size: 11px;
  color: #a0a09a;
  font-weight: 400;
}

.content-section {
  padding: 28px 32px 40px;
  flex: 1;
}

/* =====================
   RESPONSIVE
   ===================== */
@media (max-width: 1024px) {
  .sidebar { width: 68px; }
  .sidebar .logo-text,
  .sidebar .nav-label,
  .sidebar .nav-group-label,
  .sidebar .nav-divider,
  .sidebar .user-card { display: none; }
  .main-layout { margin-left: 68px; }
}

@media (max-width: 768px) {
  .user-header { padding: 0 16px; }
  .content-section { padding: 20px 16px; }
}
</style>