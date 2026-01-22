<template>
  <AdminLayout
    page-title="Gestion des utilisateurs"
    page-subtitle="Modération, approbation et suivi des comptes utilisateurs"
  >
    <!-- Stats -->
    <div class="stats-container">
      <div class="stat-card">
        <div class="stat-icon stat-icon-total">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
            <circle cx="9" cy="7" r="4"></circle>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
          </svg>
        </div>
        <div class="stat-content">
          <p class="stat-label">Total utilisateurs</p>
          <p class="stat-value">{{ stats.total }}</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon stat-icon-pending">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <polyline points="12 6 12 12 16 14"></polyline>
          </svg>
        </div>
        <div class="stat-content">
          <p class="stat-label">En attente</p>
          <p class="stat-value">{{ stats.pending }}</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon stat-icon-approved">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11.01"></polyline>
          </svg>
        </div>
        <div class="stat-content">
          <p class="stat-label">Approuvés</p>
          <p class="stat-value">{{ stats.approved }}</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon stat-icon-rejected">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="15" y1="9" x2="9" y2="15"></line>
            <line x1="9" y1="9" x2="15" y2="15"></line>
          </svg>
        </div>
        <div class="stat-content">
          <p class="stat-label">Refusés</p>
          <p class="stat-value">{{ stats.rejected }}</p>
        </div>
      </div>
    </div>

    <!-- Users content -->
    <main class="admin-content">
      <div class="toolbar">
        <div class="search-box">
          <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"></circle>
            <path d="m21 21-4.35-4.35"></path>
          </svg>
          <input
            type="text"
            v-model="searchQuery"
            placeholder="Rechercher par nom, email, entreprise..."
            class="search-input"
          />
        </div>

        <div class="filters">
          <select v-model="statusFilter" class="filter-select">
            <option value="">Tous les statuts</option>
            <option value="pending">En attente</option>
            <option value="approved">Approuvés</option>
            <option value="rejected">Refusés</option>
          </select>

          <button class="refresh-btn" @click="fetchUsers" :disabled="loading">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" :class="{ spin: loading }">
              <polyline points="23 4 23 10 17 10"></polyline>
              <polyline points="1 20 1 14 7 14"></polyline>
              <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
            </svg>
            Actualiser
          </button>
        </div>
      </div>

      <div v-if="loading" class="loading-state">
        <div class="spinner"></div>
        <p>Chargement des utilisateurs...</p>
      </div>

      <transition name="fade">
        <div v-if="error" class="error-banner">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="12"></line>
            <line x1="12" y1="16" x2="12.01" y2="16"></line>
          </svg>
          {{ error }}
          <button @click="error = ''" class="close-error">×</button>
        </div>
      </transition>

      <div v-if="!loading && filteredUsers.length" class="table-container">
        <table class="user-table">
          <thead>
            <tr>
              <th>Logo</th>
              <th>Utilisateur</th>
              <th>Entreprise</th>
              <th>Email</th>
              <th>Pays</th>
              <th>Secteur</th>
              <th>Statut</th>
              <th class="action-column">Actions</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="user in filteredUsers" :key="user.id" class="user-row">
              <td>
                <div class="logo-cell">
                  <img v-if="user.logo_url" :src="user.logo_url" alt="Logo" class="logo-img" />
                  <div v-else class="logo-placeholder">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                      <circle cx="8.5" cy="8.5" r="1.5"></circle>
                      <polyline points="21 15 16 10 5 21"></polyline>
                    </svg>
                  </div>
                </div>
              </td>

              <td>
                <div class="user-info">
                  <strong>{{ user.first_name }} {{ user.last_name }}</strong>
                  <span class="user-function">{{ user.function || 'Non spécifié' }}</span>
                </div>
              </td>

              <td>
                <div class="company-info">
                  <strong>{{ user.company_name || '-' }}</strong>
                  <span class="company-meta">{{ user.tva_number || 'N/A' }}</span>
                </div>
              </td>

              <td><span class="email-text">{{ user.email }}</span></td>
              <td><span class="country-badge">{{ user.country || '-' }}</span></td>

              <td>
                <span class="sector-text">
                  {{ user.sector === 'Autres' ? (user.sector_other || user.sector) : user.sector }}
                </span>
              </td>

              <td>
                <span :class="['status-badge', 'status-' + user.status]">
                  <span class="status-dot"></span>
                  {{ formatStatus(user.status) }}
                </span>
              </td>

              <td>
                <div class="action-buttons">
                  <button
                    v-if="user.status !== 'approved'"
                    class="action-btn approve-btn"
                    @click="approveUser(user)"
                    :disabled="loadingActionId === user.id"
                    title="Approuver"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                  </button>

                  <button
                    v-if="user.status !== 'rejected'"
                    class="action-btn reject-btn"
                    @click="rejectUser(user)"
                    :disabled="loadingActionId === user.id"
                    title="Refuser"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <line x1="18" y1="6" x2="6" y2="18"></line>
                      <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                  </button>

                  <button class="action-btn info-btn" @click="viewUserDetails(user)" title="Voir les détails">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <circle cx="12" cy="12" r="10"></circle>
                      <line x1="12" y1="16" x2="12" y2="12"></line>
                      <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="!loading && !filteredUsers.length" class="empty-state">
        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
          <circle cx="9" cy="7" r="4"></circle>
          <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
          <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
        </svg>
        <h3>Aucun utilisateur trouvé</h3>
        <p>{{ searchQuery ? 'Aucun résultat pour votre recherche' : 'Aucun utilisateur inscrit pour le moment' }}</p>
      </div>
    </main>

    <!-- Modal détails utilisateur -->
    <transition name="modal">
      <div v-if="selectedUser" class="modal-overlay" @click="closeModal">
        <div class="modal-content" @click.stop>
          <div class="modal-header">
            <h2>Détails de l'utilisateur</h2>
            <button class="modal-close" @click="closeModal">×</button>
          </div>

          <div class="modal-body">
            <div class="detail-grid">
              <div class="detail-item">
                <label>Nom complet</label>
                <p>{{ selectedUser.first_name }} {{ selectedUser.last_name }}</p>
              </div>

              <div class="detail-item">
                <label>Email</label>
                <p>{{ selectedUser.email }}</p>
              </div>

              <div class="detail-item">
                <label>Entreprise</label>
                <p>{{ selectedUser.company_name || '-' }}</p>
              </div>

              <div class="detail-item">
                <label>Fonction</label>
                <p>{{ selectedUser.function || '-' }}</p>
              </div>

              <div class="detail-item">
                <label>Numéro TVA</label>
                <p>{{ selectedUser.tva_number || '-' }}</p>
              </div>

              <div class="detail-item">
                <label>Pays</label>
                <p>{{ selectedUser.country || '-' }}</p>
              </div>

              <div class="detail-item">
                <label>Code postal</label>
                <p>{{ selectedUser.postal_code || '-' }}</p>
              </div>

              <div class="detail-item">
                <label>Adresse 1</label>
                <p>{{ selectedUser.address1 || '-' }}</p>
              </div>

              <div class="detail-item">
                <label>Adresse 2</label>
                <p>{{ selectedUser.address2 || '-' }}</p>
              </div>

              <div class="detail-item">
                <label>Secteur</label>
                <p>{{ selectedUser.sector === 'Autres' ? (selectedUser.sector_other || selectedUser.sector) : selectedUser.sector }}</p>
              </div>

              <div class="detail-item">
                <label>Partenaire</label>
                <p>{{ selectedUser.partner === 'Autre' ? (selectedUser.partner_other || selectedUser.partner) : (selectedUser.partner || '-') }}</p>
              </div>

              <div class="detail-item">
                <label>Statut</label>
                <p>
                  <span :class="['status-badge', 'status-' + selectedUser.status]">
                    <span class="status-dot"></span>
                    {{ formatStatus(selectedUser.status) }}
                  </span>
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </AdminLayout>
</template>
<script setup>
import { ref, onMounted, computed } from "vue";
import api from "@/services/api";
import AdminLayout from "@/components/AdminLayout.vue"; // si tu l'utilises dans le template

const users = ref([]);
const loading = ref(false);
const error = ref("");
const loadingActionId = ref(null);
const searchQuery = ref("");
const statusFilter = ref("");
const selectedUser = ref(null);

const token = localStorage.getItem("token");

// ✅ Si ton api.js a déjà baseURL qui finit par /api, on n’ajoute pas /api
const apiPrefix = (api.defaults.baseURL || "").endsWith("/api") ? "" : "/api";

const stats = computed(() => ({
  total: users.value.length,
  pending: users.value.filter((u) => u.status === "pending").length,
  approved: users.value.filter((u) => u.status === "approved").length,
  rejected: users.value.filter((u) => u.status === "rejected").length,
}));

const filteredUsers = computed(() => {
  let result = users.value;

  if (statusFilter.value) {
    result = result.filter((u) => u.status === statusFilter.value);
  }

  if (searchQuery.value) {
    const q = searchQuery.value.toLowerCase();
    result = result.filter(
      (u) =>
        `${u.first_name || ""} ${u.last_name || ""}`.toLowerCase().includes(q) ||
        (u.email || "").toLowerCase().includes(q) ||
        (u.company_name && u.company_name.toLowerCase().includes(q))
    );
  }

  return result;
});

const fetchUsers = async () => {
  loading.value = true;
  error.value = "";

  if (!token) {
    error.value = "Token admin manquant. Veuillez vous reconnecter.";
    loading.value = false;
    return;
  }

  try {
    const res = await api.get(`${apiPrefix}/admin/users`, {
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: "application/json",
      },
    });

    // ✅ Ancien back : res.data.data
    users.value = res.data.data || [];
  } catch (e) {
    console.error(e);
    error.value =
      e.response?.data?.message || "Erreur lors du chargement des utilisateurs.";
  } finally {
    loading.value = false;
  }
};

const formatStatus = (status) => {
  if (status === "approved") return "Approuvé";
  if (status === "rejected") return "Refusé";
  return "En attente";
};

const approveUser = async (user) => {
  if (!token) return;
  loadingActionId.value = user.id;
  error.value = "";

  try {
    const res = await api.patch(
      `${apiPrefix}/admin/users/${user.id}/approve`,
      {},
      {
        headers: {
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
        },
      }
    );

    // ✅ On met à jour depuis la réponse si dispo
    user.status = res.data.user?.status || "approved";
    user.email_verified = res.data.user?.email_verified ?? user.email_verified;
  } catch (e) {
    console.error(e);
    error.value = e.response?.data?.message || "Erreur lors de l'approbation.";
  } finally {
    loadingActionId.value = null;
  }
};

const rejectUser = async (user) => {
  if (!token) return;
  loadingActionId.value = user.id;
  error.value = "";

  try {
    const res = await api.patch(
      `${apiPrefix}/admin/users/${user.id}/reject`,
      {},
      {
        headers: {
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
        },
      }
    );

    user.status = res.data.user?.status || "rejected";
  } catch (e) {
    console.error(e);
    error.value = e.response?.data?.message || "Erreur lors du refus.";
  } finally {
    loadingActionId.value = null;
  }
};

const viewUserDetails = (user) => {
  selectedUser.value = user;
};

const closeModal = () => {
  selectedUser.value = null;
};

onMounted(fetchUsers);
</script>

<style scoped>
/* ✅ On garde le style de Users ici UNIQUEMENT (pas sidebar/header) */
.stats-container {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 20px;
  margin-bottom: 24px;
}

.stat-card {
  background: white;
  padding: 20px;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
  display: flex;
  align-items: center;
  gap: 16px;
}

.stat-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.stat-icon-total { background:#eff6ff; color:#2563eb; }
.stat-icon-pending { background:#fef3c7; color:#d97706; }
.stat-icon-approved { background:#dcfce7; color:#16a34a; }
.stat-icon-rejected { background:#fee2e2; color:#dc2626; }

.stat-label { font-size: 13px; color: #6b7280; margin-bottom: 4px; }
.stat-value { font-size: 28px; font-weight: 700; color: #0f172a; }

.admin-content { padding-bottom: 40px; }

.toolbar { display:flex; gap:16px; margin-bottom:24px; align-items:center; }
.search-box { flex:1; position:relative; }
.search-icon { position:absolute; left:14px; top:50%; transform:translateY(-50%); color:#9ca3af; }

.search-input {
  width:100%;
  padding:12px 12px 12px 44px;
  border:1px solid #e5e7eb;
  border-radius:8px;
  font-size:14px;
}

.filters{ display:flex; gap:12px; }
.filter-select, .refresh-btn {
  padding:10px 16px;
  border:1px solid #e5e7eb;
  border-radius:8px;
  background:#fff;
  cursor:pointer;
}
.refresh-btn { display:flex; gap:8px; align-items:center; }

.spin{ animation:spin 1s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

.loading-state{ text-align:center; padding:60px 20px; color:#6b7280; }
.spinner{ width:40px; height:40px; margin:0 auto 16px; border:4px solid #e5e7eb; border-top-color:#3b82f6; border-radius:999px; animation:spin .8s linear infinite; }

.error-banner{ display:flex; align-items:center; gap:12px; padding:14px 18px; background:#fee2e2; border:1px solid #fecaca; border-radius:8px; color:#991b1b; margin-bottom:20px; font-size:14px; }
.close-error{ margin-left:auto; border:none; background:none; font-size:24px; cursor:pointer; color:#991b1b; }

.fade-enter-active,.fade-leave-active{ transition: opacity .3s; }
.fade-enter-from,.fade-leave-to{ opacity:0; }

.table-container{ background:#fff; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,.05); overflow:hidden; }
.user-table{ width:100%; border-collapse:collapse; }
.user-table thead{ background:#f9fafb; border-bottom:1px solid #e5e7eb; }
.user-table th{ padding:14px 16px; text-align:left; font-size:13px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.05em; }
.user-table td{ padding:16px; font-size:14px; color:#374151; }
.user-row{ border-bottom:1px solid #f3f4f6; }
.user-row:hover{ background:#f9fafb; }

.logo-cell{ display:flex; align-items:center; justify-content:center; }
.logo-img{ width:40px; height:40px; object-fit:contain; border-radius:6px; }
.logo-placeholder{ width:40px; height:40px; background:#f3f4f6; border-radius:6px; display:flex; align-items:center; justify-content:center; color:#9ca3af; }

.user-info, .company-info{ display:flex; flex-direction:column; gap:4px; }
.user-info strong, .company-info strong{ font-weight:600; color:#0f172a; }
.user-function, .company-meta{ font-size:12px; color:#6b7280; }

.email-text{ color:#3b82f6; font-size:13px; }
.country-badge{ display:inline-block; padding:4px 10px; background:#f3f4f6; border-radius:6px; font-size:13px; font-weight:500; color:#374151; }
.sector-text{ font-size:13px; color:#6b7280; }

.status-badge{ display:inline-flex; align-items:center; gap:6px; padding:6px 12px; border-radius:999px; font-size:12px; font-weight:600; }
.status-dot{ width:6px; height:6px; border-radius:999px; }
.status-pending{ background:#fef3c7; color:#92400e; }
.status-pending .status-dot{ background:#f59e0b; }
.status-approved{ background:#dcfce7; color:#14532d; }
.status-approved .status-dot{ background:#22c55e; }
.status-rejected{ background:#fee2e2; color:#7f1d1d; }
.status-rejected .status-dot{ background:#ef4444; }

.action-column{ width:140px; }
.action-buttons{ display:flex; gap:8px; }
.action-btn{ width:32px; height:32px; border:none; border-radius:6px; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:all .2s; }
.action-btn:disabled{ opacity:.5; cursor:not-allowed; }
.approve-btn{ background:#dcfce7; color:#16a34a; }
.approve-btn:hover:not(:disabled){ background:#bbf7d0; transform:translateY(-1px); }
.reject-btn{ background:#fee2e2; color:#dc2626; }
.reject-btn:hover:not(:disabled){ background:#fecaca; transform:translateY(-1px); }
.info-btn{ background:#dbeafe; color:#2563eb; }
.info-btn:hover{ background:#bfdbfe; transform:translateY(-1px); }

.empty-state{ text-align:center; padding:80px 20px; color:#6b7280; }
.empty-state svg{ margin:0 auto 24px; opacity:.3; }
.empty-state h3{ font-size:18px; font-weight:600; color:#374151; margin-bottom:8px; }
.empty-state p{ font-size:14px; }

/* Modal */
.modal-overlay{ position:fixed; inset:0; background:rgba(0,0,0,.5); display:flex; align-items:center; justify-content:center; z-index:1000; padding:20px; }
.modal-content{ background:#fff; border-radius:16px; max-width:800px; width:100%; max-height:90vh; overflow:hidden; display:flex; flex-direction:column; box-shadow:0 20px 25px -5px rgba(0,0,0,.1); }
.modal-header{ display:flex; align-items:center; justify-content:space-between; padding:24px 32px; border-bottom:1px solid #e5e7eb; }
.modal-close{ border:none; background:none; font-size:32px; cursor:pointer; color:#9ca3af; }
.modal-body{ padding:32px; overflow-y:auto; }
.detail-grid{ display:grid; grid-template-columns:repeat(2,1fr); gap:24px; }
.detail-item label{ font-size:13px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.05em; }
.detail-item p{ font-size:15px; color:#0f172a; font-weight:500; }

.modal-enter-active,.modal-leave-active{ transition:opacity .3s; }
.modal-enter-from,.modal-leave-to{ opacity:0; }
.modal-enter-active .modal-content,.modal-leave-active .modal-content{ transition:transform .3s; }
.modal-enter-from .modal-content,.modal-leave-to .modal-content{ transform:scale(.9); }

@media (max-width: 1200px){
  .stats-container{ grid-template-columns:repeat(2,1fr); }
}
@media (max-width: 768px){
  .stats-container{ grid-template-columns:1fr; }
  .toolbar{ flex-direction:column; align-items:stretch; }
  .filters{ flex-direction:column; }
  .table-container{ overflow-x:auto; }
  .user-table{ min-width:900px; }
  .detail-grid{ grid-template-columns:1fr; }
}
</style>
