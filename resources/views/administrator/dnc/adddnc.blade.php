<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tulong Kabataan | {{ isset($dnc) ? 'Edit DNC Record' : 'Add DNC Record' }}</title>
    <link rel="icon" href="{{ asset('img/log2.png') }}" type="image/png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css" />
    <style>
        :root {
            --primary: #3b82f6;
            --primary-dark: #2563eb;
            --bg: #f9fafb;
            --text: #111827;
            --muted: #6b7280;
            --muted-700: #374151;
            --border: #e5e7eb;
            --card: #ffffff;
            --shadow: 0 4px 12px rgba(0, 0, 0, .08);
            --radius: 14px;
            --header-h: 64px;
            --sidebar-w: 256px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Inter', system-ui, Arial, sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.5;
        }

        .main {
            margin-top: var(--header-h);
            margin-left: 0;
            padding: 32px 16px;
            min-height: calc(100vh - var(--header-h));
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        .container {
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
        }

        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 28px;
            box-shadow: var(--shadow);
        }

        .page-header {
            text-align: center;
            margin-bottom: 24px;
        }

        .page-header h1 {
            margin: 0;
            font-size: 32px;
            font-weight: 700;
            color: var(--primary-dark);
        }

        .page-header .muted {
            color: var(--muted);
            margin-top: 6px;
            font-size: 15px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 15px;
            border: 1px solid transparent;
            cursor: pointer;
            transition: all .2s ease;
        }

        .btn.primary {
            background: var(--primary);
            color: #fff;
        }

        .btn.primary:hover {
            background: var(--primary-dark);
        }

        .btn.outline {
            background: #fff;
            border-color: var(--border);
            color: var(--muted-700);
        }

        .btn.outline:hover {
            background: #f3f4f6;
        }

        .btn.ghost {
            background: transparent;
            color: var(--muted-700);
        }

        .btn.ghost:hover {
            background: #f3f4f6;
            border-radius: 8px;
        }

        .btn[disabled] {
            opacity: .6;
            cursor: not-allowed;
        }

        .form-input {
            width: 100%;
            margin-top: 6px;
            padding: 12px;
            border: 1px solid var(--border);
            border-radius: 10px;
            font-size: 15px;
            background: #fff;
            transition: border-color .2s ease, box-shadow .2s ease;
        }

        .form-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, .2);
            outline: none;
        }

        textarea.form-input {
            resize: vertical;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .grid-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }

        .grid-auto {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 12px;
        }

        .field {
            display: flex;
            flex-direction: column;
            font-size: 15px;
        }

        .section-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--primary-dark);
            margin: 20px 0 12px;
        }

        .hr {
            height: 1px;
            background: var(--border);
            margin: 20px 0;
        }

        .note {
            color: var(--muted);
            font-size: 14px;
        }

        @media(max-width:900px) {
            .grid-2 {
                grid-template-columns: 1fr;
            }

            .grid-3 {
                grid-template-columns: 1fr;
            }

            .main {
                padding: 16px;
            }

            .card {
                padding: 20px;
            }
        }

        < !-- Add this CSS to your existing style tag -->.image-upload-area {
            transition: all 0.3s ease;
        }

        .image-upload-area:hover {
            border-color: #3b82f6 !important;
            background: #f0f9ff !important;
        }

        .image-preview-item img {
            transition: transform 0.2s ease;
        }

        .image-preview-item img:hover {
            transform: scale(1.05);
        }

        .remove-image,
        .delete-image {
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .image-preview-item:hover .remove-image,
        .image-preview-item:hover .delete-image {
            opacity: 1;
        }
    </style>
</head>

<body>
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <main class="main">
        <div class="container">
            <!-- Header -->
            <section class="page-header" style="margin-bottom:14px">
                <h1>{{ isset($dnc) ? 'Edit DNC Record' : 'Add DNC Record' }}</h1>
                <p class="muted">Damage, Needs, and Capacities assessment</p>
            </section>

            <!-- Form -->
            <section class="card">
                <form id="dncForm"
                    action="{{ isset($dnc) ? route('dnc.update', $dnc->dnc_id) : route('dnc.submit') }}" method="POST">
                    @csrf
                    @if (isset($dnc))
                        @method('PUT')
                    @endif

                    <!-- A. General -->
                    <div>
                        <div class="section-title">A. General Information</div>
                        <div class="grid-2">
                            <label class="field">Date of Assessment
                                <input type="date" name="date" class="form-input" required
                                    value="{{ old('date', isset($dnc) ? $dnc->date->format('Y-m-d') : now()->format('Y-m-d')) }}">
                            </label>
                            <label class="field">Assessor / Team
                                <input type="text" name="assessor" class="form-input"
                                    value="{{ old('assessor', $dnc->assessor ?? '') }}">
                            </label>
                            <label class="field">Disaster / Event
                                <input type="text" name="event" class="form-input" required
                                    value="{{ old('event', $dnc->event ?? '') }}">
                            </label>
                            <label class="field">Province
                                <input type="text" name="province" class="form-input" required
                                    value="{{ old('province', $dnc->province ?? '') }}">
                            </label>
                            <label class="field">Municipality
                                <input type="text" name="municipality" class="form-input" required
                                    value="{{ old('municipality', $dnc->municipality ?? '') }}">
                            </label>
                            <label class="field">Barangay / Sitio / Evacuation Center
                                <input type="text" name="barangay" class="form-input" required
                                    value="{{ old('barangay', $dnc->barangay ?? '') }}">
                            </label>
                            <label class="field">Total Households Affected
                                <input type="number" min="0" name="households" class="form-input"
                                    value="{{ old('households', $dnc->households ?? '') }}">
                            </label>
                            <label class="field">Total Individuals
                                <input type="number" min="0" name="individuals" class="form-input"
                                    value="{{ old('individuals', $dnc->individuals ?? '') }}">
                            </label>
                        </div>

                        <div class="hr"></div>
                        <div class="section-title" style="margin-top:0">Population Breakdown (optional)</div>
                        <div class="grid-3">
                            <label class="field">Male
                                <input type="number" min="0" name="pop_male" class="form-input"
                                    value="{{ old('pop_male', $dnc->pop_male ?? '') }}">
                            </label>
                            <label class="field">Female
                                <input type="number" min="0" name="pop_female" class="form-input"
                                    value="{{ old('pop_female', $dnc->pop_female ?? '') }}">
                            </label>
                            <label class="field">Children
                                <input type="number" min="0" name="pop_children" class="form-input"
                                    value="{{ old('pop_children', $dnc->pop_children ?? '') }}">
                            </label>
                            <label class="field">Elderly
                                <input type="number" min="0" name="pop_elderly" class="form-input"
                                    value="{{ old('pop_elderly', $dnc->pop_elderly ?? '') }}">
                            </label>
                            <label class="field">PWDs
                                <input type="number" min="0" name="pop_pwds" class="form-input"
                                    value="{{ old('pop_pwds', $dnc->pop_pwds ?? '') }}">
                            </label>
                        </div>
                    </div>

                    <!-- B. Damage -->
                    <div class="hr"></div>
                    <div>
                        <div class="section-title">B. Damage</div>
                        <div class="grid-2">
                            <label class="field">Houses Fully Damaged
                                <input type="number" min="0" name="houses_full" class="form-input"
                                    value="{{ old('houses_full', $dnc->houses_full ?? '') }}">
                            </label>
                            <label class="field">Houses Partially Damaged
                                <input type="number" min="0" name="houses_partial" class="form-input"
                                    value="{{ old('houses_partial', $dnc->houses_partial ?? '') }}">
                            </label>
                        </div>

                        <div class="hr"></div>
                        <div class="field" style="font-weight:700;">Infrastructure Affected</div>
                        <div class="grid-auto" style="margin-top:8px">
                            @php $infra = old('infrastructure',$dnc->infrastructure ?? []); @endphp
                            <label><input type="checkbox" name="infrastructure[]" value="Roads"
                                    {{ in_array('Roads', $infra) ? 'checked' : '' }}> Roads</label>
                            <label><input type="checkbox" name="infrastructure[]" value="Bridges"
                                    {{ in_array('Bridges', $infra) ? 'checked' : '' }}> Bridges</label>
                            <label><input type="checkbox" name="infrastructure[]" value="Schools"
                                    {{ in_array('Schools', $infra) ? 'checked' : '' }}> Schools</label>
                            <label><input type="checkbox" name="infrastructure[]" value="Health Centers"
                                    {{ in_array('Health Centers', $infra) ? 'checked' : '' }}> Health Centers</label>
                            <label><input type="checkbox" name="infrastructure[]" value="Water/Electric Lines"
                                    {{ in_array('Water/Electric Lines', $infra) ? 'checked' : '' }}> Water/Electric
                                Lines</label>
                        </div>

                        <div class="hr"></div>
                        <div class="section-title">Livelihood</div>
                        <div class="grid-3">
                            <label class="field">Crop losses (type)
                                <input type="text" name="crop_type" class="form-input"
                                    value="{{ old('crop_type', $dnc->crop_type ?? '') }}">
                            </label>
                            <label class="field">Crop losses (hectares)
                                <input type="number" step="0.01" min="0" name="crop_hectares"
                                    class="form-input" value="{{ old('crop_hectares', $dnc->crop_hectares ?? '') }}">
                            </label>
                            <label class="field">Livestock lost (type)
                                <input type="text" name="livestock_type" class="form-input"
                                    value="{{ old('livestock_type', $dnc->livestock_type ?? '') }}">
                            </label>
                            <label class="field">Livestock lost (number)
                                <input type="number" min="0" name="livestock_number" class="form-input"
                                    value="{{ old('livestock_number', $dnc->livestock_number ?? '') }}">
                            </label>
                            <label class="field">Tools/Equipment destroyed
                                <input type="text" name="tools_destroyed" class="form-input"
                                    value="{{ old('tools_destroyed', $dnc->tools_destroyed ?? '') }}">
                            </label>
                        </div>

                        <div class="hr"></div>
                        <div class="section-title">Community Facilities Affected</div>
                        <div class="grid-2">
                            <label class="field">Facilities affected?
                                <select name="facilities_affected" class="form-input">
                                    <option value="">Select…</option>
                                    <option value="Yes"
                                        {{ old('facilities_affected', $dnc->facilities_affected ?? '') == 'Yes' ? 'selected' : '' }}>
                                        Yes</option>
                                    <option value="No"
                                        {{ old('facilities_affected', $dnc->facilities_affected ?? '') == 'No' ? 'selected' : '' }}>
                                        No</option>
                                </select>
                            </label>
                            <label class="field">Notes
                                <textarea name="facilities_notes" rows="2" class="form-input">{{ old('facilities_notes', $dnc->facilities_notes ?? '') }}</textarea>
                            </label>
                        </div>
                    </div>

                    <!-- C. Needs -->
                    <div class="hr"></div>
                    <div>
                        <div class="section-title">C. Needs</div>
                        @php $needs = old('needs',$dnc->needs ?? []); @endphp
                        <div class="field" style="font-weight:700;">Basic Needs</div>
                        <div class="grid-auto">
                            <label><input type="checkbox" name="needs[]" value="Food"
                                    {{ in_array('Food', $needs) ? 'checked' : '' }}> Food</label>
                            <label><input type="checkbox" name="needs[]" value="Drinking Water"
                                    {{ in_array('Drinking Water', $needs) ? 'checked' : '' }}> Drinking Water</label>
                            <label><input type="checkbox" name="needs[]" value="Shelter Materials"
                                    {{ in_array('Shelter Materials', $needs) ? 'checked' : '' }}> Shelter
                                Materials</label>
                            <label><input type="checkbox" name="needs[]" value="Clothing/Hygiene Kits"
                                    {{ in_array('Clothing/Hygiene Kits', $needs) ? 'checked' : '' }}> Clothing/Hygiene
                                Kits</label>
                        </div>

                        <div class="hr"></div>
                        <div class="field" style="font-weight:700;">Health Needs</div>
                        <div class="grid-auto">
                            <label><input type="checkbox" name="needs[]" value="Medicines"
                                    {{ in_array('Medicines', $needs) ? 'checked' : '' }}> Medicines</label>
                            <label><input type="checkbox" name="needs[]" value="Medical Support"
                                    {{ in_array('Medical Support', $needs) ? 'checked' : '' }}> Medical Support</label>
                        </div>

                        <div class="hr"></div>
                        <div class="field" style="font-weight:700;">Protection Needs</div>
                        <div class="grid-auto">
                            <label><input type="checkbox" name="needs[]" value="Psychosocial Support"
                                    {{ in_array('Psychosocial Support', $needs) ? 'checked' : '' }}> Psychosocial
                                Support</label>
                            <label><input type="checkbox" name="needs[]" value="Child-Friendly Spaces"
                                    {{ in_array('Child-Friendly Spaces', $needs) ? 'checked' : '' }}> Child-Friendly
                                Spaces</label>
                            <label><input type="checkbox" name="needs[]" value="Dignity Kits"
                                    {{ in_array('Dignity Kits', $needs) ? 'checked' : '' }}> Dignity Kits</label>
                        </div>

                        <div class="hr"></div>
                        <div class="field" style="font-weight:700;">Livelihood Needs</div>
                        <div class="grid-auto">
                            <label><input type="checkbox" name="needs[]" value="Seeds & Farming Tools"
                                    {{ in_array('Seeds & Farming Tools', $needs) ? 'checked' : '' }}> Seeds & Farming
                                Tools</label>
                            <label><input type="checkbox" name="needs[]" value="Fishing Equipment"
                                    {{ in_array('Fishing Equipment', $needs) ? 'checked' : '' }}> Fishing
                                Equipment</label>
                            <label><input type="checkbox" name="needs[]" value="Cash-for-Work / Grants"
                                    {{ in_array('Cash-for-Work / Grants', $needs) ? 'checked' : '' }}> Cash-for-Work /
                                Grants</label>
                        </div>

                        <div class="hr"></div>
                        <label class="field">Other Needs / Notes
                            <textarea name="needs_other" rows="3" class="form-input">{{ old('needs_other', $dnc->needs_other ?? '') }}</textarea>
                        </label>
                    </div>

                    <!-- D. Capacities -->
                    <div class="hr"></div>
                    <div>
                        <div class="section-title">D. Capacities</div>
                        <div class="grid-2">
                            <label class="field">Community Groups Active
                                <input type="text" name="groups" class="form-input"
                                    value="{{ old('groups', $dnc->groups ?? '') }}">
                            </label>
                            <label class="field">Facilities Available
                                <input type="text" name="facilities" class="form-input"
                                    value="{{ old('facilities', $dnc->facilities ?? '') }}">
                            </label>
                            <label class="field">Skills / Human Resources
                                <input type="text" name="skills" class="form-input"
                                    value="{{ old('skills', $dnc->skills ?? '') }}">
                            </label>
                            <label class="field">Community-led Initiatives
                                <textarea name="initiatives" rows="2" class="form-input">{{ old('initiatives', $dnc->initiatives ?? '') }}</textarea>
                            </label>
                        </div>
                    </div>

                    <!-- E. Prioritization -->
                    <div class="hr"></div>
                    <div>
                        <div class="section-title">E. Prioritization</div>
                        <div class="grid-2">
                            <label class="field">Urgency Level
                                <select name="priority" class="form-input" required>
                                    <option value="">Select…</option>
                                    <option value="High"
                                        {{ old('priority', $dnc->priority ?? '') == 'High' ? 'selected' : '' }}>High
                                    </option>
                                    <option value="Medium"
                                        {{ old('priority', $dnc->priority ?? '') == 'Medium' ? 'selected' : '' }}>
                                        Medium
                                    </option>
                                    <option value="Low"
                                        {{ old('priority', $dnc->priority ?? '') == 'Low' ? 'selected' : '' }}>Low
                                    </option>
                                </select>
                            </label>
                            <label class="field">Suggested Local Solutions
                                <textarea name="solutions" rows="2" class="form-input">{{ old('solutions', $dnc->solutions ?? '') }}</textarea>
                            </label>
                        </div>


                        <div class="hr"></div>
                        <div class="grid-3">
                            <label class="field">Top Need #1
                                <input type="text" name="top_need_1" class="form-input" required
                                    value="{{ old('top_need_1', $dnc->top_need_1 ?? '') }}">
                            </label>
                            <label class="field">Top Need #2
                                <input type="text" name="top_need_2" class="form-input"
                                    value="{{ old('top_need_2', $dnc->top_need_2 ?? '') }}">
                            </label>
                            <label class="field">Top Need #3
                                <input type="text" name="top_need_3" class="form-input"
                                    value="{{ old('top_need_3', $dnc->top_need_3 ?? '') }}">
                            </label>
                        </div>
                    </div>

                    <!-- Controls -->
                    <div class="hr"></div>
                    <div style="display:flex;gap:10px;align-items:center;justify-content:space-between;flex-wrap:wrap">
                        <div style="display:flex;gap:10px">
                            <button type="submit" id="submitBtn" class="btn primary">
                                <i class="ri-save-3-line"></i> {{ isset($dnc) ? 'Update Record' : 'Save Record' }}
                            </button>
                            <a href="{{ route('dnc.view') }}" class="btn outline">Cancel</a>
                        </div>
                    </div>
                </form>
            </section>
        </div>
    </main>
</body>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const imageUpload = document.getElementById('imageUpload');
        const previewContainer = document.getElementById('imagePreviewContainer');
        const deletedImagesInput = document.getElementById('deletedImages');
        let deletedImages = [];
        let uploadedCount = 0;
        const maxImages = 10;

        // Handle existing image deletion
        document.querySelectorAll('.delete-image').forEach(button => {
            button.addEventListener('click', function() {
                const imagePath = this.getAttribute('data-image');
                deletedImages.push(imagePath);
                deletedImagesInput.value = JSON.stringify(deletedImages);
                this.parentElement.remove();
            });
        });

        // Handle new image upload
        imageUpload.addEventListener('change', function(e) {
            const files = Array.from(e.target.files);

            // Check total images
            const currentImages = previewContainer.querySelectorAll('.image-preview-item').length;
            if (currentImages + files.length > maxImages) {
                alert(
                    `Maximum ${maxImages} images allowed. You can only upload ${maxImages - currentImages} more.`
                );
                return;
            }

            files.forEach(file => {
                // Check file size (5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert(`File ${file.name} is too large. Maximum size is 5MB.`);
                    return;
                }

                // Check file type
                if (!file.type.match('image.*')) {
                    alert(`File ${file.name} is not an image.`);
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewItem = document.createElement('div');
                    previewItem.className = 'image-preview-item';
                    previewItem.style.cssText = 'position: relative;';

                    previewItem.innerHTML = `
                    <img src="${e.target.result}"
                         alt="Preview"
                         style="width: 100%; height: 120px; object-fit: cover; border-radius: 8px;">
                    <button type="button"
                            class="remove-image"
                            style="position: absolute; top: 5px; right: 5px; background: red; color: white; border: none; border-radius: 50%; width: 24px; height: 24px; cursor: pointer;">×</button>
                `;

                    previewContainer.appendChild(previewItem);

                    // Add remove functionality for new images
                    previewItem.querySelector('.remove-image').addEventListener('click',
                        function() {
                            previewItem.remove();
                            uploadedCount--;
                        });
                };
                reader.readAsDataURL(file);
                uploadedCount++;
            });

            // Reset file input to allow re-uploading same files
            imageUpload.value = '';
        });

        // Allow drag and drop
        const uploadArea = document.querySelector('.image-upload-area');

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            uploadArea.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, unhighlight, false);
        });

        function highlight() {
            uploadArea.style.borderColor = '#3b82f6';
            uploadArea.style.backgroundColor = '#f0f9ff';
        }

        function unhighlight() {
            uploadArea.style.borderColor = '#ddd';
            uploadArea.style.backgroundColor = '#fafafa';
        }

        uploadArea.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            imageUpload.files = files;

            // Trigger change event
            const event = new Event('change');
            imageUpload.dispatchEvent(event);
        }
    });
</script>



</html>
