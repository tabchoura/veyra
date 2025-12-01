<template>
  <div class="page-wrapper">
    <div class="reset-container">
      <!-- Card principal -->
      <div class="reset-card">
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
          <!-- Token invalide ou expiré -->
          <div v-if="tokenError" class="error-state">
            <div class="icon-wrapper">
              <div class="icon-circle error">
                <svg class="icon-svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
              </div>
            </div>
            <h2 class="error-title">{{ t.invalidToken }}</h2>
            <p class="error-message">{{ t.tokenExpiredMessage }}</p>
            <router-link to="/forgot-password" class="btn-retry">
              {{ t.requestNewLink }}
            </router-link>
          </div>

          <!-- Formulaire de réinitialisation -->
          <div v-else-if="!success">
            <!-- Icône -->
            <div class="icon-wrapper">
              <div class="icon-circle">
                <svg class="icon-svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                </svg>
              </div>
            </div>

            <h1 class="title">{{ t.title }}</h1>
            <p class="subtitle">{{ t.subtitle }}</p>

            <!-- Formulaire -->
            <div class="form-wrapper">
              <!-- Nouveau mot de passe -->
              <div class="form-group">
                <label class="form-label">{{ t.newPassword }}</label>
                <div class="input-wrapper">
                  <svg class="input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <rect x="5" y="11" width="14" height="10" rx="2" />
                    <path d="M12 17a1 1 0 1 0 0-2 1 1 0 0 0 0 2z" />
                    <path d="M8 11V7a4 4 0 0 1 8 0v4" />
                  </svg>
                  <input
                    :type="showPassword ? 'text' : 'password'"
                    v-model.trim="password"
                    class="form-input"
                    :class="{ 'input-error': passwordError }"
                    :placeholder="t.passwordPlaceholder"
                    @input="passwordError = ''"
                  />
                  <button
                    type="button"
                    class="toggle-password"
                    @click="showPassword = !showPassword"
                  >
                    <svg v-if="!showPassword" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                      <circle cx="12" cy="12" r="3" />
                    </svg>
                    <svg v-else width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24" />
                      <line x1="1" y1="1" x2="23" y2="23" />
                    </svg>
                  </button>
                </div>
                <transition name="error-slide">
                  <p v-if="passwordError" class="error-msg">
                    <span class="error-dot"></span>
                    {{ passwordError }}
                  </p>
                </transition>
              </div>

              <!-- Confirmer mot de passe -->
              <div class="form-group">
                <label class="form-label">{{ t.confirmPassword }}</label>
                <div class="input-wrapper">
                  <svg class="input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <rect x="5" y="11" width="14" height="10" rx="2" />
                    <path d="M12 17a1 1 0 1 0 0-2 1 1 0 0 0 0 2z" />
                    <path d="M8 11V7a4 4 0 0 1 8 0v4" />
                  </svg>
                  <input
                    :type="showConfirmPassword ? 'text' : 'password'"
                    v-model.trim="confirmPassword"
                    class="form-input"
                    :class="{ 'input-error': confirmError }"
                    :placeholder="t.confirmPlaceholder"
                    @input="confirmError = ''"
                    @keyup.enter="handleSubmit"
                  />
                  <button
                    type="button"
                    class="toggle-password"
                    @click="showConfirmPassword = !showConfirmPassword"
                  >
                    <svg v-if="!showConfirmPassword" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                      <circle cx="12" cy="12" r="3" />
                    </svg>
                    <svg v-else width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24" />
                      <line x1="1" y1="1" x2="23" y2="23" />
                    </svg>
                  </button>
                </div>
                <transition name="error-slide">
                  <p v-if="confirmError" class="error-msg">
                    <span class="error-dot"></span>
                    {{ confirmError }}
                  </p>
                </transition>
              </div>

              <!-- Indicateur de force -->
              <div v-if="password" class="password-strength">
                <div class="strength-bar">
                  <div
                    class="strength-fill"
                    :class="passwordStrength.class"
                    :style="{ width: passwordStrength.width }"
                  ></div>
                </div>
                <p class="strength-text" :class="passwordStrength.class">
                  {{ passwordStrength.text }}
                </p>
              </div>

              <button
                type="button"
                class="btn-submit"
                :disabled="isLoading"
                @click="handleSubmit"
              >
                <span v-if="isLoading" class="loading-spinner"></span>
                <span v-else>{{ t.resetButton }}</span>
              </button>
            </div>
          </div>

          <!-- Succès -->
          <div v-else class="success-wrapper">
            <div class="icon-wrapper">
              <div class="icon-circle success">
                <svg class="icon-svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
              </div>
            </div>

            <h2 class="success-title">{{ t.successTitle }}</h2>
            <p class="success-message">{{ t.successMessage }}</p>

            <router-link to="/login" class="btn-login">
              {{ t.goToLogin }}
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
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()

const language = ref('fr')
const isLangMenuOpen = ref(false)
const password = ref('')
const confirmPassword = ref('')
const showPassword = ref(false)
const showConfirmPassword = ref(false)
const passwordError = ref('')
const confirmError = ref('')
const isLoading = ref(false)
const success = ref(false)
const tokenError = ref(false)

const token = ref('')
const email = ref('')

const languageOptions = [
  { code: 'en', label: 'EN' },
  { code: 'fr', label: 'FR' }
]

const translations = {
  fr: {
    title: 'Créer un nouveau mot de passe',
    subtitle: 'Choisissez un mot de passe sécurisé pour votre compte.',
    newPassword: 'Nouveau mot de passe',
    passwordPlaceholder: 'Entrez votre nouveau mot de passe',
    confirmPassword: 'Confirmer le mot de passe',
    confirmPlaceholder: 'Confirmez votre mot de passe',
    resetButton: 'Réinitialiser le mot de passe',
    passwordShort: 'Le mot de passe doit contenir au moins 8 caractères',
    passwordMismatch: 'Les mots de passe ne correspondent pas',
    successTitle: 'Mot de passe modifié !',
    successMessage: 'Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.',
    goToLogin: 'Se connecter',
    invalidToken: 'Lien invalide ou expiré',
    tokenExpiredMessage: 'Ce lien de réinitialisation a expiré ou est invalide. Veuillez en demander un nouveau.',
    requestNewLink: 'Demander un nouveau lien',
    weak: 'Faible',
    medium: 'Moyen',
    strong: 'Fort'
  },
  en: {
    title: 'Create a new password',
    subtitle: 'Choose a secure password for your account.',
    newPassword: 'New password',
    passwordPlaceholder: 'Enter your new password',
    confirmPassword: 'Confirm password',
    confirmPlaceholder: 'Confirm your password',
    resetButton: 'Reset password',
    passwordShort: 'Password must be at least 8 characters',
    passwordMismatch: 'Passwords do not match',
    successTitle: 'Password changed!',
    successMessage: 'Your password has been reset successfully. You can now log in.',
    goToLogin: 'Log in',
    invalidToken: 'Invalid or expired link',
    tokenExpiredMessage: 'This reset link has expired or is invalid. Please request a new one.',
    requestNewLink: 'Request new link',
    weak: 'Weak',
    medium: 'Medium',
    strong: 'Strong'
  }
}

const t = computed(() => translations[language.value])

const passwordStrength = computed(() => {
  const pwd = password.value
  if (!pwd) return { width: '0%', class: '', text: '' }

  let strength = 0
  if (pwd.length >= 8) strength++
  if (/[a-z]/.test(pwd) && /[A-Z]/.test(pwd)) strength++
  if (/\d/.test(pwd)) strength++
  if (/[^a-zA-Z0-9]/.test(pwd)) strength++

  if (strength <= 2) {
    return { width: '33%', class: 'weak', text: t.value.weak }
  } else if (strength === 3) {
    return { width: '66%', class: 'medium', text: t.value.medium }
  } else {
    return { width: '100%', class: 'strong', text: t.value.strong }
  }
})

onMounted(() => {
  token.value = route.query.token || ''
  email.value = route.query.email || ''

  if (!token.value || !email.value) {
    tokenError.value = true
  }
})

const selectLanguage = (code) => {
  language.value = code
  isLangMenuOpen.value = false
}

const handleSubmit = async () => {
  passwordError.value = ''
  confirmError.value = ''

  if (password.value.length < 8) {
    passwordError.value = t.value.passwordShort
    return
  }

  if (password.value !== confirmPassword.value) {
    confirmError.value = t.value.passwordMismatch
    return
  }

  isLoading.value = true

  try {
    await axios.post('/api/auth/reset-password', {
      email: email.value,
      token: token.value,
      password: password.value,
      password_confirmation: confirmPassword.value
    })

    success.value = true
  } catch (err) {
    if (err.response && err.response.status === 400) {
      tokenError.value = true
    } else {
      passwordError.value = 'Une erreur est survenue. Veuillez réessayer.'
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
  position: fixed;
  top: 0;
  left: 0;
  padding: 1rem;
}

.reset-container {
  width: 100%;
  max-width: 500px;
}

.reset-card {
  background: white;
  border-radius: 24px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
  overflow: hidden;
}

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

.card-content {
  padding: 2rem;
  padding-top: 1rem;
}

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

.icon-circle.error {
  width: 80px;
  height: 80px;
  background: #fee2e2;
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

.icon-circle.error .icon-svg {
  width: 40px;
  height: 40px;
  color: #dc2626;
}

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
  padding: 0.75rem 2.8rem 0.75rem 2.6rem;
  border: 2px solid #d1d5db;
  border-radius: 8px;
  font-size: 0.9rem;
  outline: none;
  transition: all 0.2s;
  background-color: #f9fafb;
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

.error-msg {
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

.error-slide-enter-from,
.error-slide-leave-to {
  opacity: 0;
  transform: translateY(-6px);
}

.password-strength {
  margin-top: -0.5rem;
}

.strength-bar {
  width: 100%;
  height: 4px;
  background: #e5e7eb;
  border-radius: 999px;
  overflow: hidden;
}

.strength-fill {
  height: 100%;
  transition: all 0.3s ease;
}

.strength-fill.weak {
  background: #ef4444;
}

.strength-fill.medium {
  background: #f59e0b;
}

.strength-fill.strong {
  background: #10b981;
}

.strength-text {
  margin-top: 0.4rem;
  font-size: 0.8rem;
  font-weight: 600;
}

.strength-text.weak {
  color: #ef4444;
}

.strength-text.medium {
  color: #f59e0b;
}

.strength-text.strong {
  color: #10b981;
}

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
  margin-bottom: 2rem;
  line-height: 1.6;
}

.btn-login {
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

.btn-login:hover {
  background-color: #1d4ed8;
  box-shadow: 0 6px 16px rgba(37, 99, 235, 0.3);
}

.error-state {
  text-align: center;
  animation: fadeIn 0.3s ease-out;
}

.error-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #dc2626;
  margin-bottom: 0.75rem;
}

.error-message {
  color: #6b7280;
  margin-bottom: 2rem;
  line-height: 1.6;
}

.btn-retry {
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

.btn-retry:hover {
  background-color: #1d4ed8;
  box-shadow: 0 6px 16px rgba(37, 99, 235, 0.3);
}

.footer {
  text-align: center;
  margin-top: 1.5rem;
  font-size: 0.85rem;
  color: #6b7280;
}

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

@media (max-width: 640px) {
  .card-content {
    padding: 1.5rem;
  }

  .title {
    font-size: 1.3rem;
  }
}
</style>