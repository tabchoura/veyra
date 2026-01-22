<!-- views/Dashboard.vue -->
<template>
  <AdminLayout
    pageTitle="Admin Dashboard"
    pageSubtitle="Overview of your Veyra platform"
  >
    <!-- Error -->
    <div v-if="error" class="error-banner">
      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="10"></circle>
        <line x1="12" y1="8" x2="12" y2="12"></line>
        <line x1="12" y1="16" x2="12.01" y2="16"></line>
      </svg>
      <span>{{ error }}</span>
    </div>

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
          <p class="stat-label">Total Users</p>
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
          <p class="stat-label">Pending Approval</p>
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
          <p class="stat-label">Approved</p>
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
          <p class="stat-label">Rejected</p>
          <p class="stat-value">{{ stats.rejected }}</p>
        </div>
      </div>
    </div>

    <!-- User Status & Recent Activity -->
    <div class="dashboard-grid">
      <div class="card">
        <h3 class="card-title">User Status</h3>

        <div v-if="loading" class="loading-line">Loading stats...</div>

        <div v-else class="status-list">
          <div class="status-item">
            <div class="status-item-left">
              <div class="status-dot status-dot-green"></div>
              <span>Approved Users</span>
            </div>
            <span class="status-value">{{ stats.approved }}</span>
          </div>

          <div class="status-item">
            <div class="status-item-left">
              <div class="status-dot status-dot-orange"></div>
              <span>Pending Approval</span>
            </div>
            <span class="status-value">{{ stats.pending }}</span>
          </div>

          <div class="status-item">
            <div class="status-item-left">
              <div class="status-dot status-dot-red"></div>
              <span>Rejected Users</span>
            </div>
            <span class="status-value">{{ stats.rejected }}</span>
          </div>
        </div>

        <button class="btn-outline" @click="goUsers">Manage Users</button>
      </div>

      <div class="card">
        <h3 class="card-title">Recent Activity</h3>
        <p class="muted">Connect this to Audit Logs when your backend endpoint is ready.</p>

        <div class="activity-list">
          <div class="activity-item">
            <div class="activity-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
              </svg>
            </div>
            <div class="activity-content">
              <p class="activity-title">Example activity</p>
              <p class="activity-meta">—</p>
            </div>
          </div>
        </div>

        <button class="btn-outline" @click="goLogs">View All Logs</button>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRouter } from "vue-router";
import AdminLayout from "@/components/AdminLayout.vue";
import api from "@/services/api";

const router = useRouter();

const users = ref([]);
const loading = ref(false);
const error = ref("");

const token = localStorage.getItem("token");

// ✅ évite /api/api si baseURL finit déjà par /api
const apiPrefix = (api.defaults.baseURL || "").endsWith("/api") ? "" : "/api";

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

    // ✅ ancien backend: res.data.data
    users.value = res.data.data || [];
  } catch (e) {
    console.error(e);
    error.value =
      e.response?.data?.message || "Erreur lors du chargement des statistiques.";
  } finally {
    loading.value = false;
  }
};

const stats = computed(() => ({
  total: users.value.length,
  pending: users.value.filter((u) => u.status === "pending").length,
  approved: users.value.filter((u) => u.status === "approved").length,
  rejected: users.value.filter((u) => u.status === "rejected").length,
}));

const goUsers = () => {
  router.push("/users"); // adapte si ta route est différente
};

const goLogs = () => {
  router.push("/audit-logs"); // adapte si ta route est différente
};

onMounted(fetchUsers);
</script>

<style scoped>
/* Small error banner */
.error-banner {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 16px;
  padding: 12px 14px;
  border-radius: 12px;
  background: #fee2e2;
  border: 1px solid #fecaca;
  color: #991b1b;
  font-size: 14px;
}

.loading-line {
  color: #64748b;
  font-size: 14px;
  margin-bottom: 10px;
}

.muted {
  color: #64748b;
  font-size: 13px;
  margin-bottom: 12px;
}

/* ====== your existing styles ====== */
.stats-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 24px;
  margin-bottom: 32px;
}

.stat-card {
  background: white;
  padding: 24px;
  border-radius: 16px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
  display: flex;
  align-items: center;
  gap: 16px;
  transition: all 0.2s;
}

.stat-card:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  transform: translateY(-2px);
}

.stat-icon {
  width: 56px;
  height: 56px;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.stat-icon-total {
  background: linear-gradient(135deg, #dbeafe, #bfdbfe);
  color: #2563eb;
}

.stat-icon-pending {
  background: linear-gradient(135deg, #fef3c7, #fde68a);
  color: #d97706;
}

.stat-icon-approved {
  background: linear-gradient(135deg, #dcfce7, #bbf7d0);
  color: #16a34a;
}

.stat-icon-rejected {
  background: linear-gradient(135deg, #fee2e2, #fecaca);
  color: #dc2626;
}

.stat-content {
  flex: 1;
}

.stat-label {
  font-size: 13px;
  color: #64748b;
  margin-bottom: 6px;
  font-weight: 500;
}

.stat-value {
  font-size: 32px;
  font-weight: 700;
  color: #0f172a;
}

.dashboard-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
  gap: 24px;
}

.card {
  background: white;
  padding: 28px;
  border-radius: 16px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.card-title {
  font-size: 18px;
  font-weight: 700;
  color: #0d3b66;
  margin-bottom: 20px;
}

.status-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
  margin-bottom: 24px;
}

.status-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 16px;
  background: #f8fafc;
  border-radius: 10px;
}

.status-item-left {
  display: flex;
  align-items: center;
  gap: 12px;
  font-size: 14px;
  font-weight: 500;
  color: #334155;
}

.status-dot {
  width: 10px;
  height: 10px;
  border-radius: 999px;
  flex-shrink: 0;
}

.status-dot-green {
  background: #22c55e;
}

.status-dot-orange {
  background: #f59e0b;
}

.status-dot-red {
  background: #ef4444;
}

.status-value {
  font-size: 20px;
  font-weight: 700;
  color: #0f172a;
}

.activity-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
  margin-bottom: 24px;
}

.activity-item {
  display: flex;
  gap: 12px;
  padding: 14px 16px;
  background: #f8fafc;
  border-radius: 10px;
}

.activity-icon {
  width: 36px;
  height: 36px;
  background: white;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #64748b;
  flex-shrink: 0;
}

.activity-content {
  flex: 1;
}

.activity-title {
  font-size: 14px;
  font-weight: 500;
  color: #0f172a;
  margin-bottom: 4px;
}

.activity-meta {
  font-size: 12px;
  color: #64748b;
}

.btn-outline {
  width: 100%;
  padding: 12px 20px;
  border: 2px solid #e5e7eb;
  background: none;
  border-radius: 10px;
  font-weight: 600;
  color: #0d3b66;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-outline:hover {
  border-color: #3498db;
  color: #3498db;
}
</style>
