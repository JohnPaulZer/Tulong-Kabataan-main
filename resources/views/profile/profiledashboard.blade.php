<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tulong Kabataan</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('img/log2.png') }}" type="image/png">
    <!-- Remixicon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,300;0,400;0,700;0,900;1,400&family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap"
        rel="stylesheet">
    <!-- ECharts Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.5.0/echarts.min.js"></script>

    <link rel="stylesheet" href="{{ asset('css/landingpage.css') }}">
    <link rel="stylesheet" href="{{ asset('css/profile/campaigncard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/profile/profiledashboard.css') }}">

</head>

<body>

    @include('profile.partials.universalmodal')
    @include('partials.main-header')
    @include('administrator.partials.loading-screen')

    <div class="usedash-flex">
        <!-- Sidebar -->
        @include('profile.partials.main-sidebar')

        <main class="prof-main">
            <header class="page-header">
                <h1>My Campaign</h1>
                <p>View and manage your campaign, donations, track their performance </p>
            </header>

            <!-- Stats Section -->
            <section class="usedash-stats">
                <div class="usedash-stat-card blue">
                    <div class="usedash-stat-icon">
                        <i class="ri-heart-line"></i>
                    </div>
                    <div class="usedash-stat-content">
                        <h3 id="stat-campaigns-created">0</h3>
                        <p>Your Campaigns</p>
                        <div class="usedash-stat-trend">

                        </div>
                    </div>
                </div>

                <div class="usedash-stat-card green">
                    <div class="usedash-stat-icon">
                        <i class="ri-flag-line"></i>
                    </div>
                    <div class="usedash-stat-content">
                        <h3 id="stat-active-campaigns">0</h3>
                        <p>Active Campaigns</p>
                        <div class="usedash-stat-trend">

                        </div>
                    </div>
                </div>

                <div class="usedash-stat-card amber">
                    <div class="usedash-stat-icon">
                        <i class="ri-time-line"></i>
                    </div>
                    <div class="usedash-stat-content">
                        <h3 id="stat-ended-campaigns">0</h3>
                        <p>Ended Campaigns</p>
                        <div class="usedash-stat-trend">
                        </div>
                    </div>
                </div>

                <div class="usedash-stat-card purple">
                    <div class="usedash-stat-icon">
                        <i class="ri-money-dollar-circle-line"></i>
                    </div>
                    <div class="usedash-stat-content">
                        <h3 id="stat-total-donations">₱0</h3>
                        <p>Total Donations</p>
                        <div class="usedash-stat-trend">
                        </div>
                    </div>
                </div>
            </section>

            <!-- Campaign Analytics -->
            <section class="usedash-card" id="campaign-analytics" tabindex="-1" style="scroll-margin-top:90px;">
                <div class="usedash-card-header">
                    <h2><i class="ri-bar-chart-line"></i> Campaign Analytics</h2>
                    <div>
                        <select id="campaignSelect"
                            style="padding:6px 10px; font-size:13px; border:1px solid #d1d5db; border-radius:6px;">
                            <option value="all">All Campaigns</option>
                            @foreach ($campaigns as $campaign)
                                <option value="{{ $campaign->campaign_id }}">{{ $campaign->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="usedash-analytics-grid">
                    <div class="usedash-chart-container">
                        <h3 style="font-size:16px; font-weight:600; margin-bottom:10px; color:#374151;">Donations in
                            the Last 30 Days</h3>
                        <div id="campaignChart" style="height:300px;"></div>
                    </div>

                    <div class="usedash-chart-container">
                        <div
                            style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                            <h3 style="font-size:16px; font-weight:600; color:#374151;">Top Campaigns (by Total
                                Donations)</h3>
                            <select id="topCampaignFilter"
                                style="padding:6px 10px; font-size:13px; border:1px solid #d1d5db; border-radius:6px;">
                                <option value="all">All Campaigns</option>
                                <option value="active">Active Campaigns</option>
                                <option value="ended">Ended Campaigns</option>
                            </select>
                        </div>
                        <div id="topCampaignChart" style="height:300px;"></div>
                    </div>
                </div>
            </section>

            <!-- My Campaigns -->
            <section class="usedash-card" id="my-campaigns" tabindex="-1" style="scroll-margin-top:90px;">
                <div class="usedash-card-header">
                    <h2><i class="ri-folder-line"></i> My Campaigns</h2>
                    <div style="display:flex; gap:8px; align-items:center;">
                        <!-- Search Input -->
                        <div style="position:relative;">
                            <input type="text" id="campaignSearch" placeholder="Search campaigns..."
                                style="padding:6px 30px 6px 10px; font-size:13px; border:1px solid #d1d5db; border-radius:6px; width:200px;">
                            <i class="ri-search-line"
                                style="position:absolute; right:10px; top:50%; transform:translateY(-50%); color:#6b7280; font-size:14px;">
                            </i>
                        </div>

                        <!-- Existing Filter -->
                        <select id="campaignFilter"
                            style="padding:6px 10px; font-size:13px; border:1px solid #d1d5db; border-radius:6px;">
                            <option value="all">All Campaigns</option>
                            <option value="active">Active</option>
                            <option value="scheduled">Scheduled</option>
                            <option value="ended">Ended</option>
                        </select>
                    </div>
                </div>

                <div id="campaigns-container">
                    @include('profile.partials.campaigncard', [
                        'campaigns' => $campaigns,
                        'donations' => $donations,
                    ])
                </div>
            </section>

            <!-- Activity -->
            <section class="usedash-two-col" id="activity" tabindex="-1" style="scroll-margin-top:90px;">
                <div class="usedash-card usedash-donations-card">
                    <div class="usedash-card-header">
                        <h2><i class="ri-gift-line"></i> My Donations</h2>
                        <button class="usedash-link-btn" onclick="openAllDonationsModal()">View All</button>
                    </div>
                    <div class="usedash-donation-total">
                        <span class="usedash-amount" id="myDonationTotal">₱0</span>
                        <span class="usedash-tag" id="myDonationThisMonth">+₱0 this month</span>
                    </div>
                    <p class="usedash-donation-subtitle">Total donated to other campaigns</p>
                    <div class="usedash-donation-list" id="myDonationList"></div>
                </div>

                <div class="usedash-card">
                    <div class="usedash-card-header">
                        <h2><i class="ri-money-dollar-circle-line"></i> Recent Donations</h2>
                        <button class="usedash-link-btn" onclick="openRecentModal()">View All</button>
                    </div>
                    <div class="usedash-activity-list" id="recentDonationsList"></div>
                </div>
            </section>
        </main>
    </div>

    <!-- VIEW Modal DONATION -->
    <div id="allDonationsModal" class="modal-overlay">
        <div class="modal-container modal-medium">
            <div class="modal-header">
                <h3>All My Donations</h3>
                <button onclick="closeAllDonationsModal()" class="modal-close-btn">✖</button>
            </div>

            <!-- Loading section -->
            <div id="loadingAnimation" class="modal-loading">
                <div class="loading-spinner"></div>
                <p>Loading your donations...</p>
            </div>

            <div id="allDonationsList"></div>
        </div>
    </div>

    <!-- VIEW Recent DONATION  Modal  -->
    <div id="RecentModal" class="modal-overlay">
        <div class="modal-container modal-large">
            <div class="modal-header">
                <h2><i class="ri-money-dollar-circle-line"></i>All Donations</h2>
                <button onclick="closerecentModal()" class="modal-close-btn">&times;</button>
            </div>

            <!-- Loading section -->
            <div id="recentLoadingAnimation" class="modal-loading">
                <div class="loading-spinner"></div>
                <p>Loading recent donations...</p>
            </div>

            <div id="recentDonationsListModal"></div>
        </div>
    </div>

    {{-- Donation Last 30 Days ANALYTICS CHART --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const chartDom = document.getElementById("campaignChart");
            if (!chartDom) return;

            const myChart = echarts.init(chartDom);
            const campaignSelect = document.getElementById("campaignSelect");
            let currentCampaign = campaignSelect ? campaignSelect.value : "all";

            function fetchDonationsChart(campaignId = "all") {
                fetch(`/campaigns/donations-over-time?campaign_id=${campaignId}`, {
                        headers: {
                            "X-Requested-With": "XMLHttpRequest"
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            const option = {
                                grid: {
                                    top: 20,
                                    left: 40,
                                    right: 20,
                                    bottom: 40
                                },
                                tooltip: {
                                    trigger: "axis"
                                },
                                xAxis: {
                                    type: "category",
                                    data: data.labels,
                                    axisLabel: {
                                        rotate: 45
                                    }
                                },
                                yAxis: {
                                    type: "value",
                                    axisLabel: {
                                        formatter: val => "₱" + val.toLocaleString()
                                    }
                                },
                                series: [{
                                    name: "Donations (₱)",
                                    type: "line",
                                    smooth: true,
                                    data: data.data,
                                    lineStyle: {
                                        width: 3,
                                        color: "#3b82f6"
                                    },
                                    areaStyle: {
                                        color: "rgba(59,130,246,0.1)"
                                    },
                                    symbol: "circle",
                                    symbolSize: 6
                                }]
                            };
                            myChart.setOption(option, true);
                        }
                    })
                    .catch(err => console.error("Chart fetch error:", err));
            }

            setInterval(() => {
                fetchDonationsChart(currentCampaign);
            }, 5000);

            if (campaignSelect) {
                campaignSelect.addEventListener("change", function() {
                    currentCampaign = this.value;
                    fetchDonationsChart(currentCampaign);
                });
            }

            fetchDonationsChart(currentCampaign);
            window.addEventListener("resize", () => myChart.resize());
        });
    </script>

    {{-- ANALYTICS CHART TOP --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const chartDom = document.getElementById("topCampaignChart");
            if (!chartDom) return;

            const myChart = echarts.init(chartDom);
            const filterSelect = document.getElementById("topCampaignFilter");

            function fetchTopCampaigns(filter = "all") {
                fetch(`/campaigns/top?filter=${filter}`, {
                        headers: {
                            "X-Requested-With": "XMLHttpRequest"
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            const option = {
                                grid: {
                                    top: 20,
                                    left: 60,
                                    right: 20,
                                    bottom: 40
                                },
                                tooltip: {
                                    trigger: "axis"
                                },
                                xAxis: {
                                    type: "category",
                                    data: data.labels,
                                    axisLabel: {
                                        interval: 0,
                                        rotate: 30
                                    }
                                },
                                yAxis: {
                                    type: "value",
                                    axisLabel: {
                                        formatter: val => "₱" + val.toLocaleString()
                                    }
                                },
                                series: [{
                                    name: "Total Donations",
                                    type: "bar",
                                    data: data.data,
                                    itemStyle: {
                                        color: "#3b82f6",
                                        borderRadius: [4, 4, 0, 0]
                                    }
                                }]
                            };
                            myChart.setOption(option);
                        }
                    })
                    .catch(err => console.error("Top campaigns fetch error:", err));
            }

            fetchTopCampaigns();
            setInterval(() => fetchTopCampaigns(filterSelect.value), 5000);

            filterSelect.addEventListener("change", function() {
                fetchTopCampaigns(this.value);
            });
            window.addEventListener("resize", () => myChart.resize());
        });
    </script>


    {{-- ANALYTICS  CARD SCRIPT --}}
    <script>
        function safeJson(resp) {
            return resp.json ? resp.json() : resp;
        }

        function fetchAnalytics() {
            fetch("/campaigns/analytics", {
                    method: "GET",
                    headers: {
                        "X-Requested-With": "XMLHttpRequest"
                    }
                })
                .then(safeJson)
                .then(data => {
                    if (data.success && data.data) {
                        document.getElementById("stat-campaigns-created").textContent = data.data.campaigns_created;
                        document.getElementById("stat-active-campaigns").textContent = data.data.active_campaigns;
                        document.getElementById("stat-ended-campaigns").textContent = data.data.ended_campaigns;
                        document.getElementById("stat-total-donations").textContent = "₱" + parseFloat(data.data
                            .total_donations).toLocaleString();
                    } else if (data.error) {
                        console.error("Analytics error:", data.error);
                    }
                })
                .catch(err => console.error("Analytics fetch error:", err));
        }
        setInterval(fetchAnalytics, 5000);
        document.addEventListener("DOMContentLoaded", fetchAnalytics);
    </script>

    {{-- DONATION STATISTICS SCRIPT --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            function fetchMyDonations() {
                fetch("{{ route('donations.myAnalytics') }}", {
                        headers: {
                            "X-Requested-With": "XMLHttpRequest"
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById("myDonationTotal").textContent = "₱" + parseFloat(data
                                .total).toLocaleString();
                            document.getElementById("myDonationThisMonth").textContent = "+₱" + parseFloat(data
                                .thisMonth).toLocaleString() + " this month";
                            const list = document.getElementById("myDonationList");
                            list.innerHTML = "";
                            data.recent.forEach(d => {
                                const item = `
                            <div class="usedash-donation-item">
                                <div class="usedash-icon blue"><i class="ri-heart-pulse-line"></i></div>
                                <div class="usedash-donation-info">
                                    <p class="usedash-donation-title">${d.campaign}</p>
                                    <small>${d.date}</small>
                                </div>
                                <span class="usedash-donation-amount">₱${parseFloat(d.amount).toLocaleString()}</span>
                            </div>
                        `;
                                list.insertAdjacentHTML("beforeend", item);
                            });
                        }
                    })
                    .catch(err => console.error("My donations fetch error:", err));
            }
            fetchMyDonations();
            setInterval(fetchMyDonations, 10000);
        });
    </script>

    {{-- VIEW ALL DONATION MODAL SCRIPT --}}
    <script>
        function openAllDonationsModal() {
            const modal = document.getElementById("allDonationsModal");
            const loading = document.getElementById("loadingAnimation");
            const list = document.getElementById("allDonationsList");

            // Show modal and loading, clear previous content
            modal.style.display = "flex";
            loading.style.display = "block";
            list.innerHTML = "";

            fetch("{{ route('donations.all') }}", {
                    headers: {
                        "X-Requested-With": "XMLHttpRequest"
                    }
                })
                .then(res => res.json())
                .then(data => {
                    // Hide loading animation
                    loading.style.display = "none";

                    if (data.success) {
                        data.donations.forEach(d => {
                            const item = `
                        <div class="modal-list-item">
                            <div class="modal-list-icon">
                                <i class="ri-heart-pulse-line"></i>
                            </div>
                            <div class="modal-list-content">
                                <p class="modal-list-title">${d.campaign}</p>
                                <small class="modal-list-subtitle">${d.date}</small>
                            </div>
                            <span class="modal-list-amount">₱${parseFloat(d.amount).toLocaleString()}</span>
                        </div>
                    `;
                            list.insertAdjacentHTML("beforeend", item);
                        });
                    }
                })
                .catch(error => {
                    // Hide loading animation on error too
                    loading.style.display = "none";
                    list.innerHTML =
                        `<p style="text-align:center; color:#ef4444; padding:20px;">Error loading donations</p>`;
                });
        }

        function closeAllDonationsModal() {
            document.getElementById("allDonationsModal").style.display = "none";
        }
    </script>

    {{-- MINI FEED NOTIF --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            function fetchRecentDonations() {
                fetch("{{ route('campaigns.recentDonations') }}", {
                        headers: {
                            "X-Requested-With": "XMLHttpRequest"
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            const list = document.getElementById("recentDonationsList");
                            list.innerHTML = "";
                            if (data.donations.length === 0) {
                                list.innerHTML =
                                    "<p style='color:#6b7280; font-size:14px;'>No recent donations yet.</p>";
                                return;
                            }
                            data.donations.forEach(d => {
                                const item = `
                            <div class="usedash-activity-row">
                                <div class="usedash-activity-icon green">
                                    <i class="ri-heart-line"></i>
                                </div>
                                <div class="usedash-activity-content">
                                    <p><b>${d.donor}</b> donated
                                        <span style="color:#16a34a; font-weight:600;">₱${parseFloat(d.amount).toLocaleString()}</span>
                                        to <b>${d.campaign}</b>
                                    </p>
                                    <small>${d.date}</small>
                                </div>
                            </div>
                        `;
                                list.insertAdjacentHTML("beforeend", item);
                            });
                        }
                    })
                    .catch(err => console.error("Recent donations fetch error:", err));
            }
            fetchRecentDonations();
            setInterval(fetchRecentDonations, 10000);
        });
    </script>

    {{-- VIEW ALL RECENT MODAL --}}
    <script>
        function openRecentModal() {
            const modal = document.getElementById("RecentModal");
            const loading = document.getElementById("recentLoadingAnimation");
            const list = document.getElementById("recentDonationsListModal");

            // Show modal and loading, clear previous content
            modal.style.display = "flex";
            loading.style.display = "block";
            list.innerHTML = "";

            fetch("{{ route('campaigns.recentDonations') }}", {
                    headers: {
                        "X-Requested-With": "XMLHttpRequest"
                    }
                })
                .then(res => res.json())
                .then(data => {
                    // Hide loading animation
                    loading.style.display = "none";

                    if (data.success) {
                        data.donations.forEach(d => {
                            const item = `
                <div style="display:flex; align-items:center; padding:12px; border-bottom:1px solid #f3f4f6;">
                    <div class="usedash-activity-icon green" style="margin-right:12px;">
                        <i class="ri-heart-line"></i>
                    </div>
                    <div style="flex:1;">
                        <p style="margin:0; font-weight:600; color:#111827;">
                            <b>${d.donor}</b> donated ₱${parseFloat(d.amount).toLocaleString()}
                        </p>
                        <small style="color:#6b7280;">${d.date} → ${d.campaign}</small>
                    </div>
                </div>
            `;
                            list.insertAdjacentHTML("beforeend", item);
                        });
                    }
                })
                .catch(error => {
                    // Hide loading animation on error too
                    loading.style.display = "none";
                    list.innerHTML =
                        `<p style="text-align:center; color:#ef4444; padding:20px;">Error loading recent donations</p>`;
                });
        }

        function closerecentModal() {
            document.getElementById("RecentModal").style.display = "none";
        }
    </script>

</body>

</html>
