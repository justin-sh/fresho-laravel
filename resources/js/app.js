import {createApp} from 'vue'
import {createBootstrap} from 'bootstrap-vue-next'

// Add the necessary CSS
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue-next/dist/bootstrap-vue-next.css'
import "vue-select/dist/vue-select.css"

import App from "./App.vue"

import router from './router'

import VueSelect from "vue-select";
import pinia from "@/store/index";
import vueEsign from 'vue-esign'

/////
import "./echo";

/* import font awesome icon component */
import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
// import {faFontAwesome} from "@fortawesome/free-brands-svg-icons";
import {faMagnifyingGlass} from "@fortawesome/free-solid-svg-icons";
library.add(faMagnifyingGlass)


const app = createApp(App)
app.use(createBootstrap())
app.component('font-awesome-icon', FontAwesomeIcon)
app.component("v-select", VueSelect)
app.use(router)
app.use(pinia)
app.use(vueEsign)

app.mount('#app')
