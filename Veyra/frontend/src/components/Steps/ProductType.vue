<template>
  <div class="product-type">

    <!-- Header -->
    <div class="page-header">
      <h2 class="page-title">Product Type</h2>
      <p class="page-subtitle">Select the category and subcategory of your product</p>
    </div>

    <div class="divider"></div>

    <!-- ✅ NO MORE "Missing productId" MESSAGE -->
    <!-- Optional: you can show a soft hint (hidden by default) -->
    <!--
    <div v-if="!productId" class="soft-hint">
      Complete Step 1 to enable selections.
    </div>
    -->

    <!-- Form -->
    <div class="form-grid">
      <div class="field-group">
        <label class="field-label">Category <span class="req">*</span></label>

        <div class="select-wrap">
          <select
            v-model="form.category_id"
            class="field-input field-select"
            :class="{ 'input-error': errors.category_id }"
            :disabled="disabledAll || loading"
            @change="onCategoryChange"
          >
            <option value="" disabled>
              {{ loading ? "Loading categories..." : "Select category" }}
            </option>

            <option v-for="c in categories" :key="c.id" :value="c.id">
              {{ c.name }}
            </option>
          </select>

          <div class="select-icon">
            <div v-if="loading" class="spinner-sm"></div>
            <svg v-else width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
          </div>
        </div>

        <p v-if="errors.category_id" class="field-error">{{ errors.category_id }}</p>
      </div>

      <div class="field-group">
        <label class="field-label">Subcategory <span class="req">*</span></label>

        <div class="select-wrap">
          <select
            v-model="form.subcategory_id"
            class="field-input field-select"
            :class="{ 'input-error': errors.subcategory_id }"
            :disabled="disabledAll || !form.category_id"
            @change="validateField('subcategory_id')"
          >
            <option value="" disabled>Select subcategory</option>

            <option v-for="s in filteredSubcategories" :key="s.id" :value="s.id">
              {{ s.name }}
            </option>
          </select>

          <div class="select-icon">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
          </div>
        </div>

        <p v-if="errors.subcategory_id" class="field-error">{{ errors.subcategory_id }}</p>
      </div>
    </div>

    <div class="divider"></div>

    <!-- Actions -->
    <div class="form-footer">
      <button class="btn-prev" @click="emit('previous')" :disabled="isSaving">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <polyline points="15 18 9 12 15 6"/>
        </svg>
        Previous
      </button>

      <div class="footer-right">
        <button class="btn-save" @click="saveDraft" :disabled="isSaving || disabledAll">
          <div v-if="isSaving" class="spinner-sm"></div>
          {{ isSaving ? "Saving..." : "Save Progress" }}
        </button>

        <button class="btn-next" @click="saveAndContinue" :disabled="isSaving || !canContinue">
          Next step 
          <div v-if="isSaving" class="spinner-sm"></div>
        </button>
      </div>
    </div>

    <p v-if="serverError" class="server-error">{{ serverError }}</p>
  </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref, watch } from "vue";
import axios from "axios";

const props = defineProps({
  productId: [String, Number],
});

const emit = defineEmits(["next", "previous", "update"]);

const API_BASE_URL = import.meta.env.VITE_API_URL || "http://localhost:8000/api";

const loading = ref(false);
const isSaving = ref(false);
const serverError = ref("");

const categories = ref([]);

const form = reactive({
  category_id: "",
  subcategory_id: "",
});

const errors = reactive({
  category_id: "",
  subcategory_id: "",
});

const disabledAll = computed(() => !props.productId);

function getToken() {
  return (
    localStorage.getItem("token") ||
    localStorage.getItem("auth_token") ||
    localStorage.getItem("access_token") ||
    localStorage.getItem("authToken") ||
    localStorage.getItem("accessToken")
  );
}

async function fetchCategories() {
  if (!props.productId) return; // ✅ silent
  loading.value = true;
  serverError.value = "";

  try {
    const token = getToken();
    const res = await axios.get(`${API_BASE_URL}/categories`, {
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: "application/json",
      },
    });

    categories.value = res.data?.data || [];
  } catch (e) {
    serverError.value = e.response?.data?.message || e.message;
  } finally {
    loading.value = false;
  }
}

watch(
  () => props.productId,
  (val) => {
    // ✅ when productId arrives after Step 1, auto fetch categories
    if (val) fetchCategories();
  },
  { immediate: true }
);

const selectedCategory = computed(() => {
  return categories.value.find((c) => String(c.id) === String(form.category_id));
});

const filteredSubcategories = computed(() => {
  return selectedCategory.value?.subcategories || [];
});

function onCategoryChange() {
  errors.category_id = "";
  form.subcategory_id = "";
}

function validateField(field) {
  if (field === "category_id") {
    errors.category_id = form.category_id ? "" : "Category is required";
  }
  if (field === "subcategory_id") {
    errors.subcategory_id = form.subcategory_id ? "" : "Subcategory is required";
  }
}

function validateForm(strict = true) {
  serverError.value = "";
  validateField("category_id");
  if (strict) validateField("subcategory_id");
  return !errors.category_id && (!strict || !errors.subcategory_id);
}

const canContinue = computed(() => {
  return !!props.productId && !!form.category_id && !!form.subcategory_id && !isSaving.value;
});

async function saveDraft() {
  if (!props.productId) return; // ✅ silent
  if (!validateForm(false)) return;

  isSaving.value = true;
  serverError.value = "";

  try {
    const token = getToken();
    const payload = {
      category_id: form.category_id,
      subcategory_id: form.subcategory_id || null,
    };

    await axios.post(
      `${API_BASE_URL}/products/${props.productId}/type/save-progress`,
      payload,
      {
        headers: {
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
        },
      }
    );

    emit("update", { status: "draft" });
  } catch (e) {
    if (e.response?.status === 422) {
      const errs = e.response?.data?.errors || {};
      errors.category_id = errs.category_id?.[0] || "";
      errors.subcategory_id = errs.subcategory_id?.[0] || "";
    }
    serverError.value = e.response?.data?.message || e.message;
  } finally {
    isSaving.value = false;
  }
}

async function saveAndContinue() {
  if (!props.productId) return; // ✅ silent
  if (!validateForm(true)) return;

  isSaving.value = true;
  serverError.value = "";

  try {
    const token = getToken();
    const payload = {
      category_id: form.category_id,
      subcategory_id: form.subcategory_id,
    };

    await axios.post(
      `${API_BASE_URL}/products/${props.productId}/type/validate-step`,
      payload,
      {
        headers: {
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
        },
      }
    );

    emit("update", { status: "completed" });
    emit("next");
  } catch (e) {
    if (e.response?.status === 422) {
      const errs = e.response?.data?.errors || {};
      errors.category_id = errs.category_id?.[0] || "";
      errors.subcategory_id = errs.subcategory_id?.[0] || "";
    }
    serverError.value = e.response?.data?.message || e.message;
  } finally {
    isSaving.value = false;
  }
}

onMounted(() => {
  // watch() already handles it, but ok to keep empty
});
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap');

* { box-sizing: border-box; font-family: 'DM Sans', sans-serif; }

.product-type { max-width: 860px; margin: 0 auto; }

.page-header { margin-bottom: 14px; }
.page-title { margin: 0; font-size: 28px; font-weight: 800; color: #0f172a; letter-spacing: -0.02em; }
.page-subtitle { margin: 8px 0 0; color: #6b7280; font-size: 14.5px; font-weight: 600; }

.divider { height: 1px; background: #eeeef0; margin: 20px 0; }

.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px 20px; margin-bottom: 12px; }

.field-group { display: flex; flex-direction: column; }
.field-label { font-size: 12.5px; font-weight: 700; color: #111827; margin-bottom: 7px; }
.req { color: #e05252; margin-left: 2px; }

.field-input {
  width: 100%;
  border: 1px solid #e8e8e5;
  border-radius: 10px;
  padding: 10px 13px;
  outline: none;
  font-size: 13.5px;
  font-weight: 500;
  background: #fff;
  color: #111827;
  transition: all 0.15s ease;
}
.field-input:focus { border-color: #1a4fd6; box-shadow: 0 0 0 3px rgba(26,79,214,0.10); }
.field-input.input-error { border-color: #e05252; }
.field-input.input-error:focus { box-shadow: 0 0 0 3px rgba(224,82,82,0.10); }

.select-wrap { position: relative; }
.field-select { appearance: none; padding-right: 38px; cursor: pointer; }
.field-select:disabled { opacity: 0.55; cursor: not-allowed; }

.select-icon { position: absolute; right: 11px; top: 50%; transform: translateY(-50%); color: #6b7280; pointer-events: none; }

.spinner-sm {
  width: 14px; height: 14px;
  border: 2px solid #e8e8e5;
  border-top-color: #1a4fd6;
  border-radius: 50%;
  animation: spin 0.6s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

.field-error { margin: 6px 0 0; color: #e05252; font-size: 11.5px; font-weight: 700; }
.server-error { margin-top: 14px; color: #991b1b; font-weight: 700; }

.form-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: 22px;
  padding-top: 16px;
  border-top: 1px solid #eeeef0;
}
.footer-right { display: flex; gap: 10px; align-items: center; }

.btn-prev, .btn-save, .btn-next {
  height: 40px;
  padding: 0 16px;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 800;
  cursor: pointer;
  transition: all 0.15s ease;
  display: inline-flex;
  align-items: center;
  gap: 8px;
}

.btn-prev {
  border: 1px solid #e8e8e5;
  background: #fff;
  color: #6b7280;
}
.btn-prev:hover:not(:disabled) { background: #f5f5f3; color: #111827; }

.btn-save {
  border: 1px solid #e8e8e5;
  background: #fff;
  color: #111827;
}
.btn-save:hover:not(:disabled) { background: #f5f5f3; }

.btn-next {
  border: none;
  background: #1a4fd6;
  color: #fff;
}
.btn-next:hover:not(:disabled) { background: #1640b8; }

.btn-prev:disabled, .btn-save:disabled, .btn-next:disabled {
  opacity: 0.55;
  cursor: not-allowed;
}

@media (max-width: 900px) {
  .form-grid { grid-template-columns: 1fr; }
  .form-footer { flex-direction: column; align-items: stretch; gap: 12px; }
  .footer-right { justify-content: space-between; }
  .btn-prev, .btn-save, .btn-next { width: 100%; justify-content: center; }
}
</style>
