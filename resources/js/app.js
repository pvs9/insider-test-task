/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import App from "./layouts/App";
import { createApp } from 'vue'
import router from "./router";
import api from "./api";

const app = createApp(App)

app.config.globalProperties.$axios = api;
app.use(router)
app.mount('#app')
