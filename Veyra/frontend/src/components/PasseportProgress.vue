<template>
  <div class="sidebar-wrapper">
    <div class="sidebar-header">
      <h3 class="sidebar-title">Passport Progress</h3>
      <p class="sidebar-subtitle">{{ completedCount }} of {{ steps.length }} steps completed</p>
    </div>

    <div class="overall-progress">
      <div class="overall-progress-fill" :style="{ width: progressPercentage + '%' }"></div>
    </div>

    <nav class="steps-navigation">
      <button
        v-for="step in steps"
        :key="step.id"
        class="step-button"
        :class="{
          'step-current': step.id === activeStep,
          'step-done': step.id < activeStep
        }"
        @click="selectStep(step.id)"
      >
        <div class="step-number-circle">
          <svg
            v-if="step.id < activeStep"
            width="16"
            height="16"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="3"
          >
            <polyline points="20 6 9 17 4 12"></polyline>
          </svg>
          <span v-else>{{ step.id }}</span>
        </div>
        <span class="step-name">{{ step.label }}</span>
      </button>
    </nav>
  </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
  steps: { type: Array, required: true },
  activeStep: { type: Number, required: true },
  completedCount: { type: Number, default: 0 },
});

const emit = defineEmits(["select"]);

const progressPercentage = computed(() => {
  return Math.round((props.completedCount / props.steps.length) * 100);
});

function selectStep(stepId) {
  emit("select", stepId);
}
</script>

<style scoped>
/* ========================================
   THEME 1: Clean White & Blue (Modern)
   ======================================== */
.sidebar-wrapper {
  padding: 28px 20px;
  height: 100%;
  overflow-y: auto;
  background: #ffffff;
  border-right: 1px solid #e5e7eb;
}

.sidebar-wrapper::-webkit-scrollbar {
  width: 6px;
}

.sidebar-wrapper::-webkit-scrollbar-track {
  background: #f9fafb;
}

.sidebar-wrapper::-webkit-scrollbar-thumb {
  background: #d1d5db;
  border-radius: 3px;
}

.sidebar-wrapper::-webkit-scrollbar-thumb:hover {
  background: #9ca3af;
}

.sidebar-header {
  margin-bottom: 24px;
}

.sidebar-title {
  font-size: 20px;
  font-weight: 700;
  color: #111827;
  margin: 0 0 6px 0;
}

.sidebar-subtitle {
  font-size: 13px;
  color: #6b7280;
  margin: 0;
  font-weight: 500;
}

.overall-progress {
  height: 6px;
  background: #e5e7eb;
  border-radius: 10px;
  overflow: hidden;
  margin-bottom: 28px;
}

.overall-progress-fill {
  height: 100%;
  background: linear-gradient(90deg, #3b82f6 0%, #2563eb 100%);
  border-radius: 10px;
  transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 0 10px rgba(59, 130, 246, 0.3);
}

.steps-navigation {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.step-button {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 12px 14px;
  background: transparent;
  border: none;
  border-radius: 10px;
  cursor: pointer;
  transition: all 0.2s ease;
  text-align: left;
  color: #6b7280;
  position: relative;
}

.step-button:hover {
  background: #f3f4f6;
  transform: translateX(2px);
}

.step-button::before {
  content: '';
  position: absolute;
  left: 0;
  top: 50%;
  transform: translateY(-50%);
  width: 3px;
  height: 0;
  background: #3b82f6;
  border-radius: 0 3px 3px 0;
  transition: height 0.2s ease;
}

.step-current {
  background: #eff6ff;
  color: #1e40af;
  font-weight: 600;
}

.step-current::before {
  height: 70%;
}

.step-done {
  color: #059669;
}

.step-number-circle {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: #f3f4f6;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  font-weight: 700;
  color: #9ca3af;
  flex-shrink: 0;
  transition: all 0.2s ease;
  border: 2px solid transparent;
}

.step-current .step-number-circle {
  background: #3b82f6;
  color: white;
  border-color: #3b82f6;
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.step-done .step-number-circle {
  background: #10b981;
  color: white;
  border-color: #10b981;
}

.step-name {
  font-size: 14px;
  font-weight: 500;
  line-height: 1.4;
}

@media (max-width: 1024px) {
  .sidebar-wrapper {
    padding: 24px 18px;
  }
}

@media (max-width: 640px) {
  .sidebar-title {
    font-size: 18px;
  }

  .step-button {
    padding: 10px 12px;
    gap: 12px;
  }

  .step-number-circle {
    width: 32px;
    height: 32px;
    font-size: 13px;
  }

  .step-name {
    font-size: 13px;
  }
}
</style>