import './bootstrap';
import '../css/app.css';
// import {Form} from "@primevue/forms";
import {definePreset} from "@primevue/themes";
import {createPinia} from "pinia";
import {ConfirmationService} from "primevue";
import {ConfirmPopup} from "primevue";
import {PickList} from "primevue";
import {Password} from "primevue";
import {Paginator} from "primevue";
import {Column} from "primevue";
import {DataTable} from "primevue";
import {Toast} from "primevue";
import {ToastService} from "primevue";
import {Badge} from "primevue";
import {Ripple} from "primevue";
import {Tooltip} from "primevue";
import {Card} from "primevue";
import {Select} from "primevue";
import {DeferredContent} from "primevue";
import {MultiSelect} from "primevue";
import {Message} from "primevue";
import {Menubar} from "primevue";
import {FloatLabel} from "primevue";
import {InputText} from "primevue";
import {InputIcon} from "primevue";
import {IconField} from "primevue";
import {Form} from "vee-validate";

import {createApp, h} from 'vue'
import {createInertiaApp} from '@inertiajs/vue3'

import PrimeVue from 'primevue/config'
import Aura from '@primevue/themes/aura';
import 'primeicons/primeicons.css';

import Button from "primevue/button";
import Checkbox from "primevue/checkbox";
import Image from "primevue/image";
import InputNumber from "primevue/inputnumber";
import {ZiggyVue} from "ziggy-js";
import DefaultLayout from "./layouts/DefaultLayout.vue";
import {Ziggy} from './ziggy'
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate'

import '@fontsource/inter';

const Noir = definePreset(Aura, {
    options: {
        cssLayer: {
            name: 'primevue',
            order: 'tailwind-base, primevue, tailwind-utilities'
        }
    },
    semantic: {
        primary: {
            50: '{zinc.50}',
            100: '{zinc.100}',
            200: '{zinc.200}',
            300: '{zinc.300}',
            400: '{zinc.400}',
            500: '{zinc.500}',
            600: '{zinc.600}',
            700: '{zinc.700}',
            800: '{zinc.800}',
            900: '{zinc.900}',
            950: '{zinc.950}'
        },
        colorScheme: {
            light: {
                primary: {
                    color: '{zinc.950}',
                    inverseColor: '#ffffff',
                    hoverColor: '{zinc.900}',
                    activeColor: '{zinc.800}'
                },
                highlight: {
                    background: '{zinc.950}',
                    focusBackground: '{zinc.700}',
                    color: '#ffffff',
                    focusColor: '#ffffff'
                }
            },
            dark: {
                primary: {
                    color: '{zinc.50}',
                    inverseColor: '{zinc.950}',
                    hoverColor: '{zinc.100}',
                    activeColor: '{zinc.200}'
                },
                highlight: {
                    background: 'rgba(250, 250, 250, .16)',
                    focusBackground: 'rgba(250, 250, 250, .24)',
                    color: 'rgba(255,255,255,.87)',
                    focusColor: 'rgba(255,255,255,.87)'
                }
            }
        }
    }
});
const pinia = createPinia()
pinia.use(piniaPluginPersistedstate);

createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob('./pages/**/*.vue', {eager: true})
        let page = pages[`./pages/${name}.vue`]
        page.default.layout = DefaultLayout;
        return pages[`./pages/${name}.vue`]
    },
    setup({el, App, props, plugin}) {
        createApp({render: () => h(App, props)})
            .use(plugin)
            .use(pinia)
            .use(ToastService)
            .use(ConfirmationService)
            .use(ZiggyVue, Ziggy)
            .use(PrimeVue, {
                theme: {
                    preset: definePreset(Noir, {
                        cssLayer: {
                            name: 'primevue',
                            order: 'tailwind-base, primevue, tailwind-utilities'
                        }
                    })
                },
                pt: {
                    datatable: {
                        header: {
                            style: {
                                paddingLeft: '0',
                                paddingRight: '0'
                            }
                        }
                    }
                }
            })
            .component('Button', Button)
            .component('Checkbox', Checkbox)
            .component('Image', Image)
            .component('InputNumber', InputNumber)
            .component('IconField', IconField)
            .component('InputIcon', InputIcon)
            .component('InputText', InputText)
            .component('Paginator', Paginator)
            .component('FloatLabel', FloatLabel)
            .component('Menubar', Menubar)
            .component('MultiSelect', MultiSelect)
            .component('Message', Message)
            .component('Form', Form)
            .component('DeferredContent', DeferredContent)
            .component('Select', Select)
            .component('Badge', Badge)
            .component('Password', Password)
            .component('Card', Card)
            .component('Toast', Toast)
            .component('DataTable', DataTable)
            .component('Column', Column)
            .component('PickList', PickList)
            .component('ConfirmPopup', ConfirmPopup)
            .directive('tooltip', Tooltip)
            .directive('ripple', Ripple)
            .mount(el)
    },
});
