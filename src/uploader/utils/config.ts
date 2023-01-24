export const getMaxChunksSize = function(): number {
	const maxChunkSize = global.OC?.appConfig?.files?.max_chunk_size
	if (maxChunkSize <= 0) {
		return 0
	}

	// If invalid return default
	if (!Number(maxChunkSize)) {
		return 10 * 1024 * 1024
	}

	return Number(maxChunkSize)
}
