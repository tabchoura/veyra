<template>  
  <UserLayout  
    pageTitle="Dashboard"
    :pageSubtitle="`Bienvenue, ${clientName}`"
  >  
    <div class="grid">  
      <div class="card">  
        <p class="label">Mes Passports</p>  
        <h2 class="value">{{ kpi.passports }}</h2>  
        <p class="hint">Documents enregistrés</p>  
      </div>  
  
      <div class="card">  
        <p class="label">Audit Logs</p>  
        <h2 class="value">{{ kpi.logs }}</h2>  
        <p class="hint">Actions récentes</p>  
      </div>  
  
      <div class="card">  
        <p class="label">Statut</p>  
        <h2 class="value">{{ kpi.status }}</h2>  
        <p class="hint">Compte utilisateur</p>  
      </div>  
    </div>  
  
    <div class="panel">  
      <div class="panel-head">  
        <h3>Activité récente</h3>  
        <div class="actions">  
          <button class="btn" @click="$router.push('/user/passports')">Passports</button>  
          <button class="btn" @click="$router.push('/user/audit')">Audit</button>  
          <button class="btn primary" @click="$router.push('/user/settings')">Settings</button>  
        </div>  
      </div>  
  
      <div class="list">  
        <div v-for="(item, i) in recent" :key="i" class="item">  
          <span class="dot"></span>  
          <div class="txt">  
            <div class="top">  
              <span class="t">{{ item.title }}</span>  
              <span class="d">{{ item.date }}</span>  
            </div>  
            <p class="p">{{ item.desc }}</p>  
          </div>  
        </div>  
  
        <div v-if="!recent.length" class="empty">Aucune activité pour le moment.</div>  
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
  
// ✅ Nom du client connecté (First name + Last Name)  
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
  { title: "Connexion", date: "Aujourd'hui", desc: "Vous vous êtes connecté(e) à votre espace." },  
  { title: "Consultation", date: "Hier", desc: "Vous avez consulté la section Passports." },  
  { title: "Paramètres", date: "Cette semaine", desc: "Vous avez ouvert la page Settings." },  
];  
</script>  
  
<style scoped>  
.grid{  
  display:grid;  
  grid-template-columns: repeat(3, minmax(0,1fr));  
  gap: 14px;  
  margin-bottom: 16px;  
}  
  
.card{  
  background:#fff;  
  border:1px solid #e5e7eb;  
  border-radius: 16px;  
  padding: 16px;  
  box-shadow: 0 1px 3px rgba(0,0,0,.04);  
}  
  
.label{ margin:0 0 8px; color:#64748b; font-weight:800; font-size:12px; }  
.value{ margin:0; font-size:28px; font-weight:1000; color:#0f172a; }  
.hint{ margin:6px 0 0; color:#94a3b8; font-weight:700; font-size:12px; }  
  
.panel{  
  background:#fff;  
  border:1px solid #e5e7eb;  
  border-radius: 16px;  
  padding: 16px;  
  box-shadow: 0 1px 3px rgba(0,0,0,.04);  
}  
  
.panel-head{  
  display:flex;  
  align-items:center;  
  justify-content: space-between;  
  gap: 12px;  
  margin-bottom: 10px;  
}  
  
.panel-head h3{  
  margin:0;  
  font-size:14px;  
  font-weight:1000;  
  color:#0f172a;  
}  
  
.actions{ display:flex; gap: 8px; flex-wrap: wrap; }  
  
.btn{  
  border:1px solid #e5e7eb;  
  background:#f8fafc;  
  padding: 10px 12px;  
  border-radius: 12px;  
  font-weight:900;  
  cursor:pointer;  
}  
.btn:hover{ background:#eef2ff; }  
  
.btn.primary{  
  background:#14b8a6;  
  border:none;  
  color:#fff;  
}  
.btn.primary:hover{ filter: brightness(.96); }  
  
.list{ display:flex; flex-direction: column; gap: 10px; }  
  
.item{  
  display:flex;  
  gap: 12px;  
  padding: 12px;  
  border:1px solid #eef2f7;  
  border-radius: 14px;  
  background:#fbfdff;  
}  
  
.dot{  
  width:10px;  
  height:10px;  
  border-radius: 999px;  
  background:#14b8a6;  
  margin-top: 4px;  
  flex-shrink: 0;  
}  
  
.txt{ flex:1; }  
  
.top{  
  display:flex;  
  justify-content: space-between;  
  gap: 10px;  
  margin-bottom: 4px;  
}  
  
.t{ font-weight:1000; color:#0f172a; font-size: 13px; }  
.d{ color:#94a3b8; font-weight:900; font-size: 12px; }  
  
.p{ margin:0; color:#64748b; font-weight:700; font-size:12px; }  
  
.empty{  
  color:#94a3b8;  
  font-weight:900;  
  padding: 10px;  
}  
  
@media (max-width: 900px){  
  .grid{ grid-template-columns: 1fr; }  
}  
</style>