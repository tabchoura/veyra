<template>
  <div class="admin-wrapper">
    <!-- Header -->
    <header class="admin-header">
      <div class="admin-header-left">
        <h1>Dashboard administrateur</h1>
        <p class="subtitle">Liste des clients inscrits sur Veyra</p>
      </div>

      <div class="admin-header-right">
        <span class="admin-badge">Admin connecté</span>
        <button class="logout-btn" @click="logout">
          Se déconnecter
        </button>
      </div>
    </header>

    <!-- Contenu -->
    <main class="admin-content">
      <div v-if="loading" class="info">Chargement des utilisateurs...</div>
      <div v-if="error" class="error">{{ error }}</div>

      <table v-if="!loading && users.length" class="user-table">
        <thead>
          <tr>
            <th>Logo</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Entreprise</th>
            <th>Email</th>
            <th>Num TVA</th>
            <th>Pays</th>
            <th>Fonction</th>
            <th>Adresse 1</th>
            <th>Adresse 2</th>
            <th>Code postal</th>
            <th>Secteur</th>
            <th>Partenaire</th>
            <th>Statut</th>
            <th style="width: 170px;">Actions</th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="user in users" :key="user.id">
            <!-- Logo -->
            <td>
              <div class="logo-cell">
                <img
                  v-if="user.logo_url"
                  :src="user.logo_url"
                  alt="Logo entreprise"
                  class="logo-img"
                />
                <span v-else class="logo-placeholder">—</span>
              </div>
            </td>

            <!-- Nom / Prénom -->
            <td>{{ user.last_name || '-' }}</td>
            <td>{{ user.first_name || '-' }}</td>

            <!-- Entreprise -->
            <td>
              <strong>{{ user.company_name || '-' }}</strong>
            </td>

            <!-- Email -->
            <td>{{ user.email }}</td>

            <!-- TVA -->
            <td>{{ user.tva_number || '-' }}</td>

            <!-- Pays -->
            <td>{{ user.country || '-' }}</td>

            <!-- Fonction -->
            <td>{{ user.function || '-' }}</td>

            <!-- Adresse -->
            <td>{{ user.address1 || '-' }}</td>
            <td>{{ user.address2 || '-' }}</td>
            <td>{{ user.postal_code || '-' }}</td>

            <!-- Secteur -->
            <td>
              <span v-if="user.sector === 'Autres'">
                {{ user.sector_other || user.sector }}
              </span>
              <span v-else>
                {{ user.sector }}
              </span>
            </td>

            <!-- Partenaire -->
            <td>
              <span v-if="user.partner === 'Autre'">
                {{ user.partner_other || user.partner }}
              </span>
              <span v-else>
                {{ user.partner || '-' }}
              </span>
            </td>

            <!-- Statut -->
            <td>
              <span :class="['badge', 'status-' + user.status]">
                {{ formatStatus(user.status) }}
              </span>
            </td>

            <!-- Actions -->
            <td class="action-cell">
              <button
                class="btn approve"
                @click="approveUser(user)"
                :disabled="user.status === 'approved' || loadingActionId === user.id"
              >
                Approuver
              </button>
              <br>
              <button
                class="btn reject"
                @click="rejectUser(user)"
                :disabled="user.status === 'rejected' || loadingActionId === user.id"
              >
                Refuser
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <div v-if="!loading && !users.length" class="info">
        Aucun client trouvé pour le moment.
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";

const router = useRouter();

const users = ref([]);
const loading = ref(false);
const error = ref("");
const loadingActionId = ref(null);

// On récupère le token de l'admin connecté
const token = localStorage.getItem("token");

// Optionnel si pas déjà set globalement
axios.defaults.baseURL = "http://127.0.0.1:8000";

const fetchUsers = async () => {
  loading.value = true;
  error.value = "";

  if (!token) {
    error.value = "Token admin manquant. Veuillez vous reconnecter.";
    loading.value = false;
    return;
  }

  try {
    const res = await axios.get("/api/admin/users", {
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: "application/json",
      },
    });

    // Backend : return response()->json(['data' => $users]);
    users.value = res.data.data || [];
  } catch (e) {
    console.error(e);
    error.value =
      e.response?.data?.message ||
      "Erreur lors du chargement des utilisateurs.";
  } finally {
    loading.value = false;
  }
};

const approveUser = async (user) => {
  if (!token) return;
  loadingActionId.value = user.id;
  error.value = "";

  try {
    const res = await axios.patch(
      `/api/admin/users/${user.id}/approve`,
      {},
      {
        headers: {
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
        },
      }
    );

    // Mise à jour du user local
    user.status = res.data.user.status;
    user.email_verified = res.data.user.email_verified;
  } catch (e) {
    console.error(e);
    error.value =
      e.response?.data?.message ||
      "Erreur lors de l'approbation de l'utilisateur.";
  } finally {
    loadingActionId.value = null;
  }
};

const rejectUser = async (user) => {
  if (!token) return;
  loadingActionId.value = user.id;
  error.value = "";

  try {
    const res = await axios.patch(
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
    error.value =
      e.response?.data?.message ||
      "Erreur lors du refus de l'utilisateur.";
  } finally {
    loadingActionId.value = null;
  }
};

const formatStatus = (status) => {
  if (status === "approved") return "Approuvé";
  if (status === "rejected") return "Refusé";
  return "En attente";
};

const logout = () => {
  localStorage.removeItem("token");
  localStorage.removeItem("user");
  // adapte ce path à ta route de login admin
  router.push("/login/admin");
};

onMounted(() => {
  fetchUsers();
});
</script>

<style scoped>
.admin-wrapper {
  min-height: 100vh;
  background: #f8fafc;
  font-family: "Inter", sans-serif;
}

.admin-header {
  background: white;
  padding: 18px 40px;
  border-bottom: 1px solid #e5e7eb;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.admin-header-left h1 {
  margin: 0;
  font-size: 22px;
  font-weight: 600;
  color: #0f172a;
}

.subtitle {
  margin: 2px 0 0;
  font-size: 13px;
  color: #6b7280;
}

.admin-header-right {
  display: flex;
  align-items: center;
  gap: 10px;
}

.admin-badge {
  font-size: 12px;
  padding: 4px 10px;
  border-radius: 999px;
  background: #e0f2fe;
  color: #0369a1;
}

.logout-btn {
  border: none;
  background: #ef4444;
  color: white;
  font-size: 13px;
  padding: 6px 12px;
  border-radius: 999px;
  cursor: pointer;
  font-weight: 500;
}

.logout-btn:hover {
  background: #dc2626;
}

.admin-content {
  padding: 24px 40px;
}

/* Table */
.user-table {
  width: 100%;
  border-collapse: collapse;
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 10px rgba(15, 23, 42, 0.05);
}

.user-table th,
.user-table td {
  padding: 10px 16px;
  font-size: 14px;
  border-bottom: 1px solid #e5e7eb;
  vertical-align: middle;
}

.user-table th {
  background: #f9fafb;
  text-align: left;
  color: #6b7280;
  font-weight: 600;
}

.user-table tr:last-child td {
  border-bottom: none;
}

.action-cell {
  display: flex;
  gap: 8px;
}

/* Logo */
.logo-cell {
  display: flex;
  align-items: center;
  justify-content: center;
}

.logo-img {
  width: 36px;
  height: 36px;
  object-fit: contain;
  border-radius: 6px;
  border: 1px solid #e5e7eb;
  background: #f9fafb;
}

.logo-placeholder {
  font-size: 18px;
  color: #9ca3af;
}

/* Boutons */
.btn {
  border: none;
  padding: 6px 12px;
  border-radius: 999px;
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.btn.approve {
  background: #22c55e;
  color: white;
}

.btn.approve:hover {
  background: #16a34a;
}

.btn.reject {
  background: #ef4444;
  color: white;
}

.btn.reject:hover {
  background: #dc2626;
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Badges statut */
.badge {
  padding: 4px 10px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 500;
}

.status-pending {
  background: #fef3c7;
  color: #92400e;
}

.status-approved {
  background: #dcfce7;
  color: #15803d;
}

.status-rejected {
  background: #fee2e2;
  color: #b91c1c;
}

/* Messages */
.info {
  color: #6b7280;
  font-size: 14px;
}

.error {
  color: #dc2626;
  margin-bottom: 10px;
  font-size: 14px;
}
</style>
