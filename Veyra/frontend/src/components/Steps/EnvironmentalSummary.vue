<!-- src/components/Steps/EnvironmentalSummary.vue -->
<template>
  <div class="es-page">
    <div class="es-header">
      <h2>Environmental Summary</h2>
      <p>Overview of environmental data for your product</p>
    </div>

    <div class="es-divider"></div>

    <div class="es-top-grid">
      <div class="es-card metric mint">
        <div class="metric-head">
          <div class="metric-ico mint">
            <svg viewBox="0 0 24 24" class="ico" aria-hidden="true">
              <path
                d="M20.4 3.6c-6.2-.6-10.5 1.1-13 3.7C4.8 9.9 4.3 13 4.1 15c-.2 1.7.1 3.7.8 4.5.8.7 2.8 1 4.5.8 2-.2 5.1-.7 7.7-3.3 2.6-2.6 4.3-6.9 3.3-13.4ZM7.8 16.2c.2-2 .7-4.2 2.6-6.1 1.7-1.7 4.4-2.9 8.6-2.8-.1 4.2-1.1 7-2.8 8.6-1.9 1.9-4.1 2.4-6.1 2.6-1 .1-2.2 0-3-.3.3-.8.4-2 .3-3Z"
                fill="currentColor"
              />
            </svg>
          </div>
          <div class="metric-title">bAwear Score</div>
        </div>

        <div class="metric-main">
          <div class="metric-line">
            <div class="metric-bar">
              <div class="metric-bar-fill" :style="{ width: bawearPercent + '%' }"></div>
            </div>
            <div class="metric-unit">/100</div>
          </div>
          <div class="metric-foot">{{ bawearStatus }}</div>
        </div>
      </div>

      <div class="es-card metric sand">
        <div class="metric-head">
          <div class="metric-ico sand">
            <svg viewBox="0 0 24 24" class="ico" aria-hidden="true">
              <path d="M4 19h16v2H4v-2Zm2-2V7h3v10H6Zm5 0V4h3v13h-3Zm5 0v-8h3v8h-3Z" fill="currentColor" />
            </svg>
          </div>
          <div class="metric-title">Avg. Renewable<br />Energy</div>
        </div>
        <div class="metric-main">
          <div class="metric-big orange">{{ env.avgRenewableEnergyPct }}%</div>
          <div class="metric-foot">Across all production stages</div>
        </div>
      </div>

      <div class="es-card metric ice">
        <div class="metric-head">
          <div class="metric-ico ice">
            <svg viewBox="0 0 24 24" class="ico" aria-hidden="true">
              <path
                d="M12 2a10 10 0 1 0 10 10A10.01 10.01 0 0 0 12 2Zm7.93 9h-3.06a15.6 15.6 0 0 0-1.4-6.01A8.03 8.03 0 0 1 19.93 11ZM12 4c.9 1.2 1.8 3.3 2.22 7H9.78C10.2 7.3 11.1 5.2 12 4ZM4.07 13h3.06c.18 2.16.7 4.22 1.4 6.01A8.03 8.03 0 0 1 4.07 13Zm3.06-2H4.07a8.03 8.03 0 0 1 4.46-6.01A15.6 15.6 0 0 0 7.13 11ZM12 20c-.9-1.2-1.8-3.3-2.22-7h4.44C13.8 16.7 12.9 18.8 12 20Zm3.47-.99c.7-1.79 1.22-3.85 1.4-6.01h3.06a8.03 8.03 0 0 1-4.46 6.01Z"
                fill="currentColor"
              />
            </svg>
          </div>
          <div class="metric-title">Avg. Recycled<br />Water</div>
        </div>
        <div class="metric-main">
          <div class="metric-big blue">{{ env.avgRecycledWaterPct }}%</div>
          <div class="metric-foot">Across all production stages</div>
        </div>
      </div>
    </div>

    <div class="es-box">
      <div class="es-box-title">Certifications &amp; Compliance</div>

      <div class="es-cert-grid">
        <div class="cert-card">
          <div class="cert-title">ZDHC Supply to<br />Zero</div>
          <div class="cert-value">
            <span class="x">{{ env.certifications.zdhcSupplyToZero ? "✓" : "✕" }}</span>
            <span class="muted">{{ env.certifications.zdhcSupplyToZero ? "Yes" : "No" }}</span>
          </div>
        </div>

        <div class="cert-card">
          <div class="cert-title">ZDHC GetZay</div>
          <div class="cert-value">
            <span class="x">{{ env.certifications.zdhcGetZay ? "✓" : "✕" }}</span>
            <span class="muted">{{ env.certifications.zdhcGetZay ? "Yes" : "No" }}</span>
          </div>
        </div>

        <div class="cert-card">
          <div class="cert-title">Fiber Certifications</div>
          <div class="cert-value">
            <span class="muted">{{ env.certifications.fiberCertificationsCount }} certified</span>
          </div>
        </div>

        <div class="cert-card">
          <div class="cert-title">bAwear Report</div>
          <div class="cert-value">
            <span class="x">{{ env.certifications.bawearReportMissing ? "✕" : "✓" }}</span>
            <span class="muted">{{ env.certifications.bawearReportMissing ? "Missing" : "Provided" }}</span>
          </div>
        </div>
      </div>
    </div>

    <div class="es-note">
      <div class="note-ico">i</div>
      <div class="note-text">
        This summary provides an overview of your product's environmental data.
        Additional integrations with EcoBalys and other platforms will enhance this data in future updates.
      </div>
    </div>

    <div class="es-divider"></div>

    <div class="es-footer">
      <button class="btn ghost" @click="goPrev">‹ Previous</button>

      <div class="es-footer-right">
        <button class="btn ghost" :disabled="saving" @click="save">
          {{ saving ? "Saving..." : "Save Progress" }}
        </button>

        <button class="btn primary" :disabled="saving" @click="generate">
          Generate Passport
        </button>
      </div>
    </div>

    <div v-if="error" class="msg error">{{ error }}</div>
    <div v-if="success" class="msg success">{{ success }}</div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

// ✅ CHANGEMENT ICI: passportId → productId
const props = defineProps({
  productId: { type: String, default: "" },
});

const router = useRouter();

const saving = ref(false);
const error = ref("");
const success = ref("");

const env = ref({
  bawearScore: null,
  bawearProvided: false,
  avgRenewableEnergyPct: 0,
  avgRecycledWaterPct: 0,
  certifications: {
    zdhcSupplyToZero: false,
    zdhcGetZay: false,
    fiberCertificationsCount: 0,
    bawearReportMissing: true,
  },
});

const bawearPercent = computed(() => {
  const v = env.value.bawearScore;
  if (v == null) return 0;
  const n = Number(v);
  return Number.isFinite(n) ? Math.max(0, Math.min(100, n)) : 0;
});

const bawearStatus = computed(() => (!env.value.bawearProvided ? "Not yet provided" : ""));

async function fetchFromBackend() {
  // ✅ CHANGEMENT: passportId → productId
  if (!props.productId) return;

  error.value = "";
  success.value = "";

  try {
    // Step 12 summary
    const r1 = await axios.get(`/api/products/${props.productId}/environmental-summary`);
    const saved = r1?.data?.data || {};

    env.value = {
      ...env.value,
      ...saved,
      certifications: { ...env.value.certifications, ...(saved.certifications || {}) },
    };

    // Step 10 bAwear latest
    try {
      const r2 = await axios.get(`/api/products/${props.productId}/bawear`);
      const latest = r2?.data?.data;
      if (latest?.score_value != null) {
        env.value.bawearScore = Number(latest.score_value);
        env.value.bawearProvided = true;
      }
    } catch (_) {
      // bAwear data is optional
    }
  } catch (e) {
    error.value = e?.response?.data?.message || "Error loading environmental data.";
  }
}

async function save() {
  error.value = "";
  success.value = "";
  saving.value = true;

  try {
    // ✅ CHANGEMENT: passportId → productId
    if (props.productId) {
      await axios.post(`/api/products/${props.productId}/environmental-summary/save-progress`, {
        bawearScore: env.value.bawearScore,
        bawearProvided: env.value.bawearProvided,
        avgRenewableEnergyPct: env.value.avgRenewableEnergyPct,
        avgRecycledWaterPct: env.value.avgRecycledWaterPct,
        certifications: env.value.certifications,
      });
    }
    
    await new Promise((r) => setTimeout(r, 400));
    success.value = "Saved ✅";
    
    // ✅ CHANGEMENT: passportId → productId
    if (props.productId) {
      await fetchFromBackend();
    }
  } catch (e) {
    error.value = e?.response?.data?.message || "Error while saving.";
  } finally {
    saving.value = false;
  }
}

async function generate() {
  await save();
  if (error.value) return;

  try {
    // ✅ CHANGEMENT: passportId → productId
    if (props.productId) {
      await axios.post(`/api/products/${props.productId}/passport/generate`);
    }
    success.value = "Passport generated ✅";
    
    // ✅ CHANGEMENT IMPORTANT: passportId → productId dans la navigation
    router.push({
      path: "/user/passports/generatepassport",
      query: props.productId ? { productId: props.productId } : {},
    });
  } catch (e) {
    error.value = e?.response?.data?.message || "Error while generating passport.";
  }
}

function goPrev() {
  // ✅ CHANGEMENT: passportId → productId
  router.push({
    path: "/user/passports/ecobalys",
    query: props.productId ? { productId: props.productId } : {},
  });
}

onMounted(fetchFromBackend);
</script>

<style scoped>
/* ✅ CSS identique à ton design */
.es-page { max-width: 980px; margin: 0 auto; padding: 18px; }
.es-header h2 { margin: 0; font-size: 22px; font-weight: 900; letter-spacing: -0.01em; }
.es-header p { margin: 6px 0 0; color: #64748b; font-size: 13px; }
.es-divider { height: 1px; background: #e5e7eb; margin: 14px 0; }
.es-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 14px; padding: 16px; }
.es-top-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; }
@media (max-width: 860px){ .es-top-grid { grid-template-columns: 1fr; } }
.metric { min-height: 132px; }
.metric-head { display: flex; gap: 12px; align-items: center; }
.metric-title { font-weight: 900; color: #0f172a; font-size: 14px; line-height: 1.2; }
.metric-ico { width: 44px; height: 44px; border-radius: 12px; display: grid; place-items: center; }
.metric-ico.mint { background: #dcfce7; color: #16a34a; }
.metric-ico.sand { background: #fef3c7; color: #d97706; }
.metric-ico.ice  { background: #dbeafe; color: #2563eb; }
.metric-main { margin-top: 14px; }
.metric-line { display: flex; align-items: center; gap: 10px; }
.metric-bar { flex: 1; height: 4px; border-radius: 999px; background: #e5e7eb; overflow: hidden; }
.metric-bar-fill { height: 100%; background: #16a34a; width: 0%; }
.metric-unit { color: #94a3b8; font-weight: 800; font-size: 13px; }
.metric-foot { margin-top: 10px; color: #94a3b8; font-size: 12px; font-weight: 700; }
.metric-big { font-size: 34px; font-weight: 1000; letter-spacing: -0.02em; }
.metric-big.orange { color: #d97706; }
.metric-big.blue { color: #2563eb; }
.es-box { margin-top: 14px; border: 1px solid #e5e7eb; border-radius: 14px; padding: 16px; background: #fff; }
.es-box-title { font-weight: 900; color: #0f172a; margin-bottom: 12px; }
.es-cert-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; }
@media (max-width: 860px){ .es-cert-grid { grid-template-columns: 1fr 1fr; } }
.cert-card { border: 1px solid #e5e7eb; border-radius: 12px; padding: 12px; background: #f8fafc; text-align: center; }
.cert-title { font-weight: 900; font-size: 12.5px; color: #0f172a; line-height: 1.2; }
.cert-value { margin-top: 10px; display: inline-flex; align-items: center; gap: 8px; font-weight: 900; }
.cert-value .x { color: #94a3b8; font-size: 14px; }
.muted { color: #94a3b8; }
.es-note { margin-top: 14px; border: 1px solid #bfdbfe; background: #eff6ff; border-radius: 14px; padding: 14px; display: flex; gap: 12px; align-items: flex-start; }
.note-ico { width: 26px; height: 26px; border-radius: 999px; border: 1px solid #93c5fd; color: #1d4ed8; display: grid; place-items: center; font-weight: 1000; background: #e0f2fe; }
.note-text { color: #1d4ed8; font-size: 13px; line-height: 1.45; font-weight: 700; }
.es-footer { display: flex; justify-content: space-between; align-items: center; }
.es-footer-right { display: flex; gap: 10px; }
.btn { height: 40px; padding: 0 14px; border-radius: 10px; border: 1px solid #e5e7eb; background: #fff; cursor: pointer; font-weight: 900; }
.btn.primary { background: #0ea5a4; border-color: #0ea5a4; color: #fff; }
.btn.ghost { background: #fff; }
.btn:disabled { opacity: .65; cursor: not-allowed; }
.msg { margin-top: 10px; font-weight: 800; }
.msg.error { color: #b91c1c; }
.msg.success { color: #047857; }
</style>