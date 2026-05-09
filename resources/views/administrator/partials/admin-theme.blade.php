<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Merriweather:wght@700;900&display=swap"
    rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">

<style>
    :root {
        --admin-sidebar-w: 284px;
        --admin-header-h: 80px;
        --admin-navy: #06235f;
        --admin-navy-2: #0b3d91;
        --admin-blue: #135de8;
        --admin-blue-soft: #eaf2ff;
        --admin-page-bg: #f4f7fb;
        --admin-surface: #ffffff;
        --admin-line: #dbe5f2;
        --admin-text: #09183a;
        --admin-muted: #64708a;
        --admin-shadow: 0 14px 36px rgba(13, 37, 76, 0.08);
        --admin-shadow-soft: 0 8px 20px rgba(13, 37, 76, 0.06);
    }

    body.admin-page,
    body.admin-event-form-page,
    body.admin-dnc-form-page,
    body.admin-dnc-show-page {
        min-height: 100vh;
        background: var(--admin-page-bg) !important;
        color: var(--admin-text);
        font-family: "Inter", Arial, sans-serif !important;
        line-height: 1.5;
        letter-spacing: 0;
    }

    body.admin-page *,
    body.admin-event-form-page *,
    body.admin-dnc-form-page *,
    body.admin-dnc-show-page * {
        box-sizing: border-box;
        letter-spacing: 0;
    }

    body.admin-page h1,
    body.admin-page h2,
    body.admin-page h3,
    body.admin-event-form-page h1,
    body.admin-event-form-page h2,
    body.admin-dnc-form-page h1,
    body.admin-dnc-form-page h2,
    body.admin-dnc-show-page h1,
    body.admin-dnc-show-page h2 {
        color: var(--admin-text);
        font-family: "Inter", Arial, sans-serif !important;
        font-weight: 800;
    }

    body.admin-page .header {
        position: fixed !important;
        inset: 0 0 auto var(--admin-sidebar-w) !important;
        width: auto !important;
        height: var(--admin-header-h) !important;
        background: rgba(255, 255, 255, 0.96) !important;
        border: 0 !important;
        border-bottom: 1px solid var(--admin-line) !important;
        box-shadow: 0 4px 22px rgba(12, 32, 70, 0.06) !important;
        color: var(--admin-text) !important;
        z-index: 55 !important;
        backdrop-filter: blur(14px);
    }

    body.admin-page .header__inner {
        position: relative;
        height: 100% !important;
        max-width: none !important;
        margin: 0 !important;
        padding: 0 34px !important;
        display: flex !important;
        align-items: center !important;
        gap: 22px !important;
    }

    body.admin-page .header__left {
        display: flex !important;
        align-items: center !important;
        gap: 16px !important;
        margin-right: 8px !important;
    }

    body.admin-page .brand {
        display: none !important;
    }

    body.admin-page .menu-btn {
        width: 44px !important;
        height: 44px !important;
        display: inline-grid !important;
        place-items: center !important;
        border: 1px solid transparent !important;
        border-radius: 12px !important;
        background: transparent !important;
        color: var(--admin-text) !important;
        box-shadow: none !important;
        font-size: 24px !important;
        transition: background 0.2s ease, border-color 0.2s ease;
    }

    body.admin-page .menu-btn:hover {
        background: #f2f6fc !important;
        border-color: var(--admin-line) !important;
    }

    body.admin-page .logo-word {
        display: flex !important;
        align-items: center !important;
        margin-right: auto !important;
    }

    body.admin-page .logo-word img {
        width: 118px !important;
        height: auto !important;
        margin: 0 !important;
        object-fit: contain !important;
    }

    body.admin-page .notif {
        position: relative !important;
        width: 44px !important;
        height: 44px !important;
        display: inline-grid !important;
        place-items: center !important;
        border: 0 !important;
        background: transparent !important;
        color: var(--admin-text) !important;
        box-shadow: none !important;
        border-radius: 12px !important;
        margin-left: auto !important;
    }

    body.admin-page .notif::before {
        content: "\ef14";
        font-family: "remixicon";
        font-size: 24px;
        line-height: 1;
    }

    body.admin-page .notif::after {
        content: "3";
        position: absolute;
        top: 7px;
        right: 8px;
        width: 18px;
        height: 18px;
        display: grid;
        place-items: center;
        border-radius: 999px;
        background: var(--admin-blue);
        color: #fff;
        font-size: 11px;
        font-weight: 800;
        border: 2px solid #fff;
    }

    body.admin-page .sidebar {
        position: fixed !important;
        inset: 0 auto 0 0 !important;
        width: var(--admin-sidebar-w) !important;
        height: 100vh !important;
        padding: 24px 12px !important;
        display: flex !important;
        flex-direction: column !important;
        gap: 22px !important;
        background:
            radial-gradient(circle at 16% 7%, rgba(34, 114, 255, 0.3), transparent 24%),
            linear-gradient(180deg, #073179 0%, #052563 48%, #041b4c 100%) !important;
        color: #fff !important;
        border: 0 !important;
        box-shadow: 16px 0 38px rgba(8, 28, 66, 0.18) !important;
        z-index: 70 !important;
        overflow-y: auto !important;
        transform: translateX(0) !important;
    }

    body.admin-page .admin-sidebar__brand {
        min-height: 52px;
        padding: 0 10px 12px;
        display: flex;
        align-items: center;
        gap: 14px;
        color: #fff;
    }

    body.admin-page .admin-sidebar__brand-icon {
        width: 42px;
        height: 42px;
        display: grid;
        place-items: center;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.1);
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.1);
        color: #dceaff;
        font-size: 22px;
    }

    body.admin-page .admin-sidebar__brand-text {
        text-transform: uppercase;
        font-size: 20px;
        font-weight: 800;
    }

    body.admin-page .admin-sidebar__nav,
    body.admin-page .sidebar nav {
        display: flex !important;
        flex: 1 1 auto !important;
        flex-direction: column !important;
        gap: 10px !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    body.admin-page .side-link {
        height: 56px !important;
        padding: 0 16px !important;
        display: flex !important;
        align-items: center !important;
        gap: 14px !important;
        border-radius: 10px !important;
        color: rgba(255, 255, 255, 0.88) !important;
        text-decoration: none !important;
        font-size: 16px !important;
        font-weight: 600 !important;
        border: 1px solid transparent !important;
        background: transparent !important;
        box-shadow: none !important;
        transition: background 0.2s ease, color 0.2s ease, transform 0.2s ease;
    }

    body.admin-page .side-link i {
        width: 26px !important;
        display: inline-flex !important;
        justify-content: center !important;
        color: inherit !important;
        font-size: 24px !important;
        line-height: 1 !important;
    }

    body.admin-page .side-link:hover,
    body.admin-page .side-link.active {
        background: linear-gradient(135deg, #1f6cff, #1057de) !important;
        color: #fff !important;
        border-color: rgba(255, 255, 255, 0.14) !important;
        box-shadow: 0 12px 26px rgba(3, 21, 58, 0.28) !important;
        transform: translateX(2px);
    }

    body.admin-page .admin-sidebar__profile {
        position: relative;
        margin: auto 6px 0;
        padding: 10px 10px;
        display: flex;
        align-items: center;
        gap: 10px;
        border: 1px solid rgba(255, 255, 255, 0.88);
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.96);
        color: var(--admin-text);
        text-decoration: none;
        box-shadow: 0 14px 30px rgba(2, 17, 52, 0.26);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    body.admin-page .admin-sidebar__profile:hover {
        transform: translateY(-1px);
        box-shadow: 0 18px 34px rgba(2, 17, 52, 0.32);
    }

    body.admin-page .admin-sidebar__avatar {
        width: 46px;
        height: 46px;
        flex: 0 0 auto;
        display: grid;
        place-items: center;
        border-radius: 999px;
        background: #1f6cff;
        color: #fff;
        font-size: 20px;
        font-weight: 800;
        box-shadow: 0 12px 24px rgba(2, 17, 52, 0.3);
    }

    body.admin-page .admin-sidebar__profile-copy {
        min-width: 0;
        flex: 1 1 auto;
    }

    body.admin-page .admin-sidebar__profile-copy strong,
    body.admin-page .admin-sidebar__profile-copy small {
        display: block;
    }

    body.admin-page .admin-sidebar__profile-copy strong {
        color: var(--admin-text);
        font-size: 14px;
        font-weight: 800;
        line-height: 1.2;
    }

    body.admin-page .admin-sidebar__profile-copy small {
        margin-top: 2px;
        color: #4d5a73;
        font-size: 12px;
        font-weight: 600;
        line-height: 1.25;
    }

    body.admin-page .admin-sidebar__profile > i {
        color: #135de8;
        font-size: 22px;
        line-height: 1;
    }

    body.admin-page .overlay {
        position: fixed !important;
        inset: 0 !important;
        background: rgba(5, 17, 38, 0.42) !important;
        z-index: 65 !important;
        opacity: 0 !important;
        visibility: hidden !important;
        transition: opacity 0.2s ease, visibility 0.2s ease !important;
    }

    body.admin-page .overlay.show,
    body.admin-page .overlay.visible {
        opacity: 1 !important;
        visibility: visible !important;
    }

    body.admin-page .admin-logout-modal {
        position: fixed;
        inset: 0;
        z-index: 120;
        display: grid;
        place-items: center;
        padding: 18px;
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transition: opacity 0.18s ease, visibility 0.18s ease;
    }

    body.admin-page .admin-logout-modal.is-open {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
    }

    body.admin-page .admin-logout-modal__backdrop {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        border: 0;
        background: rgba(4, 15, 38, 0.52);
        backdrop-filter: blur(6px);
    }

    body.admin-page .admin-logout-modal__dialog {
        position: relative;
        width: min(420px, 100%);
        padding: 28px;
        border: 1px solid var(--admin-line);
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 28px 80px rgba(4, 15, 38, 0.26);
        text-align: center;
        transform: translateY(8px) scale(0.98);
        transition: transform 0.18s ease;
    }

    body.admin-page .admin-logout-modal.is-open .admin-logout-modal__dialog {
        transform: translateY(0) scale(1);
    }

    body.admin-page .admin-logout-modal__icon {
        width: 58px;
        height: 58px;
        display: grid;
        place-items: center;
        margin: 0 auto 16px;
        border-radius: 999px;
        background: var(--admin-blue-soft);
        color: var(--admin-blue);
        font-size: 28px;
    }

    body.admin-page .admin-logout-modal h2 {
        margin: 0 0 8px;
        color: var(--admin-text);
        font-size: 24px;
        font-weight: 800;
    }

    body.admin-page .admin-logout-modal p {
        margin: 0;
        color: var(--admin-muted);
        font-size: 14px;
        line-height: 1.5;
    }

    body.admin-page .admin-logout-modal__actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-top: 24px;
    }

    body.admin-page .admin-logout-modal__btn {
        min-height: 44px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 800;
        text-decoration: none;
        cursor: pointer;
    }

    body.admin-page .admin-logout-modal__btn--secondary {
        border: 1px solid #cfd9e8;
        background: #fff;
        color: #53627e;
    }

    body.admin-page .admin-logout-modal__btn--primary {
        border: 1px solid var(--admin-blue);
        background: var(--admin-blue);
        color: #fff;
        box-shadow: 0 10px 22px rgba(19, 93, 232, 0.2);
    }

    body.admin-page .main {
        width: calc(100% - var(--admin-sidebar-w)) !important;
        max-width: none !important;
        min-height: calc(100vh - var(--admin-header-h)) !important;
        margin: var(--admin-header-h) 0 0 var(--admin-sidebar-w) !important;
        padding: 34px 36px 48px !important;
        background: transparent !important;
        overflow: visible !important;
    }

    body.admin-page .main > .container,
    body.admin-page .main .container,
    body.admin-page .content,
    body.admin-page .page-container,
    body.admin-page .dashboard-container {
        width: 100% !important;
        max-width: none !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    body.admin-page .main > div[style*="max-width"] {
        max-width: none !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
    }

    body.admin-page .main > .container > h1:first-child,
    body.admin-page .page-header h1,
    body.admin-page .page-header h2,
    body.admin-page .dashboard-title,
    body.admin-page .content-title {
        margin: 0 0 20px !important;
        color: var(--admin-text) !important;
        font-size: clamp(26px, 2.2vw, 36px) !important;
        line-height: 1.15 !important;
        font-weight: 800 !important;
    }

    body.admin-page .page-header p,
    body.admin-page .dashboard-header p,
    body.admin-page .muted {
        color: var(--admin-muted) !important;
    }

    body.admin-page .page-header,
    body.admin-page .dashboard-header,
    body.admin-page .section-heading {
        margin-bottom: 24px !important;
    }

    body.admin-page .topbar {
        display: flex !important;
        align-items: flex-start !important;
        justify-content: space-between !important;
        gap: 18px !important;
        margin-bottom: 22px !important;
    }

    body.admin-page .tabs-nav,
    body.admin-page .tabs,
    body.admin-page .tab-navigation,
    body.admin-page .tab-buttons,
    body.admin-page .filter-tabs {
        display: flex !important;
        align-items: center !important;
        gap: 28px !important;
        margin: 6px 0 24px !important;
        padding: 0 !important;
        background: transparent !important;
        border-bottom: 1px solid var(--admin-line) !important;
        box-shadow: none !important;
        overflow-x: auto;
    }

    body.admin-page .tab-btn,
    body.admin-page .tab,
    body.admin-page .tab-button,
    body.admin-page .tab-link,
    body.admin-page .filter-tab {
        min-height: 48px !important;
        padding: 0 2px 14px !important;
        border: 0 !important;
        border-bottom: 3px solid transparent !important;
        border-radius: 0 !important;
        background: transparent !important;
        color: #66728b !important;
        box-shadow: none !important;
        font-size: 15px !important;
        font-weight: 700 !important;
        white-space: nowrap !important;
    }

    body.admin-page .tab-btn.active,
    body.admin-page .tab.active,
    body.admin-page .tab-button.active,
    body.admin-page .tab-link.active,
    body.admin-page .filter-tab.active {
        color: var(--admin-blue) !important;
        border-bottom-color: var(--admin-blue) !important;
    }

    body.admin-page .stats-grid,
    body.admin-page .metrics-grid,
    body.admin-page .summary-grid,
    body.admin-page .stat-grid,
    body.admin-page .dashboard-stats,
    body.admin-page .cards-grid,
    body.admin-page .panels-grid {
        display: grid !important;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)) !important;
        gap: 22px !important;
        margin: 20px 0 24px !important;
    }

    body.admin-page .panels-grid {
        grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)) !important;
    }

    body.admin-page .stat-card,
    body.admin-page .metric-card,
    body.admin-page .summary-card,
    body.admin-page .analytics-card,
    body.admin-page .chart-card,
    body.admin-page .table-card,
    body.admin-page .filter-card,
    body.admin-page .filters-card,
    body.admin-page .panel,
    body.admin-page .dashboard-card,
    body.admin-page .content-card,
    body.admin-page .card {
        background: var(--admin-surface) !important;
        border: 1px solid var(--admin-line) !important;
        border-radius: 14px !important;
        box-shadow: var(--admin-shadow-soft) !important;
    }

    body.admin-campaign-page .cards-grid > .card {
        min-height: 132px !important;
        padding: 22px 24px !important;
        display: flex !important;
        align-items: center !important;
        gap: 18px !important;
    }

    body.admin-page .stat-card .icon-wrap,
    body.admin-page .metric-card .icon-wrap,
    body.admin-page .stat-icon,
    body.admin-page .metric-icon {
        width: 66px !important;
        height: 66px !important;
        flex: 0 0 auto !important;
        display: grid !important;
        place-items: center !important;
        border-radius: 999px !important;
        background: var(--admin-blue-soft) !important;
        color: var(--admin-blue) !important;
        font-size: 28px !important;
    }

    body.admin-page .stat-card,
    body.admin-page .metric-card {
        min-height: 132px !important;
        padding: 22px 24px !important;
        display: block !important;
    }

    body.admin-campaign-page .cards-grid > .card .kv,
    body.admin-page .stat-card .count,
    body.admin-page .stat-value,
    body.admin-page .metric-value {
        margin: 0 !important;
        color: var(--admin-text) !important;
        font-size: 28px !important;
        line-height: 1.1 !important;
        font-weight: 800 !important;
    }

    body.admin-page .stat-card h3,
    body.admin-page .metric-card h3,
    body.admin-page .stat-card p,
    body.admin-page .metric-card p,
    body.admin-campaign-page .cards-grid > .card .small,
    body.admin-campaign-page .cards-grid > .card .stat-title,
    body.admin-page .stat-label,
    body.admin-page .metric-label {
        margin: 6px 0 0 !important;
        color: var(--admin-muted) !important;
        font-size: 14px !important;
        font-weight: 500 !important;
    }

    body.admin-page .summary-card,
    body.admin-page .card.stat {
        min-height: 132px !important;
        padding: 22px 24px !important;
    }

    body.admin-page .summary-card .card-head,
    body.admin-page .card.stat .stat-body {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 16px !important;
        margin: 0 0 14px !important;
        padding: 0 !important;
        border: 0 !important;
    }

    body.admin-page .summary-card .card-icon,
    body.admin-page .card.stat .stat-icon {
        width: 56px !important;
        height: 56px !important;
        display: grid !important;
        place-items: center !important;
        border-radius: 14px !important;
        background: var(--admin-blue-soft) !important;
        color: var(--admin-blue) !important;
        font-size: 24px !important;
    }

    body.admin-page .summary-card .card-value,
    body.admin-page .card.stat .big {
        margin: 0 !important;
        color: var(--admin-text) !important;
        font-size: 30px !important;
        line-height: 1.1 !important;
        font-weight: 800 !important;
    }

    body.admin-page .summary-card .card-label,
    body.admin-page .card.stat .muted {
        margin: 0 !important;
        color: var(--admin-muted) !important;
        font-size: 14px !important;
        font-weight: 600 !important;
    }

    body.admin-page .section-header,
    body.admin-page .card-header,
    body.admin-page .panel-header,
    body.admin-page .table-header {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 16px !important;
        padding-bottom: 14px !important;
        margin-bottom: 18px !important;
        border-bottom: 1px solid var(--admin-line) !important;
        background: transparent !important;
    }

    body.admin-page .section-header h2,
    body.admin-page .card-header h2,
    body.admin-page .panel-header h2,
    body.admin-page .table-header h2 {
        margin: 0 !important;
        font-size: 19px !important;
        line-height: 1.3 !important;
        font-weight: 800 !important;
    }

    body.admin-page .section-header {
        justify-content: flex-start !important;
    }

    body.admin-page .filters[role="tablist"],
    body.admin-page .table-toolbar .filter-tabs {
        padding: 0 !important;
        margin: 0 !important;
        border: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
        gap: 8px !important;
    }

    body.admin-page .filter-btn {
        min-height: 38px !important;
        padding: 0 14px !important;
        border: 1px solid #cfd9e8 !important;
        border-radius: 10px !important;
        background: #fff !important;
        color: #53627e !important;
        font-size: 14px !important;
        font-weight: 800 !important;
        box-shadow: none !important;
    }

    body.admin-page .filter-btn.active {
        border-color: #a9c7ff !important;
        background: var(--admin-blue-soft) !important;
        color: var(--admin-blue) !important;
    }

    body.admin-page .search-filter,
    body.admin-page .filters,
    body.admin-page .filter-bar,
    body.admin-page .toolbar,
    body.admin-page .table-toolbar,
    body.admin-page .controls,
    body.admin-page .search-panel {
        display: flex !important;
        align-items: center !important;
        flex-wrap: wrap !important;
        gap: 16px !important;
        padding: 18px !important;
        margin: 18px 0 !important;
        background: var(--admin-surface) !important;
        border: 1px solid var(--admin-line) !important;
        border-radius: 14px !important;
        box-shadow: var(--admin-shadow-soft) !important;
    }

    body.admin-page .search-row {
        width: 100% !important;
        display: flex !important;
        align-items: center !important;
        flex-wrap: wrap !important;
        gap: 16px !important;
        padding: 0 !important;
        margin: 0 !important;
        background: transparent !important;
        border: 0 !important;
        box-shadow: none !important;
    }

    body.admin-page .search-field {
        position: relative !important;
        flex: 1 1 320px !important;
    }

    body.admin-page .search-field input {
        width: 100% !important;
        padding-left: 42px !important;
    }

    body.admin-page .search-icon {
        position: absolute !important;
        top: 50% !important;
        left: 14px !important;
        z-index: 1 !important;
        transform: translateY(-50%) !important;
        color: #7a879e !important;
        font-size: 18px !important;
    }

    body.admin-page .actions {
        margin-left: auto !important;
        display: flex !important;
        align-items: center !important;
        gap: 12px !important;
        flex-wrap: wrap !important;
    }

    body.admin-page input[type="text"],
    body.admin-page input[type="search"],
    body.admin-page input[type="email"],
    body.admin-page input[type="number"],
    body.admin-page input[type="date"],
    body.admin-page select,
    body.admin-page textarea {
        min-height: 44px !important;
        border: 1px solid #cfd9e8 !important;
        border-radius: 10px !important;
        background: #fff !important;
        color: var(--admin-text) !important;
        font-family: "Inter", Arial, sans-serif !important;
        font-size: 14px !important;
        box-shadow: none !important;
    }

    body.admin-page input:focus,
    body.admin-page select:focus,
    body.admin-page textarea:focus {
        outline: none !important;
        border-color: var(--admin-blue) !important;
        box-shadow: 0 0 0 4px rgba(19, 93, 232, 0.12) !important;
    }

    body.admin-page .btn,
    body.admin-page .button,
    body.admin-page button,
    body.admin-page a.button {
        font-family: "Inter", Arial, sans-serif !important;
    }

    body.admin-page .btn-primary,
    body.admin-page .primary-btn,
    body.admin-page .btn.primary,
    body.admin-page button.primary,
    body.admin-page .create-btn,
    body.admin-page .btn-create {
        min-height: 44px;
        border: 1px solid var(--admin-blue) !important;
        border-radius: 10px !important;
        background: var(--admin-blue) !important;
        color: #fff !important;
        font-weight: 800 !important;
        box-shadow: 0 10px 22px rgba(19, 93, 232, 0.2) !important;
    }

    body.admin-page .btn-secondary,
    body.admin-page .secondary-btn,
    body.admin-page .btn.outline,
    body.admin-page button.outline,
    body.admin-page .clear-btn,
    body.admin-page .outline-btn {
        min-height: 44px;
        border: 1px solid #cfd9e8 !important;
        border-radius: 10px !important;
        background: #fff !important;
        color: var(--admin-blue) !important;
        font-weight: 800 !important;
        box-shadow: none !important;
    }

    body.admin-page .table-responsive,
    body.admin-page .table-wrap,
    body.admin-page .table-container {
        overflow-x: auto !important;
        background: var(--admin-surface) !important;
        border: 1px solid var(--admin-line) !important;
        border-radius: 14px !important;
        box-shadow: var(--admin-shadow-soft) !important;
    }

    body.admin-page table {
        width: 100% !important;
        border-collapse: separate !important;
        border-spacing: 0 !important;
        background: #fff !important;
        color: var(--admin-text) !important;
        font-family: "Inter", Arial, sans-serif !important;
    }

    body.admin-page th {
        background: #f7faff !important;
        color: #52627e !important;
        font-size: 12px !important;
        font-weight: 800 !important;
        text-transform: uppercase !important;
    }

    body.admin-page th,
    body.admin-page td {
        padding: 14px 16px !important;
        border-bottom: 1px solid #e6edf6 !important;
        vertical-align: middle !important;
    }

    body.admin-page tr:last-child td {
        border-bottom: 0 !important;
    }

    body.admin-page .empty-state,
    body.admin-page .empty-card,
    body.admin-page .no-records,
    body.admin-page .no-data,
    body.admin-page .empty {
        min-height: 260px;
        padding: 42px 24px !important;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        background: var(--admin-surface) !important;
        border: 2px dashed #bdd3f4 !important;
        border-radius: 16px !important;
        color: var(--admin-muted) !important;
        box-shadow: none !important;
    }

    body.admin-page .empty-state h3,
    body.admin-page .empty-card h3,
    body.admin-page .no-records h3,
    body.admin-page .no-data h3 {
        margin: 10px 0 6px !important;
        color: var(--admin-text) !important;
        font-size: 22px !important;
        font-weight: 800 !important;
    }

    body.admin-page .admin-empty-state {
        grid-column: 1 / -1;
        width: 100%;
        min-height: 220px;
        padding: 46px 24px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        background: transparent;
        border: 0;
        box-shadow: none;
        color: #5f6b7a;
    }

    body.admin-page .admin-empty-state__icon {
        margin-bottom: 18px;
        color: #aeb6c2;
        font-size: 58px;
        line-height: 1;
    }

    body.admin-page .admin-empty-state h3 {
        margin: 0 0 10px;
        color: #0f172a;
        font-size: 22px;
        font-weight: 800;
        line-height: 1.25;
    }

    body.admin-page .admin-empty-state p {
        max-width: 430px;
        margin: 0;
        color: #64748b;
        font-size: 15px;
        line-height: 1.6;
    }

    body.admin-page .admin-empty-state--compact {
        min-height: 150px;
        padding: 30px 18px;
    }

    body.admin-page .admin-empty-state--compact .admin-empty-state__icon {
        margin-bottom: 12px;
        font-size: 42px;
    }

    body.admin-page .admin-empty-state--compact h3 {
        font-size: 17px;
    }

    body.admin-page .admin-empty-state--table {
        min-height: 190px;
        padding: 34px 18px;
    }

    body.admin-page .modal-content {
        border: 1px solid var(--admin-line) !important;
        border-radius: 16px !important;
        box-shadow: 0 24px 70px rgba(5, 17, 38, 0.22) !important;
    }

    body.admin-page .badge,
    body.admin-page .status-badge {
        border-radius: 999px !important;
        font-weight: 800 !important;
    }

    body.admin-directory-page .tab-content {
        background: transparent !important;
        border: 0 !important;
        box-shadow: none !important;
        padding: 0 !important;
    }

    body.admin-directory-page .accomplishments-grid {
        display: grid !important;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)) !important;
        gap: 22px !important;
    }

    body.admin-directory-page .accomplishment-card {
        overflow: hidden !important;
        background: #fff !important;
        border: 1px solid var(--admin-line) !important;
        border-radius: 16px !important;
        box-shadow: var(--admin-shadow-soft) !important;
    }

    body.admin-directory-page .accomplishment-card__image {
        aspect-ratio: 16 / 9 !important;
        overflow: hidden !important;
        background: #edf4ff !important;
    }

    body.admin-directory-page .accomplishment-card__image img {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
        display: block !important;
    }

    body.admin-directory-page .accomplishment-card__content {
        padding: 20px !important;
    }

    body.admin-directory-page .accomplishment-card .stats-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        gap: 10px !important;
        margin: 16px 0 !important;
    }

    body.admin-directory-page .stat-item {
        padding: 12px !important;
        border: 1px solid #e6edf6 !important;
        border-radius: 12px !important;
        background: #f8fbff !important;
    }

    body.admin-directory-page .stat-item .stat-icon {
        width: 36px !important;
        height: 36px !important;
        border-radius: 10px !important;
        font-size: 18px !important;
    }

    body.admin-directory-page .stat-item .stat-value {
        font-size: 16px !important;
        line-height: 1.2 !important;
    }

    body.admin-directory-page .stat-item .stat-label {
        font-size: 11px !important;
    }

    body.admin-directory-page .event-status,
    body.admin-directory-page .report-year,
    body.admin-directory-page .pill,
    body.admin-page .chip {
        border-radius: 999px !important;
        font-weight: 800 !important;
    }

    body.admin-event-form-page .header-banner {
        padding: 58px 16px 122px !important;
        background: linear-gradient(180deg, #f8fbff 0%, #edf4ff 100%) !important;
        color: var(--admin-text) !important;
        clip-path: none !important;
        border-bottom: 1px solid var(--admin-line) !important;
        text-align: left !important;
    }

    body.admin-event-form-page .header-banner h1 {
        color: var(--admin-text) !important;
        text-shadow: none !important;
        font-size: clamp(30px, 4vw, 46px) !important;
    }

    body.admin-event-form-page .header-banner p {
        color: var(--admin-muted) !important;
        opacity: 1 !important;
        margin-left: 0 !important;
    }

    body.admin-event-form-page .form-card,
    body.admin-dnc-form-page .card,
    body.admin-dnc-form-page .form-container,
    body.admin-dnc-show-page .container {
        background: var(--admin-surface) !important;
        border: 1px solid var(--admin-line) !important;
        border-radius: 16px !important;
        box-shadow: var(--admin-shadow) !important;
    }

    body.admin-event-form-page .form-card {
        margin-top: -84px !important;
        padding: clamp(24px, 4vw, 46px) !important;
        max-width: 1020px !important;
    }

    body.admin-event-form-page .form-header {
        text-align: left !important;
        margin-bottom: 28px !important;
        padding-bottom: 22px !important;
    }

    body.admin-event-form-page .form-header h2 {
        color: var(--admin-text) !important;
        font-size: 28px !important;
    }

    body.admin-event-form-page .form-section-title {
        color: var(--admin-text) !important;
        text-transform: none !important;
        letter-spacing: 0 !important;
        font-size: 16px !important;
    }

    body.admin-event-form-page .btn-primary {
        background: var(--admin-blue) !important;
        border-color: var(--admin-blue) !important;
        border-radius: 10px !important;
        box-shadow: 0 10px 24px rgba(19, 93, 232, 0.22) !important;
    }

    body.admin-dnc-form-page,
    body.admin-dnc-show-page {
        padding: 32px 18px;
    }

    body.admin-dnc-form-page .container,
    body.admin-dnc-show-page .container {
        max-width: 1040px !important;
        margin: 0 auto !important;
    }

    body.admin-dnc-form-page h1,
    body.admin-dnc-show-page h1 {
        margin-bottom: 18px !important;
        font-size: clamp(28px, 4vw, 38px) !important;
    }

    @media (max-width: 1100px) {
        body.admin-page .header {
            left: 0 !important;
            height: 72px !important;
        }

        body.admin-page .header__inner {
            padding: 0 20px !important;
        }

        body.admin-page .header::after,
        body.admin-page .header__inner::after,
        body.admin-page .header__inner::before {
            display: none !important;
        }

        body.admin-page .sidebar {
            transform: translateX(-100%) !important;
        }

        body.admin-page .sidebar.open,
        body.admin-page .sidebar.visible {
            transform: translateX(0) !important;
        }

        body.admin-page .main {
            width: 100% !important;
            margin: 72px 0 0 !important;
            padding: 24px 18px 42px !important;
        }
    }

    @media (max-width: 760px) {
        :root {
            --admin-sidebar-w: 276px;
        }

        body.admin-page .logo-word img {
            width: 96px !important;
        }

        body.admin-page .notif {
            display: none !important;
        }

        body.admin-page .main {
            padding: 20px 14px 36px !important;
        }

        body.admin-page .topbar {
            flex-direction: column !important;
            align-items: stretch !important;
        }

        body.admin-page .stats-grid,
        body.admin-page .metrics-grid,
        body.admin-page .summary-grid,
        body.admin-page .stat-grid,
        body.admin-page .dashboard-stats,
        body.admin-page .cards-grid,
        body.admin-page .panels-grid {
            grid-template-columns: 1fr !important;
            gap: 14px !important;
        }

        body.admin-page .stat-card,
        body.admin-page .metric-card,
        body.admin-page .summary-card {
            min-height: 112px !important;
            padding: 18px !important;
        }

        body.admin-page .tabs-nav,
        body.admin-page .tabs,
        body.admin-page .tab-navigation,
        body.admin-page .tab-buttons,
        body.admin-page .filter-tabs {
            gap: 22px !important;
            margin-bottom: 18px !important;
        }

        body.admin-page .search-filter,
        body.admin-page .filters,
        body.admin-page .filter-bar,
        body.admin-page .toolbar,
        body.admin-page .table-toolbar,
        body.admin-page .controls {
            padding: 14px !important;
        }

        body.admin-page input[type="text"],
        body.admin-page input[type="search"],
        body.admin-page input[type="email"],
        body.admin-page input[type="number"],
        body.admin-page input[type="date"],
        body.admin-page select,
        body.admin-page textarea {
            width: 100% !important;
        }

        body.admin-event-form-page .header-banner {
            padding: 40px 16px 96px !important;
        }

        body.admin-event-form-page .form-card {
            margin-top: -62px !important;
            padding: 22px !important;
        }
    }

    /*
     * Admin UI polish layer.
     * This stays scoped to administrator body classes so public/user pages keep
     * their existing layouts, routes, scripts, and component styles.
     */
    :root {
        --admin-sidebar-w: 276px;
        --admin-header-h: 76px;
        --admin-navy: #10213f;
        --admin-navy-2: #172f59;
        --admin-blue: #2563eb;
        --admin-blue-dark: #1d4ed8;
        --admin-blue-soft: #eff6ff;
        --admin-green: #059669;
        --admin-green-soft: #ecfdf5;
        --admin-amber: #d97706;
        --admin-amber-soft: #fffbeb;
        --admin-red: #dc2626;
        --admin-red-soft: #fef2f2;
        --admin-purple: #7c3aed;
        --admin-purple-soft: #f5f3ff;
        --admin-page-bg: #f6f8fb;
        --admin-surface: #ffffff;
        --admin-surface-soft: #f8fafc;
        --admin-line: #dbe3ee;
        --admin-line-strong: #cbd5e1;
        --admin-text: #0f172a;
        --admin-muted: #64748b;
        --admin-radius: 8px;
        --admin-shadow: 0 18px 42px rgba(15, 23, 42, 0.08);
        --admin-shadow-soft: 0 6px 18px rgba(15, 23, 42, 0.06);
    }

    body.admin-page,
    body.admin-event-form-page,
    body.admin-dnc-form-page,
    body.admin-dnc-show-page {
        background:
            linear-gradient(180deg, #f8fbff 0%, #f4f7fb 44%, #eef3f8 100%) !important;
        color: var(--admin-text) !important;
        text-rendering: optimizeLegibility;
        -webkit-font-smoothing: antialiased;
    }

    body.admin-page a,
    body.admin-page button,
    body.admin-page input,
    body.admin-page select,
    body.admin-page textarea,
    body.admin-event-form-page a,
    body.admin-event-form-page button,
    body.admin-event-form-page input,
    body.admin-event-form-page select,
    body.admin-event-form-page textarea,
    body.admin-dnc-form-page a,
    body.admin-dnc-form-page button,
    body.admin-dnc-form-page input,
    body.admin-dnc-form-page select,
    body.admin-dnc-form-page textarea {
        font-family: "Inter", Arial, sans-serif !important;
    }

    body.admin-page .header {
        height: var(--admin-header-h) !important;
        background: rgba(255, 255, 255, 0.92) !important;
        border-bottom: 1px solid rgba(203, 213, 225, 0.76) !important;
        box-shadow: 0 8px 28px rgba(15, 23, 42, 0.06) !important;
    }

    body.admin-page .header__inner {
        padding: 0 clamp(18px, 2.6vw, 36px) !important;
    }

    body.admin-page .menu-btn {
        width: 42px !important;
        height: 42px !important;
        border-radius: var(--admin-radius) !important;
        border-color: #dbe3ee !important;
        background: #fff !important;
        color: var(--admin-text) !important;
    }

    body.admin-page .menu-btn:hover,
    body.admin-page .menu-btn:focus-visible {
        background: var(--admin-blue-soft) !important;
        border-color: #bfdbfe !important;
        color: var(--admin-blue) !important;
        outline: none !important;
    }

    body.admin-page .logo-word {
        gap: 12px !important;
    }

    body.admin-page .logo-word img {
        width: 112px !important;
        max-height: 50px !important;
        margin-top: 0 !important;
        object-fit: contain !important;
    }

    body.admin-page .notif {
        width: 42px !important;
        height: 42px !important;
        margin-left: 0 !important;
        border: 1px solid #dbe3ee !important;
        border-radius: var(--admin-radius) !important;
        background: #fff !important;
        color: #475569 !important;
    }

    body.admin-page .notif:hover,
    body.admin-page .notif:focus-visible {
        background: var(--admin-blue-soft) !important;
        border-color: #bfdbfe !important;
        color: var(--admin-blue) !important;
        outline: none !important;
    }

    body.admin-page .notif::after {
        content: "" !important;
        top: 9px !important;
        right: 10px !important;
        width: 9px !important;
        height: 9px !important;
        border: 2px solid #fff !important;
        background: var(--admin-green) !important;
    }

    body.admin-page .sidebar {
        width: var(--admin-sidebar-w) !important;
        padding: 20px 12px !important;
        gap: 18px !important;
        background:
            linear-gradient(180deg, #111827 0%, #10213f 52%, #0b1730 100%) !important;
        box-shadow: 18px 0 36px rgba(15, 23, 42, 0.18) !important;
    }

    body.admin-page .admin-sidebar__brand {
        min-height: auto !important;
        padding: 4px 10px 16px !important;
        border-bottom: 1px solid rgba(226, 232, 240, 0.14) !important;
    }

    body.admin-page .admin-sidebar__brand-icon {
        width: 42px !important;
        height: 42px !important;
        border-radius: var(--admin-radius) !important;
        background: rgba(96, 165, 250, 0.14) !important;
        color: #bfdbfe !important;
    }

    body.admin-page .admin-sidebar__brand-text {
        font-size: 13px !important;
        font-weight: 800 !important;
        letter-spacing: 0.08em !important;
    }

    body.admin-page .admin-sidebar__nav,
    body.admin-page .sidebar nav {
        gap: 6px !important;
    }

    body.admin-page .side-link {
        position: relative !important;
        height: 48px !important;
        padding: 0 13px !important;
        border-radius: var(--admin-radius) !important;
        color: rgba(226, 232, 240, 0.82) !important;
        font-size: 14px !important;
        font-weight: 650 !important;
        line-height: 1 !important;
    }

    body.admin-page .side-link i {
        width: 22px !important;
        font-size: 20px !important;
        color: rgba(191, 219, 254, 0.92) !important;
    }

    body.admin-page .side-link::before {
        content: "" !important;
        position: absolute !important;
        left: 7px !important;
        top: 50% !important;
        width: 3px !important;
        height: 0 !important;
        border-radius: 999px !important;
        background: #93c5fd !important;
        transform: translateY(-50%) !important;
        transition: height 0.2s ease !important;
    }

    body.admin-page .side-link:hover,
    body.admin-page .side-link.active {
        background: rgba(37, 99, 235, 0.2) !important;
        color: #fff !important;
        border-color: rgba(147, 197, 253, 0.2) !important;
        box-shadow: none !important;
        transform: none !important;
    }

    body.admin-page .side-link:hover::before,
    body.admin-page .side-link.active::before {
        height: 24px !important;
    }

    body.admin-page .side-link.active {
        background: linear-gradient(90deg, rgba(37, 99, 235, 0.34), rgba(37, 99, 235, 0.12)) !important;
    }

    body.admin-page .admin-sidebar__profile {
        margin: auto 4px 0 !important;
        padding: 10px !important;
        border: 1px solid rgba(226, 232, 240, 0.14) !important;
        border-radius: var(--admin-radius) !important;
        background: rgba(255, 255, 255, 0.08) !important;
        color: #fff !important;
        box-shadow: none !important;
    }

    body.admin-page .admin-sidebar__profile:hover {
        background: rgba(255, 255, 255, 0.12) !important;
        transform: none !important;
        box-shadow: none !important;
    }

    body.admin-page .admin-sidebar__avatar {
        width: 40px !important;
        height: 40px !important;
        background: #2563eb !important;
        box-shadow: none !important;
    }

    body.admin-page .admin-sidebar__profile-copy strong,
    body.admin-page .admin-sidebar__profile-copy small {
        color: inherit !important;
    }

    body.admin-page .admin-sidebar__profile-copy small {
        color: rgba(226, 232, 240, 0.72) !important;
    }

    body.admin-page .admin-sidebar__profile > i {
        color: #bfdbfe !important;
    }

    body.admin-page .main {
        width: calc(100% - var(--admin-sidebar-w)) !important;
        min-height: calc(100vh - var(--admin-header-h)) !important;
        margin: var(--admin-header-h) 0 0 var(--admin-sidebar-w) !important;
        padding: clamp(24px, 3vw, 38px) clamp(20px, 3vw, 40px) 48px !important;
    }

    body.admin-page .main > .container,
    body.admin-page .main .container,
    body.admin-page .content,
    body.admin-page .dashboard-container {
        max-width: 1480px !important;
        margin-left: auto !important;
        margin-right: auto !important;
    }

    body.admin-page .admin-page-heading,
    body.admin-page .page-header,
    body.admin-page .dashboard-header {
        display: block !important;
        margin: 0 0 24px !important;
    }

    body.admin-page .admin-page-heading h1,
    body.admin-page .page-header h1,
    body.admin-page .page-header h2,
    body.admin-page .dashboard-title,
    body.admin-page .content-title {
        margin: 0 0 7px !important;
        color: var(--admin-text) !important;
        font-size: clamp(26px, 2.1vw, 34px) !important;
        font-weight: 800 !important;
        line-height: 1.12 !important;
    }

    body.admin-page .admin-page-heading p,
    body.admin-page .page-header p,
    body.admin-page .dashboard-header p {
        max-width: 760px !important;
        margin: 0 !important;
        color: var(--admin-muted) !important;
        font-size: 15px !important;
        line-height: 1.6 !important;
    }

    body.admin-page .topbar {
        align-items: center !important;
        margin-bottom: 24px !important;
        padding: 0 !important;
        background: transparent !important;
        border: 0 !important;
        border-radius: 0 !important;
        box-shadow: none !important;
    }

    body.admin-page .stats-grid,
    body.admin-page .metrics-grid,
    body.admin-page .summary-grid,
    body.admin-page .stat-grid,
    body.admin-page .dashboard-stats,
    body.admin-page .cards-grid {
        grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)) !important;
        gap: 18px !important;
        margin: 0 0 24px !important;
    }

    body.admin-page .panels-grid,
    body.admin-page .charts-row,
    body.admin-page .charts-grid,
    body.admin-page .lower-grid {
        grid-template-columns: repeat(auto-fit, minmax(min(100%, 420px), 1fr)) !important;
        gap: 20px !important;
        margin: 0 0 24px !important;
    }

    body.admin-page .stat-card,
    body.admin-page .metric-card,
    body.admin-page .summary-card,
    body.admin-page .analytics-card,
    body.admin-page .chart-card,
    body.admin-page .table-card,
    body.admin-page .filter-card,
    body.admin-page .filters-card,
    body.admin-page .panel,
    body.admin-page .dashboard-card,
    body.admin-page .content-card,
    body.admin-page .card,
    body.admin-page .record,
    body.admin-page .event-item,
    body.admin-page .vol-item,
    body.admin-page .account-item,
    body.admin-page .location {
        border: 1px solid var(--admin-line) !important;
        border-radius: var(--admin-radius) !important;
        background: rgba(255, 255, 255, 0.96) !important;
        box-shadow: var(--admin-shadow-soft) !important;
        transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease !important;
    }

    body.admin-page .card:hover,
    body.admin-page .summary-card:hover,
    body.admin-page .stat-card:hover,
    body.admin-page .event-item:hover,
    body.admin-page .vol-item:hover,
    body.admin-page .account-item:hover,
    body.admin-page .record:hover,
    body.admin-page .location:hover {
        border-color: #bfdbfe !important;
        box-shadow: var(--admin-shadow) !important;
        transform: translateY(-1px) !important;
    }

    body.admin-page .stat-card,
    body.admin-page .metric-card,
    body.admin-page .summary-card,
    body.admin-page .card.stat,
    body.admin-campaign-page .cards-grid > .card {
        min-height: 126px !important;
        padding: 20px !important;
        overflow: hidden !important;
        border-left: 0 !important;
    }

    body.admin-page .stat-card .icon-wrap,
    body.admin-page .metric-card .icon-wrap,
    body.admin-page .stat-icon,
    body.admin-page .metric-icon,
    body.admin-page .summary-card .card-icon,
    body.admin-page .card.stat .stat-icon,
    body.admin-page .stat-ico,
    body.admin-page .icon-circle {
        width: 48px !important;
        height: 48px !important;
        border-radius: var(--admin-radius) !important;
        background: var(--admin-blue-soft) !important;
        color: var(--admin-blue) !important;
        box-shadow: none !important;
    }

    body.admin-page .stat-card .count,
    body.admin-page .stat-value,
    body.admin-page .metric-value,
    body.admin-page .summary-card .card-value,
    body.admin-page .card.stat .big,
    body.admin-campaign-page .cards-grid > .card .kv {
        color: var(--admin-text) !important;
        font-size: clamp(24px, 2vw, 30px) !important;
        font-weight: 800 !important;
        letter-spacing: 0 !important;
    }

    body.admin-page .stat-card h3,
    body.admin-page .metric-card h3,
    body.admin-page .stat-card p,
    body.admin-page .metric-card p,
    body.admin-page .summary-card .card-label,
    body.admin-page .card.stat .muted,
    body.admin-page .stat-label,
    body.admin-page .metric-label,
    body.admin-campaign-page .cards-grid > .card .small {
        color: var(--admin-muted) !important;
        font-size: 13px !important;
        font-weight: 650 !important;
        line-height: 1.45 !important;
    }

    body.admin-page .section-header,
    body.admin-page .card-header,
    body.admin-page .panel-header,
    body.admin-page .table-header,
    body.admin-page .table-toolbar {
        align-items: center !important;
        margin-bottom: 16px !important;
        padding: 0 0 14px !important;
        border-bottom: 1px solid var(--admin-line) !important;
        background: transparent !important;
    }

    body.admin-page .section-header h2,
    body.admin-page .card-header h2,
    body.admin-page .card-header h3,
    body.admin-page .panel-header h2,
    body.admin-page .table-header h2,
    body.admin-page .table-toolbar h2 {
        margin: 0 !important;
        color: var(--admin-text) !important;
        font-size: 18px !important;
        font-weight: 800 !important;
        line-height: 1.3 !important;
    }

    body.admin-page .tabs-nav,
    body.admin-page .tabs,
    body.admin-page .tab-navigation,
    body.admin-page .tab-buttons {
        gap: 22px !important;
        margin: 0 0 24px !important;
        border-bottom: 1px solid var(--admin-line) !important;
    }

    body.admin-page .tab-btn,
    body.admin-page .tab,
    body.admin-page .tab-button,
    body.admin-page .tab-link {
        min-height: 46px !important;
        color: #64748b !important;
        font-size: 14px !important;
        font-weight: 750 !important;
    }

    body.admin-page .tab-btn:hover,
    body.admin-page .tab:hover,
    body.admin-page .tab-button:hover,
    body.admin-page .tab-link:hover {
        color: var(--admin-blue) !important;
    }

    body.admin-page .tab-btn.active,
    body.admin-page .tab.active,
    body.admin-page .tab-button.active,
    body.admin-page .tab-link.active {
        color: var(--admin-blue) !important;
        border-bottom-color: var(--admin-blue) !important;
    }

    body.admin-page .search-filter,
    body.admin-page .filters:not([role="tablist"]),
    body.admin-page .filter-bar,
    body.admin-page .toolbar,
    body.admin-page .table-toolbar,
    body.admin-page .controls,
    body.admin-page .search-panel {
        gap: 14px !important;
        padding: 16px !important;
        border: 1px solid var(--admin-line) !important;
        border-radius: var(--admin-radius) !important;
        background: rgba(255, 255, 255, 0.96) !important;
        box-shadow: var(--admin-shadow-soft) !important;
    }

    body.admin-page .filters[role="tablist"],
    body.admin-page .filter-tabs {
        display: inline-flex !important;
        gap: 6px !important;
        padding: 4px !important;
        border: 1px solid var(--admin-line) !important;
        border-radius: var(--admin-radius) !important;
        background: var(--admin-surface-soft) !important;
        box-shadow: none !important;
    }

    body.admin-page .filter-btn {
        min-height: 36px !important;
        padding: 0 12px !important;
        border: 1px solid transparent !important;
        border-radius: 6px !important;
        background: transparent !important;
        color: #64748b !important;
        font-size: 13px !important;
        font-weight: 750 !important;
    }

    body.admin-page .filter-btn:hover,
    body.admin-page .filter-btn:focus-visible {
        background: #fff !important;
        color: var(--admin-blue) !important;
        outline: none !important;
    }

    body.admin-page .filter-btn.active {
        border-color: #bfdbfe !important;
        background: #fff !important;
        color: var(--admin-blue) !important;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08) !important;
    }

    body.admin-page input[type="text"],
    body.admin-page input[type="search"],
    body.admin-page input[type="email"],
    body.admin-page input[type="number"],
    body.admin-page input[type="date"],
    body.admin-page input[type="datetime-local"],
    body.admin-page input[type="password"],
    body.admin-page select,
    body.admin-page textarea,
    body.admin-event-form-page input,
    body.admin-event-form-page select,
    body.admin-event-form-page textarea,
    body.admin-dnc-form-page input,
    body.admin-dnc-form-page select,
    body.admin-dnc-form-page textarea {
        min-height: 42px !important;
        border: 1px solid var(--admin-line-strong) !important;
        border-radius: var(--admin-radius) !important;
        background: #fff !important;
        color: var(--admin-text) !important;
        font-size: 14px !important;
        line-height: 1.4 !important;
        transition: border-color 0.16s ease, box-shadow 0.16s ease, background 0.16s ease !important;
    }

    body.admin-page input:hover,
    body.admin-page select:hover,
    body.admin-page textarea:hover,
    body.admin-event-form-page input:hover,
    body.admin-event-form-page select:hover,
    body.admin-event-form-page textarea:hover,
    body.admin-dnc-form-page input:hover,
    body.admin-dnc-form-page select:hover,
    body.admin-dnc-form-page textarea:hover {
        border-color: #94a3b8 !important;
    }

    body.admin-page input:focus,
    body.admin-page select:focus,
    body.admin-page textarea:focus,
    body.admin-event-form-page input:focus,
    body.admin-event-form-page select:focus,
    body.admin-event-form-page textarea:focus,
    body.admin-dnc-form-page input:focus,
    body.admin-dnc-form-page select:focus,
    body.admin-dnc-form-page textarea:focus {
        outline: none !important;
        border-color: var(--admin-blue) !important;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.13) !important;
    }

    body.admin-page .btn,
    body.admin-page .button,
    body.admin-page a.button,
    body.admin-page .link-btn,
    body.admin-page .create-btn,
    body.admin-page .export-btn,
    body.admin-page .chart-btn,
    body.admin-event-form-page .btn,
    body.admin-dnc-form-page .btn,
    body.admin-dnc-show-page .back-btn {
        min-height: 40px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 8px !important;
        border-radius: var(--admin-radius) !important;
        font-size: 14px !important;
        font-weight: 750 !important;
        line-height: 1 !important;
        text-decoration: none !important;
        transition: background 0.16s ease, border-color 0.16s ease, color 0.16s ease, box-shadow 0.16s ease, transform 0.16s ease !important;
    }

    body.admin-page .btn:hover,
    body.admin-page .button:hover,
    body.admin-page .link-btn:hover,
    body.admin-page .create-btn:hover,
    body.admin-event-form-page .btn:hover,
    body.admin-dnc-form-page .btn:hover,
    body.admin-dnc-show-page .back-btn:hover {
        transform: translateY(-1px) !important;
    }

    body.admin-page .btn:focus-visible,
    body.admin-page a:focus-visible,
    body.admin-event-form-page .btn:focus-visible,
    body.admin-dnc-form-page .btn:focus-visible,
    body.admin-dnc-show-page .back-btn:focus-visible {
        outline: 3px solid rgba(37, 99, 235, 0.22) !important;
        outline-offset: 2px !important;
    }

    body.admin-page .btn-primary,
    body.admin-page .primary-btn,
    body.admin-page .btn.primary,
    body.admin-page button.primary,
    body.admin-page .create-btn,
    body.admin-page .btn-create,
    body.admin-page .btn-info,
    body.admin-event-form-page .btn-primary,
    body.admin-dnc-form-page .btn.primary {
        border: 1px solid var(--admin-blue) !important;
        background: var(--admin-blue) !important;
        color: #fff !important;
        box-shadow: 0 10px 18px rgba(37, 99, 235, 0.18) !important;
    }

    body.admin-page .btn-primary:hover,
    body.admin-page .primary-btn:hover,
    body.admin-page .btn.primary:hover,
    body.admin-page button.primary:hover,
    body.admin-page .create-btn:hover,
    body.admin-page .btn-create:hover,
    body.admin-page .btn-info:hover,
    body.admin-event-form-page .btn-primary:hover,
    body.admin-dnc-form-page .btn.primary:hover {
        border-color: var(--admin-blue-dark) !important;
        background: var(--admin-blue-dark) !important;
    }

    body.admin-page .btn-secondary,
    body.admin-page .secondary-btn,
    body.admin-page .btn.outline,
    body.admin-page button.outline,
    body.admin-page .clear-btn,
    body.admin-page .outline-btn,
    body.admin-page .link-btn,
    body.admin-page .export-btn,
    body.admin-page .chart-btn,
    body.admin-dnc-form-page .btn.outline,
    body.admin-dnc-show-page .back-btn {
        border: 1px solid var(--admin-line-strong) !important;
        background: #fff !important;
        color: #334155 !important;
        box-shadow: none !important;
    }

    body.admin-page .btn-secondary:hover,
    body.admin-page .secondary-btn:hover,
    body.admin-page .btn.outline:hover,
    body.admin-page button.outline:hover,
    body.admin-page .clear-btn:hover,
    body.admin-page .outline-btn:hover,
    body.admin-page .link-btn:hover,
    body.admin-page .export-btn:hover,
    body.admin-page .chart-btn:hover,
    body.admin-dnc-form-page .btn.outline:hover,
    body.admin-dnc-show-page .back-btn:hover {
        border-color: #94a3b8 !important;
        background: var(--admin-surface-soft) !important;
        color: var(--admin-blue) !important;
    }

    body.admin-page .btn-danger,
    body.admin-page .btn.danger,
    body.admin-page .btn-reject,
    body.admin-page .btn-mark-missing,
    body.admin-page .remove-photo-btn,
    body.admin-dnc-form-page .btn-danger {
        border: 1px solid #fecaca !important;
        background: var(--admin-red-soft) !important;
        color: var(--admin-red) !important;
        box-shadow: none !important;
    }

    body.admin-page .btn-danger:hover,
    body.admin-page .btn.danger:hover,
    body.admin-page .btn-reject:hover,
    body.admin-page .btn-mark-missing:hover,
    body.admin-page .remove-photo-btn:hover,
    body.admin-dnc-form-page .btn-danger:hover {
        border-color: var(--admin-red) !important;
        background: var(--admin-red) !important;
        color: #fff !important;
    }

    body.admin-page .btn-approve,
    body.admin-page .btn-mark-all {
        border: 1px solid #a7f3d0 !important;
        background: var(--admin-green-soft) !important;
        color: var(--admin-green) !important;
        box-shadow: none !important;
    }

    body.admin-page .btn-approve:hover,
    body.admin-page .btn-mark-all:hover {
        border-color: var(--admin-green) !important;
        background: var(--admin-green) !important;
        color: #fff !important;
    }

    body.admin-page button:disabled,
    body.admin-page .btn:disabled,
    body.admin-page [aria-disabled="true"] {
        opacity: 0.55 !important;
        cursor: not-allowed !important;
        transform: none !important;
        box-shadow: none !important;
    }

    body.admin-page .table-responsive,
    body.admin-page .table-wrap,
    body.admin-page .table-container,
    body.admin-page .donated-items-list {
        overflow-x: auto !important;
        border: 1px solid var(--admin-line) !important;
        border-radius: var(--admin-radius) !important;
        background: #fff !important;
        box-shadow: var(--admin-shadow-soft) !important;
    }

    body.admin-page table {
        min-width: 720px;
    }

    body.admin-page th {
        background: #f8fafc !important;
        color: #475569 !important;
        font-size: 12px !important;
        font-weight: 800 !important;
        letter-spacing: 0.03em !important;
        text-transform: uppercase !important;
    }

    body.admin-page th,
    body.admin-page td {
        padding: 15px 16px !important;
        border-bottom: 1px solid #e8eef6 !important;
    }

    body.admin-page tbody tr {
        transition: background 0.16s ease !important;
    }

    body.admin-page tbody tr:hover {
        background: #f8fbff !important;
    }

    body.admin-page .badge,
    body.admin-page .status-badge,
    body.admin-page .chip,
    body.admin-page .pill,
    body.admin-page .tag,
    body.admin-page .report-year,
    body.admin-page .event-status {
        min-height: 24px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 6px !important;
        padding: 4px 10px !important;
        border-radius: 999px !important;
        font-size: 12px !important;
        font-weight: 800 !important;
        line-height: 1 !important;
        text-transform: capitalize !important;
        position: static !important;
        width: auto !important;
        height: auto !important;
    }

    body.admin-page .badge.success,
    body.admin-page .badge.received,
    body.admin-page .badge.approved,
    body.admin-page .status-badge.green,
    body.admin-page .status-badge.attended,
    body.admin-page .tag.approved,
    body.admin-page .chip.green,
    body.admin-page .green-pill {
        border: 1px solid #bbf7d0 !important;
        background: var(--admin-green-soft) !important;
        color: #047857 !important;
    }

    body.admin-page .badge.pending,
    body.admin-page .badge.scheduled,
    body.admin-page .status-badge.orange,
    body.admin-page .tag.pending,
    body.admin-page .chip.orange,
    body.admin-page .orange-pill,
    body.admin-page .yellow-pill {
        border: 1px solid #fde68a !important;
        background: var(--admin-amber-soft) !important;
        color: #b45309 !important;
    }

    body.admin-page .badge.failed,
    body.admin-page .badge.rejected,
    body.admin-page .status-badge.red,
    body.admin-page .status-badge.missing,
    body.admin-page .tag.rejected,
    body.admin-page .chip.red,
    body.admin-page .red-pill {
        border: 1px solid #fecaca !important;
        background: var(--admin-red-soft) !important;
        color: #b91c1c !important;
    }

    body.admin-page .badge.distributed,
    body.admin-page .status-badge.blue,
    body.admin-page .tag.reupload,
    body.admin-page .chip.blue,
    body.admin-page .blue-pill,
    body.admin-page .report-year {
        border: 1px solid #bfdbfe !important;
        background: var(--admin-blue-soft) !important;
        color: var(--admin-blue-dark) !important;
    }

    body.admin-page .purple-pill {
        border: 1px solid #ddd6fe !important;
        background: var(--admin-purple-soft) !important;
        color: var(--admin-purple) !important;
    }

    body.admin-page .empty-state,
    body.admin-page .empty-card,
    body.admin-page .no-records,
    body.admin-page .no-data,
    body.admin-page .empty,
    body.admin-page .no-data-state {
        min-height: 230px !important;
        border: 1px dashed #bfdbfe !important;
        border-radius: var(--admin-radius) !important;
        background: rgba(255, 255, 255, 0.72) !important;
    }

    body.admin-account-page .account-list,
    body.admin-event-page .events-list,
    body.admin-event-page .vol-list {
        gap: 14px !important;
    }

    body.admin-account-page .account-item {
        padding: 18px !important;
        border-left-width: 4px !important;
    }

    body.admin-account-page .summary-row {
        gap: 16px !important;
        padding: 2px !important;
    }

    body.admin-account-page .avatar-large,
    body.admin-event-page .avatar,
    body.admin-event-page .avatar-sm,
    body.admin-inkind-page .avatar,
    body.admin-inkind-page .donor-avatar {
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.28) !important;
    }

    body.admin-account-page .details,
    body.admin-event-page .details,
    body.admin-event-page .participants,
    body.admin-inkind-page .donation-selection-container,
    body.admin-inkind-page .donation-group {
        border: 1px solid var(--admin-line) !important;
        border-radius: var(--admin-radius) !important;
        background: var(--admin-surface-soft) !important;
    }

    body.admin-account-page .documents .doc,
    body.admin-event-page .participant-row,
    body.admin-inkind-page .donation-item {
        border: 1px solid #e8eef6 !important;
        border-radius: var(--admin-radius) !important;
        background: #fff !important;
    }

    body.admin-event-page .event-top,
    body.admin-event-page .vol-top,
    body.admin-dnc-page .record-head {
        gap: 16px !important;
    }

    body.admin-event-page .event-thumb,
    body.admin-event-page .vol-icon,
    body.admin-dnc-page .icon-box {
        border-radius: var(--admin-radius) !important;
    }

    body.admin-inkind-page .table-toolbar {
        flex-wrap: wrap !important;
    }

    body.admin-inkind-page .toolbar-actions,
    body.admin-page .actions,
    body.admin-page .row-actions,
    body.admin-page .record-actions,
    body.admin-event-page .bulk-action,
    body.admin-page .form-buttons {
        gap: 10px !important;
    }

    body.admin-inkind-page .location {
        cursor: pointer !important;
    }

    body.admin-inkind-page .location.selected {
        border-color: var(--admin-blue) !important;
        background: var(--admin-blue-soft) !important;
    }

    body.admin-inkind-page #map,
    body.admin-inkind-page #viewMap,
    body.admin-inkind-page #modal_map,
    body.admin-event-form-page #map {
        border: 1px solid var(--admin-line) !important;
        border-radius: var(--admin-radius) !important;
        box-shadow: var(--admin-shadow-soft) !important;
    }

    body.admin-page .modal,
    body.admin-page .modal-overlay,
    body.admin-page .campaign-details-modal,
    body.admin-account-page .proof-modal {
        backdrop-filter: blur(7px) !important;
    }

    body.admin-page .modal-content,
    body.admin-page .modal-container,
    body.admin-page .campaign-details-content,
    body.admin-account-page .proof-modal-content,
    body.admin-page .admin-logout-modal__dialog,
    body.admin-dnc-page .toast-confirm {
        border: 1px solid var(--admin-line) !important;
        border-radius: var(--admin-radius) !important;
        background: #fff !important;
        box-shadow: 0 28px 70px rgba(15, 23, 42, 0.22) !important;
    }

    body.admin-page .campaign-details-close,
    body.admin-account-page .proof-modal-close,
    body.admin-page .modal-close,
    body.admin-page .btn-close,
    body.admin-page .close-btn,
    body.admin-dnc-page .toast-confirm .delete {
        border-radius: var(--admin-radius) !important;
    }

    body.admin-directory-page .accomplishments-grid {
        grid-template-columns: repeat(auto-fit, minmax(min(100%, 320px), 1fr)) !important;
    }

    body.admin-directory-page .accomplishment-card {
        border-radius: var(--admin-radius) !important;
    }

    body.admin-directory-page .accomplishment-card__image {
        aspect-ratio: 16 / 9 !important;
        height: auto !important;
    }

    body.admin-dnc-page .record h3 {
        margin: 0 0 4px !important;
        color: var(--admin-text) !important;
        font-size: 17px !important;
        font-weight: 800 !important;
        line-height: 1.3 !important;
    }

    body.admin-dnc-page .record-body {
        display: grid !important;
        gap: 8px !important;
    }

    body.admin-dnc-page .record-body .row {
        gap: 12px !important;
        padding: 0 !important;
        border: 0 !important;
    }

    body.admin-event-form-page .header-banner {
        background:
            linear-gradient(180deg, #f8fbff 0%, #edf4ff 100%) !important;
        border-bottom: 1px solid var(--admin-line) !important;
    }

    body.admin-event-form-page .form-card,
    body.admin-dnc-form-page .card,
    body.admin-dnc-show-page .container {
        border-radius: var(--admin-radius) !important;
        border: 1px solid var(--admin-line) !important;
        background: #fff !important;
        box-shadow: var(--admin-shadow) !important;
    }

    body.admin-event-form-page .form-section-title,
    body.admin-dnc-form-page .section-title {
        color: var(--admin-text) !important;
        font-size: 15px !important;
        font-weight: 800 !important;
        letter-spacing: 0 !important;
        text-transform: none !important;
    }

    body.admin-dnc-form-page .grid-auto label {
        min-height: 42px !important;
        display: flex !important;
        align-items: center !important;
        gap: 10px !important;
        padding: 10px 12px !important;
        border: 1px solid var(--admin-line) !important;
        border-radius: var(--admin-radius) !important;
        background: var(--admin-surface-soft) !important;
        color: #334155 !important;
        font-weight: 650 !important;
    }

    body.admin-dnc-show-page .pdf-header {
        border-bottom-color: var(--admin-blue) !important;
    }

    body.admin-dnc-show-page h2 {
        border-left-color: var(--admin-blue) !important;
        border-radius: 0 var(--admin-radius) var(--admin-radius) 0 !important;
        background: var(--admin-blue-soft) !important;
        color: var(--admin-blue-dark) !important;
    }

    @media (max-width: 1100px) {
        body.admin-page .header {
            left: 0 !important;
            height: 70px !important;
        }

        body.admin-page .sidebar {
            transform: translateX(-105%) !important;
            transition: transform 0.22s ease !important;
        }

        body.admin-page .sidebar.open,
        body.admin-page .sidebar.visible {
            transform: translateX(0) !important;
        }

        body.admin-page .main {
            width: 100% !important;
            min-height: calc(100vh - 70px) !important;
            margin: 70px 0 0 !important;
            padding: 24px 18px 42px !important;
        }
    }

    @media (max-width: 760px) {
        :root {
            --admin-sidebar-w: min(86vw, 276px);
        }

        body.admin-page .header__inner {
            padding: 0 14px !important;
            gap: 12px !important;
        }

        body.admin-page .logo-word img {
            width: 92px !important;
        }

        body.admin-page .main {
            padding: 20px 14px 36px !important;
        }

        body.admin-page .admin-page-heading h1,
        body.admin-page .page-header h1,
        body.admin-page .page-header h2 {
            font-size: 25px !important;
        }

        body.admin-page .topbar,
        body.admin-page .section-header,
        body.admin-page .card-header,
        body.admin-page .panel-header,
        body.admin-page .table-header,
        body.admin-page .table-toolbar,
        body.admin-event-page .event-top,
        body.admin-event-page .vol-top,
        body.admin-dnc-page .record-head,
        body.admin-dnc-page .pagination-row,
        body.admin-account-page .summary-row {
            align-items: stretch !important;
            flex-direction: column !important;
        }

        body.admin-page .actions,
        body.admin-page .toolbar-actions,
        body.admin-page .record-actions,
        body.admin-event-page .bulk-action,
        body.admin-page .form-buttons {
            width: 100% !important;
            justify-content: flex-start !important;
        }

        body.admin-page .btn,
        body.admin-page .button,
        body.admin-page .link-btn,
        body.admin-page .create-btn {
            white-space: normal !important;
        }

        body.admin-page .search-field,
        body.admin-page .search-box,
        body.admin-page .search,
        body.admin-page .date-filter {
            width: 100% !important;
            min-width: 0 !important;
        }

        body.admin-page .filters[role="tablist"],
        body.admin-page .filter-tabs,
        body.admin-page .tabs,
        body.admin-page .tabs-nav {
            width: 100% !important;
            overflow-x: auto !important;
        }

        body.admin-account-page .proof-modal-body,
        body.admin-page .campaign-details-body {
            flex-direction: column !important;
            min-height: 0 !important;
        }

        body.admin-account-page .proof-details,
        body.admin-page .campaign-details-sidebar {
            width: 100% !important;
            flex: 0 0 auto !important;
        }

        body.admin-dnc-form-page .container,
        body.admin-dnc-show-page .container {
            width: 100% !important;
        }
    }
</style>
