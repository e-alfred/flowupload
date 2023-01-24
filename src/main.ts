import { createApp } from 'vue'
import { translate, translatePlural } from "nextcloud-l10n";

import App from "./App.vue";

let app = createApp(App)
app.config.globalProperties.t = translate;
app.config.globalProperties.n = translatePlural;
app.mount('#app')
