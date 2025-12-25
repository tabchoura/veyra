<template>
  <div class="admin-wrapper">
    <!-- Header -->
    <header class="admin-header">
      <div class="admin-header-left">
        <div class="logo">Veyra<span>®</span></div>
        <div class="header-divider"></div>
        <div>
          <h1>Dashboard Administrateur</h1>
          <p class="subtitle">Gestion des utilisateurs Veyra</p>
        </div>
      </div>

      <div class="admin-header-right">
        <div class="admin-info">
          <div class="admin-avatar">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
              <circle cx="12" cy="7" r="4"></circle>
            </svg>
          </div>
          <div class="admin-details">
            <span class="admin-name">{{ adminName }}</span>
            <span class="admin-role">Administrateur</span>
          </div>
        </div>
        <button class="logout-btn" @click="logout">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
            <polyline points="16 17 21 12 16 7"></polyline>
            <line x1="21" y1="12" x2="9" y2="12"></line>
          </svg>
          Se déconnecter
        </button>
      </div>
    </header>

    <!-- Statistiques -->
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

    <!-- Contenu principal -->
    <main class="admin-content">
      <!-- Barre de filtres et recherche -->
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
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" :class="{ 'spin': loading }">
              <polyline points="23 4 23 10 17 10"></polyline>
              <polyline points="1 20 1 14 7 14"></polyline>
              <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
            </svg>
            Actualiser
          </button>
        </div>
      </div>

      <!-- Messages -->
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

      <!-- Table -->
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
              <!-- Logo -->
              <td>
                <div class="logo-cell">
                  <img
                    v-if="user.logo_url"
                    :src="user.logo_url"
                    alt="Logo"
                    class="logo-img"
                  />
                  <div v-else class="logo-placeholder">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                      <circle cx="8.5" cy="8.5" r="1.5"></circle>
                      <polyline points="21 15 16 10 5 21"></polyline>
                    </svg>
                  </div>
                </div>
              </td>

              <!-- Utilisateur -->
              <td>
                <div class="user-info">
                  <strong>{{ user.first_name }} {{ user.last_name }}</strong>
                  <span class="user-function">{{ user.function || 'Non spécifié' }}</span>
                </div>
              </td>

              <!-- Entreprise -->
              <td>
                <div class="company-info">
                  <strong>{{ user.company_name || '-' }}</strong>
                  <span class="company-meta">{{ user.tva_number || 'N/A' }}</span>
                </div>
              </td>

              <!-- Email -->
              <td>
                <span class="email-text">{{ user.email }}</span>
              </td>

              <!-- Pays -->
              <td>
                <span class="country-badge">{{ user.country || '-' }}</span>
              </td>

              <!-- Secteur -->
              <td>
                <span class="sector-text">
                  {{ user.sector === 'Autres' ? (user.sector_other || user.sector) : user.sector }}
                </span>
              </td>

              <!-- Statut -->
              <td>
                <span :class="['status-badge', 'status-' + user.status]">
                  <span class="status-dot"></span>
                  {{ formatStatus(user.status) }}
                </span>
              </td>

              <!-- Actions -->
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

                  <button
                    class="action-btn info-btn"
                    @click="viewUserDetails(user)"
                    title="Voir les détails"
                  >
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

      <!-- État vide -->
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
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import api from "@/services/api";
import { useRouter } from "vue-router";

const router = useRouter();

const users = ref([]);
const loading = ref(false);
const error = ref("");
const loadingActionId = ref(null);
const searchQuery = ref("");
const statusFilter = ref("");
const selectedUser = ref(null);

const token = localStorage.getItem("token");
const adminName = computed(() => {
  const user = localStorage.getItem("user");
  if (user) {
    const parsed = JSON.parse(user);
    return parsed.email?.split('@')[0] || 'Admin';
  }
  return 'Admin';
});

// Axios.defaults.baseURL = import.meta.env.PROD
//   ? "https://dtex.greenpulse-consulting.tn"
//   : "http://127.0.0.1:8000";
const stats = computed(() => {
  return {
    total: users.value.length,
    pending: users.value.filter(u => u.status === 'pending').length,
    approved: users.value.filter(u => u.status === 'approved').length,
    rejected: users.value.filter(u => u.status === 'rejected').length,
  };
});

const filteredUsers = computed(() => {
  let result = users.value;

  // Filtre par statut
  if (statusFilter.value) {
    result = result.filter(u => u.status === statusFilter.value);
  }

  // Filtre par recherche
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(u => 
      u.first_name?.toLowerCase().includes(query) ||
      u.last_name?.toLowerCase().includes(query) ||
      u.email?.toLowerCase().includes(query) ||
      u.company_name?.toLowerCase().includes(query)
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
const res = await api.get("/api/admin/users", {
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: "application/json",
      },
    });

    users.value = res.data.data || [];
  } catch (e) {
    console.error(e);
    error.value = e.response?.data?.message || "Erreur lors du chargement des utilisateurs.";
  } finally {
    loading.value = false;
  }
};

const approveUser = async (user) => {
  if (!token) return;
  loadingActionId.value = user.id;
  error.value = "";

  try {
const res = await api.patch(
      `/api/admin/users/${user.id}/approve`,
      {},
      {
        headers: {
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
        },
      }
    );

    user.status = res.data.user.status;
    user.email_verified = res.data.user.email_verified;
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
      `/api/admin/users/${user.id}/reject`,
      {},
      {
        headers: {
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
        },
      }
    );

    user.status = res.data.user.status;
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

const formatStatus = (status) => {
  if (status === "approved") return "Approuvé";
  if (status === "rejected") return "Refusé";
  return "En attente";
};

const logout = () => {
  localStorage.removeItem("token");
  localStorage.removeItem("user");
  router.push("/login/admin");
};

onMounted(() => {
  fetchUsers();
});
</script>

<style scoped>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.admin-wrapper {
  min-height: 100vh;
  background: #f8fafc;
  font-family: "Inter", sans-serif;
}

/* Header */
.admin-header {
  background: white;
  padding: 20px 40px;
  border-bottom: 1px solid #e5e7eb;
  display: flex;
  align-items: center;
  justify-content: space-between;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.admin-header-left {
  display: flex;
  align-items: center;
  gap: 20px;
}

.logo {
  font-weight: 700;
  font-size: 22px;
  color: #001d48;
}

.logo span {
  font-size: 13px;
  vertical-align: super;
}

.header-divider {
  width: 1px;
  height: 40px;
  background: #e5e7eb;
}

.admin-header-left h1 {
  font-size: 20px;
  font-weight: 600;
  color: #0f172a;
  margin-bottom: 2px;
}

.subtitle {
  font-size: 13px;
  color: #6b7280;
}

.admin-header-right {
  display: flex;
  align-items: center;
  gap: 16px;
}

.admin-info {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 8px 16px;
  background: #f9fafb;
  border-radius: 999px;
}

.admin-avatar {
  width: 36px;
  height: 36px;
  border-radius: 999px;
  background: linear-gradient(135deg, #3b82f6, #2563eb);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
}

.admin-details {
  display: flex;
  flex-direction: column;
}

.admin-name {
  font-size: 14px;
  font-weight: 600;
  color: #0f172a;
}

.admin-role {
  font-size: 12px;
  color: #6b7280;
}

.logout-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  border: none;
  background: #ef4444;
  color: white;
  font-size: 14px;
  padding: 10px 18px;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.2s;
}

.logout-btn:hover {
  background: #dc2626;
  transform: translateY(-1px);
}

/* Stats */
.stats-container {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 20px;
  padding: 24px 40px;
}

.stat-card {
  background: white;
  padding: 20px;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
  display: flex;
  align-items: center;
  gap: 16px;
  transition: all 0.2s;
}

.stat-card:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  transform: translateY(-2px);
}

.stat-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.stat-icon-total {
  background: #eff6ff;
  color: #2563eb;
}

.stat-icon-pending {
  background: #fef3c7;
  color: #d97706;
}

.stat-icon-approved {
  background: #dcfce7;
  color: #16a34a;
}

.stat-icon-rejected {
  background: #fee2e2;
  color: #dc2626;
}

.stat-content {
  flex: 1;
}

.stat-label {
  font-size: 13px;
  color: #6b7280;
  margin-bottom: 4px;
}

.stat-value {
  font-size: 28px;
  font-weight: 700;
  color: #0f172a;
}

/* Content */
.admin-content {
  padding: 0 40px 40px;
}

/* Toolbar */
.toolbar {
  display: flex;
  gap: 16px;
  margin-bottom: 24px;
  align-items: center;
}

.search-box {
  flex: 1;
  position: relative;
}

.search-icon {
  position: absolute;
  left: 14px;
  top: 50%;
  transform: translateY(-50%);
  color: #9ca3af;
}

.search-input {
  width: 100%;
  padding: 12px 12px 12px 44px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  font-size: 14px;
  background: white;
  transition: all 0.2s;
}

.search-input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.filters {
  display: flex;
  gap: 12px;
}

.filter-select {
  padding: 10px 16px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  font-size: 14px;
  background: white;
  cursor: pointer;
  transition: all 0.2s;
}

.filter-select:focus {
  outline: none;
  border-color: #3b82f6;
}

.refresh-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 18px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  background: white;
  cursor: pointer;
  font-size: 14px;
  font-weight: 500;
  color: #374151;
  transition: all 0.2s;
}

.refresh-btn:hover {
  background: #f9fafb;
}

.refresh-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* Loading */
.loading-state {
  text-align: center;
  padding: 60px 20px;
  color: #6b7280;
}

.spinner {
  width: 40px;
  height: 40px;
  margin: 0 auto 16px;
  border: 4px solid #e5e7eb;
  border-top-color: #3b82f6;
  border-radius: 999px;
  animation: spin 0.8s linear infinite;
}

/* Error Banner */
.error-banner {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 14px 18px;
  background: #fee2e2;
  border: 1px solid #fecaca;
  border-radius: 8px;
  color: #991b1b;
  margin-bottom: 20px;
  font-size: 14px;
}

.close-error {
  margin-left: auto;
  border: none;
  background: none;
  font-size: 24px;
  cursor: pointer;
  color: #991b1b;
  line-height: 1;
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s;
}

/* Continuation of styles */

.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

/* Table */
.table-container {
  background: white;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
  overflow: hidden;
}

.user-table {
  width: 100%;
  border-collapse: collapse;
}

.user-table thead {
  background: #f9fafb;
  border-bottom: 1px solid #e5e7eb;
}

.user-table th {
  padding: 14px 16px;
  text-align: left;
  font-size: 13px;
  font-weight: 600;
  color: #6b7280;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.user-table tbody tr {
  border-bottom: 1px solid #f3f4f6;
  transition: all 0.2s;
}

.user-table tbody tr:hover {
  background: #f9fafb;
}

.user-table td {
  padding: 16px;
  font-size: 14px;
  color: #374151;
}

/* Logo cell */
.logo-cell {
  display: flex;
  align-items: center;
  justify-content: center;
}

.logo-img {
  width: 40px;
  height: 40px;
  object-fit: contain;
  border-radius: 6px;
}

.logo-placeholder {
  width: 40px;
  height: 40px;
  background: #f3f4f6;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #9ca3af;
}

/* User info */
.user-info {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.user-info strong {
  font-weight: 600;
  color: #0f172a;
}

.user-function {
  font-size: 12px;
  color: #6b7280;
}

/* Company info */
.company-info {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.company-info strong {
  font-weight: 600;
  color: #0f172a;
}

.company-meta {
  font-size: 12px;
  color: #6b7280;
}

/* Email */
.email-text {
  color: #3b82f6;
  font-size: 13px;
}

/* Country badge */
.country-badge {
  display: inline-block;
  padding: 4px 10px;
  background: #f3f4f6;
  border-radius: 6px;
  font-size: 13px;
  font-weight: 500;
  color: #374151;
}

/* Sector */
.sector-text {
  font-size: 13px;
  color: #6b7280;
}

/* Status badge */
.status-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 600;
}

.status-dot {
  width: 6px;
  height: 6px;
  border-radius: 999px;
}

.status-pending {
  background: #fef3c7;
  color: #92400e;
}

.status-pending .status-dot {
  background: #f59e0b;
}

.status-approved {
  background: #dcfce7;
  color: #14532d;
}

.status-approved .status-dot {
  background: #22c55e;
}

.status-rejected {
  background: #fee2e2;
  color: #7f1d1d;
}

.status-rejected .status-dot {
  background: #ef4444;
}

/* Action buttons */
.action-column {
  width: 140px;
}

.action-buttons {
  display: flex;
  gap: 8px;
}

.action-btn {
  width: 32px;
  height: 32px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}

.action-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.approve-btn {
  background: #dcfce7;
  color: #16a34a;
}

.approve-btn:hover:not(:disabled) {
  background: #bbf7d0;
  transform: translateY(-1px);
}

.reject-btn {
  background: #fee2e2;
  color: #dc2626;
}

.reject-btn:hover:not(:disabled) {
  background: #fecaca;
  transform: translateY(-1px);
}

.info-btn {
  background: #dbeafe;
  color: #2563eb;
}

.info-btn:hover {
  background: #bfdbfe;
  transform: translateY(-1px);
}

/* Empty state */
.empty-state {
  text-align: center;
  padding: 80px 20px;
  color: #6b7280;
}

.empty-state svg {
  margin: 0 auto 24px;
  opacity: 0.3;
}

.empty-state h3 {
  font-size: 18px;
  font-weight: 600;
  color: #374151;
  margin-bottom: 8px;
}

.empty-state p {
  font-size: 14px;
}

/* Modal */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 20px;
}

.modal-content {
  background: white;
  border-radius: 16px;
  max-width: 800px;
  width: 100%;
  max-height: 90vh;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 24px 32px;
  border-bottom: 1px solid #e5e7eb;
}

.modal-header h2 {
  font-size: 20px;
  font-weight: 600;
  color: #0f172a;
}

.modal-close {
  border: none;
  background: none;
  font-size: 32px;
  cursor: pointer;
  color: #9ca3af;
  line-height: 1;
  transition: color 0.2s;
}

.modal-close:hover {
  color: #374151;
}

.modal-body {
  padding: 32px;
  overflow-y: auto;
}

.detail-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 24px;
}

.detail-item {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.detail-item label {
  font-size: 13px;
  font-weight: 600;
  color: #6b7280;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.detail-item p {
  font-size: 15px;
  color: #0f172a;
  font-weight: 500;
}

/* Modal animations */
.modal-enter-active, .modal-leave-active {
  transition: opacity 0.3s;
}

.modal-enter-from, .modal-leave-to {
  opacity: 0;
}

.modal-enter-active .modal-content,
.modal-leave-active .modal-content {
  transition: transform 0.3s;
}

.modal-enter-from .modal-content,
.modal-leave-to .modal-content {
  transform: scale(0.9);
}

/* Responsive */
@media (max-width: 1200px) {
  .stats-container {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 768px) {
  .admin-header {
    flex-direction: column;
    gap: 16px;
    padding: 16px 20px;
  }

  .admin-header-left {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
  }

  .header-divider {
    display: none;
  }

  .admin-header-right {
    width: 100%;
    justify-content: space-between;
  }

  .stats-container {
    grid-template-columns: 1fr;
    padding: 16px 20px;
  }

  .admin-content {
    padding: 0 20px 20px;
  }

  .toolbar {
    flex-direction: column;
    align-items: stretch;
  }

  .filters {
    flex-direction: column;
  }

  .table-container {
    overflow-x: auto;
  }

  .user-table {
    min-width: 900px;
  }

  .detail-grid {
    grid-template-columns: 1fr;
  }

  .modal-content {
    max-width: 100%;
  }
}</style>
