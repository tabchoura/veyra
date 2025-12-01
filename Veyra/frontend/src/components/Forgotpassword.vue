<template>
  <div class="page-wrapper">
    <div class="forgot-password-container">
      <!-- Card principal -->
      <div class="forgot-card">
        <!-- Header avec langue -->
        <div class="header-lang" @click="isLangMenuOpen = false">
          <div class="language-dropdown" @click.stop>
            <button
              type="button"
              class="language-toggle"
              @click="isLangMenuOpen = !isLangMenuOpen"
            >
              <span>🌐</span>
              <span>{{ language.toUpperCase() }}</span>
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
                  <span>🌐</span>
                  <span>{{ opt.label }}</span>
                </button>
              </div>
            </transition>
          </div>
        </div>

        <!-- Contenu principal -->
        <div class="card-content">
          <!-- Étape 1 : Formulaire Email -->
          <div v-if="step === 'email'">
            <!-- Icône -->
            <div class="icon-wrapper">
              <div class="icon-circle">
                <svg
                  class="icon-svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                  stroke-width="2"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"
                  />
                </svg>
              </div>
            </div>

            <!-- Titre -->
            <h1 class="title">{{ t.title }}</h1>
            <p class="subtitle">{{ t.subtitle }}</p>

            <!-- Formulaire -->
            <div class="form-wrapper">
              <div class="form-group">
                <label class="form-label">{{ t.email }}</label>
                <div class="input-wrapper">
                  <svg
                    class="input-icon"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                  >
                    <rect x="3" y="5" width="18" height="14" rx="2" />
                    <path d="m3 7 9 6 9-6" />
                  </svg>
                  <input
                    type="email"
                    v-model.trim="email"
                    class="form-input"
                    :class="{ 'input-error': emailError }"
                    :placeholder="t.emailPlaceholder"
                    @input="emailError = ''"
                    @keyup.enter="handleSubmit"
                  />
                </div>
                <transition name="error-slide">
                  <p v-if="emailError" class="error-message">
                    <span class="error-dot"></span>
                    {{ emailError }}
                  </p>
                </transition>
              </div>

              <button
                type="button"
                class="btn-submit"
                :disabled="isLoading"
                @click="handleSubmit"
              >
                <span v-if="isLoading" class="loading-spinner"></span>
                <span v-else>{{ t.sendLink }}</span>
              </button>
            </div>

            <!-- Retour connexion -->
            <div class="back-link">
              <router-link to="/login" class="link-blue">
                <svg
                  class="arrow-icon"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                  stroke-width="2"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M10 19l-7-7m0 0l7-7m-7 7h18"
                  />
                </svg>
                {{ t.backToLogin }}
              </router-link>
            </div>
          </div>

          <!-- Étape 2 : Succès -->
          <div v-else class="success-wrapper">
            <!-- Icône succès -->
            <div class="icon-wrapper">
              <div class="icon-circle success">
                <svg
                  class="icon-svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                  stroke-width="2"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76"
                  />
                </svg>
              </div>
            </div>

            <h2 class="success-title">{{ t.successTitle }}</h2>
            <p class="success-message">{{ t.successMessage }}</p>
            <p class="success-hint">{{ t.checkSpam }}</p>

            <!-- Email envoyé à -->
            <div class="email-display">
              <p class="email-label">{{ t.sentTo }}</p>
              <p class="email-value">{{ email }}</p>
            </div>

            <!-- Renvoyer -->
            <div class="resend-section">
              {{ t.didNotReceive }}
              <button type="button" class="resend-btn" @click="handleResend">
                {{ t.resend }}
              </button>
            </div>

            <!-- Retour connexion -->
            <router-link to="/login" class="btn-back-login">
              {{ t.backToLogin }}
            </router-link>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="footer">
        <p>© 2025 Veyra® - Green Pulse Consulting</p>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref, computed } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const router = useRouter()

const email = ref('')
const language = ref('fr')
const isLangMenuOpen = ref(false)
const emailError = ref('')
const isLoading = ref(false)
const step = ref('email') // 'email' ou 'success'

const languageOptions = [
  { code: 'en', label: 'EN' },
  { code: 'fr', label: 'FR' }
]

const translations = {
  fr: {
    title: 'Réinitialiser votre mot de passe',
    subtitle:
      'Entrez votre adresse email et nous vous enverrons un lien pour réinitialiser votre mot de passe.',
    email: 'Email',
    emailPlaceholder: 'Renseignez votre adresse email',
    sendLink: 'Envoyer le lien',
    backToLogin: 'Retour à la connexion',
    emailInvalid: 'Email invalide',
    loading: 'Envoi en cours...',
    successTitle: 'Email envoyé !',
    successMessage:
      'Un lien de réinitialisation a été envoyé à votre adresse email. Veuillez vérifier votre boîte de réception.',
    checkSpam: 'Pensez également à vérifier vos spams.',
    didNotReceive: "Vous n'avez pas reçu l'email ?",
    resend: 'Renvoyer',
    emailNotFound: 'Aucun compte associé à cet email',
    sentTo: 'Email envoyé à :'
  },
  en: {
    title: 'Reset your password',
    subtitle:
      'Enter your email address and we will send you a link to reset your password.',
    email: 'Email',
    emailPlaceholder: 'Enter your email address',
    sendLink: 'Send link',
    backToLogin: 'Back to login',
    emailInvalid: 'Invalid email',
    loading: 'Sending...',
    successTitle: 'Email sent!',
    successMessage:
      'A reset link has been sent to your email address. Please check your inbox.',
    checkSpam: 'Also check your spam folder.',
    didNotReceive: "Didn't receive the email?",
    resend: 'Resend',
    emailNotFound: 'No account associated with this email',
    sentTo: 'Email sent to:'
  }
}

const t = computed(() => translations[language.value])

const selectLanguage = (code) => {
  language.value = code
  isLangMenuOpen.value = false
}

const validateEmail = (emailValue) => {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return emailRegex.test(emailValue)
}

const handleSubmit = async () => {
  emailError.value = ''

  if (!validateEmail(email.value)) {
    emailError.value = t.value.emailInvalid
    return
  }

  isLoading.value = true

  try {
    await axios.post('/api/auth/forgot-password', {
      email: email.value
    })

    step.value = 'success'
  } catch (err) {
    if (err.response && err.response.status === 404) {
      emailError.value = t.value.emailNotFound
    } else {
      emailError.value = 'Une erreur est survenue. Veuillez réessayer.'
    }
  } finally {
    isLoading.value = false
  }
}

const handleResend = () => {
  step.value = 'email'
  email.value = ''
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
  background: radial-gradient(
    circle at top left,
    #e0f2fe 0,
    #eff6ff 30%,
    #f9fafb 70%,
    #e5e7eb 100%
  );
  display: flex;
  align-items: center;
  justify-content: center;
  position: fixed;
  top: 0;
  left: 0;
  padding: 1rem;
}

.forgot-password-container {
  width: 100%;
  max-width: 500px;
}

.forgot-card {
  background: white;
  border-radius: 24px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
  overflow: hidden;
}

/* Header langue */
.header-lang {
  padding: 1.5rem 1.5rem 0;
  display: flex;
  justify-content: flex-end;
  position: relative;
}

.language-dropdown {
  position: relative;
}

.language-toggle {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  border-radius: 999px;
  border: 1px solid #e5e7eb;
  background: white;
  cursor: pointer;
  font-size: 0.9rem;
  color: #111827;
  transition: all 0.2s ease;
  font-weight: 500;
}

.language-toggle:hover {
  border-color: #2563eb;
  box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
}

.chevron {
  font-size: 0.75rem;
}

.language-menu {
  position: absolute;
  right: 0;
  top: 110%;
  background: white;
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
  padding: 0.5rem 1rem;
  font-size: 0.9rem;
  cursor: pointer;
  color: #111827;
  transition: background 0.2s;
}

.language-option:hover {
  background-color: #f3f4f6;
}

/* Transitions */
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

/* Contenu principal */
.card-content {
  padding: 2rem;
  padding-top: 1rem;
}

/* Icône */
.icon-wrapper {
  display: flex;
  justify-content: center;
  margin-bottom: 1.5rem;
}

.icon-circle {
  width: 64px;
  height: 64px;
  background: #dbeafe;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.icon-circle.success {
  width: 80px;
  height: 80px;
  background: #dcfce7;
  animation: scaleIn 0.4s ease-out;
}

.icon-svg {
  width: 32px;
  height: 32px;
  color: #2563eb;
}

.icon-circle.success .icon-svg {
  width: 40px;
  height: 40px;
  color: #16a34a;
}

/* Titre */
.title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #111827;
  text-align: center;
  margin-bottom: 0.5rem;
}

.subtitle {
  color: #6b7280;
  text-align: center;
  margin-bottom: 2rem;
  font-size: 0.9rem;
  line-height: 1.6;
}

/* Formulaire */
.form-wrapper {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-label {
  font-size: 0.9rem;
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
  width: 20px;
  height: 20px;
  color: #9ca3af;
}

.form-input {
  width: 100%;
  padding: 0.75rem 1rem 0.75rem 2.6rem;
  border: 2px solid #d1d5db;
  border-radius: 8px;
  font-size: 0.9rem;
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

/* Bouton submit */
.btn-submit {
  width: 100%;
  padding: 0.85rem;
  background-color: #2563eb;
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
}

.btn-submit:hover:not(:disabled) {
  background-color: #1d4ed8;
  box-shadow: 0 6px 16px rgba(37, 99, 235, 0.3);
}

.btn-submit:disabled {
  background: #9ca3af;
  cursor: not-allowed;
  box-shadow: none;
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

/* Retour connexion */
.back-link {
  margin-top: 1.5rem;
  text-align: center;
}

.link-blue {
  color: #2563eb;
  text-decoration: none;
  font-size: 0.9rem;
  font-weight: 500;
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  transition: color 0.2s;
}

.link-blue:hover {
  color: #1d4ed8;
  text-decoration: underline;
}

.arrow-icon {
  width: 16px;
  height: 16px;
}

/* Succès */
.success-wrapper {
  text-align: center;
  animation: fadeIn 0.3s ease-out;
}

.success-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #111827;
  margin-bottom: 0.75rem;
}

.success-message {
  color: #6b7280;
  margin-bottom: 0.5rem;
  line-height: 1.6;
}

.success-hint {
  font-size: 0.85rem;
  color: #9ca3af;
  margin-bottom: 2rem;
}

.email-display {
  background-color: #f9fafb;
  border-radius: 8px;
  padding: 1rem;
  margin-bottom: 1.5rem;
}

.email-label {
  font-size: 0.85rem;
  color: #6b7280;
  margin-bottom: 0.25rem;
}

.email-value {
  font-weight: 600;
  color: #111827;
}

.resend-section {
  font-size: 0.9rem;
  color: #6b7280;
  margin-bottom: 1.5rem;
}

.resend-btn {
  color: #2563eb;
  font-weight: 500;
  background: none;
  border: none;
  cursor: pointer;
  text-decoration: underline;
  padding: 0;
}

.resend-btn:hover {
  color: #1d4ed8;
}

.btn-back-login {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  background-color: #2563eb;
  color: white;
  border-radius: 8px;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.2s;
  box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
}

.btn-back-login:hover {
  background-color: #1d4ed8;
  box-shadow: 0 6px 16px rgba(37, 99, 235, 0.3);
}

/* Footer */
.footer {
  text-align: center;
  margin-top: 1.5rem;
  font-size: 0.85rem;
  color: #6b7280;
}

/* Animations */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes scaleIn {
  from {
    opacity: 0;
    transform: scale(0.8);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

/* Responsive */
@media (max-width: 640px) {
  .card-content {
    padding: 1.5rem;
  }

  .title {
    font-size: 1.3rem;
  }

  .subtitle {
    font-size: 0.85rem;
  }
}
</style>