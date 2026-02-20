<template>
  <div class="step-wrap">
    <div class="title">
      <h2>Fabric Information</h2>
      <p>Define the fabric specifications and treatments</p>
      <div class="divider"></div>
    </div>

    <div v-if="fabrics.length === 0" class="empty-add" @click="addFabric">
      <span class="plus">+</span>
      <span>Add Fabric</span>
    </div>

    <div v-else>
      <div class="banner" :class="{ ok: isComplete }">
        <div class="banner-left">
          <span class="icon">!</span>
          <span class="text">Total Composition: {{ total.toFixed(1) }}%</span>
        </div>
      </div>

      <div v-for="(f, idx) in fabrics" :key="f.localId" class="card">
        <div class="card-header">
          <h3>Fabric #{{ idx + 1 }}</h3>
          <button v-if="fabrics.length > 1" class="remove" type="button" @click="removeFabric(idx)">
            🗑 <span>Remove</span>
          </button>
        </div>

        <div class="grid-2">
          <!-- Producing Country (required) -->
          <div class="field">
            <label>Producing Country <span class="req">*</span></label>
            <select v-model="f.producing_country_id">
              <option :value="null" disabled>Select country</option>
              <option v-for="c in countries" :key="c.id" :value="c.id">
                {{ c.name_en || c.name }}
              </option>
            </select>
          </div>

          <!-- Fabric Type (required) -->
          <div class="field">
            <label>Fabric Type <span class="req">*</span></label>
            <select v-model="f.fabric_type_id">
              <option :value="null" disabled>Select type</option>
              <option v-for="t in fabricTypes" :key="t.id" :value="t.id">
                {{ t.name }}
              </option>
            </select>
          </div>

          <!-- Percentage (required) -->
          <div class="field">
            <label>Percentage <span class="req">*</span></label>
            <input
              v-model.number="f.percentage"
              type="number"
              min="0"
              max="100"
              step="0.1"
              inputmode="decimal"
            />
          </div>

          <!-- optional -->
          <div class="field">
            <label>Production Date</label>
            <input v-model="f.production_date" type="date" />
          </div>
        </div>

        <div class="grid-2">
          <div class="field">
            <label>Producing Organization</label>
            <input v-model="f.producing_organization" type="text" placeholder="Optional" />
          </div>

          <div class="field">
            <label>Address</label>
            <input v-model="f.address" type="text" placeholder="Optional" />
          </div>

          <div class="field">
            <label>Postal Code</label>
            <input v-model="f.postal_code" type="text" placeholder="Optional" />
          </div>
        </div>

        <!-- Dyeing -->
        <div class="toggle-row">
          <div class="toggle-left"><label>Dyeing Applied?</label></div>
          <label class="switch">
            <input type="checkbox" v-model="f.has_dyeing" @change="onToggleDyeing(f)" />
            <span class="slider"></span>
          </label>
        </div>

        <div v-if="f.has_dyeing" class="field">
          <label>Dyeing Method <span class="req">*</span></label>
          <select v-model="f.colouring_method_id">
            <option :value="null" disabled>Select dyeing method</option>
            <option v-for="m in colouringMethods" :key="m.id" :value="m.id">
              {{ m.name }}
            </option>
          </select>
        </div>

        <!-- Finishing -->
        <div class="toggle-row">
          <div class="toggle-left"><label>Finishing Applied?</label></div>
          <label class="switch">
            <input type="checkbox" v-model="f.has_finishing" @change="onToggleFinishing(f)" />
            <span class="slider"></span>
          </label>
        </div>

        <div v-if="f.has_finishing" class="field">
          <label>Finishing Method <span class="req">*</span></label>
          <select v-model="f.finishing_method_id">
            <option :value="null" disabled>Select finishing method</option>
            <option v-for="m in finishingMethods" :key="m.id" :value="m.id">
              {{ m.name }}
            </option>
          </select>
        </div>

        <div class="section">
          <h4>ZDHC Certifications</h4>

          <div class="zdhc-row">
            <div class="zdhc-item">
              <span>Supply to Zero</span>
              <label class="switch">
                <input type="checkbox" v-model="f.zdhc_supply_to_zero" />
                <span class="slider"></span>
              </label>
            </div>

            <div class="zdhc-item">
              <span>GetZD</span>
              <label class="switch">
                <input type="checkbox" v-model="f.zdhc_get_zd" />
                <span class="slider"></span>
              </label>
            </div>
          </div>
        </div>

        <div class="section">
          <h4>Environmental Metrics</h4>

          <div class="metric">
            <div class="metric-head">
              <span>Renewable Energy</span>
              <span class="pct">{{ f.renewable_energy_percentage }}%</span>
            </div>
            <input v-model.number="f.renewable_energy_percentage" type="range" min="0" max="100" />
          </div>

          <div class="metric">
            <div class="metric-head">
              <span>Recycled Water</span>
              <span class="pct">{{ f.recycled_water_percentage }}%</span>
            </div>
            <input v-model.number="f.recycled_water_percentage" type="range" min="0" max="100" />
          </div>
        </div>
      </div>

      <div class="empty-add" @click="addFabric">
        <span class="plus">+</span>
        <span>Add Fabric</span>
      </div>
    </div>

    <div class="footer">
      <button class="btn secondary" type="button" @click="$emit('previous')">‹ Previous</button>

      <div class="right">
        <button class="btn light" type="button" @click="saveProgress" :disabled="loading">
          💾 Save Progress
        </button>
        <button class="btn primary" type="button" @click="validateStep" :disabled="loading">
          Next Step ›
        </button>
      </div>
    </div>

    <p v-if="error" class="error">{{ error }}</p>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from "vue";
import axios from "axios";

const props = defineProps({
  productId: { type: [Number, String], required: true },
});

const emit = defineEmits(["next", "previous", "update"]);

const API_BASE_URL = import.meta.env.VITE_API_URL || "http://localhost:8000/api";

const fabrics = ref([]);
const countries = ref([]);
const fabricTypes = ref([]);
const colouringMethods = ref([]);
const finishingMethods = ref([]);
const loading = ref(false);
const error = ref("");

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
  return token
    ? { Authorization: `Bearer ${token}`, Accept: "application/json" }
    : { Accept: "application/json" };
}

const total = computed(() =>
  fabrics.value.reduce((s, f) => s + (Number(f.percentage) || 0), 0)
);

const isComplete = computed(() => Math.abs(total.value - 100) <= 0.01);

function key() {
  return (
    (globalThis.crypto?.randomUUID?.() || Math.random().toString(36).slice(2)) + Date.now()
  );
}

function blankFabric() {
  return {
    localId: key(),
    id: null,

    producing_country_id: null,
    fabric_type_id: null,

    percentage: 0,
    production_date: null,

    producing_organization: "",
    address: "",
    postal_code: "",

    has_dyeing: false,
    colouring_method_id: null,

    has_finishing: false,
    finishing_method_id: null,

    renewable_energy_percentage: 0,
    recycled_water_percentage: 0,

    zdhc_supply_to_zero: false,
    zdhc_get_zd: false,
  };
}

function addFabric() {
  fabrics.value.push(blankFabric());
}

function onToggleDyeing(f) {
  if (!f.has_dyeing) f.colouring_method_id = null;
}

function onToggleFinishing(f) {
  if (!f.has_finishing) f.finishing_method_id = null;
}

async function removeFabric(index) {
  const f = fabrics.value[index];

  if (f?.id) {
    loading.value = true;
    error.value = "";
    try {
      await axios.delete(`${API_BASE_URL}/products/${props.productId}/fabrics/${f.id}`, {
        headers: authHeaders(),
      });
    } catch (e) {
      error.value = e?.response?.data?.message || "Delete failed";
      loading.value = false;
      return;
    } finally {
      loading.value = false;
    }
  }

  fabrics.value.splice(index, 1);

  if (fabrics.value.length === 0) addFabric();
}

async function fetchCountries() {
  try {
    const res = await axios.get(`${API_BASE_URL}/countries`, {
      headers: authHeaders(),
    });
    countries.value = res.data?.data || [];
  } catch (e) {
    console.error("Failed to load countries:", e);
    countries.value = [];
  }
}

/** ✅ CORRIGÉ: /fabric-types, /colouring-methods, /finishing-methods au lieu de /reference/... */
async function fetchRefs() {
  try {
    const typesRes = await axios.get(`${API_BASE_URL}/fabric-types`, { 
      headers: authHeaders() 
    });
    fabricTypes.value = typesRes.data?.data || [];

    const dyeRes = await axios.get(`${API_BASE_URL}/colouring-methods`, { 
      headers: authHeaders() 
    });
    colouringMethods.value = dyeRes.data?.data || [];

    const finishRes = await axios.get(`${API_BASE_URL}/finishing-methods`, { 
      headers: authHeaders() 
    });
    finishingMethods.value = finishRes.data?.data || [];

  } catch (e) {
    console.error("Failed to load references:", e);
    error.value = e?.response?.data?.message || "Failed to load reference data";
  }
}

async function fetchFabrics() {
  try {
    const res = await axios.get(`${API_BASE_URL}/products/${props.productId}/fabrics`, {
      headers: authHeaders(),
    });

    const data = res.data?.data || [];
    fabrics.value = data.map((f) => ({
      ...blankFabric(),
      ...f,
      localId: f?.id ? String(f.id) : key(),

      producing_country_id: f?.producing_country_id ?? null,
      fabric_type_id: f?.fabric_type_id ?? null,

      colouring_method_id: f?.colouring_method_id ?? null,
      finishing_method_id: f?.finishing_method_id ?? null,

      has_dyeing: !!f?.has_dyeing,
      has_finishing: !!f?.has_finishing,
    }));
  } catch (e) {
    console.error("Failed to load fabrics:", e);
    error.value = e?.response?.data?.message || "Failed to load fabrics";
  }
}

function validateFabricRow(f) {
  if (!f.producing_country_id) return false;
  if (!f.fabric_type_id) return false;

  const p = Number(f.percentage);
  if (!Number.isFinite(p) || p <= 0 || p > 100) return false;

  if (f.has_dyeing && !f.colouring_method_id) return false;
  if (f.has_finishing && !f.finishing_method_id) return false;

  return true;
}

async function upsertAll() {
  for (const f of fabrics.value) {
    if (!validateFabricRow(f)) {
      throw new Error(
        "Please complete required fields (Country, Fabric type, percentage, and selected methods if toggled)."
      );
    }

    const payload = {
      producing_country_id: f.producing_country_id,
      fabric_type_id: f.fabric_type_id,
      percentage: f.percentage,
      production_date: f.production_date || null,

      producing_organization: f.producing_organization || null,
      address: f.address || null,
      postal_code: f.postal_code || null,

      has_dyeing: f.has_dyeing ? 1 : 0,
      colouring_method_id: f.has_dyeing ? f.colouring_method_id : null,

      has_finishing: f.has_finishing ? 1 : 0,
      finishing_method_id: f.has_finishing ? f.finishing_method_id : null,

      renewable_energy_percentage: f.renewable_energy_percentage ?? 0,
      recycled_water_percentage: f.recycled_water_percentage ?? 0,

      zdhc_supply_to_zero: !!f.zdhc_supply_to_zero,
      zdhc_get_zd: !!f.zdhc_get_zd,
    };

    if (!f.id) {
      const created = await axios.post(
        `${API_BASE_URL}/products/${props.productId}/fabrics`,
        payload,
        { headers: authHeaders() }
      );
      f.id = created.data?.data?.id;
    } else {
      await axios.put(
        `${API_BASE_URL}/products/${props.productId}/fabrics/${f.id}`,
        payload,
        { headers: authHeaders() }
      );
    }
  }
}

async function saveProgress() {
  loading.value = true;
  error.value = "";
  try {
    await upsertAll();
    const res = await axios.post(
      `${API_BASE_URL}/products/${props.productId}/fabrics/save-progress`,
      {},
      { headers: authHeaders() }
    );
    emit("update", { status: "draft", volet: 5, backend: res.data });
    // alert(res.data?.message || "Progress saved successfully");
  } catch (e) {
    error.value = e?.message || e?.response?.data?.message || "Save progress failed";
    alert(error.value);
  } finally {
    loading.value = false;
  }
}


async function validateStep() {
  loading.value = true;
  error.value = "";
  try {
    await upsertAll();

    if (!isComplete.value) {
      throw new Error("Total Composition must equal 100% to validate this step.");
    }

    const res = await axios.post(
      `${API_BASE_URL}/products/${props.productId}/fabrics/validate-step`,
      {},
      { headers: authHeaders() }
    );
    emit("update", { status: "completed", volet: 5, backend: res.data });
    emit("next");
    alert(res.data?.message || "Step 5 completed successfully");
  } catch (e) {
    error.value = e?.message || e?.response?.data?.message || "Validation failed";
    alert(error.value);
  } finally {
    loading.value = false;
  }
}

onMounted(async () => {
  await Promise.all([fetchCountries(), fetchRefs()]);
  await fetchFabrics();

  if (fabrics.value.length === 0) addFabric();
});
</script>

<style scoped>
.step-wrap {
  max-width: 980px;
  margin: 0 auto;
}
.title h2 {
  font-size: 26px;
  font-weight: 800;
  margin: 0;
}
.title p {
  margin: 6px 0 14px;
  color: #64748b;
}
.divider {
  height: 1px;
  background: #e2e8f0;
  margin: 10px 0 18px;
}

.banner {
  background: #fff8e6;
  border: 1px solid #f6d48a;
  padding: 14px 16px;
  border-radius: 10px;
  margin-bottom: 18px;
}
.banner.ok {
  background: #ecfdf5;
  border-color: #a7f3d0;
}
.banner-left {
  display: flex;
  align-items: center;
  gap: 10px;
}
.icon {
  width: 22px;
  height: 22px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f59e0b;
  color: white;
  font-weight: 800;
}
.banner.ok .icon {
  background: #10b981;
}
.text {
  font-weight: 700;
  color: #92400e;
}
.banner.ok .text {
  color: #065f46;
}

.empty-add {
  border: 2px dashed #e2e8f0;
  border-radius: 10px;
  padding: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  cursor: pointer;
  user-select: none;
  margin-bottom: 18px;
}
.plus {
  font-size: 18px;
  font-weight: 900;
}

.card {
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 18px;
  margin-bottom: 16px;
}
.card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 14px;
}
.card-header h3 {
  margin: 0;
  font-size: 16px;
  font-weight: 800;
  color: #0f172a;
}
.remove {
  border: none;
  background: transparent;
  color: #ef4444;
  font-weight: 700;
  cursor: pointer;
  display: flex;
  gap: 8px;
  align-items: center;
}

.grid-2 {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 14px;
  margin-bottom: 10px;
}
.field label {
  display: block;
  font-weight: 700;
  margin-bottom: 8px;
  color: #0f172a;
}
.req {
  color: #ef4444;
}
.field input,
.field select {
  width: 100%;
  height: 42px;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 0 12px;
  outline: none;
}

.toggle-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-top: 1px solid #eef2f7;
  margin-top: 16px;
  padding-top: 14px;
}
.section {
  margin-top: 16px;
  border-top: 1px solid #eef2f7;
  padding-top: 14px;
}
.section h4 {
  margin: 0 0 10px;
  font-size: 14px;
  font-weight: 800;
  color: #0f172a;
}

.zdhc-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 14px;
}
.zdhc-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 12px;
  border-radius: 10px;
  background: #f8fafc;
}

.metric {
  margin: 12px 0;
}
.metric-head {
  display: flex;
  justify-content: space-between;
  margin-bottom: 6px;
  color: #0f172a;
  font-weight: 700;
}
.metric input[type="range"] {
  width: 100%;
}

.footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 18px;
}
.right {
  display: flex;
  gap: 12px;
  align-items: center;
}

.btn {
  height: 44px;
  border-radius: 10px;
  padding: 0 18px;
  border: 1px solid transparent;
  font-weight: 800;
  cursor: pointer;
}
.btn.secondary {
  background: #fff;
  border-color: #e2e8f0;
}
.btn.light {
  background: #fff;
  border-color: #e2e8f0;
}
.btn.primary {
  background: #0f766e;
  color: white;
}

.error {
  margin-top: 12px;
  color: #ef4444;
  font-weight: 700;
}

.switch {
  position: relative;
  display: inline-block;
  width: 44px;
  height: 24px;
}
.switch input {
  display: none;
}
.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: #e2e8f0;
  transition: 0.2s;
  border-radius: 999px;
}
.slider:before {
  position: absolute;
  content: "";
  height: 18px;
  width: 18px;
  left: 3px;
  top: 3px;
  background: white;
  transition: 0.2s;
  border-radius: 50%;
}
.switch input:checked + .slider {
  background: #10b981;
}
.switch input:checked + .slider:before {
  transform: translateX(20px);
}

@media (max-width: 768px) {
  .grid-2,
  .zdhc-row {
    grid-template-columns: 1fr;
  }
}
</style>