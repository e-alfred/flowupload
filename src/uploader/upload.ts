import { getMaxChunksSize } from './utils/config'
import type { AxiosResponse } from 'axios'

export enum Status {
	INITIALIZED = 0,
	UPLOADING = 1,
	ASSEMBLING = 2,
	FINISHED = 3,
	CANCELLED = 4,
	FAILED = 5,
}
export class Upload {

	private _path: string
	private _isChunked: boolean
	private _chunks: number

	private _size: number
	private _uploaded: number = 0
	private _startTime: number = 0

	private _status: Status = Status.INITIALIZED
	private _controller: AbortController
	private _response: AxiosResponse|null = null

	constructor(path: string, chunked: boolean = false, size: number) {
		const chunks = getMaxChunksSize() > 0 ? Math.ceil(size / getMaxChunksSize()) : 1
		this._path = path
		this._isChunked = chunked && getMaxChunksSize() > 0 && chunks > 1
		this._chunks = this._isChunked ? chunks : 1
		this._size = size
		this._controller = new AbortController()
	}

	get path(): string {
		return this._path
	}

	get isChunked(): boolean {
		return this._isChunked
	}

	get chunks(): number {
		return this._chunks
	}

	get size(): number {
		return this._size
	}

	get uploaded(): number {
		return this._uploaded
	}

	get startTime(): number {
		return this._startTime
	}

	set response(response: AxiosResponse|null) {
		this._response = response
	}


	get response(): AxiosResponse|null {
		return this._response
	}

	/**
	 * Update the uploaded bytes of this upload
	 */
	set uploaded(length: number) {
		if (length >= this._size) {
			this._status = this._isChunked
				? Status.ASSEMBLING
				: Status.FINISHED
			this._uploaded = this._size
			return
		}

		this._status = Status.UPLOADING
		this._uploaded = length

		// If first progress, let's log the start time
		if (this._startTime === 0) {
			this._startTime = new Date().getTime()
		}
	}

	get status(): number {
		return this._status
	}

	/**
	 * Update this upload status
	 */
	set status(status: Status) {
		this._status = status
	}

	/**
	 * Returns the axios cancel token source
	 */
	get signal(): AbortSignal {
		return this._controller.signal
	}

	/**
	 * Cancel any ongoing requests linked to this upload
	 */
	cancel() {
		this._controller.abort()
		this._status = Status.CANCELLED
	}

}
