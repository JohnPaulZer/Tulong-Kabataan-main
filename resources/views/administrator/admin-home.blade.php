<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tulong Kabataan | Administrator Dashboard</title>
    <link rel="icon" href="{{ asset('img/log2.png') }}" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <!-- Google Fonts: Playfair Display & Open Sans -->
    <link
        href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,300;0,400;0,700;0,900;1,400&family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap"
        rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.5.0/echarts.min.js"></script>

    <style>
        :root {
            --primary: #3b82f6;
            --bg: #f9fafb;
            /* gray-50 */
            --text: #111827;
            /* gray-900 */
            --muted: #6b7280;
            /* gray-500 */
            --muted-700: #374151;
            /* gray-700 */
            --border: #e5e7eb;
            /* gray-200 */
            --card: #ffffff;
            --shadow: 0 1px 2px rgba(0, 0, 0, .06), 0 1px 3px rgba(0, 0, 0, .1);
            --radius: 12px;
            --header-h: 64px;
            /* 16 * 4 */
            --sidebar-w: 256px;
            /* 64 * 4 */
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        .heading-font,
        .section-title,
        .featured-title,
        .campaign-title,
        .btn,
        button,
        .raised,
        .campaign-count,
        .featured-badge,
        .pagination-number {
            font-family: "Poppins", sans-serif;
            letter-spacing: -0.01em;
        }

        /* 2. Secondary Font Selectors */
        p,
        a,
        li,
        span,
        input,
        select,
        textarea,
        label,
        .body-font,
        .section-desc,
        .featured-desc,
        .filters-label,
        .goal,
        .pagination-info {
            font-family: "Lato", sans-serif;
            font-weight: 400;
            text-decoration: none;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
        }

        body {
            margin: 0;
            font-family: 'Lato', sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.45;
        }

        /* Header */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: var(--header-h);
            background: #fff;
            /* gray-900 */
            color: #fff;
            z-index: 50;
            box-shadow: var(--shadow);
        }

        .header__inner {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 16px;
        }

        .header__left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .menu-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border: 0;
            background: #1f2937;
            color: #fff;
            border-radius: 8px;
            cursor: pointer;
        }

        .menu-btn:hover {
            background: #374151;
        }

        .brand {
            font-weight: 1000;
            font-size: 25px;
            letter-spacing: .06em;
            color: black;
        }

        .logo-word {
            font-family: "Pacifico", cursive;
            color: var(--primary);
            font-size: 24px;
        }

        .notif {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: white;
            color: #000000;
            border: 0;
            cursor: pointer;
        }


        /* Sidebar */
        .sidebar {
            position: fixed;
            left: 0;
            top: var(--header-h);
            bottom: 0;
            width: var(--sidebar-w);
            background: #1e3a8a;
            /* blue-800 */
            color: #fff;
            transform: translateX(-100%);
            transition: transform .3s ease;
            z-index: 40;
            padding: 16px;
            overflow: auto;
        }

        .sidebar.open {
            transform: translateX(0);
        }

        .sidebar nav {
            display: grid;
            gap: 8px;
        }

        .side-btn,
        .side-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 8px;
            background: transparent;
            border: 0;
            color: #d1d5db;
            /* gray-300 */
            text-decoration: none;
            cursor: pointer;
            white-space: nowrap;
        }

        .side-btn.primary {
            background: var(--primary);
            color: #fff;
        }

        .side-btn:hover,
        .side-link:hover {
            background: #1e40af;
        }

        /* blue-700 */

        /* Sidebar overlay (mobile) */
        .overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .5);
            z-index: 30;
            display: none;
        }

        .overlay.show {
            display: block;
        }

        /* Main */
        .main {
            padding-top: var(--header-h);
            min-height: 100vh;
        }

        @media (min-width:1024px) {
            .main {
                margin-left: var(--sidebar-w);
            }

            .menu-btn {
                display: none;
            }

            .sidebar {
                transform: translateX(0);
            }

            .overlay {
                display: none !important;
            }

            .header__inner {
                padding: 0 24px;
            }
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 24px 16px;
        }


        .side-link.active,
        .side-btn.active {
            background: var(--primary);
            color: #fff !important;
        }

        /* --- Custom Styles for Accomplishments --- */

        /* --- 📈 Enhanced Styles for Accomplishments --- */

        .section-header {
            display: flex;
            align-items: center;
            margin-bottom: 28px;
            /* Increased vertical spacing */
            padding: 12px 0;
            /* Increased vertical padding */
            border-bottom: 1px solid var(--border);
            /* Thinner, lighter divider */
        }

        .section-header i {
            font-size: 30px;
            /* Slightly larger icon */
            color: var(--primary);
            margin-right: 16px;
            /* Increased spacing between icon and text */
        }

        .section-header h2 {
            /* Assuming 'Playfair Display' is a strong, decorative font */
            font-family: 'Playfair Display', serif;
            font-size: 26px;
            /* Slightly larger heading */
            color: var(--text);
            margin: 0;
            font-weight: 700;
        }

        .accomplishments-grid {
            display: grid;
            /* Increased minimum column size and gap for better use of space */
            grid-template-columns: repeat(auto-fill, minmax(310px, 1fr));
            gap: 32px;
            margin-bottom: 48px;
        }

        .accomplishment-card {
            background: var(--card);
            border-radius: var(--radius);
            /* Subtle initial shadow */
            box-shadow: 0 1px 3px rgba(0, 0, 0, .05);
            overflow: hidden;
            border: 1px solid var(--border);
            /* Smoother transition */
            transition: transform 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94), box-shadow 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94), border-color 0.3s;
            cursor: pointer;
        }

        .accomplishment-card:hover {
            transform: translateY(-4px);
            /* Reduced lift for subtlety */
            /* More depth on hover using the original shadow variable */
            box-shadow: var(--shadow);
            border-color: var(--primary);
            /* Highlight with primary color on hover */
        }

        .accomplishment-card__image {
            width: 100%;
            height: 160px;
            /* Reduced height for content focus */
            background: var(--bg);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--muted);
            font-weight: 500;
            /* Slightly lighter weight for placeholder text */
            overflow: hidden;
        }

        .accomplishment-card__image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .accomplishment-card__content {
            padding: 20px;
            /* Increased inner padding */
        }

        .accomplishment-card__content h3 {
            /* Replaced 'Open Sans' with a standard sans-serif for consistency if 'Inter' is used in body */
            font-family: inherit;
            font-size: 20px;
            /* Larger title */
            font-weight: 700;
            color: var(--primary);
            margin-top: 0;
            margin-bottom: 6px;
            line-height: 1.3;
        }

        .accomplishment-card__content p {
            font-size: 15px;
            /* Slightly larger, more readable font size */
            color: var(--muted-700);
            margin-bottom: 16px;
            /* Increased spacing before the list */
            line-height: 1.4;
        }

        .accomplishment-card__content ul {
            list-style: none;
            padding: 0;
            margin: 0;
            font-size: 14px;
        }

        .accomplishment-card__content ul li {
            margin-bottom: 8px;
            /* Increased list item spacing */
            display: flex;
            align-items: flex-start;
            /* Aligns icons properly, even with wrapped text */
            font-weight: 500;
        }

        .accomplishment-card__content ul li i {
            color: var(--primary);
            margin-right: 10px;
            /* Increased spacing */
            font-size: 16px;
            /* Larger icon for visibility */
            /* Ensure icon stays aligned at the top */
            min-width: 16px;
        }

        /* --- TABS Styles --- */
        .tabs-nav {
            display: flex;
            gap: 10px;
            margin-bottom: 24px;
            overflow-x: auto;
            /* Allow horizontal scrolling on mobile */
            white-space: nowrap;
            padding-bottom: 5px;
            /* Space for scrollbar if needed */
            border-bottom: 2px solid var(--border);
            -ms-overflow-style: none;
            /* IE and Edge */
            scrollbar-width: none;
            /* Firefox */
        }

        .tabs-nav::-webkit-scrollbar {
            display: none;
            /* Chrome, Safari, Opera */
        }

        .tab-btn {
            background: none;
            border: none;
            padding: 12px 20px;
            font-family: "Poppins", sans-serif;
            font-size: 15px;
            font-weight: 600;
            color: var(--muted);
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
            outline: none;
        }

        .tab-btn:hover {
            color: var(--primary);
            background-color: rgba(59, 130, 246, 0.05);
            border-radius: 8px 8px 0 0;
        }

        .tab-btn.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
        }

        .tab-content {
            display: none;
            animation: fadeIn 0.4s ease-in-out;
        }

        .tab-content.active {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* --- Pagination & Filter Styles --- */
        .controls-row {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 20px;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            color: var(--muted-700);
        }

        .controls-row select {
            padding: 6px 12px;
            border: 1px solid var(--border);
            border-radius: 6px;
            background: #fff;
            color: var(--text);
            cursor: pointer;
            outline: none;
        }

        .controls-row select:focus {
            border-color: var(--primary);
        }

        .pagination-controls {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-top: 30px;
            margin-bottom: 10px;
        }

        .pagination-btn {
            padding: 8px 14px;
            min-width: 36px;
            border: 1px solid var(--border);
            background: #fff;
            color: var(--text);
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
            font-family: 'Lato', sans-serif;
            font-size: 14px;
            display: inline-flex;
            justify-content: center;
            align-items: center;
        }

        .pagination-btn:hover:not(:disabled) {
            border-color: var(--primary);
            color: var(--primary);
            background: #eff6ff;
        }

        .pagination-btn.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .pagination-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background: #f3f4f6;
        }

        /* Report Header */
        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--border);
        }

        .report-header h3 {
            font-size: 18px;
            font-weight: 600;
            color: var(--text);
            margin: 0;
            line-height: 1.3;
            flex: 1;
        }

        .report-year {
            background: var(--primary);
            color: white;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-left: 12px;
            white-space: nowrap;
        }

        /* Report Description */
        .report-description {
            font-size: 14px;
            color: var(--muted-700);
            line-height: 1.5;
            margin-bottom: 20px;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            margin-bottom: 20px;
            padding: 16px;
            background: #f8fafc;
            border-radius: 8px;
            border: 1px solid var(--border);
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .stat-icon {
            width: 36px;
            height: 36px;
            background: var(--primary);
            color: white;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .stat-icon i {
            font-size: 18px;
        }

        .stat-content {
            display: flex;
            flex-direction: column;
        }

        .stat-value {
            font-size: 16px;
            font-weight: 700;
            color: var(--text);
            line-height: 1.2;
        }

        .stat-label {
            font-size: 11px;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        /* Report Date */
        .report-date {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--muted);
            font-size: 13px;
            margin-bottom: 20px;
            padding: 8px 12px;
            background: #f8fafc;
            border-radius: 6px;
            border: 1px solid var(--border);
        }

        .report-date i {
            font-size: 16px;
        }

        /* Donated Items Section */
        .donated-section {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid var(--border);
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 16px;
        }

        .section-title i {
            color: var(--primary);
            font-size: 20px;
        }

        .section-title h4 {
            font-size: 16px;
            font-weight: 600;
            color: var(--text);
            margin: 0;
        }

        /* Donated Items Table */
        .donated-items-list {
            max-height: 250px;
            overflow-y: auto;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: white;
        }

        .donated-items-list table {
            width: 100%;
            border-collapse: collapse;
        }

        .donated-items-list thead {
            position: sticky;
            top: 0;
            z-index: 1;
        }

        .donated-items-list th {
            background: #f8fafc;
            padding: 12px 16px;
            font-size: 12px;
            font-weight: 600;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        .donated-items-list th.text-center {
            text-align: center;
        }

        .donated-items-list td {
            padding: 12px 16px;
            border-bottom: 1px solid var(--border);
            vertical-align: top;
        }

        .donated-items-list tr:last-child td {
            border-bottom: none;
        }

        /* Item Name Cell */
        .item-name {
            min-width: 150px;
        }

        .item-primary {
            font-weight: 600;
            color: var(--text);
            margin-bottom: 4px;
            font-size: 14px;
        }

        .item-category {
            font-size: 11px;
            color: var(--muted);
            background: #f1f5f9;
            padding: 2px 8px;
            border-radius: 4px;
            display: inline-block;
            font-weight: 500;
        }

        /* Quantity Badge */
        .quantity-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            background: var(--primary);
            color: white;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }

        /* Donor Info Cell */
        .donor-info {
            min-width: 180px;
        }

        .donor-name {
            font-weight: 600;
            color: var(--text);
            margin-bottom: 4px;
            font-size: 14px;
        }

        .donor-contact {
            font-size: 11px;
            color: var(--muted);
            line-height: 1.3;
        }

        .contact-email {
            word-break: break-all;
        }

        .contact-phone {
            white-space: nowrap;
        }

        /* No Data State */
        .no-data-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 32px 16px;
            text-align: center;
            color: var(--muted);
            border: 2px dashed var(--border);
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .no-data-state i {
            font-size: 48px;
            margin-bottom: 16px;
            opacity: 0.5;
        }

        .no-data-state p {
            margin: 0;
            font-size: 14px;
            font-weight: 500;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
                gap: 12px;
                padding: 12px;
            }

            .donated-items-list {
                max-height: 200px;
            }

            .donated-items-list th,
            .donated-items-list td {
                padding: 10px 12px;
            }

            .report-header {
                flex-direction: column;
                gap: 8px;
            }

            .report-year {
                align-self: flex-start;
                margin-left: 0;
            }
        }

        /* Table Scrollbar Styling */
        .donated-items-list::-webkit-scrollbar {
            width: 6px;
        }

        .donated-items-list::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 0 8px 8px 0;
        }

        .donated-items-list::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        .donated-items-list::-webkit-scrollbar-thumb:hover {
            background: var(--primary);
        }

        /* Event Status Badge in Image */
        .event-status {
            position: absolute;
            top: 12px;
            right: 12px;
            background: rgba(255, 255, 255, 0.95);
            padding: 6px 12px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 600;
            color: var(--text);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(4px);
        }

        .event-status i {
            color: var(--primary);
            font-size: 14px;
        }

        /* Event Date Section */
        .event-date {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 20px;
            padding: 12px;
            background: #f8fafc;
            border-radius: 8px;
            border: 1px solid var(--border);
        }

        .date-range,
        .event-location {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: var(--text);
        }

        .date-range i,
        .event-location i {
            color: var(--primary);
            font-size: 16px;
            flex-shrink: 0;
        }

        .date-range span,
        .event-location span {
            font-weight: 500;
        }

        /* Event Details Section */
        .event-details-section {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid var(--border);
        }

        .details-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 20px;
        }

        .detail-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 10px;
            background: white;
            border-radius: 6px;
            border: 1px solid var(--border);
        }

        .detail-label {
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 120px;
            flex-shrink: 0;
        }

        .detail-label i {
            color: var(--primary);
            font-size: 16px;
        }

        .detail-label span {
            font-size: 13px;
            font-weight: 600;
            color: var(--muted);
        }

        .detail-value {
            flex: 1;
            font-size: 14px;
            color: var(--text);
            font-weight: 500;
            word-break: break-word;
        }

        .contact-email,
        .contact-phone {
            font-size: 13px;
            color: var(--muted);
        }

        /* Roles Section */
        .roles-section {
            margin-top: 20px;
        }

        .roles-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .role-item {
            padding: 10px 12px;
            background: #f8fafc;
            border-radius: 6px;
            border: 1px solid var(--border);
            transition: all 0.2s;
        }

        .role-item:hover {
            background: #f1f5f9;
            border-color: var(--primary);
        }

        .role-name {
            font-weight: 600;
            color: var(--text);
            font-size: 14px;
            margin-bottom: 4px;
        }

        .role-details {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 12px;
        }

        .role-slots {
            background: var(--primary);
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-weight: 600;
        }

        .role-desc {
            color: var(--muted);
            font-style: italic;
        }

        /* Success Metrics */
        .success-metrics {
            margin-top: 20px;
            padding: 16px;
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border-radius: 8px;
            border: 1px solid #bae6fd;
        }

        .metrics-header {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 16px;
        }

        .metrics-header i {
            color: var(--primary);
            font-size: 20px;
        }

        .metrics-header h4 {
            font-size: 16px;
            font-weight: 600;
            color: var(--text);
            margin: 0;
        }

        .metrics-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 16px;
        }

        .metric {
            text-align: center;
            padding: 12px;
            background: white;
            border-radius: 6px;
            border: 1px solid #bae6fd;
        }

        .metric-value {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary);
            line-height: 1;
            margin-bottom: 6px;
        }

        .metric-label {
            font-size: 12px;
            color: var(--muted);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Empty State */
        .empty-state {
            grid-column: 1 / -1;
            text-align: center;
            padding: 48px 24px;
            background: white;
            border-radius: var(--radius);
            border: 2px dashed var(--border);
        }

        .empty-state-icon {
            font-size: 64px;
            color: var(--muted);
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .empty-state h3 {
            font-size: 20px;
            color: var(--text);
            margin-bottom: 12px;
            font-weight: 600;
        }

        .empty-state p {
            color: var(--muted);
            font-size: 15px;
            max-width: 400px;
            margin: 0 auto;
            line-height: 1.5;
        }

        /* Truncate for long location names */
        .truncate {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
                padding: 12px;
            }

            .detail-item {
                flex-direction: column;
                gap: 8px;
            }

            .detail-label {
                min-width: auto;
            }

            .metrics-content {
                grid-template-columns: 1fr;
            }

            .metric-value {
                font-size: 20px;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    @include('administrator.partials.loading-screen')

    <header class="header">
        <div class="header__inner">
            <div class="header__left">
                <button id="sidebarToggle" class="menu-btn" aria-label="Toggle menu">
                    <i class="ri-menu-line"></i>
                </button>
                <h1 class="brand">ADMINISTRATOR</h1>
            </div>

            <div class="logo-word"><img src="{{ asset('img/log.png') }}" alt=""
                    style="width: 120px; height: 60px; margin-top: 8px;">
            </div>

            <button class="notif" aria-label="Notifications">

            </button>
        </div>
    </header>

    {{-- Sidebar --}}
    @include('administrator.partials.main-sidebar')

    <div id="sidebarOverlay" class="overlay" aria-hidden="true"></div>

    <main class="main">
        <div class="container">
            <h1>Tulong Kabataan Directory of Activities</h1>

            <!-- TAB NAVIGATION -->
            <div class="tabs-nav">
                <button class="tab-btn active" onclick="openTab(event, 'events')">Successful Events</button>
                <button class="tab-btn" onclick="openTab(event, 'donation')">Donation Impact</button>
                <button class="tab-btn" onclick="openTab(event, 'campaigns')">Major Campaigns</button>
            </div>

            <!-- TAB CONTENT 2: Major Events -->
            <div id="events" class="tab-content active">
                <div class="section-header">
                    <i class="ri-calendar-event-line"></i>
                    <h2>Successful Events</h2>
                </div>


                <section class="accomplishments-grid">
                    @forelse($endedEvents as $event)
                        <article class="accomplishment-card">
                            <div class="accomplishment-card__image">
                                @if ($event->photo)
                                    <img src="{{ asset('storage/' . $event->photo) }}" alt="{{ $event->title }}">
                                @else
                                    <img src="{{ asset('img/camp.jpg') }}" alt="{{ $event->title }}">
                                @endif
                                @if ($event->volunteerRoles && $event->volunteerRoles->count() > 0)
                                    <div class="event-status">
                                        <i class="ri-team-line"></i>
                                        <span>{{ $event->volunteerRoles->count() }} Roles</span>
                                    </div>
                                @endif
                            </div>

                            <div class="accomplishment-card__content">
                                <!-- Event Header -->
                                <div class="report-header">
                                    <h3>{{ $event->title }}</h3>
                                    <span
                                        class="report-year">{{ \Carbon\Carbon::parse($event->end_date)->format('Y') }}</span>
                                </div>

                                <!-- Description -->
                                <p class="report-description">{{ Str::limit($event->description, 150) }}</p>

                                <!-- Stats Grid -->
                                <div class="stats-grid">
                                    @if ($event->registrations && $event->registrations->count() > 0)
                                        <div class="stat-item">
                                            <div class="stat-icon">
                                                <i class="ri-user-line"></i>
                                            </div>
                                            <div class="stat-content">
                                                <span class="stat-value">{{ $event->registrations->count() }}</span>
                                                <span class="stat-label">Volunteers</span>
                                            </div>
                                        </div>
                                    @endif

                                    @if ($event->volunteerRoles && $event->volunteerRoles->count() > 0)
                                        <div class="stat-item">
                                            <div class="stat-icon">
                                                <i class="ri-briefcase-line"></i>
                                            </div>
                                            <div class="stat-content">
                                                <span class="stat-value">{{ $event->volunteerRoles->count() }}</span>
                                                <span class="stat-label">Roles</span>
                                            </div>
                                        </div>
                                    @endif

                                    @if ($event->location)
                                        <div class="stat-item">
                                            <div class="stat-icon">
                                                <i class="ri-map-pin-line"></i>
                                            </div>
                                            <div class="stat-content">
                                                <span
                                                    class="stat-value truncate">{{ Str::limit($event->location, 12) }}</span>
                                                <span class="stat-label">Location</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Event Date -->
                                <div class="event-date">
                                    <div class="date-range">
                                        <i class="ri-calendar-line"></i>
                                        <span>
                                            {{ \Carbon\Carbon::parse($event->start_date)->format('F j') }}
                                            @if ($event->end_date && $event->end_date != $event->start_date)
                                                - {{ \Carbon\Carbon::parse($event->end_date)->format('j, Y') }}
                                            @else
                                                {{ \Carbon\Carbon::parse($event->start_date)->format(', Y') }}
                                            @endif
                                        </span>
                                    </div>
                                    @if ($event->location)
                                        <div class="event-location">
                                            <i class="ri-map-pin-2-line"></i>
                                            <span>{{ $event->location }}</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Coordinator & Details -->
                                @if ($event->coordinator_name || $event->volunteerRoles)
                                    <div class="event-details-section">
                                        <div class="section-title">
                                            <i class="ri-information-line"></i>
                                            <h4>Event Details</h4>
                                        </div>

                                        <div class="details-list">
                                            @if ($event->coordinator_name)
                                                <div class="detail-item">
                                                    <div class="detail-label">
                                                        <i class="ri-user-star-line"></i>
                                                        <span>Coordinator:</span>
                                                    </div>
                                                    <div class="detail-value">{{ $event->coordinator_name }}</div>
                                                </div>
                                                @if ($event->coordinator_email || $event->coordinator_phone)
                                                    <div class="detail-item">
                                                        <div class="detail-label">
                                                            <i class="ri-contacts-line"></i>
                                                            <span>Contact:</span>
                                                        </div>
                                                        <div class="detail-value">
                                                            @if ($event->coordinator_email)
                                                                <span
                                                                    class="contact-email">{{ $event->coordinator_email }}</span>
                                                            @endif
                                                            @if ($event->coordinator_phone)
                                                                @if ($event->coordinator_email)
                                                                    •
                                                                @endif
                                                                <span
                                                                    class="contact-phone">{{ $event->coordinator_phone }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
                                            @endif

                                            @if ($event->deadline)
                                                <div class="detail-item">
                                                    <div class="detail-label">
                                                        <i class="ri-time-line"></i>
                                                        <span>Deadline:</span>
                                                    </div>
                                                    <div class="detail-value">
                                                        {{ \Carbon\Carbon::parse($event->deadline)->format('F j, Y') }}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Volunteer Roles - CORRECTED -->
                                        @if ($event->volunteerRoles && $event->volunteerRoles->count() > 0)
                                            <div class="roles-section">
                                                <div class="section-title">
                                                    <i class="ri-team-line"></i>
                                                    <h4>Volunteer Roles</h4>
                                                </div>

                                                <div class="roles-list">
                                                    @foreach ($event->volunteerRoles as $role)
                                                        <div class="role-item">
                                                            <div class="role-name">{{ $role->name }}</div>
                                                            @if ($role->description)
                                                                <div class="role-details">
                                                                    <span
                                                                        class="role-desc">{{ Str::limit($role->description, 50) }}</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                <!-- Success Metrics -->
                                @if ($event->registrations && $event->registrations->count() > 0)
                                    <div class="success-metrics">
                                        <div class="metrics-header">
                                            <i class="ri-award-line"></i>
                                            <h4>Event Success</h4>
                                        </div>
                                        <div class="metrics-content">
                                            <div class="metric">
                                                <div class="metric-value">{{ $event->registrations->count() }}</div>
                                                <div class="metric-label">Total Volunteers</div>
                                            </div>
                                            @if ($event->volunteerRoles && $event->volunteerRoles->count() > 0)
                                                <div class="metric">
                                                    <div class="metric-value">
                                                        {{ $event->volunteerRoles->count() }}
                                                    </div>
                                                    <div class="metric-label">Available Roles</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </article>
                    @empty
                        <!-- Empty State -->
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="ri-calendar-event-line"></i>
                            </div>
                            <h3>No Past Events Yet</h3>
                            <p>There are no completed events to display at the moment.</p>
                        </div>
                    @endforelse
                </section>

                <!-- Pagination Container -->
                <div class="pagination-controls"></div>
            </div>

            <!-- TAB CONTENT 3: Donation Impact -->
            <div id="donation" class="tab-content">
                <div class="section-header">
                    <i class="ri-heart-line"></i>
                    <h2>Donation Impact</h2>
                </div>



                <section class="accomplishments-grid">
                    @foreach ($impactReports as $report)
                        <article class="accomplishment-card">
                            <div class="accomplishment-card__image">
                                @if ($report->photos && count($report->photos) > 0)
                                    @php
                                        $firstPhoto = is_array($report->photos) ? $report->photos[0] : $report->photos;
                                    @endphp
                                    <img src="{{ asset('storage/' . $firstPhoto) }}" alt="{{ $report->title }}">
                                @else
                                    <img src="{{ asset('img/inkind.png') }}" alt="{{ $report->title }}">
                                @endif
                            </div>

                            <div class="accomplishment-card__content">
                                <!-- Report Header -->
                                <div class="report-header">
                                    <h3>{{ $report->title }}</h3>
                                    <span class="report-year">{{ $report->report_date->format('Y') }}</span>
                                </div>

                                <!-- Description -->
                                <p class="report-description">{{ Str::limit($report->description, 150) }}</p>

                                <!-- Summary Stats -->
                                @if ($report->donations->count() > 0)
                                    @php
                                        $totalFamilies = $report->donations->sum('families_benefited');
                                        $totalValue = $report->donations->sum('total_value');
                                        $uniqueDonors = $report->donations->pluck('donor_name')->filter()->unique();
                                        $totalQuantity = $report->donations->sum('quantity');
                                    @endphp

                                    <div class="stats-grid">
                                        @if ($totalFamilies > 0)
                                            <div class="stat-item">
                                                <div class="stat-icon">
                                                    <i class="ri-group-line"></i>
                                                </div>
                                                <div class="stat-content">
                                                    <span class="stat-value">{{ $totalFamilies }}</span>
                                                    <span class="stat-label">Families Assisted</span>
                                                </div>
                                            </div>
                                        @endif

                                        @if ($totalValue > 0)
                                            <div class="stat-item">
                                                <div class="stat-icon">
                                                    <i class="ri-money-dollar-circle-line"></i>
                                                </div>
                                                <div class="stat-content">
                                                    <span
                                                        class="stat-value">₱{{ number_format($totalValue, 0) }}</span>
                                                    <span class="stat-label">Total Value</span>
                                                </div>
                                            </div>
                                        @endif

                                        @if ($uniqueDonors->count() > 0)
                                            <div class="stat-item">
                                                <div class="stat-icon">
                                                    <i class="ri-user-heart-line"></i>
                                                </div>
                                                <div class="stat-content">
                                                    <span class="stat-value">{{ $uniqueDonors->count() }}</span>
                                                    <span class="stat-label">Donors</span>
                                                </div>
                                            </div>
                                        @endif

                                        @if ($totalQuantity > 0)
                                            <div class="stat-item">
                                                <div class="stat-icon">
                                                    <i class="ri-box-3-line"></i>
                                                </div>
                                                <div class="stat-content">
                                                    <span class="stat-value">{{ $totalQuantity }}</span>
                                                    <span class="stat-label">Total Items</span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Date -->
                                    <div class="report-date">
                                        <i class="ri-calendar-line"></i>
                                        <span>{{ $report->report_date->format('F j, Y') }}</span>
                                    </div>

                                    <!-- Donated Items Section -->
                                    <div class="donated-section">
                                        <div class="section-title">
                                            <i class="ri-gift-line"></i>
                                            <h4>Donated Items</h4>
                                        </div>

                                        <div class="donated-items-list">
                                            <table>
                                                <thead>
                                                    <tr>
                                                        <th>Item</th>
                                                        <th class="text-center">Qty</th>
                                                        <th>Donor</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($report->donations as $donation)
                                                        <tr>
                                                            <td class="item-name">
                                                                <div class="item-primary">{{ $donation->item_name }}
                                                                </div>
                                                                @if ($donation->category)
                                                                    <div class="item-category">
                                                                        {{ $donation->category }}</div>
                                                                @endif
                                                            </td>
                                                            <td class="text-center">
                                                                <span
                                                                    class="quantity-badge">{{ $donation->quantity }}</span>
                                                            </td>
                                                            <td class="donor-info">
                                                                <div class="donor-name">{{ $donation->donor_name }}
                                                                </div>
                                                                @if ($donation->donor_email || $donation->donor_phone)
                                                                    <div class="donor-contact">
                                                                        @if ($donation->donor_email)
                                                                            <span
                                                                                class="contact-email">{{ $donation->donor_email }}</span>
                                                                        @endif
                                                                        @if ($donation->donor_phone)
                                                                            @if ($donation->donor_email)
                                                                                •
                                                                            @endif
                                                                            <span
                                                                                class="contact-phone">{{ $donation->donor_phone }}</span>
                                                                        @endif
                                                                    </div>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @else
                                    <!-- No Donations State -->
                                    <div class="no-data-state">
                                        <i class="ri-file-list-line"></i>
                                        <p>No donation data available for this report</p>
                                    </div>
                                    <div class="report-date">
                                        <i class="ri-calendar-line"></i>
                                        <span>{{ $report->report_date->format('F j, Y') }}</span>
                                    </div>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </section>

            </div>

            <!-- TAB CONTENT 4: Major Campaigns -->
            <div id="campaigns" class="tab-content">
                <div class="section-header">
                    <i class="ri-megaphone-line"></i>
                    <h2>Major Campaigns</h2>
                </div>



                <section class="accomplishments-grid">
                    @forelse($endedCampaigns as $campaign)
                        <article class="accomplishment-card">
                            <div class="accomplishment-card__image">
                                @if ($campaign->featured_image)
                                    <img src="{{ asset('storage/' . $campaign->featured_image) }}"
                                        alt="{{ $campaign->title }}">
                                @elseif($campaign->images && is_array($campaign->images) && count($campaign->images) > 0)
                                    <img src="{{ asset('storage/' . $campaign->images[0]) }}"
                                        alt="{{ $campaign->title }}">
                                @else
                                    <img src="{{ asset('img/camp.jpg') }}" alt="{{ $campaign->title }}">
                                @endif
                            </div>

                            <div class="accomplishment-card__content">
                                <!-- Campaign Header -->
                                <div class="report-header">
                                    <h3>{{ $campaign->title }}</h3>
                                    @if ($campaign->ends_at)
                                        <span class="report-year">{{ $campaign->ends_at->format('Y') }}</span>
                                    @endif
                                </div>

                                <!-- Description -->
                                <p class="report-description">{{ Str::limit($campaign->description, 150) }}</p>

                                <!-- Campaign Stats Grid -->
                                <div class="stats-grid">
                                    <div class="stat-item">
                                        <div class="stat-icon">
                                            <i class="ri-money-dollar-circle-line"></i>
                                        </div>
                                        <div class="stat-content">
                                            <span
                                                class="stat-value">₱{{ number_format($campaign->current_amount ?? 0, 0) }}</span>
                                            <span class="stat-label">Raised</span>
                                        </div>
                                    </div>

                                    <div class="stat-item">
                                        <div class="stat-icon">
                                            <i class="ri-target-line"></i>
                                        </div>
                                        <div class="stat-content">
                                            <span
                                                class="stat-value">₱{{ number_format($campaign->target_amount ?? 0, 0) }}</span>
                                            <span class="stat-label">Target</span>
                                        </div>
                                    </div>

                                    @if ($campaign->donor_count)
                                        <div class="stat-item">
                                            <div class="stat-icon">
                                                <i class="ri-user-heart-line"></i>
                                            </div>
                                            <div class="stat-content">
                                                <span class="stat-value">{{ $campaign->donor_count }}</span>
                                                <span class="stat-label">Donors</span>
                                            </div>
                                        </div>
                                    @endif

                                    @if ($campaign->views)
                                        <div class="stat-item">
                                            <div class="stat-icon">
                                                <i class="ri-eye-line"></i>
                                            </div>
                                            <div class="stat-content">
                                                <span class="stat-value">{{ number_format($campaign->views) }}</span>
                                                <span class="stat-label">Views</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Campaign Dates -->
                                <div class="event-date">
                                    @if ($campaign->starts_at && $campaign->ends_at)
                                        <div class="date-range">
                                            <i class="ri-calendar-line"></i>
                                            <span>
                                                {{ $campaign->starts_at->format('F j') }}
                                                @if ($campaign->ends_at && $campaign->ends_at->format('Y-m-d') != $campaign->starts_at->format('Y-m-d'))
                                                    - {{ $campaign->ends_at->format('j, Y') }}
                                                @else
                                                    {{ $campaign->starts_at->format(', Y') }}
                                                @endif
                                            </span>
                                        </div>
                                    @endif

                                    @if ($campaign->campaign_organizer)
                                        <div class="event-location">
                                            <i class="ri-organization-chart"></i>
                                            <span>{{ $campaign->campaign_organizer }}</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Campaign Progress -->
                                @if ($campaign->target_amount && $campaign->target_amount > 0)
                                    @php
                                        $progress = $campaign->current_amount
                                            ? ($campaign->current_amount / $campaign->target_amount) * 100
                                            : 0;
                                        $progress = min($progress, 100);
                                    @endphp
                                    <div class="success-metrics">
                                        <div class="metrics-header">
                                            <i class="ri-line-chart-line"></i>
                                            <h4>Campaign Progress</h4>
                                        </div>
                                        <div class="metrics-content">
                                            <div class="metric">
                                                <div class="metric-value">{{ number_format($progress, 1) }}%</div>
                                                <div class="metric-label">Achieved</div>
                                            </div>
                                            @if ($campaign->donor_count)
                                                <div class="metric">
                                                    <div class="metric-value">{{ $campaign->donor_count }}</div>
                                                    <div class="metric-label">Donors</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                <!-- Campaign Details List -->
                                <div class="event-details-section">
                                    <div class="section-title">
                                        <i class="ri-information-line"></i>
                                        <h4>Campaign Details</h4>
                                    </div>

                                    <div class="details-list">
                                        @if ($campaign->status)
                                            <div class="detail-item">
                                                <div class="detail-label">
                                                    <i class="ri-flag-line"></i>
                                                    <span>Status:</span>
                                                </div>
                                                <div class="detail-value">
                                                    <span
                                                        style="text-transform: capitalize;">{{ $campaign->status }}</span>
                                                </div>
                                            </div>
                                        @endif

                                        @if ($campaign->schedule_type)
                                            <div class="detail-item">
                                                <div class="detail-label">
                                                    <i class="ri-time-line"></i>
                                                    <span>Schedule:</span>
                                                </div>
                                                <div class="detail-value" style="text-transform: capitalize;">
                                                    {{ str_replace('_', ' ', $campaign->schedule_type) }}
                                                </div>
                                            </div>
                                        @endif

                                        @if ($campaign->organizer && $campaign->organizer->name)
                                            <div class="detail-item">
                                                <div class="detail-label">
                                                    <i class="ri-user-star-line"></i>
                                                    <span>Organizer:</span>
                                                </div>
                                                <div class="detail-value">{{ $campaign->organizer->name }}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </article>
                    @empty
                        <!-- Empty State for Campaigns -->
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="ri-megaphone-line"></i>
                            </div>
                            <h3>No Completed Campaigns</h3>
                            <p>There are no completed campaigns to display at the moment.</p>
                        </div>
                    @endforelse
                </section>


            </div>

        </div>
    </main>

    <script>
        // Tab switching logic
        function openTab(evt, tabName) {
            // Hide all elements with class="tab-content"
            var tabContent = document.getElementsByClassName("tab-content");
            for (var i = 0; i < tabContent.length; i++) {
                tabContent[i].style.display = "none";
                tabContent[i].classList.remove("active");
            }

            // Get all elements with class="tab-btn" and remove the class "active"
            var tabLinks = document.getElementsByClassName("tab-btn");
            for (var i = 0; i < tabLinks.length; i++) {
                tabLinks[i].classList.remove("active");
            }

            // Show the current tab, and add an "active" class to the button that opened the tab
            var selectedTab = document.getElementById(tabName);
            selectedTab.style.display = "block";
            // Small delay to trigger animation
            setTimeout(() => {
                selectedTab.classList.add("active");
            }, 10);

            evt.currentTarget.classList.add("active");
        }
    </script>

    <script>
        // Pagination & Filter Logic
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = ['events', 'donation', 'campaigns'];

            tabs.forEach(tabId => {
                initPagination(tabId);
            });

            function initPagination(tabId) {
                const tab = document.getElementById(tabId);
                if (!tab) return;

                const grid = tab.querySelector('.accomplishments-grid');
                if (!grid) return;

                const items = Array.from(grid.children);
                const select = tab.querySelector('.per-page-select');
                const paginationContainer = tab.querySelector('.pagination-controls');

                let currentPage = 1;
                let itemsPerPage = parseInt(select.value) || 6;

                function render() {
                    const totalItems = items.length;
                    const isAll = select.value === 'all';
                    const limit = isAll ? totalItems : itemsPerPage;
                    const totalPages = Math.ceil(totalItems / limit);

                    // Ensure current page is valid
                    if (currentPage > totalPages && totalPages > 0) currentPage = 1;

                    // Toggle visibility
                    const start = (currentPage - 1) * limit;
                    const end = start + limit;

                    items.forEach((item, index) => {
                        if (index >= start && index < end) {
                            item.style.display = ''; // Show (default display type)
                        } else {
                            item.style.display = 'none';
                        }
                    });

                    renderControls(totalPages);
                }

                function renderControls(totalPages) {
                    paginationContainer.innerHTML = '';
                    if (totalPages <= 1) return;

                    // Prev Button
                    const prevBtn = document.createElement('button');
                    prevBtn.innerHTML = '<i class="ri-arrow-left-s-line"></i>';
                    prevBtn.className = 'pagination-btn';
                    prevBtn.disabled = currentPage === 1;
                    prevBtn.onclick = () => {
                        if (currentPage > 1) {
                            currentPage--;
                            render();
                        }
                    };
                    paginationContainer.appendChild(prevBtn);

                    // Page Numbers
                    for (let i = 1; i <= totalPages; i++) {
                        const btn = document.createElement('button');
                        btn.textContent = i;
                        btn.className = `pagination-btn ${i === currentPage ? 'active' : ''}`;
                        btn.onclick = () => {
                            currentPage = i;
                            render();
                        };
                        paginationContainer.appendChild(btn);
                    }

                    // Next Button
                    const nextBtn = document.createElement('button');
                    nextBtn.innerHTML = '<i class="ri-arrow-right-s-line"></i>';
                    nextBtn.className = 'pagination-btn';
                    nextBtn.disabled = currentPage === totalPages;
                    nextBtn.onclick = () => {
                        if (currentPage < totalPages) {
                            currentPage++;
                            render();
                        }
                    };
                    paginationContainer.appendChild(nextBtn);
                }

                // Event Listener for Dropdown
                select.addEventListener('change', (e) => {
                    itemsPerPage = e.target.value === 'all' ? items.length : parseInt(e.target.value);
                    currentPage = 1;
                    render();
                });

                // Initial render
                render();
            }
        });
    </script>

    <script>
        // Sidebar toggle (mobile)
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            function toggleSidebar() {
                const isOpen = sidebar.classList.toggle('open');
                overlay.classList.toggle('show', isOpen);
            }
            if (toggleBtn) toggleBtn.addEventListener('click', toggleSidebar);
            if (overlay) overlay.addEventListener('click', toggleSidebar);
        });
    </script>


    <script>
        // Clear browser history stack
        history.pushState(null, null, location.href);

        // Prevent going back
        window.onpopstate = function(event) {
            history.go(1);
            // Force reload the page
            window.location.reload(true);
        };

        // Check if page was loaded from cache (back/forward button)
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                // Page was loaded from cache, reload and check session
                window.location.reload();
            }
        });

        // Alternative: Check navigation type
        if (performance.navigation.type === 2) { // Type 2 = back/forward
            // Force a fresh request to server
            window.location.href = window.location.href + '?t=' + new Date().getTime();
        }
    </script>
</body>

</html>
