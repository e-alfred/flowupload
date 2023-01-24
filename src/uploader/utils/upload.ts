import type { AxiosResponse } from 'axios'
import { generateRemoteUrl } from '@nextcloud/router'
import { getCurrentUser } from '@nextcloud/auth'
import axios from '@nextcloud/axios'
import crypto from 'crypto-browserify'
import PLimit from 'p-limit'

const readerLimit = PLimit(1)
const reader = new FileReader()

/**
 * Upload some data to a given path
 */
export const uploadData = async function(url: string, data: Blob | (() => Promise<Blob>), signal: AbortSignal, onUploadProgress = () => {}): Promise<AxiosResponse> {
	if (typeof data === 'function') {
		data = await data()
	}

	return await axios.request({
		method: 'PUT',
		url,
		data,
		signal,
		onUploadProgress,
	})
}

/**
 * Get chunk of the file. Doing this on the fly
 * give us a big performance boost and proper
 * garbage collection
 */
export const getChunk = function(file: File, start: number, length: number): Promise<Blob> {
	if (!file.type) {
		return Promise.reject(new Error('Unknown file type'))
	}

	// Since we use a global FileReader, we need to only read one chunk at a time
	return readerLimit(() => new Promise((resolve, reject) => {
		reader.onload = () => {
			if (reader.result !== null) {
				resolve(new Blob([reader.result], {
					type: 'application/octet-stream',
				}))
			}
			reject(new Error('Error while reading the file'))
		}
		reader.readAsArrayBuffer(file.slice(start, start + length))
	}))
}

/**
 * Create a temporary upload workspace to upload the chunks to
 */
export const initChunkWorkspace = async function(): Promise<string> {
	const chunksWorkspace = generateRemoteUrl(`dav/uploads/${getCurrentUser()?.uid}`)
	const tempWorkspace = `web-file-upload-${crypto.randomBytes(16).toString('hex')}`
	const url = `${chunksWorkspace}/${tempWorkspace}`

	await axios.request({
		method: 'MKCOL',
		url,
	})

	return url
}
