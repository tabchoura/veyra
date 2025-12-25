import axios from "axios";

const api = axios.create({
  baseURL: import.meta.env.PROD
    ? "https://dtex.greenpulse-consulting.tn"
    : "http://127.0.0.1:8000",
  headers: {
    Accept: "application/json",
  },
});

export default api;
