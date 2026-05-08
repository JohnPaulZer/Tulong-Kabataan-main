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
</style>
