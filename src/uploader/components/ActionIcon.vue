<script>
import { sanitizeSVG } from '@skjnldsv/sanitize-svg'

export default {
	props: {
		svg: {
			type: String,
			default: '',
		},
	},

	data() {
		return {
			cleanSvg: '',
		}
	},

	beforeMount() {
		this.sanitizeSVG()
	},

	methods: {
		async sanitizeSVG() {
			if (!this.svg) {
				return
			}
			this.cleanSvg = await sanitizeSVG(this.svg)
		},
	},

	render(createElement) {
		if (!this.cleanSvg) {
			return
		}

		return createElement('span', {
			class: 'upload-picker__menu-icon',
			domProps: {
				innerHTML: this.cleanSvg,
			},
		})
	},
}
</script>

<style lang="css" scoped>
.upload-picker__menu-icon {
	display: flex;
	justify-content: center;
	align-items: center;
	width: 44px;
	height: 44px;
	opacity: 1;
}
.upload-picker__menu-icon svg {
	fill: currentColor;
	max-width: 20px;
	max-height: 20px;
}
</style>
