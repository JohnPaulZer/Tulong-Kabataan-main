<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campaign | Tulong Kabataan</title>
    <link rel="icon" href="img/log2.png" type="image/png">
    <!-- Remixicon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <!-- Google Fonts: Playfair Display & Open Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,300;0,400;0,700;0,900;1,400&family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/landingpage.css') }}?v=4">
    <link rel="stylesheet" href="{{ asset('css/campaign/campaignpage.css') }}?v=4">

    @livewireStyles
</head>

<body>
    <!-- Navigation & Header -->
    @include('partials.main-header')
    @include('administrator.partials.loading-screen')

    <!-- Main Content -->
    <main>
        <section class="campaigns-section">
            <div class="tk-container">
                <div class="tk-section-header">
                    <div class="tk-section-header-left">
                        <h1 class="section-title">All Campaigns</h1>
                        <p class="section-desc">Browse, filter, and discover campaigns that need your support</p>
                    </div>

                    <form action="{{ route('campaign.createpage') }}" method="GET"
                        style="position: relative; display: inline-block;">
                        <?php
                        use Illuminate\Support\Facades\Auth;
                        
                        // Get current user
                        $user = Auth::user();
                        $isVerified = false;
                        
                        // Check if user is authenticated and has verified identity status
                        if ($user && $user->identityStatus) {
                            $isVerified = $user->identityStatus->status === 'Verified';
                        }
                        ?>
                        <?php if ($isVerified): ?>
                        <button class="btn create-campaign tooltip-btn" type="submit">
                            <i class="ri-add-line"></i> Create Campaign
                        </button>
                        <?php else: ?>
                        <button class="btn create-campaign tooltip-btn" type="button" disabled
                            title="Verify your account to create campaign" data-bs-toggle="tooltip"
                            data-bs-placement="top"
                            style="
                            opacity: 0.65;
                            cursor: not-allowed;
                            background-color: #f8f9fa;
                            border: 1px solid #ced4da;
                            color: #868e96;
                            box-shadow: none;
                            transform: none !important;
                            transition: none;
                            filter: grayscale(0.3);
                        ">
                            <i class="ri-add-line" style="opacity: 0.7;"></i> Create Campaign
                        </button>
                        <?php endif; ?>
                    </form>

                </div>

                <div class="tk-featured-program">
                    <div class="tk-featured-card">
                        <img src="{{ asset('img/diss.jpg') }}" alt="Tulong Kabataan Youth Program" class="featured-img">
                        <div class="tk-featured-content">
                            <div class="tk-featured-content-header">
                                <span class="featured-badge">YOUTH EMPOWERMENT</span>
                                <span class="featured-views"><i class="ri-eye-line"></i> 3.2k views</span>
                            </div>
                            <h2 class="featured-title">Tulong Kabataan: Youth for Community Action</h2>
                            <p class="featured-desc">
                                A comprehensive youth development program empowering Filipino youth through leadership
                                training,
                                community service, and disaster preparedness initiatives. Join us in building resilient
                                communities led
                                by capable young leaders.
                            </p>
                            <div class="tk-featured-actions">

                            </div>
                        </div>
                    </div>
                </div>

                <!-- Campaigns Layout -->
                <div class="tk-campaigns-layout">
                    <!-- Campaigns Main Column -->
                    <div class="tk-campaigns-main" id="campaigns-container">
                        <livewire:campaigns-grid />
                    </div> <!-- /.tk-campaigns-main -->
                </div> <!-- /.tk-campaigns-layout -->



            </div>
        </section>
    </main>

    @include('partials.main-footer')

    @livewireScripts
</body>

</html>
