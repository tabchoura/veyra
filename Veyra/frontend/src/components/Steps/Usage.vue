<template>
  <div class="u-page">
    <!-- Header (same theme as other steps) -->
    <div class="page-header">
      <h2 class="page-title">Usage Information</h2>
      <p class="page-subtitle">Specify delivery details, care instructions, and repairability</p>
    </div>

    <div class="divider"></div>

    <!-- Loading / Error -->
    <div v-if="loadingInit" class="u-alert info">ℹ️ Loading usage data…</div>
    <div v-else-if="error" class="u-alert error">❌ {{ error }}</div>

    <template v-else>
      <!-- Delivery -->
      <div class="card">
        <div class="card-title">
          <span class="icon">🚚</span>
          <span>Delivery Information</span>
        </div>

        <div class="grid-2">
          <div class="field">
            <label class="label">Delivery Country <span class="req">*</span></label>
            <div class="select-wrap">
              <select
                v-model="form.delivery_country_id"
                class="input select"
                :class="{ error: fieldErr('delivery_country_id') }"
                :disabled="busy"
                @change="clearErr('delivery_country_id')"
              >
                <option :value="null" disabled>Select country</option>
                <option v-for="c in countries" :key="c.id" :value="c.id">
                  {{ c.name_en || c.name }}
                </option>
              </select>
              <span class="select-icon">▾</span>
            </div>
            <p v-if="fieldErr('delivery_country_id')" class="err">{{ fieldErr("delivery_country_id") }}</p>
          </div>

          <div class="field">
            <label class="label">Delivery Date <span class="req">*</span></label>
            <input
              type="date"
              v-model="form.delivery_date"
              class="input"
              :class="{ error: fieldErr('delivery_date') }"
              :disabled="busy"
              @change="clearErr('delivery_date')"
            />
            <p v-if="fieldErr('delivery_date')" class="err">{{ fieldErr("delivery_date") }}</p>
          </div>
        </div>
      </div>

      <!-- Care -->
      <div class="card">
        <div class="card-title">
          <span class="icon">🧼</span>
          <span>Care Instructions</span>
        </div>

        <div class="grid-1">
          <div class="field">
            <label class="label">Washing Temperature (°C)</label>
            <input
              type="number"
              min="0"
              max="120"
              v-model.number="form.washing_temperature"
              class="input"
              :disabled="busy"
              placeholder="e.g., 30"
            />
            <p class="hint">Leave empty if not applicable. Typical ranges: 20–60°C.</p>
          </div>
        </div>

        <div class="divider-soft"></div>

        <div class="toggles">
          <div class="toggle-box" v-for="t in toggleList" :key="t.key">
            <div class="toggle-left">
              <div class="toggle-label">{{ t.label }}</div>
              <div class="toggle-sub">{{ t.sub }}</div>
            </div>

            <label class="switch">
              <input type="checkbox" v-model="form[t.key]" :disabled="busy" />
              <span class="slider"></span>
            </label>
          </div>
        </div>
      </div>

      <!-- Repairability -->
      <div class="card">
        <div class="card-title">
          <span class="icon">🛠️</span>
          <span>Repairability</span>
        </div>

        <div class="repair-row">
          <div>
            <div class="repair-label">Repairable Product <span class="req">*</span></div>
            <div class="repair-sub">Can this product be repaired?</div>
          </div>

          <label class="switch">
            <input type="checkbox" v-model="form.is_repairable" :disabled="busy" @change="onToggleRepairable" />
            <span class="slider"></span>
          </label>
        </div>

        <div v-if="form.is_repairable" class="grid-1 mt-12">
          <div class="field">
            <label class="label">Repair Comment <span class="req">*</span></label>
            <textarea
              rows="3"
              v-model.trim="form.repair_comment"
              class="input textarea"
              :class="{ error: fieldErr('repair_comment') }"
              :disabled="busy"
              placeholder="Explain how/why it can be repaired..."
              @input="clearErr('repair_comment')"
            ></textarea>
            <p v-if="fieldErr('repair_comment')" class="err">{{ fieldErr("repair_comment") }}</p>
            <p class="hint">Example: replace zipper, re-stitch seams, patch fabric, etc.</p>
          </div>
        </div>
      </div>

      <!-- Footer (same as other steps) -->
      <div class="actions">
        <button class="btn-secondary" type="button" @click="emit('previous')" :disabled="busy">
          ‹ Previous
        </button>

        <div class="right-actions">
          <button class="btn-light" type="button" @click="saveProgress" :disabled="busy">
            💾 Save Progress
          </button>

          <button class="btn-primary" type="button" @click="validateStep" :disabled="busy">
            Next Step ›
          </button>
        </div>
      </div>

      <div v-if="toast" class="toast" :class="{ error: toastType === 'error' }">{{ toast }}</div>
    </template>
  </div>
</template>

<script setup>
import { onMounted, reactive, ref } from "vue";
import axios from "axios";

const props = defineProps({
  productId: { type: [Number, String], required: true },
});
const emit = defineEmits(["next", "previous", "update"]);

const API_BASE_URL = import.meta.env.VITE_API_URL || "http://localhost:8000/api";

const countries = ref([]);
const loadingInit = ref(true);
const busy = ref(false);
const error = ref("");

const toast = ref("");
const toastType = ref("success");

const toggleList = [
  { key: "hand_wash", label: "Hand Wash", sub: "Recommended for delicate items" },
  { key: "machine_wash", label: "Machine Wash", sub: "Standard washing allowed" },
  { key: "dry_clean", label: "Dry Clean", sub: "Professional cleaning required" },
  { key: "bleach", label: "Bleach", sub: "Bleach permitted" },
  { key: "dry_shade", label: "Dry Shade", sub: "Avoid direct sunlight" },
  { key: "tumble_dry", label: "Tumble Dry", sub: "Dryer permitted" },
  { key: "ironing", label: "Ironing", sub: "Ironing allowed" },
];

function showToast(msg, type = "success") {
  toast.value = msg;
  toastType.value = type;
  setTimeout(() => {
    toast.value = "";
    toastType.value = "success";
  }, 2200);
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
  const headers = { Accept: "application/json" };
  if (token) headers.Authorization = `Bearer ${token}`;
  return headers;
}

/** form */
const form = reactive({
  brand: "",

  delivery_country_id: null,
  delivery_date: "",

  washing_temperature: 30,

  hand_wash: false,
  machine_wash: false,
  dry_clean: false,
  bleach: false,
  dry_shade: false,
  tumble_dry: false,
  ironing: false,

  is_repairable: false,
  repair_comment: "",
});

/** inline field errors (same UX as other steps) */
const fieldErrors = reactive({
  delivery_country_id: "",
  delivery_date: "",
  repair_comment: "",
});

function fieldErr(k) {
  return fieldErrors[k] || "";
}
function clearErr(k) {
  fieldErrors[k] = "";
}
function resetErrs() {
  Object.keys(fieldErrors).forEach((k) => (fieldErrors[k] = ""));
}

function onToggleRepairable() {
  if (!form.is_repairable) {
    form.repair_comment = "";
    clearErr("repair_comment");
  }
}

/** payload */
function buildPayload() {
  const wt =
    form.washing_temperature === "" || form.washing_temperature === null
      ? null
      : Number(form.washing_temperature);

  return {
    brand: form.brand?.trim() ? form.brand.trim() : null,

    delivery_country_id: form.delivery_country_id ?? null,
    delivery_date: form.delivery_date || null,

    washing_temperature: Number.isFinite(wt) ? wt : null,

    hand_wash: !!form.hand_wash,
    machine_wash: !!form.machine_wash,
    dry_clean: !!form.dry_clean,
    bleach: !!form.bleach,
    dry_shade: !!form.dry_shade,
    tumble_dry: !!form.tumble_dry,
    ironing: !!form.ironing,

    is_repairable: !!form.is_repairable,
    repair_comment: form.is_repairable ? (form.repair_comment?.trim() || null) : null,
  };
}

/** validation */
function frontValidate() {
  resetErrs();
  let ok = true;

  if (!form.delivery_country_id) {
    fieldErrors.delivery_country_id = "Delivery country is required";
    ok = false;
  }

  if (!form.delivery_date) {
    fieldErrors.delivery_date = "Delivery date is required";
    ok = false;
  }

  if (form.is_repairable && !form.repair_comment?.trim()) {
    fieldErrors.repair_comment = "Repair comment is required when repairable is enabled";
    ok = false;
  }

  return ok;
}

async function fetchCountries() {
  const res = await axios.get(`${API_BASE_URL}/countries`, { headers: authHeaders() });
  countries.value = res.data?.data ?? res.data ?? [];
}

async function fetchUsage() {
  const res = await axios.get(`${API_BASE_URL}/products/${props.productId}/usage`, {
    headers: authHeaders(),
  });

  const u = res.data?.data;
  if (!u) return;

  form.brand = u.brand ?? "";
  form.delivery_country_id = u.delivery_country_id ?? null;
  form.delivery_date = u.delivery_date ?? "";

  form.washing_temperature =
    u.washing_temperature === null || u.washing_temperature === undefined ? 30 : Number(u.washing_temperature);

  form.hand_wash = !!u.hand_wash;
  form.machine_wash = !!u.machine_wash;
  form.dry_clean = !!u.dry_clean;
  form.bleach = !!u.bleach;
  form.dry_shade = !!u.dry_shade;
  form.tumble_dry = !!u.tumble_dry;
  form.ironing = !!u.ironing;

  form.is_repairable = !!u.is_repairable;
  form.repair_comment = u.repair_comment ?? "";
}

async function upsert() {
  await axios.post(`${API_BASE_URL}/products/${props.productId}/usage`, buildPayload(), {
    headers: authHeaders(),
  });
}

function extractError(e, fallback) {
  const data = e?.response?.data;
  if (data?.message) return data.message;

  if (data?.errors && typeof data.errors === "object") {
    const firstKey = Object.keys(data.errors)[0];
    const firstMsg = Array.isArray(data.errors[firstKey]) ? data.errors[firstKey][0] : data.errors[firstKey];
    return firstMsg || fallback;
  }

  return fallback;
}

function applyBackend422(e) {
  const bag = e?.response?.data?.errors;
  if (!bag) return;

  // map known fields if present
  if (bag.delivery_country_id) fieldErrors.delivery_country_id = bag.delivery_country_id[0] || "Invalid value";
  if (bag.delivery_date) fieldErrors.delivery_date = bag.delivery_date[0] || "Invalid value";
  if (bag.repair_comment) fieldErrors.repair_comment = bag.repair_comment[0] || "Invalid value";
}

async function saveProgress() {
  error.value = "";
  if (!frontValidate()) {
    error.value = "Please fix the highlighted fields.";
    return;
  }

  busy.value = true;
  try {
    await upsert();

    const res = await axios.post(
      `${API_BASE_URL}/products/${props.productId}/usage/save-progress`,
      {},
      { headers: authHeaders() }
    );

    emit("update", { status: "draft", volet: 8, backend: res.data });
    showToast(res.data?.message || "Progress saved", "success");
  } catch (e) {
    if (e?.response?.status === 422) applyBackend422(e);
    error.value = extractError(e, "Save progress failed");
    showToast("Save failed", "error");
  } finally {
    busy.value = false;
  }
}

async function validateStep() {
  error.value = "";
  if (!frontValidate()) {
    error.value = "Please fix the highlighted fields.";
    return;
  }

  busy.value = true;
  try {
    await upsert();

    const res = await axios.post(
      `${API_BASE_URL}/products/${props.productId}/usage/validate-step`,
      {},
      { headers: authHeaders() }
    );

    emit("update", { status: "completed", volet: 8, backend: res.data });
    emit("next");
    showToast(res.data?.message || "Step validated", "success");
  } catch (e) {
    if (e?.response?.status === 422) applyBackend422(e);
    error.value = extractError(e, "Validation failed");
    showToast("Validation failed", "error");
  } finally {
    busy.value = false;
  }
}

onMounted(async () => {
  loadingInit.value = true;
  error.value = "";
  try {
    await fetchCountries();
    await fetchUsage();
  } catch (e) {
    error.value = extractError(e, "Failed to load usage data");
  } finally {
    loadingInit.value = false;
  }
});
</script>

<style scoped>
/* ✅ SAME THEME AS YOUR OTHER STEPS */
.u-page { max-width: 1020px; margin: 0 auto; padding: 12px; }

.page-header { margin-bottom: 18px; }
.page-title { margin: 0; font-size: 28px; font-weight: 900; color: #0f172a; letter-spacing: -0.02em; }
.page-subtitle { margin: 6px 0 0; color: #64748b; font-size: 15px; font-weight: 600; }

.divider { height: 1px; background: #e2e8f0; margin: 18px 0 24px; }
.divider-soft { height: 1px; background: #eef2f7; margin: 16px 0; }

/* Alerts */
.u-alert{
  border-radius:12px;
  padding:14px 16px;
  font-weight:800;
  border:1.5px solid #e2e8f0;
  background:#f8fafc;
  color:#0f172a;
}
.u-alert.error{ background:#fff1f2; border-color:#fecaca; color:#991b1b; }
.u-alert.info{ background:#eff6ff; border-color:#bfdbfe; color:#1e40af; }

/* Cards */
.card{
  background:#fff;
  border:1.5px solid #e2e8f0;
  border-radius:14px;
  padding:18px;
  margin-bottom:16px;
}
.card-title{
  display:flex; align-items:center; gap:10px;
  font-weight:900; color:#0f172a;
  margin-bottom:14px;
}
.icon{
  width:34px; height:34px;
  display:flex; align-items:center; justify-content:center;
  border-radius:10px;
  background:#f1f5f9;
}

/* Grids */
.grid-2{ display:grid; grid-template-columns:1fr 1fr; gap:14px; }
.grid-1{ display:grid; grid-template-columns:1fr; gap:14px; }
@media (max-width: 820px){ .grid-2{ grid-template-columns:1fr; } }

/* Fields */
.field{ display:flex; flex-direction:column; }
.label{ font-size:13px; font-weight:900; color:#0f172a; margin-bottom:8px; }
.req{ color:#ef4444; }

.input{
  width:100%;
  border:1.5px solid #e2e8f0;
  border-radius:10px;
  padding:11px 14px;
  outline:none;
  font-size:14px;
  font-weight:700;
  background:#fff;
  transition:.2s;
}
.input:focus{ border-color:#0ea5e9; box-shadow:0 0 0 3px rgba(14,165,233,.12); }
.input.error{ border-color:#ef4444; }
.textarea{ resize:vertical; min-height: 90px; }

.select-wrap{ position:relative; }
.select{ appearance:none; padding-right:40px; }
.select-icon{
  position:absolute; right:12px; top:50%;
  transform:translateY(-50%);
  color:#64748b; pointer-events:none;
  font-weight:900;
}

.err{ margin:6px 0 0; color:#ef4444; font-weight:800; font-size:12px; }
.hint{ margin:8px 0 0; color:#64748b; font-size:12px; font-weight:700; }

/* Toggles grid */
.toggles{
  display:grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap:12px;
}
@media (max-width: 900px){ .toggles{ grid-template-columns:1fr 1fr; } }
@media (max-width: 520px){ .toggles{ grid-template-columns:1fr; } }

.toggle-box{
  display:flex; align-items:center; justify-content:space-between;
  padding:12px 14px;
  border-radius:12px;
  background:#f8fafc;
  border:1px solid #eef2f7;
}
.toggle-left{ display:flex; flex-direction:column; gap:2px; }
.toggle-label{ font-weight:900; color:#0f172a; }
.toggle-sub{ color:#64748b; font-size:12px; font-weight:700; }

/* Repair row */
.repair-row{
  display:flex;
  align-items:center;
  justify-content:space-between;
  padding:14px;
  border-radius:12px;
  background:#f8fafc;
  border:1px solid #eef2f7;
}
.repair-label{ font-weight:900; color:#0f172a; }
.repair-sub{ margin-top:4px; font-size:12px; color:#64748b; font-weight:700; }
.mt-12{ margin-top:12px; }

/* Actions (same as other steps) */
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
.btn-primary{ background:#0f766e; border-color:#0f766e; color:#fff; }
.btn-primary:hover{ background:#0b5f58; }
.btn-light:hover, .btn-secondary:hover{ background:#f8fafc; }
.btn-secondary:disabled, .btn-light:disabled, .btn-primary:disabled{ opacity:.5; cursor:not-allowed; }

@media (max-width: 900px){
  .actions{ flex-direction:column; align-items:stretch; }
  .right-actions{ flex-direction:column; align-items:stretch; }
  .btn-secondary, .btn-light, .btn-primary{ width:100%; justify-content:center; }
}

/* Toast */
.toast{
  margin-top:12px;
  padding:10px 12px;
  border-radius:12px;
  background:#ecfdf5;
  border:1px solid #a7f3d0;
  color:#065f46;
  font-weight:900;
}
.toast.error{
  background:#fff1f2;
  border-color:#fecaca;
  color:#991b1b;
}

/* Switch (same) */
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
</style>
