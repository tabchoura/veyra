<template>
  <div class="page-wrapper">
    <div class="login-card">
      <!-- Panneau gauche -->
      <div class="left-panel">
        <div class="background-animation">
          <div class="bubble bubble-1"></div>
          <div class="bubble bubble-2"></div>
        </div>

        <div class="welcome-content">
          <h1 class="welcome-title">
            Espace administrateur
          </h1>
          <div class="welcome-divider"></div>
          <p class="welcome-text">
            Connectez-vous avec vos identifiants admin pour accéder au tableau de bord Veyra.
          </p>
        </div>

        <div class="person-icon">
          <div class="person-head"></div>
          <div class="person-body"></div>
          <div class="person-foot"></div>
        </div>

        <div class="panel-footer">
          <p>
            Retour au site ? 
            <router-link to="/login" class="footer-link">
              Connexion utilisateur
            </router-link>
          </p>
        </div>
      </div>

      <!-- Panneau droit : formulaire -->
      <div class="right-panel">
        <header class="topbar">
          <div class="logo">Veyra<span>®</span></div>
        </header>

        <div class="form-container">
          <h2 class="form-title">Connexion administrateur</h2>
          <p class="form-subtitle">
            Veuillez entrer vos identifiants administrateur.
          </p>

          <form @submit.prevent="handleLogin">
            <div class="form-group">
              <label for="email">Email admin</label>
              <input
                id="email"
                type="email"
                v-model="email"
                required
                :class="{ 'input-error': errors.email }"
                @input="errors.email = ''"
                placeholder="admin@admin.com"
              />
              <p v-if="errors.email" class="error-message">
                {{ errors.email }}
              </p>
            </div>

            <div class="form-group">
              <label for="password">Mot de passe</label>
              <div class="password-wrapper">
                <input
                  id="password"
                  :type="showPassword ? 'text' : 'password'"
                  v-model="password"
                  required
                  :class="{ 'input-error': errors.password }"
                  @input="errors.password = ''"
                  placeholder="••••••••"
                />
                <button
                  type="button"
                  class="toggle-password"
                  @click="showPassword = !showPassword"
                >
                  {{ showPassword ? "🙈" : "👁️" }}
                </button>
              </div>
              <p v-if="errors.password" class="error-message">
                {{ errors.password }}
              </p>
            </div>

            <p v-if="serverError" class="server-error">
              {{ serverError }}
            </p>

            <button
              type="submit"
              class="primary-btn"
              :disabled="loading"
            >
              <span v-if="loading">Connexion...</span>
              <span v-else>Se connecter</span>
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";

const router = useRouter();

const email = ref("");
const password = ref("");
const showPassword = ref(false);
const loading = ref(false);
const serverError = ref("");

const errors = ref({
  email: "",
  password: "",
});

// ⚙️ Optionnel : si tu n'as pas déjà mis baseURL globalement
axios.defaults.baseURL = "http://127.0.0.1:8000";

const handleLogin = async () => {
  serverError.value = "";
  errors.value.email = "";
  errors.value.password = "";

  if (!email.value) {
    errors.value.email = "L'email est obligatoire.";
    return;
  }
  if (!password.value) {
    errors.value.password = "Le mot de passe est obligatoire.";
    return;
  }

  loading.value = true;

  try {
    const response = await axios.post("/api/auth/loginadmin", {
      email: email.value,
      password: password.value,
    });

    const user = response.data.user;
    const token = response.data.token;

    // On vérifie que c'est bien un admin
  // On vérifie que c'est bien un ADMIN
if (user.user_type !== "ADMIN") {
  serverError.value =
    "Ce compte n'a pas les droits administrateur.";
  loading.value = false;
  return;
}


    // Stocker le token + infos user
    localStorage.setItem("token", token);
    localStorage.setItem("user", JSON.stringify(user));

    // Configurer Axios pour les prochains appels
    axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;

    // Redirection vers dashboard admin
    router.push("/dashboard/admin");
  } catch (error) {
    console.error(error);
    if (error.response?.status === 401) {
      serverError.value = "Identifiants invalides.";
    } else if (error.response?.status === 403) {
      serverError.value =
        error.response.data?.message ||
        "Accès refusé. Vérifiez vos droits ou votre email.";
    } else {
      serverError.value =
        error.response?.data?.message ||
        "Une erreur est survenue lors de la connexion.";
    }
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.page-wrapper {
  min-height: 100vh;
  background: #0f172a;
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: "Inter", sans-serif;
}

.login-card {
  width: 900px;
  max-width: 95%;
  min-height: 520px;
  background: #0b1220;
  border-radius: 24px;
  box-shadow: 0 24px 80px rgba(15, 23, 42, 0.5);
  display: grid;
  grid-template-columns: 1.2fr 1fr;
  overflow: hidden;
}

/* Left panel */
.left-panel {
  position: relative;
  padding: 32px;
  background: radial-gradient(circle at top left, #1d4ed8, #0f172a);
  color: white;
  overflow: hidden;
}

.background-animation {
  position: absolute;
  inset: 0;
  opacity: 0.35;
  pointer-events: none;
}

.bubble {
  position: absolute;
  border-radius: 999px;
  filter: blur(30px);
}
.bubble-1 {
  width: 220px;
  height: 220px;
  background: #38bdf8;
  top: -40px;
  right: -40px;
}
.bubble-2 {
  width: 260px;
  height: 260px;
  background: #22c55e;
  bottom: -60px;
  left: -60px;
}

.welcome-content {
  position: relative;
  z-index: 1;
  margin-top: 40px;
}

.welcome-title {
  font-size: 28px;
  font-weight: 700;
  margin-bottom: 8px;
}

.welcome-divider {
  width: 48px;
  height: 3px;
  border-radius: 999px;
  background: #38bdf8;
  margin-bottom: 12px;
}

.welcome-text {
  font-size: 14px;
  color: #e5e7eb;
  max-width: 260px;
}

.person-icon {
  position: absolute;
  bottom: 90px;
  right: 40px;
  width: 64px;
  height: 64px;
}
.person-head {
  width: 18px;
  height: 18px;
  border-radius: 999px;
  background: #facc15;
  margin: 0 auto;
}
.person-body {
  width: 26px;
  height: 26px;
  border-radius: 999px;
  background: #38bdf8;
  margin: 4px auto;
}
.person-foot {
  width: 40px;
  height: 6px;
  border-radius: 999px;
  background: rgba(15, 23, 42, 0.8);
  margin: 4px auto;
}

.panel-footer {
  position: absolute;
  bottom: 20px;
  left: 32px;
  font-size: 12px;
  color: #e5e7eb;
}

.footer-link {
  color: #bfdbfe;
  text-decoration: underline;
}

/* Right panel */
.right-panel {
  background: #020617;
  color: #e5e7eb;
  display: flex;
  flex-direction: column;
}

.topbar {
  display: flex;
  justify-content: flex-end;
  padding: 14px 24px;
}

.logo {
  font-weight: 700;
  font-size: 18px;
  color: #e5e7eb;
}
.logo span {
  font-size: 11px;
  vertical-align: super;
}

.form-container {
  padding: 32px 32px 40px;
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.form-title {
  font-size: 22px;
  font-weight: 600;
  margin-bottom: 6px;
}

.form-subtitle {
  font-size: 13px;
  color: #9ca3af;
  margin-bottom: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
  margin-bottom: 16px;
}

label {
  font-size: 13px;
  margin-bottom: 4px;
  color: #e5e7eb;
}

input {
  padding: 10px 12px;
  font-size: 14px;
  border-radius: 8px;
  border: 1px solid #1f2937;
  background: #020617;
  color: #e5e7eb;
  outline: none;
  transition: all 0.2s;
}

input:focus {
  border-color: #3b82f6;
  box-shadow: 0 0 0 1px rgba(59, 130, 246, 0.4);
}

.password-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.password-wrapper input {
  width: 100%;
  padding-right: 40px;
}

.toggle-password {
  position: absolute;
  right: 10px;
  border: none;
  background: transparent;
  cursor: pointer;
  font-size: 16px;
}

.input-error {
  border-color: #ef4444 !important;
}

.error-message {
  margin-top: 4px;
  font-size: 12px;
  color: #fecaca;
}

.server-error {
  margin: 8px 0 12px;
  font-size: 13px;
  color: #fecaca;
}

.primary-btn {
  width: 100%;
  margin-top: 8px;
  padding: 10px 14px;
  border-radius: 999px;
  border: none;
  font-size: 14px;
  font-weight: 600;
  background: linear-gradient(to right, #2563eb, #38bdf8);
  color: white;
  cursor: pointer;
  transition: all 0.2s;
}

.primary-btn:hover {
  filter: brightness(1.05);
  transform: translateY(-1px);
}

.primary-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Responsive */
@media (max-width: 820px) {
  .login-card {
    grid-template-columns: 1fr;
    min-height: auto;
  }
  .left-panel {
    display: none;
  }
}
</style>
