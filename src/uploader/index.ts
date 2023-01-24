import { Uploader } from './uploader'
import UploadPicker from './components/UploadPicker.js'
export { Status as UploaderStatus } from './uploader'
export { Upload, Status as UploadStatus } from './upload'

var _uploader: Uploader

/**
 * Get an Uploader instance
 */
export function getUploader(): Uploader {
	const isPublic = document.querySelector('input[name="isPublic"][value="1"]') !== null

	if (_uploader instanceof Uploader) {
		return _uploader
	}

	// Init uploader
	_uploader = new Uploader(isPublic)
	return _uploader
}

/**
 * Upload a file
 * This will init an Uploader instance if none exists.
 * You will be able to retrieve it with `getUploader`
 */
export function upload(destinationPath: string, file: File): Uploader {

	// Init uploader and start uploading
	const uploader = getUploader()
	uploader.upload(destinationPath, file)

	return uploader
}

/** UploadPicker vue component */
export { UploadPicker }
