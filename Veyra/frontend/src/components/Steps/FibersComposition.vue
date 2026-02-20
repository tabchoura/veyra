<template>
  <div class="fibers-page">
    <div class="page-header">
      <h2 class="page-title">Fibers Composition</h2>
      <p class="page-subtitle">Define the fiber composition of your product</p>
    </div>

    <div class="divider"></div>

    <!-- TOTAL BAR -->
    <div class="total-bar" :class="{ warn: totalRounded !== 100, ok: totalRounded === 100 }">
      <div class="total-left">
        <div class="warn-icon">
          <!-- ✅ icon changes -->
          <svg
            v-if="totalRounded !== 100"
            width="18"
            height="18"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2.5"
          >
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="7" x2="12" y2="13"></line>
            <circle cx="12" cy="17" r="1"></circle>
          </svg>

          <svg
            v-else
            width="18"
            height="18"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2.5"
          >
            <polyline points="20 6 9 17 4 12"></polyline>
          </svg>
        </div>

        <div class="total-text">
          <span class="total-label">Total Composition:</span>
          <span class="total-value">{{ totalPercentage.toFixed(1) }}%</span>
        </div>
      </div>

      <!-- ✅ text changes -->
      <div class="total-right">
        <span v-if="totalRounded === 100">Perfect ✅</span>
        <span v-else>Must equal 100%</span>
      </div>
    </div>

    <!-- Add fiber -->
    <button class="add-empty" type="button" @click="addFiber" :disabled="saving || disabledAll">
      <span class="plus">＋</span>
      Add Fiber
    </button>

    <div class="divider"></div>

    <div v-if="fibers.length > 0">
      <div v-for="(row, idx) in fibers" :key="row.localKey" class="fiber-card">
        <div class="fiber-card-header">
          <h3 class="fiber-card-title">Fiber #{{ idx + 1 }}</h3>

          <button
            class="remove-btn"
            type="button"
            @click="removeFiber(idx)"
            :disabled="saving || disabledAll || fibers.length <= 1"
          >
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <polyline points="3 6 5 6 21 6"></polyline>
              <path
                d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"
              ></path>
            </svg>
            Remove
          </button>
        </div>

        <!-- 3 cols -->
        <div class="grid-3">
          <div class="field">
            <label class="label">Fiber Type <span class="req">*</span></label>
            <div class="select-wrap">
              <select
                v-model="row.fiber_id"
                class="input select"
                :class="{ error: getErr(row, 'fiber_id') }"
                :disabled="loading || saving || disabledAll"
                @change="clearErr(row, 'fiber_id')"
              >
                <option :value="null" disabled>Select fiber</option>
                <option v-for="m in materials" :key="m.id" :value="m.id">
                  {{ m.name }}
                </option>
              </select>

              <div class="select-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
              </div>
            </div>
            <p v-if="getErr(row, 'fiber_id')" class="err">{{ getErr(row, "fiber_id") }}</p>
          </div>

          <div class="field">
            <label class="label">Percentage <span class="req">*</span></label>
            <input
              v-model="row.percentage"
              class="input"
              :class="{ error: getErr(row, 'percentage') }"
              type="number"
              min="0"
              max="100"
              step="0.1"
              placeholder="0.0"
              :disabled="saving || disabledAll"
              @input="onPercentageInput(row)"
              @blur="validateRow(row)"
            />
            <p v-if="getErr(row, 'percentage')" class="err">{{ getErr(row, "percentage") }}</p>
          </div>

          <div class="field">
            <label class="label">Origin Country <span class="req">*</span></label>
            <div class="select-wrap">
              <select
                v-model="row.origin_country_id"
                class="input select"
                :class="{ error: getErr(row, 'origin_country_id') }"
                :disabled="loading || saving || disabledAll"
                @change="clearErr(row, 'origin_country_id')"
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
            <p v-if="getErr(row, 'origin_country_id')" class="err">{{ getErr(row, "origin_country_id") }}</p>
          </div>
        </div>

        <!-- Transaction date -->
        <div class="grid-1">
          <div class="field">
            <label class="label">Transaction Date</label>
            <input v-model="row.transaction_date" type="date" class="input" :disabled="saving || disabledAll" />
          </div>
        </div>

        <div class="divider-soft"></div>

        <!-- Has Certification -->
        <div class="toggle-row">
          <div class="toggle-label">Has Certification?</div>
          <label class="switch">
            <input
              type="checkbox"
              v-model="row.has_certification"
              :disabled="saving || disabledAll"
              @change="onToggleCertification(row)"
            />
            <span class="slider"></span>
          </label>
        </div>

        <div v-if="row.has_certification" class="cert-grid">
          <div class="field">
            <label class="label">Certificate Number <span class="req">*</span></label>
            <input
              v-model="row.certificate_number"
              class="input"
              :class="{ error: getErr(row, 'certificate_number') }"
              :disabled="saving || disabledAll"
              placeholder="Certificate number"
              @blur="validateRow(row)"
            />
            <p v-if="getErr(row, 'certificate_number')" class="err">{{ getErr(row, "certificate_number") }}</p>
          </div>

          <div class="field">
            <label class="label">Validity Date <span class="req">*</span></label>
            <input
              v-model="row.validity_date"
              type="date"
              class="input"
              :class="{ error: getErr(row, 'validity_date') }"
              :disabled="saving || disabledAll"
              @blur="validateRow(row)"
            />
            <p v-if="getErr(row, 'validity_date')" class="err">{{ getErr(row, "validity_date") }}</p>
          </div>

          <div class="field">
            <label class="label">Transaction Reference</label>
            <input v-model="row.transaction_reference" class="input" :disabled="saving || disabledAll" placeholder="Optional" />
          </div>
        </div>

        <div class="divider-soft"></div>

        <!-- Client Audit -->
        <div class="toggle-row">
          <div class="toggle-label">Client Audit?</div>
          <label class="switch">
            <input
              type="checkbox"
              v-model="row.has_client_audit"
              :disabled="saving || disabledAll"
              @change="onToggleAudit(row)"
            />
            <span class="slider"></span>
          </label>
        </div>

        <div v-if="row.has_client_audit" class="grid-1">
          <div class="field">
            <label class="label">Audit Comments <span class="req">*</span></label>
            <textarea
              v-model="row.audit_comments"
              class="input textarea"
              :class="{ error: getErr(row, 'audit_comments') }"
              :disabled="saving || disabledAll"
              placeholder="Write audit comments..."
              @blur="validateRow(row)"
            ></textarea>
            <p v-if="getErr(row, 'audit_comments')" class="err">{{ getErr(row, "audit_comments") }}</p>
          </div>
        </div>
      </div>

      <button class="add-btn" type="button" @click="addFiber" :disabled="saving || disabledAll">
        <span class="plus">＋</span> Add Fiber
      </button>
    </div>

    <!-- ACTIONS -->
    <div class="actions">
      <button class="btn-secondary" type="button" @click="$emit('previous')" :disabled="saving">
        <span class="arrow">‹</span> Previous
      </button>

      <div class="right-actions">
        <button class="btn-light" type="button" @click="saveProgress" :disabled="saving || disabledAll || fibers.length === 0">
          Save Progress
        </button>

        <button class="btn-primary" type="button" @click="validateStep" :disabled="saving || disabledAll || !canNext">
          Next Step <span class="arrow">›</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref, watch } from "vue";
import axios from "axios";

const props = defineProps({
  productId: [String, Number], // ✅ not required (same as old fix)
});

const emit = defineEmits(["next", "previous", "update"]);

const API_BASE_URL = import.meta.env.VITE_API_URL || "http://localhost:8000/api";

const loading = ref(false);
const saving = ref(false);

const materials = ref([]);
const countries = ref([]);
const fibers = ref([]);

const errs = reactive({});

const disabledAll = computed(() => !props.productId);

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

function getErr(row, field) {
  return errs[row.localKey]?.[field] || "";
}
function setErr(row, field, msg) {
  if (!errs[row.localKey]) errs[row.localKey] = {};
  errs[row.localKey][field] = msg;
}
function clearErr(row, field) {
  if (!errs[row.localKey]) return;
  errs[row.localKey][field] = "";
}

const totalPercentage = computed(() =>
  fibers.value.reduce((sum, f) => sum + (parseFloat(f.percentage) || 0), 0)
);

/* ✅ NEW: rounded total for 100% comparison (avoid 99.999) */
const totalRounded = computed(() => Math.round(totalPercentage.value * 10) / 10);

const canNext = computed(() => {
  if (disabledAll.value) return false;
  if (totalRounded.value !== 100) return false;
  if (fibers.value.length < 1) return false;
  return fibers.value.every((row) => validateRow(row, true));
});

function normalizeRow(row) {
  if (!row.has_certification) {
    row.certificate_number = "";
    row.validity_date = "";
  }
  if (!row.has_client_audit) {
    row.audit_comments = "";
  }
}

function validateRow(row, silent = false) {
  let ok = true;

  if (!row.fiber_id) {
    ok = false;
    if (!silent) setErr(row, "fiber_id", "Fiber type is required");
  } else if (!silent) clearErr(row, "fiber_id");

  const p = parseFloat(row.percentage);
  if (row.percentage === null || row.percentage === "" || isNaN(p) || p <= 0) {
    ok = false;
    if (!silent) setErr(row, "percentage", "Percentage must be > 0");
  } else if (p > 100) {
    ok = false;
    if (!silent) setErr(row, "percentage", "Percentage cannot exceed 100");
  } else if (!silent) clearErr(row, "percentage");

  if (!row.origin_country_id) {
    ok = false;
    if (!silent) setErr(row, "origin_country_id", "Origin country is required");
  } else if (!silent) clearErr(row, "origin_country_id");

  if (row.has_certification) {
    if (!row.certificate_number?.trim()) {
      ok = false;
      if (!silent) setErr(row, "certificate_number", "Certificate number is required");
    } else if (!silent) clearErr(row, "certificate_number");

    if (!row.validity_date) {
      ok = false;
      if (!silent) setErr(row, "validity_date", "Validity date is required");
    } else if (!silent) clearErr(row, "validity_date");
  } else if (!silent) {
    clearErr(row, "certificate_number");
    clearErr(row, "validity_date");
  }

  if (row.has_client_audit) {
    if (!row.audit_comments?.trim()) {
      ok = false;
      if (!silent) setErr(row, "audit_comments", "Audit comments are required");
    } else if (!silent) clearErr(row, "audit_comments");
  } else if (!silent) {
    clearErr(row, "audit_comments");
  }

  return ok;
}

function onPercentageInput(row) {
  if (row.percentage === "" || row.percentage === null) return;

  let val = parseFloat(row.percentage);
  if (isNaN(val)) val = 0;

  val = Math.max(0, Math.min(100, val));
  row.percentage = val;

  const total = totalPercentage.value;
  if (total > 100) {
    const over = total - 100;
    row.percentage = Math.max(0, val - over);
  }
}

function addFiber() {
  fibers.value.push({
    localKey: key(),
    id: null,
    fiber_id: null,
    percentage: null,
    origin_country_id: null,
    transaction_date: "",
    has_certification: false,
    certificate_number: "",
    validity_date: "",
    transaction_reference: "",
    has_client_audit: false,
    audit_comments: "",
  });
}

function onToggleCertification(row) {
  normalizeRow(row);
  validateRow(row);
}
function onToggleAudit(row) {
  normalizeRow(row);
  validateRow(row);
}

async function removeFiber(idx) {
  if (fibers.value.length <= 1) return;

  const row = fibers.value[idx];

  // ✅ if no productId, only remove locally
  if (!props.productId) {
    fibers.value.splice(idx, 1);
    return;
  }

  if (row.id) {
    saving.value = true;
    try {
      await axios.delete(`${API_BASE_URL}/products/${props.productId}/fibers/${row.id}`, {
        headers: authHeaders(),
      });
      fibers.value.splice(idx, 1);
    } catch (e) {
      alert(e.response?.data?.message || e.message);
    } finally {
      saving.value = false;
    }
  } else {
    fibers.value.splice(idx, 1);
  }
}

/** LOAD MATERIALS (protected) */
async function fetchMaterials() {
  const res = await axios.get(`${API_BASE_URL}/materials`, { headers: authHeaders() });
  materials.value = res.data?.data || [];
}

/** LOAD COUNTRIES (public in your routes) */
async function fetchCountries() {
  const res = await axios.get(`${API_BASE_URL}/countries`, { headers: { Accept: "application/json" } });
  if (res.data?.data) countries.value = res.data.data;
  else if (Array.isArray(res.data)) countries.value = res.data;
  else countries.value = [];
}

async function fetchExistingFibers() {
  if (!props.productId) {
    fibers.value = [];
    return; // ✅ silent
  }

  loading.value = true;
  try {
    const res = await axios.get(`${API_BASE_URL}/products/${props.productId}/fibers`, {
      headers: authHeaders(),
    });

    const data = res.data?.data || [];
    if (data.length) {
      fibers.value = data.map((f) => ({
        localKey: key(),
        id: f.id,
        fiber_id: f.fiber_id ?? f.fiber?.id ?? null,
        percentage: f.percentage ?? null,
        origin_country_id: f.origin_country_id ?? f.originCountry?.id ?? null,
        transaction_date: f.transaction_date || "",
        has_certification: !!f.has_certification,
        certificate_number: f.certificate_number || "",
        validity_date: f.validity_date || "",
        transaction_reference: f.transaction_reference || "",
        has_client_audit: !!f.has_client_audit,
        audit_comments: f.audit_comments || "",
      }));
    } else {
      fibers.value = [];
    }

    emit("update", { step3Completed: !!res.data?.completed, step3Status: res.data?.status || "orange" });
  } catch (e) {
    alert(e.response?.data?.message || e.message);
  } finally {
    loading.value = false;
  }
}

/** UPSERT */
async function upsertRow(row) {
  const payload = {
    fiber_id: row.fiber_id,
    percentage: row.percentage,
    origin_country_id: row.origin_country_id,
    transaction_date: row.transaction_date || null,

    has_certification: row.has_certification ? 1 : 0,
    certificate_number: row.has_certification ? (row.certificate_number || null) : null,
    validity_date: row.has_certification ? (row.validity_date || null) : null,
    transaction_reference: row.transaction_reference || null,

    has_client_audit: row.has_client_audit ? 1 : 0,
    audit_comments: row.has_client_audit ? (row.audit_comments || null) : null,
  };

  if (!row.id) {
    const res = await axios.post(`${API_BASE_URL}/products/${props.productId}/fibers`, payload, { headers: authHeaders() });
    row.id = res.data?.data?.id || row.id;
  } else {
    await axios.put(`${API_BASE_URL}/products/${props.productId}/fibers/${row.id}`, payload, { headers: authHeaders() });
  }
}

async function saveAllRows() {
  if (disabledAll.value) return false;

  if (fibers.value.length < 1) {
    alert("At least one fiber is required.");
    return false;
  }

  let ok = true;
  for (const row of fibers.value) {
    normalizeRow(row);
    if (!validateRow(row)) ok = false;
  }
  if (!ok) {
    alert("Please complete required fields.");
    return false;
  }

  saving.value = true;
  try {
    for (const row of fibers.value) await upsertRow(row);
    return true;
  } catch (e) {
    alert(e.response?.data?.message || e.message);
    return false;
  } finally {
    saving.value = false;
  }
}

async function saveProgress() {
  const saved = await saveAllRows();
  if (!saved) return;

  saving.value = true;
  try {
    const res = await axios.post(`${API_BASE_URL}/products/${props.productId}/fibers/save-progress`, {}, { headers: authHeaders() });
    emit("update", { step3Completed: false, step3Status: "orange" });
    // alert(res.data?.message || "Progress saved successfully");
  } catch (e) {
    alert(e.response?.data?.message || e.message);
  } finally {
    saving.value = false;
  }
}

async function validateStep() {
  const saved = await saveAllRows();
  if (!saved) return;

  const total = totalRounded.value;
  if (total !== 100) {
    alert(`Total must be 100% (current: ${total.toFixed(1)}%)`);
    return;
  }

  saving.value = true;
  try {
    const res = await axios.post(`${API_BASE_URL}/products/${props.productId}/fibers/validate-step`, {}, { headers: authHeaders() });
    emit("update", { step3Completed: true, step3Status: "green" });
    emit("next");
    alert(res.data?.message || "Step 3 completed successfully");
  } catch (e) {
    alert(e.response?.data?.message || e.message);
  } finally {
    saving.value = false;
  }
}

watch(
  () => props.productId,
  async (v) => {
    // ✅ when productId arrives after step1, load existing fibers
    if (v) await fetchExistingFibers();
  },
  { immediate: true }
);

onMounted(async () => {
  await Promise.all([fetchMaterials(), fetchCountries()]);
  // fetchExistingFibers handled by watch(immediate)
});
</script>

<style scoped>
/* (ton CSS identique) */
.fibers-page { max-width: 100%; }
.page-header { margin-bottom: 18px; }
.page-title { margin: 0; font-size: 28px; font-weight: 900; color: #0f172a; letter-spacing: -0.02em; }
.page-subtitle { margin: 6px 0 0; color: #64748b; font-size: 15px; font-weight: 600; }
.divider { height: 1px; background: #e2e8f0; margin: 18px 0 24px; }

/* ✅ BASE */
.total-bar{
  display:flex; align-items:center; justify-content:space-between;
  border-radius:12px; padding:16px 18px; margin-bottom:22px;
  border:1.5px solid #f59e0b;
  background:#fff7e6;
  transition: .2s ease;
}

/* ✅ ORANGE when not 100% */
.total-bar.warn{
  border-color:#f59e0b;
  background:#fff7e6;
}

/* ✅ GREEN when 100% */
.total-bar.ok{
  border-color:#10b981;
  background:#ecfdf5;
}

.total-left{ display:flex; align-items:center; gap:12px; }

/* keep your icon container */
.warn-icon{
  width:34px; height:34px; border-radius:50%;
  display:flex; align-items:center; justify-content:center;
  background:#ffedd5; color:#f59e0b;
}

/* ✅ icon container becomes green when ok */
.total-bar.ok .warn-icon{
  background:#d1fae5;
  color:#10b981;
}

/* keep your text */
.total-text{ display:flex; gap:8px; align-items:center; font-weight:900; color:#9a3412; }
.total-label{ font-size:16px; }
.total-value{ font-size:16px; }
.total-right{ font-weight:800; color:#f59e0b; }

/* ✅ right text becomes green when ok */
.total-bar.ok .total-text{ color:#065f46; }
.total-bar.ok .total-right{ color:#10b981; }

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
  margin-bottom: 8px;
}
.add-empty:hover{ background:#f8fafc; }
.plus{ font-size:18px; }

.fiber-card{
  background:#fff;
  border:1.5px solid #e2e8f0;
  border-radius:14px;
  padding:18px 18px;
  margin: 0 0 18px;
}
.fiber-card-header{
  display:flex; align-items:center; justify-content:space-between;
  margin-bottom: 14px;
}
.fiber-card-title{ margin:0; font-size:18px; font-weight:900; color:#0f172a; }
.remove-btn{
  background:transparent; border:none; color:#ef4444;
  font-weight:900; cursor:pointer; display:flex; align-items:center; gap:8px;
}
.remove-btn:disabled{ opacity:.4; cursor:not-allowed; }

.grid-3{ display:grid; grid-template-columns: 1fr 1fr 1fr; gap:16px; }
.grid-1{ display:grid; grid-template-columns: 1fr; margin-top: 14px; }

.field{ display:flex; flex-direction:column; }
.label{ font-size:13px; font-weight:900; color:#0f172a; margin-bottom:8px; }
.req{ color:#ef4444; }

.input{
  border:1.5px solid #e2e8f0; border-radius:10px;
  padding:11px 14px; font-size:14px; font-weight:700;
  outline:none; transition:.2s;
}
.input:focus{ border-color:#0ea5e9; box-shadow:0 0 0 3px rgba(14,165,233,.12); }
.input.error{ border-color:#ef4444; }
.err{ margin:6px 0 0; color:#ef4444; font-weight:800; font-size:12px; }

.select-wrap{ position:relative; }
.select{ appearance:none; padding-right:40px; }
.select-icon{ position:absolute; right:12px; top:50%; transform:translateY(-50%); color:#64748b; pointer-events:none; }

.divider-soft{ height:1px; background:#e2e8f0; margin:18px 0; }

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

.cert-grid{ display:grid; grid-template-columns: 1fr 1fr 1fr; gap:16px; }
.textarea{ min-height: 90px; resize:vertical; }

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

@media (max-width: 900px){
  .grid-3, .cert-grid{ grid-template-columns:1fr; }
  .actions{ flex-direction:column; align-items:stretch; }
  .right-actions{ flex-direction:column; align-items:stretch; }
  .btn-secondary, .btn-light, .btn-primary{ width:100%; justify-content:center; }
}
</style>
