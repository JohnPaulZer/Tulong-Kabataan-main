@extends('layouts.info-page')

@section('title', 'Cookie Policy | Tulong Kabataan')
@section('body_class', 'info-page cookie-policy-page')
@section('hero_kicker', 'Legal')
@section('hero_title', 'Cookie Policy')
@section('hero_subtitle', 'Last Updated: October 24, 2025')

@section('section_nav')
    <a class="is-active" href="#introduction">Introduction</a>
    <a href="#what-cookies-are">What Cookies Are</a>
    <a href="#how-we-use-cookies">How We Use Cookies</a>
    <a href="#types-of-cookies">Types of Cookies</a>
    <a href="#local-storage">Local Storage</a>
    <a href="#managing-cookies">Managing Cookies</a>
    <a href="#third-party-services">Third-Party Services</a>
    <a href="#changes">Changes</a>
    <a href="#contact-information">Contact Information</a>
@endsection

@section('content')
    <section id="introduction" class="info-section">
        <h2>Introduction</h2>
        <p>This Cookie Policy explains how Tulong Kabataan may use cookies, session data, local storage, and similar technologies to operate the platform and support user-side features such as login sessions, security, form workflows, notifications, event registration, campaign access, and donation-related pages.</p>
        <p>Based on the current platform setup, Tulong Kabataan may use essential cookies or local storage only when needed to support login sessions, security, preferences, temporary interface state, or basic platform functionality. We do not describe advertising cookies or behavioral advertising tracking here because the current project does not show that kind of implementation.</p>
    </section>

    <section id="what-cookies-are" class="info-section">
        <h2>What Cookies Are</h2>
        <p>Cookies are small text files that a website may place on your browser or device. They help the website remember information during your visit or across visits. Similar technologies, such as local storage or session storage, may store small pieces of data in your browser to support interface behavior or temporary user actions.</p>
    </section>

    <section id="how-we-use-cookies" class="info-section">
        <h2>How We Use Cookies</h2>
        <p>Tulong Kabataan may use cookies and similar technologies for practical platform purposes, including:</p>
        <ul>
            <li>Keeping users signed in during a secure session.</li>
            <li>Protecting forms and requests from unauthorized or cross-site activity.</li>
            <li>Supporting login, registration, email verification, password reset, and logout flows.</li>
            <li>Remembering temporary interface states, such as loading screens, notices, or pending messages.</li>
            <li>Helping event, campaign, donation, in-kind donation, notification, and profile pages function correctly.</li>
            <li>Maintaining platform security, debugging technical issues, and preventing misuse.</li>
        </ul>
    </section>

    <section id="types-of-cookies" class="info-section">
        <h2>Types of Cookies We May Use</h2>
        <ul>
            <li><strong>Essential session cookies:</strong> Used to keep the platform working, maintain login sessions, protect form submissions, and support secure navigation.</li>
            <li><strong>Security cookies:</strong> Used to help verify requests, reduce unauthorized activity, and protect account actions.</li>
            <li><strong>Preference or interface storage:</strong> Used to remember temporary UI behavior, such as loading mode, toast messages, or basic interaction state.</li>
            <li><strong>Operational logs:</strong> Server-side logs may record basic technical details such as timestamps, request paths, IP addresses, browser information, and errors for security and maintenance.</li>
        </ul>
        <p>We do not currently claim to use advertising cookies, ad pixels, or cross-site behavioral advertising cookies on this platform.</p>
    </section>

    <section id="local-storage" class="info-section">
        <h2>Local Storage and Session Storage</h2>
        <p>Some Tulong Kabataan pages may use browser storage to support user experience. For example, temporary storage may be used to remember loading screen behavior, show a pending event message, or preserve interface state after a page action.</p>
        <p>Local storage and session storage are stored in your browser. Session storage usually clears when the browser session ends, while local storage may remain until deleted by the browser, the page script, or the user.</p>
    </section>

    <section id="managing-cookies" class="info-section">
        <h2>Managing Cookies</h2>
        <p>You can control cookies and browser storage through your browser settings. You may be able to block, delete, or limit cookies and site data. However, disabling essential cookies may prevent Tulong Kabataan features from working correctly, including login, registration, account verification, donations, campaign creation, event registration, in-kind donation tracking, and secure form submissions.</p>
        <p>If you use a shared or public device, we recommend signing out after using your account and clearing browser data when appropriate.</p>
    </section>

    <section id="third-party-services" class="info-section">
        <h2>Third-Party Services</h2>
        <p>Tulong Kabataan may rely on third-party services for functions such as authentication, email delivery, payment processing, social media links, hosting, file storage, or external communication channels. These third-party services may use their own cookies or similar technologies when you interact with them.</p>
        <p>For example, if you choose to sign in with Google, visit our Facebook page, open an external social media link, or use a payment provider outside the platform, that service may process data under its own policies. We encourage you to review third-party privacy and cookie notices before using those services.</p>
    </section>

    <section id="changes" class="info-section">
        <h2>Changes to This Cookie Policy</h2>
        <p>We may update this Cookie Policy if Tulong Kabataan changes its platform features, security practices, browser storage behavior, or third-party service integrations. Any updates will be posted on this page with a revised last updated date.</p>
    </section>

    <section id="contact-information" class="info-section">
        <h2>Contact Information</h2>
        <p>If you have questions about this Cookie Policy or how Tulong Kabataan uses cookies or browser storage, please contact the support team through the available contact channels or email <a href="mailto:tulongkabataan.bicol@gmail.com">tulongkabataan.bicol@gmail.com</a>.</p>
    </section>
@endsection
