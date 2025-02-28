import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './App.vue';

// Import styles using the modern approach
import './styles/main.scss';

// Create the Pinia store
const pinia = createPinia();

// Create and mount the Vue app
const app = createApp(App);
app.use(pinia);
app.mount('#imagine-editor-app');
