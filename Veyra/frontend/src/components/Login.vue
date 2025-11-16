<template>
  <div class="page-wrapper">
    <div class="login-card">
      <!-- Panneau gauche bleu -->
      <div class="left-panel">
        <!-- Animation de fond -->
        <div class="background-animation">
          <div class="bubble bubble-1"></div>
          <div class="bubble bubble-2"></div>
        </div>

        <div class="welcome-content">
          <h1 class="welcome-title">
            {{ t.title }}
          </h1>

          <div class="welcome-divider"></div>

          <p class="welcome-text">
            {{ t.description }}
          </p>
        </div>

        <!-- Icône personnage avec 3 cercles -->
        <div class="person-icon">
          <div class="person-head"></div>
          <div class="person-body"></div>
          <div class="person-foot"></div>
        </div>

        <!-- Footer -->
        <div class="panel-footer">
          <p>
            {{ t.question }}
            <a href="#" class="footer-link">{{ t.faq }}</a>
            {{ t.or }}
            <a
              href="mailto:noreplay@greenpulse-consulting.tn"
              class="footer-link"
            >
              {{ t.contact }}
            </a>
          </p>
        </div>
      </div>

      <!-- Panneau droit - Formulaire -->
      <div class="right-panel" @click="isLangMenuOpen = false">
        <div class="form-container">
          <!-- Sélecteur de langue (style menu déroulant) -->
          <div class="language-dropdown" @click.stop>
            <button
              type="button"
              class="language-toggle"
              @click="isLangMenuOpen = !isLangMenuOpen"
            >
              <span class="globe-icon">🌐</span>
              <span class="lang-code">{{ language.toUpperCase() }}</span>
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

          <!-- Logo -->
          <div class="logo-section">
            <h1 class="logo-text">
              Veyra<sup>®</sup><br />
            </h1>
            <p class="year">• 2025</p>
          </div>

          <!-- Formulaire -->
          <form @submit.prevent="login" class="login-form" novalidate>
            <!-- Email -->
            <div class="form-group">
              <label class="form-label">{{ t.email }}</label>
              <div class="input-wrapper">
                <svg
                  class="input-icon"
                  xmlns="http://www.w3.org/2000/svg"
                  width="20"
                  height="20"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                >
                  <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                  <path d="m3 7 9 6 9-6"></path>
                </svg>
                <input
                  type="email"
                  v-model.trim="email"
                  class="form-input"
                  :class="{ 'input-error': emailError }"
                  :placeholder="t.emailPlaceholder"
                  @input="emailError = ''"
                  required
                />
              </div>
              <transition name="error-slide">
                <p v-if="emailError" class="error-message">
                  <span class="error-dot"></span>
                  {{ emailError }}
                </p>
              </transition>
            </div>

            <!-- Mot de passe -->
            <div class="form-group">
              <label class="form-label">{{ t.password }}</label>
              <div class="input-wrapper">
                <svg
                  class="input-icon"
                  xmlns="http://www.w3.org/2000/svg"
                  width="20"
                  height="20"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                >
                  <rect x="5" y="11" width="14" height="10" rx="2"></rect>
                  <path d="M12 17a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"></path>
                  <path d="M8 11V7a4 4 0 0 1 8 0v4"></path>
                </svg>
                <input
                  :type="showPassword ? 'text' : 'password'"
                  v-model.trim="password"
                  class="form-input"
                  :class="{ 'input-error': passwordError }"
                  :placeholder="t.passwordPlaceholder"
                  @input="passwordError = ''"
                  required
                />
                <button
                  type="button"
                  class="toggle-password"
                  @click="showPassword = !showPassword"
                  :title="showPassword ? t.hide : t.show"
                >
                  <svg
                    v-if="!showPassword"
                    xmlns="http://www.w3.org/2000/svg"
                    width="20"
                    height="20"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                  >
                    <path
                      d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"
                    ></path>
                    <circle cx="12" cy="12" r="3"></circle>
                  </svg>
                  <svg
                    v-else
                    xmlns="http://www.w3.org/2000/svg"
                    width="20"
                    height="20"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                  >
                    <path
                      d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"
                    ></path>
                    <line x1="1" y1="1" x2="23" y2="23"></line>
                  </svg>
                </button>
              </div>
              <transition name="error-slide">
                <p v-if="passwordError" class="error-message">
                  <span class="error-dot"></span>
                  {{ passwordError }}
                </p>
              </transition>
            </div>

            <!-- Mot de passe oublié + remember me -->
            <div class="forgot-password">
              <label class="checkbox-container">
                <input type="checkbox" v-model="rememberMe" />
                <span class="checkmark"></span>
                <span class="checkbox-label">Se souvenir de moi</span>
              </label>

              <a href="#" class="link-blue">{{ t.forgotPassword }}</a>
            </div>

            <!-- Bouton connexion -->
            <button
              type="submit"
              class="btn-connect"
              :disabled="isLoading"
            >
              <span v-if="isLoading" class="loading-spinner"></span>
              <span v-else>{{ t.connect }}</span>
            </button>

            <!-- Message succès (optionnel à afficher) -->
            <p v-if="successMessage" class="success-message">
              {{ successMessage }}
            </p>

            <!-- Première connexion -->
            <div class="register-section">
              <span>{{ t.firstTime }} </span>
              <router-link to="/register" class="link-blue">
                {{ t.activate }}
              </router-link>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import axios from 'axios'

const email = ref('')
const password = ref('')
const showPassword = ref(false)
const language = ref('fr')
const isLoading = ref(false)
const emailError = ref('')
const passwordError = ref('')
const isLangMenuOpen = ref(false)
const rememberMe = ref(false)
const successMessage = ref('')

const languageOptions = [
  { code: 'en', label: 'EN' },
  { code: 'fr', label: 'FR' }
]

const translations = {
  fr: {
    title: 'Bienvenue sur  Veyra ®',
    description:
      "Transformez votre démarche de durabilité avec Veyra. La solution intégrée pour la comptabilité carbone, le reporting environnemental et la planification climatique.",
    question: 'Une question ? Lisez la',
    faq: 'FAQ',
    or: 'ou',
    contact: 'contactez-nous',
    email: 'Email',
    emailPlaceholder: 'Renseignez votre adresse email',
    password: 'Mot de passe',
    passwordPlaceholder: 'Renseignez votre mot de passe',
    forgotPassword: 'Mot de passe oublié ?',
    connect: 'Se connecter',
    firstTime: 'Première connexion ?',
    activate: 'Activer mon compte',
    loading: 'Connexion en cours...',
    emailInvalid: 'Email invalide',
    passwordShort: 'Mot de passe trop court (min. 6 caractères)',
    show: 'Afficher',
    hide: 'Masquer',
    success: 'Connexion réussie !'
  },
  en: {
    title: 'Welcome to Veyra ®',
    description:
      'transform your sustainability journey with Veyra. The integrated solution for carbon accounting, environmental reporting, and climate action planning.',
    question: 'Any questions? Read the',
    faq: 'FAQ',
    or: 'or',
    contact: 'contact us',
    email: 'Email',
    emailPlaceholder: 'Enter your email address',
    password: 'Password',
    passwordPlaceholder: 'Enter your password',
    forgotPassword: 'Forgot password?',
    connect: 'Sign in',
    firstTime: 'First time?',
    activate: 'Activate my account',
    loading: 'Connecting...',
    emailInvalid: 'Invalid email',
    passwordShort: 'Password too short (min. 6 characters)',
    show: 'Show',
    hide: 'Hide',
    success: 'Login successful!'
  }
}

const t = computed(() => translations[language.value])

const changeLanguage = (lang) => {
  language.value = lang
}

const selectLanguage = (code) => {
  changeLanguage(code)
  isLangMenuOpen.value = false
}

const validateEmail = (emailValue) => {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return emailRegex.test(emailValue)
}

const login = async () => {
  emailError.value = ''
  passwordError.value = ''
  successMessage.value = ''

  if (!validateEmail(email.value)) {
    emailError.value = t.value.emailInvalid
    return
  }

  if (password.value.length < 6) {
    passwordError.value = t.value.passwordShort
    return
  }

  isLoading.value = true

  try {
    // si tu as mis axios.defaults.baseURL = 'http://127.0.0.1:8000' dans un fichier global
    await axios.get('/sanctum/csrf-cookie', { withCredentials: true })

    const response = await axios.post(
      '/api/auth/login',
      {
        email: email.value,
        password: password.value,
        remember: rememberMe.value
      },
      { withCredentials: true }
    )

    const userData = response.data
    const storage = rememberMe.value ? localStorage : sessionStorage
    storage.setItem('userSession', JSON.stringify(userData))

    successMessage.value = t.value.success
    // Ici ensuite tu pourras faire :
    // if (userData.user.role === 'admin') router.push('/admin/dashboard')
    // else router.push('/dashboardclient')
  } catch (err) {
    if (err.response && err.response.status === 401) {
      alert('Identifiants incorrects ou utilisateur non trouvé')
    } else {
      alert("Une erreur est survenue. Veuillez réessayer.")
    }
  } finally {
    isLoading.value = false
  }
}
</script>

<style scoped>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.page-wrapper {
  min-height: 100vh;
  width: 100vw;
  background: radial-gradient(circle at top left, #e0f2fe 0, #eff6ff 30%, #f9fafb 70%, #e5e7eb 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0;
  padding: 0;
  position: fixed;
  top: 0;
  left: 0;
}

.login-card {
  display: flex;
  width: 100%;
  max-width: 1200px;
  height: auto;
  min-height: 650px;
  border-radius: 24px;
  overflow: hidden;
  background-color: #ffffff;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
}

/* Panneau gauche */
.left-panel {
  flex: 1;
  background: #003b7a;
  color: white;
  padding: 3.5rem;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  position: relative;
  overflow: hidden;
}

/* Animation de fond */
.background-animation {
  position: absolute;
  inset: 0;
  opacity: 0.08;
  pointer-events: none;
}

.bubble {
  position: absolute;
  border-radius: 50%;
  background: white;
  filter: blur(60px);
}

.bubble-1 {
  width: 280px;
  height: 280px;
  top: 80px;
  left: 80px;
  animation: float1 6s ease-in-out infinite;
}

.bubble-2 {
  width: 380px;
  height: 380px;
  bottom: 140px;
  right: 60px;
  background: #4a90e2;
  animation: float2 8s ease-in-out infinite;
}

@keyframes float1 {
  0%,
  100% {
    transform: translate(0, 0);
  }
  50% {
    transform: translate(30px, -30px);
  }
}

@keyframes float2 {
  0%,
  100% {
    transform: translate(0, 0);
  }
  50% {
    transform: translate(-40px, 40px);
  }
}

.welcome-content {
  max-width: 500px;
  position: relative;
  z-index: 2;
}

.welcome-title {
  font-size: 1.8rem;
  font-weight: 400;
  line-height: 1.4;
  margin-bottom: 2rem;
}

.welcome-divider {
  width: 100%;
  height: 1px;
  background-color: rgba(255, 255, 255, 0.5);
  margin-bottom: 1.5rem;
}

.welcome-text {
  font-size: 0.95rem;
  line-height: 1.7;
  font-weight: 300;
  opacity: 0.95;
}

/* Icône personnage */
.person-icon {
  position: absolute;
  bottom: 50px;
  left: 50%;
  transform: translateX(-50%);
  z-index: 2;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0;
}

.person-head {
  width: 90px;
  height: 90px;
  background: white;
  border-radius: 50%;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
  margin-bottom: -15px;
  position: relative;
  z-index: 3;
}

.person-body {
  width: 200px;
  height: 200px;
  background: white;
  border-radius: 50%;
  border: 30px solid #003b7a;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
  margin-bottom: -20px;
  position: relative;
  z-index: 2;
}

.person-foot {
  width: 260px;
  height: 80px;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 999px;
  filter: blur(18px);
  position: relative;
  z-index: 1;
  opacity: 0.9;
  margin-top: 0;
}

.panel-footer {
  text-align: center;
  font-size: 0.9rem;
  position: relative;
  z-index: 2;
}

.footer-link {
  color: white;
  text-decoration: underline;
}

/* Panneau droit */
.right-panel {
  flex: 1;
  background: white;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 3rem;
}

.form-container {
  width: 100%;
  max-width: 450px;
}

/* Sélecteur langue dropdown */
.language-dropdown {
  display: flex;
  justify-content: flex-end;
  margin-bottom: 2rem;
  position: relative;
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

/* Menu déroulant */
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

/* Transition du menu */
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

/* Logo */
.logo-section {
  text-align: center;
  margin-bottom: 2.5rem;
}

.logo-text {
  font-size: 2.2rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  line-height: 1.2;
  color: #111827;
  margin-bottom: 0.3rem;
}

.year {
  font-size: 1.1rem;
  color: #6b7280;
  font-weight: 300;
}

/* Formulaire */
.login-form {
  display: flex;
  flex-direction: column;
  gap: 1.3rem;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-label {
  font-size: 0.95rem;
  font-weight: 600;
  color: #374151;
  margin-bottom: 0.5rem;
}

.input-wrapper {
  position: relative;
}

.input-icon {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: #9ca3af;
}

.form-input {
  width: 100%;
  padding: 0.9rem 1rem 0.9rem 2.6rem;
  border: 1.5px solid #d1d5db;
  border-radius: 8px;
  font-size: 0.95rem;
  outline: none;
  transition: all 0.2s;
  background-color: #f9fafb;
}

.form-input::placeholder {
  color: #9ca3af;
}

.form-input:focus {
  border-color: #2563eb;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
  background-color: white;
}

.form-input.input-error {
  border-color: #ef4444;
  background-color: #fef2f2;
}

.form-input.input-error:focus {
  border-color: #dc2626;
  box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15);
}

/* Erreurs */
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

/* Mot de passe */
.toggle-password {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  cursor: pointer;
  color: #9ca3af;
  padding: 6px;
  border-radius: 6px;
}

.toggle-password:hover {
  color: #4b5563;
  background-color: #f3f4f6;
}

/* Forgot + remember me */
.forgot-password {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: -0.3rem;
}

.checkbox-container {
  display: inline-flex;
  align-items: center;
  cursor: pointer;
  user-select: none;
  font-size: 0.85rem;
  color: #4b5563;
  gap: 0.4rem;
}

.checkbox-container input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
}

.checkmark {
  width: 16px;
  height: 16px;
  border-radius: 4px;
  border: 1px solid #9ca3af;
  display: inline-block;
  position: relative;
  background-color: #f9fafb;
}

.checkbox-container input:checked + .checkmark {
  background-color: #2563eb;
  border-color: #2563eb;
}

.checkmark::after {
  content: '';
  position: absolute;
  display: none;
}

.checkbox-container input:checked + .checkmark::after {
  display: block;
  left: 4px;
  top: 1px;
  width: 5px;
  height: 9px;
  border: solid #ffffff;
  border-width: 0 2px 2px 0;
  transform: rotate(45deg);
}

.checkbox-label {
  font-size: 0.85rem;
}

.link-blue {
  color: #2563eb;
  text-decoration: none;
  font-size: 0.9rem;
  font-weight: 500;
}

.link-blue:hover {
  text-decoration: underline;
}

/* Bouton */
.btn-connect {
  width: 100%;
  padding: 1rem;
  background-color: #3b82f6;
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  margin-top: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.4rem;
}

.btn-connect:hover:not(:disabled) {
  background-color: #2563eb;
}

.btn-connect:disabled {
  background: #9ca3af;
  cursor: not-allowed;
}

.loading-spinner {
  width: 18px;
  height: 18px;
  border-radius: 999px;
  border: 2.5px solid rgba(255, 255, 255, 0.35);
  border-top-color: white;
  animation: spin 0.7s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

/* Message succès */
.success-message {
  margin-top: 0.6rem;
  font-size: 0.85rem;
  color: #16a34a;
  text-align: center;
}

/* Section register */
.register-section {
  text-align: center;
  font-size: 0.95rem;
  color: #6b7280;
  margin-top: 0.5rem;
}

/* Responsive */
@media (max-width: 1024px) {
  .login-card {
    flex-direction: column;
  }

  .person-icon {
    display: none;
  }

  .left-panel {
    min-height: 250px;
  }
}
</style>
