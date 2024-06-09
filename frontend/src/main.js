import './assets/main.css'

import { createApp } from 'vue'
import { createPinia } from 'pinia'

import App from './App.vue'
import router from './router'
import VueGoogleMaps from '@fawmi/vue-google-maps'


const app = createApp(App)

app.use(VueGoogleMaps, {
    load: {
        key: 'AIzaSyBqvZfzDW7YlZHtfaR-5l1v8f0YkMzswQM',
        libraries: "places"
    },
})

app.use(createPinia())
app.use(router)


app.mount('#app')
