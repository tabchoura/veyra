<template>
  <div class="success-wrapper">
    <div class="success-card">
      <div class="icon">📧</div>

      <h1>{{ t("title") }}</h1>
      <p class="message">
        {{ t("message1") }}<br />
        {{ t("message2") }}
      </p>

      <p class="hint">
        {{ t("hint") }}
      </p>

      <router-link to="/login" class="btn">
        {{ t("loginBtn") }}
      </router-link>

      <!-- Language switch -->
      <div class="lang-switch">
        <button
          v-for="opt in languages"
          :key="opt"
          :class="{ active: lang === opt }"
          @click="lang = opt"
        >
          {{ opt.toUpperCase() }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";

const lang = ref("fr");

const languages = ["fr", "en"];

const translations = {
  fr: {
    title: "Compte créé avec succès 🎉",
    message1: "Merci ! Un email de confirmation vous a été envoyé.",
    message2: "Veuillez cliquer sur le lien reçu pour activer votre compte.",
    hint: "Pensez à vérifier votre dossier spam si vous ne voyez rien.",
    loginBtn: "Aller à la connexion",
  },
  en: {
    title: "Account successfully created 🎉",
    message1: "Thank you! A confirmation email has been sent.",
    message2: "Please click the link in the email to activate your account.",
    hint: "Check your spam folder if you don’t see the email.",
    loginBtn: "Go to login",
  },
};

const t = (key) => translations[lang.value][key];
</script>

<style scoped>
.success-wrapper {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f8f9fb;
  font-family: "Inter", sans-serif;
}

.success-card {
  background: white;
  padding: 50px 40px;
  max-width: 420px;
  width: 100%;
  border-radius: 14px;
  box-shadow: 0 15px 40px rgba(0, 0, 0, 0.08);
  text-align: center;
}

.icon {
  font-size: 48px;
  margin-bottom: 20px;
}

h1 {
  font-size: 24px;
  font-weight: 700;
  color: #001d48;
  margin-bottom: 16px;
}

.message {
  font-size: 15px;
  color: #374151;
  line-height: 1.6;
  margin-bottom: 12px;
}

.hint {
  font-size: 13px;
  color: #6b7280;
  margin-bottom: 30px;
}

.btn {
  display: inline-block;
  background: #0a58ff;
  color: white;
  padding: 12px 26px;
  border-radius: 999px;
  font-weight: 600;
  text-decoration: none;
  transition: background 0.2s ease;
}

.btn:hover {
  background: #0847cc;
}

.lang-switch {
  margin-top: 25px;
  display: flex;
  justify-content: center;
  gap: 10px;
}

.lang-switch button {
  border: 1px solid #e5e7eb;
  background: white;
  padding: 6px 14px;
  border-radius: 999px;
  cursor: pointer;
  font-size: 13px;
  color: #374151;
}

.lang-switch button.active {
  background: #0a58ff;
  color: white;
  border-color: #0a58ff;
}
</style>
