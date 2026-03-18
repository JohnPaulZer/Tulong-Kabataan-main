<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tulong Kabataan Footer & Modals</title>
    <!-- Remix Icons -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #4f46e5;
            --primary-dark: #3730a3;
            --secondary-bg: #f8fafc;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --modal-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            --footer-bg: #1a1a2c;
            --footer-text: #b3b3cc;
        }

        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f1f5f9;
            box-sizing: border-box;
        }

        *, *:before, *:after {
            box-sizing: inherit;
        }

        /* ==================== FOOTER STYLES ==================== */
        .footer {
            background: var(--footer-bg);
            color: #fff;
            padding: 60px 0 30px 0;
            font-family: 'Inter', sans-serif;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
            display: flex;
            flex-direction: column;
            gap: 32px;
        }

        .footer-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 40px;
        }

        .footer-content-left {
            max-width: 450px;
        }

        .footer-logo {
            display: inline-block;
        }

        .footer-logo img {
            height: 70px;
            margin-bottom: 16px;
            display: block;
        }

        .footer-description {
            color: var(--footer-text);
            font-size: 15px;
            line-height: 1.7;
            margin: 0;
        }

        .footer-socials {
            display: flex;
            gap: 12px;
        }

        .footer-socials a {
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: #fff;
            font-size: 20px;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .footer-socials a:hover {
            background: var(--primary-color);
            transform: translateY(-4px);
            border-color: var(--primary-color);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.4);
        }

        hr {
            border: none;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            width: 100%;
            margin: 10px 0;
        }

        .footer-bottom {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            padding-top: 10px;
        }

        .footer-bottom p {
            color: #64748b;
            font-size: 14px;
            margin: 0;
        }

        .footer-links {
            display: flex;
            gap: 30px;
        }

        .footer-links a {
            color: #94a3b8;
            font-size: 14px;
            text-decoration: none;
            transition: color 0.2s;
            position: relative;
        }

        .footer-links a:hover {
            color: #fff;
        }

        .footer-links a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 1px;
            bottom: -2px;
            left: 0;
            background-color: var(--primary-color);
            transition: width 0.3s;
        }

        .footer-links a:hover::after {
            width: 100%;
        }

        /* Responsive Footer - Covers Tablets (up to 900px) and Mobile */
        @media (max-width: 900px) {
            .footer-top {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
            
            .footer-content-left {
                display: flex;
                flex-direction: column;
                align-items: center; /* Centers logo and text horizontally */
                text-align: center;
                max-width: 100%; /* Allow full width for centering */
            }

            .footer-bottom {
                flex-direction: column-reverse;
                text-align: center;
            }
            .footer-links {
                flex-wrap: wrap;
                justify-content: center;
                gap: 16px;
            }
        }

        /* ==================== MODAL SYSTEM ==================== */
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(8px);
            z-index: 9999;
            display: none;
            justify-content: center;
            align-items: center; /* Vertically center */
            padding: 16px; /* Ensures space from screen edges on mobile */
            animation: fadeIn 0.3s ease-out forwards;
        }

        .modal-content {
            background-color: #fff;
            /* Responsive Width Logic */
            width: 90%; /* Default: Take up 90% of screen width (creates margin) */
            max-width: 700px; /* Cap width on large screens */
            max-height: 85vh; /* Never exceed 85% of viewport height */
            
            border-radius: 16px;
            box-shadow: var(--modal-shadow);
            display: flex;
            flex-direction: column;
            position: relative;
            animation: slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            overflow: hidden;
            
            /* Safety margin for very small screens */
            margin: auto; 
        }

        /* Modal Header */
        .modal-header {
            padding: 20px 24px;
            background: #fff;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0; /* Prevents header from shrinking */
        }

        .modal-title {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .modal-title i {
            font-size: 24px;
            color: var(--primary-color);
            background: #e0e7ff;
            padding: 8px;
            border-radius: 8px;
        }

        .modal-title h3 {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            font-size: 1.25rem;
            color: var(--text-main);
            font-weight: 600;
        }

        .close {
            background: transparent;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            font-size: 24px;
            padding: 8px;
            border-radius: 50%;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .close:hover {
            background-color: #f1f5f9;
            color: var(--text-main);
        }

        /* Modal Body */
        .modal-body {
            padding: 24px;
            overflow-y: auto; /* Scrollable content */
            color: var(--text-main);
            line-height: 1.8;
            font-size: 0.95rem;
        }

        /* Custom Scrollbar */
        .modal-body::-webkit-scrollbar {
            width: 8px;
        }
        .modal-body::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        .modal-body::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        /* Typography inside Modal */
        .modal-body h4 {
            font-family: 'Poppins', sans-serif;
            color: var(--primary-color);
            font-size: 1.1rem;
            margin-top: 32px;
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e0e7ff;
            display: inline-block;
        }

        .modal-body h4:first-child {
            margin-top: 0;
        }

        .modal-body p {
            margin-bottom: 16px;
            color: #475569;
        }

        .modal-body ul {
            background: var(--secondary-bg);
            padding: 20px 20px 20px 40px;
            border-radius: 8px;
            margin-bottom: 24px;
        }

        .modal-body li {
            margin-bottom: 8px;
            color: #475569;
        }

        .last-updated {
            font-size: 0.85rem;
            color: var(--text-muted);
            font-style: italic;
            margin-bottom: 24px;
            display: block;
        }

        /* Modal Footer */
        .modal-footer {
            padding: 20px 24px;
            background: #f8fafc;
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            flex-shrink: 0; /* Prevents footer from shrinking */
        }

        .btn {
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            font-family: 'Inter', sans-serif;
        }

        .btn-secondary {
            background: #fff;
            border: 1px solid var(--border-color);
            color: var(--text-muted);
        }

        .btn-secondary:hover {
            background: #f1f5f9;
            color: var(--text-main);
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }

        /* ==================== CONTACT MODAL SPECIFIC ==================== */
        .contact-grid {
            display: grid;
            /* Auto-fit with minmax ensures responsive cards */
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); 
            gap: 16px;
            margin-bottom: 30px;
        }

        .contact-card {
            background: #fff;
            border: 1px solid var(--border-color);
            padding: 24px;
            border-radius: 12px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .contact-card:hover {
            border-color: var(--primary-color);
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05);
            transform: translateY(-2px);
        }

        .contact-icon-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 8px;
        }

        .bg-blue { background: #eff6ff; color: #3b82f6; }
        .bg-indigo { background: #eef2ff; color: #4f46e5; }
        .bg-orange { background: #fff7ed; color: #f97316; }

        .contact-card h5 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-main);
        }

        .contact-card p {
            margin: 0;
            font-size: 0.9rem;
            color: var(--text-muted);
            line-height: 1.5;
        }

        .contact-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--primary-color);
            font-weight: 500;
            font-size: 0.9rem;
            margin-top: auto;
            text-decoration: none;
        }

        .contact-link:hover {
            text-decoration: underline;
        }

        .visit-us-section {
            background: #f8fafc;
            border-radius: 12px;
            padding: 24px;
            border: 1px solid var(--border-color);
        }

        .visit-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 16px;
        }

        .visit-header i {
            color: var(--primary-color);
            font-size: 1.2rem;
        }
        
        .visit-header h5 {
            margin: 0;
            font-size: 1.1rem;
            color: var(--text-main);
        }

        .address-details p {
            margin-bottom: 8px;
            font-size: 0.95rem;
        }
        
        .address-details strong {
            color: var(--text-main);
        }

        /* Specific Mobile Overrides */
        @media (max-width: 600px) {
            .contact-grid {
                grid-template-columns: 1fr; /* Force single column on very small screens */
            }

            .modal-footer {
                flex-direction: column-reverse; /* Stack buttons on mobile */
            }
            
            .btn {
                width: 100%;
                display: flex;
                justify-content: center;
            }
        }
    </style>
</head>
<body>

    <!-- Main Footer -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-top">
                <div class="footer-content-left">
                    <a href="#" class="footer-logo">
                        <!-- RESTORED LOGO AS REQUESTED -->
                        <img src="{{ asset('img/log1.png') }}" alt="Tulong Kabataan Logo">
                    </a>
                    <p class="footer-description">
                        Empowering the next generation. Connecting generosity with community needs through secure donations and meaningful volunteer opportunities across the Bicol region.
                    </p>
                </div>

                <div class="footer-socials">
                    <a href="https://www.facebook.com/tulongkabataanbicol" target="_blank" aria-label="Facebook">
                        <i class="ri-facebook-fill"></i>
                    </a>
                    <a href="https://x.com/TulongKab_Bicol" target="_blank" aria-label="Twitter/X">
                        <i class="ri-twitter-x-fill"></i>
                    </a>
                    <a href="#" aria-label="Instagram">
                        <i class="ri-instagram-line"></i>
                    </a>
                </div>
            </div>

            <hr>

            <div class="footer-bottom">
                <p>&copy; 2025 Tulong Kabataan. All rights reserved.</p>
                <div class="footer-links">
                    <a href="javascript:void(0)" onclick="openModal('privacy')">Privacy Policy</a>
                    <a href="javascript:void(0)" onclick="openModal('terms')">Terms of Service</a>
                    <a href="javascript:void(0)" onclick="openModal('contact')">Contact Us</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- ==================== PRIVACY POLICY MODAL ==================== -->
    <div id="privacyModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">
                    <i class="ri-shield-check-line"></i>
                    <h3>Privacy Policy</h3>
                </div>
                <button class="close" onclick="closeModal('privacy')"><i class="ri-close-line"></i></button>
            </div>
            <div class="modal-body">
                <span class="last-updated">Last Updated: October 24, 2025</span>
                
                <p>At Tulong Kabataan ("we," "our," or "us"), we are committed to protecting your privacy and ensuring the security of your personal information. This Privacy Policy outlines how we collect, use, disclosure, and safeguard your data when you visit our website or participate in our volunteer and donation programs.</p>

                <h4>1. Information We Collect</h4>
                <p>We may collect personal information that you voluntarily provide to us when you register on the website, express an interest in obtaining information about us or our products and services, when you participate in activities on the website (such as posting messages in our online forums or entering competitions, contests or giveaways) or otherwise when you contact us.</p>
                <ul>
                    <li><strong>Personal Identity Information:</strong> Name, email address, phone number, and mailing address.</li>
                    <li><strong>Financial Data:</strong> We do not store credit card details on our servers. All donation transactions are processed through secure third-party payment gateways (e.g., GCash, PayMaya, Stripe).</li>
                    <li><strong>Volunteer Data:</strong> Skills, availability, and emergency contact information provided during volunteer registration.</li>
                </ul>

                <h4>2. How We Use Your Information</h4>
                <p>We use the information we collect or receive:</p>
                <ul>
                    <li>To facilitate account creation and logon processes.</li>
                    <li>To send you administrative information, such as updates to our terms, conditions, and policies.</li>
                    <li>To fulfill and manage your donations and volunteer schedules.</li>
                    <li>To request feedback and contact you about your use of our services.</li>
                    <li>To enforce our terms, conditions, and policies for business purposes, to comply with legal and regulatory requirements or in connection with our contract.</li>
                </ul>

                <h4>3. Disclosure of Your Information</h4>
                <p>We may share information we have collected about you in certain situations. Your information may be disclosed as follows:</p>
                <ul>
                    <li><strong>By Law or to Protect Rights:</strong> If we believe the release of information about you is necessary to respond to legal process, to investigate or remedy potential violations of our policies, or to protect the rights, property, and safety of others.</li>
                    <li><strong>Third-Party Service Providers:</strong> We may share your information with third parties that perform services for us or on our behalf, including payment processing, data analysis, email delivery, hosting services, customer service, and marketing assistance.</li>
                </ul>

                <h4>4. Data Security</h4>
                <p>We use administrative, technical, and physical security measures to help protect your personal information. While we have taken reasonable steps to secure the personal information you provide to us, please be aware that despite our efforts, no security measures are perfect or impenetrable, and no method of data transmission can be guaranteed against any interception or other type of misuse.</p>

                <h4>5. Contact Us</h4>
                <p>If you have questions or comments about this policy, you may email us at privacy@tulongkabataan.ph or by post to our office address listed in the Contact section.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal('privacy')">Close</button>
                <button class="btn btn-primary" onclick="closeModal('privacy')">I Understand</button>
            </div>
        </div>
    </div>

    <!-- ==================== TERMS OF SERVICE MODAL ==================== -->
    <div id="termsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">
                    <i class="ri-file-text-line"></i>
                    <h3>Terms of Service</h3>
                </div>
                <button class="close" onclick="closeModal('terms')"><i class="ri-close-line"></i></button>
            </div>
            <div class="modal-body">
                <span class="last-updated">Effective Date: January 1, 2025</span>
                
                <p>Welcome to Tulong Kabataan. By accessing our website, making a donation, or signing up as a volunteer, you agree to be bound by these Terms of Service. Please read them carefully.</p>

                <h4>1. User Responsibilities</h4>
                <p>By using our platform, you represent and warrant that:</p>
                <ul>
                    <li>All registration information you submit will be true, accurate, current, and complete.</li>
                    <li>You will maintain the accuracy of such information and promptly update such registration information as necessary.</li>
                    <li>You have the legal capacity and you agree to comply with these Terms of Service.</li>
                    <li>You will not use the Site for any illegal or unauthorized purpose.</li>
                </ul>

                <h4>2. Donations and Refunds</h4>
                <p>All donations made through Tulong Kabataan are final. However, if you believe that an error has been made in connection with your online donation, contact us immediately. Refunds are returned using the original method of payment. If you made your donation by credit card, your refund will be credited to that same credit card.</p>
                
                <h4>3. Volunteer Conduct</h4>
                <p>As a volunteer representing Tulong Kabataan, you agree to:</p>
                <ul>
                    <li>Treat all beneficiaries, staff, and fellow volunteers with respect and dignity.</li>
                    <li>Adhere to the safety protocols and guidelines provided during orientation.</li>
                    <li>Not engage in any form of harassment, discrimination, or offensive behavior.</li>
                    <li>Maintain confidentiality regarding the personal information of beneficiaries.</li>
                </ul>

                <h4>4. Intellectual Property Rights</h4>
                <p>Unless otherwise indicated, the Site is our proprietary property and all source code, databases, functionality, software, website designs, audio, video, text, photographs, and graphics on the Site (collectively, the “Content”) and the trademarks, service marks, and logos contained therein (the “Marks”) are owned or controlled by us or licensed to us, and are protected by copyright and trademark laws.</p>

                <h4>5. Limitation of Liability</h4>
                <p>In no event will we or our directors, employees, or agents be liable to you or any third party for any direct, indirect, consequential, exemplary, incidental, special, or punitive damages, including lost profit, lost revenue, loss of data, or other damages arising from your use of the site, even if we have been advised of the possibility of such damages.</p>

                <h4>6. Modifications to Terms</h4>
                <p>We reserve the right to change, modify, or remove the contents of the Site at any time or for any reason at our sole discretion without notice. We also reserve the right to modify these Terms of Service at any time.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal('terms')">Decline</button>
                <button class="btn btn-primary" onclick="acceptTerms()">I Agree</button>
            </div>
        </div>
    </div>

    <!-- ==================== CONTACT US MODAL ==================== -->
    <div id="contactModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">
                    <i class="ri-customer-service-2-line"></i>
                    <h3>Get in Touch</h3>
                </div>
                <button class="close" onclick="closeModal('contact')"><i class="ri-close-line"></i></button>
            </div>
            <div class="modal-body">
                <p style="margin-bottom: 30px;">
                    Have questions about our programs, want to partner with us, or need assistance with a donation? 
                    Our team is here to help. Choose your preferred method below.
                </p>

                <!-- Grid Layout for Contact Options -->
                <div class="contact-grid">
                    
                    <!-- Email Card -->
                    <div class="contact-card">
                        <div class="contact-icon-wrapper bg-blue">
                            <i class="ri-mail-send-line"></i>
                        </div>
                        <h5>Email Support</h5>
                        <p>For general inquiries, partnerships, and donation concerns.</p>
                        <a href="mailto:tulongkabataan.bicol@gmail.com" class="contact-link">
                            tulongkabataan.bicol@gmail.com <i class="ri-arrow-right-line"></i>
                        </a>
                    </div>

                    <!-- Social Card -->
                    <div class="contact-card">
                        <div class="contact-icon-wrapper bg-indigo">
                            <i class="ri-messenger-line"></i>
                        </div>
                        <h5>Social Media</h5>
                        <p>Message us on Facebook for the fastest response time.</p>
                        <a href="https://www.facebook.com/tulongkabataanbicol" target="_blank" class="contact-link">
                            @tulongkabataanbicol <i class="ri-arrow-right-line"></i>
                        </a>
                    </div>

                    <!-- Emergency Card -->
                    <div class="contact-card">
                        <div class="contact-icon-wrapper bg-orange">
                            <i class="ri-alarm-warning-line"></i>
                        </div>
                        <h5>Emergency Relief</h5>
                        <p>Urgent coordination regarding ongoing calamity operations.</p>
                        <a href="tel:+639123456789" class="contact-link">
                            +63 912 345 6789 <i class="ri-arrow-right-line"></i>
                        </a>
                    </div>
                </div>

                <!-- Visit Us Section -->
                <div class="visit-us-section">
                    <div class="visit-header">
                        <i class="ri-map-pin-2-fill"></i>
                        <h5>Visit Our Headquarters</h5>
                    </div>
                    <div class="address-details">
                        <p><strong>Address:</strong> 2nd Floor, Community Center Bldg, Rizal Street, Legazpi City, Albay, Philippines 4500</p>
                        <p><strong>Hours:</strong> Monday - Friday: 9:00 AM - 5:00 PM</p>
                        <p style="margin-bottom: 0; color: var(--text-muted); font-size: 0.85rem;">*Please schedule an appointment via email before visiting.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal('contact')">Close</button>
            </div>
        </div>
    </div>

    <script>
        // Modal Logic
        const modals = {
            privacy: document.getElementById('privacyModal'),
            terms: document.getElementById('termsModal'),
            contact: document.getElementById('contactModal')
        };

        function openModal(type) {
            if (modals[type]) {
                modals[type].style.display = 'flex';
                document.body.style.overflow = 'hidden'; // Prevent background scrolling
            }
        }

        function closeModal(type) {
            if (modals[type]) {
                modals[type].style.display = 'none';
                document.body.style.overflow = 'auto'; // Restore scrolling
            }
        }

        function acceptTerms() {
            // Add logic to save acceptance (e.g., localStorage or API call)
            alert("Thank you for accepting the Terms of Service.");
            closeModal('terms');
        }

        // Close modal when clicking outside content
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        }

        // Close on Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                Object.values(modals).forEach(modal => {
                    modal.style.display = 'none';
                });
                document.body.style.overflow = 'auto';
            }
        });
    </script>
</body>
</html>