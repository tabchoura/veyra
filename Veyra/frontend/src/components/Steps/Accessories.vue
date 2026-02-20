<!-- src/components/Steps/Accessories.vue -->
<template>
  <div class="acc-page">
    <!-- Header (same theme as other pages) -->
    <div class="page-header">
      <h2 class="page-title">Accessories</h2>
      <p class="page-subtitle">Add all accessories used in your product</p>
    </div>

    <div class="divider"></div>

    <!-- Loading / Error -->
    <div v-if="loading" class="acc-alert info">ℹ️ Loading accessories…</div>
    <div v-else-if="error" class="acc-alert error">❌ {{ error }}</div>

    <template v-else>
      <!-- EMPTY -->
      <div v-if="accessories.length === 0" class="acc-empty">
        <div class="acc-empty-inner">
          <div class="acc-empty-icon">📦</div>
          <div class="acc-empty-title">No accessories added yet</div>

          <button class="add-empty" type="button" @click="addFirstAccessory" :disabled="saving || disabledAll">
            <span class="plus">＋</span>
            Add Accessory
          </button>
        </div>
      </div>

      <!-- LIST -->
      <div v-else class="acc-list">
        <div v-for="(a, idx) in accessories" :key="a._key" class="acc-item">
          <div class="acc-item-head">
            <h3 class="acc-item-title">Accessory #{{ idx + 1 }}</h3>

            <button class="remove-btn" type="button" @click="removeAccessory(idx)" :disabled="saving || disabledAll">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <polyline points="3 6 5 6 21 6"></polyline>
                <path
                  d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"
                ></path>
              </svg>
              Remove
            </button>
          </div>

          <div class="grid-3">
            <!-- Type -->
            <div class="field">
              <label class="label">Accessory Type <span class="req">*</span></label>
              <div class="select-wrap">
                <select
                  v-model="a.accessory_type_id"
                  class="input select"
                  :class="{ error: hasFieldError(idx, 'accessory_type_id') }"
                  @change="clearFieldError(idx, 'accessory_type_id')"
                  :disabled="saving || disabledAll"
                >
                  <option :value="null" disabled>Select type</option>
                  <option v-for="t in accessoryTypes" :key="t.id" :value="t.id">
                    {{ t.name }}
                  </option>
                </select>

                <div class="select-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="6 9 12 15 18 9"></polyline>
                  </svg>
                </div>
              </div>
              <p v-if="getFieldError(idx, 'accessory_type_id')" class="err">{{ getFieldError(idx, "accessory_type_id") }}</p>
            </div>

            <!-- Weight -->
            <div class="field">
              <label class="label">Weight (g) <span class="req">*</span></label>
              <input
                v-model.number="a.weight"
                type="number"
                min="0"
                step="0.01"
                class="input"
                :class="{ error: hasFieldError(idx, 'weight') }"
                @input="clearFieldError(idx, 'weight')"
                :disabled="saving || disabledAll"
                placeholder="0.0"
              />
              <p v-if="getFieldError(idx, 'weight')" class="err">{{ getFieldError(idx, "weight") }}</p>
            </div>

            <!-- Country -->
            <div class="field">
              <label class="label">Producing Country <span class="req">*</span></label>
              <div class="select-wrap">
                <select
                  v-model="a.producing_country_id"
                  class="input select"
                  :class="{ error: hasFieldError(idx, 'producing_country_id') }"
                  @change="clearFieldError(idx, 'producing_country_id')"
                  :disabled="saving || disabledAll"
                >
                  <option :value="null" disabled>Select country</option>
                  <option v-for="c in countries" :key="c.id" :value="c.id">
                    {{ c.name_en || c.name }}
                  </option>
                </select>

                <div class="select-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="6 9 12 15 18 9"></polyline>
                  </svg>
                </div>
              </div>
              <p v-if="getFieldError(idx, 'producing_country_id')" class="err">{{ getFieldError(idx, "producing_country_id") }}</p>
            </div>
          </div>

          <div class="divider-soft"></div>

          <!-- Certification -->
          <div class="toggle-row">
            <div class="toggle-label">Has Certification?</div>
            <label class="switch">
              <input
                type="checkbox"
                v-model="a.has_certification"
                :disabled="saving || disabledAll"
                @change="onToggleCertification(a, idx)"
              />
              <span class="slider"></span>
            </label>
          </div>

          <div v-if="a.has_certification" class="cert-grid">
            <div class="field">
              <label class="label">Certificate Number <span class="req">*</span></label>
              <input
                v-model="a.certificate_number"
                type="text"
                class="input"
                :class="{ error: hasFieldError(idx, 'certificate_number') }"
                @input="clearFieldError(idx, 'certificate_number')"
                :disabled="saving || disabledAll"
                placeholder="Ex: CERT-2026-001"
              />
              <p v-if="getFieldError(idx, 'certificate_number')" class="err">{{ getFieldError(idx, "certificate_number") }}</p>
            </div>

            <div class="field">
              <label class="label">Validity Date <span class="req">*</span></label>
              <input
                v-model="a.validity_date"
                type="date"
                class="input"
                :class="{ error: hasFieldError(idx, 'validity_date') }"
                @input="clearFieldError(idx, 'validity_date')"
                :disabled="saving || disabledAll"
              />
              <p v-if="getFieldError(idx, 'validity_date')" class="err">{{ getFieldError(idx, "validity_date") }}</p>
            </div>

            <div class="field">
              <label class="label">Transaction Reference</label>
              <input
                v-model="a.transaction_reference"
                class="input"
                :disabled="saving || disabledAll"
                placeholder="Optional"
              />
            </div>
          </div>

          <div class="divider-soft"></div>

          <!-- Client Audit -->
          <div class="toggle-row">
            <div class="toggle-label">Client Audit?</div>
            <label class="switch">
              <input type="checkbox" v-model="a.has_client_audit" :disabled="saving || disabledAll" />
              <span class="slider"></span>
            </label>
          </div>
          <p v-if="getFieldError(idx, 'has_client_audit')" class="err">{{ getFieldError(idx, "has_client_audit") }}</p>

          <div v-if="a.has_client_audit" class="grid-1">
            <div class="field">
              <label class="label">Audit Comments <span class="req">*</span></label>
              <textarea
                v-model="a.audit_comments"
                class="input textarea"
                :class="{ error: hasFieldError(idx, 'audit_comments') }"
                @input="clearFieldError(idx, 'audit_comments')"
                :disabled="saving || disabledAll"
                placeholder="Write audit comments..."
              ></textarea>
              <p v-if="getFieldError(idx, 'audit_comments')" class="err">{{ getFieldError(idx, "audit_comments") }}</p>
            </div>
          </div>

          <div class="divider-soft"></div>

          <!-- Metrics -->
          <div class="panel">
            <div class="panel-title">
              <span class="panel-icon">🌿</span>
              <span>Environmental Metrics</span>
            </div>

            <div class="metric">
              <div class="metric-head">
                <div class="metric-name">
                  <span class="metric-icon">♻️</span>
                  Renewable Energy
                </div>
                <div class="metric-value">{{ clampPct(a.renewable_energy_percentage) }}%</div>
              </div>
              <input
                type="range"
                min="0"
                max="100"
                step="1"
                v-model.number="a.renewable_energy_percentage"
                :disabled="saving || disabledAll"
              />
              <div class="metric-hint">Percentage of renewable energy used</div>
            </div>

            <div class="metric">
              <div class="metric-head">
                <div class="metric-name">
                  <span class="metric-icon">💧</span>
                  Recycled Water
                </div>
                <div class="metric-value">{{ clampPct(a.recycled_water_percentage) }}%</div>
              </div>
              <input
                type="range"
                min="0"
                max="100"
                step="1"
                v-model.number="a.recycled_water_percentage"
                :disabled="saving || disabledAll"
              />
              <div class="metric-hint">Percentage of recycled water used</div>
            </div>
          </div>
        </div>

        <button class="add-btn" type="button" @click="addAccessory" :disabled="saving || disabledAll">
          <span class="plus">＋</span> Add Accessory
        </button>
      </div>

      <!-- Actions (same as other pages) -->
      <div class="actions">
        <button class="btn-secondary" type="button" @click="goPrev" :disabled="saving">
          <span class="arrow">‹</span> Previous
        </button>

        <div class="right-actions">
          <button class="btn-light" type="button" @click="saveProgress" :disabled="saving || disabledAll || accessories.length === 0">
            Save Progress
          </button>

          <button class="btn-primary" type="button" @click="validateStep" :disabled="saving || disabledAll || accessories.length === 0">
            Next Step <span class="arrow">›</span>
          </button>
        </div>
      </div>

      <div v-if="saveMsg" class="save-msg">{{ saveMsg }}</div>
    </template>
  </div>
</template>

<script setup>
import { onMounted, ref, computed } from "vue";
import axios from "axios";
import { useRoute } from "vue-router";

const props = defineProps({
  productId: { type: [Number, String], default: null },
});
const emit = defineEmits(["next", "previous", "update"]);

const route = useRoute();
const API_BASE_URL = import.meta.env.VITE_API_URL || "http://localhost:8000/api";

const loading = ref(true);
const saving = ref(false);
const error = ref("");

const accessoryTypes = ref([]);
const countries = ref([]);
const accessories = ref([]);

const saveMsg = ref("");
const errors = ref([]);

/** ✅ same behavior as other pages */
const disabledAll = computed(() => !effectiveProductId.value);

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

const effectiveProductId = ref(null);

function getProductId() {
  if (props.productId) return String(props.productId);
  const q = route.query.productId;
  if (q) return String(q);
  const ls = localStorage.getItem("product_id");
  if (ls) return String(ls);
  return null;
}

function makeEmptyAccessory() {
  return {
    id: null,
    _key: key(),

    accessory_type_id: null,
    producing_country_id: null,

    weight: null,

    has_client_audit: false,
    audit_comments: "",

    has_certification: false,
    certificate_number: "",
    validity_date: null,
    transaction_reference: "",

    renewable_energy_percentage: 0,
    recycled_water_percentage: 0,
  };
}

function clampPct(v) {
  const n = Number(v ?? 0);
  if (Number.isNaN(n)) return 0;
  return Math.max(0, Math.min(100, Math.round(n)));
}

/* per-row errors */
function setFieldError(idx, field, message) {
  if (!errors.value[idx]) errors.value[idx] = {};
  errors.value[idx][field] = message;
}
function clearFieldError(idx, field) {
  if (errors.value[idx]) delete errors.value[idx][field];
}
function getFieldError(idx, field) {
  return errors.value[idx]?.[field] || "";
}
function hasFieldError(idx, field) {
  return Boolean(getFieldError(idx, field));
}

function normalizeAccessory(a) {
  a.renewable_energy_percentage = clampPct(a.renewable_energy_percentage);
  a.recycled_water_percentage = clampPct(a.recycled_water_percentage);

  if (!a.has_certification) {
    a.certificate_number = "";
    a.validity_date = null;
    a.transaction_reference = "";
  }
  if (!a.has_client_audit) {
    a.audit_comments = "";
  }
}

function onToggleCertification(a, idx) {
  if (!a.has_certification) {
    a.certificate_number = "";
    a.validity_date = null;
    a.transaction_reference = "";
    clearFieldError(idx, "certificate_number");
    clearFieldError(idx, "validity_date");
  }
}

function validateOne(a, idx, silent = false) {
  normalizeAccessory(a);
  let ok = true;

  if (!silent) {
    // clear row errors (keep array structure)
    errors.value[idx] = errors.value[idx] || {};
    Object.keys(errors.value[idx]).forEach((k) => delete errors.value[idx][k]);
  }

  if (!a.accessory_type_id) {
    if (!silent) setFieldError(idx, "accessory_type_id", "Accessory type is required");
    ok = false;
  }

  const w = Number(a.weight);
  if (!Number.isFinite(w) || w <= 0) {
    if (!silent) setFieldError(idx, "weight", "Weight must be > 0");
    ok = false;
  }

  if (!a.producing_country_id) {
    if (!silent) setFieldError(idx, "producing_country_id", "Producing country is required");
    ok = false;
  }

  if (typeof a.has_client_audit !== "boolean") {
    if (!silent) setFieldError(idx, "has_client_audit", "Client audit value is required");
    ok = false;
  }

  if (a.has_client_audit) {
    if (!a.audit_comments?.trim()) {
      if (!silent) setFieldError(idx, "audit_comments", "Audit comments are required");
      ok = false;
    }
  }

  if (a.has_certification) {
    if (!a.certificate_number?.trim()) {
      if (!silent) setFieldError(idx, "certificate_number", "Certificate number is required");
      ok = false;
    }
    if (!a.validity_date) {
      if (!silent) setFieldError(idx, "validity_date", "Validity date is required");
      ok = false;
    }
  }

  return ok;
}

function validateAll(silent = false) {
  if (!Array.isArray(accessories.value)) return false;
  if (!silent) errors.value = accessories.value.map(() => ({}));

  let ok = true;
  accessories.value.forEach((a, idx) => {
    if (!validateOne(a, idx, silent)) ok = false;
  });
  return ok;
}

async function fetchAccessoryTypes() {
  const res = await axios.get(`${API_BASE_URL}/accessory-types`, { headers: authHeaders() });
  accessoryTypes.value = res.data?.data || [];
}

async function fetchCountries() {
  const res = await axios.get(`${API_BASE_URL}/countries`, { headers: authHeaders() });
  countries.value = res.data?.data || [];
}

async function fetchAccessories() {
  const res = await axios.get(`${API_BASE_URL}/products/${effectiveProductId.value}/accessories`, {
    headers: authHeaders(),
  });

  const list = res.data?.data || [];
  accessories.value = (Array.isArray(list) ? list : []).map((x) => ({
    ...makeEmptyAccessory(),
    id: x.id ?? null,
    _key: x.id ? String(x.id) : key(),

    accessory_type_id: x.accessory_type_id ?? null,
    producing_country_id: x.producing_country_id ?? null,

    weight: x.weight ?? null,

    has_client_audit: !!x.has_client_audit,
    audit_comments: x.audit_comments ?? "",

    has_certification: !!x.has_certification,
    certificate_number: x.certificate_number ?? "",
    validity_date: x.validity_date ?? null,
    transaction_reference: x.transaction_reference ?? "",

    renewable_energy_percentage: clampPct(x.renewable_energy_percentage ?? 0),
    recycled_water_percentage: clampPct(x.recycled_water_percentage ?? 0),
  }));

  errors.value = accessories.value.map(() => ({}));
}

function addFirstAccessory() {
  accessories.value = [makeEmptyAccessory()];
  errors.value = [{}];
}

function addAccessory() {
  accessories.value.push(makeEmptyAccessory());
  errors.value.push({});
}

async function removeAccessory(idx) {
  const a = accessories.value[idx];
  if (!a) return;

  saving.value = true;
  saveMsg.value = "";
  error.value = "";

  try {
    if (a.id) {
      await axios.delete(`${API_BASE_URL}/products/${effectiveProductId.value}/accessories/${a.id}`, {
        headers: authHeaders(),
      });
    }
    accessories.value.splice(idx, 1);
    errors.value.splice(idx, 1);
  } catch (e) {
    error.value = e?.response?.data?.message || "Failed to remove accessory.";
  } finally {
    saving.value = false;
  }
}

async function upsertAll() {
  for (const a of accessories.value) {
    normalizeAccessory(a);

    const payload = {
      accessory_type_id: a.accessory_type_id,
      producing_country_id: a.producing_country_id,
      weight: Number(a.weight),

      has_client_audit: a.has_client_audit ? 1 : 0,
      audit_comments: a.has_client_audit ? (a.audit_comments || null) : null,

      has_certification: a.has_certification ? 1 : 0,
      certificate_number: a.has_certification ? (a.certificate_number || null) : null,
      validity_date: a.has_certification ? (a.validity_date || null) : null,
      transaction_reference: a.has_certification ? (a.transaction_reference || null) : null,

      renewable_energy_percentage: clampPct(a.renewable_energy_percentage),
      recycled_water_percentage: clampPct(a.recycled_water_percentage),
    };

    if (!a.id) {
      const created = await axios.post(`${API_BASE_URL}/products/${effectiveProductId.value}/accessories`, payload, {
        headers: authHeaders(),
      });
      a.id = created.data?.data?.id;
      a._key = a.id ? String(a.id) : a._key;
    } else {
      await axios.put(`${API_BASE_URL}/products/${effectiveProductId.value}/accessories/${a.id}`, payload, {
        headers: authHeaders(),
      });
    }
  }
}

function applyLaravelErrorsToRows(dataErrors) {
  Object.entries(dataErrors || {}).forEach(([key, msgs]) => {
    const msg = Array.isArray(msgs) ? msgs[0] : String(msgs);

    const m = key.match(/^accessories\.(\d+)\.(.+)$/);
    if (m) {
      const idx = Number(m[1]);
      const field = m[2];
      setFieldError(idx, field, msg);
      return;
    }

    // fallback -> first row
    setFieldError(0, key, msg);
  });
}

async function saveProgress() {
  saveMsg.value = "";
  error.value = "";

  if (!accessories.value.length) {
    error.value = "Add at least one accessory.";
    return;
  }

  if (!validateAll(false)) {
    error.value = "Please fix the errors before saving.";
    return;
  }

  saving.value = true;
  try {
    await upsertAll();

    const res = await axios.post(
      `${API_BASE_URL}/products/${effectiveProductId.value}/accessories/save-progress`,
      {},
      { headers: authHeaders() }
    );

    emit("update", { status: "draft", volet: 7, backend: res.data });

    saveMsg.value = res.data?.message || "Progress saved successfully";
    setTimeout(() => (saveMsg.value = ""), 2500);
  } catch (e) {
    const data = e?.response?.data;
    error.value = data?.message || "Failed to save progress.";
    if (data?.errors) applyLaravelErrorsToRows(data.errors);
  } finally {
    saving.value = false;
  }
}

async function validateStep() {
  error.value = "";
  saveMsg.value = "";

  if (!accessories.value.length) {
    error.value = "Add at least one accessory.";
    return;
  }

  if (!validateAll(false)) {
    error.value = "Please fix the errors before continuing.";
    return;
  }

  saving.value = true;
  try {
    await upsertAll();

    const res = await axios.post(
      `${API_BASE_URL}/products/${effectiveProductId.value}/accessories/validate-step`,
      {},
      { headers: authHeaders() }
    );

    emit("update", { status: "completed", volet: 7, backend: res.data });
    emit("next");
  } catch (e) {
    const data = e?.response?.data;
    error.value = data?.message || "Validation failed.";
    if (data?.errors) applyLaravelErrorsToRows(data.errors);
  } finally {
    saving.value = false;
  }
}

function goPrev() {
  emit("previous");
}

onMounted(async () => {
  loading.value = true;
  error.value = "";

  effectiveProductId.value = getProductId();
  if (!effectiveProductId.value) {
    loading.value = false;
    error.value = "Missing productId. Please complete Step 1 (Product Initialization) first.";
    return;
  }

  localStorage.setItem("product_id", String(effectiveProductId.value));

  try {
    await Promise.all([fetchAccessoryTypes(), fetchCountries()]);
    await fetchAccessories();

    if (accessories.value.length === 0) addFirstAccessory();
  } catch (e) {
    error.value = e?.response?.data?.message || "Failed to load accessories.";
  } finally {
    loading.value = false;
  }
});
</script>

<style scoped>
/* ✅ SAME THEME AS YOUR OTHER STEPS */
.acc-page { max-width: 100%; }

.page-header { margin-bottom: 18px; }
.page-title { margin: 0; font-size: 28px; font-weight: 900; color: #0f172a; letter-spacing: -0.02em; }
.page-subtitle { margin: 6px 0 0; color: #64748b; font-size: 15px; font-weight: 600; }
.divider { height: 1px; background: #e2e8f0; margin: 18px 0 24px; }

/* Card like other pages */
.acc-card{
  background:#fff;
  border:1.5px solid #e2e8f0;
  border-radius:14px;
  padding:18px 18px;
}

/* Alerts */
.acc-alert{
  border-radius:12px;
  padding:14px 16px;
  font-weight:800;
  border:1.5px solid #e2e8f0;
  background:#f8fafc;
  color:#0f172a;
}
.acc-alert.error{
  background:#fff1f2;
  border-color:#fecaca;
  color:#991b1b;
}
.acc-alert.info{
  background:#eff6ff;
  border-color:#bfdbfe;
  color:#1e40af;
}

/* Empty */
.acc-empty-inner{
  border:2px dashed #d1d5db;
  border-radius:14px;
  background:#fff;
  padding:28px 18px;
  text-align:center;
}
.acc-empty-icon{ font-size:44px; opacity:.6; }
.acc-empty-title{ margin:10px 0 16px; font-weight:900; color:#475569; }

/* Add button (same as other pages) */
.add-empty{
  width:100%;
  border:2px dashed #d1d5db;
  border-radius:10px;
  background:#fff;
  padding:16px 18px;
  display:flex; align-items:center; justify-content:center;
  gap:12px;
  font-weight:900; cursor:pointer;
  transition:.2s;
  margin-top: 14px;
}
.add-empty:hover{ background:#f8fafc; }
.plus{ font-size:18px; }

/* List items */
.acc-list{ display:flex; flex-direction:column; gap:18px; }
.acc-item{
  background:#fff;
  border:1.5px solid #e2e8f0;
  border-radius:14px;
  padding:18px 18px;
}
.acc-item-head{
  display:flex; align-items:center; justify-content:space-between;
  margin-bottom:14px;
}
.acc-item-title{ margin:0; font-size:18px; font-weight:900; color:#0f172a; }

.remove-btn{
  background:transparent; border:none; color:#ef4444;
  font-weight:900; cursor:pointer; display:flex; align-items:center; gap:8px;
}
.remove-btn:disabled{ opacity:.4; cursor:not-allowed; }

.divider-soft{ height:1px; background:#e2e8f0; margin:18px 0; }

/* Grids */
.grid-3{ display:grid; grid-template-columns: 1fr 1fr 1fr; gap:16px; }
.grid-1{ display:grid; grid-template-columns: 1fr; gap:16px; }
.cert-grid{ display:grid; grid-template-columns: 1fr 1fr 1fr; gap:16px; }

/* Fields */
.field{ display:flex; flex-direction:column; }
.label{ font-size:13px; font-weight:900; color:#0f172a; margin-bottom:8px; }
.req{ color:#ef4444; }

.input{
  border:1.5px solid #e2e8f0; border-radius:10px;
  padding:11px 14px; font-size:14px; font-weight:700;
  outline:none; transition:.2s; background:#fff;
}
.input:focus{ border-color:#0ea5e9; box-shadow:0 0 0 3px rgba(14,165,233,.12); }
.input.error{ border-color:#ef4444; }
.err{ margin:6px 0 0; color:#ef4444; font-weight:800; font-size:12px; }

.select-wrap{ position:relative; }
.select{ appearance:none; padding-right:40px; }
.select-icon{ position:absolute; right:12px; top:50%; transform:translateY(-50%); color:#64748b; pointer-events:none; }

.textarea{ min-height:90px; resize:vertical; }

/* Toggles */
.toggle-row{
  display:flex; justify-content:space-between; align-items:center;
  padding:10px 0;
}
.toggle-label{ font-weight:900; color:#0f172a; }

.switch{ position:relative; display:inline-block; width:46px; height:24px; }
.switch input{ display:none; }
.slider{
  position:absolute; cursor:pointer; inset:0;
  background:#e2e8f0; transition:.2s; border-radius:999px;
}
.slider:before{
  position:absolute; content:""; height:18px; width:18px;
  left:3px; top:3px; background:#fff; transition:.2s; border-radius:999px;
  box-shadow:0 2px 6px rgba(0,0,0,.12);
}
.switch input:checked + .slider{ background:#0f766e; }
.switch input:checked + .slider:before{ transform:translateX(22px); }

/* Metrics panel like other pages */
.panel{
  border:1.5px solid #e2e8f0;
  border-radius:14px;
  padding:16px;
  background:#fff;
}
.panel-title{ display:flex; align-items:center; gap:10px; font-weight:900; color:#0f172a; margin-bottom:12px; }
.panel-icon{ width:34px; height:34px; border-radius:10px; display:flex; align-items:center; justify-content:center; background:#f1f5f9; }

.metric{ margin-bottom:14px; }
.metric-head{ display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; }
.metric-name{ display:flex; align-items:center; gap:8px; font-weight:900; color:#0f172a; }
.metric-icon{ opacity:.9; }
.metric-value{ font-weight:900; color:#0f766e; }
.metric-hint{ margin-top:8px; color:#64748b; font-size:12px; font-weight:700; }
.metric input[type="range"]{ width:100%; }

/* Add more button like other pages */
.add-btn{
  width:100%;
  border:1.5px solid #e2e8f0;
  background:#fff;
  border-radius:10px;
  padding:14px 18px;
  font-weight:900;
  cursor:pointer;
  display:flex; align-items:center; justify-content:center;
  gap:10px;
  margin: 8px 0 18px;
}
.add-btn:hover{ background:#f8fafc; }

/* Actions like other pages */
.actions{
  display:flex; justify-content:space-between; align-items:center;
  gap:12px; margin-top: 10px;
}
.right-actions{ display:flex; gap:12px; align-items:center; }

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

.save-msg{
  margin-top:12px;
  color:#0f766e;
  font-weight:900;
}

@media (max-width: 900px){
  .grid-3, .cert-grid{ grid-template-columns:1fr; }
  .actions{ flex-direction:column; align-items:stretch; }
  .right-actions{ flex-direction:column; align-items:stretch; }
  .btn-secondary, .btn-light, .btn-primary{ width:100%; justify-content:center; }
}
</style>
