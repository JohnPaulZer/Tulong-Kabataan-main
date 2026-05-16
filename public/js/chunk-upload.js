(function () {
    const DEFAULTS = {
        chunkSize: 3 * 1024 * 1024,
        maxParallel: 2,
        retries: 3,
        maxSizeMb: 25,
        allowedExtensions: ["jpg", "jpeg", "png", "webp", "pdf"],
        allowedMimeTypes: ["image/jpeg", "image/png", "image/webp", "application/pdf"],
        endpoints: {
            init: "/api/uploads/chunk/init",
            chunk: "/api/uploads/chunk",
            complete: "/api/uploads/chunk/complete",
            cancel: "/api/uploads/chunk/cancel/",
        },
    };

    const statusText = {
        preparing: "Preparing upload",
        uploading: "Uploading chunks",
        processing: "Processing file",
        complete: "Upload complete",
        failed: "Upload failed",
        cancelled: "Upload cancelled",
    };

    class ChunkUploader {
        constructor(options = {}) {
            this.options = { ...DEFAULTS, ...options };
            this.controller = null;
            this.uploadId = null;
        }

        cancel() {
            if (this.controller) this.controller.abort();
            if (this.uploadId) {
                fetch(`${this.options.endpoints.cancel}${encodeURIComponent(this.uploadId)}`, {
                    method: "DELETE",
                    headers: this.headers(),
                }).catch(() => {});
            }
        }

        async upload(file, module, callbacks = {}) {
            this.validate(file);
            this.controller = new AbortController();
            try {
                callbacks.onStatus?.("preparing", statusText.preparing);

                const totalChunks = Math.ceil(file.size / this.options.chunkSize);
                const init = await this.postJson(this.options.endpoints.init, {
                    fileName: file.name,
                    fileSize: file.size,
                    fileType: file.type,
                    totalChunks,
                    chunkSize: this.options.chunkSize,
                    module,
                });

                this.uploadId = init.uploadId;
                callbacks.onStatus?.("uploading", statusText.uploading);

                let uploadedChunks = 0;
                const queue = Array.from({ length: totalChunks }, (_, index) => index);
                const workerCount = Math.min(this.options.maxParallel, totalChunks);
                const workers = Array.from({ length: workerCount }, async () => {
                    while (queue.length > 0) {
                        const index = queue.shift();
                        await this.uploadChunk(file, module, index, totalChunks);
                        uploadedChunks += 1;
                        callbacks.onProgress?.(Math.round((uploadedChunks / totalChunks) * 100), uploadedChunks, totalChunks);
                    }
                });

                await Promise.all(workers);
                callbacks.onStatus?.("processing", statusText.processing);

                const completed = await this.postJson(this.options.endpoints.complete, {
                    uploadId: this.uploadId,
                    module,
                });

                callbacks.onProgress?.(100, totalChunks, totalChunks);
                callbacks.onStatus?.("complete", statusText.complete);

                return completed;
            } catch (error) {
                this.cancel();
                throw error;
            }
        }

        async uploadChunk(file, module, index, totalChunks) {
            const start = index * this.options.chunkSize;
            const end = Math.min(file.size, start + this.options.chunkSize);
            const blob = file.slice(start, end);

            for (let attempt = 1; attempt <= this.options.retries; attempt += 1) {
                try {
                    const formData = new FormData();
                    formData.append("uploadId", this.uploadId);
                    formData.append("fileName", file.name);
                    formData.append("fileSize", file.size);
                    formData.append("fileType", file.type);
                    formData.append("totalChunks", totalChunks);
                    formData.append("chunkIndex", index);
                    formData.append("chunkSize", blob.size);
                    formData.append("module", module);
                    formData.append("chunk", blob, `${index}.chunk`);

                    const response = await fetch(this.options.endpoints.chunk, {
                        method: "POST",
                        body: formData,
                        headers: this.headers(false),
                        signal: this.controller.signal,
                    });

                    if (!response.ok) throw new Error(await this.errorMessage(response));
                    const data = await response.json();
                    if (!data.success) throw new Error(data.message || "Chunk upload failed.");
                    return data;
                } catch (error) {
                    if (this.controller.signal.aborted) throw error;
                    if (attempt >= this.options.retries) throw error;
                    await wait(350 * attempt);
                }
            }
        }

        async postJson(url, payload) {
            const response = await fetch(url, {
                method: "POST",
                headers: this.headers(true),
                body: JSON.stringify(payload),
                signal: this.controller?.signal,
            });

            if (!response.ok) throw new Error(await this.errorMessage(response));
            const data = await response.json();
            if (!data.success) throw new Error(data.message || "Upload failed.");
            return data;
        }

        headers(json = true) {
            const headers = {
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-TOKEN": csrfToken(),
            };

            if (json) headers["Content-Type"] = "application/json";
            return headers;
        }

        validate(file) {
            const extension = (file.name.split(".").pop() || "").toLowerCase();
            const maxBytes = this.options.maxSizeMb * 1024 * 1024;

            if (!file || file.size <= 0) throw new Error("The selected file is empty.");
            if (file.size > maxBytes) throw new Error(`The selected file must be ${this.options.maxSizeMb}MB or smaller.`);
            if (!this.options.allowedExtensions.includes(extension)) throw new Error("This file extension is not allowed.");
            if (file.type && !this.options.allowedMimeTypes.includes(file.type)) throw new Error("This file type is not allowed.");
        }

        async errorMessage(response) {
            try {
                const data = await response.json();
                return data.message || "Upload failed.";
            } catch (_) {
                return "Upload failed.";
            }
        }
    }

    function csrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.content ||
            document.querySelector('input[name="_token"]')?.value ||
            "";
    }

    function wait(ms) {
        return new Promise((resolve) => setTimeout(resolve, ms));
    }

    function ensureProgress(input) {
        let box = input.closest(".tk-field, form")?.querySelector(`[data-chunk-progress-for="${input.id}"]`);
        if (box) return box;

        box = document.createElement("div");
        box.className = "chunk-upload-progress";
        box.dataset.chunkProgressFor = input.id;
        box.innerHTML = `
            <div class="chunk-upload-progress__row">
                <span data-chunk-status>Preparing upload</span>
                <button type="button" data-chunk-cancel>Cancel</button>
            </div>
            <div class="chunk-upload-progress__bar"><span data-chunk-bar></span></div>
        `;
        input.closest(".tk-field, label, form")?.appendChild(box);
        return box;
    }

    function setProgress(box, status, percent) {
        box.style.display = "block";
        const label = box.querySelector("[data-chunk-status]");
        const bar = box.querySelector("[data-chunk-bar]");
        if (label) label.textContent = status;
        if (bar) bar.style.width = `${Math.max(0, Math.min(100, percent || 0))}%`;
    }

    function hiddenInput(form, name, value) {
        const input = document.createElement("input");
        input.type = "hidden";
        input.name = name;
        input.value = value;
        input.dataset.chunkGenerated = "true";
        form.appendChild(input);
        return input;
    }

    function removeGeneratedInputsFor(input) {
        const form = input.form;
        if (!form) return;

        form.querySelectorAll(`[data-chunk-generated][data-source-input="${input.id}"]`).forEach((el) => el.remove());
    }

    function hiddenNameFor(input) {
        return input.dataset.chunkPathName || `${input.name.replace(/\[\]$/, "")}_uploaded_path`;
    }

    function hasGeneratedInputFor(input) {
        const form = input.form;
        if (!form) return false;

        return Boolean(form.querySelector(`[data-chunk-generated][data-source-input="${input.id}"][value]`));
    }

    function resetChunkState(input) {
        removeGeneratedInputsFor(input);
        input.disabled = false;
        input.dataset.chunkStatus = "";
        input.dataset.chunkError = "";
        input.dataset.chunkPromiseId = "";
        input._tkChunkUploadPromise = null;

        const progress = input.closest(".tk-field, form")?.querySelector(`[data-chunk-progress-for="${input.id}"]`);
        if (progress) progress.style.display = "none";
    }

    function showUploadModal(message, type = "error") {
        if (typeof window.showNotificationModal === "function") {
            window.showNotificationModal(message, type, type === "error" ? "Upload failed" : null);
            return;
        }

        if (type === "error") {
            alert(message);
        }
    }

    function appendUploadedPath(form, input, name, value) {
        const generated = hiddenInput(form, name, value);
        generated.dataset.sourceInput = input.id;
    }

    async function uploadInputFiles(form, input) {
        if (!input.files || input.files.length === 0 || !input.dataset.chunkModule) {
            return;
        }

        if (input.dataset.chunkStatus === "uploading" && input._tkChunkUploadPromise) {
            return input._tkChunkUploadPromise;
        }

        if (input.dataset.chunkStatus === "complete" && hasGeneratedInputFor(input)) {
            return;
        }

        removeGeneratedInputsFor(input);
        input.disabled = false;

        const files = Array.from(input.files || []);
        const moduleName = input.dataset.chunkModule;
        const hiddenName = hiddenNameFor(input);
        const multiple = input.multiple || input.name.endsWith("[]");
        const progress = ensureProgress(input);
        const submitters = Array.from(form.querySelectorAll('button[type="submit"], input[type="submit"]'));
        const originalLabels = new Map();
        let activeUploader = null;

        submitters.forEach((button) => {
            originalLabels.set(button, button.value || button.textContent);
            button.disabled = true;
        });

        const cancelButton = progress.querySelector("[data-chunk-cancel]");
        cancelButton?.replaceWith(cancelButton.cloneNode(true));
        progress.querySelector("[data-chunk-cancel]")?.addEventListener("click", () => {
            activeUploader?.cancel();
            input.dataset.chunkStatus = "cancelled";
            input.disabled = false;
            setProgress(progress, statusText.cancelled, 0);
            submitters.forEach((button) => button.disabled = false);
        }, { once: true });

        const uploadPromise = (async () => {
            input.dataset.chunkStatus = "uploading";
            input.dataset.chunkError = "";
            input.dataset.chunkPromiseId = `${Date.now()}-${Math.random()}`;
            const promiseId = input.dataset.chunkPromiseId;

            try {
                for (let index = 0; index < files.length; index += 1) {
                    activeUploader = new ChunkUploader(window.TKChunkUploadConfig || {});
                    const file = files[index];
                    const result = await activeUploader.upload(file, moduleName, {
                        onStatus: (_, label) => {
                            const filePart = files.length > 1 ? ` (${index + 1}/${files.length})` : "";
                            setProgress(progress, `${label}${filePart}`, index === 0 ? 0 : Math.round((index / files.length) * 100));
                        },
                        onProgress: (percent) => {
                            const overall = files.length > 1
                                ? Math.round(((index + percent / 100) / files.length) * 100)
                                : percent;
                            const filePart = files.length > 1 ? ` (${index + 1}/${files.length})` : "";
                            setProgress(progress, `Uploading chunks${filePart}`, overall);
                        },
                    });

                    appendUploadedPath(form, input, multiple ? `${hiddenName}[]` : hiddenName, result.path);
                }

                input.dataset.chunkStatus = "complete";
                input.disabled = true;
                setProgress(progress, statusText.complete, 100);
            } catch (error) {
                if (input.dataset.chunkPromiseId !== promiseId) return;
                input.dataset.chunkStatus = "failed";
                input.dataset.chunkError = error.message || statusText.failed;
                input.disabled = false;
                removeGeneratedInputsFor(input);
                setProgress(progress, input.dataset.chunkError, 0);
                showUploadModal(input.dataset.chunkError, "error");
                throw error;
            } finally {
                if (input.dataset.chunkPromiseId === promiseId) {
                    input._tkChunkUploadPromise = null;
                    submitters.forEach((button) => {
                        button.disabled = false;
                        if (button.value && originalLabels.has(button)) button.value = originalLabels.get(button);
                    });
                }
            }
        })();

        input._tkChunkUploadPromise = uploadPromise;
        return uploadPromise;
    }

    async function bindChunkedForm(form) {
        if (form.dataset.chunkUploadBound === "true") return;
        form.dataset.chunkUploadBound = "true";

        const chunkInputs = Array.from(form.querySelectorAll('input[type="file"][data-chunk-module]'));
        chunkInputs.forEach((input) => {
            const startUpload = () => {
                uploadInputFiles(form, input).catch(() => {});
            };

            input.addEventListener("change", startUpload);
            input.addEventListener("tk:files-selected", startUpload);
        });

        form.addEventListener("submit", async (event) => {
            if (form.dataset.chunkSubmitting === "true") return;

            const inputs = chunkInputs.filter((input) => input.files && input.files.length > 0);

            if (!inputs.length) return;

            const incomplete = inputs.filter((input) => input.dataset.chunkStatus !== "complete" || !hasGeneratedInputFor(input));
            if (!incomplete.length) return;

            event.preventDefault();
            event.stopImmediatePropagation();
            const submitters = Array.from(form.querySelectorAll('button[type="submit"], input[type="submit"]'));
            submitters.forEach((button) => button.disabled = true);

            try {
                await Promise.all(incomplete.map((input) => uploadInputFiles(form, input)));

                const failed = inputs.find((input) => input.dataset.chunkStatus === "failed");
                if (failed) {
                    throw new Error(failed.dataset.chunkError || statusText.failed);
                }

                form.dataset.chunkSubmitting = "true";
                form.requestSubmit();
            } catch (error) {
                inputs.forEach((input) => {
                    input.disabled = false;
                    const progress = ensureProgress(input);
                    setProgress(progress, error.message || statusText.failed, 0);
                });
                showUploadModal(error.message || statusText.failed, "error");
                submitters.forEach((button) => button.disabled = false);
            }
        }, true);
    }

    function injectStyles() {
        if (document.getElementById("chunk-upload-styles")) return;
        const style = document.createElement("style");
        style.id = "chunk-upload-styles";
        style.textContent = `
            .chunk-upload-progress{display:none;margin-top:10px;font-size:13px;color:#334155}
            .chunk-upload-progress__row{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:6px}
            .chunk-upload-progress__row button{border:0;background:transparent;color:#b91c1c;cursor:pointer;font:inherit;padding:2px 0}
            .chunk-upload-progress__bar{height:7px;background:#e2e8f0;border-radius:999px;overflow:hidden}
            .chunk-upload-progress__bar span{display:block;height:100%;width:0;background:#2563eb;transition:width .2s ease}
        `;
        document.head.appendChild(style);
    }

    window.TKChunkUploader = ChunkUploader;
    window.TKBindChunkedForm = bindChunkedForm;
    window.TKResetChunkInput = resetChunkState;

    document.addEventListener("DOMContentLoaded", () => {
        injectStyles();
        document.querySelectorAll("form[data-chunk-upload-form]").forEach(bindChunkedForm);
    });
})();
