<template>  
  <UserLayout  
    pageTitle="Dashboard"
    :pageSubtitle="`Bienvenue, ${clientName}`"
  >  
    <!-- KPI Cards -->
    <div class="grid">  
      <div class="card passports">
        <div class="card-icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
            <polyline points="14 2 14 8 20 8"></polyline>
            <line x1="16" y1="13" x2="8" y2="13"></line>
            <line x1="16" y1="17" x2="8" y2="17"></line>
            <polyline points="10 9 9 9 8 9"></polyline>
          </svg>
        </div>
        <div class="card-content">
          <p class="label">Mes Passports</p>  
          <h2 class="value">{{ kpi.passports }}</h2>  
          <p class="hint">Documents enregistrés</p>
        </div>
        <div class="card-glow"></div>
      </div>  
  
      <div class="card logs">
        <div class="card-icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
            <polyline points="14 2 14 8 20 8"></polyline>
            <line x1="16" y1="13" x2="8" y2="13"></line>
            <line x1="16" y1="17" x2="8" y2="17"></line>
          </svg>
        </div>
        <div class="card-content">
          <p class="label">Audit Logs</p>  
          <h2 class="value">{{ kpi.logs }}</h2>  
          <p class="hint">Actions récentes</p>
        </div>
        <div class="card-glow"></div>
      </div>  
  
      <div class="card status">
        <div class="card-icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11.01"></polyline>
          </svg>
        </div>
        <div class="card-content">
          <p class="label">Statut</p>  
          <h2 class="value status-value">{{ kpi.status }}</h2>  
          <p class="hint">Compte utilisateur</p>
        </div>
        <div class="card-glow"></div>
      </div>  
    </div>  
  
    <!-- Quick Actions -->
    <div class="quick-actions">
      <h3 class="section-title">Actions rapides</h3>
      <div class="actions-grid">
        <button class="action-card" @click="$router.push('/user/passports/createpasseport')">
          <div class="action-icon primary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <line x1="12" y1="5" x2="12" y2="19"></line>
              <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
          </div>
          <div class="action-content">
            <h4>Nouveau Passport</h4>
            <p>Créer un passeport produit</p>
          </div>
          <svg class="action-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
        </button>

        <button class="action-card" @click="$router.push('/user/passports')">
          <div class="action-icon blue">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
              <polyline points="14 2 14 8 20 8"></polyline>
            </svg>
          </div>
          <div class="action-content">
            <h4>Mes Passports</h4>
            <p>Gérer vos documents</p>
          </div>
          <svg class="action-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
        </button>

        <button class="action-card" @click="$router.push('/user/settings')">
          <div class="action-icon purple">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="3"></circle>
              <path d="M12 1v6m0 6v6m5.196-14.196l-4.242 4.242m-2.828 2.828l-4.242 4.242M23 12h-6m-6 0H1m14.196 5.196l-4.242-4.242m-2.828-2.828l-4.242-4.242"></path>
            </svg>
          </div>
          <div class="action-content">
            <h4>Paramètres</h4>
            <p>Configurer votre compte</p>
          </div>
          <svg class="action-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
        </button>
      </div>
    </div>
  
    <!-- Recent Activity -->
    <div class="panel">  
      <div class="panel-head">  
        <div class="panel-title">
          <div class="panel-title-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
            </svg>
          </div>
          <h3>Activité récente</h3>
        </div>
        <span class="activity-count">{{ recent.length }} événements</span>
      </div>  
  
      <div class="list">  
        <div v-for="(item, i) in recent" :key="i" class="item" :style="`--delay: ${i * 0.07}s`">  
          <div class="item-indicator" :class="item.type || 'default'"></div>
          <div class="item-content">  
            <div class="item-header">  
              <span class="item-title">{{ item.title }}</span>  
              <span class="item-date">{{ item.date }}</span>  
            </div>  
            <p class="item-desc">{{ item.desc }}</p>  
          </div>  
        </div>  
  
        <div v-if="!recent.length" class="empty">
          <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="12"></line>
            <line x1="12" y1="16" x2="12.01" y2="16"></line>
          </svg>
          <p>Aucune activité pour le moment</p>
        </div>  
      </div>  
    </div>  
  </UserLayout>  
</template>  
  
<script setup>  
import { computed } from "vue";  
import UserLayout from "@/components/UserLayout.vue";

const user = computed(() => {  
  const u = localStorage.getItem("user");  
  return u ? JSON.parse(u) : null;  
});  
  
const clientName = computed(() => {  
  if (!user.value) return "Client";  
  if (user.value.first_name && user.value.last_name) {  
    return `${user.value.first_name} ${user.value.last_name}`;  
  } else if (user.value.first_name) {  
    return user.value.first_name;  
  } else if (user.value.last_name) {  
    return user.value.last_name;  
  } else if (user.value.company_name) {  
    return user.value.company_name;  
  } else {  
    return user.value.email ? user.value.email.split("@")[0] : "Client";  
  }  
});  
  
const kpi = computed(() => ({  
  passports: 0,
  logs: 0,  
  status: user.value ? "Actif" : "—",  
}));  
  
const recent = [  
  { title: "Connexion", date: "Aujourd'hui", desc: "Vous vous êtes connecté(e) à votre espace.", type: "login" },  
  { title: "Consultation", date: "Hier", desc: "Vous avez consulté la section Passports.", type: "view" },  
  { title: "Paramètres", date: "Cette semaine", desc: "Vous avez ouvert la page Settings.", type: "settings" },  
];  
</script>  
  
<style scoped>  
/* ========================================
   KPI Cards
   ======================================== */
.grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 18px;
  margin-bottom: 24px;
}

.card {
  background: white;
  border: 1px solid #e8edf5;
  border-radius: 18px;
  padding: 22px 22px 20px;
  display: flex;
  gap: 16px;
  align-items: flex-start;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04), 0 4px 16px rgba(0, 0, 0, 0.03);
  transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
  position: relative;
  overflow: hidden;
}

.card-glow {
  position: absolute;
  top: 0; left: 0; right: 0;
  height: 2px;
  background: linear-gradient(90deg, transparent 0%, var(--accent-color) 50%, transparent 100%);
  opacity: 0;
  transition: opacity 0.3s ease;
}

.card:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
  border-color: rgba(var(--accent-rgb), 0.2);
}

.card:hover .card-glow {
  opacity: 1;
}

.card.passports {
  --accent-color: #01367df2;
  --accent-rgb: 1, 54, 125;
}
.card.logs {
  --accent-color: #8b5cf6;
  --accent-rgb: 139, 92, 246;
}
.card.status {
  --accent-color: #10b981;
  --accent-rgb: 16, 185, 129;
}

.card-icon {
  width: 46px;
  height: 46px;
  border-radius: 13px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.card:hover .card-icon {
  transform: scale(1.1) rotate(4deg);
}

.card.passports .card-icon {
  background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
  color: #01367df2;
}
.card.logs .card-icon {
  background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%);
  color: #8b5cf6;
}
.card.status .card-icon {
  background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
  color: #10b981;
}

.card-content { flex: 1; }

.label {
  margin: 0 0 6px;
  color: #64748b;
  font-weight: 700;
  font-size: 12.5px;
  letter-spacing: 0.01em;
}

.value {
  margin: 0;
  font-size: 34px;
  font-weight: 900;
  color: #0f172a;
  line-height: 1;
  letter-spacing: -0.03em;
}

.status-value {
  font-size: 28px;
}

.hint {
  margin: 7px 0 0;
  color: #94a3b8;
  font-weight: 600;
  font-size: 11.5px;
}

/* ========================================
   Quick Actions
   ======================================== */
.quick-actions { margin-bottom: 24px; }

.section-title {
  font-size: 17px;
  font-weight: 800;
  color: #0f172a;
  margin: 0 0 14px;
  letter-spacing: -0.02em;
}

.actions-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 14px;
}

.action-card {
  background: white;
  border: 1px solid #e8edf5;
  border-radius: 14px;
  padding: 16px 16px 16px 16px;
  display: flex;
  gap: 14px;
  align-items: center;
  cursor: pointer;
  transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
  text-align: left;
  box-shadow: 0 1px 4px rgba(0,0,0,0.03);
}

.action-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(1, 54, 125, 0.1);
  border-color: #c7d9f5;
}

.action-card:hover .action-arrow {
  opacity: 1;
  transform: translateX(0);
  color: #01367d;
}

.action-icon {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.action-icon.primary {
  background: linear-gradient(135deg, #01367df2 0%, #0245a1 100%);
  color: white;
}
.action-icon.blue {
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
  color: white;
}
.action-icon.purple {
  background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
  color: white;
}

.action-content { flex: 1; }

.action-content h4 {
  margin: 0;
  font-size: 13.5px;
  font-weight: 700;
  color: #0f172a;
}

.action-content p {
  margin: 3px 0 0;
  font-size: 11.5px;
  color: #64748b;
  font-weight: 500;
}

.action-arrow {
  opacity: 0;
  transform: translateX(-4px);
  transition: all 0.2s ease;
  color: #94a3b8;
  flex-shrink: 0;
}

/* ========================================
   Activity Panel
   ======================================== */
.panel {
  background: white;
  border: 1px solid #e8edf5;
  border-radius: 18px;
  padding: 20px;
  box-shadow: 0 1px 4px rgba(0,0,0,0.04), 0 4px 16px rgba(0,0,0,0.03);
}

.panel-head {
  margin-bottom: 16px;
  padding-bottom: 14px;
  border-bottom: 1.5px solid #f1f5f9;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.panel-title {
  display: flex;
  align-items: center;
  gap: 10px;
}

.panel-title-icon {
  width: 32px;
  height: 32px;
  border-radius: 9px;
  background: linear-gradient(135deg, #eff6ff, #dbeafe);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #01367d;
}

.panel-title h3 {
  margin: 0;
  font-size: 15px;
  font-weight: 800;
  color: #0f172a;
  letter-spacing: -0.01em;
}

.activity-count {
  font-size: 11px;
  font-weight: 700;
  color: #94a3b8;
  background: #f8fafc;
  border: 1px solid #e8edf5;
  padding: 4px 10px;
  border-radius: 20px;
}

.list {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.item {
  display: flex;
  gap: 14px;
  padding: 13px 14px;
  border: 1px solid #f1f5f9;
  border-radius: 12px;
  background: #fafbfc;
  transition: all 0.2s ease;
  animation: fadeIn 0.4s ease calc(var(--delay, 0s)) both;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(8px); }
  to { opacity: 1; transform: translateY(0); }
}

.item:hover {
  background: white;
  border-color: #e2eaf6;
  transform: translateX(4px);
  box-shadow: 0 2px 12px rgba(1, 54, 125, 0.06);
}

.item-indicator {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  margin-top: 5px;
  flex-shrink: 0;
  transition: all 0.2s;
}

.item-indicator.login,
.item-indicator.default {
  background: #01367d;
  box-shadow: 0 0 0 3px rgba(1, 54, 125, 0.1);
}
.item-indicator.view {
  background: #8b5cf6;
  box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
}
.item-indicator.settings {
  background: #f59e0b;
  box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
}

.item:hover .item-indicator {
  transform: scale(1.3);
}

.item-content { flex: 1; }

.item-header {
  display: flex;
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 4px;
}

.item-title {
  font-weight: 700;
  color: #0f172a;
  font-size: 13.5px;
}

.item-date {
  color: #94a3b8;
  font-weight: 600;
  font-size: 11px;
  white-space: nowrap;
}

.item-desc {
  margin: 0;
  color: #64748b;
  font-weight: 500;
  font-size: 12.5px;
  line-height: 1.5;
}

.empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 48px 20px;
  color: #94a3b8;
  gap: 10px;
}

.empty svg { opacity: 0.4; }

.empty p {
  margin: 0;
  font-weight: 600;
  font-size: 13px;
}

/* ========================================
   Responsive
   ======================================== */
@media (max-width: 1024px) {
  .grid, .actions-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .card { padding: 16px; }
  .value { font-size: 28px; }
  .action-card { padding: 14px; }
}
</style>