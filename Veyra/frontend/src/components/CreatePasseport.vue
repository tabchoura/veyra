<template>
  <div class="passport-container">
    <div class="passport-layout">

      <!-- Sidebar -->
      <aside class="sidebar" :class="{ 'sidebar-open': sidebarOpen }">
        <PassportProgress
          :steps="steps"
          :activeStep="activeStep"
          :completedCount="completedCount"
          @select="goStep"
        />
      </aside>

      <!-- Overlay mobile -->
      <div v-if="sidebarOpen" class="sidebar-overlay" @click="closeSidebar"></div>

      <!-- Main -->
      <main class="main-content">

        <!-- Top bar -->
        <div class="topbar">
          <button class="topbar-menu" @click="toggleSidebar">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <line x1="3" y1="7" x2="21" y2="7"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="17" x2="21" y2="17"/>
            </svg>
          </button>

          <div class="topbar-id">
            <span class="topbar-id-label">Passport ID</span>
            <div class="topbar-id-row">
              <span class="topbar-id-value">{{ itemCode || "—" }}</span>
              <button class="btn-copy" @click="copyToClipboard" :class="{ copied: isCopied }" :disabled="!itemCode">
                <svg v-if="!isCopied" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                </svg>
                <svg v-else width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                  <polyline points="20 6 9 17 4 12"/>
                </svg>
              </button>
            </div>
            <span class="topbar-date">{{ formattedDate }}</span>
          </div>

          <div class="topbar-actions">
            <button class="btn-action" @click="saveDraft" :disabled="isSaving">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                <polyline points="17 21 17 13 7 13 7 21"/>
                <polyline points="7 3 7 8 15 8"/>
              </svg>
              {{ isSaving ? "Saving..." : "Save Draft" }}
            </button>

            <button class="btn-action btn-back" @click="handleBack">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M19 12H5M5 12L12 19M5 12L12 5"/>
              </svg>
              Back
            </button>
          </div>
        </div>

        <!-- Progress bar -->
        <div class="progress-strip">
          <div class="strip-top">
            <div class="strip-left">
              <span class="strip-step">Step {{ activeStep }} / {{ steps.length }}</span>
              <span class="strip-title">{{ currentStepName }}</span>
            </div>
            <div class="strip-right">
              <span class="strip-pct">{{ completionPercentage }}%</span>
              <span class="strip-pct-label">Complete</span>
            </div>
          </div>
          <div class="strip-track">
            <div class="strip-fill" :style="{ width: completionPercentage + '%' }"></div>
          </div>
        </div>

        <!-- Step content -->
        <transition name="fade-slide" mode="out-in">
          <div class="content-area" :key="activeStep">
            <component
              :is="currentComponent"
              :itemCode="itemCode"
              :creationDatetime="creationDatetime"
              :productId="productId"
              @next="nextStep"
              @previous="previousStep"
              @update="handleUpdate"
            />
          </div>
        </transition>

        <!-- Nav controls -->
        <div class="nav-controls">
          <button class="btn-nav btn-prev" @click="previousStep" :disabled="activeStep === 1">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <path d="M15 18L9 12L15 6"/>
            </svg>
            Previous
          </button>

          <div class="dots">
            <button
              v-for="step in steps" :key="step.id"
              class="dot"
              :class="{ 'dot-active': step.id === activeStep, 'dot-done': step.id <= completedCount }"
              @click="goStep(step.id)"
            ></button>
          </div>

          <button class="btn-nav btn-next" @click="nextStep" :disabled="activeStep === steps.length || isSaving">
            Next
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <path d="M9 18L15 12L9 6"/>
            </svg>
          </button>
        </div>

      </main>
    </div>

    <!-- Toast -->
    <transition name="toast-slide">
      <div v-if="toast.show" class="toast" :class="'toast-' + toast.type">
        <div class="toast-icon">
          <svg v-if="toast.type === 'success'" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
          <svg v-else width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
        </div>
        <span>{{ toast.message }}</span>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { computed, ref, watch, onMounted, onUnmounted } from "vue";
import { useRouter } from "vue-router";

import PassportProgress from "@/components/PasseportProgress.vue";
import ProductInitialization from "@/components/Steps/ProductInitialization.vue";
import ProductType from "@/components/Steps/ProductType.vue";
import FibersComposition from "@/components/Steps/FibersComposition.vue";
import Yarn from "@/components/Steps/YarnInformation.vue";
import Fabric from "@/components/Steps/FabricInformation.vue";
import Manufacturing from "@/components/Steps/ManufacturingDetails.vue";
import Accessories from "@/components/Steps/Accessories.vue";
import Usage from "@/components/Steps/Usage.vue";
import EndOfLife from "@/components/Steps/EndOfLife.vue";
import BawearAssessment from "@/components/Steps/BawearScore.vue";
import EcoBalys from "@/components/Steps/EcoBalys.vue";
import EnvironmentalSummary from "@/components/Steps/EnvironmentalSummary.vue";
import GeneratePassport from "@/components/Steps/GeneratePassport.vue";

const router = useRouter();

const itemCode = ref("");
const creationDatetime = ref(new Date());
const productId = ref(null);

const steps = [
  { id: 1, label: "Product Initialization" },
  { id: 2, label: "Product Type" },
  { id: 3, label: "Fibers Composition" },
  { id: 4, label: "Yarn" },
  { id: 5, label: "Fabric" },
  { id: 6, label: "Manufacturing" },
  { id: 7, label: "Accessories" },
  { id: 8, label: "Usage" },
  { id: 9, label: "End of Life" },
  { id: 10, label: "bAwear Score" },
  { id: 11, label: "EcoBalys" },
  { id: 12, label: "Environmental Data" },
  { id: 13, label: "Passport Generation" },
];

const activeStep = ref(1);
const completedCount = ref(0);
const isCopied = ref(false);
const isSaving = ref(false);
const sidebarOpen = ref(false);
const toast = ref({ show: false, message: "", type: "success" });

const currentComponent = computed(() => {
  const map = [null, ProductInitialization, ProductType, FibersComposition, Yarn, Fabric, Manufacturing, Accessories, Usage, EndOfLife, BawearAssessment, EcoBalys, EnvironmentalSummary, GeneratePassport];
  return map[activeStep.value] || ProductInitialization;
});

const currentStepName = computed(() => steps.find(s => s.id === activeStep.value)?.label || "");
const completionPercentage = computed(() => Math.round((completedCount.value / steps.length) * 100));

const formattedDate = computed(() => {
  const d = creationDatetime.value instanceof Date ? creationDatetime.value : new Date(creationDatetime.value);
  return d.toLocaleDateString("en-US", { month: "short", day: "numeric", year: "numeric", hour: "2-digit", minute: "2-digit", hour12: true });
});

function goStep(stepId) { activeStep.value = stepId; closeSidebar(); }
function nextStep() {
  if (activeStep.value < steps.length) {
    activeStep.value++;
    completedCount.value = Math.max(completedCount.value, activeStep.value - 1);
    window.scrollTo({ top: 0, behavior: "smooth" });
  }
}
function previousStep() {
  if (activeStep.value > 1) { activeStep.value--; window.scrollTo({ top: 0, behavior: "smooth" }); }
}

async function copyToClipboard() {
  try {
    await navigator.clipboard.writeText(itemCode.value || "");
    isCopied.value = true;
    showToast("ID copied to clipboard!", "success");
    setTimeout(() => isCopied.value = false, 2000);
  } catch { showToast("Failed to copy ID", "error"); }
}

async function saveDraft() {
  isSaving.value = true;
  try {
    await new Promise(r => setTimeout(r, 600));
    showToast("Draft saved successfully!", "success");
  } catch { showToast("Failed to save draft", "error"); }
  finally { isSaving.value = false; }
}

function handleBack() {
  if (completedCount.value > 0) {
    if (confirm("You have unsaved changes. Leave?")) router.push("/user/passports");
  } else { router.push("/user/passports"); }
}

function handleUpdate(data) {
  if (!data) return;
  if (data.productId) productId.value = data.productId;
  if (data.itemCode) itemCode.value = data.itemCode;
  if (data.creationDatetime) creationDatetime.value = data.creationDatetime;
  if (data.status === "completed" && activeStep.value > completedCount.value) completedCount.value = activeStep.value;
}

function toggleSidebar() { sidebarOpen.value = !sidebarOpen.value; }
function closeSidebar() { sidebarOpen.value = false; }

function showToast(message, type = "success") {
  toast.value = { show: true, message, type };
  setTimeout(() => toast.value.show = false, 3000);
}

function handleKeyboard(e) {
  if ((e.ctrlKey || e.metaKey) && e.key === "s") { e.preventDefault(); saveDraft(); }
  if (e.key === "ArrowRight" && activeStep.value < steps.length) nextStep();
  if (e.key === "ArrowLeft" && activeStep.value > 1) previousStep();
}

onMounted(() => window.addEventListener("keydown", handleKeyboard));
onUnmounted(() => window.removeEventListener("keydown", handleKeyboard));
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap');

* { box-sizing: border-box; font-family: 'DM Sans', sans-serif; }

.passport-container {
  min-height: 100vh;
  background: #f5f5f3;
}

.passport-layout {
  display: grid;
  grid-template-columns: 300px 1fr;
  min-height: 100vh;
}

/* =====================
   SIDEBAR
   ===================== */
.sidebar {
  background: #ffffff;
  border-right: 1px solid #e8e8e5;
  position: sticky;
  top: 0;
  height: 100vh;
  overflow-y: auto;
}

.sidebar-overlay { display: none; }

/* =====================
   MAIN
   ===================== */
.main-content {
  padding: 20px 28px 40px;
  display: flex;
  flex-direction: column;
  gap: 14px;
  min-width: 0;
}

/* =====================
   TOPBAR
   ===================== */
.topbar {
  background: #ffffff;
  border: 1px solid #e8e8e5;
  border-radius: 10px;
  padding: 14px 18px;
  display: flex;
  align-items: center;
  gap: 20px;
}

.topbar-menu {
  display: none;
  background: #f5f5f3;
  border: 1px solid #e8e8e5;
  border-radius: 7px;
  padding: 7px;
  cursor: pointer;
  color: #6b6b66;
  flex-shrink: 0;
}

.topbar-id {
  display: flex;
  flex-direction: column;
  gap: 2px;
  flex: 1;
  padding-left: 4px;
  border-left: 1px solid #e8e8e5;
  margin-left: 4px;
}

.topbar-id-label {
  font-size: 10px;
  font-weight: 700;
  color: #a0a09a;
  text-transform: uppercase;
  letter-spacing: 0.08em;
}

.topbar-id-row {
  display: flex;
  align-items: center;
  gap: 7px;
}

.topbar-id-value {
  font-size: 14px;
  font-weight: 600;
  color: #1a1a18;
  font-family: 'DM Mono', monospace;
}

.btn-copy {
  padding: 4px;
  background: transparent;
  border: none;
  color: #a0a09a;
  cursor: pointer;
  border-radius: 4px;
  display: flex;
  align-items: center;
  transition: all 0.15s ease;
}
.btn-copy:hover { color: #1a1a18; }
.btn-copy.copied { color: #1a8c50; }
.btn-copy:disabled { opacity: 0.3; cursor: not-allowed; }

.topbar-date {
  font-size: 11px;
  color: #a0a09a;
  font-family: 'DM Mono', monospace;
}

.topbar-actions {
  display: flex;
  gap: 8px;
  flex-shrink: 0;
}

.btn-action {
  display: flex;
  align-items: center;
  gap: 7px;
  height: 36px;
  padding: 0 13px;
  border-radius: 7px;
  border: 1px solid #e8e8e5;
  background: #fff;
  color: #6b6b66;
  font-size: 12.5px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.15s ease;
  font-family: 'DM Sans', sans-serif;
}
.btn-action:hover:not(:disabled) { background: #f5f5f3; color: #1a1a18; }
.btn-action:disabled { opacity: 0.5; cursor: not-allowed; }
.btn-back { }

/* =====================
   PROGRESS STRIP
   ===================== */
.progress-strip {
  background: #01275a;
  border-radius: 10px;
  padding: 16px 20px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.strip-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
}

.strip-left {
  display: flex;
  flex-direction: column;
  gap: 3px;
}

.strip-right {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 1px;
  flex-shrink: 0;
}

.strip-step {
  font-size: 11px;
  color: rgba(255,255,255,0.4);
  font-weight: 600;
  font-family: 'DM Mono', monospace;
  letter-spacing: 0.06em;
}

.strip-title {
  font-size: 15px;
  font-weight: 700;
  color: #ffffff;
  letter-spacing: -0.01em;
}

.strip-pct {
  font-size: 20px;
  font-weight: 700;
  color: #6ee7b7;
  font-family: 'DM Mono', monospace;
  line-height: 1;
}

.strip-pct-label {
  font-size: 10px;
  color: rgba(255,255,255,0.35);
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.08em;
}

.strip-track {
  height: 3px;
  background: rgba(255,255,255,0.12);
  border-radius: 10px;
  overflow: hidden;
}

.strip-fill {
  height: 100%;
  background: #6ee7b7;
  border-radius: 10px;
  transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

/* =====================
   CONTENT AREA
   ===================== */
.content-area {
  background: #ffffff;
  border: 1px solid #e8e8e5;
  border-radius: 10px;
  padding: 28px;
  min-height: 480px;
}

/* =====================
   NAV CONTROLS
   ===================== */
.nav-controls {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 16px;
}

.btn-nav {
  display: flex;
  align-items: center;
  gap: 7px;
  height: 40px;
  padding: 0 16px;
  font-size: 13px;
  font-weight: 600;
  border-radius: 8px;
  border: none;
  cursor: pointer;
  transition: all 0.15s ease;
  font-family: 'DM Sans', sans-serif;
}

.btn-prev {
  background: #ffffff;
  color: #6b6b66;
  border: 1px solid #e8e8e5;
}
.btn-prev:hover:not(:disabled) { background: #f5f5f3; color: #1a1a18; }

.btn-next {
  background: #01275a;
  color: white;
}
.btn-next:hover:not(:disabled) { background: #01367d; }

.btn-nav:disabled { opacity: 0.4; cursor: not-allowed; }

.dots {
  display: flex;
  gap: 5px;
  align-items: center;
}

.dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: #e0e0dc;
  border: none;
  padding: 0;
  cursor: pointer;
  transition: all 0.2s ease;
}
.dot:hover { background: #c0c0ba; transform: scale(1.2); }
.dot-done { background: #c6ead8; }
.dot-active {
  background: #01275a;
  transform: scale(1.4);
}

/* =====================
   TOAST
   ===================== */
.toast {
  position: fixed;
  bottom: 24px;
  right: 24px;
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 18px;
  background: white;
  border-radius: 9px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.1);
  z-index: 1000;
  font-size: 13px;
  font-weight: 600;
  border-left: 3px solid;
}

.toast-success { border-color: #1a8c50; color: #1a1a18; }
.toast-success .toast-icon { color: #1a8c50; }
.toast-error { border-color: #e05252; color: #1a1a18; }
.toast-error .toast-icon { color: #e05252; }

/* =====================
   TRANSITIONS
   ===================== */
.fade-slide-enter-active, .fade-slide-leave-active { transition: all 0.25s ease; }
.fade-slide-enter-from { opacity: 0; transform: translateX(12px); }
.fade-slide-leave-to { opacity: 0; transform: translateX(-12px); }
.toast-slide-enter-active, .toast-slide-leave-active { transition: all 0.25s ease; }
.toast-slide-enter-from, .toast-slide-leave-to { opacity: 0; transform: translateY(8px); }

/* =====================
   RESPONSIVE
   ===================== */
@media (max-width: 1024px) {
  .passport-layout { grid-template-columns: 1fr; }
  .sidebar {
    position: fixed;
    top: 0; left: -248px;
    width: 248px;
    z-index: 1000;
    transition: left 0.25s ease;
    height: 100vh;
  }
  .sidebar.sidebar-open { left: 0; }
  .sidebar-overlay {
    display: block;
    position: fixed; inset: 0;
    background: rgba(0,0,0,0.4);
    z-index: 999;
  }
  .topbar-menu { display: flex; }
  .main-content { padding: 16px 16px 32px; }
}

@media (max-width: 768px) {
  .topbar { flex-wrap: wrap; }
  .topbar-actions { width: 100%; }
  .btn-action { flex: 1; justify-content: center; }
  .content-area { padding: 18px; }
  .dots { display: none; }
}
</style>