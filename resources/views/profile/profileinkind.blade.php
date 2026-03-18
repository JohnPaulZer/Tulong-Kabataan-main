<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tulong Kabataan</title>
    <link rel="icon" href="{{ asset('img/log2.png') }}" type="image/png">
    <!-- Remixicon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,300;0,400;0,700;0,900;1,400&family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap"
        rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.5.0/echarts.min.js"></script>

    <link rel="stylesheet" href="{{ asset('css/landingpage.css') }}">
    <link rel="stylesheet" href="{{ asset('css/profile/profileinkind.css') }}">
</head>

<body>

    @include('profile.partials.universalmodal')
    @include('administrator.partials.loading-screen')
    <!-- Navigation & Header -->
    @include('partials.main-header')

    <div class="ikd-prof-flex">
        <!-- Sidebar -->
        @include('profile.partials.main-sidebar')

        <main class="prof-main">
            <div class="ikd-container">
                <!-- Header -->
                <div class="ikd-page-head">
                    <div class="ikd-page-head-row">
                        <div>
                            <h1 class="ikd-page-title">My In-Kind Donations</h1>
                            <p class="ikd-page-sub">View and manage your submitted donations, track their status </p>
                        </div>
                    </div>
                </div>

                <!-- Donation Analytics -->
                <section class="ikd-panel">
                    <div class="ikd-section-head">
                        <h2 class="ikd-section-title"><span class="ikd-icon"><i
                                    class="ri-bar-chart-line"></i></span>In-Kind Donation</h2>
                    </div>
                    <div class="ikd-charts">
                        <div>
                            <h3 class="ikd-item-title" style="font-size:16px;margin-bottom:10px"> In-Kind Donation Flow
                                by Status</h3>
                            <div class="ikd-chart" id="ikd-donationTrendChart"></div>
                        </div>
                        <div>
                            <h3 class="ikd-item-title" style="font-size:16px;margin-bottom:10px">Category Distribution
                            </h3>
                            <div class="ikd-chart" id="ikd-categoryChart"></div>
                        </div>
                    </div>
                </section>

                <!-- Filter Tabs (MY DONATIONS) -->
                <section class="ikd-panel" id="ikd-my-donations">
                    <div class="ikd-panel-row">
                        <div class="ikd-tabs">
                            <button class="ikd-tab ikd-is-active" data-filter="all">All Donations</button>
                            <button class="ikd-tab" data-filter="Scheduled">Scheduled</button>
                            <button class="ikd-tab" data-filter="Received">Received</button>
                            <button class="ikd-tab" data-filter="Cancelled">Cancelled</button>
                        </div>
                        <div class="ikd-toolbar">
                            <div class="ikd-search">
                                <input type="text" placeholder="Search by item name..." id="ikd-searchInput" />
                                <div class="ikd-icon-left"><i class="ri-search-line"></i></div>
                            </div>
                        </div>
                    </div>

                    <!-- Donations List -->
                    <div class="ikd-list">
                        @foreach ($inkindDonations as $donation)
                            <div class="ikd-donation-card {{ $donation->status === 'approved' ? 'ikd-approved' : '' }}"
                                data-status="{{ $donation->status }}">
                                <div class="ikd-left" style="flex:1">
                                    <div class="ikd-row">
                                        <h3 class="ikd-item-title">{{ $donation->item_name }}</h3>

                                        @if ($donation->status === 'Scheduled')
                                            <span class="ikd-badge ikd-badge-yellow ikd-status-badge">Scheduled</span>
                                        @elseif($donation->status === 'Received')
                                            <span class="ikd-badge ikd-badge-green ikd-status-badge">Received</span>
                                        @elseif($donation->status === 'Cancelled')
                                            <span class="ikd-badge ikd-badge-red ikd-status-badge">Cancelled</span>
                                        @endif
                                    </div>

                                    <div class="ikd-grid">
                                        <div>
                                            <p class="ikd-label">Category</p>
                                            <span class="ikd-badge ikd-badge-blue">{{ $donation->category }}</span>
                                        </div>
                                        <div>
                                            <p class="ikd-label">Quantity</p>
                                            <p class="ikd-value">{{ $donation->quantity }}
                                                {{ $donation->unit ?? 'items' }}</p>
                                        </div>
                                        <div>
                                            <p class="ikd-label">Drop-off Location</p>
                                            <p class="ikd-value">{{ $donation->dropoffPoint?->name ?? 'N/A' }}</p>
                                        </div>
                                        <div>
                                            <p class="ikd-label">Submitted</p>
                                            <p class="ikd-value">
                                                {{ $donation->created_at->format('M d, Y h:i A') }}
                                            </p>
                                        </div>
                                    </div>
                                    <p class="ikd-desc">{{ $donation->description }}</p>
                                </div>

                                <div class="ikd-right-col">
                                    <div class="ikd-row" style="gap:8px">
                                        @if ($donation->status !== 'Cancelled')
                                            <button class="ikd-btn ikd-btn-danger ikd-cancel-btn"
                                                data-id="{{ $donation->inkind_id }}">
                                                Cancel
                                            </button>
                                        @endif

                                        <button class="ikd-btn ikd-btn-ghost ikd-delete-btn"
                                            data-id="{{ $donation->inkind_id }}">
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            </div>
        </main>
    </div>

    {{-- AJAX FOR BUTTON CANCEL AND DELETE --}}
    <script>
        let ikdActionType = null;
        let ikdDonationId = null;
        let ikdTargetBtn = null;

        // --- OPEN MODAL ---
        function openIkdConfirmModal(type, donationId, btn) {
            ikdActionType = type;
            ikdDonationId = donationId;
            ikdTargetBtn = btn;

            // Modal text
            document.getElementById("confirmModalTitle").textContent =
                type === "cancel" ? "Confirm Cancellation" : "Confirm Deletion";
            document.getElementById("confirmModalMessage").textContent =
                type === "cancel" ?
                "Are you sure you want to cancel this donation?" :
                "Are you sure you want to delete this donation? This cannot be undone.";

            // Button styling
            const confirmBtn = document.getElementById("confirmActionBtn");
            confirmBtn.textContent = type === "cancel" ? "Cancel Donation" : "Delete";
            confirmBtn.style.background = type === "cancel" ? "#f59e0b" : "#ef4444";

            document.getElementById("confirmModal").style.display = "flex";
        }

        // --- CLOSE MODAL ---
        function closeIkdConfirmModal() {
            document.getElementById("confirmModal").style.display = "none";
            ikdActionType = null;
            ikdDonationId = null;
            ikdTargetBtn = null;
        }

        // --- TOAST ---
        function showToast(message, type = "success") {
            const toast = document.getElementById("toast");
            toast.textContent = message;
            toast.style.background = type === "error" ? "#dc2626" : "#16a34a";
            toast.style.opacity = "1";

            setTimeout(() => {
                toast.style.opacity = "0";
            }, 3000);
        }

        // --- TRIGGER MODAL FROM BUTTONS ---
        document.addEventListener("click", function(e) {
            const cancelBtn = e.target.closest(".ikd-cancel-btn");
            const deleteBtn = e.target.closest(".ikd-delete-btn");

            if (cancelBtn) {
                e.preventDefault();
                openIkdConfirmModal("cancel", cancelBtn.dataset.id, cancelBtn);
            }
            if (deleteBtn) {
                e.preventDefault();
                openIkdConfirmModal("delete", deleteBtn.dataset.id, deleteBtn);
            }
        });

        // --- CONFIRM ACTION ---
        document.getElementById("confirmActionBtn").addEventListener("click", () => {
            if (!ikdActionType || !ikdDonationId) return;

            const url =
                ikdActionType === "cancel" ?
                `/inkind/${ikdDonationId}/cancel` :
                `/inkind/${ikdDonationId}`;
            const method = ikdActionType === "cancel" ? "PATCH" : "DELETE";

            fetch(url, {
                    method: method,
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute(
                            "content"),
                    },
                })
                .then((res) => res.json())
                .then((data) => {
                    if (data.success) {
                        const card = ikdTargetBtn.closest(".ikd-donation-card");

                        if (ikdActionType === "cancel") {
                            const badge = card.querySelector(".ikd-status-badge");
                            if (badge) {
                                badge.textContent = "Cancelled";
                                badge.className = "ikd-badge ikd-badge-red ikd-status-badge";
                            }
                            card.setAttribute("data-status", "Cancelled");
                            card.classList.add("ikd-cancelled");
                            ikdTargetBtn.remove();
                        } else if (ikdActionType === "delete") {
                            card.remove();
                        }

                        showToast(
                            data.message ||
                            (ikdActionType === "cancel" ?
                                "Donation cancelled successfully." :
                                "Donation deleted successfully.")
                        );
                    } else {
                        showToast(data.error || "Action failed.", "error");
                    }
                })
                .catch(() => showToast("Request failed.", "error"))
                .finally(closeIkdConfirmModal);
        });

        // --- CANCEL MODAL BUTTON ---
        document.getElementById("cancelConfirmBtn").addEventListener("click", closeIkdConfirmModal);
    </script>

    {{-- AJAX POLLING AND ANALYTICS CHART --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const trendChart = echarts.init(document.getElementById('ikd-donationTrendChart'));
            const categoryChart = echarts.init(document.getElementById('ikd-categoryChart'));

            let currentFilter = "all"; // default active tab
            let currentSearch = ""; // default search term

            function applyFilters() {
                const cards = document.querySelectorAll(".ikd-donation-card");
                let visibleCount = 0;

                cards.forEach(card => {
                    const status = card.getAttribute("data-status")?.toLowerCase();
                    const title = card.querySelector(".ikd-item-title")?.textContent.toLowerCase() || "";
                    const matchesFilter = (currentFilter === "all" || status === currentFilter
                        .toLowerCase());
                    const matchesSearch = title.includes(currentSearch);
                    const isVisible = matchesFilter && matchesSearch;

                    card.style.display = isVisible ? "flex" : "none";
                    if (isVisible) visibleCount++;
                });

                // Add or update "no results" message
                const listContainer = document.querySelector(".ikd-list");
                let noResultsMsg = listContainer.querySelector(".ikd-no-results");

                if (visibleCount === 0) {
                    if (!noResultsMsg) {
                        noResultsMsg = document.createElement("div");
                        noResultsMsg.className = "ikd-no-results";
                        noResultsMsg.innerHTML = `
                <div style="text-align:center; padding:40px 20px; color:#6b7280;">
                    <i class="ri-search-line" style="font-size:48px; margin-bottom:16px; display:block;"></i>
                    <h3 style="margin:0 0 8px 0;">No In-Kind Donations found</h3>
                    <p>Try selecting a different filter or search term</p>
                </div>
            `;
                        listContainer.appendChild(noResultsMsg);
                    }
                } else if (noResultsMsg) {
                    noResultsMsg.remove();
                }

                // Reset scroll to top of section if on mobile
                if (visibleCount === 0 && window.innerWidth <= 768) {
                    const section = document.getElementById("ikd-my-donations");
                    if (section) {
                        setTimeout(() => {
                            section.scrollIntoView({
                                behavior: 'smooth'
                            });
                        }, 100);
                    }
                }
            }

            function fetchDonationsAndAnalytics() {
                fetch("{{ route('inkind.list') }}", {
                        method: "GET",
                        headers: {
                            "X-Requested-With": "XMLHttpRequest"
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (!data.success) return;

                        const donations = data.donations;
                        const list = document.querySelector(".ikd-list");
                        list.innerHTML = "";

                        // --- rebuild list ---
                        donations.forEach(donation => {
                            const card = document.createElement("div");
                            card.className = "ikd-donation-card " +
                                (donation.status === "Received" ? "ikd-approved" :
                                    donation.status === "Cancelled" ? "ikd-cancelled" : "");
                            card.setAttribute("data-status", donation.status);

                            card.innerHTML = `
              <div class="ikd-left" style="flex:1">
                <div class="ikd-row">
                  <h3 class="ikd-item-title">${donation.item_name}</h3>
                  ${donation.status === "Scheduled"
                      ? `<span class="ikd-badge ikd-badge-yellow ikd-status-badge">Scheduled</span>`
                      : donation.status === "Received"
                      ? `<span class="ikd-badge ikd-badge-green ikd-status-badge">Received</span>`
                      : `<span class="ikd-badge ikd-badge-red ikd-status-badge">Cancelled</span>`}
                </div>
                <div class="ikd-grid">
                  <div>
                    <p class="ikd-label">Category</p>
                    <span class="ikd-badge ikd-badge-blue">${donation.category}</span>
                  </div>
                  <div>
                    <p class="ikd-label">Quantity</p>
                    <p class="ikd-value">${donation.quantity} ${donation.unit}</p>
                  </div>
                  <div>
                    <p class="ikd-label">Drop-off Location</p>
                    <p class="ikd-value">${donation.dropoffPoint?.name ?? 'N/A'}</p>
                  </div>
                  <div>
                    <p class="ikd-label">Submitted</p>
                    <p class="ikd-value">${donation.created_at}</p>
                  </div>
                </div>
                <p class="ikd-desc">${donation.description ?? ''}</p>
              </div>
              <div class="ikd-right-col">
                <div class="ikd-row" style="gap:8px">
                  ${
                    (donation.status !== "Cancelled" && donation.status !== "Received")
                      ? `<button class="ikd-btn ikd-btn-danger ikd-cancel-btn" data-id="${donation.inkind_id}">Cancel</button>`
                      : ""
                  }
                  <button class="ikd-btn ikd-btn-ghost ikd-delete-btn" data-id="${donation.inkind_id}">Delete</button>
                </div>
              </div>
            `;
                            list.appendChild(card);
                        });

                        // 🔄 reapply filters
                        applyFilters();

                        // --- analytics update ---
                        let scheduled = 0,
                            received = 0,
                            cancelled = 0;
                        let categoryMap = {};

                        donations.forEach(d => {
                            if (d.status === "Scheduled") scheduled++;
                            else if (d.status === "Received") received++;
                            else if (d.status === "Cancelled") cancelled++;
                            if (d.category) categoryMap[d.category] = (categoryMap[d.category] || 0) +
                                1;
                        });

                        // Status bar chart
                        trendChart.setOption({
                            animation: true,
                            tooltip: {
                                trigger: 'item',
                                backgroundColor: 'rgba(255,255,255,.9)',
                                borderColor: '#e5e7eb',
                                borderWidth: 1,
                                textStyle: {
                                    color: '#1f2937'
                                }
                            },
                            xAxis: {
                                type: 'category',
                                data: ['Scheduled', 'Received', 'Cancelled'],
                                axisLine: {
                                    lineStyle: {
                                        color: '#e5e7eb'
                                    }
                                },
                                axisTick: {
                                    show: false
                                },
                                axisLabel: {
                                    color: '#6b7280'
                                }
                            },
                            yAxis: {
                                type: 'value',
                                axisLine: {
                                    show: false
                                },
                                axisTick: {
                                    show: false
                                },
                                axisLabel: {
                                    color: '#6b7280'
                                },
                                splitLine: {
                                    lineStyle: {
                                        color: '#f3f4f6'
                                    }
                                }
                            },
                            series: [{
                                name: 'Donations',
                                type: 'bar',
                                barWidth: '40%',
                                data: [{
                                        value: scheduled,
                                        itemStyle: {
                                            color: '#facc15'
                                        }
                                    },
                                    {
                                        value: received,
                                        itemStyle: {
                                            color: '#22c55e'
                                        }
                                    },
                                    {
                                        value: cancelled,
                                        itemStyle: {
                                            color: '#ef4444'
                                        }
                                    }
                                ],
                                label: {
                                    show: true,
                                    position: 'top',
                                    color: '#374151',
                                    fontWeight: 600
                                }
                            }]
                        });

                        // Category pie chart
                        const categoryData = Object.keys(categoryMap).map(cat => ({
                            value: categoryMap[cat],
                            name: cat
                        }));
                        categoryChart.setOption({
                            animation: true,
                            tooltip: {
                                trigger: 'item',
                                backgroundColor: 'rgba(255,255,255,.9)',
                                borderColor: '#e5e7eb',
                                borderWidth: 1,
                                textStyle: {
                                    color: '#1f2937'
                                }
                            },
                            series: [{
                                name: 'Category Distribution',
                                type: 'pie',
                                radius: ['40%', '70%'],
                                center: ['50%', '50%'],
                                data: categoryData,
                                emphasis: {
                                    itemStyle: {
                                        shadowBlur: 10,
                                        shadowOffsetX: 0,
                                        shadowColor: 'rgba(0,0,0,.5)'
                                    }
                                },
                                label: {
                                    show: true,
                                    position: 'outside'
                                }
                            }]
                        });
                    })
                    .catch(err => console.error("Fetch failed:", err));
            }

            // Initial + refresh
            fetchDonationsAndAnalytics();
            setInterval(fetchDonationsAndAnalytics, 15000);

            // Tabs filter
            document.querySelectorAll(".ikd-tab").forEach(tab => {
                tab.addEventListener("click", function() {
                    document.querySelectorAll(".ikd-tab").forEach(t => t.classList.remove(
                        "ikd-is-active"));
                    this.classList.add("ikd-is-active");
                    currentFilter = this.getAttribute("data-filter");
                    applyFilters();
                });
            });

            // Search
            const searchInput = document.getElementById("ikd-searchInput");
            if (searchInput) {
                searchInput.addEventListener("input", function() {
                    currentSearch = this.value.toLowerCase().trim();
                    applyFilters();
                });
            }

            // Resize
            window.addEventListener('resize', () => {
                trendChart.resize();
                categoryChart.resize();
            });
        });
    </script>



    {{-- MOBILE SCRIPT --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggle = document.getElementById('ikd-ikd-toggle');
            const submenu = document.getElementById('ikd-ikd-submenu');
            if (!toggle || !submenu) return;

            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                const open = submenu.classList.toggle('ikd-open');
                toggle.classList.toggle('ikd-open', open);
                toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            });
        });
    </script>

    <!-- Mobile tap-to-expand rail -->
    <script>
        (function() {
            const sidebar = document.getElementById('usedashSidebar');
            if (!sidebar) return;

            sidebar.addEventListener('click', function(e) {
                if (!window.matchMedia('(max-width: 768px)').matches) return;

                const isOpen = sidebar.classList.contains('open');
                const isLinkOrBtn = e.target.closest('a, button');

                if (!isOpen) {
                    sidebar.classList.add('open'); // first tap opens
                    if (isLinkOrBtn) e.preventDefault(); // prevent immediate nav
                }
            });

            // close when tapping outside
            document.addEventListener('click', function(e) {
                if (window.matchMedia('(max-width: 768px)').matches) {
                    if (!sidebar.contains(e.target)) sidebar.classList.remove('open');
                }
            });

            // close when resizing to desktop
            window.addEventListener('resize', function() {
                if (!window.matchMedia('(max-width: 768px)').matches) {
                    sidebar.classList.remove('open');
                }
            });
        })();
    </script>




</body>

</html>
