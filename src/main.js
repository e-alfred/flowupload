import Vue from 'vue'
import Flow from '@flowjs/flow.js'
import { translate, translatePlural } from 'nextcloud-l10n'

import App from './App'

Vue.prototype.t = translate
Vue.prototype.n = translatePlural

export default new Vue({
	el: '#content',
	render: h => h(App),
})