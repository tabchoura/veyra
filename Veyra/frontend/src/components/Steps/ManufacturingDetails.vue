<template>
  <div class="mfg-page">
    <!-- Header -->
    <div class="page-header">
      <h2 class="page-title">Manufacturing Details</h2>
      <p class="page-subtitle">Specify manufacturing processes and certifications</p>
    </div>

    <div class="divider"></div>

    <!-- Main Card -->
    <div class="card">
      <!-- Production Location -->
      <div class="section-head">
        <div class="section-title">
          <span class="section-icon">🏭</span>
          <span>Production Location</span>
        </div>
      </div>

      <div class="grid-1">
        <div class="field">
          <label class="label">Producing Country <span class="req">*</span></label>
          <div class="select-wrap">
            <select
              v-model="form.producing_country_id"
              class="input select"
              :class="{ error: err('producing_country_id') }"
              :disabled="loading"
              @change="clearErr('producing_country_id')"
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
          <p v-if="err('producing_country_id')" class="err">{{ err("producing_country_id") }}</p>
        </div>
      </div>

      <div class="divider-soft"></div>

      <!-- Dyeing + Finishing -->
      <div class="grid-2">
        <div class="field">
          <label class="label">Dyeing Method <span class="req">*</span></label>
          <div class="select-wrap">
            <select
              v-model="form.colouring_method_id"
              class="input select"
              :class="{ error: err('colouring_method_id') }"
              :disabled="loading"
              @change="clearErr('colouring_method_id')"
            >
              <option :value="null" disabled>Select dyeing method</option>
              <option v-for="m in colouringMethods" :key="m.id" :value="m.id">
                {{ m.name }}
              </option>
            </select>
            <div class="select-icon">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
            </div>
          </div>
          <p v-if="err('colouring_method_id')" class="err">{{ err("colouring_method_id") }}</p>
        </div>

        <div class="field">
          <label class="label">Finishing Method <span class="req">*</span></label>
          <div class="select-wrap">
            <select
              v-model="form.finishing_method_id"
              class="input select"
              :class="{ error: err('finishing_method_id') }"
              :disabled="loading"
              @change="clearErr('finishing_method_id')"
            >
              <option :value="null" disabled>Select finishing method</option>
              <option v-for="m in finishingMethods" :key="m.id" :value="m.id">
                {{ m.name }}
              </option>
            </select>
            <div class="select-icon">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
            </div>
          </div>
          <p v-if="err('finishing_method_id')" class="err">{{ err("finishing_method_id") }}</p>
        </div>
      </div>

      <div class="grid-1" style="margin-top: 14px">
        <div class="field">
          <label class="label">Finish Treatment <span class="req">*</span></label>
          <div class="select-wrap">
            <select
              v-model="form.finish_treatment_id"
              class="input select"
              :class="{ error: err('finish_treatment_id') }"
              :disabled="loading"
              @change="clearErr('finish_treatment_id')"
            >
              <option :value="null" disabled>Select finish treatment</option>
              <option v-for="t in finishTreatments" :key="t.id" :value="t.id">
                {{ t.name }}
              </option>
            </select>
            <div class="select-icon">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
            </div>
          </div>
          <p v-if="err('finish_treatment_id')" class="err">{{ err("finish_treatment_id") }}</p>
        </div>
      </div>

      <div class="divider-soft"></div>

      <!-- Special Effects -->
      <div class="toggle-card">
        <div class="toggle-left">
          <span class="spark">✨</span>
          <div>
            <div class="toggle-title">Special Effects Applied?</div>
            <div class="toggle-sub">Enable if any special finishing/effects are applied</div>
          </div>
        </div>

        <label class="switch">
          <input type="checkbox" v-model="form.special_effects" :disabled="loading" />
          <span class="slider"></span>
        </label>
      </div>

      <div class="divider-soft"></div>

      <!-- Environmental -->
      <div class="panel">
        <div class="panel-title">
          <span class="panel-icon">🌿</span>
          <span>Environmental Metrics</span>
        </div>

        <div class="metric">
          <div class="metric-head">
            <div class="metric-name">
              <span class="metric-icon">♻️</span>
              Renewable Energy Usage
            </div>
            <div class="metric-value">{{ form.renewable_energy_percentage }}%</div>
          </div>
          <input type="range" min="0" max="100" v-model.number="form.renewable_energy_percentage" :disabled="loading" />
          <div class="metric-hint">Percentage of renewable energy used in manufacturing</div>
        </div>

        <div class="metric">
          <div class="metric-head">
            <div class="metric-name">
              <span class="metric-icon">💧</span>
              Recycled Water Usage
            </div>
            <div class="metric-value">{{ form.recycled_water_percentage }}%</div>
          </div>
          <input type="range" min="0" max="100" v-model.number="form.recycled_water_percentage" :disabled="loading" />
          <div class="metric-hint">Percentage of recycled water used in manufacturing</div>
        </div>
      </div>

      <!-- ZDHC -->
      <div class="panel" style="margin-top: 14px">
        <div class="panel-title">
          <span class="panel-icon">🛡️</span>
          <span>ZDHC Certifications</span>
        </div>

        <div class="zdhc-row">
          <div class="zdhc-item">
            <span>Supply to Zero</span>
            <label class="switch">
              <input type="checkbox" v-model="form.zdhc_supply_to_zero" :disabled="loading" />
              <span class="slider"></span>
            </label>
          </div>

          <div class="zdhc-item">
            <span>GetZD</span>
            <label class="switch">
              <input type="checkbox" v-model="form.zdhc_get_zd" :disabled="loading" />
              <span class="slider"></span>
            </label>
          </div>
        </div>
      </div>

      <div class="divider-soft"></div>

      <!-- Certification -->
      <div class="toggle-row">
        <div class="toggle-label">Has Certification?</div>
        <label class="switch">
          <input type="checkbox" v-model="form.has_certification" :disabled="loading" @change="onToggleCert" />
          <span class="slider"></span>
        </label>
      </div>

      <div v-if="form.has_certification" class="cert-grid">
        <div class="field">
          <label class="label">Certificate Number <span class="req">*</span></label>
          <input
            v-model="form.certificate_number"
            class="input"
            :class="{ error: err('certificate_number') }"
            :disabled="loading"
            @input="clearErr('certificate_number')"
            placeholder="Certificate number"
          />
          <p v-if="err('certificate_number')" class="err">{{ err("certificate_number") }}</p>
        </div>

        <div class="field">
          <label class="label">Validity Date <span class="req">*</span></label>
          <input
            type="date"
            v-model="form.validity_date"
            class="input"
            :class="{ error: err('validity_date') }"
            :disabled="loading"
            @change="clearErr('validity_date')"
          />
          <p v-if="err('validity_date')" class="err">{{ err("validity_date") }}</p>
        </div>

        <div class="field">
          <label class="label">Transaction Reference</label>
          <input
            v-model="form.transaction_reference"
            class="input"
            :disabled="loading"
            placeholder="Optional"
          />
        </div>
      </div>

      <div class="divider-soft"></div>

      <!-- Client Audit -->
      <div class="toggle-row">
        <div class="toggle-label">Client Audit?</div>
        <label class="switch">
          <input type="checkbox" v-model="form.has_client_audit" :disabled="loading" @change="onToggleAudit" />
          <span class="slider"></span>
        </label>
      </div>

      <div v-if="form.has_client_audit" class="grid-1">
        <div class="field">
          <label class="label">Audit Comments <span class="req">*</span></label>
          <textarea
            v-model="form.audit_comments"
            class="input textarea"
            :class="{ error: err('audit_comments') }"
            :disabled="loading"
            placeholder="Write audit comments..."
            @input="clearErr('audit_comments')"
          />
          <p v-if="err('audit_comments')" class="err">{{ err("audit_comments") }}</p>
        </div>
      </div>
    </div>

    <!-- Actions ✅ same as other pages -->
    <div class="actions">
      <button class="btn-secondary" type="button" @click="$emit('previous')" :disabled="loading">
        <span class="arrow">‹</span> Previous
      </button>

      <div class="right-actions">
        <button class="btn-light" type="button" @click="saveProgress" :disabled="loading">
          Save Progress
        </button>

        <button class="btn-primary" type="button" @click="validateStep" :disabled="loading">
          Next Step <span class="arrow">›</span>
        </button>
      </div>
    </div>

    <p v-if="error" class="error-text">{{ error }}</p>
  </div>
</template>

<script setup>
import { onMounted, reactive, ref } from "vue";
import axios from "axios";

const props = defineProps({
  productId: { type: [String, Number], required: true },
});
const emit = defineEmits(["next", "previous", "update"]);

const API_BASE_URL = import.meta.env.VITE_API_URL || "http://localhost:8000/api";

const loading = ref(false);
const error = ref("");

const countries = ref([]);
const colouringMethods = ref([]);
const finishingMethods = ref([]);
const finishTreatments = ref([]);

const form = reactive({
  id: null,
  producing_country_id: null,

  colouring_method_id: null,
  finishing_method_id: null,
  finish_treatment_id: null,

  special_effects: false,

  renewable_energy_percentage: 0,
  recycled_water_percentage: 0,

  zdhc_supply_to_zero: false,
  zdhc_get_zd: false,

  has_certification: false,
  certificate_number: "",
  validity_date: null,
  transaction_reference: "",

  has_client_audit: false,
  audit_comments: "",
});

const errors = reactive({
  producing_country_id: "",
  colouring_method_id: "",
  finishing_method_id: "",
  finish_treatment_id: "",
  certificate_number: "",
  validity_date: "",
  audit_comments: "",
});

function err(field) {
  return errors[field] || "";
}
function clearErr(field) {
  errors[field] = "";
}
function resetErrors() {
  Object.keys(errors).forEach((k) => (errors[k] = ""));
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

function pickArray(res) {
  return res?.data?.data ?? res?.data ?? [];
}

function normalize() {
  form.renewable_energy_percentage = Number(form.renewable_energy_percentage || 0);
  form.recycled_water_percentage = Number(form.recycled_water_percentage || 0);

  if (!form.has_certification) {
    form.certificate_number = "";
    form.validity_date = null;
  }
  if (!form.has_client_audit) {
    form.audit_comments = "";
  }
}

function validate(silent = false) {
  normalize();
  let ok = true;

  if (!silent) resetErrors();

  if (!form.producing_country_id) {
    ok = false;
    if (!silent) errors.producing_country_id = "Producing country is required";
  }
  if (!form.colouring_method_id) {
    ok = false;
    if (!silent) errors.colouring_method_id = "Dyeing method is required";
  }
  if (!form.finishing_method_id) {
    ok = false;
    if (!silent) errors.finishing_method_id = "Finishing method is required";
  }
  if (!form.finish_treatment_id) {
    ok = false;
    if (!silent) errors.finish_treatment_id = "Finish treatment is required";
  }

  if (form.has_certification) {
    if (!form.certificate_number?.trim()) {
      ok = false;
      if (!silent) errors.certificate_number = "Certificate number is required";
    }
    if (!form.validity_date) {
      ok = false;
      if (!silent) errors.validity_date = "Validity date is required";
    }
  }

  if (form.has_client_audit) {
    if (!form.audit_comments?.trim()) {
      ok = false;
      if (!silent) errors.audit_comments = "Audit comments are required";
    }
  }

  return ok;
}

function onToggleCert() {
  normalize();
  validate(true);
}
function onToggleAudit() {
  normalize();
  validate(true);
}

async function fetchRefs() {
  const results = await Promise.allSettled([
    axios.get(`${API_BASE_URL}/countries`, { headers: authHeaders() }),
    axios.get(`${API_BASE_URL}/colouring-methods`, { headers: authHeaders() }),
    axios.get(`${API_BASE_URL}/finishing-methods`, { headers: authHeaders() }),
    axios.get(`${API_BASE_URL}/finish-treatments`, { headers: authHeaders() }),
  ]);

  if (results[0].status === "fulfilled") countries.value = pickArray(results[0].value);
  else countries.value = [];

  if (results[1].status === "fulfilled") colouringMethods.value = pickArray(results[1].value);
  else colouringMethods.value = [];

  if (results[2].status === "fulfilled") finishingMethods.value = pickArray(results[2].value);
  else finishingMethods.value = [];

  if (results[3].status === "fulfilled") finishTreatments.value = pickArray(results[3].value);
  else finishTreatments.value = [];
}

async function fetchExisting() {
  const res = await axios.get(`${API_BASE_URL}/products/${props.productId}/manufacturings`, {
    headers: authHeaders(),
  });

  const data = pickArray(res);
  const m = data?.[0];
  if (!m) return;

  form.id = m.id ?? null;
  form.producing_country_id = m.producing_country_id ?? null;
  form.colouring_method_id = m.colouring_method_id ?? null;
  form.finishing_method_id = m.finishing_method_id ?? null;
  form.finish_treatment_id = m.finish_treatment_id ?? null;

  form.special_effects = !!m.special_effects;

  form.renewable_energy_percentage = Number(m.renewable_energy_percentage || 0);
  form.recycled_water_percentage = Number(m.recycled_water_percentage || 0);

  form.zdhc_supply_to_zero = !!m.zdhc_supply_to_zero;
  form.zdhc_get_zd = !!m.zdhc_get_zd;

  form.has_certification = !!m.has_certification;
  form.certificate_number = m.certificate_number || "";
  form.validity_date = m.validity_date || null;
  form.transaction_reference = m.transaction_reference || "";

  form.has_client_audit = !!m.has_client_audit;
  form.audit_comments = m.audit_comments || "";
}

function applyBackend422(e) {
  const bag = e?.response?.data?.errors;
  if (!bag) return;

  Object.keys(bag).forEach((k) => {
    errors[k] = Array.isArray(bag[k]) ? bag[k][0] : String(bag[k]);
  });
}

async function upsert() {
  if (!validate()) throw new Error("Please complete required fields.");

  const payload = {
    producing_country_id: form.producing_country_id,
    colouring_method_id: form.colouring_method_id,
    finishing_method_id: form.finishing_method_id,
    finish_treatment_id: form.finish_treatment_id,

    special_effects: form.special_effects ? 1 : 0,

    renewable_energy_percentage: Number(form.renewable_energy_percentage || 0),
    recycled_water_percentage: Number(form.recycled_water_percentage || 0),

    zdhc_supply_to_zero: form.zdhc_supply_to_zero ? 1 : 0,
    zdhc_get_zd: form.zdhc_get_zd ? 1 : 0,

    has_certification: form.has_certification ? 1 : 0,
    certificate_number: form.has_certification ? (form.certificate_number || null) : null,
    validity_date: form.has_certification ? (form.validity_date || null) : null,
    transaction_reference: form.transaction_reference || null,

    has_client_audit: form.has_client_audit ? 1 : 0,
    audit_comments: form.has_client_audit ? (form.audit_comments || null) : null,
  };

  if (!form.id) {
    const created = await axios.post(`${API_BASE_URL}/products/${props.productId}/manufacturings`, payload, {
      headers: authHeaders(),
    });
    form.id = created?.data?.data?.id ?? form.id;
  } else {
    await axios.put(`${API_BASE_URL}/products/${props.productId}/manufacturings/${form.id}`, payload, {
      headers: authHeaders(),
    });
  }
}

async function saveProgress() {
  loading.value = true;
  error.value = "";
  resetErrors();

  try {
    await upsert();
    const res = await axios.post(
      `${API_BASE_URL}/products/${props.productId}/manufacturings/save-progress`,
      {},
      { headers: authHeaders() }
    );
    emit("update", { status: "draft", volet: 6, backend: res.data });
  } catch (e) {
    if (e?.response?.status === 422) applyBackend422(e);
    error.value = e?.response?.data?.message || e?.message || "Save progress failed";
  } finally {
    loading.value = false;
  }
}

async function validateStep() {
  loading.value = true;
  error.value = "";
  resetErrors();

  try {
    await upsert();
    const res = await axios.post(
      `${API_BASE_URL}/products/${props.productId}/manufacturings/validate-step`,
      {},
      { headers: authHeaders() }
    );
    emit("update", { status: "completed", volet: 6, backend: res.data });
    emit("next");
  } catch (e) {
    if (e?.response?.status === 422) applyBackend422(e);
    error.value = e?.response?.data?.message || e?.message || "Validation failed";
  } finally {
    loading.value = false;
  }
}

onMounted(async () => {
  loading.value = true;
  error.value = "";
  try {
    await fetchRefs();
    await fetchExisting();
  } catch (e) {
    error.value = e?.response?.data?.message || e?.message || "Loading failed";
  } finally {
    loading.value = false;
  }
});
</script>

<style scoped>
/* ✅ EXACT SAME THEME FEEL AS YOUR OTHER PAGES */
.mfg-page { max-width: 100%; }
.page-header { margin-bottom: 18px; }
.page-title { margin: 0; font-size: 28px; font-weight: 900; color: #0f172a; letter-spacing: -0.02em; }
.page-subtitle { margin: 6px 0 0; color: #64748b; font-size: 15px; font-weight: 600; }
.divider { height: 1px; background: #e2e8f0; margin: 18px 0 24px; }

.card {
  background: #fff;
  border: 1.5px solid #e2e8f0;
  border-radius: 14px;
  padding: 18px 18px;
}

.section-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; }
.section-title { display: flex; align-items: center; gap: 10px; font-weight: 900; color: #0f172a; }
.section-icon { width: 34px; height: 34px; border-radius: 10px; display:flex; align-items:center; justify-content:center; background: #f1f5f9; }

.divider-soft { height: 1px; background: #e2e8f0; margin: 18px 0; }

.grid-1 { display: grid; grid-template-columns: 1fr; margin-top: 0; gap: 16px; }
.grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

.field { display: flex; flex-direction: column; }
.label { font-size: 13px; font-weight: 900; color: #0f172a; margin-bottom: 8px; }
.req { color: #ef4444; }

.input {
  border: 1.5px solid #e2e8f0;
  border-radius: 10px;
  padding: 11px 14px;
  font-size: 14px;
  font-weight: 700;
  outline: none;
  transition: .2s;
}
.input:focus { border-color: #0ea5e9; box-shadow: 0 0 0 3px rgba(14,165,233,.12); }
.input.error { border-color: #ef4444; }
.err { margin: 6px 0 0; color: #ef4444; font-weight: 800; font-size: 12px; }

.select-wrap { position: relative; }
.select { appearance: none; padding-right: 40px; }
.select-icon { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #64748b; pointer-events: none; }

.toggle-card {
  border: 1.5px solid #e2e8f0;
  border-radius: 14px;
  padding: 14px 16px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: #fff;
}
.toggle-left { display: flex; align-items: center; gap: 12px; }
.spark { font-size: 18px; }
.toggle-title { font-weight: 900; color: #0f172a; }
.toggle-sub { color: #64748b; font-weight: 700; font-size: 12px; margin-top: 2px; }

.panel {
  background: #fff;
  border: 1.5px solid #e2e8f0;
  border-radius: 14px;
  padding: 16px;
}
.panel-title { display: flex; align-items: center; gap: 10px; font-weight: 900; color: #0f172a; margin-bottom: 12px; }
.panel-icon { width: 34px; height: 34px; border-radius: 10px; display:flex; align-items:center; justify-content:center; background:#f1f5f9; }

.metric { margin-bottom: 14px; }
.metric-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; }
.metric-name { display: flex; align-items: center; gap: 8px; font-weight: 900; color: #0f172a; }
.metric-value { font-weight: 900; color: #0f766e; }
.metric-hint { margin-top: 8px; color: #64748b; font-size: 12px; font-weight: 700; }
.metric input[type="range"] { width: 100%; }

.zdhc-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.zdhc-item {
  background: #fff;
  border: 1.5px solid #e2e8f0;
  border-radius: 14px;
  padding: 14px 16px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-weight: 900;
  color: #0f172a;
}

.toggle-row { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; }
.toggle-label { font-weight: 900; color: #0f172a; }

.textarea { min-height: 90px; resize: vertical; }

/* ✅ Buttons EXACT same as your previous pages */
.actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 12px;
  margin-top: 10px;
}
.right-actions { display: flex; gap: 12px; align-items: center; }

.btn-secondary, .btn-light, .btn-primary {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  padding: 12px 18px;
  border-radius: 10px;
  font-weight: 900;
  cursor: pointer;
  transition: .2s;
  border: 1.5px solid #e2e8f0;
  background: #fff;
}
.btn-primary { background: #0f766e; border-color: #0f766e; color: #fff; }
.btn-primary:hover { background: #0b5f58; }
.btn-light:hover, .btn-secondary:hover { background: #f8fafc; }
.btn-secondary:disabled, .btn-light:disabled, .btn-primary:disabled { opacity: .5; cursor: not-allowed; }

.arrow { font-size: 18px; line-height: 1; }

.error-text { margin-top: 12px; color: #ef4444; font-weight: 800; }

/* ✅ Switch EXACT same as other pages */
.switch { position: relative; display: inline-block; width: 46px; height: 24px; }
.switch input { display: none; }
.slider {
  position: absolute; cursor: pointer; inset: 0;
  background: #e2e8f0; transition: .2s; border-radius: 999px;
}
.slider:before {
  position: absolute; content: "";
  height: 18px; width: 18px; left: 3px; top: 3px;
  background: #fff; transition: .2s; border-radius: 999px;
  box-shadow: 0 2px 6px rgba(0,0,0,.12);
}
.switch input:checked + .slider { background: #0f766e; }
.switch input:checked + .slider:before { transform: translateX(22px); }

@media (max-width: 900px) {
  .grid-2, .zdhc-row { grid-template-columns: 1fr; }
  .actions { flex-direction: column; align-items: stretch; }
  .right-actions { flex-direction: column; align-items: stretch; }
  .btn-secondary, .btn-light, .btn-primary { width: 100%; justify-content: center; }
}
</style>
