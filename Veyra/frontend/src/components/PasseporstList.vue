<!-- views/PassportsView.vue -->
<template>
  <UserLayout pageTitle="Passports" pageSubtitle="Gérez vos passeports produits">
    <div class="wrap">

      <!-- Header -->
      <div class="top">
        <div>
          <h1 class="title">Digital Product Passports</h1>
          <p class="subtitle">Manage your EU-compliant product passports</p>
        </div>

        <button class="cta" @click="$router.push('/user/passports/createpasseport')">
          <span class="cta-icon">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
              <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
          </span>
          Create New Passport
        </button>
      </div>

      <!-- KPI cards (style Veyra) -->
      <div class="kpis">
        <div class="kpi-card">
          <div class="kpi-ic neutral">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
              <polyline points="14 2 14 8 20 8"/>
            </svg>
          </div>
          <div class="kpi-txt">
            <div class="kpi-val">{{ kpi.total }}</div>
            <div class="kpi-lab">Total Passports</div>
          </div>
        </div>

        <div class="kpi-card">
          <div class="kpi-ic green">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
              <polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
          </div>
          <div class="kpi-txt">
            <div class="kpi-val">{{ kpi.published }}</div>
            <div class="kpi-lab">Published</div>
          </div>
        </div>

        <div class="kpi-card">
          <div class="kpi-ic amber">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"/>
              <polyline points="12 6 12 12 16 14"/>
            </svg>
          </div>
          <div class="kpi-txt">
            <div class="kpi-val">{{ kpi.inProgress }}</div>
            <div class="kpi-lab">In Progress</div>
          </div>
        </div>

        <div class="kpi-card">
          <div class="kpi-ic neutral">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
              <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
          </div>
          <div class="kpi-txt">
            <div class="kpi-val">{{ kpi.drafts }}</div>
            <div class="kpi-lab">Drafts</div>
          </div>
        </div>
      </div>

      <!-- Search + Filter bar (style Veyra) -->
      <div class="controls">
        <div class="search-wide">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
          </svg>
          <input v-model="search" placeholder="Search passports..." />
        </div>

        <div class="filter-wrap">
          <div class="filter-ic">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M22 3H2l8 9v7l4 2v-9l8-9z"/>
            </svg>
          </div>
          <select v-model="filterStatus" class="filter-select">
            <option value="">All Status</option>
            <option value="published">Published</option>
            <option value="in_progress">In Progress</option>
            <option value="draft">Draft</option>
          </select>
        </div>
      </div>

      <!-- Panel/Table -->
      <div class="panel">

        <!-- Loading -->
        <div v-if="loading" class="table-state">
          <div class="spinner"></div>
          <span>Loading...</span>
        </div>

        <!-- Error -->
        <div v-else-if="error" class="table-state error-state">
          <p>{{ error }}</p>
          <button class="btn-retry" @click="fetchPassports">Retry</button>
        </div>

        <!-- Empty -->
        <div v-else-if="!filteredPassports.length" class="table-state">
          <p>{{ passports.length ? "No results for this search" : "No passport yet" }}</p>
          <button v-if="!passports.length" class="btn-create-empty" @click="$router.push('/user/passports/createpasseport')">
            Create your first passport
          </button>
        </div>

        <!-- Table -->
        <div v-else class="table-wrap">
          <div class="thead">
            <span>PRODUCT</span>
            <span>ITEM CODE</span>
            <span>STATUS</span>
            <span>CREATED</span>
            <span>PROGRESS</span>
            <span class="th-actions">ACTIONS</span>
          </div>

          <div v-for="p in filteredPassports" :key="p.id" class="trow">
            <!-- PRODUCT -->
            <div class="product">
              <div class="thumb">
                <img v-if="p.product_image" :src="p.product_image" :alt="p.product_name" />
                <svg v-else width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                  <rect x="3" y="3" width="18" height="18" rx="2"/>
                  <circle cx="8.5" cy="8.5" r="1.5"/>
                  <polyline points="21 15 16 10 5 21"/>
                </svg>
              </div>

              <div class="pinfo">
                <div class="pname">{{ p.product_name || "—" }}</div>
                <div class="pcat">{{ p.category || "No category" }}</div>
              </div>
            </div>

            <!-- ITEM CODE -->
            <div class="code-pill">
              {{ p.item_code ? ("DPP-" + p.item_code) : "—" }}
            </div>

            <!-- STATUS -->
            <div>
              <span class="status" :class="'st-' + getPassportStatus(p)">
                {{ statusLabel(getPassportStatus(p)) }}
              </span>
            </div>

            <!-- CREATED -->
            <div class="created">{{ formatDate(p.created_at) }}</div>

            <!-- PROGRESS -->
            <div class="progress">
              <div class="bar">
                <div class="fill" :style="{ width: progressPercent(p) + '%' }"></div>
              </div>
              <span class="pct">{{ completedSteps(p) }}/13</span>
            </div>

            <!-- ACTIONS -->
            <div class="actions">
              <button class="dots" @click.stop="toggleMenu(p.id)">
                ⋮
              </button>

              <div v-if="openMenu === p.id" class="dropdown" @click.stop>
                <button class="dd-item" @click="goView(p)">
                  <span class="ico">👁️</span> View
                </button>
                <button class="dd-item" @click="goEdit(p)">
                  <span class="ico">✏️</span> Edit
                </button>
                <button class="dd-item" @click="goPublic(p)">
                  <span class="ico">🌐</span> View Public
                </button>
                <div class="dd-sep"></div>
                <button class="dd-item danger" @click="deletePassport(p)">
                  <span class="ico">🗑️</span> Delete
                </button>
              </div>
            </div>
          </div>

        </div>
      </div>

    </div>
  </UserLayout>
</template>

<script setup>
import { computed, onMounted, onUnmounted, ref } from "vue";
import axios from "axios";
import UserLayout from "@/components/UserLayout.vue";

const passports = ref([]);
const loading = ref(false);
const error = ref("");

const search = ref("");
const filterStatus = ref("");
const openMenu = ref(null);

function authHeaders() {
  const token = localStorage.getItem("token");
  return { Authorization: token ? `Bearer ${token}` : "", Accept: "application/json" };
}

function getPassportStatus(p) {
  // tu as déjà plusieurs noms possibles
  const s = (p?.status || p?.passport_status || "draft");
  return s; // attendu: published | in_progress | draft
}

function statusLabel(s) {
  if (s === "published") return "Published";
  if (s === "in_progress") return "In Progress";
  return "Draft";
}

function formatDate(d) {
  if (!d) return "—";
  return new Date(d).toLocaleDateString("en-US", { month: "short", day: "numeric", year: "numeric" });
}

/* ✅ Progress:
   - si ton backend renvoie un champ du genre completed_steps -> on l'utilise
   - sinon ça reste 0
*/
function completedSteps(p) {
  const v =
    p?.completed_steps ??
    p?.completedSteps ??
    p?.progress_steps ??
    0;
  return Math.max(0, Math.min(13, Number(v) || 0));
}

function progressPercent(p) {
  return Math.round((completedSteps(p) / 13) * 100);
}

async function fetchPassports() {
  loading.value = true;
  error.value = "";
  try {
    const res = await axios.get("http://127.0.0.1:8000/api/products", { headers: authHeaders() });
    passports.value = res.data?.data || [];
  } catch (e) {
    error.value = e?.response?.data?.message || "Failed to load passports";
  } finally {
    loading.value = false;
  }
}

const filteredPassports = computed(() => {
  const q = search.value.trim().toLowerCase();
  return passports.value.filter(p => {
    const name = (p.product_name || "").toLowerCase();
    const code = (p.item_code || "").toLowerCase();
    const matchSearch = !q || name.includes(q) || code.includes(q);

    const s = getPassportStatus(p);
    const matchStatus = !filterStatus.value || s === filterStatus.value;

    return matchSearch && matchStatus;
  });
});

const kpi = computed(() => ({
  total: passports.value.length,
  published: passports.value.filter(p => getPassportStatus(p) === "published").length,
  inProgress: passports.value.filter(p => getPassportStatus(p) === "in_progress").length,
  drafts: passports.value.filter(p => getPassportStatus(p) === "draft").length,
}));

function toggleMenu(id) {
  openMenu.value = openMenu.value === id ? null : id;
}

function handleOutsideClick() {
  openMenu.value = null;
}

onMounted(() => {
  fetchPassports();
  document.addEventListener("click", handleOutsideClick);
});
onUnmounted(() => document.removeEventListener("click", handleOutsideClick));

/* ACTIONS (routes à adapter si besoin) */
function goView(p) {
  openMenu.value = null;
  // ton ancien view : /user/passports/:id
  window.location.href = `/user/passports/${p.id}`;
}

function goEdit(p) {
  openMenu.value = null;
  window.location.href = `/user/passports/${p.id}/edit`;
}

function goPublic(p) {
  openMenu.value = null;
  // adapte à ta route public (ex: /public/passports/:id ou /passport/:item_code)
  window.open(`/public/passports/${p.id}`, "_blank");
}

async function deletePassport(p) {
  openMenu.value = null;
  if (!confirm("Delete this passport?")) return;

  try {
    // ⚠️ adapte à ton endpoint delete
    await axios.delete(`http://127.0.0.1:8000/api/products/${p.id}`, { headers: authHeaders() });
    passports.value = passports.value.filter(x => x.id !== p.id);
  } catch (e) {
    alert(e?.response?.data?.message || "Delete failed");
  }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap');
* { box-sizing: border-box; font-family: 'DM Sans', sans-serif; }

.wrap { padding: 6px 0; }

/* Header */
.top{
  display:flex; justify-content:space-between; align-items:flex-start;
  gap:16px; margin-bottom:18px;
}
.title{ margin:0; font-size:24px; font-weight:800; color:#0f172a; }
.subtitle{ margin:6px 0 0; font-size:13px; color:#64748b; }

.cta{
  display:flex; align-items:center; gap:10px;
  background:#0f766e; color:#fff; border:none;
  padding:10px 14px; border-radius:10px;
  font-weight:700; cursor:pointer;
}
.cta:hover{ filter:brightness(0.97); }
.cta-icon{
  width:26px; height:26px; border-radius:8px;
  background:rgba(255,255,255,.18);
  display:flex; align-items:center; justify-content:center;
}

/* KPI (comme Veyra) */
.kpis{
  display:grid;
  grid-template-columns: repeat(4, 1fr);
  gap:14px;
  margin: 12px 0 16px;
}
.kpi-card{
  background:#fff;
  border:1px solid #e5e7eb;
  border-radius:12px;
  padding:14px 16px;
  display:flex; align-items:center; gap:12px;
}
.kpi-ic{
  width:40px; height:40px; border-radius:12px;
  display:flex; align-items:center; justify-content:center;
  border:1px solid #e5e7eb;
}
.kpi-ic.neutral{ background:#f8fafc; color:#334155; }
.kpi-ic.green{ background:#ecfdf5; color:#059669; border-color:#bbf7d0; }
.kpi-ic.amber{ background:#fffbeb; color:#d97706; border-color:#fde68a; }

.kpi-val{ font-size:22px; font-weight:800; color:#0f172a; line-height:1; }
.kpi-lab{ margin-top:4px; font-size:12px; color:#64748b; font-weight:600; }

/* Controls bar */
.controls{
  display:flex; align-items:center; gap:12px;
  margin-bottom: 14px;
}
.search-wide{
  flex:1;
  background:#fff;
  border:1px solid #e5e7eb;
  border-radius:12px;
  padding:10px 12px;
  display:flex; align-items:center; gap:10px;
  color:#94a3b8;
}
.search-wide input{
  border:none; outline:none; width:100%;
  font-size:13px; color:#0f172a;
}
.filter-wrap{
  display:flex; align-items:center;
  background:#fff;
  border:1px solid #e5e7eb;
  border-radius:12px;
  overflow:hidden;
}
.filter-ic{
  width:42px; height:42px;
  display:flex; align-items:center; justify-content:center;
  color:#64748b; background:#f8fafc;
  border-right:1px solid #e5e7eb;
}
.filter-select{
  height:42px;
  border:none; outline:none;
  padding:0 12px;
  font-weight:700; color:#0f172a;
  background:#fff;
  cursor:pointer;
}

/* Panel + Table */
.panel{
  background:#fff;
  border:1px solid #e5e7eb;
  border-radius:14px;
  overflow:hidden;
}
.table-wrap{ overflow-x:auto; }
.thead{
  display:grid;
  grid-template-columns: 2.2fr 1.2fr 1fr 1fr 1.2fr 70px;
  gap:12px;
  padding:14px 18px;
  background:#f8fafc;
  border-bottom:1px solid #e5e7eb;
}
.thead span{
  font-size:11px;
  font-weight:800;
  letter-spacing:.06em;
  color:#64748b;
}
.th-actions{ text-align:right; }

.trow{
  display:grid;
  grid-template-columns: 2.2fr 1.2fr 1fr 1fr 1.2fr 70px;
  gap:12px;
  padding:14px 18px;
  align-items:center;
  border-bottom:1px solid #f1f5f9;
}
.trow:hover{ background:#fbfdff; }
.trow:last-child{ border-bottom:none; }

/* Product cell */
.product{ display:flex; align-items:center; gap:12px; min-width:0; }
.thumb{
  width:40px; height:40px; border-radius:12px;
  background:#f1f5f9; border:1px solid #e5e7eb;
  display:flex; align-items:center; justify-content:center;
  overflow:hidden; color:#94a3b8;
  flex-shrink:0;
}
.thumb img{ width:100%; height:100%; object-fit:cover; }

.pinfo{ min-width:0; }
.pname{
  font-size:13px; font-weight:800; color:#0f172a;
  white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
}
.pcat{
  font-size:12px; color:#64748b;
  white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
}

/* Code pill */
.code-pill{
  justify-self:start;
  font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
  font-size:12px;
  padding:7px 10px;
  background:#f1f5f9;
  border:1px solid #e5e7eb;
  border-radius:10px;
  color:#0f172a;
  font-weight:700;
  width:fit-content;
}

/* Status */
.status{
  display:inline-flex;
  padding:6px 10px;
  border-radius:999px;
  font-size:12px;
  font-weight:800;
  width:fit-content;
}
.st-published{ background:#dcfce7; color:#15803d; }
.st-in_progress{ background:#fef3c7; color:#b45309; }
.st-draft{ background:#f1f5f9; color:#334155; }

.created{
  font-size:12px; color:#334155; font-weight:700;
}

/* Progress */
.progress{ display:flex; align-items:center; gap:10px; }
.bar{
  width:130px; height:8px;
  background:#e2e8f0;
  border-radius:999px;
  overflow:hidden;
}
.fill{
  height:100%;
  background:#14b8a6;
  border-radius:999px;
}
.pct{
  font-size:12px;
  font-weight:800;
  color:#334155;
}

/* Actions dropdown */
.actions{ position:relative; justify-self:end; }
.dots{
  width:38px; height:38px;
  border-radius:12px;
  border:1px solid #e5e7eb;
  background:#fff;
  cursor:pointer;
  font-size:18px;
  color:#334155;
}
.dots:hover{ background:#f8fafc; }

.dropdown{
  position:absolute;
  right:0; top:44px;
  width:180px;
  background:#fff;
  border:1px solid #e5e7eb;
  border-radius:12px;
  box-shadow: 0 12px 30px rgba(15,23,42,.12);
  padding:8px;
  z-index:50;
}
.dd-item{
  width:100%;
  display:flex;
  align-items:center;
  gap:10px;
  padding:10px 10px;
  border:none;
  background:transparent;
  cursor:pointer;
  border-radius:10px;
  font-weight:800;
  color:#0f172a;
  text-align:left;
  font-size:13px;
}
.dd-item:hover{ background:#f8fafc; }
.dd-sep{ height:1px; background:#e5e7eb; margin:6px 0; }
.dd-item.danger{ color:#dc2626; }
.dd-item.danger:hover{ background:#fef2f2; }

.table-state{
  display:flex; flex-direction:column; align-items:center; justify-content:center;
  padding:60px 20px; gap:12px; color:#64748b;
}
.error-state{ color:#dc2626; }
.spinner{
  width:28px; height:28px;
  border:2px solid #e5e7eb;
  border-top-color:#14b8a6;
  border-radius:50%;
  animation:spin .7s linear infinite;
}
@keyframes spin{ to{ transform:rotate(360deg);} }
.btn-retry,.btn-create-empty{
  height:40px; padding:0 14px; border-radius:12px;
  font-weight:800; cursor:pointer; border:1px solid #e5e7eb; background:#fff;
}
.btn-create-empty{ background:#0f766e; color:#fff; border:none; }

/* Responsive */
@media (max-width: 1100px){
  .kpis{ grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 900px){
  .thead, .trow{
    grid-template-columns: 2fr 1fr 1fr 70px;
  }
  .created, .progress{ display:none; }
  .controls{ flex-direction:column; align-items:stretch; }
  .filter-wrap{ width:100%; }
}
@media (max-width: 650px){
  .top{ flex-direction:column; align-items:stretch; }
  .cta{ justify-content:center; }
}
</style>
