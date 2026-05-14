<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campaign | Tulong Kabataan</title>
    <link rel="icon" href="img/log2.png" type="image/png">
    <!-- Remixicon -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>

<body class="campaign-page">
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
                        <p class="section-desc">Browse community-led campaigns and support the causes that need help today.</p>
                    </div>

                    <form action="{{ route('campaign.createpage') }}" method="GET"
                        class="campaign-create-form">
                        @php
                            $user = \Illuminate\Support\Facades\Auth::user();
                            $isVerified = $user && $user->identityStatus && $user->identityStatus->status === 'Verified';
                        @endphp

                        @if ($isVerified)
                            <button class="btn create-campaign tooltip-btn" type="submit">
                                <i class="ri-add-line"></i>
                                <span>Create Campaign</span>
                            </button>
                        @else
                            <button class="btn create-campaign tooltip-btn is-disabled" type="button" disabled
                                title="Verify your account to create campaign" data-bs-toggle="tooltip"
                                data-bs-placement="top">
                                <i class="ri-add-line"></i>
                                <span>Create Campaign</span>
                            </button>
                        @endif
                    </form>

                </div>

                <div class="tk-featured-program">
                    <article class="tk-featured-card">
                        <div class="tk-featured-media">
                            <img src="{{ asset('img/diss.jpg') }}" alt="Tulong Kabataan Youth Program" class="featured-img">
                        </div>
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
                        </div>
                    </article>
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
