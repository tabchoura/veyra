<template>
  <div class="page">
    <div class="header">
      <h2>bAwear Score</h2>
      <p>Connect your bAwear sustainability assessment</p>
      <div class="divider"></div>
    </div>

    <div v-if="loadingInit" class="state">Loading…</div>

    <div v-else>
      <p v-if="error" class="error">{{ error }}</p>

      <!-- PDF Upload Section -->
      <div class="card">
        <div class="card-title">
          <span class="icon">📄</span>
          <span>bAwear Report Upload</span>
        </div>

        <div class="upload-section">
          <label class="upload-area" @dragover.prevent @drop.prevent="onDrop">
            <input type="file" accept="application/pdf" @change="onFileChange" hidden />
            <div class="upload-content">
              <div class="upload-icon">📄</div>
              <div class="upload-title">Upload PDF Report</div>
              <div class="upload-subtitle">Drag & drop or click to browse</div>
              <div class="upload-hint">PDF up to 10MB</div>

              <div v-if="fileName" class="file-badge">
                <span class="file-name">{{ fileName }}</span>
                <button type="button" class="file-remove" @click.stop="clearFile">×</button>
              </div>
            </div>
          </label>
        </div>
      </div>

      <!-- Environmental Indicators -->
      <div class="card">
        <div class="card-title">
          <span class="icon">🌍</span>
          <span>Environmental Indicators</span>
        </div>

        <div class="info-box">
          <p>Enter the values from your bAwear report (per kg of product)</p>
        </div>

        <div class="grid-2">
          <div class="field">
            <label>Global Warming (kg CO₂-eq) <span class="req">*</span></label>
            <input
              type="number"
              step="0.01"
              min="0"
              v-model.number="form.global_warming"
              placeholder="e.g. 12.50"
            />
          </div>

          <div class="field">
            <label>Fossil Energy (MJ) <span class="req">*</span></label>
            <input
              type="number"
              step="0.01"
              min="0"
              v-model.number="form.fossil_energy"
              placeholder="e.g. 30.10"
            />
          </div>

          <div class="field">
            <label>Water Use (Liter) <span class="req">*</span></label>
            <input
              type="number"
              step="0.01"
              min="0"
              v-model.number="form.water_use"
              placeholder="e.g. 45.00"
            />
          </div>

          <div class="field">
            <label>Land Use (m²) <span class="req">*</span></label>
            <input
              type="number"
              step="0.01"
              min="0"
              v-model.number="form.land_use"
              placeholder="e.g. 1.20"
            />
          </div>
        </div>
      </div>

      <!-- Summary -->
      <div class="summary" v-if="hasData">
        <div class="summary-title">Assessment Summary</div>
        <div class="summary-grid">
          <div class="summary-item" v-if="form.global_warming">
            <span class="summary-label">CO₂ Equivalent:</span>
            <span class="summary-value">{{ form.global_warming }} kg</span>
          </div>
          <div class="summary-item" v-if="form.fossil_energy">
            <span class="summary-label">Fossil Energy:</span>
            <span class="summary-value">{{ form.fossil_energy }} MJ</span>
          </div>
          <div class="summary-item" v-if="form.water_use">
            <span class="summary-label">Water Usage:</span>
            <span class="summary-value">{{ form.water_use }} L</span>
          </div>
          <div class="summary-item" v-if="form.land_use">
            <span class="summary-label">Land Usage:</span>
            <span class="summary-value">{{ form.land_use }} m²</span>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="footer">
        <button class="btn secondary" @click="goPrev" :disabled="busy">‹ Previous</button>

        <div class="right">
          <button class="btn light" @click="save" :disabled="busy">
            {{ busy ? "Saving..." : "💾 Save Progress" }}
          </button>
          <button class="btn primary" @click="next" :disabled="busy">Next Step ›</button>
        </div>
      </div>

      <div v-if="toast" class="toast">{{ toast }}</div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref, computed } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "axios";

// ✅ Configure API base URL
const API_BASE_URL = import.meta.env.VITE_API_URL || "http://localhost:8000/api";

// Helper to get auth token
function getToken() {
  return (
    localStorage.getItem("token") ||
    localStorage.getItem("auth_token") ||
    localStorage.getItem("access_token") ||
    localStorage.getItem("authToken") ||
    localStorage.getItem("accessToken")
  );
}

// Helper to create auth headers
function authHeaders() {
  const token = getToken();
  const headers = { Accept: "application/json" };
  if (token) headers.Authorization = `Bearer ${token}`;
  return headers;
}

/**
 * ✅ productId can come from:
 * 1. Props (when used inside CreatePasseport parent component)
 * 2. URL query (when accessed directly via route)
 */
const props = defineProps({
  productId: { type: [Number, String], required: false },
});

const emit = defineEmits(["next", "previous", "update"]);

const route = useRoute();
const router = useRouter();

// Try multiple sources for productId
const productId = computed(() => {
  // 1. Try from props first (preferred when inside parent component)
  if (props.productId) {
    console.log('✅ productId from props:', props.productId);
    return props.productId;
  }
  
  // 2. Try from route query
  if (route.query.productId) {
    console.log('✅ productId from route.query:', route.query.productId);
    return route.query.productId;
  }
  
  // 3. Try from URL path (if format is /passports/bawearscore/PROD-123)
  const pathMatch = window.location.pathname.match(/PROD-[A-Z0-9]+/);
  if (pathMatch) {
    console.log('✅ ProductId from URL path:', pathMatch[0]);
    return pathMatch[0];
  }
  
  // 4. Try from localStorage (fallback)
  const stored = localStorage.getItem('currentProductId');
  if (stored) {
    console.log('✅ ProductId from localStorage:', stored);
    return stored;
  }
  
  console.error('❌ ProductId not found anywhere');
  return null;
});

const form = ref({
  score: 0,
  global_warming: null,
  fossil_energy: null,
  water_use: null,
  land_use: null,
});

const file = ref(null);
const fileName = ref("");

const busy = ref(false);
const loadingInit = ref(true);
const error = ref("");
const toast = ref("");
const latest = ref(null);

const hasData = computed(() => {
  return (
    form.value.global_warming ||
    form.value.fossil_energy ||
    form.value.water_use ||
    form.value.land_use
  );
});

function showToast(msg) {
  toast.value = msg;
  setTimeout(() => (toast.value = ""), 2200);
}

function onFileChange(e) {
  const f = e.target.files?.[0];
  if (!f) return;

  if (f.type !== "application/pdf") {
    error.value = "Please upload a PDF file only.";
    return;
  }
  if (f.size > 10 * 1024 * 1024) {
    error.value = "File too large (max 10MB).";
    return;
  }
  file.value = f;
  fileName.value = f.name;
  error.value = "";
}

function onDrop(e) {
  const f = e.dataTransfer.files?.[0];
  if (!f) return;
  onFileChange({ target: { files: [f] } });
}

function clearFile() {
  file.value = null;
  fileName.value = "";
}

async function fetchLatest() {
  console.log('🔍 DEBUG fetchLatest - productId:', productId.value);
  
  if (!productId.value) {
    console.error('❌ Missing productId');
    error.value = "Missing product ID. Please navigate from the product creation flow.";
    return;
  }

  error.value = "";
  try {
    const { data } = await axios.get(`${API_BASE_URL}/products/${productId.value}/bawear`, {
      headers: authHeaders(),
    });
    latest.value = data.data;
    console.log('✅ Data loaded:', latest.value);

    // Pre-fill score
    if (latest.value?.score_value != null) {
      form.value.score = Number(latest.value.score_value);
    }

    // Pre-fill indicators from normalized_payload if available
    const ind = latest.value?.normalized_payload?.indicators;
    if (ind) {
      form.value.global_warming = ind.global_warming ?? form.value.global_warming;
      form.value.fossil_energy = ind.fossil_energy ?? form.value.fossil_energy;
      form.value.water_use = ind.water_use ?? form.value.water_use;
      form.value.land_use = ind.land_use ?? form.value.land_use;
    }
  } catch (e) {
    console.error('❌ Error fetching data:', e);
    if (e.response?.status === 404) {
      // No existing assessment - that's OK
      console.log('ℹ️ No existing bAwear assessment found');
    } else {
      error.value = e?.response?.data?.message || e.message || "Error loading data.";
    }
  }
}

function buildNormalizedPayload() {
  return {
    indicators: {
      global_warming: form.value.global_warming,
      fossil_energy: form.value.fossil_energy,
      water_use: form.value.water_use,
      land_use: form.value.land_use,
    },
  };
}

function frontValidate() {
  if (!form.value.global_warming) return "Global Warming value is required.";
  if (!form.value.fossil_energy) return "Fossil Energy value is required.";
  if (!form.value.water_use) return "Water Use value is required.";
  if (!form.value.land_use) return "Land Use value is required.";
  return "";
}

async function save() {
  console.log('💾 DEBUG save - productId:', productId.value);
  
  const v = frontValidate();
  if (v) {
    error.value = v;
    return;
  }

  error.value = "";
  busy.value = true;

  try {
    if (!productId.value) {
      console.error('❌ Missing productId in save()');
      throw new Error("Missing productId.");
    }

    // If PDF is present -> upload (creates new assessment)
    if (file.value) {
      const fd = new FormData();
      fd.append("pdf", file.value);

      const res = await axios.post(
        `${API_BASE_URL}/products/${productId.value}/bawear/pdf`,
        fd,
        { 
          headers: { 
            ...authHeaders(),
            "Content-Type": "multipart/form-data" 
          } 
        }
      );

      const assessmentId = res.data.data.id;

      await axios.patch(
        `${API_BASE_URL}/products/${productId.value}/bawear/${assessmentId}`, 
        {
          normalized_payload: buildNormalizedPayload(),
          score_value: form.value.score,
          score_unit: "/100",
          status: "processed",
        },
        { headers: authHeaders() }
      );

      clearFile();
    } else {
      // No pdf: must have existing assessment to update
      if (!latest.value?.id) {
        throw new Error("Please upload a PDF report first (no existing record).");
      }

      await axios.patch(
        `${API_BASE_URL}/products/${productId.value}/bawear/${latest.value.id}`, 
        {
          normalized_payload: buildNormalizedPayload(),
          score_value: form.value.score,
          score_unit: "/100",
          status: "processed",
        },
        { headers: authHeaders() }
      );
    }

    await fetchLatest();
    emit("update", { status: "draft", volet: 10 });
    showToast("Progress saved ✅");
    console.log('✅ Save successful');
  } catch (e) {
    console.error('❌ Save error:', e);
    error.value = e?.response?.data?.message || e.message || "Error while saving.";
  } finally {
    busy.value = false;
  }
}

async function next() {
  console.log('➡️ DEBUG next - productId:', productId.value);
  
  await save();
  if (error.value) {
    console.error('❌ Cannot proceed to next step - save failed');
    return;
  }

  emit("update", { status: "completed", volet: 10 });
  emit("next");
  showToast("Step validated ✅");
}

function goPrev() {
  console.log('⬅️ DEBUG goPrev - productId:', productId.value);
  emit("previous");
}

onMounted(async () => {
  console.log('🚀 BawearScore mounted - productId:', productId.value);
  
  loadingInit.value = true;
  error.value = "";
  try {
    await fetchLatest();
  } catch (e) {
    console.error('❌ Error during initialization:', e);
    error.value = "Failed to load bAwear assessment data.";
  } finally {
    loadingInit.value = false;
  }
});
</script>
<style>
/* ✅ SAME THEME AS EndOfLife / Usage / Manufacturing */

.page{ max-width:1020px; margin:0 auto; padding:12px; }

.header{ margin-bottom: 14px; }
.header h2{
  margin:0;
  font-size:28px;
  font-weight:900;
  color:#0f172a;
  letter-spacing:-0.02em;
}
.header p{
  margin:6px 0 0;
  color:#64748b;
  font-weight:700;
}
.divider{ height:1px; background:#e2e8f0; margin: 16px 0 18px; }

.state{
  border-radius:12px;
  padding:14px 16px;
  font-weight:900;
  border:1.5px solid #e2e8f0;
  background:#f8fafc;
  color:#0f172a;
}

.error{
  border-radius:12px;
  padding:14px 16px;
  font-weight:900;
  border:1.5px solid #fecaca;
  background:#fff1f2;
  color:#991b1b;
  margin-bottom: 14px;
}

/* Card */
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
  font-size:18px;
}

/* Info box */
.info-box{
  background:#f8fafc;
  border:1.5px solid #e2e8f0;
  border-radius:12px;
  padding:12px 14px;
  margin-bottom:14px;
}
.info-box p{
  margin:0;
  color:#64748b;
  font-size:13px;
  font-weight:700;
}

/* Upload */
.upload-section{ margin-top: 8px; }

.upload-area{
  cursor:pointer;
  border:2px dashed #cbd5e1;
  border-radius:14px;
  min-height:180px;
  display:flex;
  align-items:center;
  justify-content:center;
  padding:20px;
  transition:.2s;
  background:#f8fafc;
}
.upload-area:hover{
  border-color:#94a3b8;
  background:#f1f5f9;
}

.upload-content{ text-align:center; width:100%; }

.upload-icon{
  width:56px;
  height:56px;
  border-radius:50%;
  background:#dcfce7;
  display:flex;
  align-items:center;
  justify-content:center;
  margin:0 auto 12px;
  font-size:24px;
  color:#166534;
}

.upload-title{
  font-weight:900;
  color:#0f172a;
  font-size:16px;
  margin-bottom:4px;
}
.upload-subtitle{
  color:#64748b;
  font-size:13px;
  font-weight:700;
  margin-bottom:6px;
}
.upload-hint{
  color:#94a3b8;
  font-size:12px;
  font-weight:700;
}

/* File badge */
.file-badge{
  margin-top:14px;
  display:inline-flex;
  align-items:center;
  gap:10px;
  padding:10px 14px;
  background:#ecfdf5;
  border:1.5px solid #a7f3d0;
  border-radius:10px;
  font-size:13px;
  font-weight:800;
  color:#065f46;
}
.file-name{
  max-width:300px;
  overflow:hidden;
  text-overflow:ellipsis;
  white-space:nowrap;
}
.file-remove{
  border:none;
  background:transparent;
  cursor:pointer;
  font-size:20px;
  line-height:1;
  color:#059669;
  font-weight:900;
  padding:0 4px;
  transition:.2s;
}
.file-remove:hover{
  color:#047857;
  transform:scale(1.15);
}

/* Form grid */
.grid-2{ display:grid; grid-template-columns: 1fr 1fr; gap:14px; }
@media (max-width: 820px){ .grid-2{ grid-template-columns:1fr; } }

.field label{
  display:block;
  font-size:13px;
  font-weight:900;
  color:#0f172a;
  margin-bottom:8px;
}
.req{ color:#ef4444; }

.field input, .field select, .field textarea{
  width:100%;
  border:1.5px solid #e2e8f0;
  border-radius:10px;
  padding:11px 14px;
  outline:none;
  font-size:14px;
  font-weight:700;
  color:#0f172a;
  background:#fff;
  transition:.2s;
}
.field input:focus, .field select:focus, .field textarea:focus{
  border-color:#0ea5e9;
  box-shadow:0 0 0 3px rgba(14,165,233,.12);
}
.field input::placeholder{
  color:#cbd5e1;
  font-weight:600;
}

/* Summary */
.summary{
  border:1.5px solid #a7f3d0;
  background:#ecfdf5;
  border-radius:14px;
  padding:18px;
  margin: 10px 0 18px;
}
.summary-title{
  font-weight:900;
  color:#0f172a;
  margin-bottom:12px;
  font-size:14px;
}
.summary-grid{
  display:grid;
  grid-template-columns: 1fr 1fr;
  gap:10px;
}
@media (max-width: 640px){ .summary-grid{ grid-template-columns:1fr; } }

.summary-item{
  display:flex;
  justify-content:space-between;
  align-items:center;
  padding:10px 12px;
  background:#d1fae5;
  border-radius:10px;
}
.summary-label{
  font-weight:800;
  color:#065f46;
  font-size:13px;
}
.summary-value{
  font-weight:900;
  color:#047857;
  font-size:14px;
}

/* Footer / Buttons */
.footer{
  display:flex;
  justify-content:space-between;
  align-items:center;
  gap:12px;
  margin-top: 10px;
}
.right{ display:flex; gap:12px; align-items:center; }

.btn{
  display:inline-flex;
  align-items:center;
  justify-content:center;
  height:44px;
  padding:0 18px;
  border-radius:10px;
  font-weight:900;
  cursor:pointer;
  transition:.2s;
  border:1.5px solid #e2e8f0;
  background:#fff;
  font-size:14px;
}
.btn:disabled{ opacity:.55; cursor:not-allowed; }

.btn.secondary:hover:not(:disabled),
.btn.light:hover:not(:disabled){
  background:#f8fafc;
}

.btn.primary{
  background:#0f766e;
  border-color:#0f766e;
  color:#fff;
}
.btn.primary:hover:not(:disabled){ background:#0b5f58; }

@media (max-width: 900px){
  .footer{ flex-direction:column; align-items:stretch; }
  .right{ width:100%; flex-direction:column; align-items:stretch; }
  .btn{ width:100%; }
}

/* Toast */
.toast{
  margin-top:12px;
  padding:10px 12px;
  border-radius:12px;
  background:#ecfdf5;
  border:1.5px solid #a7f3d0;
  color:#065f46;
  font-weight:900;
  font-size:14px;
}
</style>