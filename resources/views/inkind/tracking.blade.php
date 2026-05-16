<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracking & Needs | Tulong Kabataan</title>
    <link rel="icon" href="{{ page_media_url('site_favicon', asset('img/log2.png')) }}" type="image/png">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body.impact-tracker-page {
            --impact-brand: #4f46e5;
            --impact-brand-dark: #4338ca;
            --impact-brand-soft: #eef2ff;
            --impact-text: #0f172a;
            --impact-muted: #64748b;
            --impact-border: #e2e8f0;
            --impact-border-strong: #cbd5e1;
            --impact-surface: #ffffff;
            --impact-soft: #f8fafc;
            --impact-shadow: 0 12px 28px rgba(15, 23, 42, 0.07);
            --impact-shadow-hover: 0 18px 38px rgba(15, 23, 42, 0.1);
            --impact-max: 1200px;
            --impact-gutter: clamp(16px, 4vw, 28px);
            --primary: var(--impact-brand);
            --primary-light: var(--impact-brand-soft);
            --text-dark: var(--impact-text);
            --text-gray: var(--impact-muted);
            --bg-light: #f8fafc;
            margin: 0;
            color: var(--impact-text);
            font-family: var(--font-body);
            line-height: 1.6;
        }

        .tracking-header,
        .tracking-header *,
        .tracking-header *::before,
        .tracking-header *::after,
        .tracking-main,
        .tracking-main *,
        .tracking-main *::before,
        .tracking-main *::after,
        .modal,
        .modal *,
        .modal *::before,
        .modal *::after {
            box-sizing: border-box;
        }

        .tracking-header h1,
        .tracking-main h1,
        .tracking-main h2,
        .tracking-main h3,
        .tracking-main h4,
        .tracking-main h5,
        .tracking-main h6,
        .modal h2,
        .modal h3,
        .modal h4 {
            font-family: var(--font-heading);
            letter-spacing: 0;
        }

        .tracking-header p,
        .tracking-header a,
        .tracking-header span,
        .tracking-main p,
        .tracking-main a,
        .tracking-main li,
        .tracking-main span,
        .tracking-main button,
        .modal p,
        .modal a,
        .modal li,
        .modal span,
        .modal button {
            font-family: var(--font-body);
            letter-spacing: 0;
        }

        .tracking-header a,
        .tracking-main a,
        .modal a {
            text-decoration: none;
        }

        .tracking-container {
            width: 100%;
            max-width: var(--impact-max);
            margin-inline: auto;
            padding-inline: var(--impact-gutter);
        }

        .tracking-header {
            position: relative;
            isolation: isolate;
            display: grid;
            min-height: clamp(420px, 48vw, 600px);
            place-items: center;
            overflow: hidden;
            padding: clamp(92px, 11vw, 132px) var(--impact-gutter) clamp(98px, 11vw, 138px);
            text-align: center;
            background: #111827;
        }

        .tracking-header::before {
            content: "";
            position: absolute;
            inset: 0;
            z-index: -2;
            background: url('{{ page_media_url('donation_tracking_hero_image', asset('img/bg1.jpg')) }}') center / cover no-repeat;
            filter: saturate(1.03) contrast(1.04);
        }

        .tracking-header::after {
            content: "";
            position: absolute;
            inset: 0;
            z-index: -1;
            background:
                radial-gradient(circle at 48% 42%, rgba(2, 6, 23, 0.3), transparent 36%),
                linear-gradient(135deg, rgba(15, 23, 42, 0.78), rgba(49, 46, 129, 0.72));
        }

        .tracking-header .tracking-container {
            position: relative;
            z-index: 2;
            display: grid;
            justify-items: center;
            gap: 18px;
            padding-inline: 0;
        }

        .tracking-title {
            max-width: 18ch;
            margin: 0;
            color: #ffffff;
            font-size: clamp(2.2rem, 5vw, 4rem);
            font-weight: 800;
            line-height: 1.08;
            text-shadow: 0 3px 20px rgba(15, 23, 42, 0.45);
            text-wrap: balance;
        }

        .tracking-desc {
            max-width: 67ch;
            margin: 0;
            color: rgba(255, 255, 255, 0.92);
            font-size: clamp(0.98rem, 1.45vw, 1.15rem);
            line-height: 1.7;
            text-shadow: 0 2px 12px rgba(15, 23, 42, 0.42);
        }

        .tracking-header .hero-back-link {
            position: absolute;
            top: clamp(-4.5rem, -5vw, -3rem);
            left: 0;
            display: inline-flex;
            min-height: 40px;
            align-items: center;
            justify-content: center;
            gap: 6px;
            color: #ffffff;
            padding: 0 0.35rem;
            font-size: 0.94rem;
            font-weight: 700;
            line-height: 1.2;
            text-shadow: 0 2px 8px rgba(15, 23, 42, 0.45);
            transition: color 0.18s ease, opacity 0.18s ease;
        }

        .tracking-header .hero-back-link i {
            font-size: 1.05rem;
            line-height: 1;
        }

        .tracking-header .hero-back-link:hover,
        .tracking-header .hero-back-link:focus-visible {
            color: #ffffff;
            opacity: 0.82;
        }

        .tracking-main {
            padding-top: clamp(38px, 5.8vw, 62px);
            padding-bottom: clamp(64px, 8vw, 96px);
        }

        .ops-section,
        .centers-section {
            margin-bottom: clamp(42px, 6vw, 72px);
        }

        .section-header {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 22px;
            margin-bottom: clamp(22px, 3.4vw, 34px);
        }

        .section-copy {
            display: grid;
            max-width: 760px;
            gap: 10px;
        }

        .section-title {
            margin: 0;
            color: var(--impact-text);
            font-size: clamp(1.55rem, 3vw, 2.2rem);
            font-weight: 800;
            line-height: 1.18;
            text-wrap: balance;
        }

        .section-desc {
            max-width: 64ch;
            margin: 0;
            color: #475569;
            font-size: 1rem;
            line-height: 1.7;
        }

        .ops-controls {
            display: inline-flex;
            flex: 0 0 auto;
            align-items: center;
            gap: 10px;
        }

        .slider-btn {
            display: inline-flex;
            width: 42px;
            height: 42px;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--impact-border);
            border-radius: 8px;
            background: #ffffff;
            color: var(--impact-brand);
            cursor: pointer;
            font-size: 1.2rem;
            line-height: 1;
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.06);
            transition: background-color 0.18s ease, border-color 0.18s ease, color 0.18s ease, box-shadow 0.18s ease, transform 0.18s ease;
        }

        .slider-btn:hover:not(:disabled),
        .slider-btn:focus-visible:not(:disabled) {
            border-color: var(--impact-brand);
            background: var(--impact-brand);
            color: #ffffff;
            box-shadow: 0 12px 22px rgba(79, 70, 229, 0.2);
            transform: translateY(-1px);
        }

        .slider-btn:disabled {
            border-color: #e5e7eb;
            background: #f8fafc;
            color: #cbd5e1;
            cursor: not-allowed;
            box-shadow: none;
        }

        .slider-btn.is-hidden {
            display: none;
        }

        .ops-slider-wrapper {
            width: 100%;
            overflow: hidden;
            padding: 2px;
        }

        .ops-track {
            display: flex;
            gap: 22px;
            transition: transform 0.35s ease;
            will-change: transform;
        }

        .ops-card {
            display: flex;
            flex: 0 0 calc((100% - 44px) / 3);
            min-width: 0;
            flex-direction: column;
            overflow: hidden;
            border: 1px solid var(--impact-border);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.97);
            box-shadow: var(--impact-shadow);
            transition: border-color 0.18s ease, box-shadow 0.18s ease, transform 0.18s ease;
        }

        .ops-card:hover {
            border-color: var(--impact-border-strong);
            box-shadow: var(--impact-shadow-hover);
            transform: translateY(-2px);
        }

        .ops-track-single .ops-card {
            display: grid;
            flex-basis: min(100%, 900px);
            grid-template-columns: minmax(320px, 0.92fr) minmax(0, 1.08fr);
            margin-inline: auto;
        }

        .ops-img-container {
            position: relative;
            height: 218px;
            overflow: hidden;
            background: #e5e7eb;
        }

        .ops-track-single .ops-img-container {
            height: 100%;
            min-height: 300px;
        }

        .ops-img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.45s ease;
        }

        .ops-card:hover .ops-img {
            transform: scale(1.025);
        }

        .ops-date-badge {
            position: absolute;
            top: 12px;
            left: 12px;
            display: inline-flex;
            min-height: 32px;
            align-items: center;
            gap: 7px;
            border: 1px solid rgba(255, 255, 255, 0.72);
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.92);
            color: var(--impact-brand);
            padding: 0 11px;
            font-size: 0.76rem;
            font-weight: 800;
            line-height: 1;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.12);
        }

        .ops-content {
            display: flex;
            min-width: 0;
            flex: 1;
            flex-direction: column;
            padding: clamp(20px, 2.4vw, 26px);
        }

        .ops-title {
            margin: 0 0 9px;
            color: var(--impact-text);
            font-size: 1.12rem;
            font-weight: 800;
            line-height: 1.3;
        }

        .ops-desc {
            flex: 1;
            margin: 0 0 20px;
            color: #475569;
            font-size: 0.95rem;
            line-height: 1.62;
        }

        .ops-meta {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            margin-top: auto;
            padding-top: 16px;
            border-top: 1px solid #eef2f7;
        }

        .ops-meta-item {
            display: grid;
            grid-template-columns: auto minmax(0, 1fr);
            align-items: center;
            gap: 10px;
            min-height: 48px;
            border: 1px solid #e6ebf3;
            border-radius: 8px;
            background: #f8fafc;
            padding: 10px;
            color: #334155;
            font-size: 0.86rem;
            font-weight: 700;
            line-height: 1.35;
        }

        .ops-meta-item i {
            display: inline-flex;
            width: 30px;
            height: 30px;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            background: var(--impact-brand-soft);
            color: var(--impact-brand);
            font-size: 1rem;
            line-height: 1;
        }

        .ops-meta-label {
            display: block;
            color: var(--impact-muted);
            font-size: 0.74rem;
            font-weight: 700;
            line-height: 1.2;
        }

        .centers-card,
        .cta-section {
            border: 1px solid var(--impact-border);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.97);
            box-shadow: var(--impact-shadow);
        }

        .centers-card {
            overflow: hidden;
        }

        .centers-card-header {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: clamp(20px, 2.6vw, 28px);
            border-bottom: 1px solid #eef2f7;
        }

        .centers-icon {
            display: inline-flex;
            width: 48px;
            height: 48px;
            flex: 0 0 auto;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            background: var(--impact-brand-soft);
            color: var(--impact-brand);
            font-size: 1.35rem;
            line-height: 1;
        }

        .card-title {
            margin: 0 0 5px;
            color: var(--impact-text);
            font-size: 1.25rem;
            font-weight: 800;
            line-height: 1.25;
        }

        .card-subtitle {
            margin: 0;
            color: var(--impact-muted);
            font-size: 0.94rem;
            line-height: 1.55;
        }

        .location-container {
            max-height: 420px;
            overflow-y: auto;
            padding: 8px clamp(18px, 2.6vw, 28px) clamp(18px, 2.6vw, 28px);
        }

        .location-container::-webkit-scrollbar {
            width: 8px;
        }

        .location-container::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 999px;
        }

        .location-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 999px;
        }

        .location-list {
            display: grid;
            gap: 12px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .location-item {
            border: 1px solid #e6ebf3;
            border-radius: 8px;
            background: #ffffff;
            padding: 16px;
        }

        .loc-details {
            display: grid;
            gap: 10px;
        }

        .loc-topline {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 14px;
        }

        .loc-details h4 {
            margin: 0;
            color: var(--impact-text);
            font-size: 1rem;
            font-weight: 800;
            line-height: 1.35;
        }

        .loc-meta {
            display: grid;
            gap: 7px;
            margin: 0;
            color: #475569;
            font-size: 0.93rem;
            line-height: 1.5;
        }

        .loc-meta span {
            display: grid;
            grid-template-columns: auto minmax(0, 1fr);
            gap: 8px;
            align-items: start;
        }

        .loc-meta i {
            margin-top: 3px;
            color: var(--impact-brand);
            font-size: 1rem;
            line-height: 1;
        }

        .status-badge {
            display: inline-flex;
            min-height: 28px;
            flex: 0 0 auto;
            align-items: center;
            justify-content: center;
            border: 1px solid transparent;
            border-radius: 999px;
            padding: 0 10px;
            font-size: 0.76rem;
            font-weight: 800;
            line-height: 1;
            white-space: nowrap;
        }

        .status-badge.active {
            border-color: #bbf7d0;
            background: #f0fdf4;
            color: #166534;
        }

        .status-badge.inactive {
            border-color: #fecaca;
            background: #fff1f2;
            color: #991b1b;
        }

        .cta-section {
            display: grid;
            justify-items: center;
            gap: 14px;
            margin-bottom: 0;
            border-style: dashed;
            border-color: #c7d2fe;
            background:
                linear-gradient(135deg, rgba(238, 242, 255, 0.78), rgba(255, 255, 255, 0.96)),
                #ffffff;
            padding: clamp(26px, 4vw, 42px);
            text-align: center;
        }

        .cta-icon {
            display: inline-flex;
            width: 52px;
            height: 52px;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            background: var(--impact-brand-soft);
            color: var(--impact-brand);
            font-size: 1.4rem;
            line-height: 1;
        }

        .cta-section h2 {
            margin: 0;
            color: var(--impact-text);
            font-size: clamp(1.35rem, 2.6vw, 1.8rem);
            font-weight: 800;
            line-height: 1.25;
        }

        .cta-section p {
            max-width: 56ch;
            margin: 0;
            color: #475569;
            font-size: 0.98rem;
            line-height: 1.65;
        }

        .cta-btn {
            display: inline-flex;
            min-height: 48px;
            align-items: center;
            justify-content: center;
            gap: 9px;
            margin-top: 4px;
            border: 1px solid var(--impact-brand);
            border-radius: 8px;
            background: var(--impact-brand);
            color: #ffffff;
            padding: 0 20px;
            font-size: 0.95rem;
            font-weight: 700;
            line-height: 1.2;
            box-shadow: 0 12px 22px rgba(79, 70, 229, 0.18);
            transition: background-color 0.18s ease, border-color 0.18s ease, box-shadow 0.18s ease, transform 0.18s ease;
        }

        .cta-btn:hover,
        .cta-btn:focus-visible {
            border-color: var(--impact-brand-dark);
            background: var(--impact-brand-dark);
            color: #ffffff;
            box-shadow: 0 15px 26px rgba(79, 70, 229, 0.24);
            transform: translateY(-1px);
        }

        .modal {
            position: fixed;
            inset: 0;
            z-index: 1000;
            display: none;
        }

        .modal.is-open {
            display: block;
        }

        .modal-overlay {
            position: fixed;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-y: auto;
            background: rgba(15, 23, 42, 0.56);
            padding: 20px;
        }

        .modal-container {
            display: flex;
            width: min(100%, 680px);
            max-height: min(90vh, 760px);
            flex-direction: column;
            overflow: hidden;
            border: 1px solid rgba(226, 232, 240, 0.92);
            border-radius: 8px;
            background: #ffffff;
            box-shadow: 0 24px 70px rgba(15, 23, 42, 0.26);
        }

        .modal-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            border-bottom: 1px solid #eef2f7;
            background: #ffffff;
            padding: 20px 22px;
        }

        .modal-title {
            margin: 0;
            color: var(--impact-text);
            font-size: 1.2rem;
            font-weight: 800;
            line-height: 1.35;
        }

        .modal-close {
            display: inline-flex;
            width: 36px;
            height: 36px;
            flex: 0 0 auto;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--impact-border);
            border-radius: 8px;
            background: #ffffff;
            color: #334155;
            cursor: pointer;
            font-size: 1.1rem;
            line-height: 1;
            transition: background-color 0.18s ease, border-color 0.18s ease, color 0.18s ease;
        }

        .modal-close:hover,
        .modal-close:focus-visible {
            border-color: var(--impact-brand);
            background: var(--impact-brand-soft);
            color: var(--impact-brand);
        }

        .modal-body {
            overflow-y: auto;
            padding: 22px;
        }

        .modal-content-section {
            margin-bottom: 18px;
        }

        .modal-content-section:last-child {
            margin-bottom: 0;
        }

        .modal-section-label {
            display: block;
            margin-bottom: 10px;
            color: var(--impact-text);
            font-weight: 800;
        }

        .modal-date-pill {
            display: inline-flex;
            min-height: 34px;
            align-items: center;
            gap: 8px;
            border: 1px solid #c7d2fe;
            border-radius: 999px;
            background: var(--impact-brand-soft);
            color: var(--impact-brand);
            padding: 0 12px;
            font-size: 0.84rem;
            font-weight: 800;
        }

        .modal-report-description {
            margin: 0;
            border: 1px solid #e6ebf3;
            border-radius: 8px;
            background: #f8fafc;
            padding: 14px;
            color: #475569;
            line-height: 1.65;
        }

        .category-group {
            margin-bottom: 12px;
            border: 1px solid #e6ebf3;
            border-radius: 8px;
            background: #ffffff;
            padding: 14px;
        }

        .category-group:last-child {
            margin-bottom: 0;
        }

        .category-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin: 0 0 10px;
            border-bottom: 1px solid #eef2f7;
            padding-bottom: 10px;
            color: var(--impact-text);
            font-size: 0.98rem;
            font-weight: 800;
        }

        .category-total {
            display: inline-flex;
            min-height: 26px;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            background: var(--impact-brand-soft);
            color: var(--impact-brand);
            padding: 0 10px;
            font-size: 0.75rem;
            font-weight: 800;
            white-space: nowrap;
        }

        .donations-list {
            display: grid;
            gap: 8px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .donation-item {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            border-bottom: 1px dashed #e5e7eb;
            padding: 0 0 8px;
        }

        .donation-item:last-child {
            border-bottom: 0;
            padding-bottom: 0;
        }

        .donation-info {
            display: grid;
            gap: 3px;
            min-width: 0;
        }

        .donation-name {
            color: #334155;
            font-weight: 700;
        }

        .donation-donor {
            color: var(--impact-muted);
            font-size: 0.82rem;
        }

        .donation-quantity {
            color: var(--impact-brand);
            font-weight: 800;
            white-space: nowrap;
        }

        .photos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(92px, 1fr));
            gap: 10px;
        }

        .photo-item img {
            display: block;
            width: 100%;
            height: 92px;
            border: 1px solid #e6ebf3;
            border-radius: 8px;
            object-fit: cover;
            cursor: pointer;
        }

        .empty-state {
            display: grid;
            justify-items: center;
            gap: 8px;
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            background: #f8fafc;
            padding: 22px;
            color: var(--impact-muted);
            text-align: center;
        }

        .empty-state i {
            color: var(--impact-brand);
            font-size: 1.5rem;
        }

        .empty-state p {
            margin: 0;
        }

        @media (max-width: 980px) {
            .section-header {
                align-items: flex-start;
                flex-direction: column;
            }

            .ops-controls {
                align-self: flex-start;
            }

            .ops-card {
                flex-basis: calc((100% - 22px) / 2);
            }

            .ops-track-single .ops-card {
                grid-template-columns: 1fr;
            }

            .ops-track-single .ops-img-container {
                height: 260px;
                min-height: 0;
            }
        }

        @media (max-width: 640px) {
            .tracking-header {
                min-height: 520px;
                padding-top: 78px;
                padding-bottom: 88px;
            }

            .tracking-title {
                max-width: 14ch;
                font-size: clamp(2rem, 8vw, 2.75rem);
            }

            .tracking-desc {
                font-size: 0.98rem;
                line-height: 1.62;
            }

            .tracking-header .hero-back-link {
                width: fit-content;
                max-width: 100%;
            }

            .ops-card {
                flex-basis: 100%;
            }

            .ops-controls {
                width: 100%;
                justify-content: flex-start;
            }

            .ops-meta {
                grid-template-columns: 1fr;
            }

            .centers-card-header {
                flex-direction: column;
            }

            .loc-topline {
                flex-direction: column;
                gap: 10px;
            }

            .status-badge {
                align-self: flex-start;
            }

            .cta-btn {
                width: min(100%, 340px);
            }

            .modal-overlay {
                align-items: flex-start;
                padding: 14px;
            }

            .modal-header,
            .modal-body {
                padding: 18px;
            }

            .donation-item,
            .category-title {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .hero-back-link,
            .slider-btn,
            .ops-track,
            .ops-card,
            .ops-img,
            .cta-btn,
            .modal-close {
                transition: none;
            }

            .hero-back-link:hover,
            .hero-back-link:focus-visible,
            .slider-btn:hover:not(:disabled),
            .slider-btn:focus-visible:not(:disabled),
            .ops-card:hover,
            .cta-btn:hover,
            .cta-btn:focus-visible {
                transform: none;
            }

            .ops-card:hover .ops-img {
                transform: none;
            }
        }
    </style>
</head>

<body class="impact-tracker-page">

    @include('partials.main-header')
    @include('partials.universalmodal')
    @include('administrator.partials.loading-screen')

    <header class="tracking-header">
        <div class="tracking-container">
            <a href="{{ route('inkind.page') }}" class="hero-back-link" aria-label="Back to In-Kind Donations">
                <i class="ri-arrow-left-line" aria-hidden="true"></i>
                <span>Back</span>
            </a>
            <h1 class="tracking-title">Donation Impact Tracker</h1>
            <p class="tracking-desc">
                Transparency is key. See exactly where in-kind donations are being distributed and track the remaining
                items needed to reach our campaign goals.
            </p>
        </div>
        @include('partials.wave-divider')
    </header>

    <main class="tracking-container tracking-main">

        <section class="ops-section">
            <div class="section-header">
                <div class="section-copy">
                    <h2 class="section-title">Recent Relief Operations</h2>
                    <p class="section-desc">See how your in-kind donations are making a tangible impact on the ground.</p>
                </div>

                <div class="ops-controls" aria-label="Relief operation controls">
                    <button class="slider-btn prev" id="sliderPrevBtn" type="button" aria-label="Previous relief operation">
                        <i class="ri-arrow-left-s-line" aria-hidden="true"></i>
                    </button>
                    <button class="slider-btn next" id="sliderNextBtn" type="button" aria-label="Next relief operation">
                        <i class="ri-arrow-right-s-line" aria-hidden="true"></i>
                    </button>
                </div>
            </div>

            <div class="ops-slider-wrapper">
                <div class="ops-track {{ $impactReports->count() === 1 ? 'ops-track-single' : '' }}" id="opsTrack">

                    @forelse($impactReports as $report)
                        @php
                            $itemsDistributed = $report->donations->sum('quantity');
                            $donorCount = $report->donations->unique('donor_name')->count();
                        @endphp
                        <article class="ops-card" data-report-id="{{ $report->impact_report_id }}">
                            <div class="ops-img-container">
                                @if ($report->photos && count($report->photos) > 0)
                                    <img src="{{ file_url($report->photos[0]) }}" alt="{{ $report->title }}" class="ops-img">
                                @else
                                    <img src="{{ page_media_url('donation_default_image', asset('img/inkind.png')) }}"
                                        alt="{{ $report->title }}" class="ops-img">
                                @endif
                                <span class="ops-date-badge">
                                    <i class="ri-calendar-line" aria-hidden="true"></i>
                                    {{ \Carbon\Carbon::parse($report->report_date)->format('M d, Y') }}
                                </span>
                            </div>
                            <div class="ops-content">
                                <h3 class="ops-title">{{ $report->title }}</h3>
                                <p class="ops-desc">{{ Str::limit($report->description, 100) }}</p>
                                <div class="ops-meta">
                                    <div class="ops-meta-item">
                                        <i class="ri-gift-line" aria-hidden="true"></i>
                                        <span>
                                            <span class="ops-meta-label">Items Distributed</span>
                                            @if ($report->donations->count() > 0)
                                                {{ $itemsDistributed }} {{ $itemsDistributed === 1 ? 'Item' : 'Items' }}
                                            @else
                                                No items recorded
                                            @endif
                                        </span>
                                    </div>
                                    <div class="ops-meta-item">
                                        <i class="ri-user-line" aria-hidden="true"></i>
                                        <span>
                                            <span class="ops-meta-label">Donors</span>
                                            @if ($report->donations->count() > 0)
                                                {{ $donorCount }} {{ $donorCount === 1 ? 'Donor' : 'Donors' }}
                                            @else
                                                No donors recorded
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @empty
                        @for ($i = 0; $i < 3; $i++)
                            <article class="ops-card">
                                <div class="ops-img-container">
                                    <img src="{{ page_media_url('empty_state_image', asset('img/camp.jpg')) }}"
                                        alt="No reports yet" class="ops-img">
                                    <span class="ops-date-badge">
                                        <i class="ri-calendar-line" aria-hidden="true"></i>
                                        Coming Soon
                                    </span>
                                </div>
                                <div class="ops-content">
                                    <h3 class="ops-title">No Reports Yet</h3>
                                    <p class="ops-desc">Impact reports will appear here once created.</p>
                                    <div class="ops-meta">
                                        <div class="ops-meta-item">
                                            <i class="ri-inbox-line" aria-hidden="true"></i>
                                            <span>
                                                <span class="ops-meta-label">Status</span>
                                                Waiting for data
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        @endfor
                    @endforelse

                </div>
            </div>
        </section>

        <section class="centers-section">
            <article class="centers-card">
                <div class="centers-card-header">
                    <span class="centers-icon" aria-hidden="true">
                        <i class="ri-map-2-line"></i>
                    </span>
                    <div>
                        <h2 class="card-title">Distribution Centers</h2>
                        <p class="card-subtitle">Active centers where in-kind donations are coordinated and distributed.</p>
                    </div>
                </div>

                <div class="location-container">
                    <ul class="location-list">
                        @forelse($dropOffPoints as $point)
                            <li class="location-item">
                                <div class="loc-details">
                                    <div class="loc-topline">
                                        <h4>{{ $point->name }}</h4>
                                        <span class="status-badge {{ $point->is_active ? 'active' : 'inactive' }}">
                                            {{ $point->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>

                                    <p class="loc-meta">
                                        @if ($point->address)
                                            <span>
                                                <i class="ri-map-pin-line" aria-hidden="true"></i>
                                                {{ $point->address }}
                                            </span>
                                        @endif

                                        @if ($point->schedule_datetime)
                                            <span>
                                                <i class="ri-calendar-line" aria-hidden="true"></i>
                                                {{ $point->schedule_datetime }}
                                            </span>
                                        @endif
                                    </p>
                                </div>
                            </li>
                        @empty
                            <li class="location-item">
                                <div class="loc-details">
                                    <div class="loc-topline">
                                        <h4>No distribution centers available</h4>
                                    </div>
                                    <p class="loc-meta">
                                        <span>Please check back later for updates.</span>
                                    </p>
                                </div>
                            </li>
                        @endforelse
                    </ul>
                </div>
            </article>
        </section>

        <div class="modal" id="reportModal" aria-hidden="true">
            <div class="modal-overlay" tabindex="-1" data-micromodal-close>
                <div class="modal-container" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
                    <header class="modal-header">
                        <h2 class="modal-title" id="modalTitle"></h2>
                        <button class="modal-close" type="button" aria-label="Close modal" data-micromodal-close>
                            <i class="ri-close-line" aria-hidden="true"></i>
                        </button>
                    </header>
                    <div class="modal-body" id="modalBody"></div>
                </div>
            </div>
        </div>

        <section class="cta-section">
            <span class="cta-icon" aria-hidden="true">
                <i class="ri-hand-heart-line"></i>
            </span>
            <h2>Can you fill these gaps?</h2>
            <p>Every item counts. Click below to schedule your drop-off or pickup.</p>
            <a href="{{ route('inkindmodal') }}" class="cta-btn">
                <span>Proceed to Donation Form</span>
                <i class="ri-arrow-right-line" aria-hidden="true"></i>
            </a>
        </section>

    </main>

    @include('partials.main-footer')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const track = document.getElementById('opsTrack');
            const prevBtn = document.getElementById('sliderPrevBtn');
            const nextBtn = document.getElementById('sliderNextBtn');
            const cards = document.querySelectorAll('.ops-card');

            let currentIndex = 0;

            function getCardsPerView() {
                if (window.innerWidth <= 640) return 1;
                if (window.innerWidth <= 980) return 2;
                return 3;
            }

            function getTrackGap() {
                if (!track) return 22;
                const styles = window.getComputedStyle(track);
                return parseFloat(styles.columnGap || styles.gap || '22') || 22;
            }

            function updateSlider() {
                if (!track || !cards.length || !prevBtn || !nextBtn) return;

                const cardsPerView = getCardsPerView();
                const maxIndex = Math.max(0, cards.length - cardsPerView);
                const shouldHideControls = maxIndex === 0;

                prevBtn.classList.toggle('is-hidden', shouldHideControls);
                nextBtn.classList.toggle('is-hidden', shouldHideControls);

                if (shouldHideControls) {
                    currentIndex = 0;
                    track.style.transform = 'translateX(0)';
                    prevBtn.disabled = true;
                    nextBtn.disabled = true;
                    return;
                }

                currentIndex = Math.min(currentIndex, maxIndex);

                const cardWidth = cards[0].offsetWidth;
                const gap = getTrackGap();
                const moveAmount = (cardWidth + gap) * currentIndex;
                track.style.transform = `translateX(-${moveAmount}px)`;

                prevBtn.disabled = currentIndex === 0;
                nextBtn.disabled = currentIndex >= maxIndex;
            }

            if (nextBtn && prevBtn) {
                nextBtn.addEventListener('click', () => {
                    const cardsPerView = getCardsPerView();
                    const maxIndex = Math.max(0, cards.length - cardsPerView);
                    if (currentIndex < maxIndex) {
                        currentIndex++;
                        updateSlider();
                    }
                });

                prevBtn.addEventListener('click', () => {
                    if (currentIndex > 0) {
                        currentIndex--;
                        updateSlider();
                    }
                });
            }

            window.addEventListener('resize', () => {
                currentIndex = 0;
                updateSlider();
            });

            window.addEventListener('load', updateSlider);
            updateSlider();
        });

        const modal = document.getElementById('reportModal');
        let isModalOpen = false;

        function openModal() {
            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            isModalOpen = true;
            modal.querySelector('.modal-close').focus();
        }

        function closeModal() {
            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = 'auto';
            isModalOpen = false;
        }

        modal.addEventListener('click', function(e) {
            if (e.target.hasAttribute('data-micromodal-close')) closeModal();
        });

        document.querySelector('.modal-close').addEventListener('click', closeModal);

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && isModalOpen) closeModal();
        });

        async function loadReportDetails(reportId) {
            try {
                const response = await fetch(`/api/impact-reports/${reportId}`);
                const data = await response.json();

                document.getElementById('modalTitle').textContent = data.title;
                const modalBody = document.getElementById('modalBody');

                const donationsByCategory = {};
                if (data.donations && data.donations.length > 0) {
                    data.donations.forEach(donation => {
                        const category = donation.category || 'Uncategorized';
                        if (!donationsByCategory[category]) {
                            donationsByCategory[category] = [];
                        }
                        donationsByCategory[category].push(donation);
                    });
                }

                let donationsHTML = '';
                if (Object.keys(donationsByCategory).length > 0) {
                    donationsHTML = `
                <div class="modal-content-section">
                    <strong class="modal-section-label">Donated Items by Category:</strong>
                    ${Object.keys(donationsByCategory).map(category => {
                        const categoryDonations = donationsByCategory[category];
                        const totalQuantity = categoryDonations.reduce((sum, d) => sum + parseInt(d.quantity), 0);
                        return `
                                    <div class="category-group">
                                        <h4 class="category-title">
                                            <span><i class="ri-price-tag-3-line"></i> ${category}</span>
                                            <span class="category-total">${totalQuantity} units total</span>
                                        </h4>
                                        <ul class="donations-list">
                                            ${categoryDonations.map(donation => `
                                            <li class="donation-item">
                                                <div class="donation-info">
                                                    <span class="donation-name">${donation.item_name}</span>
                                                    ${donation.donor_name ? `<small class="donation-donor"><i class="ri-user-line"></i> ${donation.donor_name}</small>` : ''}
                                                </div>
                                                <span class="donation-quantity">${donation.quantity} units</span>
                                            </li>
                                        `).join('')}
                                        </ul>
                                    </div>
                            `;
                    }).join('')}
                </div>`;
                } else {
                    donationsHTML = `
                <div class="modal-content-section">
                    <strong class="modal-section-label">Donated Items:</strong>
                    <div class="empty-state"><i class="ri-inbox-line"></i><p>No items recorded in this report.</p></div>
                </div>`;
                }

                let photosHTML = '';
                if (data.photos && data.photos.length > 0) {
                    photosHTML = `
                <div class="modal-content-section">
                    <strong class="modal-section-label">Report Photos (${data.photos.length}):</strong>
                    <div class="photos-grid">
                        ${data.photos.map(photo => `
                                <div class="photo-item"><img src="${photo}" alt="Report Photo" onclick="window.open('${photo}', '_blank')"></div>
                            `).join('')}
                    </div>
                </div>`;
                }

                modalBody.innerHTML = `
            <div class="modal-content-section">
                <div class="modal-date-pill">
                    <i class="ri-calendar-line"></i> ${data.report_date_formatted}
                </div>
            </div>
            <div class="modal-content-section">
                <strong class="modal-section-label">Report Description:</strong>
                <p class="modal-report-description">${data.description}</p>
            </div>
            ${donationsHTML}
            ${photosHTML}
        `;
                openModal();
            } catch (error) {
                console.error('Error loading report:', error);
            }
        }

        document.querySelectorAll('.ops-card').forEach(card => {
            card.style.cursor = 'pointer';
            card.addEventListener('click', function() {
                const reportId = this.dataset.reportId;
                if (reportId) loadReportDetails(reportId);
            });
        });
    </script>
</body>

</html>
