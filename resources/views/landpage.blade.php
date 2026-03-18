<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tulong Kabataan</title>
    <link rel="icon" href="{{ asset('img/log2.png') }}" type="image/png" />
    <!-- Remixicon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css" />
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,300;0,400;0,700;0,900;1,400&family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/landingpage.css') }}?v=4">

</head>

<body>
    @include('administrator.partials.loading-screen')

    <!-- Navigation & Header -->
    @include('partials.main-header')

    <!-- HERO -->
    <section class="hero">
        <div class="hero-bg">
            <img src="{{ asset('img/bg1.jpg') }}" alt="Hero background" />
            <div class="hero-bg-overlay"></div>
        </div>

        <div class="hero-flex hero-flex-center">
            <div class="hero-content">
                <h1 class="heading-font">Make a Difference Today</h1>
                <p class="body-font">Join thousands of donors and volunteers who are changing lives through our
                    platform. Every contribution matters.</p>
                <div class="hero-actions">
                    <a href="{{ route('campaignpage') }}" class="primary-btn body-font"><i
                            class="ri-heart-add-line"></i> Start Donating</a>
                    <a href="{{ route('event.page') }}" class="secondary-btn body-font"><i class="ri-group-line"></i>
                        Join an Event</a>
                </div>
            </div>



            <div class="hero-stats">
                <?php
                // Calculate stats using your models
                use App\Models\Campaign;
                use App\Models\EventRegistration;
                
                // Total Donations - sum of all current_amount from campaigns
                $totalDonations = Campaign::sum('current_amount');
                
                // Active Volunteers - count all event registrations (all registered volunteers)
                $activeVolunteers = EventRegistration::count();
                
                // Successful Campaigns - count all campaigns with status 'ended'
                $successfulCampaigns = Campaign::where('status', 'ended')->count();
                
                // Format the total donations
                if ($totalDonations >= 1000000) {
                    $formattedDonations = '₱' . number_format($totalDonations / 1000000, 1) . 'M';
                } elseif ($totalDonations >= 1000) {
                    $formattedDonations = '₱' . number_format($totalDonations / 1000, 1) . 'K';
                } else {
                    $formattedDonations = '₱' . number_format($totalDonations);
                }
                
                // Format volunteers count
                if ($activeVolunteers >= 1000) {
                    $formattedVolunteers = number_format($activeVolunteers / 1000, 1) . 'K';
                } else {
                    $formattedVolunteers = number_format($activeVolunteers);
                }
                
                // Format campaigns count
                $formattedCampaigns = number_format($successfulCampaigns);
                ?>

                <div class="stat">
                    <div class="stat-icon"><i class="ri-hand-heart-line"></i></div>
                    <div class="stat-value heading-font"><?php echo $formattedDonations; ?></div>
                    <div class="stat-label body-font">Total Donations Raised</div>
                </div>
                <div class="stat">
                    <div class="stat-icon"><i class="ri-team-line"></i></div>
                    <div class="stat-value heading-font"><?php echo $formattedVolunteers; ?></div>
                    <div class="stat-label body-font">Active Volunteers</div>
                </div>
                <div class="stat">
                    <div class="stat-icon"><i class="ri-heart-line"></i></div>
                    <div class="stat-value heading-font"><?php echo $formattedCampaigns; ?></div>
                    <div class="stat-label body-font">Successful Campaigns</div>
                </div>
            </div>
        </div>
    </section>

    <!-- IMPACT -->
    <section class="impact-section">
        <div class="impact-header">
            <h2 class="heading-font">Our Impact in Disaster Relief</h2>
            <p class="body-font">Together with our community, we've made significant strides in helping communities
                recover and rebuild from natural disasters.</p>
        </div>

        <div class="impact-content">
            <div class="impact-image">
                <img src="{{ asset('img/diss.jpg') }}" alt="Disaster Relief Efforts">
            </div>
            <div class="impact-right">
                <div class="impact-stats-list">
                    <div class="impact-stat">
                        <div class="impact-stat-value heading-font">Hope Delivered</div>
                        <div class="impact-stat-label body-font">For twelve thousand families, hope was not a concept—it
                            was delivered.</div>
                    </div>
                    <div class="impact-stat">
                        <div class="impact-stat-value heading-font">Our Mission</div>
                        <div class="impact-stat-label body-font">Bicolano youth, ready to help fellow Bicolanos.</div>
                    </div>
                    <div class="impact-stat">
                        <div class="impact-stat-value heading-font">Swift Response</div>
                        <div class="impact-stat-label body-font">In times of need, we are never late.</div>
                    </div>
                    <div class="impact-stat">
                        <div class="impact-stat-value heading-font">Community Reach</div>
                        <div class="impact-stat-label body-font">The aid of the youth, within reach of every community.
                        </div>
                    </div>

                </div>

                <div class="impact-details">
                    <div class="impact-detail">
                        <i class="ri-home-heart-line"></i>
                        <div>
                            <h3 class="heading-font">Immediate Shelter Support</h3>
                            <p class="body-font">Provided temporary housing and essential supplies to displaced families
                                within 24 hours.</p>
                        </div>
                    </div>
                    <div class="impact-detail">
                        <i class="ri-medicine-bottle-line"></i>
                        <div>
                            <h3 class="heading-font">Medical Assistance</h3>
                            <p class="body-font">Deployed mobile medical units and supplied essential medications to
                                affected areas.</p>
                        </div>
                    </div>
                    <div class="impact-detail">
                        <i class="ri-community-line"></i>
                        <div>
                            <h3 class="heading-font">Community Rebuilding</h3>
                            <p class="body-font">Coordinated long-term reconstruction efforts with local organizations
                                and volunteers.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="impact-cta">
                <h3 class="heading-font">Ready to Make a Difference?</h3>
                <p class="body-font">Join our emergency response network and help communities when they need it most.
                </p>
                <div class="cta-buttons">
                    <a href="{{ route('campaignpage') }}" class="primary-btn alt body-font"><i
                            class="ri-money-dollar-circle-line"></i> Donate
                        Now</a>
                    <a href="{{ route('event.page') }}" class="secondary-btn alt body-font"><i
                            class="ri-group-line"></i> Join as Volunteer</a>
                </div>
            </div>
        </div>
    </section>

    {{-- <!-- FEATURED CAMPAIGNS -->
    <section class="featured-campaigns">
        <div class="section-header">
            <h2 class="heading-font">DISTRIBUTED IN-KIND DONATIONS</h2>
        </div>

        <div class="campaign-list">
            <div class="campaign-card">
                <a href="" style="display:block">
                    <div class="campaign-image">
                        <img src="{{ asset('img/camp.jpg') }}" alt="Clean Water Initiative" />
                    </div>
                    <div class="campaign-info">
                        <div class="campaign-meta">
                            <span class="campaign-badge urgent body-font"><i class="ri-alarm-warning-line"></i>
                                URGENT</span>
                            <span class="campaign-time body-font"><i class="ri-timer-line"></i> 14 days left</span>
                        </div>
                        <h3 class="heading-font">Clean Water for Riverside Community</h3>
                        <p class="body-font">Help provide clean drinking water to 500+ families in the drought-affected
                            Riverside region.</p>
                        <div class="campaign-progress">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 77%;"></div>
                            </div>
                            <div class="progress-meta body-font">
                                <span><i class="ri-money-dollar-circle-line"></i> $38,450 raised</span>
                                <span>of $50,000 goal</span>
                            </div>
                        </div>
                        <div class="campaign-actions">
                            <button class="primary-btn body-font btn donate show-donation-modal"><i
                                    class="ri-heart-add-line"></i> Donate Now</button>
                            <button class="icon-btn"><i class="ri-share-line"></i></button>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Duplicate sample cards (x2) -->
            <div class="campaign-card">
                <a href="" style="display:block">
                    <div class="campaign-image">
                        <img src="{{ asset('img/camp.jpg') }}" alt="Clean Water Initiative" />
                    </div>
                    <div class="campaign-info">
                        <div class="campaign-meta">
                            <span class="campaign-badge urgent body-font"><i class="ri-alarm-warning-line"></i>
                                URGENT</span>
                            <span class="campaign-time body-font"><i class="ri-timer-line"></i> 14 days left</span>
                        </div>
                        <h3 class="heading-font">Clean Water for Riverside Community</h3>
                        <p class="body-font">Help provide clean drinking water to 500+ families in the drought-affected
                            Riverside region.</p>
                        <div class="campaign-progress">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 77%;"></div>
                            </div>
                            <div class="progress-meta body-font">
                                <span><i class="ri-money-dollar-circle-line"></i> $38,450 raised</span>
                                <span>of $50,000 goal</span>
                            </div>
                        </div>
                        <div class="campaign-actions">
                            <button class="primary-btn body-font btn donate show-donation-modal"><i
                                    class="ri-heart-add-line"></i> Donate Now</button>
                            <button class="icon-btn"><i class="ri-share-line"></i></button>
                        </div>
                    </div>
                </a>
            </div>

            <div class="campaign-card">
                <a href="" style="display:block">
                    <div class="campaign-image">
                        <img src="{{ asset('img/camp.jpg') }}" alt="Clean Water Initiative" />
                    </div>
                    <div class="campaign-info">
                        <div class="campaign-meta">
                            <span class="campaign-badge urgent body-font"><i class="ri-alarm-warning-line"></i>
                                URGENT</span>
                            <span class="campaign-time body-font"><i class="ri-timer-line"></i> 14 days left</span>
                        </div>
                        <h3 class="heading-font">Clean Water for Riverside Community</h3>
                        <p class="body-font">Help provide clean drinking water to 500+ families in the drought-affected
                            Riverside region.</p>
                        <div class="campaign-progress">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 77%;"></div>
                            </div>
                            <div class="progress-meta body-font">
                                <span><i class="ri-money-dollar-circle-line"></i> $38,450 raised</span>
                                <span>of $50,000 goal</span>
                            </div>
                        </div>
                        <div class="campaign-actions">
                            <button class="primary-btn body-font btn donate show-donation-modal"><i
                                    class="ri-heart-add-line"></i> Donate Now</button>
                            <button class="icon-btn"><i class="ri-share-line"></i></button>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </section> --}}

    <!-- TRUST -->
    <section class="trust-section">
        <div class="trust-header">
            <h2 class="heading-font">Your Trust & Security Matter</h2>
            <p class="body-font">We're committed to creating a secure platform where you can donate and volunteer with
                confidence.</p>
        </div>
        <div class="trust-features">
            <div class="trust-feature">
                <i class="ri-shield-check-line"></i>
                <h3 class="heading-font">Secure Payments</h3>
                <p class="body-font">All transactions are encrypted and processed through trusted payment gateways.</p>
            </div>
            <div class="trust-feature">
                <i class="ri-user-follow-line"></i>
                <h3 class="heading-font">Verified Campaigns</h3>
                <p class="body-font">All campaigns undergo thorough verification before being published on our
                    platform.
                </p>
            </div>
            <div class="trust-feature">
                <i class="ri-eye-line"></i>
                <h3 class="heading-font">Transparent Reporting</h3>
                <p class="body-font">Track exactly where your donations go and the impact they make in real-time.</p>
            </div>
            <div class="trust-feature">
                <i class="ri-lock-line"></i>
                <h3 class="heading-font">Data Protection</h3>
                <p class="body-font">Your personal information is protected with industry-standard security measures.
                </p>
            </div>
        </div>
    </section>

    @include('partials.main-footer')

    <div id="modal-container"></div>


</body>

</html>
