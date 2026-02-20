<template>
  <div class="step">
    <div class="step-head">
      <h2 class="step-title">Yarn Information</h2>
      <p class="step-subtitle">Define the yarn specifications used in your product</p>
    </div>

    <div class="divider"></div>

    <!-- ALERT 403 -->
    <div v-if="blocked" class="blocked">
      <div class="blocked-title">Access blocked</div>
      <div class="blocked-text">{{ blockedMessage }}</div>
    </div>

    <template v-else>
      <!-- Total banner (only when at least 1 yarn exists) -->
      <div v-if="yarns.length" class="total-banner" :class="{ ok: roundedTotal === 100 }">
        <div class="left">
          <span class="icon">!</span>
          <span>Total Composition: <strong>{{ totalPercentage.toFixed(1) }}%</strong></span>
        </div>
        <div class="right">Must equal 100%</div>
      </div>

      <!-- EMPTY -->
      <div v-if="!yarns.length" class="empty">
        <button class="add-zone" type="button" @click="addYarn" :disabled="loading">
          <span class="plus">+</span>
          <span>Add Yarn</span>
        </button>
      </div>

      <!-- LIST -->
      <div v-else class="list">
        <div v-for="(y, idx) in yarns" :key="y.localKey" class="card">
          <div class="card-top">
            <div class="card-title">Yarn #{{ idx + 1 }}</div>

            <button class="remove" type="button" @click="removeYarn(idx)" :disabled="loading || yarns.length <= 1">
              🗑 <span>Remove</span>
            </button>
          </div>

          <div class="grid">
            <div class="field">
              <label>Producing Country <span class="req">*</span></label>
              <select v-model.number="y.producing_country_id" :disabled="loading">
                <option :value="null" disabled>Select country</option>
                <option v-for="c in countries" :key="c.id" :value="c.id">
                  {{ c.name_en || c.name }}
                </option>
              </select>
            </div>

            <div class="field">
              <label>Yarn Type</label>
              <select v-model.number="y.yarn_type_id" :disabled="loading">
                <option :value="null" disabled>Select type</option>
                <option v-for="t in yarnTypes" :key="t.id" :value="t.id">
                  {{ t.name }}
                </option>
              </select>
            </div>

            <div class="field">
              <label>Percentage <span class="req">*</span></label>
              <input
                type="number"
                step="0.1"
                min="0"
                max="100"
                v-model="y.percentage"
                :disabled="loading"
                placeholder="0.0"
                @input="onPercentageInput(y)"
              />
            </div>
          </div>

          <div class="toggle-row">
            <div class="toggle-label">Has Certification?</div>
            <label class="switch">
              <input type="checkbox" v-model="y.has_certification" :disabled="loading" @change="normalizeRow(y)" />
              <span class="slider"></span>
            </label>
          </div>

          <div v-if="y.has_certification" class="grid">
            <div class="field">
              <label>Certificate Number <span class="req">*</span></label>
              <input v-model="y.certificate_number" :disabled="loading" />
            </div>
            <div class="field">
              <label>Validity Date <span class="req">*</span></label>
              <input type="date" v-model="y.validity_date" :disabled="loading" />
            </div>
          </div>

          <div class="toggle-row">
            <div class="toggle-label">Client Audit?</div>
            <label class="switch">
              <input type="checkbox" v-model="y.has_client_audit" :disabled="loading" @change="normalizeRow(y)" />
              <span class="slider"></span>
            </label>
          </div>

          <div v-if="y.has_client_audit" class="field">
            <label>Audit Comments <span class="req">*</span></label>
            <textarea rows="3" v-model="y.audit_comments" :disabled="loading"></textarea>
          </div>

          <div class="metrics">
            <div class="metrics-title">Environmental Metrics</div>

            <div class="metric">
              <div class="metric-row">
                <span>Renewable Energy</span>
                <span class="pct">{{ y.renewable_energy_percentage }}%</span>
              </div>
              <input type="range" min="0" max="100" v-model.number="y.renewable_energy_percentage" :disabled="loading" />
            </div>

            <div class="metric">
              <div class="metric-row">
                <span>Recycled Water</span>
                <span class="pct">{{ y.recycled_water_percentage }}%</span>
              </div>
              <input type="range" min="0" max="100" v-model.number="y.recycled_water_percentage" :disabled="loading" />
            </div>
          </div>
        </div>

        <button class="add-zone" type="button" @click="addYarn" style="width: 100%" :disabled="loading">
          <span class="plus">+</span>
          <span>Add Yarn</span>
        </button>
      </div>

      <!-- FOOTER BUTTONS ✅ same style as other steps -->
      <div class="footer">
        <button class="btn-secondary" type="button" @click="$emit('previous')" :disabled="loading">
          <span class="arrow">‹</span> Previous
        </button>

        <div class="right-btns">
          <button class="btn-light" type="button" @click="saveProgress" :disabled="loading || !yarns.length">
            Save Progress
          </button>

          <button class="btn-primary" type="button" @click="validateAndNext" :disabled="loading || !canNext">
            Next Step <span class="arrow">›</span>
          </button>
        </div>
      </div>
    </template>

    <div v-if="toast.show" class="toast" :class="toast.type">{{ toast.message }}</div>
  </div>
</template>

<script setup>
import { onMounted, ref, computed } from "vue";
import axios from "axios";

const props = defineProps({
  productId: { type: [Number, String], required: true },
});
const emit = defineEmits(["next", "previous", "update"]);

const API_BASE_URL = import.meta.env.VITE_API_URL || "http://localhost:8000/api";

const loading = ref(false);
const blocked = ref(false);
const blockedMessage = ref("");

const countries = ref([]);
const yarnTypes = ref([]);
const yarns = ref([]);

const toast = ref({ show: false, message: "", type: "success" });

function showToast(message, type = "success") {
  toast.value = { show: true, message, type };
  setTimeout(() => (toast.value.show = false), 2500);
}

function key() {
  return (globalThis.crypto?.randomUUID?.() || Math.random().toString(36).slice(2)) + Date.now();
}

function getToken() {
  return (
    localStorage.getItem("token") ||
    localStorage.getItem("auth_token") ||
    localStorage.getItem("access_token") ||
    localStorage.getItem("authToken") ||
    localStorage.getItem("accessToken")
  );
}

function authHeaders() {
  const token = getToken();
  return token ? { Authorization: `Bearer ${token}`, Accept: "application/json" } : { Accept: "application/json" };
}

const totalPercentage = computed(() => yarns.value.reduce((sum, y) => sum + (parseFloat(y.percentage) || 0), 0));
const roundedTotal = computed(() => Math.round(totalPercentage.value * 10) / 10);

const canNext = computed(() => {
  if (!yarns.value.length) return false;
  if (roundedTotal.value !== 100) return false;
  return yarns.value.every((y) => validateRow(y, true));
});

function normalizeRow(y) {
  if (!y.has_certification) {
    y.certificate_number = "";
    y.validity_date = "";
  }
  if (!y.has_client_audit) {
    y.audit_comments = "";
  }
}

function validateRow(y, silent = false) {
  normalizeRow(y);

  if (!y.producing_country_id) return false;

  const p = parseFloat(y.percentage);
  if (y.percentage === null || y.percentage === "" || Number.isNaN(p) || p <= 0) return false;
  if (p > 100) return false;

  if (y.has_certification) {
    if (!y.certificate_number?.trim()) return false;
    if (!y.validity_date) return false;
  }
  if (y.has_client_audit) {
    if (!y.audit_comments?.trim()) return false;
  }
  return true;
}

function onPercentageInput(y) {
  if (y.percentage === "" || y.percentage === null) return;

  let val = parseFloat(y.percentage);
  if (Number.isNaN(val)) val = 0;

  val = Math.max(0, Math.min(100, val));
  y.percentage = val;

  const total = totalPercentage.value;
  if (total > 100) {
    const over = total - 100;
    y.percentage = Math.max(0, val - over);
  }
}

/** ✅ CORRIGÉ: /yarn-types au lieu de /reference/yarn-types */
async function fetchRefs() {
  const results = await Promise.allSettled([
    axios.get(`${API_BASE_URL}/countries`, { headers: authHeaders() }),
    axios.get(`${API_BASE_URL}/yarn-types`, { headers: authHeaders() }),
  ]);

  if (results[0].status === "fulfilled") {
    countries.value = results[0].value.data?.data || [];
  } else {
    countries.value = [];
    showToast(results[0].reason?.response?.data?.message || "Failed to load countries", "error");
  }

  if (results[1].status === "fulfilled") {
    yarnTypes.value = results[1].value.data?.data || [];
  } else {
    yarnTypes.value = [];
    showToast(results[1].reason?.response?.data?.message || "Failed to load yarn types", "error");
  }
}

async function fetchYarns() {
  if (!props.productId || props.productId === "null" || props.productId === "undefined") {
    console.warn("⚠️ YarnInformation: productId manquant =", props.productId);
    blocked.value = true;
    blockedMessage.value = "Please complete Step 1 (Product Initialization) first to create a product.";
    loading.value = false;
    return;
  }

  loading.value = true;
  blocked.value = false;
  blockedMessage.value = "";

  try {
    console.log("✅ Fetching yarns for productId:", props.productId);
    const res = await axios.get(`${API_BASE_URL}/products/${props.productId}/yarns`, {
      headers: authHeaders(),
    });

    const data = res.data?.data || [];
    if (data.length) {
      yarns.value = data.map((y) => ({
        ...y,
        localKey: key(),
        id: y.id ?? null,
        producing_country_id: y.producing_country_id ?? null,
        yarn_type_id: y.yarn_type_id ?? null,
        percentage: y.percentage ?? null,
        renewable_energy_percentage: Number(y.renewable_energy_percentage ?? 0),
        recycled_water_percentage: Number(y.recycled_water_percentage ?? 0),
        has_certification: !!y.has_certification,
        certificate_number: y.certificate_number ?? "",
        validity_date: y.validity_date ?? "",
        has_client_audit: !!y.has_client_audit,
        audit_comments: y.audit_comments ?? "",
      }));
    } else {
      yarns.value = [];
    }
  } catch (err) {
    if (err?.response?.status === 403) {
      blocked.value = true;
      blockedMessage.value = err.response?.data?.message || "Forbidden";
    } else {
      showToast(err?.response?.data?.message || "Failed to load yarns", "error");
    }
  } finally {
    loading.value = false;
  }
}

function addYarn() {
  yarns.value.push({
    localKey: key(),
    id: null,
    producing_country_id: null,
    yarn_type_id: null,
    percentage: null,
    renewable_energy_percentage: 0,
    recycled_water_percentage: 0,

    producing_organization: "",
    address: "",
    postal_code: "",
    yarn_type: "",
    production_date: "",

    has_certification: false,
    certificate_number: "",
    validity_date: "",

    has_client_audit: false,
    audit_comments: "",
  });
}

async function removeYarn(idx) {
  yarns.value.splice(idx, 1);
  showToast("Removed locally (no DELETE route for yarns).", "error");
}

async function upsertYarn(y) {
  const payload = {
    producing_country_id: y.producing_country_id,
    yarn_type_id: y.yarn_type_id || null,
    percentage: y.percentage,

    renewable_energy_percentage: y.renewable_energy_percentage,
    recycled_water_percentage: y.recycled_water_percentage,

    producing_organization: y.producing_organization || null,
    address: y.address || null,
    postal_code: y.postal_code || null,
    yarn_type: y.yarn_type || null,
    production_date: y.production_date || null,

    has_certification: y.has_certification ? 1 : 0,
    certificate_number: y.has_certification ? (y.certificate_number || null) : null,
    validity_date: y.has_certification ? (y.validity_date || null) : null,

    has_client_audit: y.has_client_audit ? 1 : 0,
    audit_comments: y.has_client_audit ? (y.audit_comments || null) : null,
  };

  if (!y.id) {
    const res = await axios.post(`${API_BASE_URL}/products/${props.productId}/yarns`, payload, {
      headers: authHeaders(),
    });
    y.id = res.data?.data?.id || y.id;
  } else {
    await axios.put(`${API_BASE_URL}/products/${props.productId}/yarns/${y.id}`, payload, {
      headers: authHeaders(),
    });
  }
}

async function saveAll() {
  if (!yarns.value.length) {
    showToast("Add at least one yarn.", "error");
    return false;
  }

  for (let i = 0; i < yarns.value.length; i++) {
    if (!validateRow(yarns.value[i])) {
      showToast(`Please complete required fields (Yarn #${i + 1}).`, "error");
      return false;
    }
  }

  if (roundedTotal.value > 100) {
    showToast("Total percentage cannot exceed 100%", "error");
    return false;
  }

  loading.value = true;
  try {
    for (const y of yarns.value) await upsertYarn(y);
    return true;
  } catch (err) {
    showToast(err?.response?.data?.message || "Save failed", "error");
    return false;
  } finally {
    loading.value = false;
  }
}

async function saveProgress() {
  const ok = await saveAll();
  if (!ok) return;

  loading.value = true;
  try {
    const res = await axios.post(`${API_BASE_URL}/products/${props.productId}/yarns/save-progress`, {}, { headers: authHeaders() });
    showToast(res.data?.message || "Progress saved!", "success");
    emit("update", { status: "draft" });
    await fetchYarns();
  } catch (err) {
    showToast(err?.response?.data?.message || "Save-progress failed", "error");
  } finally {
    loading.value = false;
  }
}

async function validateAndNext() {
  const ok = await saveAll();
  if (!ok) return;

  if (roundedTotal.value !== 100) {
    showToast(`Total must be 100% (current: ${roundedTotal.value.toFixed(1)}%)`, "error");
    return;
  }

  loading.value = true;
  try {
    const res = await axios.post(`${API_BASE_URL}/products/${props.productId}/yarns/validate-step`, {}, { headers: authHeaders() });
    showToast(res.data?.message || "Volet 4 completed", "success");
    emit("update", { status: "completed" });
    emit("next");
  } catch (err) {
    showToast(err?.response?.data?.message || "Validation failed", "error");
  } finally {
    loading.value = false;
  }
}

onMounted(async () => {
  await fetchRefs();
  await fetchYarns();
});
</script>

<style scoped>
.step { display:flex; flex-direction:column; gap:14px; }
.step-title { font-size:24px; font-weight:700; margin:0; }
.step-subtitle { margin:4px 0 0 0; color:#64748b; }
.divider { height:1px; background:#e2e8f0; margin:8px 0; }

.blocked { padding:16px; border:1px solid #fecaca; background:#fff1f2; border-radius:10px; }
.blocked-title { font-weight:700; color:#991b1b; margin-bottom:6px; }
.blocked-text { color:#7f1d1d; }

.total-banner { display:flex; justify-content:space-between; align-items:center; padding:14px 16px; border-radius:10px; background:#fff7ed; border:1px solid #fed7aa; color:#9a3412; }
.total-banner.ok { background:#ecfdf5; border-color:#a7f3d0; color:#065f46; }
.total-banner .left { display:flex; align-items:center; gap:10px; }
.total-banner .icon { width:18px; height:18px; display:inline-flex; align-items:center; justify-content:center; border-radius:50%; border:1px solid currentColor; font-weight:900; }
.total-banner .right { font-weight:600; opacity:.9; }

.empty { padding:10px 0; }
.add-zone { border:2px dashed #e2e8f0; background:#fff; border-radius:10px; padding:18px; display:flex; align-items:center; justify-content:center; gap:10px; cursor:pointer; }
.add-zone:hover { border-color:#cbd5e1; }
.plus { font-size:18px; font-weight:800; }

.list { display:flex; flex-direction:column; gap:14px; }

.card { border:1px solid #e2e8f0; border-radius:12px; padding:16px; background:#fff; }
.card-top { display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; }
.card-title { font-weight:800; color:#0f172a; }
.remove { border:none; background:transparent; color:#ef4444; cursor:pointer; display:flex; align-items:center; gap:8px; font-weight:600; }

.grid { display:grid; grid-template-columns: 1fr 1fr 1fr; gap:14px; }
.field { display:flex; flex-direction:column; gap:8px; }
.field label { font-size:13px; font-weight:700; color:#0f172a; }
.req { color:#ef4444; }
.field input, .field select, .field textarea {
  border:1px solid #e2e8f0; border-radius:8px; padding:10px 12px; outline:none;
}
.field input:focus, .field select:focus, .field textarea:focus { border-color:#94a3b8; }

.toggle-row { display:flex; justify-content:space-between; align-items:center; margin:14px 0; padding-top:12px; border-top:1px solid #f1f5f9; }
.toggle-label { font-weight:700; color:#0f172a; }

.switch { position:relative; width:44px; height:24px; }
.switch input { opacity:0; width:0; height:0; }
.slider {
  position:absolute; cursor:pointer; inset:0; background:#e2e8f0; transition:.2s; border-radius:999px;
}
.slider:before {
  content:""; position:absolute; height:18px; width:18px; left:3px; top:3px; background:white; border-radius:50%; transition:.2s;
}
.switch input:checked + .slider { background:#10b981; }
.switch input:checked + .slider:before { transform:translateX(20px); }

.metrics { margin-top:10px; padding-top:12px; border-top:1px solid #f1f5f9; }
.metrics-title { font-weight:800; margin-bottom:10px; color:#0f172a; }
.metric { margin-bottom:12px; }
.metric-row { display:flex; justify-content:space-between; margin-bottom:6px; color:#0f172a; font-weight:600; }
.pct { color:#0ea5e9; }
.metric input[type="range"] { width:100%; }

/* ✅ Footer buttons same style as other steps */
.footer{
  display:flex; justify-content:space-between; align-items:center;
  gap:12px; margin-top: 10px;
}
.right-btns{ display:flex; gap:12px; align-items:center; }

.btn-secondary, .btn-light, .btn-primary{
  display:inline-flex; align-items:center; gap:10px;
  padding:12px 18px; border-radius:10px;
  font-weight:900; cursor:pointer; transition:.2s;
  border:1.5px solid #e2e8f0; background:#fff;
}
.btn-primary{
  background:#0f766e; border-color:#0f766e; color:#fff;
}
.btn-primary:hover{ background:#0b5f58; }
.btn-light:hover, .btn-secondary:hover{ background:#f8fafc; }
.btn-secondary:disabled, .btn-light:disabled, .btn-primary:disabled{ opacity:.5; cursor:not-allowed; }

.arrow{ font-size:18px; line-height:1; }

.toast {
  position:fixed; bottom:20px; right:20px; padding:12px 14px; border-radius:10px;
  background:#fff; border-left:4px solid #10b981; box-shadow:0 10px 30px rgba(0,0,0,.12);
  font-weight:700;
}
.toast.error { border-left-color:#ef4444; }

@media (max-width: 900px){
  .grid{ grid-template-columns: 1fr; }
  .footer{ flex-direction:column; align-items:stretch; }
  .right-btns{ flex-direction:column; align-items:stretch; }
  .btn-secondary, .btn-light, .btn-primary{ width:100%; justify-content:center; }
}
</style>
