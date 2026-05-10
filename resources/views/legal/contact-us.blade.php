@extends('layouts.info-page')

@section('title', 'Contact Us | Tulong Kabataan')
@section('body_class', 'info-page contact-us-page')
@section('hero_kicker', 'Support')
@section('hero_title', 'Contact Us')
@section('hero_subtitle', 'We are here to help with program, partnership, volunteer, donation, safety, and privacy concerns.')

@section('section_nav')
    <a class="is-active" href="#contact-options">Contact Options</a>
    <a href="#what-we-can-help-with">What We Can Help With</a>
    <a href="#before-contacting">Before Contacting Us</a>
    <a href="#urgent-concerns">Urgent Concerns</a>
    <a href="#privacy-requests">Privacy Requests</a>
    <a href="#visit-headquarters">Visit Our Headquarters</a>
    <a href="#support-guidance">Support Guidance</a>
@endsection

@section('content')
    <section id="contact-options" class="info-section">
        <h2>Get in Touch</h2>
        <p>Have questions about our programs, want to partner with us, need assistance with a donation, or want to report a platform concern? Our team is here to help. Choose your preferred method below.</p>

        <div class="info-contact-grid">
            <article class="info-contact-card">
                <div class="info-contact-card__icon info-contact-card__icon--blue" aria-hidden="true">
                    <i class="ri-mail-send-line"></i>
                </div>
                <h3>Email Support</h3>
                <p>For general inquiries, partnerships, campaign concerns, and donation issues.</p>
                <a href="mailto:tulongkabataan.bicol@gmail.com">
                    tulongkabataan.bicol@gmail.com
                    <i class="ri-arrow-right-line" aria-hidden="true"></i>
                </a>
            </article>

            <article class="info-contact-card">
                <div class="info-contact-card__icon info-contact-card__icon--indigo" aria-hidden="true">
                    <i class="ri-messenger-line"></i>
                </div>
                <h3>Social Media</h3>
                <p>Message us on Facebook for community updates and faster coordination.</p>
                <a href="https://www.facebook.com/tulongkabataanbicol" target="_blank" rel="noopener noreferrer">
                    @tulongkabataanbicol
                    <i class="ri-arrow-right-line" aria-hidden="true"></i>
                </a>
            </article>

            <article class="info-contact-card">
                <div class="info-contact-card__icon info-contact-card__icon--orange" aria-hidden="true">
                    <i class="ri-alarm-warning-line"></i>
                </div>
                <h3>Emergency Relief</h3>
                <p>Urgent coordination regarding ongoing calamity operations or time-sensitive relief concerns.</p>
                <a href="tel:+639123456789">
                    +63 912 345 6789
                    <i class="ri-arrow-right-line" aria-hidden="true"></i>
                </a>
            </article>
        </div>
    </section>

    <section id="what-we-can-help-with" class="info-section">
        <h2>What We Can Help With</h2>
        <p>To help direct your concern to the right team member, please identify the type of support you need.</p>
        <ul>
            <li><strong>Donation support:</strong> Missing donation records, payment proof review, duplicate donation concerns, manual donation requests, refund or correction requests, and suspected fake donations.</li>
            <li><strong>Campaign support:</strong> Campaign creation questions, verification requirements, campaign status, campaign updates, transparency reports, beneficiary documentation, and campaign misuse reports.</li>
            <li><strong>Account and verification support:</strong> Login issues, email verification, identity verification reuploads, profile updates, account security, and suspicious account activity.</li>
            <li><strong>Volunteer and event support:</strong> Event registration, role assignments, attendance concerns, event reminders, safety questions, and volunteer conduct reports.</li>
            <li><strong>In-kind donation support:</strong> Drop-off point questions, item categories, donation status, cancellation, delivery coordination, and item suitability concerns.</li>
            <li><strong>Partnership and community coordination:</strong> School, barangay, youth group, NGO, sponsor, and community partner inquiries.</li>
            <li><strong>Privacy and safety concerns:</strong> Data privacy requests, unauthorized use of photos or documents, exposed personal information, beneficiary privacy concerns, and reports involving minors or vulnerable persons.</li>
        </ul>
    </section>

    <section id="before-contacting" class="info-section">
        <h2>Before Contacting Us</h2>
        <p>Please include enough information for us to review your concern responsibly. Avoid sending unnecessary sensitive information unless we specifically ask for it through an official channel.</p>
        <ul>
            <li>Your full name and the email address or phone number connected to your Tulong Kabataan account.</li>
            <li>The campaign title, event name, donation reference number, in-kind donation record, or verification request involved.</li>
            <li>A short but clear description of what happened, when it happened, and what action you are requesting.</li>
            <li>Relevant screenshots, receipts, proof of payment, or supporting documents if they are necessary to resolve the issue.</li>
            <li>For safety or privacy concerns, identify the page, post, account, campaign, or message that may have caused harm.</li>
        </ul>
    </section>

    <section id="urgent-concerns" class="info-section">
        <h2>Urgent Concerns and Safety Reports</h2>
        <p>For urgent concerns involving suspected fraud, unauthorized fundraising, misuse of beneficiary information, exposed identity documents, threats, harassment, exploitation, minors, or safety risks during relief operations, contact us as soon as possible through email, Facebook, or the available emergency relief number.</p>
        <p>Tulong Kabataan may preserve records, restrict accounts, pause campaigns, remove content, notify affected users, or coordinate with appropriate authorities or partners when needed to protect donors, volunteers, beneficiaries, and the community.</p>
    </section>

    <section id="privacy-requests" class="info-section">
        <h2>Privacy Requests</h2>
        <p>If you want to request access, correction, deletion, blocking, or review of personal information processed by Tulong Kabataan, please contact us through an official channel and clearly mark your message as a privacy request.</p>
        <p>For privacy requests, we may ask you to verify your identity before taking action. Some information may need to be retained for donation records, campaign accountability, security, legal compliance, fraud prevention, dispute resolution, or protection of other users and beneficiaries.</p>
    </section>

    <section id="visit-headquarters" class="info-section info-callout">
        <div class="info-callout__icon" aria-hidden="true">
            <i class="ri-map-pin-2-fill"></i>
        </div>
        <div>
            <h2>Visit Our Headquarters</h2>
            <p><strong>Address:</strong> 2nd Floor, Community Center Bldg, Rizal Street, Legazpi City, Albay, Philippines 4500</p>
            <p><strong>Hours:</strong> Monday - Friday: 9:00 AM - 5:00 PM</p>
            <p class="info-muted">Please schedule an appointment via email before visiting so the appropriate coordinator can assist you.</p>
        </div>
    </section>

    <section id="support-guidance" class="info-section">
        <h2>Support Guidance</h2>
        <p>For official concerns, please contact the Tulong Kabataan support team through the available contact channels. We aim to review concerns carefully and route them to the appropriate administrator, campaign organizer, volunteer coordinator, or partner where necessary.</p>
        <p>Please use respectful language and provide accurate information. False reports, abusive messages, fraudulent documents, or threats may result in account restrictions, campaign review, or other appropriate action under the Terms of Service.</p>
    </section>
@endsection
