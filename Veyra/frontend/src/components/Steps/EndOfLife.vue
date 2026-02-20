<template>
  <div class="eol-page">
    <!-- Header (same theme) -->
    <div class="page-header">
      <h2 class="page-title">End of Life</h2>
      <p class="page-subtitle">Define how this product can be disposed of responsibly</p>
    </div>

    <div class="divider"></div>

    <div v-if="loadingInit" class="eol-alert info">ℹ️ Loading end-of-life data…</div>

    <div v-else>
      <div v-if="error" class="eol-alert error">❌ {{ error }}</div>

      <!-- Expected End of Life -->
      <div class="card">
        <div class="card-title">
          <span class="icon">📅</span>
          <span>Expected End of Life</span>
        </div>

        <div class="grid-2">
          <div class="field">
            <label class="label">End of Life Date <span class="req">*</span></label>
            <input
              type="date"
              v-model="form.end_of_life_date"
              class="input"
              :class="{ error: err('end_of_life_date') }"
              :disabled="busy"
              @change="clearErr('end_of_life_date')"
            />
            <p v-if="err('end_of_life_date')" class="err">{{ err("end_of_life_date") }}</p>
          </div>

          <div class="field">
            <label class="label">End of Life Country <span class="req">*</span></label>
            <div class="select-wrap">
              <select
                v-model="form.end_of_life_country_id"
                class="input select"
                :class="{ error: err('end_of_life_country_id') }"
                :disabled="busy"
                @change="clearErr('end_of_life_country_id')"
              >
                <option :value="null" disabled>Select country</option>
                <option v-for="c in countries" :key="c.id" :value="c.id">
                  {{ c.name_en || c.name }}
                </option>
              </select>
              <span class="select-icon">▾</span>
            </div>
            <p v-if="err('end_of_life_country_id')" class="err">{{ err("end_of_life_country_id") }}</p>
          </div>
        </div>
      </div>

      <!-- Recoverable + comment -->
      <div class="card">
        <div class="card-title">
          <span class="icon">♻️</span>
          <span>Recoverable</span>
        </div>

        <div class="toggle-card">
          <div class="toggle-left">
            <span class="spark">♻️</span>
            <div>
              <div class="toggle-title">Is recoverable? <span class="req">*</span></div>
              <div class="toggle-sub">Can the product be recovered/valued in any way?</div>
            </div>
          </div>

          <label class="switch">
            <input type="checkbox" v-model="form.is_recoverable" :disabled="busy" />
            <span class="slider"></span>
          </label>
        </div>

        <div class="grid-1" style="margin-top: 14px;">
          <div class="field">
            <label class="label">Comment</label>
            <textarea
              rows="3"
              v-model.trim="form.comment"
              class="input textarea"
              :disabled="busy"
              placeholder="Optional…"
            ></textarea>
            <p class="hint">Add any useful notes (optional).</p>
          </div>
        </div>
      </div>

      <!-- Disposal methods -->
      <div class="methods">
        <div class="method-card">
          <div class="method-left">
            <span class="badge green">↺</span>
            <div>
              <div class="method-title">Reusable</div>
              <div class="method-sub">Product can be reused</div>
            </div>
          </div>
          <label class="switch">
            <input type="checkbox" v-model="form.reuse" :disabled="busy" />
            <span class="slider"></span>
          </label>
        </div>

        <div class="method-card">
          <div class="method-left">
            <span class="badge blue">♻</span>
            <div>
              <div class="method-title">Recyclable</div>
              <div class="method-sub">Materials can be recycled</div>
            </div>
          </div>
          <label class="switch">
            <input type="checkbox" v-model="form.recycling" :disabled="busy" @change="onToggleBlock('recycling')" />
            <span class="slider"></span>
          </label>
        </div>

        <div class="method-card">
          <div class="method-left">
            <span class="badge orange">🔥</span>
            <div>
              <div class="method-title">Incineration</div>
              <div class="method-sub">Energy recovery through burning</div>
            </div>
          </div>
          <label class="switch">
            <input type="checkbox" v-model="form.incineration" :disabled="busy" @change="onToggleBlock('incineration')" />
            <span class="slider"></span>
          </label>
        </div>

        <div class="method-card">
          <div class="method-left">
            <span class="badge green">🍃</span>
            <div>
              <div class="method-title">Composting</div>
              <div class="method-sub">Biodegradable disposal</div>
            </div>
          </div>
          <label class="switch">
            <input type="checkbox" v-model="form.composting" :disabled="busy" @change="onToggleBlock('composting')" />
            <span class="slider"></span>
          </label>
        </div>

        <div class="method-card full">
          <div class="method-left">
            <span class="badge gray">🗑</span>
            <div>
              <div class="method-title">Landfill</div>
              <div class="method-sub">Last resort disposal method</div>
            </div>
          </div>
          <label class="switch">
            <input type="checkbox" v-model="form.landfill" :disabled="busy" @change="onToggleBlock('landfill')" />
            <span class="slider"></span>
          </label>
        </div>
      </div>

      <!-- ✅ show missing disposal methods error -->
      <div v-if="err('disposal_methods')" class="eol-alert error" style="margin-top:-6px;">
        ❌ {{ err("disposal_methods") }}
      </div>

      <!-- Conditional blocks -->
      <div v-if="form.recycling" class="card">
        <div class="card-title"><span class="icon">♻️</span><span>Recycling Details</span></div>

        <div class="grid-2">
          <div class="field">
            <label class="label">Recycling Country <span class="req">*</span></label>
            <div class="select-wrap">
              <select
                v-model="form.recycling_country_id"
                class="input select"
                :class="{ error: err('recycling_country_id') }"
                :disabled="busy"
                @change="clearErr('recycling_country_id')"
              >
                <option :value="null" disabled>Select country</option>
                <option v-for="c in countries" :key="c.id" :value="c.id">{{ c.name_en || c.name }}</option>
              </select>
              <span class="select-icon">▾</span>
            </div>
            <p v-if="err('recycling_country_id')" class="err">{{ err("recycling_country_id") }}</p>
          </div>

          <div class="field">
            <label class="label">Recycling Method <span class="req">*</span></label>
            <input
              v-model.trim="form.recycling_method"
              class="input"
              :class="{ error: err('recycling_method') }"
              :disabled="busy"
              placeholder="Method..."
              @input="clearErr('recycling_method')"
            />
            <p v-if="err('recycling_method')" class="err">{{ err("recycling_method") }}</p>
          </div>
        </div>

        <div class="grid-2">
          <div class="field">
            <label class="label">Valued Product</label>
            <input v-model.trim="form.recycling_valued_product" class="input" :disabled="busy" placeholder="Optional..." />
          </div>
          <div class="field">
            <label class="label">Organization</label>
            <input v-model.trim="form.recycling_organization" class="input" :disabled="busy" placeholder="Optional..." />
          </div>
        </div>
      </div>

      <div v-if="form.incineration" class="card">
        <div class="card-title"><span class="icon">🔥</span><span>Incineration Details</span></div>

        <div class="grid-2">
          <div class="field">
            <label class="label">Incineration Country <span class="req">*</span></label>
            <div class="select-wrap">
              <select
                v-model="form.incineration_country_id"
                class="input select"
                :class="{ error: err('incineration_country_id') }"
                :disabled="busy"
                @change="clearErr('incineration_country_id')"
              >
                <option :value="null" disabled>Select country</option>
                <option v-for="c in countries" :key="c.id" :value="c.id">{{ c.name_en || c.name }}</option>
              </select>
              <span class="select-icon">▾</span>
            </div>
            <p v-if="err('incineration_country_id')" class="err">{{ err("incineration_country_id") }}</p>
          </div>

          <div class="field">
            <label class="label">Incineration Method <span class="req">*</span></label>
            <input
              v-model.trim="form.incineration_method"
              class="input"
              :class="{ error: err('incineration_method') }"
              :disabled="busy"
              placeholder="Method..."
              @input="clearErr('incineration_method')"
            />
            <p v-if="err('incineration_method')" class="err">{{ err("incineration_method") }}</p>
          </div>
        </div>

        <div class="grid-2">
          <div class="field">
            <label class="label">Valued Product</label>
            <input v-model.trim="form.incineration_valued_product" class="input" :disabled="busy" placeholder="Optional..." />
          </div>
          <div class="field">
            <label class="label">Organization</label>
            <input v-model.trim="form.incineration_organization" class="input" :disabled="busy" placeholder="Optional..." />
          </div>
        </div>
      </div>

      <div v-if="form.composting" class="card">
        <div class="card-title"><span class="icon">🍃</span><span>Composting Details</span></div>

        <div class="grid-2">
          <div class="field">
            <label class="label">Composting Country <span class="req">*</span></label>
            <div class="select-wrap">
              <select
                v-model="form.composting_country_id"
                class="input select"
                :class="{ error: err('composting_country_id') }"
                :disabled="busy"
                @change="clearErr('composting_country_id')"
              >
                <option :value="null" disabled>Select country</option>
                <option v-for="c in countries" :key="c.id" :value="c.id">{{ c.name_en || c.name }}</option>
              </select>
              <span class="select-icon">▾</span>
            </div>
            <p v-if="err('composting_country_id')" class="err">{{ err("composting_country_id") }}</p>
          </div>

          <div class="field">
            <label class="label">Composting Method <span class="req">*</span></label>
            <input
              v-model.trim="form.composting_method"
              class="input"
              :class="{ error: err('composting_method') }"
              :disabled="busy"
              placeholder="Method..."
              @input="clearErr('composting_method')"
            />
            <p v-if="err('composting_method')" class="err">{{ err("composting_method") }}</p>
          </div>
        </div>

        <div class="grid-2">
          <div class="field">
            <label class="label">Valued Product</label>
            <input v-model.trim="form.composting_valued_product" class="input" :disabled="busy" placeholder="Optional..." />
          </div>
          <div class="field">
            <label class="label">Organization</label>
            <input v-model.trim="form.composting_organization" class="input" :disabled="busy" placeholder="Optional..." />
          </div>
        </div>
      </div>

      <div v-if="form.landfill" class="card">
        <div class="card-title"><span class="icon">🗑️</span><span>Landfill Details</span></div>

        <div class="grid-2">
          <div class="field">
            <label class="label">Landfill Country <span class="req">*</span></label>
            <div class="select-wrap">
              <select
                v-model="form.landfill_country_id"
                class="input select"
                :class="{ error: err('landfill_country_id') }"
                :disabled="busy"
                @change="clearErr('landfill_country_id')"
              >
                <option :value="null" disabled>Select country</option>
                <option v-for="c in countries" :key="c.id" :value="c.id">{{ c.name_en || c.name }}</option>
              </select>
              <span class="select-icon">▾</span>
            </div>
            <p v-if="err('landfill_country_id')" class="err">{{ err("landfill_country_id") }}</p>
          </div>

          <div class="field">
            <label class="label">Landfill Method <span class="req">*</span></label>
            <input
              v-model.trim="form.landfill_method"
              class="input"
              :class="{ error: err('landfill_method') }"
              :disabled="busy"
              placeholder="Method..."
              @input="clearErr('landfill_method')"
            />
            <p v-if="err('landfill_method')" class="err">{{ err("landfill_method") }}</p>
          </div>
        </div>

        <div class="grid-2">
          <div class="field">
            <label class="label">Valued Product</label>
            <input v-model.trim="form.landfill_valued_product" class="input" :disabled="busy" placeholder="Optional..." />
          </div>
          <div class="field">
            <label class="label">Organization</label>
            <input v-model.trim="form.landfill_organization" class="input" :disabled="busy" placeholder="Optional..." />
          </div>
        </div>
      </div>

      <!-- Summary -->
      <div class="summary">
        <div class="summary-title">End of Life Summary</div>
        <div class="summary-text">
          <span v-if="selectedMethods.length === 0">No disposal methods selected</span>
          <span v-else>{{ selectedMethods.join(" • ") }}</span>
        </div>
      </div>

      <!-- Actions -->
      <div class="actions">
        <button class="btn-secondary" type="button" @click="goPrevious" :disabled="busy">
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
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "axios";

const props = defineProps({
  productId: { type: [Number, String], required: false },
});
const emit = defineEmits(["next", "previous", "update"]);

const route = useRoute();
const router = useRouter();

const API_BASE_URL = import.meta.env.VITE_API_URL || "http://localhost:8000/api";

// ✅ robust productId (props → query → params → localStorage)
const productId = computed(() => {
  const pid =
    props.productId ??
    route.query.productId ??
    route.params.productId ??
    localStorage.getItem("product_id") ??
    localStorage.getItem("currentProductId");

  if (pid === null || pid === undefined || pid === "") return null;

  // store for next steps
  localStorage.setItem("product_id", String(pid));
  localStorage.setItem("currentProductId", String(pid));

  return String(pid);
});

const countries = ref([]);
const loadingInit = ref(true);
const busy = ref(false);
const error = ref("");
const toast = ref("");
const toastType = ref("success");

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
  is_recoverable: false,
  comment: "",

  end_of_life_date: "",
  end_of_life_country_id: null,

  reuse: false,
  recycling: false,
  incineration: false,
  composting: false,
  landfill: false,

  recycling_country_id: null,
  recycling_method: "",
  recycling_valued_product: "",
  recycling_organization: "",

  incineration_country_id: null,
  incineration_method: "",
  incineration_valued_product: "",
  incineration_organization: "",

  composting_country_id: null,
  composting_method: "",
  composting_valued_product: "",
  composting_organization: "",

  landfill_country_id: null,
  landfill_method: "",
  landfill_valued_product: "",
  landfill_organization: "",
});

/** field errors */
const errors = reactive({
  end_of_life_date: "",
  end_of_life_country_id: "",

  recycling_country_id: "",
  recycling_method: "",

  incineration_country_id: "",
  incineration_method: "",

  composting_country_id: "",
  composting_method: "",

  landfill_country_id: "",
  landfill_method: "",

  disposal_methods: "",
});

function err(k) {
  return errors[k] || "";
}
function clearErr(k) {
  errors[k] = "";
}
function resetErrs() {
  Object.keys(errors).forEach((k) => (errors[k] = ""));
}

const selectedMethods = computed(() => {
  const arr = [];
  if (form.reuse) arr.push("Reusable");
  if (form.recycling) arr.push("Recyclable");
  if (form.incineration) arr.push("Incineration");
  if (form.composting) arr.push("Composting");
  if (form.landfill) arr.push("Landfill");
  return arr;
});

function clearBlock(prefix) {
  form[`${prefix}_country_id`] = null;
  form[`${prefix}_method`] = "";
  form[`${prefix}_valued_product`] = "";
  form[`${prefix}_organization`] = "";
  clearErr(`${prefix}_country_id`);
  clearErr(`${prefix}_method`);
}

function onToggleBlock(prefix) {
  if (!form[prefix]) clearBlock(prefix);
}

function normalizeByToggles() {
  if (!form.recycling) clearBlock("recycling");
  if (!form.incineration) clearBlock("incineration");
  if (!form.composting) clearBlock("composting");
  if (!form.landfill) clearBlock("landfill");
}

function buildPayload() {
  normalizeByToggles();

  return {
    is_recoverable: !!form.is_recoverable,
    comment: form.comment?.trim() ? form.comment.trim() : null,

    end_of_life_date: form.end_of_life_date || null,
    end_of_life_country_id: form.end_of_life_country_id ?? null,

    reuse: !!form.reuse,
    recycling: !!form.recycling,
    incineration: !!form.incineration,
    composting: !!form.composting,
    landfill: !!form.landfill,

    recycling_country_id: form.recycling ? (form.recycling_country_id ?? null) : null,
    recycling_method: form.recycling ? (form.recycling_method?.trim() || null) : null,
    recycling_valued_product: form.recycling_valued_product?.trim() || null,
    recycling_organization: form.recycling_organization?.trim() || null,

    incineration_country_id: form.incineration ? (form.incineration_country_id ?? null) : null,
    incineration_method: form.incineration ? (form.incineration_method?.trim() || null) : null,
    incineration_valued_product: form.incineration_valued_product?.trim() || null,
    incineration_organization: form.incineration_organization?.trim() || null,

    composting_country_id: form.composting ? (form.composting_country_id ?? null) : null,
    composting_method: form.composting ? (form.composting_method?.trim() || null) : null,
    composting_valued_product: form.composting_valued_product?.trim() || null,
    composting_organization: form.composting_organization?.trim() || null,

    landfill_country_id: form.landfill ? (form.landfill_country_id ?? null) : null,
    landfill_method: form.landfill ? (form.landfill_method?.trim() || null) : null,
    landfill_valued_product: form.landfill_valued_product?.trim() || null,
    landfill_organization: form.landfill_organization?.trim() || null,
  };
}

function frontValidate() {
  resetErrs();
  let ok = true;

  if (!form.end_of_life_date) {
    errors.end_of_life_date = "End of life date is required";
    ok = false;
  }
  if (!form.end_of_life_country_id) {
    errors.end_of_life_country_id = "End of life country is required";
    ok = false;
  }

  if (!form.reuse && !form.recycling && !form.incineration && !form.composting && !form.landfill) {
    errors.disposal_methods = "Please select at least one disposal method";
    ok = false;
  }

  if (form.recycling) {
    if (!form.recycling_country_id) {
      errors.recycling_country_id = "Recycling country is required";
      ok = false;
    }
    if (!form.recycling_method?.trim()) {
      errors.recycling_method = "Recycling method is required";
      ok = false;
    }
  }

  if (form.incineration) {
    if (!form.incineration_country_id) {
      errors.incineration_country_id = "Incineration country is required";
      ok = false;
    }
    if (!form.incineration_method?.trim()) {
      errors.incineration_method = "Incineration method is required";
      ok = false;
    }
  }

  if (form.composting) {
    if (!form.composting_country_id) {
      errors.composting_country_id = "Composting country is required";
      ok = false;
    }
    if (!form.composting_method?.trim()) {
      errors.composting_method = "Composting method is required";
      ok = false;
    }
  }

  if (form.landfill) {
    if (!form.landfill_country_id) {
      errors.landfill_country_id = "Landfill country is required";
      ok = false;
    }
    if (!form.landfill_method?.trim()) {
      errors.landfill_method = "Landfill method is required";
      ok = false;
    }
  }

  return ok;
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

  Object.entries(bag).forEach(([k, msgs]) => {
    const msg = Array.isArray(msgs) ? msgs[0] : String(msgs);
    if (errors[k] !== undefined) errors[k] = msg;
  });
}

async function fetchCountries() {
  const res = await axios.get(`${API_BASE_URL}/countries`, { headers: authHeaders() });
  countries.value = res.data?.data ?? res.data ?? [];
}

async function fetchEol() {
  if (!productId.value) {
    error.value = "Missing productId. Please complete Step 1 first.";
    return;
  }

  const res = await axios.get(`${API_BASE_URL}/products/${productId.value}/end-of-life`, {
    headers: authHeaders(),
  });

  const e = res.data?.data;
  if (!e) return;

  form.is_recoverable = !!e.is_recoverable;
  form.comment = e.comment ?? "";

  form.end_of_life_date = e.end_of_life_date ?? "";
  form.end_of_life_country_id = e.end_of_life_country_id ?? null;

  form.reuse = !!e.reuse;
  form.recycling = !!e.recycling;
  form.incineration = !!e.incineration;
  form.composting = !!e.composting;
  form.landfill = !!e.landfill;

  form.recycling_country_id = e.recycling_country_id ?? null;
  form.recycling_method = e.recycling_method ?? "";
  form.recycling_valued_product = e.recycling_valued_product ?? "";
  form.recycling_organization = e.recycling_organization ?? "";

  form.incineration_country_id = e.incineration_country_id ?? null;
  form.incineration_method = e.incineration_method ?? "";
  form.incineration_valued_product = e.incineration_valued_product ?? "";
  form.incineration_organization = e.incineration_organization ?? "";

  form.composting_country_id = e.composting_country_id ?? null;
  form.composting_method = e.composting_method ?? "";
  form.composting_valued_product = e.composting_valued_product ?? "";
  form.composting_organization = e.composting_organization ?? "";

  form.landfill_country_id = e.landfill_country_id ?? null;
  form.landfill_method = e.landfill_method ?? "";
  form.landfill_valued_product = e.landfill_valued_product ?? "";
  form.landfill_organization = e.landfill_organization ?? "";
}

async function upsert() {
  if (!productId.value) throw new Error("Missing productId");
  await axios.post(`${API_BASE_URL}/products/${productId.value}/end-of-life`, buildPayload(), {
    headers: authHeaders(),
  });
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
      `${API_BASE_URL}/products/${productId.value}/end-of-life/save-progress`,
      {},
      { headers: authHeaders() }
    );

    emit("update", { status: "draft", volet: 9, backend: res.data });
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
  resetErrs();

  if (!frontValidate()) {
    error.value = "Please fix the highlighted fields.";
    return;
  }

  if (!productId.value) {
    error.value = "Missing productId. Please complete Step 1 first.";
    return;
  }

  busy.value = true;

  try {
    // 1) Save data
    await upsert();

    // 2) Validate step (backend)
    const res = await axios.post(
      `${API_BASE_URL}/products/${productId.value}/end-of-life/validate-step`,
      {},
      { headers: authHeaders() }
    );

    // 3) Update stepper/parent
    emit("update", { status: "completed", volet: 9, backend: res.data });

    // ✅ IMPORTANT: stay inside createpasseport and go to next step
    emit("next");

    // ✅ DO NOT ROUTE AWAY if we are already inside createpasseport
    // fallback only if user opened this step page directly
    if (!route.path.includes("/user/passports/createpasseport")) {
      await router.push({
        path: "/user/passports/createpasseport",
        query: { productId: productId.value },
      });
    }

    showToast(res.data?.message || "Step validated", "success");
  } catch (e) {
    if (e?.response?.status === 422) applyBackend422(e);
    error.value = extractError(e, "Validation failed");
    showToast("Validation failed", "error");
  } finally {
    busy.value = false;
  }
}
function goPrevious() {
  // if inside stepper -> emit previous
  if (route.path.includes("/user/passports/createpasseport")) {
    emit("previous");
    return;
  }

  // fallback standalone
  if (productId.value) router.push(`/user/passports/usage?productId=${productId.value}`);
  else emit("previous");
}


onMounted(async () => {
  loadingInit.value = true;
  error.value = "";
  try {
    await fetchCountries();
    await fetchEol();
  } catch (e) {
    error.value = extractError(e, "Failed to load end-of-life data");
  } finally {
    loadingInit.value = false;
  }
});
</script>

<style scoped>
/* ✅ SAME THEME AS YOUR OTHER STEPS (Manufacturing/Usage) */
.eol-page{ max-width:1020px; margin:0 auto; padding:12px; }

.page-header{ margin-bottom: 14px; }
.page-title{ margin:0; font-size:28px; font-weight:900; color:#0f172a; letter-spacing:-0.02em; }
.page-subtitle{ margin:6px 0 0; color:#64748b; font-weight:700; }

.divider{ height:1px; background:#e2e8f0; margin: 16px 0 18px; }
.divider-soft{ height:1px; background:#eef2f7; margin:16px 0; }

.eol-alert{
  border-radius:12px;
  padding:14px 16px;
  font-weight:900;
  border:1.5px solid #e2e8f0;
  background:#f8fafc;
  color:#0f172a;
  margin-bottom: 14px;
}
.eol-alert.error{ background:#fff1f2; border-color:#fecaca; color:#991b1b; }
.eol-alert.info{ background:#eff6ff; border-color:#bfdbfe; color:#1e40af; }

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

.grid-2{ display:grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.grid-1{ display:grid; grid-template-columns: 1fr; gap: 14px; }
@media (max-width: 820px){ .grid-2{ grid-template-columns:1fr; } }

.field{ display:flex; flex-direction:column; }
.label{ font-size:13px; font-weight:900; color:#0f172a; margin-bottom:8px; }
.req{ color:#ef4444; }

.input{
  width:100%;
  border:1.5px solid #e2e8f0;
  border-radius:10px;
  padding:11px 14px;
  font-size:14px;
  font-weight:700;
  outline:none;
  transition:.2s;
  background:#fff;
}
.input:focus{ border-color:#0ea5e9; box-shadow:0 0 0 3px rgba(14,165,233,.12); }
.input.error{ border-color:#ef4444; }
.textarea{ min-height:90px; resize:vertical; }

.select-wrap{ position:relative; }
.select{ appearance:none; padding-right:40px; }
.select-icon{ position:absolute; right:12px; top:50%; transform:translateY(-50%); color:#64748b; pointer-events:none; font-weight:900; }

.err{ margin:6px 0 0; color:#ef4444; font-weight:800; font-size:12px; }
.hint{ margin:8px 0 0; color:#64748b; font-size:12px; font-weight:700; }

.toggle-card{
  border:1.5px solid #e2e8f0;
  border-radius:12px;
  padding:14px;
  display:flex; align-items:center; justify-content:space-between;
  background:#fff;
}
.toggle-left{ display:flex; gap:12px; align-items:flex-start; }
.spark{ font-size:18px; }
.toggle-title{ font-weight:900; color:#0f172a; }
.toggle-sub{ color:#64748b; font-weight:700; font-size:12px; margin-top:2px; }
.mt-12{ margin-top:12px; }

/* Methods */
.methods{
  display:grid;
  grid-template-columns: 1fr 1fr;
  gap:14px;
  margin-bottom: 16px;
}
@media (max-width: 900px){ .methods{ grid-template-columns:1fr; } }

.method-card{
  background:#fff;
  border:1.5px solid #e2e8f0;
  border-radius:14px;
  padding:16px;
  display:flex;
  justify-content:space-between;
  align-items:center;
}
.method-card.full{ grid-column: 1 / -1; }
.method-left{ display:flex; align-items:center; gap:12px; }
.method-title{ font-weight:900; color:#0f172a; }
.method-sub{ font-size:12px; color:#64748b; font-weight:700; }

.badge{
  width:38px; height:38px; border-radius:999px;
  display:flex; align-items:center; justify-content:center;
  font-weight:900;
}
.badge.green{ background:#dcfce7; color:#166534; }
.badge.blue{ background:#dbeafe; color:#1d4ed8; }
.badge.orange{ background:#ffedd5; color:#9a3412; }
.badge.gray{ background:#f1f5f9; color:#334155; }

/* Summary */
.summary{
  border:1.5px solid #a7f3d0;
  background:#ecfdf5;
  border-radius:14px;
  padding:18px;
  margin: 10px 0 18px;
}
.summary-title{ font-weight:900; color:#0f172a; margin-bottom:6px; }
.summary-text{ color:#065f46; font-weight:900; }

/* Actions */
.actions{
  display:flex; justify-content:space-between; align-items:center;
  gap:12px; margin-top: 10px;
}
.right-actions{ display:flex; gap:12px; align-items:center; }

.btn-secondary,.btn-light,.btn-primary{
  display:inline-flex; align-items:center; gap:10px;
  padding:12px 18px; border-radius:10px;
  font-weight:900; cursor:pointer; transition:.2s;
  border:1.5px solid #e2e8f0; background:#fff;
}
.btn-primary{ background:#0f766e; border-color:#0f766e; color:#fff; }
.btn-primary:hover{ background:#0b5f58; }
.btn-light:hover,.btn-secondary:hover{ background:#f8fafc; }
.btn-secondary:disabled,.btn-light:disabled,.btn-primary:disabled{ opacity:.5; cursor:not-allowed; }

@media (max-width: 900px){
  .actions{ flex-direction:column; align-items:stretch; }
  .right-actions{ flex-direction:column; align-items:stretch; }
  .btn-secondary,.btn-light,.btn-primary{ width:100%; justify-content:center; }
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

/* Switch */
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
