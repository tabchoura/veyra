<template>
  <div class="product-init">
    <div class="page-header">
      <h2 class="page-title">Product Initialization</h2>
      <p class="page-subtitle">Enter the basic information about your product</p>
    </div>

    <div class="divider"></div>

    <!-- Auto-generated fields -->
    <div class="info-card">
      <div class="fields-row">
        <div class="field-group">
          <label class="field-label">Item Code</label>
          <div class="input-wrapper">
            <input class="field-input readonly" :value="displayItemCode" readonly />
            <div class="input-icon">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
              </svg>
            </div>
          </div>
          <p class="field-hint">Auto-generated unique identifier</p>
        </div>

        <div class="field-group">
          <label class="field-label">Creation Date & Time</label>
          <div class="input-wrapper">
            <input class="field-input readonly" :value="formattedDateTime" readonly />
            <div class="input-icon">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
              </svg>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Product Image Upload -->
    <div class="form-section">
      <label class="field-label">
        Product Image <span class="required">*</span>
      </label>

      <div
        class="upload-zone"
        :class="{
          'upload-hover': isDragging,
          'upload-error': errors.product_image,
          'upload-success': uploadedImage
        }"
        @drop.prevent="handleDrop"
        @dragover.prevent="isDragging = true"
        @dragleave.prevent="isDragging = false"
        @click="triggerFileInput"
      >
        <input
          ref="fileInput"
          type="file"
          accept="image/jpeg,image/png,image/webp"
          @change="handleFileSelect"
          style="display: none"
        />

        <div v-if="!uploadedImage" class="upload-content">
          <div class="upload-icon">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
              <circle cx="8.5" cy="8.5" r="1.5"></circle>
              <polyline points="21 15 16 10 5 21"></polyline>
            </svg>
          </div>
          <div class="upload-text">
            <div class="upload-title">Upload Product Image</div>
            <div class="upload-description">Drag & drop or click to browse</div>
            <div class="upload-specs">JPG, PNG, WEBP up to 5MB</div>
          </div>
        </div>

        <div v-else class="upload-preview">
          <img :src="uploadedImage" alt="Product preview" class="preview-image" />
          <div class="preview-overlay">
            <button class="btn-change" @click.stop="triggerFileInput">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
              </svg>
              Change Image
            </button>
            <button class="btn-remove" @click.stop="removeImage">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="3 6 5 6 21 6"></polyline>
                <path
                  d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"
                ></path>
              </svg>
              Remove
            </button>
          </div>
        </div>
      </div>

      <p v-if="errors.product_image" class="field-error">{{ errors.product_image }}</p>
    </div>

    <!-- Product Details -->
    <div class="form-grid">
      <div class="field-group">
        <label class="field-label">Product Name <span class="required">*</span></label>
        <input
          v-model="formData.productName"
          class="field-input"
          :class="{ 'input-error': errors.product_name }"
          placeholder="Enter product name"
          @blur="validateField('productName')"
        />
        <p v-if="errors.product_name" class="field-error">{{ errors.product_name }}</p>
      </div>

      <div class="field-group">
        <label class="field-label">Weight (kg) <span class="required">*</span></label>
        <input
          v-model="formData.weight"
          class="field-input"
          :class="{ 'input-error': errors.weight }"
          type="number"
          placeholder="0.000"
          step="0.001"
          min="0"
          @blur="validateField('weight')"
        />
        <p v-if="errors.weight" class="field-error">{{ errors.weight }}</p>
      </div>

      <div class="field-group">
        <label class="field-label">Batch / Serial Number</label>
        <input v-model="formData.batchNumber" class="field-input" placeholder="Optional" />
      </div>

      <div class="field-group">
        <label class="field-label">PRODCOM Code</label>
        <input v-model="formData.prodcomCode" class="field-input" placeholder="Optional" />
      </div>
    </div>

    <div class="divider"></div>

    <!-- ✅ Declaring Organization (exact like screenshot) -->
    <div class="org-block">
      <h3 class="org-title">Declaring Organization</h3>

      <div class="org-grid">
        <div class="field-group">
          <label class="field-label">Organization Name <span class="required">*</span></label>
          <input
            v-model="formData.orgName"
            class="field-input"
            :class="{ 'input-error': errors.declaring_organization }"
            placeholder="Enter organization name"
            @blur="validateField('orgName')"
          />
          <p v-if="errors.declaring_organization" class="field-error">{{ errors.declaring_organization }}</p>
        </div>

        <div class="field-group">
          <label class="field-label">Organization Country <span class="required">*</span></label>
          <div class="select-wrapper">
            <select
              v-model="formData.orgCountry"
              class="field-input field-select"
              :class="{ 'input-error': errors.organization_country_id }"
              @change="validateField('orgCountry')"
              :disabled="loadingCountries"
            >
              <option value="" disabled>
                {{ loadingCountries ? 'Loading countries...' : 'Select country' }}
              </option>
              <option v-for="country in countries" :key="country.id" :value="country.id">
                {{ country.name_en }}
              </option>
            </select>
            <div class="select-icon">
              <svg v-if="!loadingCountries" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
              <div v-else class="spinner-small"></div>
            </div>
          </div>
          <p v-if="errors.organization_country_id" class="field-error">{{ errors.organization_country_id }}</p>
        </div>

        <div class="field-group">
          <label class="field-label">Address</label>
          <input v-model="formData.address" class="field-input" placeholder="Optional" />
        </div>

        <div class="field-group">
          <label class="field-label">Postal Code</label>
          <input v-model="formData.postalCode" class="field-input" placeholder="Optional" />
          <p v-if="errors.postal_code" class="field-error">{{ errors.postal_code }}</p>
        </div>

        <!-- ✅ Product Description full width under organization -->
        <div class="field-group org-desc">
          <label class="field-label">Product Description <span class="required">*</span></label>
          <textarea
            v-model="formData.itemDescription"
            class="field-input textarea"
            :class="{ 'input-error': errors.item_description }"
            placeholder="Describe your product in detail..."
            @blur="validateField('itemDescription')"
          ></textarea>

          <div class="desc-footer">
            <p v-if="errors.item_description" class="field-error">{{ errors.item_description }}</p>
            <p class="desc-count">{{ (formData.itemDescription || '').length }}/3000 characters</p>
          </div>
        </div>
      </div>
    </div>

    <!-- ✅ Footer like screenshot -->
    <div class="footer-actions">
      <button class="btn-left" @click="emit('previous')" :disabled="isSaving">
        ‹ Previous
      </button>

      <div class="footer-right">
        <button class="btn-secondary" @click="handleSaveDraft" :disabled="isSaving">
          <div v-if="isSaving" class="spinner-small"></div>
          {{ isSaving ? 'Saving...' : 'Save Progress' }}
        </button>

        <button class="btn-primary" @click="handleContinue" :disabled="isSaving">
          Next Step
          <div v-if="isSaving" class="spinner-small"></div>
        </button>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref, computed, reactive, onMounted, watch } from "vue";
import axios from "axios";

const props = defineProps({
  itemCode: String,
  creationDatetime: [String, Date],
  productId: [String, Number],
});

const emit = defineEmits(["next", "previous", "update"]);

const API_BASE_URL = import.meta.env.VITE_API_URL || "http://localhost:8000/api";

// ---------- STATE ----------
const fileInput = ref(null);
const isDragging = ref(false);
const uploadedImage = ref(null);
const uploadedFile = ref(null);
const isSaving = ref(false);

const loadingCountries = ref(false);
const countries = ref([]);

const localItemCode = ref(props.itemCode || "");
const localCreationDatetime = ref(props.creationDatetime || "");
const localProductId = ref(props.productId || null);

watch(() => props.itemCode, (v) => (localItemCode.value = v || ""));
watch(() => props.creationDatetime, (v) => (localCreationDatetime.value = v || ""));
watch(() => props.productId, (v) => (localProductId.value = v || null));

const formData = reactive({
  productName: "",
  weight: "",
  batchNumber: "",
  prodcomCode: "",
  itemDescription: "",
  orgName: "",
  orgCountry: "",
  address: "",
  postalCode: "",
});

const errors = reactive({
  product_image: "",
  product_name: "",
  weight: "",
  declaring_organization: "",
  organization_country_id: "",
  item_description: "",
  postal_code: "",
});

// ---------- HELPERS ----------
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
  if (!token) return null;
  return {
    Authorization: `Bearer ${token}`,
    Accept: "application/json",
  };
}

function api() {
  // axios client simple
  return axios.create({
    baseURL: API_BASE_URL,
    headers: authHeaders() || {},
  });
}

// récupérer l'id quel que soit le format
function extractProductId(resData) {
  return (
    resData?.data?.id ??
    resData?.id ??
    resData?.data?.product_id ??
    resData?.product?.id ??
    resData?.data?.product?.id ??
    null
  );
}

function extractItemCode(resData) {
  return resData?.data?.item_code ?? resData?.item_code ?? null;
}

function extractCreationDatetime(resData) {
  return (
    resData?.data?.creation_datetime ??
    resData?.creation_datetime ??
    resData?.data?.created_at ??
    resData?.created_at ??
    null
  );
}

const displayItemCode = computed(() => {
  if (!localItemCode.value) return "";
  return "DDP-" + String(localItemCode.value).replace(/^ITEM-/, "");
});

const formattedDateTime = computed(() => {
  if (!localCreationDatetime.value) return "";
  const date =
    typeof localCreationDatetime.value === "string"
      ? new Date(localCreationDatetime.value)
      : localCreationDatetime.value;

  return date.toLocaleString("en-US", {
    month: "short",
    day: "numeric",
    year: "numeric",
    hour: "2-digit",
    minute: "2-digit",
    hour12: true,
  });
});

// ---------- FILE UPLOAD ----------
function triggerFileInput() {
  fileInput.value?.click();
}
function handleFileSelect(e) {
  const f = e.target.files?.[0];
  if (f) processFile(f);
}
function handleDrop(e) {
  isDragging.value = false;
  const f = e.dataTransfer.files?.[0];
  if (f) processFile(f);
}
function processFile(file) {
  errors.product_image = "";
  const valid = ["image/jpeg", "image/png", "image/webp"];
  if (!valid.includes(file.type)) {
    errors.product_image = "Please upload a JPG, PNG, or WEBP image";
    return;
  }
  if (file.size > 5 * 1024 * 1024) {
    errors.product_image = "File size must be less than 5MB";
    return;
  }

  uploadedFile.value = file;
  const reader = new FileReader();
  reader.onload = (ev) => (uploadedImage.value = ev.target.result);
  reader.readAsDataURL(file);
}
function removeImage() {
  uploadedImage.value = null;
  uploadedFile.value = null;
  errors.product_image = "";
  if (fileInput.value) fileInput.value.value = "";
}

// ---------- VALIDATION ----------
function validate() {
  errors.product_name = formData.productName.trim() ? "" : "Product name is required";
  errors.weight =
    formData.weight === "" || formData.weight === null
      ? "Weight is required"
      : parseFloat(formData.weight) < 0
      ? "Weight must be positive"
      : "";

  errors.declaring_organization = formData.orgName.trim() ? "" : "Organization name is required";
  errors.organization_country_id = formData.orgCountry ? "" : "Country is required";

  errors.item_description = !formData.itemDescription.trim()
    ? "Description is required"
    : formData.itemDescription.length > 3000
    ? "Max 3000 characters"
    : "";

  // image obligatoire au 1er enregistrement
  if (!localProductId.value && !uploadedFile.value) {
    errors.product_image = "Product image is required";
  } else {
    errors.product_image = "";
  }

  return !Object.values(errors).some(Boolean);
}

function scrollToFirstError() {
  const el = document.querySelector(".field-error, .upload-error");
  el?.scrollIntoView({ behavior: "smooth", block: "center" });
}

// ---------- BUILD FORM DATA ----------
function buildFD(isDraft) {
  const fd = new FormData();
  fd.append("product_name", formData.productName);
  fd.append("weight", String(formData.weight));
  fd.append("batch_serial", formData.batchNumber || "");
  fd.append("prodcom_code", formData.prodcomCode || "");
  fd.append("declaring_organization", formData.orgName);
  fd.append("organization_country_id", String(formData.orgCountry || ""));
  fd.append("organization_address", formData.address || "");
  fd.append("postal_code", formData.postalCode || "");
  fd.append("item_description", formData.itemDescription || "");
  fd.append("is_draft", isDraft ? "1" : "0");
  if (uploadedFile.value) fd.append("product_image", uploadedFile.value);
  return fd;
}

// ---------- API CALLS (SIMPLE) ----------
async function createOrSaveProduct(isDraft) {
  const client = api();
  const fd = buildFD(isDraft);

  // si produit existe -> save-progress avec product_id
  if (localProductId.value) {
    fd.append("product_id", String(localProductId.value));
    const res = await client.post("/products/save-progress", fd);
    return res.data;
  }

  // sinon -> create product
  const res = await client.post("/products", fd);
  return res.data;
}

async function completeVolet1() {
  const client = api();
  if (!localProductId.value) throw new Error("Missing productId before complete-volet1");
  const res = await client.post(`/products/${localProductId.value}/complete-volet1`, {});
  return res.data;
}

// ---------- ACTIONS ----------
async function handleSaveDraft() {
  if (!validate()) {
    scrollToFirstError();
    return;
  }

  isSaving.value = true;
  try {
    const data = await createOrSaveProduct(true);

    // mettre à jour local ids
    localProductId.value = extractProductId(data) ?? localProductId.value;
    localItemCode.value = extractItemCode(data) ?? localItemCode.value;
    localCreationDatetime.value = extractCreationDatetime(data) ?? localCreationDatetime.value;

    emit("update", {
      productId: localProductId.value,
      itemCode: localItemCode.value,
      creationDatetime: localCreationDatetime.value,
      status: "draft",
    });

    alert("Progress saved!");
  } catch (e) {
    if (e.response?.status === 422) {
      alert("Please fix validation errors from server.");
    } else if (e.response?.status === 401) {
      alert("Session expired. Please login again.");
    } else {
      alert(e.response?.data?.message || e.message);
    }
  } finally {
    isSaving.value = false;
  }
}

async function handleContinue() {
  if (!validate()) {
    scrollToFirstError();
    return;
  }

  isSaving.value = true;
  try {
    // 1) create/save product
    const data = await createOrSaveProduct(false);

    localProductId.value = extractProductId(data) ?? localProductId.value;
    localItemCode.value = extractItemCode(data) ?? localItemCode.value;
    localCreationDatetime.value = extractCreationDatetime(data) ?? localCreationDatetime.value;

    if (!localProductId.value) {
      throw new Error("API did not return productId. Check ProductController response format.");
    }

    // 2) complete volet 1
    const completed = await completeVolet1();

    // parfois complete-volet1 renvoie data.id etc.
    localProductId.value = extractProductId(completed) ?? localProductId.value;
    localItemCode.value = extractItemCode(completed) ?? localItemCode.value;
    localCreationDatetime.value = extractCreationDatetime(completed) ?? localCreationDatetime.value;

    // 3) emit update au parent (TRÈS IMPORTANT)
    emit("update", {
      productId: localProductId.value,
      itemCode: localItemCode.value,
      creationDatetime: localCreationDatetime.value,
      status: "completed",
      step1Completed: true,
    });

    emit("next");
  } catch (e) {
    alert(e.response?.data?.message || e.message);
  } finally {
    isSaving.value = false;
  }
}

// ---------- COUNTRIES ----------
async function fetchCountries() {
  const headers = authHeaders();
  if (!headers) {
    alert("You are not logged in. Please login first.");
    return;
  }

  loadingCountries.value = true;
  try {
    const res = await axios.get(`${API_BASE_URL}/countries`, { headers });
    if (res.data?.success && res.data?.data) countries.value = res.data.data;
    else if (Array.isArray(res.data)) countries.value = res.data;
    else countries.value = [];
  } catch (e) {
    alert(`Error loading countries: ${e.response?.data?.message || e.message}`);
  } finally {
    loadingCountries.value = false;
  }
}

onMounted(() => {
  fetchCountries();
});
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap');

* { box-sizing: border-box; }
.product-init {
  max-width: 860px;
  margin: 0 auto;
  padding: 16px;
  font-family: 'DM Sans', sans-serif;
  color: #111827;
}

/* Header */
.page-header { margin-bottom: 14px; }
.page-title {
  margin: 0;
  font-size: 28px;
  font-weight: 800;
  color: #0f172a;
  letter-spacing: -0.02em;
}
.page-subtitle {
  margin: 8px 0 0;
  color: #6b7280;
  font-size: 14.5px;
  font-weight: 600;
}

/* Divider (soft) */
.divider {
  height: 1px;
  background: #eeeef0;
  margin: 20px 0;
}

/* === Cards / Sections === */
.info-card,
.content-area,
.org-block,
.form-section {
  background: #fff;
  border: 1px solid #ececec;
  border-radius: 12px;
}

.info-card {
  padding: 16px 18px;
  margin-bottom: 18px;
}

/* Like section label */
.section-label {
  font-size: 11px;
  font-weight: 800;
  color: #9aa0a6;
  text-transform: uppercase;
  letter-spacing: 0.09em;
  margin-bottom: 12px;
}

/* Auto fields row */
.fields-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 18px;
}
.input-wrapper { position: relative; }

/* Readonly inputs */
.field-input.readonly {
  background: #f7f7f8;
  color: #6b7280;
  cursor: not-allowed;
  padding-right: 44px;
}

/* Input icon (readonly fields) */
.input-icon {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: #9aa0a6;
  pointer-events: none;
}

/* Form grid */
.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px 20px;
  margin-bottom: 12px;
}

.field-group { display: flex; flex-direction: column; }
.field-label {
  display: block;
  font-weight: 700;
  color: #111827;
  margin-bottom: 7px;
  font-size: 13px;
}
.required, .req { color: #e05252; margin-left: 2px; }

/* Inputs (clean pro) */
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
  font-family: 'DM Sans', sans-serif;
}
.field-input::placeholder { color: #c5c5c5; }
.field-input:focus {
  border-color: #1a4fd6;
  box-shadow: 0 0 0 3px rgba(26,79,214,0.10);
}
.field-input.input-error { border-color: #e05252; }
.field-input.input-error:focus { box-shadow: 0 0 0 3px rgba(224,82,82,0.10); }

.textarea {
  min-height: 130px;
  resize: vertical;
}

/* Errors */
.field-error {
  margin: 6px 0 0;
  color: #e05252;
  font-size: 11.5px;
  font-weight: 700;
}
.field-hint {
  margin: 6px 0 0;
  color: #9aa0a6;
  font-size: 12px;
  font-weight: 600;
}

/* Select */
.select-wrapper, .select-wrap { position: relative; }
.field-select {
  appearance: none;
  padding-right: 42px;
  cursor: pointer;
}
.field-select:disabled {
  opacity: 0.55;
  cursor: not-allowed;
}
.select-icon {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: #6b7280;
  pointer-events: none;
}

/* Spinners */
.spinner-small, .spinner-sm {
  width: 16px;
  height: 16px;
  border: 2px solid #e8e8e5;
  border-top-color: #1a4fd6;
  border-radius: 50%;
  animation: spin 0.6s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* === Upload zone (pro clean) === */
.upload-zone {
  border: 1.5px dashed #d8d8d4;
  border-radius: 12px;
  padding: 20px;
  text-align: left;
  background: #fff;
  cursor: pointer;
  transition: all 0.2s ease;
  overflow: hidden;
}
.upload-zone:hover { border-color: #1a4fd6; background: #f8fafe; }
.upload-zone.upload-hover { border-color: #1a4fd6; background: #f0f4fb; }
.upload-zone.upload-error { border-color: #e05252; background: #fff6f6; }
.upload-zone.upload-success { border-style: solid; border-color: #e8e8e5; padding: 0; }

/* If you use old content blocks (upload-content) */
.upload-content { display: flex; flex-direction: column; align-items: center; gap: 14px; padding: 18px 0; }
.upload-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  background: #f0f4fb;
  border: 1px solid #d8e4f5;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #1a4fd6;
}
.upload-title { font-weight: 800; color: #111827; font-size: 15px; }
.upload-description { color: #6b7280; font-size: 13px; font-weight: 600; }
.upload-specs {
  color: #9aa0a6;
  font-size: 11.5px;
  font-weight: 700;
  font-family: 'DM Mono', monospace;
}

/* Preview */
.upload-preview { position: relative; width: 100%; height: 240px; }
.preview-image {
  width: 100%;
  height: 100%;
  object-fit: contain;
  border-radius: 12px;
  display: block;
}
.preview-overlay {
  position: absolute;
  inset: 0;
  background: rgba(0,0,0,0.55);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  opacity: 0;
  transition: opacity 0.2s ease;
  border-radius: 12px;
}
.upload-preview:hover .preview-overlay { opacity: 1; }

.btn-change, .btn-remove {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 9px 14px;
  border: none;
  border-radius: 9px;
  font-weight: 800;
  font-size: 12.5px;
  cursor: pointer;
  transition: all 0.15s ease;
}
.btn-change { background: #fff; color: #111827; }
.btn-change:hover { background: #f3f4f6; }
.btn-remove { background: #e05252; color: #fff; }
.btn-remove:hover { background: #c94040; }

/* Organization block (if you keep your org-grid structure) */
.org-block { margin-top: 8px; padding: 16px 18px; }
.org-title {
  margin: 0 0 12px;
  font-size: 18px;
  font-weight: 900;
  color: #0f172a;
}
.org-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px 20px;
}
.org-desc { grid-column: 1 / -1; }
.desc-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  min-height: 22px;
}
.desc-count, .char-count {
  color: #9aa0a6;
  font-size: 11px;
  font-weight: 700;
  font-family: 'DM Mono', monospace;
  white-space: nowrap;
}
.char-warn { color: #d97706; }

/* Footer actions (clean) */
.footer-actions, .form-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: 22px;
  padding-top: 16px;
  border-top: 1px solid #eeeef0;
}
.footer-right { display: flex; gap: 10px; align-items: center; }

.btn-left, .btn-prev {
  height: 42px;
  padding: 0 14px;
  border-radius: 10px;
  border: 1px solid #e8e8e5;
  background: #fff;
  color: #6b7280;
  font-weight: 800;
  cursor: pointer;
  transition: all 0.15s ease;
}
.btn-left:hover:not(:disabled), .btn-prev:hover:not(:disabled) {
  background: #f5f5f3;
  color: #111827;
}
.btn-left:disabled, .btn-prev:disabled { opacity: .55; cursor: not-allowed; }

.btn-secondary, .btn-save {
  height: 42px;
  padding: 0 16px;
  border-radius: 10px;
  font-weight: 900;
  font-size: 13px;
  cursor: pointer;
  border: 1px solid #e8e8e5;
  background: #fff;
  color: #111827;
  transition: all .15s ease;
  display: inline-flex;
  align-items: center;
  gap: 8px;
}
.btn-secondary:hover:not(:disabled), .btn-save:hover:not(:disabled) { background: #f5f5f3; }

.btn-primary, .btn-next {
  height: 42px;
  padding: 0 16px;
  border-radius: 10px;
  font-weight: 900;
  font-size: 13px;
  cursor: pointer;
  border: none;
  background: #1a4fd6;
  color: #fff;
  transition: all .15s ease;
  display: inline-flex;
  align-items: center;
  gap: 8px;
}
.btn-primary:hover:not(:disabled), .btn-next:hover:not(:disabled) { background: #1640b8; }

.btn-secondary:disabled, .btn-primary:disabled,
.btn-save:disabled, .btn-next:disabled { opacity: .55; cursor: not-allowed; }

/* Responsive */
@media (max-width: 900px) {
  .fields-row, .form-grid, .org-grid { grid-template-columns: 1fr; }
  .footer-actions, .form-footer { flex-direction: column; align-items: stretch; gap: 12px; }
  .footer-right { justify-content: space-between; }
  .btn-left, .btn-secondary, .btn-primary, .btn-prev, .btn-save, .btn-next {
    width: 100%;
    justify-content: center;
  }
  .upload-preview { height: 210px; }
}
</style>
