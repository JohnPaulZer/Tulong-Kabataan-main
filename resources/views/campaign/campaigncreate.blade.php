<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Create Campaign | Tulong Kabataan</title>
    <link rel="icon" href="{{ asset('img/log2.png') }}" type="image/png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,300;0,400;0,700;0,900;1,400&family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/landingpage.css') }}?v=4">
    <link rel="stylesheet" href="{{ asset('css/campaign/campaigncreatepage.css') }}?v=4">

</head>

<body>

    @include('partials.main-header')
    @include('partials.universalmodal')
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
                            placeholder="Enter campaign title" maxlength="100" required>
                    </div>
                    <div class="tk-counter"><span id="titleCount">0</span>/100</div>
                </div>

                <div class="tk-grid-2">
                    <div class="tk-field">
                        <label for="campaign_organizer" class="tk-label">Campaign Organizer</label>
                        <div class="tk-input-wrap">
                            <span class="tk-leading-icon"><i class="ri-user-heart-line"></i></span>
                            <input type="text" id="campaign_organizer" name="campaign_organizer"
                                class="tk-input has-icon" placeholder="Enter organizer name" required>
                        </div>
                    </div>

                    <div class="tk-field">
                        <label for="target_amount" class="tk-label">Goal Amount</label>
                        <div class="tk-input-wrap">
                            <span class="tk-leading-icon"><i class="ri-money-dollar-circle-line"></i></span>
                            <input type="number" id="target_amount" name="target_amount" class="tk-input has-icon"
                                placeholder="Enter fundraising goal" min="0" step="1" required>
                        </div>
                        <div class="tk-inline" id="goalHelper">Tip: set a realistic goal donors can help achieve.</div>
                    </div>
                </div>

                <div class="tk-field">
                    <label for="description" class="tk-label">Description</label>
                    <textarea id="description" name="description" class="tk-input tk-textarea"
                        placeholder="Describe your campaign in detail..." maxlength="2000" required></textarea>
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
                        <button type="button" class="seg-opt is-active" data-value="one_time" role="tab"
                            aria-selected="true">One-Time</button>
                        <button type="button" class="seg-opt" data-value="recurring" role="tab"
                            aria-selected="false">Recurring</button>
                    </div>
                    <select id="schedule_type" name="schedule_type" aria-hidden="true">
                        <option value="one_time" selected>One-Time</option>
                        <option value="recurring">Recurring</option>
                    </select>
                </div>

                <div class="tk-grid-2" id="one_time_dates">
                    <div class="tk-field">
                        <label for="starts_at" class="tk-label">Start Date & Time (Optional)</label>
                        <div class="tk-input-wrap">
                            <span class="tk-leading-icon"><i class="ri-time-line"></i></span>
                            <input type="datetime-local" id="starts_at" name="starts_at" step="60"
                                class="tk-input has-icon">
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
                                class="tk-input has-icon">
                        </div>
                        <div class="tk-help">Set when the campaign should end (optional).</div>
                    </div>
                </div>

                <div class="tk-grid-2" id="recurring_days_container" style="display:none;">
                    <div class="tk-field">
                        <label for="recurring_days" class="tk-label">Select Days</label>
                        <select id="recurring_days" name="recurring_days[]" class="tk-input" multiple>
                            <option value="mon">Monday</option>
                            <option value="tue">Tuesday</option>
                            <option value="wed">Wednesday</option>
                            <option value="thu">Thursday</option>
                            <option value="fri">Friday</option>
                            <option value="sat">Saturday</option>
                            <option value="sun">Sunday</option>
                        </select>
                        <div class="tk-help">Hold ⌘/Ctrl to select multiple days.</div>
                    </div>

                    <div class="tk-field" id="recurring_time_container">
                        <label for="recurring_time" class="tk-label">Time</label>
                        <div class="tk-input-wrap">
                            <span class="tk-leading-icon"><i class="ri-time-line"></i></span>
                            <input type="time" id="recurring_time" name="recurring_time" step="60"
                                class="tk-input has-icon">
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
                                placeholder="09XX XXX XXXX" pattern="[0-9]{11}" maxlength="11" required>
                        </div>
                        <div class="tk-help">Enter your 11-digit GCash mobile number</div>
                    </div>

                    <div class="tk-field">
                        <label class="tk-label">Upload GCash QR Code</label>
                        <input type="file" id="qr_code" name="qr_code" accept="image/*"
                            style="position:absolute;left:-9999px">
                        <div class="tk-drop" id="qrDrop">
                            <button type="button" class="tk-drop-btn" id="qrPick">
                                <i class="ri-upload-2-line"></i> Choose QR Code
                            </button>
                            <small>JPG/PNG up to 5MB. Clear, high-contrast QR recommended.</small>
                            <div class="tk-previews" id="qrPreview" style="display:none"></div>
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
                            style="position:absolute;left:-9999px">
                        <div class="tk-drop" id="coverDrop">
                            <button type="button" class="tk-drop-btn" id="coverPick"><i
                                    class="ri-upload-2-line"></i>
                                Choose Cover</button>
                            <small>JPG/PNG up to 5MB. Best size 1200×630.</small>
                            <div class="tk-previews" id="coverPreview" style="display:none"></div>
                        </div>
                    </div>

                    <div class="tk-field">
                        <label class="tk-label">Additional Images</label>
                        <input type="file" id="images" name="images[]" accept="image/*" multiple
                            style="position:absolute;left:-9999px">
                        <div class="tk-drop" id="galleryDrop">
                            <button type="button" class="tk-drop-btn" id="galleryPick"><i
                                    class="ri-image-add-line"></i>
                                Add Images</button>
                            <small>Up to 10 images. You can drag &amp; drop here.</small>
                            <div class="tk-previews" id="galleryPreview"></div>
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


    <script src="{{ asset('js/campaigncreate/form-ux-enhancements.js') }}"></script>
    <script src="{{ asset('js/campaigncreate/dropzone-manager.js') }}"></script>
    <script src="{{ asset('js/campaigncreate/image-upload-validation.js') }}"></script>
    <script src="{{ asset('js/campaigncreate/qr-code-cropper.js') }}"></script>

</body>

</html>
