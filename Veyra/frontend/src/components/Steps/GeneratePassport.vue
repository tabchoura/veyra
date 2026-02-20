<template>
  <div class="gp-wrap">
    <div class="gp-card">
      <h1 class="gp-title">Generate Passport</h1>
      <p class="gp-sub">Configure access settings and generate your Digital Product Passport</p>

      <div v-if="ready" class="gp-alert ok">✅ All required fields are complete</div>

      <div v-if="isGenerated" class="gp-alert info">
        ℹ️ Passport generated on <b>{{ formatDate(generatedAt) }}</b>
      </div>

      <!-- Access Level -->
      <div class="gp-section">
        <h3 class="gp-h3">Access Level</h3>

        <div class="gp-levels">
          <label class="gp-level" :class="{active: form.accessLevel==='internal'}">
            <input type="radio" value="internal" v-model="form.accessLevel" />
            <div>
              <div class="gp-level-title">Internal Only</div>
              <div class="gp-level-desc">Only your organization can view this passport</div>
            </div>
          </label>

          <label class="gp-level" :class="{active: form.accessLevel==='partner'}">
            <input type="radio" value="partner" v-model="form.accessLevel" />
            <div>
              <div class="gp-level-title">Partner Access</div>
              <div class="gp-level-desc">Share with specific authorized partners</div>
            </div>
          </label>

          <label class="gp-level" :class="{active: form.accessLevel==='public'}">
            <input type="radio" value="public" v-model="form.accessLevel" />
            <div>
              <div class="gp-level-title">Public</div>
              <div class="gp-level-desc">Anyone with the link can view this passport</div>
            </div>
          </label>
        </div>
      </div>

      <!-- Partner Emails (only if partner) -->
      <div v-if="form.accessLevel==='partner'" class="gp-section gp-box">
        <div class="gp-row gp-between">
          <div>
            <div class="gp-h4">Authorized partner emails</div>
            <div class="gp-muted">Add at least 1 email</div>
          </div>
          <button class="gp-btn ghost" type="button" @click="addEmail">+ Add email</button>
        </div>

        <div v-for="(email, idx) in form.authorizedEmails" :key="idx" class="gp-email-row">
          <input class="gp-input" type="email" v-model.trim="form.authorizedEmails[idx]" placeholder="partner@example.com" />
          <button class="gp-btn danger" type="button" @click="removeEmail(idx)">Remove</button>
        </div>

        <div v-if="errors.partner" class="gp-error">{{ errors.partner }}</div>
      </div>

      <!-- QR -->
      <div class="gp-section gp-qr">
        <div class="gp-qr-left">
          <div class="gp-qr-ico">▦</div>
          <div>
            <div class="gp-h4">QR Code Generation</div>
            <div class="gp-muted">A unique QR code will be generated for this passport</div>
          </div>
        </div>

        <label class="gp-switch">
          <input type="checkbox" v-model="form.withQr" />
          <span class="gp-slider"></span>
        </label>
      </div>

      <div class="gp-info">QR code will be generated when you publish the passport</div>

      <!-- Actions -->
      <div class="gp-actions">
        <button class="gp-btn ghost" :disabled="loading" @click="saveProgress">Save progress</button>
        <button class="gp-btn primary" :disabled="loading || !canPublish" @click="publish">
          Generate Digital Product Passport
        </button>
      </div>

      <div class="gp-foot">
        By generating, you confirm all information is accurate and compliant with EU DPP regulations
      </div>

      <!-- Public URL -->
      <div v-if="publicUrl" class="gp-section gp-box">
        <div class="gp-h4">Public link</div>
        <div class="gp-public-row">
          <input class="gp-input" :value="publicUrl" readonly />
          <button class="gp-btn" @click="copy(publicUrl)">Copy</button>
        </div>
      </div>

      <div v-if="errors.general" class="gp-error">{{ errors.general }}</div>
    </div>
  </div>
</template>
<script setup>
import { reactive, ref, computed, onMounted, watch } from "vue";
import { useRoute } from "vue-router";
import axios from "axios";

const API_BASE_URL = import.meta.env.VITE_API_URL || "http://localhost:8000/api";

function authHeaders() {
  const token =
    localStorage.getItem("token") ||
    localStorage.getItem("auth_token") ||
    localStorage.getItem("access_token") ||
    localStorage.getItem("authToken") ||
    localStorage.getItem("accessToken");

  const headers = { Accept: "application/json" };
  if (token) headers.Authorization = `Bearer ${token}`;
  return headers;
}

const props = defineProps({
  productId: { type: [String, Number], required: false }, // ✅ plus required
});

const route = useRoute();

const loading = ref(false);
const publicUrl = ref(null);
const isGenerated = ref(false);
const generatedAt = ref(null);
const ready = ref(true);

const form = reactive({
  accessLevel: "internal",
  withQr: false,
  authorizedEmails: [],
  authorizedUserIds: [],
  authorizedPartnerIds: [],
});

const errors = reactive({
  partner: "",
  general: "",
});

/** ✅ robust productId (props → query → params → localStorage) */
const productId = computed(() => {
  const pid =
    props.productId ??
    route.query.productId ??
    route.params.productId ??
    localStorage.getItem("product_id") ??
    localStorage.getItem("currentProductId");

  if (pid === null || pid === undefined || pid === "") return null;

  // ✅ store for next steps
  localStorage.setItem("product_id", String(pid));
  localStorage.setItem("currentProductId", String(pid));

  return String(pid);
});

function setAuthHeader() {
  const token =
    localStorage.getItem("token") ||
    localStorage.getItem("auth_token") ||
    localStorage.getItem("access_token") ||
    localStorage.getItem("authToken") ||
    localStorage.getItem("accessToken");

  if (token) axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
}

const canPublish = computed(() => {
  if (!ready.value) return false;
  if (form.accessLevel === "partner") {
    return form.authorizedEmails.map(e => (e || "").trim()).filter(Boolean).length >= 1;
  }
  return true;
});

function addEmail() {
  form.authorizedEmails.push("");
}
function removeEmail(i) {
  form.authorizedEmails.splice(i, 1);
}

function validate() {
  errors.partner = "";
  errors.general = "";

  if (form.accessLevel === "partner") {
    const emails = form.authorizedEmails.map(e => (e || "").trim()).filter(Boolean);
    if (emails.length < 1) {
      errors.partner = "Please add at least one partner email.";
      return false;
    }
    const bad = emails.find(e => !/^\S+@\S+\.\S+$/.test(e));
    if (bad) {
      errors.partner = `Invalid email: ${bad}`;
      return false;
    }
  }
  return true;
}

async function load() {
  if (!productId.value) {
    errors.general = "Product ID is missing! Please navigate from the creation flow.";
    return;
  }

  setAuthHeader();
  loading.value = true;
  errors.general = "";

  try {
    const res = await axios.get(`/api/products/${productId.value}/passport/generation`);

    // ✅ if API returns empty, keep defaults
    const d = res?.data?.data;
    if (!d) {
      publicUrl.value = null;
      isGenerated.value = false;
      generatedAt.value = null;
      return;
    }

    form.accessLevel = d.accessLevel || "internal";
    form.withQr = !!d.withQr;

    form.authorizedEmails = Array.isArray(d.authorizedEmails) ? d.authorizedEmails : [];
    form.authorizedUserIds = Array.isArray(d.authorizedUserIds) ? d.authorizedUserIds : [];
    form.authorizedPartnerIds = Array.isArray(d.authorizedPartnerIds) ? d.authorizedPartnerIds : [];

    publicUrl.value = d.publicUrl || null;
    isGenerated.value = !!d.isGenerated;
    generatedAt.value = d.generatedAt || null;

  } catch (e) {
    // 404 = no settings yet => ok
    if (e.response?.status === 404) {
      form.accessLevel = "internal";
      form.withQr = false;
      form.authorizedEmails = [];
      publicUrl.value = null;
      isGenerated.value = false;
      generatedAt.value = null;
      return;
    }

    errors.general = e?.response?.data?.message || e.message || "Failed to load generation settings.";
  } finally {
    loading.value = false;
  }
}

async function saveProgress() {
  if (!validate()) return;
  if (!productId.value) {
    errors.general = "Product ID is missing!";
    return;
  }

  setAuthHeader();
  loading.value = true;

  try {
    await axios.post(`/api/products/${productId.value}/passport/save-progress`, {
      accessLevel: form.accessLevel,
      withQr: form.withQr,
      authorizedEmails: form.authorizedEmails,
      authorizedUserIds: form.authorizedUserIds,
      authorizedPartnerIds: form.authorizedPartnerIds,
    });

    errors.general = "";
    await load();
  } catch (e) {
    errors.general = e?.response?.data?.message || "Save failed.";
  } finally {
    loading.value = false;
  }
}
async function publish() {
  if (!validate()) return;
  if (!productId.value) {
    errors.general = "Product ID is missing!";
    return;
  }

  loading.value = true;
  errors.general = "";

  try {
    // Save first
    await axios.post(
      `${API_BASE_URL}/products/${productId.value}/passport/save-progress`,
      {
        accessLevel: form.accessLevel,
        withQr: form.withQr,
        authorizedEmails: form.authorizedEmails,
        authorizedUserIds: form.authorizedUserIds,
        authorizedPartnerIds: form.authorizedPartnerIds,
      },
      { headers: authHeaders() }
    );

    // Publish
    const res = await axios.post(
      `${API_BASE_URL}/products/${productId.value}/passport/publish`,
      {},
      { headers: authHeaders() }
    );

    publicUrl.value = res.data?.data?.publicUrl || null;
    isGenerated.value = true;
    generatedAt.value = res.data?.data?.generatedAt || new Date().toISOString();
  } catch (e) {
    console.log("PUBLISH ERROR:", e?.response?.status, e?.response?.data || e);

    // ✅ affiche quelque chose d’utile
    const data = e?.response?.data;
    if (data?.message) errors.general = data.message;
    else if (data?.errors) {
      const firstKey = Object.keys(data.errors)[0];
      errors.general = Array.isArray(data.errors[firstKey]) ? data.errors[firstKey][0] : String(data.errors[firstKey]);
    } else {
      errors.general = e.message || "Publish failed.";
    }
  } finally {
    loading.value = false;
  }
}

async function copy(text) {
  try {
    await navigator.clipboard.writeText(text);
    alert("Copied to clipboard!");
  } catch {
    alert("Copy failed. Please copy manually.");
  }
}

function formatDate(dateStr) {
  if (!dateStr) return "-";
  const d = new Date(dateStr);
  return isNaN(d.getTime()) ? dateStr : d.toLocaleString();
}

watch(() => form.accessLevel, () => {
  errors.partner = "";
});

// ✅ reload if productId becomes available later (ex: parent loads)
watch(productId, (v) => {
  if (v) load();
});

onMounted(() => {
  load();
});
</script>


<style scoped>
.gp-wrap { padding: 28px; display:flex; justify-content:center; background:#f6f8fb; min-height:100vh; }
.gp-card { width: 920px; max-width: 100%; background:#fff; border:1px solid #e8eef5; border-radius:16px; padding:26px; box-shadow:0 10px 30px rgba(16,24,40,.06); }
.gp-title { font-size:26px; font-weight:800; margin:0 0 6px; color:#101828; }
.gp-sub { margin:0 0 18px; color:#5b6b7c; }
.gp-alert { padding:12px 14px; border-radius:12px; margin: 12px 0 14px; font-weight:700; }
.gp-alert.ok { background:#eafff3; border:1px solid #bff2d5; color:#156c3f; }
.gp-alert.info { background:#eef6ff; border:1px solid #cfe2ff; color:#1f4aa7; }

.gp-section { margin: 18px 0; }
.gp-h3 { margin: 0 0 12px; font-size:14px; color:#2b3a4a; font-weight:800; }
.gp-h4 { font-weight:800; margin:0; color:#2b3a4a; }
.gp-muted { color:#66768a; font-size:12px; margin-top:3px; }

.gp-levels { display:flex; gap:14px; flex-wrap:wrap; }
.gp-level { flex:1; min-width:240px; border:1px solid #e6edf4; border-radius:14px; padding:14px; display:flex; gap:10px; cursor:pointer; transition:.15s; }
.gp-level input { margin-top:4px; }
.gp-level.active { border-color:#0f766e; box-shadow:0 0 0 3px rgba(15,118,110,.12); }
.gp-level-title { font-weight:800; }
.gp-level-desc { color:#66768a; font-size:12px; margin-top:2px; line-height:1.35; }

.gp-box { border:1px dashed #dbe6f1; border-radius:14px; padding:14px; background:#fbfdff; }
.gp-row { display:flex; gap:12px; }
.gp-between { justify-content:space-between; align-items:center; }

.gp-email-row { display:flex; gap:10px; margin-top:10px; }
.gp-input { width:100%; border:1px solid #d7e2ee; border-radius:12px; padding:10px 12px; outline:none; }
.gp-input:focus { border-color:#0f766e; box-shadow:0 0 0 3px rgba(15,118,110,.10); }

.gp-qr { display:flex; align-items:center; justify-content:space-between; border:1px solid #e6edf4; border-radius:14px; padding:16px; }
.gp-qr-left { display:flex; gap:12px; align-items:center; }
.gp-qr-ico { width:44px; height:44px; border-radius:12px; display:flex; align-items:center; justify-content:center; background:#d8fff4; color:#0f766e; font-weight:900; }

.gp-info { background:#f6f9fc; border:1px solid #e6edf4; padding:12px 14px; border-radius:12px; color:#56697d; }

.gp-actions { display:flex; gap:12px; justify-content:flex-end; margin-top:18px; flex-wrap:wrap; }
.gp-btn { border:1px solid #d7e2ee; background:#fff; padding:10px 14px; border-radius:12px; cursor:pointer; font-weight:800; }
.gp-btn:hover { background:#f7fafc; }
.gp-btn.primary { background:#0f766e; border-color:#0f766e; color:#fff; padding:12px 18px; }
.gp-btn.primary:disabled { opacity:.6; cursor:not-allowed; }
.gp-btn.ghost { background:#fff; }
.gp-btn.danger { border-color:#ffccd1; color:#b42318; }
.gp-btn:disabled { opacity:.65; cursor:not-allowed; }

.gp-foot { margin-top:12px; font-size:12px; color:#66768a; text-align:center; }
.gp-public-row { display:flex; gap:10px; margin-top:10px; }
.gp-error { margin-top:10px; color:#b42318; font-weight:700; }

.gp-switch { position:relative; width:56px; height:30px; display:inline-block; }
.gp-switch input { display:none; }
.gp-slider { position:absolute; inset:0; background:#dbe6f1; border-radius:99px; transition:.2s; }
.gp-slider::after { content:""; position:absolute; width:24px; height:24px; top:3px; left:3px; background:#fff; border-radius:50%; transition:.2s; box-shadow:0 4px 10px rgba(16,24,40,.12); }
.gp-switch input:checked + .gp-slider { background:#0f766e; }
.gp-switch input:checked + .gp-slider::after { transform:translateX(26px); }
</style>