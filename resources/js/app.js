import {createApp} from 'vue'
import {createBootstrap} from 'bootstrap-vue-next'

// Add the necessary CSS
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue-next/dist/bootstrap-vue-next.css'
import "vue-select/dist/vue-select.css"

import App from "./App.vue"

import router from './router'

import VueSelect  from "vue-select";

const app = createApp(App)
app.use(createBootstrap())
app.component("v-select", VueSelect)
app.use(router)

app.mount('#app')
