<template>
	<form ref="form"
		:class="{'upload-picker--uploading': isUploading, 'upload-picker--paused': isPaused}"
		class="upload-picker"
		data-upload-picker>
		<!-- New button -->
		<NcButton v-if="newFileMenuEntries.length === 0"
			:disabled="disabled"
			data-upload-picker-add
			@click="onClick">
			<template #icon>
				<Plus title="" :size="20" decorative />
			</template>
			{{ addLabel }}
		</NcButton>

		<!-- New file menu -->
		<NcActions v-else :menu-title="addLabel">
			<template #icon>
				<Plus title="" :size="20" decorative />
			</template>
			<NcActionButton data-upload-picker-add @click="onClick">
				<template #icon>
					<Upload title="" :size="20" decorative />
				</template>
				{{ uploadLabel }}
			</NcActionButton>

			<!-- Custom new file entries -->
			<NcActionButton v-for="entry in newFileMenuEntries"
				:key="entry.id"
				:icon="entry.iconClass"
				class="upload-picker__menu-entry"
				@click="entry.handler">
				<template #icon>
					<ActionIcon :svg="entry.iconSvgInline" />
				</template>
				{{ entry.displayName }}
			</NcActionButton>
		</NcActions>

		<!-- Progressbar and status, hidden by css -->
		<div class="upload-picker__progress">
			<NcProgressBar :error="hasFailure"
				:value="progress"
				size="medium" />
			<p>{{ timeLeft }}</p>
		</div>

		<!-- Cancel upload button -->
		<NcButton v-if="isUploading"
			class="upload-picker__cancel"
			type="tertiary"
			:aria-label="cancelLabel"
			data-upload-picker-cancel
			@click="onCancel">
			<template #icon>
				<Cancel title=""
					:size="20" />
			</template>
		</NcButton>

		<!-- Hidden files picker input -->
		<input v-show="false"
			ref="input"
			type="file"
			:accept="accept"
			:multiple="multiple"
			data-upload-picker-input
			@change="onPick">
	</form>
</template>

<script>
import { getNewFileMenuEntries } from '@nextcloud/files'
import { getUploader } from '../index.ts'
import makeEta from 'simple-eta'

import NcActionButton from '@nextcloud/vue/dist/Components/NcActionButton.js'
import NcActions from '@nextcloud/vue/dist/Components/NcActions.js'
import NcButton from '@nextcloud/vue/dist/Components/NcButton.js'
import NcProgressBar from '@nextcloud/vue/dist/Components/NcProgressBar.js'

import Cancel from 'vue-material-design-icons/Cancel.vue'
import Plus from 'vue-material-design-icons/Plus.vue'
import Upload from 'vue-material-design-icons/Upload.vue'

import { Status as UploadStatus } from '../upload.ts'
import { t } from '../utils/l10n.ts'
import { Uploader, Status } from '../uploader.ts'
import ActionIcon from './ActionIcon.vue'
import logger from '../utils/logger.ts'

/** @type {Uploader} */
const uploadManager = getUploader()

export default {
	name: 'UploadPicker',
	components: {
		NcActionButton,
		ActionIcon,
		NcActions,
		NcButton,
		Cancel,
		Plus,
		NcProgressBar,
		Upload,
	},

	props: {
		accept: {
			type: Array,
			default: null,
		},
		disabled: {
			type: Boolean,
			default: false,
		},
		multiple: {
			type: Boolean,
			default: false,
		},
		destination: {
			type: String,
			default: null,
		},
		root: {
			type: String,
			default: null,
		},
		context: {
			type: Object,
			default: undefined,
		},
	},

	data() {
		return {
			addLabel: t('Add'),
			cancelLabel: t('Cancel uploads'),
			uploadLabel: t('Upload files'),

			eta: null,
			timeLeft: '',

			newFileMenuEntries: getNewFileMenuEntries(this.context),
			uploadManager,
		}
	},

	computed: {
		totalQueueSize() {
			return this.uploadManager.info?.size || 0
		},
		uploadedQueueSize() {
			return this.uploadManager.info?.progress || 0
		},
		progress() {
			return Math.round(this.uploadedQueueSize / this.totalQueueSize * 100) || 0
		},

		queue() {
			return this.uploadManager.queue
		},

		hasFailure() {
			return this.queue?.filter(upload => upload.status === UploadStatus.FAILED).length !== 0
		},
		isUploading() {
			return this.queue?.length > 0
		},
		isAssembling() {
			return this.queue?.filter(upload => upload.status === UploadStatus.ASSEMBLING).length !== 0
		},
		isPaused() {
			return this.uploadManager.info?.status === Status.PAUSED
		},
	},

	watch: {
		/**
		 * If the context change, we need to refresh the menu
		 *
		 * @param {FileInfo} context the current NewFileMenu context
		 */
		context(context) {
			this.setContext(context)
		},

		totalQueueSize(size) {
			this.eta = makeEta({ min: 0, max: size })
			this.updateStatus()
		},

		uploadedQueueSize(size) {
			this.eta.report(size)
			this.updateStatus()
		},

		destination(destination) {
			this.setDestination(destination)
		},

		root(path) {
			this.setRoot(path)
		},

		queue(queue, oldQueue) {
			if (queue.length < oldQueue.length) {
				this.$emit('uploaded', oldQueue.filter(upload => !queue.includes(upload)))
			}
		},

		hasFailure(hasFailure) {
			if (hasFailure) {
				this.$emit('failed', this.queue)
			}
		},

		isPaused(isPaused) {
			if (isPaused) {
				this.$emit('paused', this.queue)
			} else {
				this.$emit('resumed', this.queue)
			}
		},
	},

	beforeMount() {
		this.setDestination(this.destination)
		this.setRoot(this.root)

		this.setContext(this.context)
		logger.debug('UploadPicker initialised')
	},

	methods: {
		/**
		 * Trigger file picker
		 */
		onClick() {
			this.$refs.input.click()
		},

		/**
		 * Start uploading
		 */
		async onPick() {
			const files = [...this.$refs.input.files]
			files.forEach(file => {
				uploadManager.upload(file.name, file)
			})
			this.$refs.form.reset()
		},

		/**
		 * Cancel ongoing queue
		 */
		onCancel() {
			this.uploadManager.queue.forEach(upload => {
				upload.cancel()
			})
			this.$refs.form.reset()
		},

		updateStatus() {
			if (this.isPaused) {
				this.timeLeft = t('paused')
				return
			}

			const estimate = Math.round(this.eta.estimate())

			if (estimate === Infinity) {
				this.timeLeft = t('estimating time left')
				return
			}
			if (estimate < 10) {
				this.timeLeft = t('a few seconds left')
				return
			}
			if (estimate > 60) {
				const date = new Date(0)
				date.setSeconds(estimate)
				const time = date.toISOString().slice(11, 11 + 8)
				this.timeLeft = t('{time} left', { time }) // TRANSLATORS time has the format 00:00:00
				return
			}
			this.timeLeft = t('{seconds} seconds left', { seconds: estimate })
		},

		setDestination(destination) {
			logger.debug(`Destination path set to ${destination}`)
			this.uploadManager.destination = destination
		},

		setRoot(path) {
			logger.debug(`Root path set to ${path}`)
			this.uploadManager.root = path
		},

		setContext(context) {
			logger.debug('Context changed to', context)
			this.newFileMenuEntries = getNewFileMenuEntries(context)
		},
	},
}
</script>

<style lang="scss" scoped>
$progress-width: 200px;

.upload-picker {
	display: inline-flex;
	align-items: center;
	height: 44px;

	&__progress {
		width: $progress-width;
		// Animate show/hide
		max-width: 0;
		transition: max-width var(--animation-quick) ease-in-out;

		// Align progress/text separation with the middle
		margin-top: 8px;

		p {
			overflow: hidden;
			white-space: nowrap;
			text-overflow: ellipsis;
		}
	}

	&--uploading &__progress {
		max-width: $progress-width;

		// Visually more pleasing
		margin-right: 20px;
		margin-left: 8px;
	}

	&--paused &__progress {
		animation: breathing 3s ease-out infinite normal;
	}
}

@keyframes breathing {
	0% {
		opacity: .5;
	}
	25% {
		opacity: 1;
	}
	60% {
		opacity: .5;
	}
	100% {
		opacity: .5;
	}
}

</style>
