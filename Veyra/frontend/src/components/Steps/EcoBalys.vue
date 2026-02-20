<!-- src/components/Steps/EcoBalys.vue -->
<template>
  <div class="eco-page">
    <!-- Header -->
    <div class="eco-head">
      <h2 class="eco-title">EcoBalys Integration</h2>
      <p class="eco-subtitle">Connect to EcoBalys for lifecycle assessment data</p>
    </div>

    <div class="eco-divider"></div>

    <!-- Main card -->
    <div class="eco-card">
      <!-- Big banner -->
      <div class="eco-banner">
        <div class="eco-banner-icon">
          <!-- database icon -->
          <svg viewBox="0 0 24 24" class="ico" aria-hidden="true">
            <path
              d="M12 3c-4.97 0-9 1.34-9 3s4.03 3 9 3 9-1.34 9-3-4.03-3-9-3Zm-9 6v4c0 1.66 4.03 3 9 3s9-1.34 9-3V9c-1.86 1.54-5.67 2.5-9 2.5S4.86 10.54 3 9Zm0 7v4c0 1.66 4.03 3 9 3s9-1.34 9-3v-4c-1.86 1.54-5.67 2.5-9 2.5S4.86 17.54 3 16Z"
              fill="currentColor"
            />
          </svg>
        </div>

        <div class="eco-banner-title">EcoBalys API Integration</div>
        <div class="eco-banner-text">
          This integration will automatically fetch lifecycle assessment data,
          carbon footprint calculations, and environmental impact metrics from EcoBalys.
        </div>

        <div class="eco-soon">
          <span class="eco-soon-dot">
            <svg viewBox="0 0 24 24" class="ico-sm" aria-hidden="true">
              <path
                d="M12 8a1 1 0 0 1 1 1v3.59l2.2 2.2a1 1 0 1 1-1.4 1.42l-2.5-2.5A1 1 0 0 1 11 13V9a1 1 0 0 1 1-1Zm0-6a10 10 0 1 0 10 10A10.01 10.01 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8 8.01 8.01 0 0 1-8 8Z"
                fill="currentColor"
              />
            </svg>
          </span>
          Coming Soon
        </div>
      </div>

      <!-- Feature cards row -->
      <div class="eco-features">
        <div class="eco-feature">
          <div class="eco-feature-ico green">
            <svg viewBox="0 0 24 24" class="ico" aria-hidden="true">
              <path
                d="M4 19h16v2H4v-2Zm2-8 4 4 8-8 1.4 1.4-9.4 9.4-5.4-5.4L6 11Z"
                fill="currentColor"
              />
            </svg>
          </div>
          <div class="eco-feature-title">Carbon Footprint</div>
          <div class="eco-feature-text">Automatic CO2 equivalent calculations</div>
        </div>

        <div class="eco-feature">
          <div class="eco-feature-ico blue">
            <svg viewBox="0 0 24 24" class="ico" aria-hidden="true">
              <path
                d="M12 3c-4.97 0-9 1.34-9 3s4.03 3 9 3 9-1.34 9-3-4.03-3-9-3Zm-9 6v4c0 1.66 4.03 3 9 3s9-1.34 9-3V9c-1.86 1.54-5.67 2.5-9 2.5S4.86 10.54 3 9Z"
                fill="currentColor"
              />
            </svg>
          </div>
          <div class="eco-feature-title">LCA Data</div>
          <div class="eco-feature-text">Complete lifecycle assessment metrics</div>
        </div>

        <div class="eco-feature">
          <div class="eco-feature-ico purple">
            <svg viewBox="0 0 24 24" class="ico" aria-hidden="true">
              <path
                d="M14 3h7v7h-2V6.41l-9.29 9.3-1.42-1.42 9.3-9.29H14V3ZM5 5h6v2H7v10h10v-4h2v6H5V5Z"
                fill="currentColor"
              />
            </svg>
          </div>
          <div class="eco-feature-title">Real-time Sync</div>
          <div class="eco-feature-text">Automatic data synchronization</div>
        </div>
      </div>

      <!-- Connect button -->
      <div class="eco-connect">
        <button class="eco-btn connect" disabled>
          <span class="eco-btn-ico" aria-hidden="true">
            <svg viewBox="0 0 24 24" class="ico-sm">
              <path
                d="M14 3h7v7h-2V6.41l-9.29 9.3-1.42-1.42 9.3-9.29H14V3ZM5 5h6v2H7v10h10v-4h2v6H5V5Z"
                fill="currentColor"
              />
            </svg>
          </span>
          Connect EcoBalys Account
        </button>
      </div>

      <div class="eco-divider soft"></div>

      <!-- Footer -->
      <div class="eco-footer">
        <button class="eco-btn ghost" @click="goPrev">‹ Previous</button>

        <div class="eco-footer-right">
          <button class="eco-btn ghost" :disabled="saving" @click="save">
            <span class="eco-btn-ico" aria-hidden="true">
              <svg viewBox="0 0 24 24" class="ico-sm">
                <path
                  d="M17 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7l-4-4Zm-5 18a3 3 0 1 1 3-3 3 3 0 0 1-3 3ZM6 8V5h10v3H6Z"
                  fill="currentColor"
                />
              </svg>
            </span>
            {{ saving ? "Saving..." : "Save Progress" }}
          </button>

          <button class="eco-btn primary" :disabled="saving" @click="next">
            Next Step
            <span class="eco-btn-ico right" aria-hidden="true">
              <svg viewBox="0 0 24 24" class="ico-sm">
                <path
                  d="M10 17a1 1 0 0 1-.7-1.7L12.6 12 9.3 8.7a1 1 0 0 1 1.4-1.4l4 4a1 1 0 0 1 0 1.4l-4 4a1 1 0 0 1-.7.3Z"
                  fill="currentColor"
                />
              </svg>
            </span>
          </button>
        </div>
      </div>
    </div>

    <div v-if="error" class="eco-msg error">{{ error }}</div>
    <div v-if="success" class="eco-msg success">{{ success }}</div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";

/**
 * Option 1 (wizard parent controls steps): emit events instead.
 * Option 2 (router controls): useRouter() and push.
 *
 * Ici je fais router par défaut (tu changes les paths selon ton projet).
 */
const props = defineProps({
  passportId: { type: String, default: "" }, // si ton parent peut le passer
});

const router = useRouter();
const saving = ref(false);
const error = ref("");
const success = ref("");

async function save() {
  // Front only (Coming soon): just fake save
  error.value = "";
  success.value = "";
  saving.value = true;

  try {
    // TODO: call your API when ready
    await new Promise((r) => setTimeout(r, 400));
    success.value = "Saved ✅";
  } catch (e) {
    error.value = "Error while saving.";
  } finally {
    saving.value = false;
  }
}

async function next() {
  await save();
  if (error.value) return;

  // ✅ Step 12 example
  router.push({
    path: "/user/passports/environmentaldata",
    query: props.passportId ? { passportId: props.passportId } : {},
  });
}

function goPrev() {
  // ✅ Step 10 previous example
  router.push({
    path: "/user/passports/bawearscore",
    query: props.passportId ? { passportId: props.passportId } : {},
  });
}
</script>

<style scoped>
.eco-page {
  max-width: 980px;
  margin: 0 auto;
  padding: 18px;
}

.eco-head {
  margin-bottom: 10px;
}

.eco-title {
  margin: 0;
  font-size: 22px;
  font-weight: 800;
  letter-spacing: -0.01em;
}

.eco-subtitle {
  margin: 6px 0 0;
  color: #64748b;
  font-size: 13px;
}

.eco-divider {
  height: 1px;
  background: #e5e7eb;
  margin: 14px 0;
}
.eco-divider.soft {
  margin-top: 18px;
}

.eco-card {
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 16px;
  padding: 18px;
  box-shadow: 0 1px 0 rgba(15, 23, 42, 0.03);
}

/* Banner */
.eco-banner {
  border-radius: 14px;
  border: 1px solid #dbeafe;
  background: linear-gradient(180deg, #f0f7ff 0%, #f6fbff 100%);
  padding: 22px 16px;
  text-align: center;
}

.eco-banner-icon {
  width: 56px;
  height: 56px;
  margin: 0 auto 10px;
  border-radius: 16px;
  background: #eaf3ff;
  border: 1px solid #dbeafe;
  display: grid;
  place-items: center;
  color: #2563eb;
}

.eco-banner-title {
  margin-top: 4px;
  font-weight: 900;
  color: #1d4ed8;
  font-size: 18px;
}

.eco-banner-text {
  margin: 8px auto 0;
  max-width: 720px;
  color: #2563eb;
  font-size: 13px;
  line-height: 1.55;
}

.eco-soon {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  margin-top: 14px;
  padding: 8px 12px;
  border-radius: 999px;
  background: #fff7ed;
  color: #c2410c;
  border: 1px solid #fed7aa;
  font-weight: 800;
  font-size: 12px;
}

.eco-soon-dot {
  display: grid;
  place-items: center;
}

/* Features */
.eco-features {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 14px;
  margin-top: 16px;
}

@media (max-width: 860px) {
  .eco-features {
    grid-template-columns: 1fr;
  }
}

.eco-feature {
  border: 1px solid #e5e7eb;
  border-radius: 14px;
  padding: 14px;
  background: #fff;
}

.eco-feature-ico {
  width: 42px;
  height: 42px;
  border-radius: 12px;
  display: grid;
  place-items: center;
  margin-bottom: 10px;
}
.eco-feature-ico.green {
  background: #dcfce7;
  color: #16a34a;
}
.eco-feature-ico.blue {
  background: #dbeafe;
  color: #2563eb;
}
.eco-feature-ico.purple {
  background: #ede9fe;
  color: #7c3aed;
}

.eco-feature-title {
  font-weight: 900;
  color: #0f172a;
  font-size: 14px;
}

.eco-feature-text {
  margin-top: 6px;
  color: #64748b;
  font-size: 12.5px;
  line-height: 1.4;
}

/* Connect button */
.eco-connect {
  display: flex;
  justify-content: center;
  margin-top: 16px;
}

.eco-btn {
  height: 40px;
  padding: 0 14px;
  border-radius: 10px;
  border: 1px solid #e5e7eb;
  background: #fff;
  cursor: pointer;
  font-weight: 800;
  display: inline-flex;
  align-items: center;
  gap: 10px;
  color: #0f172a;
}

.eco-btn.connect {
  min-width: 240px;
  justify-content: center;
  color: #94a3b8;
  background: #f8fafc;
}

.eco-btn.primary {
  background: #0ea5a4;
  border-color: #0ea5a4;
  color: #fff;
}

.eco-btn.ghost {
  background: #fff;
}

.eco-btn:disabled {
  opacity: 0.65;
  cursor: not-allowed;
}

.eco-btn-ico {
  display: grid;
  place-items: center;
}
.eco-btn-ico.right {
  margin-left: 2px;
}

/* Footer */
.eco-footer {
  margin-top: 14px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.eco-footer-right {
  display: flex;
  gap: 10px;
}

.eco-msg {
  margin-top: 10px;
  font-weight: 700;
}
.eco-msg.error {
  color: #b91c1c;
}
.eco-msg.success {
  color: #047857;
}

/* Icons sizes */
.ico {
  width: 22px;
  height: 22px;
}
.ico-sm {
  width: 18px;
  height: 18px;
}
</style>
