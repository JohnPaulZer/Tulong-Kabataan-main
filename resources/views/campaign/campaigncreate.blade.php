<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Create Campaign | Tulong Kabataan</title>
    <link rel="icon" href="{{ page_media_url('site_favicon', asset('img/log2.png')) }}" type="image/png" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body class="campaign-create-page">
    @php
        $campaignUploadMaxKb = max(1, (int) config('r2.validation.campaign_image_max_size_kb', 15360));
        $campaignUploadMaxMb = max(1, (int) ceil($campaignUploadMaxKb / 1024));
        $campaignCreateAssetVersion = fn (string $path) => file_exists(public_path($path)) ? filemtime(public_path($path)) : time();
        $selectedScheduleType = old('schedule_type', 'one_time');
        $selectedRecurringDays = old('recurring_days', []);
    @endphp

    @include('partials.universalmodal')
    @include('partials.main-header')
    @include('administrator.partials.loading-screen')

    <div class="tk-container">
        <div class="tk-back-wrap">
            <button type="button" class="tk-back" onclick="window.history.back()">
                <i class="ri-arrow-left-line"></i> Back
            </button>
        </div>

        <div class="tk-page-head">
            <h1 class="tk-title">Create a New Campaign</h1>
            <p class="tk-sub">Fill out the form to submit your campaign and reach donors. You can publish now or
                schedule the campaign.</p>

            <div class="tk-steps">
                <div class="tk-step is-active"><i class="ri-edit-2-line"></i> Basics</div>
                <div class="tk-step"><i class="ri-calendar-schedule-line"></i> Schedule</div>
                <div class="tk-step"><i class="ri-image-add-line"></i> Media</div>
                <div class="tk-step"><i class="ri-shield-check-line"></i> Options</div>
            </div>
        </div>

        <form class="campaign-form tk-form" action="{{ route('campaign.create') }}" method="POST"
            data-chunk-upload-form
            enctype="multipart/form-data">
            @csrf

            <!-- Basics -->
            <section class="tk-section">
                <div class="tk-sec-head">
                    <h3 class="tk-sec-title"><i class="ri-edit-2-line"></i> Campaign Basics</h3>
                    <p class="tk-sec-help">Start with a clear title, organizer, and fundraising goal.</p>
                </div>

                <div class="tk-field">
                    <label for="title" class="tk-label">Campaign Title</label>
                    <div class="tk-input-wrap">
                        <span class="tk-leading-icon"><i class="ri-flag-line"></i></span>
                        <input type="text" id="title" name="title" class="tk-input has-icon"
                            placeholder="Enter campaign title" maxlength="100" value="{{ old('title') }}" required>
                    </div>
                    <div class="tk-counter"><span id="titleCount">0</span>/100</div>
                </div>

                <div class="tk-grid-2">
                    <div class="tk-field">
                        <label for="campaign_organizer" class="tk-label">Campaign Organizer</label>
                        <div class="tk-input-wrap">
                            <span class="tk-leading-icon"><i class="ri-user-heart-line"></i></span>
                            <input type="text" id="campaign_organizer" name="campaign_organizer"
                                class="tk-input has-icon" placeholder="Enter organizer name"
                                value="{{ old('campaign_organizer') }}" required>
                        </div>
                    </div>

                    <div class="tk-field">
                        <label for="target_amount" class="tk-label">Goal Amount</label>
                        <div class="tk-input-wrap">
                            <span class="tk-leading-icon"><i class="ri-money-dollar-circle-line"></i></span>
                            <input type="number" id="target_amount" name="target_amount" class="tk-input has-icon"
                                placeholder="Enter fundraising goal" min="1" step="1"
                                value="{{ old('target_amount') }}" required>
                        </div>
                        <div class="tk-inline" id="goalHelper">Tip: set a realistic goal donors can help achieve.</div>
                    </div>
                </div>

                <div class="tk-field">
                    <label for="description" class="tk-label">Description</label>
                    <textarea id="description" name="description" class="tk-input tk-textarea"
                        placeholder="Describe your campaign in detail..." maxlength="2000" required>{{ old('description') }}</textarea>
                    <div class="tk-counter"><span id="descCount">0</span>/2000</div>
                </div>
            </section>

            <!-- Schedule -->
            <section class="tk-section">
                <div class="tk-sec-head">
                    <h3 class="tk-sec-title"><i class="ri-calendar-schedule-line"></i> Schedule</h3>
                    <p class="tk-sec-help">Publish immediately, pick a start/end, or create a recurring schedule.</p>
                </div>

                <div class="tk-field">
                    <label class="tk-label">Schedule Type</label>
                    <div class="tk-seg" role="tablist" aria-label="Schedule Type">
                        <button type="button" class="seg-opt {{ $selectedScheduleType === 'one_time' ? 'is-active' : '' }}"
                            data-value="one_time" role="tab"
                            aria-selected="{{ $selectedScheduleType === 'one_time' ? 'true' : 'false' }}">One-Time</button>
                        <button type="button" class="seg-opt {{ $selectedScheduleType === 'recurring' ? 'is-active' : '' }}"
                            data-value="recurring" role="tab"
                            aria-selected="{{ $selectedScheduleType === 'recurring' ? 'true' : 'false' }}">Recurring</button>
                    </div>
                    <select id="schedule_type" name="schedule_type" aria-hidden="true">
                        <option value="one_time" @selected($selectedScheduleType === 'one_time')>One-Time</option>
                        <option value="recurring" @selected($selectedScheduleType === 'recurring')>Recurring</option>
                    </select>
                </div>

                <div class="tk-grid-2" id="one_time_dates" style="{{ $selectedScheduleType === 'recurring' ? 'display:none;' : '' }}">
                    <div class="tk-field">
                        <label for="starts_at" class="tk-label">Start Date & Time (Optional)</label>
                        <div class="tk-input-wrap">
                            <span class="tk-leading-icon"><i class="ri-time-line"></i></span>
                            <input type="datetime-local" id="starts_at" name="starts_at" step="60"
                                class="tk-input has-icon" value="{{ old('starts_at') }}">
                        </div>
                        <div id="schedule_preview" class="tk-preview immediate" style="display:block;">
                            <span class="tk-badge blue"><i class="ri-broadcast-line"></i> Immediate</span>
                            <div class="tk-help" id="schedule_text">Campaign will be published immediately</div>
                        </div>
                    </div>

                    <div class="tk-field">
                        <label for="ends_at" class="tk-label">End Date & Time (Optional)</label>
                        <div class="tk-input-wrap">
                            <span class="tk-leading-icon"><i class="ri-stop-circle-line"></i></span>
                            <input type="datetime-local" id="ends_at" name="ends_at" step="60"
                                class="tk-input has-icon" value="{{ old('ends_at') }}">
                        </div>
                        <div class="tk-help">Set when the campaign should end (optional).</div>
                    </div>
                </div>

                <div class="tk-grid-2" id="recurring_days_container" style="{{ $selectedScheduleType === 'recurring' ? '' : 'display:none;' }}">
                    <div class="tk-field">
                        <label for="recurring_days" class="tk-label">Select Days</label>
                        <select id="recurring_days" name="recurring_days[]" class="tk-input" multiple>
                            <option value="mon" @selected(in_array('mon', $selectedRecurringDays, true))>Monday</option>
                            <option value="tue" @selected(in_array('tue', $selectedRecurringDays, true))>Tuesday</option>
                            <option value="wed" @selected(in_array('wed', $selectedRecurringDays, true))>Wednesday</option>
                            <option value="thu" @selected(in_array('thu', $selectedRecurringDays, true))>Thursday</option>
                            <option value="fri" @selected(in_array('fri', $selectedRecurringDays, true))>Friday</option>
                            <option value="sat" @selected(in_array('sat', $selectedRecurringDays, true))>Saturday</option>
                            <option value="sun" @selected(in_array('sun', $selectedRecurringDays, true))>Sunday</option>
                        </select>
                        <div class="tk-help">Hold ⌘/Ctrl to select multiple days.</div>
                    </div>

                    <div class="tk-field" id="recurring_time_container">
                        <label for="recurring_time" class="tk-label">Time</label>
                        <div class="tk-input-wrap">
                            <span class="tk-leading-icon"><i class="ri-time-line"></i></span>
                            <input type="time" id="recurring_time" name="recurring_time" step="60"
                                class="tk-input has-icon" value="{{ old('recurring_time') }}">
                        </div>
                        <div class="tk-help">Set the time when the campaign runs on selected days.</div>
                    </div>
                </div>
            </section>

            <!-- Payment QR Code (restyled to match Media section) -->
            <section class="tk-section">
                <div class="tk-sec-head">
                    <h3 class="tk-sec-title"><i class="ri-qr-code-line"></i> Payment QR Code</h3>

                </div>

                <div class="tk-grid-2">
                    <div class="tk-field">
                        <label for="gcash_number" class="tk-label">GCash Number</label>
                        <div class="tk-input-wrap">
                            <span class="tk-leading-icon"><i class="ri-phone-line"></i></span>
                            <input type="tel" id="gcash_number" name="gcash_number" class="tk-input has-icon"
                                placeholder="09XXXXXXXXX" inputmode="numeric" pattern="09[0-9]{9}" maxlength="11"
                                value="{{ old('gcash_number') }}" required>
                        </div>
                        <div class="tk-help">Enter your 11-digit GCash mobile number</div>
                    </div>

                    <div class="tk-field">
                        <label class="tk-label">Upload GCash QR Code</label>
                        <input type="file" id="qr_code" name="qr_code" accept="image/*"
                            data-chunk-module="campaign_qr" data-chunk-path-name="qr_code_uploaded_path"
                            style="position:absolute;left:-9999px">
                        @if (old('qr_code_uploaded_path'))
                            <input type="hidden" name="qr_code_uploaded_path" value="{{ old('qr_code_uploaded_path') }}"
                                data-chunk-generated data-source-input="qr_code">
                        @endif
                        <div class="tk-drop" id="qrDrop">
                            <button type="button" class="tk-drop-btn" id="qrPick">
                                <i class="ri-upload-2-line"></i> Choose QR Code
                            </button>
                            <small>JPG/PNG/WebP up to {{ $campaignUploadMaxMb }}MB. Clear, high-contrast QR recommended.</small>
                            <div class="tk-previews" id="qrPreview" style="{{ old('qr_code_uploaded_path') ? 'display:grid' : 'display:none' }}">
                                @if (old('qr_code_uploaded_path'))
                                    <div class="tk-thumb" data-restored-preview>
                                        <div class="tk-thumb-content">
                                            <img src="{{ file_url(old('qr_code_uploaded_path')) }}" alt="Uploaded QR code">
                                            <div class="tk-thumb-overlay">
                                                <div class="tk-file-info">
                                                    <i class="ri-qr-code-line"></i>
                                                    <span class="tk-file-name">Uploaded QR Code</span>
                                                    <span class="tk-file-size">Ready</span>
                                                </div>
                                            </div>
                                            <button type="button" class="tk-del" aria-label="Remove QR Code">
                                                <i class="ri-close-line"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Media -->
            <section class="tk-section">
                <div class="tk-sec-head">
                    <h3 class="tk-sec-title"><i class="ri-image-add-line"></i> Media</h3>
                    <p class="tk-sec-help">Add a compelling cover and supporting images.</p>
                </div>

                <div class="tk-grid-2">
                    <div class="tk-field">
                        <label class="tk-label">Cover Image</label>
                        <input type="file" id="featured_image" name="featured_image" accept="image/*"
                            data-chunk-module="campaign_featured" data-chunk-path-name="featured_image_uploaded_path"
                            style="position:absolute;left:-9999px">
                        @if (old('featured_image_uploaded_path'))
                            <input type="hidden" name="featured_image_uploaded_path"
                                value="{{ old('featured_image_uploaded_path') }}" data-chunk-generated
                                data-source-input="featured_image">
                        @endif
                        <div class="tk-drop" id="coverDrop">
                            <button type="button" class="tk-drop-btn" id="coverPick"><i
                                    class="ri-upload-2-line"></i>
                                Choose Cover</button>
                            <small>JPG/PNG/WebP up to {{ $campaignUploadMaxMb }}MB. Best size 1200×630.</small>
                            <div class="tk-previews" id="coverPreview" style="{{ old('featured_image_uploaded_path') ? 'display:grid' : 'display:none' }}">
                                @if (old('featured_image_uploaded_path'))
                                    <div class="tk-thumb" data-restored-preview>
                                        <div class="tk-thumb-content">
                                            <img src="{{ file_url(old('featured_image_uploaded_path')) }}" alt="Uploaded cover image">
                                            <div class="tk-thumb-overlay">
                                                <div class="tk-file-info">
                                                    <i class="ri-image-line"></i>
                                                    <span class="tk-file-name">Uploaded Cover</span>
                                                    <span class="tk-file-size">Ready</span>
                                                </div>
                                            </div>
                                            <button type="button" class="tk-del" aria-label="Remove Cover">
                                                <i class="ri-close-line"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="tk-field">
                        <label class="tk-label">Additional Images</label>
                        <input type="file" id="images" name="images[]" accept="image/*" multiple
                            data-chunk-module="campaign_image" data-chunk-path-name="images_uploaded_paths"
                            style="position:absolute;left:-9999px">
                        @foreach ((array) old('images_uploaded_paths', []) as $uploadedImagePath)
                            <input type="hidden" name="images_uploaded_paths[]" value="{{ $uploadedImagePath }}"
                                data-chunk-generated data-source-input="images">
                        @endforeach
                        <div class="tk-drop" id="galleryDrop">
                            <button type="button" class="tk-drop-btn" id="galleryPick"><i
                                    class="ri-image-add-line"></i>
                                Add Images</button>
                            <small>Up to 10 images, {{ $campaignUploadMaxMb }}MB each. You can drag &amp; drop here.</small>
                            <div class="tk-previews" id="galleryPreview" style="{{ count((array) old('images_uploaded_paths', [])) ? 'display:grid' : '' }}">
                                @foreach ((array) old('images_uploaded_paths', []) as $uploadedImagePath)
                                    <div class="tk-thumb" data-restored-preview>
                                        <div class="tk-thumb-content">
                                            <img src="{{ file_url($uploadedImagePath) }}" alt="Uploaded campaign image">
                                            <div class="tk-thumb-overlay">
                                                <div class="tk-file-info">
                                                    <i class="ri-image-line"></i>
                                                    <span class="tk-file-name">Uploaded Image</span>
                                                    <span class="tk-file-size">Ready</span>
                                                </div>
                                            </div>
                                            <button type="button" class="tk-del" aria-label="Remove Image">
                                                <i class="ri-close-line"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <div class="tk-submit">
                <button type="submit" class="tk-btn primary"><i class="ri-check-line"></i> Submit Campaign</button>
                <button type="button" class="tk-btn ghost" onclick="window.history.back()"><i
                        class="ri-arrow-go-back-line"></i> Cancel</button>
            </div>
        </form>
    </div>


    @include('partials.main-footer')

    <script>
        window.TKCampaignUploadConfig = {
            maxImageSizeMb: @json($campaignUploadMaxMb)
        };
        window.TKChunkUploadConfig = {
            ...(window.TKChunkUploadConfig || {}),
            maxSizeMb: @json($campaignUploadMaxMb),
            allowedExtensions: ["jpg", "jpeg", "png", "webp"],
            allowedMimeTypes: ["image/jpeg", "image/png", "image/webp"]
        };
    </script>

    <script src="{{ asset('js/campaigncreate/form-ux-enhancements.js') }}?v={{ $campaignCreateAssetVersion('js/campaigncreate/form-ux-enhancements.js') }}"></script>
    <script src="{{ asset('js/chunk-upload.js') }}?v={{ $campaignCreateAssetVersion('js/chunk-upload.js') }}"></script>
    <script src="{{ asset('js/campaigncreate/dropzone-manager.js') }}?v={{ $campaignCreateAssetVersion('js/campaigncreate/dropzone-manager.js') }}"></script>
    <script src="{{ asset('js/campaigncreate/image-upload-validation.js') }}?v={{ $campaignCreateAssetVersion('js/campaigncreate/image-upload-validation.js') }}"></script>
    <script src="{{ asset('js/campaigncreate/qr-code-cropper.js') }}?v={{ $campaignCreateAssetVersion('js/campaigncreate/qr-code-cropper.js') }}"></script>

</body>

</html>
