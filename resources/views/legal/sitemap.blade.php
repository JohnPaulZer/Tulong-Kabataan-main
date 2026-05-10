@extends('layouts.info-page')

@section('title', 'Sitemap | Tulong Kabataan')
@section('body_class', 'info-page sitemap-page')
@section('hero_kicker', 'Support')
@section('hero_title', 'Sitemap')
@section('hero_subtitle', 'A simple guide to the main public and user-side pages on Tulong Kabataan.')

@section('section_nav')
    <a class="is-active" href="#main-pages">Main Pages</a>
    <a href="#user-account">User Account</a>
    <a href="#requests-process">Requests and Applications</a>
    <a href="#donations-volunteering">Donations and Volunteering</a>
    <a href="#legal-support">Legal and Support</a>
@endsection

@section('content')
    <section id="main-pages" class="info-section">
        <h2>Main Pages</h2>
        <p>Use these pages to learn about Tulong Kabataan, browse active community support opportunities, and find public information.</p>
        <div class="info-sitemap-grid">
            <a class="info-sitemap-card" href="{{ route('landpage') }}">
                <span>Home</span>
                <small>Main landing page for Tulong Kabataan.</small>
            </a>
            <a class="info-sitemap-card" href="{{ route('campaignpage') }}">
                <span>Campaigns</span>
                <small>Browse donation campaigns and community needs.</small>
            </a>
            <a class="info-sitemap-card" href="{{ route('event.page') }}">
                <span>Events</span>
                <small>View volunteer events and opportunities.</small>
            </a>
            <a class="info-sitemap-card" href="{{ route('inkind.page') }}">
                <span>Donate Items</span>
                <small>Learn about in-kind donation categories and drop-off options.</small>
            </a>
            <a class="info-sitemap-card" href="{{ route('about.us') }}">
                <span>About Us</span>
                <small>Learn about Tulong Kabataan's mission and impact.</small>
            </a>
            <a class="info-sitemap-card" href="{{ route('inkind.tracking') }}">
                <span>Donation Impact</span>
                <small>See in-kind donation impact reports and drop-off information.</small>
            </a>
        </div>
    </section>

    <section id="user-account" class="info-section">
        <h2>User Account</h2>
        <p>These pages help users access and manage their Tulong Kabataan accounts. Some pages may require sign-in.</p>
        <div class="info-sitemap-grid">
            <a class="info-sitemap-card" href="{{ route('login.page') }}">
                <span>Login</span>
                <small>Sign in to your Tulong Kabataan account.</small>
            </a>
            <a class="info-sitemap-card" href="{{ route('login.register') }}">
                <span>Register</span>
                <small>Create a new user account.</small>
            </a>
            <a class="info-sitemap-card" href="{{ auth()->check() ? route('profile') : route('login.page') }}">
                <span>Profile</span>
                <small>View and update your user profile after signing in.</small>
            </a>
            <a class="info-sitemap-card" href="{{ auth()->check() ? route('profile.dash') : route('login.page') }}">
                <span>User Dashboard</span>
                <small>Review your campaigns, donations, analytics, and updates.</small>
            </a>
            <a class="info-sitemap-card" href="{{ auth()->check() ? route('profile.event') : route('login.page') }}">
                <span>My Events</span>
                <small>Track event registrations and volunteer participation.</small>
            </a>
            <a class="info-sitemap-card" href="{{ auth()->check() ? route('profile.inkind') : route('login.page') }}">
                <span>My In-Kind Donations</span>
                <small>Review your submitted in-kind donation records.</small>
            </a>
        </div>
    </section>

    <section id="requests-process" class="info-section">
        <h2>Requests and Applications</h2>
        <p>These pages support user-side requests, applications, and status checking. They are included only because the routes exist and are user-facing.</p>
        <div class="info-sitemap-grid">
            <a class="info-sitemap-card" href="{{ auth()->check() ? route('campaign.createpage') : route('login.page') }}">
                <span>Create Campaign</span>
                <small>Submit a campaign request for community support.</small>
            </a>
            <a class="info-sitemap-card" href="{{ auth()->check() ? route('verify.page') : route('login.page') }}">
                <span>Account Verification</span>
                <small>Submit identity verification information when required.</small>
            </a>
            <a class="info-sitemap-card" href="{{ auth()->check() ? route('donations.track') : route('login.page') }}">
                <span>My Donations</span>
                <small>Check your submitted donation activity.</small>
            </a>
            <a class="info-sitemap-card" href="{{ auth()->check() ? route('profile.dash') : route('login.page') }}">
                <span>Check Campaign Status</span>
                <small>Review campaign status and updates from your dashboard.</small>
            </a>
        </div>
    </section>

    <section id="donations-volunteering" class="info-section">
        <h2>Donations and Volunteering</h2>
        <p>Use these pages to find ways to support campaigns, participate in events, or give in-kind donations.</p>
        <div class="info-sitemap-grid">
            <a class="info-sitemap-card" href="{{ route('campaignpage') }}">
                <span>Donate to Campaigns</span>
                <small>Choose a campaign and follow the donation process.</small>
            </a>
            <a class="info-sitemap-card" href="{{ route('event.page') }}">
                <span>Volunteer Events</span>
                <small>Browse events and select volunteer opportunities.</small>
            </a>
            <a class="info-sitemap-card" href="{{ route('inkind.page') }}">
                <span>In-Kind Donations</span>
                <small>Submit item donations through the in-kind donation process.</small>
            </a>
            <a class="info-sitemap-card" href="{{ route('inkind.tracking') }}">
                <span>Impact Tracker</span>
                <small>View impact reports connected to distributed donations.</small>
            </a>
        </div>
    </section>

    <section id="legal-support" class="info-section">
        <h2>Legal and Support</h2>
        <p>These pages explain platform policies, support channels, privacy practices, and site navigation.</p>
        <div class="info-sitemap-grid">
            <a class="info-sitemap-card" href="{{ route('privacy.policy') }}">
                <span>Privacy Policy</span>
                <small>Learn how Tulong Kabataan handles personal information.</small>
            </a>
            <a class="info-sitemap-card" href="{{ route('terms.service') }}">
                <span>Terms of Service</span>
                <small>Review platform rules, responsibilities, and restrictions.</small>
            </a>
            <a class="info-sitemap-card" href="{{ route('cookie.policy') }}">
                <span>Cookie Policy</span>
                <small>Understand cookies, local storage, and basic platform functionality.</small>
            </a>
            <a class="info-sitemap-card" href="{{ route('contact.us') }}">
                <span>Contact Us</span>
                <small>Find official support channels and guidance.</small>
            </a>
            <a class="info-sitemap-card" href="{{ route('sitemap') }}">
                <span>Sitemap</span>
                <small>You are here. Use this guide to navigate the platform.</small>
            </a>
        </div>
    </section>
@endsection
