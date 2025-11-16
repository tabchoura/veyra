<template>
  <div class="register-wrapper">
    <!-- Header -->
    <header class="topbar" @click="isLangMenuOpen = false">
      <div class="logo">Veyra<span>®</span></div>

      <div class="lang-login">
        <!-- Sélecteur de langue style login -->
        <div class="language-dropdown" @click.stop>
          <button
            type="button"
            class="language-toggle"
            @click="isLangMenuOpen = !isLangMenuOpen"
          >
            <span class="globe-icon">🌐</span>
            <span class="lang-code">{{ lang.toUpperCase() }}</span>
            <span class="chevron">▾</span>
          </button>

          <transition name="fade-scale">
            <div v-if="isLangMenuOpen" class="language-menu">
              <button
                v-for="opt in languageOptions"
                :key="opt.code"
                type="button"
                class="language-option"
                @click="selectLanguage(opt.code)"
              >
                <span class="globe-icon">🌐</span>
                <span>{{ opt.label }}</span>
              </button>
            </div>
          </transition>
        </div>

        <!-- Lien connexion -->
        <router-link to="/login" class="login-link">
          {{ t("login") }}
        </router-link>
      </div>
    </header>

    <!-- Container -->
    <div class="register-container">
      <!-- Stepper -->
      <div class="stepper">
        <div v-for="(step, index) in t('steps')" :key="index" class="step">
          <div class="circle" :class="{ active: index <= currentStep }"></div>
          <p :class="{ activeText: index <= currentStep }">{{ step }}</p>
          <div
            v-if="index < t('steps').length - 1"
            class="line"
            :class="{ filled: index < currentStep }"
          ></div>
        </div>
      </div>

      <!-- Form -->
      <div class="form-card">
        <h1 class="form-title">{{ t("createAccount") }}</h1>
        <p class="form-subtitle">
          {{ t("subtitle") }}
        </p>

        <form @submit.prevent="nextStep">
          <!-- INPUT FICHIER GLOBAL (accessible partout) -->
          <input
            ref="fileInput"
            type="file"
            accept="image/*"
            @change="handleLogoUpload($event)"
            style="display: none"
          />

          <!-- ÉTAPE 1 : Détails société -->
          <div v-if="currentStep === 0">
            <h3 class="section-title">{{ t("company") }}</h3>

            <!-- Upload Logo -->
            <div class="form-group">
              <label>{{ t("companyLogo") }}</label>
              <div class="logo-upload-container">
                <div class="logo-preview" @click="triggerFileInput">
                  <div v-if="!form.logoPreview" class="logo-placeholder">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      width="40"
                      height="40"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2"
                    >
                      <rect
                        x="3"
                        y="3"
                        width="18"
                        height="18"
                        rx="2"
                        ry="2"
                      ></rect>
                      <circle cx="8.5" cy="8.5" r="1.5"></circle>
                      <polyline points="21 15 16 10 5 21"></polyline>
                    </svg>
                    <p>{{ t("uploadLogo") }}</p>
                  </div>
                  <img
                    v-else
                    :src="form.logoPreview"
                    alt="Logo preview"
                    class="logo-image"
                  />
                </div>
                <button
                  v-if="form.logoPreview"
                  type="button"
                  class="remove-logo-btn"
                  @click="removeLogo"
                >
                  {{ t("removeLogo") }}
                </button>
              </div>
            </div>

            <div class="form-group">
              <label>{{ t("companyName") }} *</label>
              <input
                v-model="form.nomSociete"
                required
                :class="{ 'input-error': errors.nomSociete }"
                @input="errors.nomSociete = ''"
              />
              <transition name="error-slide">
                <p v-if="errors.nomSociete" class="error-message">
                  <span class="error-dot"></span>
                  {{ errors.nomSociete }}
                </p>
              </transition>
            </div>

            <div class="form-group">
              <label>{{ t("tvaNumber") }} *</label>
              <input
                v-model="form.tvaNumber"
                required
                :class="{ 'input-error': errors.tvaNumber }"
                @input="errors.tvaNumber = ''"
              />
              <transition name="error-slide">
                <p v-if="errors.tvaNumber" class="error-message">
                  <span class="error-dot"></span>
                  {{ errors.tvaNumber }}
                </p>
              </transition>
            </div>

            <div class="form-group">
              <label>{{ t("address") }} *</label>
              <input
                v-model="form.adresse1"
                required
                :class="{ 'input-error': errors.adresse1 }"
                @input="errors.adresse1 = ''"
              />
              <transition name="error-slide">
                <p v-if="errors.adresse1" class="error-message">
                  <span class="error-dot"></span>
                  {{ errors.adresse1 }}
                </p>
              </transition>
            </div>

            <div class="form-group">
              <label>{{ t("address2") }}</label>
              <input
                v-model="form.adresse2"
                :class="{ 'input-error': errors.adresse2 }"
                @input="errors.adresse2 = ''"
              />
            </div>

            <div class="grid-2">
              <div class="form-group">
                <label>{{ t("postalCode") }} *</label>
                <input
                  v-model="form.codePostal"
                  required
                  :class="{ 'input-error': errors.codePostal }"
                  @input="errors.codePostal = ''"
                />
                <transition name="error-slide">
                  <p v-if="errors.codePostal" class="error-message">
                    <span class="error-dot"></span>
                    {{ errors.codePostal }}
                  </p>
                </transition>
              </div>
              <div class="form-group">
                <label>{{ t("country") }} *</label>
                <select
                  v-model="form.pays"
                  required
                  :class="{ 'input-error': errors.pays }"
                  @change="errors.pays = ''"
                >
                  <option disabled value="">{{ t("selectCountry") }}</option>
                  <option v-for="country in countries" :key="country">
                    {{ country }}
                  </option>
                </select>
                <transition name="error-slide">
                  <p v-if="errors.pays" class="error-message">
                    <span class="error-dot"></span>
                    {{ errors.pays }}
                  </p>
                </transition>
              </div>
            </div>

            <div class="form-group">
              <label>{{ t("sector") }} *</label>
              <select
                v-model="form.secteur"
                required
                :class="{ 'input-error': errors.secteur }"
                @change="errors.secteur = ''"
              >
                <option disabled value="">{{ t("selectSector") }}</option>
                <option>Textile & mode</option>
                <option>Agroalimentaire</option>
                <option>Électronique & technologies</option>
                <option>Construction & BTP</option>
                <option>Industrie manufacturière</option>
                <option>Énergie (production, distribution, stockage)</option>
                <option>Immobilier & gestion d’actifs</option>
                <option>Chimie, plasturgie & matériaux</option>
                <option>Emballage & logistique</option>
                <option>Automobile & mobilité</option>
                <option>Autres</option>
              </select>
              <transition name="error-slide">
                <p v-if="errors.secteur" class="error-message">
                  <span class="error-dot"></span>
                  {{ errors.secteur }}
                </p>
              </transition>
            </div>

            <div v-if="form.secteur === 'Autres'" class="form-group">
              <label>{{ t("specifySector") }} *</label>
              <input
                v-model="form.autresSecteur"
                required
                :class="{ 'input-error': errors.autresSecteur }"
                @input="errors.autresSecteur = ''"
              />
              <transition name="error-slide">
                <p v-if="errors.autresSecteur" class="error-message">
                  <span class="error-dot"></span>
                  {{ errors.autresSecteur }}
                </p>
              </transition>
            </div>

            <div class="form-group">
              <label>{{ t("partner") }}</label>
              <select
                v-model="form.partner"
                :class="{ 'input-error': errors.partner }"
                @change="errors.partner = ''"
              >
                <option disabled value="">{{ t("selectPartner") }}</option>
                <option>bAwear Score</option>
                <option>FTTH</option>
                <option>MODINT</option>
                <option>CBI</option>
                <option>Autre</option>
              </select>
              <transition name="error-slide">
                <p v-if="errors.partner" class="error-message">
                  <span class="error-dot"></span>
                  {{ errors.partner }}
                </p>
              </transition>
            </div>

            <div v-if="form.partner === 'Autre'" class="form-group">
              <label>{{ t("specifyPartner") }} *</label>
              <input
                v-model="form.partnerOther"
                :class="{ 'input-error': errors.partnerOther }"
                @input="errors.partnerOther = ''"
              />
              <transition name="error-slide">
                <p v-if="errors.partnerOther" class="error-message">
                  <span class="error-dot"></span>
                  {{ errors.partnerOther }}
                </p>
              </transition>
            </div>
          </div>

          <!-- ÉTAPE 2 : Détails compte -->
          <div v-if="currentStep === 1">
            <h3 class="section-title">{{ t("accountDetails") }}</h3>

            <div class="form-group">
              <label>{{ t("email") }} *</label>
              <input
                type="email"
                v-model="form.email"
                required
                :class="{ 'input-error': errors.email }"
                @input="errors.email = ''"
              />
              <transition name="error-slide">
                <p v-if="errors.email" class="error-message">
                  <span class="error-dot"></span>
                  {{ errors.email }}
                </p>
              </transition>
            </div>

            <div class="grid-2">
              <div class="form-group">
                <label>{{ t("password") }} *</label>
                <div class="password-wrapper">
                  <input
                    :type="showPassword ? 'text' : 'password'"
                    v-model="form.password"
                    required
                    minlength="8"
                    :class="{ 'input-error': errors.password }"
                    @input="errors.password = ''"
                  />
                  <button
                    type="button"
                    class="eye-btn"
                    @click="showPassword = !showPassword"
                  >
                    {{ showPassword ? "🙈" : "👁️" }}
                  </button>
                </div>
                <transition name="error-slide">
                  <p v-if="errors.password" class="error-message">
                    <span class="error-dot"></span>
                    {{ errors.password }}
                  </p>
                </transition>
              </div>

              <div class="form-group">
                <label>{{ t("confirmPassword") }} *</label>
                <div class="password-wrapper">
                  <input
                    :type="showPasswordConfirm ? 'text' : 'password'"
                    v-model="form.passwordConfirm"
                    required
                    minlength="8"
                    :class="{ 'input-error': errors.passwordConfirm }"
                    @input="errors.passwordConfirm = ''"
                  />
                  <button
                    type="button"
                    class="eye-btn"
                    @click="showPasswordConfirm = !showPasswordConfirm"
                  >
                    {{ showPasswordConfirm ? "🙈" : "👁️" }}
                  </button>
                </div>
                <transition name="error-slide">
                  <p v-if="errors.passwordConfirm" class="error-message">
                    <span class="error-dot"></span>
                    {{ errors.passwordConfirm }}
                  </p>
                </transition>
              </div>
            </div>

            <div class="grid-2">
              <div class="form-group">
                <label>{{ t("firstName") }} *</label>
                <input
                  v-model="form.firstName"
                  required
                  :class="{ 'input-error': errors.firstName }"
                  @input="errors.firstName = ''"
                />
                <transition name="error-slide">
                  <p v-if="errors.firstName" class="error-message">
                    <span class="error-dot"></span>
                    {{ errors.firstName }}
                  </p>
                </transition>
              </div>
              <div class="form-group">
                <label>{{ t("lastName") }} *</label>
                <input
                  v-model="form.lastName"
                  required
                  :class="{ 'input-error': errors.lastName }"
                  @input="errors.lastName = ''"
                />
                <transition name="error-slide">
                  <p v-if="errors.lastName" class="error-message">
                    <span class="error-dot"></span>
                    {{ errors.lastName }}
                  </p>
                </transition>
              </div>
            </div>

            <div class="form-group">
              <label>{{ t("position") }}</label>
              <input v-model="form.fonction" />
            </div>
          </div>

          <!-- ÉTAPE 3 : Confirmation -->
          <div v-if="currentStep === 2" class="confirmation">
            <h3 class="section-title">{{ t("confirmation") }}</h3>

            <!-- Logo à l'étape 3 -->
            <div class="confirmation-logo">
              <h4 class="confirmation-logo-title">{{ t("logoSummary") }}</h4>
              <div
                class="logo-preview logo-preview-small"
                @click="triggerFileInput"
              >
                <div v-if="!form.logoPreview" class="logo-placeholder">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="32"
                    height="32"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                  >
                    <rect
                      x="3"
                      y="3"
                      width="18"
                      height="18"
                      rx="2"
                      ry="2"
                    ></rect>
                    <circle cx="8.5" cy="8.5" r="1.5"></circle>
                    <polyline points="21 15 16 10 5 21"></polyline>
                  </svg>
                  <p>{{ t("addLogo") }}</p>
                </div>
                <img
                  v-else
                  :src="form.logoPreview"
                  alt="Logo preview"
                  class="logo-image"
                />
              </div>
              <button
                v-if="form.logoPreview"
                type="button"
                class="remove-logo-btn"
                @click="removeLogo"
              >
                {{ t("changeLogo") }}
              </button>
            </div>

            <p class="confirmation-text">
              {{ t("thankYou") }}, {{ form.firstName }} {{ form.lastName }} !
              {{ t("verifyInfo") }}
            </p>
            <ul>
              <li>
                <strong>{{ t("company2") }} :</strong> {{ form.nomSociete }}
              </li>
              <li>
                <strong>{{ t("email") }} :</strong> {{ form.email }}
              </li>
              <li>
                <strong>{{ t("country") }} :</strong> {{ form.pays }}
              </li>
            </ul>
          </div>

          <!-- Boutons -->
          <div class="btn-row">
            <button
              v-if="currentStep > 0"
              type="button"
              class="secondary-btn"
              @click="prevStep"
            >
              {{ t("back") }}
            </button>

            <button v-if="currentStep < 2" type="submit" class="primary-btn">
              {{ t("next") }}
            </button>

            <button
              v-if="currentStep === 2"
              type="button"
              class="primary-btn"
              @click="submitForm"
              :disabled="loading"
            >
              <span v-if="loading">...</span>
              <span v-else>{{ t("createBtn") }}</span>
            </button>
          </div>

          <p
            v-if="serverError"
            style="color: #dc2626; margin-top: 10px; font-size: 0.9rem"
          >
            {{ serverError }}
          </p>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";

const router = useRouter();

const currentStep = ref(0);

// langue : même logique que la page login
const lang = ref("fr");
const isLangMenuOpen = ref(false);
const languageOptions = [
  { code: "fr", label: "FR" },
  { code: "en", label: "EN" },
];

const fileInput = ref(null);
const loading = ref(false);
const serverError = ref("");

const showPassword = ref(false);
const showPasswordConfirm = ref(false);

const form = reactive({
  logo: null,
  logoPreview: "",
  nomSociete: "",
  tvaNumber: "",
  adresse1: "",
  adresse2: "",
  codePostal: "",
  pays: "",
  secteur: "",
  autresSecteur: "",
  partner: "",
  partnerOther: "",
  email: "",
  password: "",
  passwordConfirm: "",
  firstName: "",
  lastName: "",
  fonction: "",
});

const errors = reactive({
  nomSociete: "",
  tvaNumber: "",
  adresse1: "",
  adresse2: "",
  codePostal: "",
  pays: "",
  secteur: "",
  autresSecteur: "",
  partner: "",
  partnerOther: "",
  email: "",
  password: "",
  passwordConfirm: "",
  firstName: "",
  lastName: "",
});

const translations = {
  fr: {
    login: "Se connecter",
    createAccount: "Créer un compte",
    subtitle:
      "Veuillez renseigner vos informations pour commencer à utiliser Veyra.",
    steps: ["Société", "Compte", "Confirmation"],
    company: "Détails de l'entreprise",
    companyLogo: "Logo de l'entreprise",
    uploadLogo: "Cliquer pour ajouter un logo",
    removeLogo: "Supprimer le logo",
    changeLogo: "Changer le logo",
    addLogo: "Ajouter un logo (optionnel)",
    logoSummary: "Logo de votre entreprise",
    companyName: "Nom de la société",
    tvaNumber: "Numéro de TVA",
    address: "Adresse",
    address2: "Adresse (ligne 2)",
    postalCode: "Code postal",
    country: "Pays",
    sector: "Secteur d'activité",
    selectCountry: "Sélectionnez un pays",
    selectSector: "Sélectionnez un secteur",
    specifySector: "Précisez votre secteur",
    partner: "Partenaire",
    selectPartner: "Sélectionnez un partenaire",
    specifyPartner: "Précisez votre partenaire",
    accountDetails: "Détails du compte",
    email: "Adresse e-mail",
    password: "Mot de passe",
    confirmPassword: "Confirmer le mot de passe",
    firstName: "Prénom",
    lastName: "Nom",
    fullName: "Nom & prénom",
    position: "Fonction",
    confirmation: "Confirmation",
    thankYou: "Merci",
    verifyInfo:
      "Veuillez vérifier vos informations avant validation finale.",
    company2: "Entreprise",
    back: "Retour",
    next: "Suivant",
    createBtn: "Créer le compte",
    success: "Compte créé avec succès !",
    passwordMatch: "Les mots de passe ne correspondent pas",
    passwordLength: "Le mot de passe doit contenir au moins 8 caractères",
    required: "Ce champ est obligatoire",
    emailInvalid: "Email invalide",
  },
  en: {
    login: "Login",
    createAccount: "Create Account",
    subtitle: "Please provide your information to start using Veyra.",
    steps: ["Company", "Account", "Confirmation"],
    company: "Company Details",
    companyLogo: "Company Logo",
    uploadLogo: "Click to add a logo",
    removeLogo: "Remove logo",
    changeLogo: "Change logo",
    addLogo: "Add a logo (optional)",
    logoSummary: "Your company logo",
    companyName: "Company Name",
    tvaNumber: "VAT Number",
    address: "Address",
    address2: "Address (line 2)",
    postalCode: "Postal Code",
    country: "Country",
    sector: "Industry Sector",
    selectCountry: "Select a country",
    selectSector: "Select a sector",
    specifySector: "Specify your sector",
    partner: "Partner",
    selectPartner: "Select a partner",
    specifyPartner: "Specify your partner",
    accountDetails: "Account Details",
    email: "Email Address",
    password: "Password",
    confirmPassword: "Confirm Password",
    firstName: "First Name",
    lastName: "Last Name",
    fullName: "Full Name",
    position: "Position",
    confirmation: "Confirmation",
    thankYou: "Thank you",
    verifyInfo: "Please verify your information before final validation.",
    company2: "Company",
    back: "Back",
    next: "Next",
    createBtn: "Create Account",
    success: "Account created successfully!",
    passwordMatch: "Passwords do not match",
    passwordLength: "Password must be at least 8 characters long",
    required: "This field is required",
    emailInvalid: "Invalid email",
  },
};

const t = (key) => translations[lang.value][key];

// changement langue
const selectLanguage = (code) => {
  lang.value = code;
  isLangMenuOpen.value = false;
};

const countries = [
  "Afghanistan",
  "Albania",
  "Algeria",
  "Andorra",
  "Angola",
  "Antigua and Barbuda",
  "Argentina",
  "Armenia",
  "Australia",
  "Austria",
  "Azerbaijan",
  "Bahamas",
  "Bahrain",
  "Bangladesh",
  "Barbados",
  "Belarus",
  "Belgium",
  "Belize",
  "Benin",
  "Bhutan",
  "Bolivia",
  "Bosnia and Herzegovina",
  "Botswana",
  "Brazil",
  "Brunei",
  "Bulgaria",
  "Burkina Faso",
  "Burundi",
  "Cabo Verde",
  "Cambodia",
  "Cameroon",
  "Canada",
  "Central African Republic",
  "Chad",
  "Chile",
  "China",
  "Colombia",
  "Comoros",
  "Congo",
  "Costa Rica",
  "Croatia",
  "Cuba",
  "Cyprus",
  "Czech Republic",
  "Denmark",
  "Djibouti",
  "Dominica",
  "Dominican Republic",
  "Ecuador",
  "Egypt",
  "El Salvador",
  "Equatorial Guinea",
  "Eritrea",
  "Estonia",
  "Eswatini",
  "Ethiopia",
  "Fiji",
  "Finland",
  "France",
  "Gabon",
  "Gambia",
  "Georgia",
  "Germany",
  "Ghana",
  "Greece",
  "Grenada",
  "Guatemala",
  "Guinea",
  "Guinea-Bissau",
  "Guyana",
  "Haiti",
  "Honduras",
  "Hungary",
  "Iceland",
  "India",
  "Indonesia",
  "Iran",
  "Iraq",
  "Ireland",
  "Israel",
  "Italy",
  "Jamaica",
  "Japan",
  "Jordan",
  "Kazakhstan",
  "Kenya",
  "Kiribati",
  "Kosovo",
  "Kuwait",
  "Kyrgyzstan",
  "Laos",
  "Latvia",
  "Lebanon",
  "Lesotho",
  "Liberia",
  "Libya",
  "Liechtenstein",
  "Lithuania",
  "Luxembourg",
  "Madagascar",
  "Malawi",
  "Malaysia",
  "Maldives",
  "Mali",
  "Malta",
  "Marshall Islands",
  "Mauritania",
  "Mauritius",
  "Mexico",
  "Micronesia",
  "Moldova",
  "Monaco",
  "Mongolia",
  "Montenegro",
  "Morocco",
  "Mozambique",
  "Myanmar",
  "Namibia",
  "Nauru",
  "Nepal",
  "Netherlands",
  "New Zealand",
  "Nicaragua",
  "Niger",
  "Nigeria",
  "North Korea",
  "North Macedonia",
  "Norway",
  "Oman",
  "Pakistan",
  "Palau",
  "Palestine",
  "Panama",
  "Papua New Guinea",
  "Paraguay",
  "Peru",
  "Philippines",
  "Poland",
  "Portugal",
  "Qatar",
  "Romania",
  "Russia",
  "Rwanda",
  "Saint Kitts and Nevis",
  "Saint Lucia",
  "Saint Vincent and the Grenadines",
  "Samoa",
  "San Marino",
  "Sao Tome and Principe",
  "Saudi Arabia",
  "Senegal",
  "Serbia",
  "Seychelles",
  "Sierra Leone",
  "Singapore",
  "Slovakia",
  "Slovenia",
  "Solomon Islands",
  "Somalia",
  "South Africa",
  "South Korea",
  "South Sudan",
  "Spain",
  "Sri Lanka",
  "Sudan",
  "Suriname",
  "Sweden",
  "Switzerland",
  "Syria",
  "Taiwan",
  "Tajikistan",
  "Tanzania",
  "Thailand",
  "Timor-Leste",
  "Togo",
  "Tonga",
  "Trinidad and Tobago",
  "Tunisia",
  "Turkey",
  "Turkmenistan",
  "Tuvalu",
  "Uganda",
  "Ukraine",
  "United Arab Emirates",
  "United Kingdom",
  "United States",
  "Uruguay",
  "Uzbekistan",
  "Vanuatu",
  "Vatican City",
  "Venezuela",
  "Vietnam",
  "Yemen",
  "Zambia",
  "Zimbabwe",
];

const validateEmail = (email) => {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
};

const triggerFileInput = () => {
  if (fileInput.value) {
    fileInput.value.click();
  }
};

const handleLogoUpload = (event) => {
  const file = event.target.files && event.target.files[0];
  if (!file) return;

  form.logo = file;

  const reader = new FileReader();
  reader.onload = (e) => {
    form.logoPreview = e.target.result;
  };
  reader.readAsDataURL(file);
};

const removeLogo = () => {
  form.logo = null;
  form.logoPreview = "";
  if (fileInput.value) {
    fileInput.value.value = "";
  }
};

const nextStep = () => {
  Object.keys(errors).forEach((key) => (errors[key] = ""));

  if (currentStep.value === 0) {
    let hasError = false;

    if (!form.nomSociete) {
      errors.nomSociete = t("required");
      hasError = true;
    }
    if (!form.tvaNumber) {
      errors.tvaNumber = t("required");
      hasError = true;
    }
    if (!form.adresse1) {
      errors.adresse1 = t("required");
      hasError = true;
    }
    if (!form.codePostal) {
      errors.codePostal = t("required");
      hasError = true;
    }
    if (!form.pays) {
      errors.pays = t("required");
      hasError = true;
    }
    if (!form.secteur) {
      errors.secteur = t("required");
      hasError = true;
    }
    if (form.secteur === "Autres" && !form.autresSecteur) {
      errors.autresSecteur = t("required");
      hasError = true;
    }
    if (form.partner === "Autre" && !form.partnerOther) {
      errors.partnerOther = t("required");
      hasError = true;
    }

    if (hasError) return;
  }

  if (currentStep.value === 1) {
    let hasError = false;

    if (!form.email) {
      errors.email = t("required");
      hasError = true;
    } else if (!validateEmail(form.email)) {
      errors.email = t("emailInvalid");
      hasError = true;
    }

    if (!form.password) {
      errors.password = t("required");
      hasError = true;
    } else if (form.password.length < 8) {
      errors.password = t("passwordLength");
      hasError = true;
    }

    if (!form.passwordConfirm) {
      errors.passwordConfirm = t("required");
      hasError = true;
    } else if (form.password !== form.passwordConfirm) {
      errors.passwordConfirm = t("passwordMatch");
      hasError = true;
    }

    if (!form.firstName) {
      errors.firstName = t("required");
      hasError = true;
    }
    if (!form.lastName) {
      errors.lastName = t("required");
      hasError = true;
    }

    if (hasError) return;
  }

  if (currentStep.value < 2) currentStep.value++;
};

const prevStep = () => {
  if (currentStep.value > 0) currentStep.value--;
};

const submitForm = async () => {
  loading.value = true;
  serverError.value = "";

  try {
    // 1. Récupérer le CSRF cookie
    await axios.get("http://127.0.0.1:8000/sanctum/csrf-cookie", {
      withCredentials: true,
    });
    const formData = new FormData();

    // Société
    formData.append("company_name", form.nomSociete);
    // ⚠️ Assure-toi que le backend utilise bien 'vat_number'
    formData.append("tva_number", form.tvaNumber);
    formData.append("address1", form.adresse1);
    formData.append("address2", form.adresse2 || "");
    formData.append("postal_code", form.codePostal);
    formData.append("country", form.pays);
    formData.append("sector", form.secteur);
    if (form.secteur === "Autres") {
      formData.append("sector_other", form.autresSecteur);
    }

    // Partenaires
    formData.append("partner", form.partner || "");
    if (form.partner === "Autre") {
      formData.append("partner_other", form.partnerOther || "");
    }

    // Auth + contact
    formData.append("email", form.email);
    formData.append("password", form.password);
    formData.append("password_confirmation", form.passwordConfirm);
    formData.append("first_name", form.firstName);
    formData.append("last_name", form.lastName);
    formData.append("function", form.fonction || "");

    // Préférences
    formData.append("language", lang.value);

    // Logo
    if (form.logo) {
      formData.append("logo", form.logo);
    }

    await axios.post("http://127.0.0.1:8000/api/auth/register", formData, {
      withCredentials: true,
      headers: { "Content-Type": "multipart/form-data" },
    });

    alert(t("success"));
    router.push("/login");
  } catch (error) {
    console.error(error);
    serverError.value =
      error.response?.data?.message ||
      (lang.value === "fr"
        ? "Une erreur est survenue lors de la création du compte."
        : "An error occurred while creating the account.");
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.register-wrapper {
  background: #f8f9fb;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  font-family: "Inter", sans-serif;
}

.topbar {
  display: flex;
  justify-content: space-between;
  padding: 20px 50px;
  background: white;
  border-bottom: 1px solid #e5e5e5;
  align-items: center;
}

.logo {
  font-weight: 700;
  font-size: 20px;
  color: #001d48;
}
.logo span {
  font-size: 12px;
  vertical-align: super;
}

.lang-login {
  display: flex;
  align-items: center;
  gap: 16px;
}

/* Dropdown langue */
.language-dropdown {
  position: relative;
  margin-bottom: 0px;
}

.language-toggle {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  padding: 0.45rem 0.9rem;
  border-radius: 999px;
  border: 1px solid #e5e7eb;
  background: #ffffff;
  cursor: pointer;
  font-size: 0.9rem;
  color: #111827;
  transition: all 0.2s ease;
  box-shadow: 0 0 0 0 transparent;
}

.language-toggle:hover {
  border-color: #2563eb;
  box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
}

.globe-icon {
  font-size: 1rem;
}

.lang-code {
  font-weight: 500;
}

.chevron {
  font-size: 0.75rem;
  margin-left: 0.15rem;
}

.language-menu {
  position: absolute;
  right: 0;
  top: 110%;
  background: #ffffff;
  border-radius: 12px;
  box-shadow: 0 12px 30px rgba(15, 23, 42, 0.15);
  padding: 0.5rem 0;
  min-width: 140px;
  z-index: 20;
}

.language-option {
  width: 100%;
  border: none;
  background: transparent;
  display: flex;
  align-items: center;
  gap: 0.6rem;
  padding: 0.4rem 0.9rem;
  font-size: 0.9rem;
  cursor: pointer;
  color: #111827;
}

.language-option:hover {
  background-color: #f3f4f6;
}

/* Transition du menu langue */
.fade-scale-enter-active,
.fade-scale-leave-active {
  transition: all 0.15s ease-out;
  transform-origin: top right;
}
.fade-scale-enter-from,
.fade-scale-leave-to {
  opacity: 0;
  transform: scale(0.95) translateY(-4px);
}

/* Bouton login */
.login-link {
  padding: 8px 16px;
  background-color: #3b82f6;
  text-decoration: none;
  color: white;
  border: none;
  border-radius: 999px;
  font-size: 0.9rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}
.login-link:hover {
  background-color: #2563eb;
}

.register-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-top: 40px;
}

.stepper {
  display: flex;
  align-items: center;
  background: white;
  padding: 30px 40px;
  border-radius: 10px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
  margin-bottom: 40px;
}

.step {
  display: flex;
  align-items: center;
  position: relative;
}

.circle {
  width: 14px;
  height: 14px;
  border-radius: 50%;
  border: 2px solid #ccc;
  background: white;
  margin-right: 10px;
  transition: all 0.3s;
}
.circle.active {
  border-color: #0a58ff;
  background: #0a58ff;
}

.line {
  height: 2px;
  width: 50px;
  background: #ccc;
  margin: 0 10px;
}
.line.filled {
  background: #0a58ff;
}

.step p {
  font-size: 14px;
  color: #999;
}
.activeText {
  color: #0a58ff !important;
  font-weight: 600;
}

.form-card {
  background: white;
  padding: 40px;
  width: 90%;
  max-width: 650px;
  border-radius: 10px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.form-title {
  font-size: 28px;
  font-weight: 700;
  color: #001d48;
}

.form-subtitle {
  color: #666;
  margin-bottom: 30px;
}

.section-title {
  font-size: 18px;
  color: #001d48;
  margin-bottom: 15px;
}

.form-group {
  display: flex;
  flex-direction: column;
  margin-bottom: 20px;
}

label {
  color: #444;
  font-weight: 500;
  margin-bottom: 5px;
  font-size: 14px;
}

input,
select {
  padding: 10px;
  border: 1px solid #d5d5d5;
  border-radius: 6px;
  font-size: 14px;
  transition: all 0.2s;
  background-color: #f9fafb;
}
input:focus,
select:focus {
  border-color: #0a58ff;
  outline: none;
  background-color: white;
}

.input-error {
  border-color: #ef4444 !important;
  background-color: #fef2f2 !important;
}

.input-error:focus {
  border-color: #dc2626 !important;
  box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15) !important;
}

.error-message {
  margin-top: 0.4rem;
  color: #dc2626;
  font-size: 0.85rem;
  display: flex;
  align-items: center;
  gap: 0.35rem;
  font-weight: 500;
}

.error-dot {
  width: 6px;
  height: 6px;
  background-color: #dc2626;
  border-radius: 50%;
}

.error-slide-enter-active,
.error-slide-leave-active {
  transition: all 0.25s ease;
}

.error-slide-enter-from {
  opacity: 0;
  transform: translateY(-6px);
}

.error-slide-leave-to {
  opacity: 0;
  transform: translateY(-6px);
}

.grid-2 {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 20px;
}

.confirmation ul {
  list-style: none;
  padding: 0;
  margin-top: 20px;
}

.confirmation li {
  padding: 10px 0;
  border-bottom: 1px solid #f0f0f0;
}

.confirmation-text {
  margin-top: 10px;
}

.confirmation-logo {
  margin-bottom: 20px;
}

.confirmation-logo-title {
  font-size: 14px;
  font-weight: 600;
  color: #374151;
  margin-bottom: 8px;
}

.btn-row {
  display: flex;
  justify-content: flex-end;
  gap: 15px;
  margin-top: 20px;
}

.primary-btn {
  background: #0a58ff;
  color: white;
  border: none;
  border-radius: 8px;
  padding: 10px 22px;
  cursor: pointer;
  font-weight: 600;
}
.secondary-btn {
  background: transparent;
  border: 1px solid #0a58ff;
  color: #0a58ff;
  border-radius: 8px;
  padding: 10px 22px;
  cursor: pointer;
  font-weight: 600;
}
.primary-btn:hover {
  background: #0847cc;
}

/* Logo Upload Styles */
.logo-upload-container {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.logo-preview {
  width: 150px;
  height: 150px;
  border: 2px dashed #d5d5d5;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s;
  background-color: #f9fafb;
}

.logo-preview-small {
  width: 110px;
  height: 110px;
}

.logo-preview:hover {
  border-color: #0a58ff;
  background-color: #f0f7ff;
}

.logo-placeholder {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 10px;
  color: #9ca3af;
  text-align: center;
  padding: 20px;
}

.logo-placeholder svg {
  color: #d1d5db;
}

.logo-placeholder p {
  font-size: 13px;
  font-weight: 500;
}

.logo-image {
  width: 100%;
  height: 100%;
  object-fit: contain;
  border-radius: 6px;
}

.remove-logo-btn {
  align-self: flex-start;
  padding: 6px 14px;
  background: #ef4444;
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 13px;
  cursor: pointer;
  font-weight: 500;
  transition: background 0.2s;
  margin-top: 8px;
}

.remove-logo-btn:hover {
  background: #dc2626;
}

/* Password eye */
.password-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.password-wrapper input {
  width: 100%;
  padding-right: 36px;
}

.eye-btn {
  position: absolute;
  right: 8px;
  border: none;
  background: transparent;
  cursor: pointer;
  font-size: 16px;
  padding: 0;
}
</style>
