<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tulong Kabataan | Administrator Dashboard</title>
    <link rel="icon" href="{{ asset('img/log2.png') }}" type="image/png">
    <!-- Remixicon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <!-- Google Fonts: Playfair Display & Open Sans -->
    <link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Playfair+Display:wght@700;800&display=swap"
        rel="stylesheet">
    <!-- Charts -->
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

        * {
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
        }

        body {
            margin: 0;
            font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji", "Segoe UI Emoji";
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

        .notif:hover {
            background: #374151;
        }

        .notif .badge {
            position: absolute;
            top: -6px;
            right: -6px;
            background: #ef4444;
            color: #fff;
            width: 20px;
            height: 20px;
            font-size: 12px;
            border-radius: 999px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
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

        /* Main content — push to the right to allow for sidebar width on large screens */
        .main {
            margin-top: 64px;
            padding: 20px;
            transition: margin-left .28s ease;
            margin-left: 260px;
            min-height: calc(100vh - 64px);
        }

        .main.fullwidth {
            margin-left: 0;
        }

        /* Responsive: collapse sidebar on small screens */
        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-110%);
            }

            .sidebar.visible {
                transform: translateX(0);
            }

            .overlay {
                display: none;
            }

            .overlay.visible {
                display: block;
            }

            .main {
                margin-left: 0;
            }
        }

        /* Controls row */
        .controls {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 20px;
            align-items: center;
        }

        .controls .left {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }

        .controls .field {
            position: relative;
        }

        .controls input[type="date"],
        .controls select {
            padding: 8px 10px 8px 36px;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            font-size: 13px;
            background: white;
            outline: none;
        }

        .controls .icon-inside {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            pointer-events: none;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 10px;
            font-size: 14px;
            cursor: pointer;
            border: none;
        }

        .btn-light {
            background: #f3f4f6;
            color: #374151;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-secondary {
            background: var(--secondary);
            color: white;
        }

        /* Grid for top cards */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(1, 1fr);
            gap: 16px;
            margin-bottom: 20px;
        }

        @media(min-width:640px) {
            .cards-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media(min-width:1024px) {
            .cards-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        .card {
            background: var(--card-bg);
            padding: 18px;
            border-radius: 16px;
            box-shadow: var(--shadow);
            border: 1px solid var(--panel-border);
        }

        .card .meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        /* Chart/panel grid */
        .panels-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 16px;
            margin-bottom: 20px;
        }

        @media(min-width:1024px) {
            .panels-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        .panel {
            background: var(--card-bg);
            border-radius: 16px;
            box-shadow: var(--shadow);
            border: 1px solid var(--panel-border);
            padding: 18px;
        }

        /* Campaign list / progress */
        .progress-row {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 18px;
        }

        .progress-row h4 {
            margin: 0;
            font-size: 16px;
        }

        .progress {
            height: 10px;
            background: #e6e7ea;
            border-radius: 999px;
            overflow: hidden;
            width: 100%;
        }

        .progress>.bar {
            height: 100%;
            border-radius: 999px;
        }

        /* Map placeholder panel */
        .map-panel {
            position: relative;
            height: 320px;
            border-radius: 0 0 12px 12px;
            overflow: hidden;
        }

        .map-panel .map-bg {
            position: absolute;
            inset: 0;
            background-size: cover;
            background-position: center;
            filter: contrast(.98);
        }

        .map-legend {
            position: absolute;
            left: 16px;
            right: 16px;
            bottom: 16px;
            background: rgba(255, 255, 255, .95);
            backdrop-filter: blur(4px);
            border-radius: 12px;
            padding: 10px;
        }

        /* Recent activity list */
        .activity-list {
            max-height: 320px;
            overflow: auto;
            padding: 12px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        /* Health indicators grid */
        .health-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
        }

        @media(min-width:768px) {
            .health-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        .health-card {
            text-align: center;
            background: var(--card-bg);
            padding: 18px;
            border-radius: 12px;
            border: 1px solid var(--panel-border);
        }

        /* Utility */
        .small {
            font-size: 13px;
            color: var(--muted);
        }

        .kv {
            font-size: 20px;
            font-weight: 700;
        }

        /* Icon helper */
        .icon-circle {
            width: 48px;
            height: 48px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 8px;
        }

        /* Utility spacing */
        .mb-1 {
            margin-bottom: 8px;
        }

        .mb-2 {
            margin-bottom: 12px;
        }

        .mb-3 {
            margin-bottom: 16px;
        }

        /* Sidebar toggle button shown on small screens */
        .sidebar-toggle {
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            background: transparent;
            border: none;
            color: white;
            cursor: pointer;
        }

        .notification-btn {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: #111827b3;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            color: white;
        }

        /* small text badge */
        .badge {
            position: absolute;
            top: -6px;
            right: -6px;
            background: #ef4444;
            color: white;
            width: 20px;
            height: 20px;
            border-radius: 999px;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        /* Misc layout adjustments for exactness similar to original */
        .controls .right {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .side-link.active,
        .side-btn.active {
            background: var(--primary);
            color: #fff !important;
        }

        /* Trend indicators */
        .trend-blue {
            font-size: 12px;
            font-weight: 600;
            color: #3b82f6;
            background: #dbeafe;
            padding: 2px 6px;
            border-radius: 999px;
        }

        .trend-orange {
            font-size: 12px;
            font-weight: 600;
            color: #f97316;
            background: #ffedd5;
            padding: 2px 6px;
            border-radius: 999px;
        }

        .trend-green {
            font-size: 12px;
            font-weight: 600;
            color: #10b981;
            background: #d1fae5;
            padding: 2px 6px;
            border-radius: 999px;
        }

        .trend-red {
            font-size: 12px;
            font-weight: 600;
            color: #ef4444;
            background: #fee2e2;
            padding: 2px 6px;
            border-radius: 999px;
        }

        /* Stat card specific styles */
        .card--pad {
            padding: 20px;
        }

        .stat-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .stat-ico {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stat-ico i {
            font-size: 20px;
        }

        .bg-blue-100 {
            background: #dbeafe;
        }

        .bg-blue-100 i {
            color: #3b82f6;
        }

        .bg-orange-100 {
            background: #ffedd5;
        }

        .bg-orange-100 i {
            color: #f97316;
        }

        .stat-title {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 24px;
            font-weight: 700;
            color: #111827;
            margin: 0;
        }

        .chart {
            width: 100%;
        }

        /* Add these styles to your existing CSS section, around line 350-360 */

        .chart-card {
            background: var(--card);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 20px;
            border: 1px solid var(--border);
            margin-bottom: 20px;
            height: 400px;
            /* Give it a fixed height */
        }

        .chart-card h3 {
            margin: 0 0 16px;
            font-size: 18px;
            font-weight: 700;
            color: var(--text);
        }

        .chart-container {
            width: 100%;
            height: 320px;
            /* Fixed height for the chart */
            margin-top: 10px;
        }

        /* Chart/panel grid - UPDATED for full width */
        .panels-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 16px;
            margin-bottom: 20px;
        }

        @media(min-width:1024px) {
            .panels-grid {
                grid-template-columns: 1fr;

            }
        }



        /* Campaign Details Modal CSS */
        .campaign-details-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            justify-content: center;
            align-items: center;
            z-index: 3000;
        }

        .campaign-details-content {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            max-width: 1400px;
            /* Increased from 1200px */
            width: 95%;
            max-height: 90vh;
            position: relative;
            z-index: 3001;
            display: flex;
            flex-direction: column;
            gap: 15px;
            overflow-y: auto;
        }

        .campaign-details-close {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #ef4444;
            border: none;
            color: #fff;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 20px;
            z-index: 3002;
            transition: background 0.2s;
        }

        .campaign-details-close:hover {
            background: #dc2626;
        }

        .campaign-details-header {
            margin-bottom: 10px;
            text-align: center;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 15px;
        }

        .campaign-details-title {
            font-size: 20px;
            font-weight: 600;
            color: #1f2937;
            margin: 0;
        }

        .campaign-details-body {
            display: flex;
            gap: 25px;
            /* Increased gap */
            align-items: flex-start;
            width: 100%;
            min-height: 550px;
            /* Increased min-height */
        }

        .campaign-details-sidebar {
            flex: 0 0 280px;
            /* Slightly reduced width for more image space */
            background: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }

        .campaign-detail-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .campaign-detail-item:last-child {
            border-bottom: none;
        }

        .campaign-detail-label {
            font-weight: 600;
            color: #374151;
            font-size: 14px;
        }

        .campaign-detail-value {
            font-weight: 500;
            color: #1f2937;
            font-size: 14px;
            text-align: right;
            max-width: 180px;
            word-break: break-word;
        }

        #detailStatus {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }

        #detailStatus.pending {
            background: #fef3c7;
            color: #92400e;
        }

        #detailStatus.approved {
            background: #d1fae5;
            color: #065f46;
        }

        #detailStatus.rejected {
            background: #fee2e2;
            color: #991b1b;
        }

        .campaign-proof-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 15px;
            height: 100%;
        }

        .proof-main-image {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: #f9fafb;
            border-radius: 8px;
            padding: 25px;
            /* Increased padding */
            border: 1px solid #e5e7eb;
            min-height: 500px;
            /* Increased from 400px */
            overflow: hidden;
        }

        .proof-image {
            max-width: 100%;
            max-height: 500px;
            /* Increased from 350px */
            object-fit: contain;
            border-radius: 8px;
            /* Slightly larger radius */
            cursor: pointer;
            transition: transform 0.3s;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            /* Added subtle shadow */
        }

        .proof-image:hover {
            transform: scale(1.03);
            /* Slightly larger hover effect */
        }

        .proof-caption {
            margin-top: 20px;
            /* Increased margin */
            text-align: center;
            font-weight: 600;
            color: #374151;
            font-size: 16px;
            background: #f3f4f6;
            padding: 10px 20px;
            /* Increased padding */
            border-radius: 6px;
            width: 100%;
            max-width: 80%;
            margin-left: auto;
            margin-right: auto;
        }

        .proof-actions {
            display: flex;
            justify-content: center;
            gap: 10px;
            padding: 12px;
            /* Increased padding */
            background: #f8fafc;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }

        .proof-action-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            /* Slightly larger */
            height: 42px;
            border-radius: 6px;
            border: 1px solid #d1d5db;
            background: white;
            color: #374151;
            cursor: pointer;
            font-size: 20px;
            /* Increased icon size */
            transition: all 0.2s;
        }

        .proof-action-btn:hover {
            background: #f3f4f6;
            border-color: #9ca3af;
        }

        .proof-action-btn:active {
            transform: scale(0.95);
        }

        /* Image Overlay Styles */
        .image-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.95);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 4000;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .overlay-close {
            position: absolute;
            top: 20px;
            right: 20px;
            background: #ef4444;
            border: none;
            color: white;
            width: 45px;
            /* Larger close button */
            height: 45px;
            border-radius: 50%;
            font-size: 26px;
            /* Larger X */
            cursor: pointer;
            z-index: 4001;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.3s;
        }

        .overlay-close:hover {
            background: #dc2626;
        }

        .overlay-image-container {
            width: 95%;
            /* Increased from 90% */
            height: 95%;
            /* Increased from 90% */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .overlay-image-container img {
            max-width: 100%;
            max-height: 90%;
            /* Increased from 85% */
            object-fit: contain;
            border-radius: 8px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.6);
            /* Enhanced shadow */
        }

        .overlay-caption {
            margin-top: 25px;
            /* Increased margin */
            color: white;
            font-size: 20px;
            /* Larger font */
            font-weight: 500;
            text-align: center;
            background: rgba(0, 0, 0, 0.7);
            padding: 12px 25px;
            /* Increased padding */
            border-radius: 8px;
            max-width: 85%;
            /* Slightly wider */
        }

        /* Responsive Design */
        @media (max-width: 900px) {
            .campaign-details-body {
                flex-direction: column;
            }

            .campaign-details-sidebar {
                flex: 0 0 auto;
                width: 100%;
            }

            .proof-main-image {
                min-height: 350px;
                /* Increased from 300px */
            }

            .proof-image {
                max-height: 300px;
                /* Increased from 250px */
            }
        }

        /* For even larger screens */
        @media (min-width: 1600px) {
            .campaign-details-content {
                max-width: 1500px;
            }

            .proof-main-image {
                min-height: 550px;
            }

            .proof-image {
                max-height: 550px;
            }
        }

        /* Button Styles */
        .btn-outline {
            padding: 6px 12px;
            background: white;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            color: #374151;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .btn-outline:hover {
            background: #f3f4f6;
            border-color: #9ca3af;
        }

        .btn-outline i {
            font-size: 14px;
        }
    </style>
</head>

<body>
    @include('administrator.partials.loading-screen')
    @include('partials.universalmodal')

    <!-- Header -->
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

    <!-- Sidebar -->
    @include('administrator.partials.main-sidebar')

    <!-- Overlay (mobile) -->
    <div id="sidebarOverlay" class="overlay" aria-hidden="true"></div>

    <!-- Main -->
    <main id="mainContent" class="main" role="main">
        <div style="max-width:1200px; margin:0 auto;">

            <!-- Top Controls -->
        

            <!-- Top Analytics Cards -->
            <section class="cards-grid">

                <!-- Total Active Campaigns -->
                <div class="card">
                    <div class="meta">
                        <div style="display:flex;align-items:center;gap:12px;">
                            <div
                                style="width:48px;height:48px;border-radius:12px;background:#e6f2ff;display:flex;align-items:center;justify-content:center;">
                                <i class="ri-megaphone-line" style="color:#2563eb;font-size:20px;"></i>
                            </div>
                        </div>
                    </div>
                    <h3 class="small">Total Active Campaigns</h3>
                    <p class="kv" id="totalActiveCampaigns">0</p>
                </div>

                <article class="card card--pad">
                    <div class="stat-top">
                        <div class="stat-ico bg-orange-100"><i class="ri-megaphone-line"></i></div>
                        <span class="trend-orange" id="campaignChange">+0%</span>
                    </div>
                    <h4 class="stat-title">Campaign Count</h4>
                    <p class="stat-value" id="totalCampaigns">0</p>
                </article>

                <!-- Total Funds Raised --> 
                <div class="card">
                    <div class="meta">
                        <div style="display:flex;align-items:center;gap:12px;">
                            <div
                                style="width:48px;height:48px;border-radius:12px;background:#ecfdf5;display:flex;align-items:center;justify-content:center;">
                                <i class="ri-money-dollar-circle-line" style="color:#10b981;font-size:20px;"></i>
                            </div>
                        </div>
                    </div>
                    <h3 class="small">Total Funds Raised</h3>
                    <p class="kv" id="totalFundsRaised">₱0.00</p>
                </div>

                <article class="card card--pad">
                    <div class="stat-top">
                        <div class="stat-ico bg-blue-100"><i class="ri-gift-line"></i></div>
                        <span class="trend-blue" id="donationChange">+0%</span>
                    </div>
                    <h4 class="stat-title">Today's Donations</h4>
                    <p class="stat-value" id="todayDonations">₱0.00</p>
                </article>
            </section>


            <!-- Charts Section -->
            <section class="panels-grid" style="margin-bottom: 20px;">
                <!-- Transaction Per Month Chart -->
                <div class="chart-card">
                    <h3>Monthly Funds Overview</h3>
                    <div id="transactionChart" class="chart-container"></div>
                </div>
            </section>

            <!-- Existing Campaigns Section (Static with Toggle Requests) -->
            <section class="panel" style="margin-top: 30px;">
                @include('administrator.campaign.partials.campaign_list')
            </section>


            <!-- Pagination -->
            <div id="paginationContainer" class="pagination-container">
                <nav id="campaignPagination" class="pagination"></nav>
            </div>

        </div><!-- /container -->
    </main>

    <!-- Sidebar toggle behaviour:-->
    <script>
        /*
                                                                                                                                                                                                                                                                                                                          Sidebar toggle behaviour:
                                                                                                                                                                                                                                                                                                                          - On wide screens the sidebar is visible by default.
                                                                                                                                                                                                                                                                                                                          - On small screens the sidebar is collapsed; clicking the menu toggles it.
                                                                                                                                                                                                                                                                                                                        */
        (function() {
            const sidebar = document.getElementById('sidebar');
            const toggle = document.getElementById('sidebarToggle');
            const overlay = document.getElementById('sidebarOverlay');
            const main = document.getElementById('mainContent');

            function isLarge() {
                return window.innerWidth >= 1024;
            }

            function showSidebar() {
                if (isLarge()) {
                    sidebar.classList.remove('collapsed');
                    sidebar.classList.remove('visible');
                    overlay.classList.remove('visible');
                    main.classList.remove('fullwidth');
                } else {
                    sidebar.classList.add('visible');
                    overlay.classList.add('visible');
                    main.classList.add('fullwidth');
                }
            }

            function hideSidebar() {
                if (isLarge()) {
                    sidebar.classList.remove('collapsed');
                    overlay.classList.remove('visible');
                    main.classList.remove('fullwidth');
                } else {
                    sidebar.classList.remove('visible');
                    overlay.classList.remove('visible');
                    main.classList.remove('fullwidth');
                }
            }

            // Initialize state
            if (!isLarge()) {
                sidebar.classList.add('collapsed');
            } else {
                sidebar.classList.remove('collapsed');
            }

            toggle.addEventListener('click', function() {
                if (sidebar.classList.contains('visible') || sidebar.classList.contains('collapsed')) {
                    // toggle to show
                    if (sidebar.classList.contains('visible')) {
                        hideSidebar();
                        sidebar.classList.add('collapsed');
                    } else {
                        showSidebar();
                        sidebar.classList.remove('collapsed');
                    }
                } else {
                    // if neither, decide by size
                    if (isLarge()) sidebar.classList.add('collapsed');
                    else showSidebar();
                }
            });

            overlay.addEventListener('click', function() {
                hideSidebar();
                sidebar.classList.add('collapsed');
            });

            // adapt on resize
            window.addEventListener('resize', function() {
                if (isLarge()) {
                    sidebar.classList.remove('collapsed');
                    sidebar.classList.remove('visible');
                    overlay.classList.remove('visible');
                    main.classList.remove('fullwidth');
                } else {
                    sidebar.classList.add('collapsed');
                }
            });
        })();
    </script>

    <!-- Highlight active sidebar link when selected -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarLinks = document.querySelectorAll('.side-link');

            const currentPage = window.location.pathname.split('/').pop();

            sidebarLinks.forEach(link => {

                link.classList.remove('active');

                if (link.getAttribute('href') && link.getAttribute('href').includes(currentPage)) {
                    link.classList.add('active');
                }

                link.addEventListener('click', function() {
                    sidebarLinks.forEach(l => l.classList.remove('active'));
                    link.classList.add('active');
                });
            });
        });
    </script>

    <!-- JS TOGGLE FUNCTION -->
    <script>
        function toggleRequests(id) {
            const el = document.getElementById(id);
            el.style.display = (el.style.display === "none" || el.style.display === "") ? "block" : "none";
        }
    </script>

    {{-- BUTTON AJAX  --}}
    <script>
        function showConfirmModal(url, requestId, actionType) {
            const modal = document.getElementById('confirmModal');
            const title = document.getElementById('confirmModalTitle');
            const message = document.getElementById('confirmModalMessage');
            const confirmBtn = document.getElementById('confirmActionBtn');
            const cancelBtn = document.getElementById('cancelConfirmBtn');

            title.textContent = 'Confirm Action';
            message.textContent = `Are you sure you want to ${actionType} this manual donation request?`;

            confirmBtn.style.background = actionType === 'approved' ? '#16a34a' : '#ef4444';
            confirmBtn.textContent = actionType === 'approved' ? 'Approve' : 'Reject';

            modal.style.display = 'flex';

            cancelBtn.onclick = () => modal.style.display = 'none';
            confirmBtn.onclick = async () => {
                modal.style.display = 'none';
                await handleManualAction(url, requestId, actionType);
            };
        }

        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            const bgColor =
                type === 'error' ? '#dc2626' :
                type === 'reject' ? '#ef4444' :
                '#16a34a';
            toast.style.background = bgColor;
            toast.textContent = message;
            toast.style.opacity = '1';
            toast.style.transform = 'translateY(0)';

            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(-20px)';
            }, 3000);
        }

        async function handleManualAction(url, requestId, actionType) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const actionCell = document.getElementById(`action-cell-${requestId}`);
            const statusCell = document.getElementById(`status-cell-${requestId}`);

            actionCell.innerHTML = `<button class="btn btn-disabled" disabled>Processing...</button>`;

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({})
                });

                if (!response.ok) throw new Error('Request failed');

                const bg = actionType === 'approved' ? '#dcfce7' : '#fee2e2';
                const color = actionType === 'approved' ? '#166534' : '#991b1b';
                const label = actionType.charAt(0).toUpperCase() + actionType.slice(1);

                statusCell.innerHTML = `
            <span style="background:${bg};color:${color};
                font-size:13px;font-weight:600;padding:4px 12px;
                border-radius:999px;display:inline-flex;align-items:center;justify-content:center;">
                ${label}
            </span>`;

                actionCell.innerHTML = `<button class="btn btn-disabled" disabled>No actions</button>`;

                showToast(
                    actionType === 'approved' ?
                    'Manual donation approved successfully!' :
                    'Manual donation request rejected.',
                    actionType === 'approved' ? 'success' : 'reject'
                );

            } catch (error) {
                console.error(error);
                showToast('Something went wrong. Please try again.', 'error');
                actionCell.innerHTML = `
            <button class="btn btn-light"
                    onclick="showConfirmModal('${url}', ${requestId}, '${actionType}')">
                Retry
            </button>`;
            }
        }
    </script>

    {{-- Campaign AJAX --}}
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const campaignInterval = 5000;
            const statsInterval = 5000;
            const campaignSection = document.querySelector(".panel");

            let openToggles = new Set();
            let isModalOpen = false; // Track modal state

            window.toggleRequests = function(id) {
                const el = document.getElementById(id);
                const isHidden = el.style.display === "none" || el.style.display === "";
                el.style.display = isHidden ? "block" : "none";

                if (isHidden) {
                    openToggles.add(id);
                } else {
                    openToggles.delete(id);
                }
            }

            // Initialize modal functionality
            function initCampaignModals() {
                const campaignModal = document.getElementById('campaignDetailsModal');
                const closeCampaignModal = document.getElementById('closeCampaignDetailsModal');
                const overlay = document.getElementById('imageOverlay');
                const overlayClose = overlay?.querySelector('.overlay-close');
                const proofImage = document.getElementById('campaignProofImage');

                // View proof button click handler - REBIND after content loads
                document.querySelectorAll('.view-campaign-proof-btn').forEach(btn => {
                    // Remove any existing listeners by cloning
                    const newBtn = btn.cloneNode(true);
                    btn.parentNode.replaceChild(newBtn, btn);
                });

                // Rebind the click events
                document.querySelectorAll('.view-campaign-proof-btn').forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        e.stopPropagation();

                        // Get data from button attributes
                        const requestId = this.getAttribute('data-request-id');
                        const requestedBy = this.getAttribute('data-requested-by');
                        const amount = this.getAttribute('data-amount');
                        const reference = this.getAttribute('data-reference');
                        const status = this.getAttribute('data-status');
                        const campaign = this.getAttribute('data-campaign');
                        const organizer = this.getAttribute('data-organizer');
                        const requestedAt = this.getAttribute('data-requested-at');
                        const proofImageUrl = this.getAttribute('data-proof-image');

                        // Populate modal with data
                        document.getElementById('detailRequestId').textContent = requestId;
                        document.getElementById('detailRequestedBy').textContent = requestedBy;
                        document.getElementById('detailAmount').textContent = amount;
                        document.getElementById('detailReference').textContent = reference;
                        document.getElementById('detailCampaign').textContent = campaign;
                        document.getElementById('detailOrganizer').textContent = organizer;
                        document.getElementById('detailRequestedAt').textContent = requestedAt;

                        // Set status with appropriate styling
                        const statusElement = document.getElementById('detailStatus');
                        statusElement.textContent = status.charAt(0).toUpperCase() + status.slice(
                            1);
                        statusElement.className = 'campaign-detail-value';
                        statusElement.classList.add(status);

                        // Set proof image
                        if (proofImage) {
                            proofImage.src = proofImageUrl;
                            proofImage.onclick = function() {
                                openImageOverlay(proofImageUrl, 'Payment Proof');
                            };

                            // Setup zoom functionality with boundaries
                            setupImageZoom(proofImage);
                        }

                        // Show modal and update state
                        if (campaignModal) {
                            campaignModal.style.display = 'flex';
                            isModalOpen = true; // Set modal as open
                        }
                    });
                });

                // Close modal handlers
                if (closeCampaignModal) {
                    closeCampaignModal.onclick = () => {
                        if (campaignModal) {
                            campaignModal.style.display = 'none';
                            isModalOpen = false; // Set modal as closed
                        }
                    };
                }

                if (campaignModal) {
                    campaignModal.onclick = e => {
                        if (e.target === campaignModal) {
                            campaignModal.style.display = 'none';
                            isModalOpen = false; // Set modal as closed
                        }
                    };
                }

                // Image overlay handlers
                if (overlayClose) {
                    overlayClose.onclick = closeImageOverlay;
                }

                if (overlay) {
                    overlay.onclick = e => {
                        if (e.target === overlay) {
                            closeImageOverlay();
                        }
                    };
                }

                // Function to setup image zoom functionality with boundaries
                function setupImageZoom(imageElement) {
                    if (!imageElement) return;

                    let scale = 1;
                    let isDragging = false;
                    let startX, startY, translateX = 0,
                        translateY = 0;
                    const minScale = 0.5;
                    const maxScale = 3;

                    // Get container element
                    const container = imageElement.closest('.proof-main-image') || imageElement.parentElement;

                    // Reset transform
                    imageElement.style.transform = 'scale(1) translate(0px, 0px)';
                    imageElement.style.transformOrigin = 'center center';
                    imageElement.style.transition = 'transform 0.1s ease';
                    imageElement.style.cursor = 'zoom-in';

                    // Function to constrain translation within bounds
                    function constrainTranslation() {
                        if (!container) return;

                        const containerRect = container.getBoundingClientRect();
                        const imageRect = imageElement.getBoundingClientRect();

                        // Calculate max translation based on zoom level and container size
                        const maxTranslateX = Math.max(0, (imageRect.width * scale - containerRect.width) / 2);
                        const maxTranslateY = Math.max(0, (imageRect.height * scale - containerRect.height) / 2);

                        // Constrain translation to prevent image from going outside container
                        if (scale > 1) {
                            translateX = Math.max(-maxTranslateX, Math.min(maxTranslateX, translateX));
                            translateY = Math.max(-maxTranslateY, Math.min(maxTranslateY, translateY));
                        } else {
                            // When not zoomed, keep image centered
                            translateX = 0;
                            translateY = 0;
                        }
                    }

                    // Mouse wheel zoom
                    imageElement.addEventListener('wheel', function(e) {
                        e.preventDefault();

                        const rect = imageElement.getBoundingClientRect();
                        const offsetX = e.clientX - rect.left;
                        const offsetY = e.clientY - rect.top;

                        const delta = e.deltaY < 0 ? 1.1 : 0.9; // Zoom in or out
                        const newScale = Math.max(minScale, Math.min(maxScale, scale * delta));

                        // Calculate new translate values to keep zoom centered on mouse position
                        const scaleChange = newScale / scale;
                        translateX = offsetX - scaleChange * (offsetX - translateX);
                        translateY = offsetY - scaleChange * (offsetY - translateY);
                        scale = newScale;

                        // Constrain translation after zoom
                        constrainTranslation();

                        updateTransform();

                        // Update cursor based on zoom level
                        imageElement.style.cursor = scale > 1 ? 'grab' : 'zoom-in';
                    });

                    // Touch events for pinch zoom
                    let initialDistance = null;

                    imageElement.addEventListener('touchstart', function(e) {
                        if (e.touches.length === 2) {
                            e.preventDefault();
                            const touch1 = e.touches[0];
                            const touch2 = e.touches[1];
                            initialDistance = Math.hypot(
                                touch2.clientX - touch1.clientX,
                                touch2.clientY - touch1.clientY
                            );
                        } else if (e.touches.length === 1 && scale > 1) {
                            isDragging = true;
                            startX = e.touches[0].clientX - translateX;
                            startY = e.touches[0].clientY - translateY;
                        }
                    });

                    imageElement.addEventListener('touchmove', function(e) {
                        if (e.touches.length === 2 && initialDistance !== null) {
                            e.preventDefault();
                            const touch1 = e.touches[0];
                            const touch2 = e.touches[1];
                            const currentDistance = Math.hypot(
                                touch2.clientX - touch1.clientX,
                                touch2.clientY - touch1.clientY
                            );

                            const delta = currentDistance / initialDistance;
                            const newScale = Math.max(minScale, Math.min(maxScale, scale * delta));

                            // Calculate center point between two fingers
                            const centerX = (touch1.clientX + touch2.clientX) / 2;
                            const centerY = (touch1.clientY + touch2.clientY) / 2;
                            const rect = imageElement.getBoundingClientRect();
                            const offsetX = centerX - rect.left;
                            const offsetY = centerY - rect.top;

                            const scaleChange = newScale / scale;
                            translateX = offsetX - scaleChange * (offsetX - translateX);
                            translateY = offsetY - scaleChange * (offsetY - translateY);
                            scale = newScale;

                            constrainTranslation();

                            updateTransform();
                            imageElement.style.cursor = scale > 1 ? 'grabbing' : 'zoom-in';
                        } else if (e.touches.length === 1 && isDragging && scale > 1) {
                            e.preventDefault();
                            translateX = e.touches[0].clientX - startX;
                            translateY = e.touches[0].clientY - startY;

                            constrainTranslation();
                            updateTransform();
                        }
                    });

                    imageElement.addEventListener('touchend', function(e) {
                        if (e.touches.length < 2) {
                            initialDistance = null;
                        }
                        if (e.touches.length === 0) {
                            isDragging = false;
                        }
                    });

                    // Mouse drag for panning when zoomed
                    imageElement.addEventListener('mousedown', function(e) {
                        if (scale > 1) {
                            e.preventDefault();
                            isDragging = true;
                            startX = e.clientX - translateX;
                            startY = e.clientY - translateY;
                            imageElement.style.cursor = 'grabbing';
                        }
                    });

                    imageElement.addEventListener('mousemove', function(e) {
                        if (isDragging && scale > 1) {
                            e.preventDefault();
                            translateX = e.clientX - startX;
                            translateY = e.clientY - startY;

                            constrainTranslation();
                            updateTransform();
                        }
                    });

                    imageElement.addEventListener('mouseup', function() {
                        isDragging = false;
                        imageElement.style.cursor = scale > 1 ? 'grab' : 'zoom-in';
                    });

                    imageElement.addEventListener('mouseleave', function() {
                        isDragging = false;
                        imageElement.style.cursor = scale > 1 ? 'grab' : 'zoom-in';
                    });

                    // Double click to reset zoom
                    imageElement.addEventListener('dblclick', function(e) {
                        e.preventDefault();
                        resetToOriginal();
                    });

                    // Click outside image to reset zoom
                    if (container) {
                        container.addEventListener('click', function(e) {
                            // Check if click is outside the image element
                            if (!imageElement.contains(e.target) && scale !== 1) {
                                resetToOriginal();
                            }
                        });
                    }

                    function updateTransform() {
                        imageElement.style.transform =
                            `scale(${scale}) translate(${translateX}px, ${translateY}px)`;
                    }

                    function resetToOriginal() {
                        scale = 1;
                        translateX = 0;
                        translateY = 0;
                        updateTransform();
                        imageElement.style.cursor = 'zoom-in';
                    }

                    // Reset zoom when modal closes
                    const resetZoom = () => {
                        resetToOriginal();
                    };

                    // Add reset function to window for easy access
                    window.resetImageZoom = resetZoom;
                }

                // Function to open image overlay
                window.openImageOverlay = function(src, caption) {
                    const overlay = document.getElementById('imageOverlay');
                    const overlayImg = document.getElementById('overlayImage');
                    const overlayCaption = document.getElementById('overlayCaption');

                    if (overlay && overlayImg) {
                        overlayImg.src = src;
                        overlayCaption.textContent = caption || '';
                        overlay.style.display = 'flex';
                        document.body.style.overflow = 'hidden';

                        // Setup zoom for overlay image with boundaries
                        setupImageZoom(overlayImg);
                    }
                };

                // Function to close image overlay
                window.closeImageOverlay = function() {
                    const overlay = document.getElementById('imageOverlay');
                    if (overlay) {
                        overlay.style.display = 'none';
                        document.body.style.overflow = '';
                    }
                };

                // Escape key to close modals
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') {
                        if (campaignModal && campaignModal.style.display === 'flex') {
                            campaignModal.style.display = 'none';
                            isModalOpen = false;
                        }
                        if (overlay && overlay.style.display === 'flex') {
                            closeImageOverlay();
                        }
                    }
                });
            }

            // Fetch campaigns without pagination/search
            async function fetchNewCampaigns() {
                try {
                    // Skip AJAX refresh if modal is open
                    if (isModalOpen) {
                        console.log("Skipping campaign refresh - modal is open", new Date()
                            .toLocaleTimeString());
                        return;
                    }

                    const response = await fetch("{{ route('admin.campaigns.latest') }}", {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });
                    if (!response.ok) throw new Error("Campaign fetch failed");

                    const data = await response.json();

                    campaignSection.innerHTML = data.html;

                    // Restore open toggles
                    openToggles.forEach(id => {
                        const el = document.getElementById(id);
                        if (el) el.style.display = "block";
                    });

                    // Initialize modals after loading new content
                    initCampaignModals();

                    console.log("Campaigns refreshed and toggles preserved", new Date().toLocaleTimeString());

                } catch (error) {
                    console.error("❌ Error fetching campaigns:", error);
                }
            }

            // Initial load
            fetchNewCampaigns();

            // Set up interval for polling
            setInterval(fetchNewCampaigns, campaignInterval);

            // ======================================
            // Dashboard Live Stats
            // ======================================
            async function fetchCampaignStats() {
                try {
                    const response = await fetch(
                        "{{ route('admin.dashboard.stats') }}", { // Make sure this matches your actual route name
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                    if (!response.ok) throw new Error("Stats fetch failed");

                    const data = await response.json();

                    // Update existing stats
                    document.querySelector("#totalActiveCampaigns").textContent =
                        data.totalActiveCampaigns ?? 0;
                    document.querySelector("#totalFundsRaised").textContent =
                        `₱${Number(data.totalFundsRaised || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;

                    // Update new stats for Today's Donations card
                    const todayDonations = document.querySelector("#todayDonations");
                    const donationChange = document.querySelector("#donationChange");
                    if (todayDonations) {
                        todayDonations.textContent =
                            `₱${Number(data.todayDonations || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                    }

                    if (donationChange) {
                        const change = data.donationChange || 0;
                        donationChange.textContent = `${change >= 0 ? '+' : ''}${change}%`;
                        donationChange.className = change >= 0 ? 'trend-green' : 'trend-red';
                    }

                    // Update new stats for Campaign Count card
                    const totalCampaigns = document.querySelector("#totalCampaigns");
                    const campaignChange = document.querySelector("#campaignChange");
                    if (totalCampaigns) {
                        totalCampaigns.textContent = data.totalCampaigns ?? 0;
                    }

                    if (campaignChange) {
                        const change = data.campaignChange || 0;
                        campaignChange.textContent = `${change >= 0 ? '+' : ''}${change}%`;
                        campaignChange.className = change >= 0 ? 'trend-orange' : 'trend-red';
                    }

                } catch (error) {
                    console.error("❌ Error fetching stats:", error);
                }
            }

            fetchCampaignStats();
            setInterval(fetchCampaignStats, statsInterval);
        });
    </script>

    <!-- Transaction Chart Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Transaction Per Month Chart
            const transChartEl = document.getElementById('transactionChart');
            if (transChartEl) {
                const transChart = echarts.init(transChartEl);

                // Default empty data
                let chartData = {
                    months: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov',
                        'Dec'
                    ],
                    amounts: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                    year: new Date().getFullYear()
                };

                // Function to fetch live data
                async function fetchMonthlyFunds() {
                    try {
                        const response = await fetch("{{ route('admin.monthly.funds') }}", {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });

                        if (!response.ok) throw new Error("Monthly funds fetch failed");

                        const data = await response.json();

                        if (data.monthlyData) {
                            // Update chart data
                            chartData.amounts = data.monthlyData;
                            chartData.year = data.year;

                            // Update the chart
                            updateChart();
                        }

                    } catch (error) {
                        console.error("❌ Error fetching monthly funds:", error);
                    }
                }

                // Function to update the chart with new data
                function updateChart() {
                    const option = {
                        animation: true,
                        grid: {
                            top: 30,
                            right: 30,
                            bottom: 40,
                            left: 60
                        },
                        title: {
                            text: `Monthly Funds (${chartData.year})`,
                            left: 'center',
                            textStyle: {
                                fontSize: 16,
                                fontWeight: 'bold',
                                color: '#111827'
                            }
                        },
                        xAxis: {
                            type: 'category',
                            data: chartData.months,
                            axisLine: {
                                lineStyle: {
                                    color: '#e5e7eb'
                                }
                            },
                            axisTick: {
                                show: false
                            },
                            axisLabel: {
                                color: '#6b7280',
                                fontSize: 12
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
                                color: '#6b7280',
                                fontSize: 12,
                                formatter: function(value) {
                                    return '₱' + value.toLocaleString();
                                }
                            },
                            splitLine: {
                                lineStyle: {
                                    color: '#f3f4f6'
                                }
                            }
                        },
                        series: [{
                            data: chartData.amounts,
                            type: 'line',
                            smooth: true,
                            symbol: 'circle',
                            symbolSize: 8,
                            lineStyle: {
                                color: 'rgba(59, 130, 246, 1)', // blue-500
                                width: 3
                            },
                            itemStyle: {
                                color: 'rgba(59, 130, 246, 1)',
                                borderColor: '#fff',
                                borderWidth: 2
                            },
                            areaStyle: {
                                color: {
                                    type: 'linear',
                                    x: 0,
                                    y: 0,
                                    x2: 0,
                                    y2: 1,
                                    colorStops: [{
                                            offset: 0,
                                            color: 'rgba(59, 130, 246, 0.2)'
                                        },
                                        {
                                            offset: 1,
                                            color: 'rgba(59, 130, 246, 0.01)'
                                        }
                                    ]
                                }
                            },
                            label: {
                                show: true,
                                position: 'top',
                                formatter: function(params) {
                                    return '₱' + params.value.toLocaleString();
                                },
                                fontSize: 11,
                                fontWeight: 'bold',
                                color: '#374151'
                            }
                        }],
                        tooltip: {
                            trigger: 'axis',
                            backgroundColor: 'rgba(255,255,255,0.95)',
                            borderColor: '#e5e7eb',
                            textStyle: {
                                color: '#1f2937'
                            },
                            formatter: function(params) {
                                const data = params[0];
                                return `
                                <div style="font-weight: bold; margin-bottom: 5px;">${data.name}</div>
                                <div>Amount: <span style="font-weight: bold; color: #3b82f6;">₱${data.value.toLocaleString()}</span></div>
                            `;
                            }
                        }
                    };

                    transChart.setOption(option);
                }

                // Initialize with empty data
                updateChart();

                // Fetch live data immediately
                fetchMonthlyFunds();

                // Refresh data every 30 seconds
                setInterval(fetchMonthlyFunds, 30000);

                // Handle window resize
                window.addEventListener('resize', () => transChart.resize());
            }
        });
    </script>



</body>

</html>
