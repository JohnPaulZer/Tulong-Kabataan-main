<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Events | Tulong Kabataan</title>
    <link rel="icon" href="img/log2.png" type="image/png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css" />
    <link
        href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,300;0,400;0,700;0,900;1,400&family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/landingpage.css') }}?v=4">
    <link rel="stylesheet" href="{{ asset('css/inkind/inkindmodal.css') }}">
</head>

<body>
    @include('partials.main-header')

    <!-- Hero Banner -->
    <section class="hero-banner">
        <div class="hero-content">
            <h1>Tulong Kabataan</h1>
            <p>Be part of our mission. Donate items today to support communities in need.</p>
        </div>
    </section>

    <!-- Donation Form -->
    <div class="form-container">
        <div class="form-header">
            <h2>Donate Your Items</h2>
            <p>Complete the form below to submit your in-kind donation.</p>
            <button type="button" id="addItemBtn" class="add-item-btn">
                <i class="ri-add-line"></i> Add Another Item
            </button>
        </div>

        <form class="donation-form" action="{{ route('inkind.donate') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Donor Information -->
            <div class="form-card">
                @if (!auth()->check())
                    <!-- Donor Info for Guests -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="donor_name">Full Name</label>
                            <input type="text" id="donor_name" name="donor_name" class="input-field">
                            <span class="input-note">Leave blank to donate anonymously</span>
                        </div>
                        <div class="form-group">
                            <label for="donor_email">Email*</label>
                            <input type="email" id="donor_email" name="donor_email" class="input-field"
                                placeholder="Your email address" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="donor_phone">Phone Number*</label>
                            <input type="tel" id="donor_phone" name="donor_phone" class="input-field"
                                placeholder="09XXXXXXXXX" pattern="^09\d{9}$" maxlength="11"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,11)">
                        </div>
                    </div>
                @else
                    <!-- Hidden inputs for registered users -->
                    <input type="hidden" name="donor_name"
                        value="{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}">
                    <input type="hidden" name="donor_email" value="{{ auth()->user()->email }}">
                    <input type="hidden" name="donor_phone" value="{{ auth()->user()->phone_number }}">
                @endif

                <!-- Hidden user_id for tracking -->
                <input type="hidden" name="user_id" value="{{ auth()->check() ? auth()->user()->user_id : '' }}">
            </div>

            <!-- Items Container -->
            <div id="itemsContainer">
                <!-- First Item (Default) -->
                <div class="form-card item-row" data-item-index="0">
                    <div class="item-header">
                        <h3>Item #1</h3>
                        <button type="button" class="remove-item-btn" style="display: none;">
                            <i class="ri-delete-bin-line"></i> Remove
                        </button>
                    </div>

                    <div class="form-group">
                        <label for="items[0][item_name]">Item Name*</label>
                        <input type="text" id="items[0][item_name]" name="items[0][item_name]" class="input-field"
                            placeholder="Item Name" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="items[0][category]">Item Category*</label>
                            <select id="items[0][category]" name="items[0][category]" class="input-field" required>
                                <option value="">Select a category</option>
                                <option value="Food & Groceries">Food & Groceries</option>
                                <option value="Clothing & Accessories">Clothing & Accessories</option>
                                <option value="Home Goods">Home Goods</option>
                                <option value="School Supplies">School Supplies</option>
                                <option value="Medical Supplies">Medical Supplies</option>
                                <option value="Electronics">Electronics</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="items[0][quantity]">Quantity*</label>
                            <input type="number" id="items[0][quantity]" name="items[0][quantity]" class="input-field"
                                placeholder="Number of items" required min="1"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="items[0][description]">Item Description</label>
                        <textarea id="items[0][description]" name="items[0][description]" rows="3" class="input-field"
                            placeholder="Describe the item's condition, brand, size, etc."></textarea>
                    </div>
                </div>
            </div>

            <!-- Drop-off Location (Common for all items) -->
            <div class="form-card">
                <div class="form-group">
                    <label for="dropoff_id">Preferred Drop-off Location*</label>
                    <select id="dropoff_id" name="dropoff_id" class="input-field" required>
                        <option value="">Select a location</option>
                        @foreach ($locations as $location)
                            <option value="{{ $location->dropoff_id }}" {{ !$location->is_active ? 'disabled' : '' }}>
                                {{ $location->name }} - {{ $location->address }}
                                @if (!$location->is_active)
                                    (Currently unavailable)
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Availability -->
            <div class="location-availability">
                <span class="location-title">Location Availability</span>
                <ul>
                    @foreach ($locations as $location)
                        <li><span class="dot blue"></span> {{ $location->name }}:
                            {{ $location->schedule_datetime ?? 'Not specified' }}</li>
                    @endforeach
                </ul>
            </div>

            <!-- Confirmations -->
            <div class="checkbox-section">
                <div>
                    <input type="checkbox" id="condition-confirmation" required>
                    <label for="condition-confirmation">I confirm these items are in good, usable condition</label>
                </div>
                <div>
                    <input type="checkbox" id="review-confirmation" required>
                    <label for="review-confirmation">I understand that I will be the one to deliver the in-kind
                        donation to the selected drop-off point.</label>
                </div>
            </div>

            <!-- Submit -->
            <div class="submit-button">
                <button type="submit">Submit Donation</button>
            </div>
        </form>
    </div>

    <!-- Footer -->
    @include('partials.main-footer')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let itemCount = 1;
            const itemsContainer = document.getElementById('itemsContainer');
            const addItemBtn = document.getElementById('addItemBtn');

            addItemBtn.addEventListener('click', function() {
                const newIndex = itemCount;
                const newItem = document.createElement('div');
                newItem.className = 'form-card item-row';
                newItem.setAttribute('data-item-index', newIndex);

                newItem.innerHTML = `
                    <div class="item-header">
                        <h3>Item #${newIndex + 1}</h3>
                        <button type="button" class="remove-item-btn">
                            <i class="ri-delete-bin-line"></i> Remove
                        </button>
                    </div>

                    <div class="form-group">
                        <label for="items[${newIndex}][item_name]">Item Name*</label>
                        <input type="text" id="items[${newIndex}][item_name]" name="items[${newIndex}][item_name]" class="input-field" placeholder="Item Name" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="items[${newIndex}][category]">Item Category*</label>
                            <select id="items[${newIndex}][category]" name="items[${newIndex}][category]" class="input-field" required>
                                <option value="">Select a category</option>
                                <option value="Food & Groceries">Food & Groceries</option>
                                <option value="Clothing & Accessories">Clothing & Accessories</option>
                                <option value="Home Goods">Home Goods</option>
                                <option value="School Supplies">School Supplies</option>
                                <option value="Medical Supplies">Medical Supplies</option>
                                <option value="Electronics">Electronics</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="items[${newIndex}][quantity]">Quantity*</label>
                            <input type="number" id="items[${newIndex}][quantity]" name="items[${newIndex}][quantity]" class="input-field" placeholder="Number of items" required min="1" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="items[${newIndex}][description]">Item Description</label>
                        <textarea id="items[${newIndex}][description]" name="items[${newIndex}][description]" rows="3" class="input-field" placeholder="Describe the item's condition, brand, size, etc."></textarea>
                    </div>
                `;

                itemsContainer.appendChild(newItem);
                itemCount++;

                // Show remove buttons on all items except the first one
                document.querySelectorAll('.remove-item-btn').forEach(btn => {
                    btn.style.display = 'inline-block';
                });

                // Add remove functionality
                newItem.querySelector('.remove-item-btn').addEventListener('click', function() {
                    newItem.remove();
                    updateItemNumbers();
                });
            });

            function updateItemNumbers() {
                const items = document.querySelectorAll('.item-row');
                items.forEach((item, index) => {
                    const header = item.querySelector('h3');
                    header.textContent = `Item #${index + 1}`;

                    // Update data attribute
                    item.setAttribute('data-item-index', index);

                    // Hide remove button if only one item remains
                    if (items.length === 1) {
                        item.querySelector('.remove-item-btn').style.display = 'none';
                    }
                });
                itemCount = items.length;
            }

            // Initial setup for remove buttons
            document.querySelectorAll('.remove-item-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const itemRow = this.closest('.item-row');
                    if (document.querySelectorAll('.item-row').length > 1) {
                        itemRow.remove();
                        updateItemNumbers();
                    }
                });
            });
        });
    </script>
    <script>
document.addEventListener("DOMContentLoaded", function() {
    // Select the specific dropdown by its ID
    const dropdown = document.getElementById('dropoff_id');
    
    if (dropdown) {
        const options = dropdown.options;
        // Set the maximum number of characters allowed before cutting off
        const maxChars = 45; // You can adjust this number based on your width

        for (let i = 0; i < options.length; i++) {
            let text = options[i].text;
            
            // Check if text is longer than the limit
            if (text.length > maxChars) {
                // Cut the text and add '...'
                options[i].text = text.substring(0, maxChars) + '...';
                // Optional: Save full text in title attribute so hovering shows full address
                options[i].title = text; 
            }
        }
    }
});
</script>
</body>

</html>
