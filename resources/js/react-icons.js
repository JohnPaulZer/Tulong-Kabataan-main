import React from 'react';
import { createRoot } from 'react-dom/client';
import {
    RiAddLine,
    RiAlarmLine,
    RiAlarmWarningLine,
    RiAlertLine,
    RiArrowDownSLine,
    RiArrowGoBackLine,
    RiArrowLeftLine,
    RiArrowLeftSLine,
    RiArrowRightLine,
    RiArrowRightSLine,
    RiArrowUpLine,
    RiArrowUpSLine,
    RiAwardLine,
    RiBarChartLine,
    RiBookOpenLine,
    RiBox3Line,
    RiBriefcaseLine,
    RiBroadcastLine,
    RiCalendar2Fill,
    RiCalendar2Line,
    RiCalendarCheckLine,
    RiCalendarEventFill,
    RiCalendarEventLine,
    RiCalendarLine,
    RiCalendarScheduleLine,
    RiCalendarTodoLine,
    RiCameraLine,
    RiCheckDoubleLine,
    RiCheckLine,
    RiCheckboxBlankCircleFill,
    RiCheckboxBlankLine,
    RiCheckboxCircleFill,
    RiCheckboxCircleLine,
    RiCheckboxMultipleLine,
    RiCloseCircleLine,
    RiCloseLine,
    RiCommunityLine,
    RiComputerLine,
    RiContactsLine,
    RiCustomerService2Line,
    RiDashboardLine,
    RiDeleteBinLine,
    RiDropLine,
    RiEdit2Line,
    RiEditLine,
    RiErrorWarningLine,
    RiEyeLine,
    RiEyeOffLine,
    RiFacebookFill,
    RiFileChartLine,
    RiFileCopyLine,
    RiFileDownloadLine,
    RiFileLine,
    RiFileListLine,
    RiFileTextLine,
    RiFlagLine,
    RiFocus3Line,
    RiFolderLine,
    RiGift2Line,
    RiGiftLine,
    RiGroupLine,
    RiHandCoinFill,
    RiHandCoinLine,
    RiHandHeartFill,
    RiHandHeartLine,
    RiHeartAddLine,
    RiHeartFill,
    RiHeartLine,
    RiHeartPulseLine,
    RiHome5Line,
    RiHomeHeartLine,
    RiHomeLine,
    RiHourglassLine,
    RiImageAddLine,
    RiImageLine,
    RiInboxLine,
    RiInformationLine,
    RiInstagramLine,
    RiLeafFill,
    RiLineChartLine,
    RiLoader4Line,
    RiLockLine,
    RiLoginBoxLine,
    RiLogoutBoxRLine,
    RiMailCheckLine,
    RiMailLine,
    RiMailSendLine,
    RiMap2Line,
    RiMapPin2Fill,
    RiMapPin2Line,
    RiMapPinLine,
    RiMedicineBottleLine,
    RiMegaphoneLine,
    RiMenuLine,
    RiMessengerLine,
    RiMoneyDollarCircleLine,
    RiNavigationLine,
    RiNotification3Line,
    RiOrganizationChart,
    RiPauseCircleLine,
    RiPencilFill,
    RiPhoneLine,
    RiPlayCircleLine,
    RiPriceTag3Line,
    RiQrCodeLine,
    RiRefreshLine,
    RiSave3Line,
    RiSearchLine,
    RiSendPlaneFill,
    RiSettingsLine,
    RiShareLine,
    RiShieldCheckFill,
    RiShieldCheckLine,
    RiShoppingBasketLine,
    RiStopCircleLine,
    RiTShirtLine,
    RiTeamLine,
    RiTimeLine,
    RiTimerLine,
    RiTruckLine,
    RiTwitterXFill,
    RiUpload2Line,
    RiUser3Line,
    RiUserAddLine,
    RiUserFill,
    RiUserFollowFill,
    RiUserFollowLine,
    RiUserHeartLine,
    RiUserLine,
    RiUserLocationLine,
    RiUserStarLine,
} from 'react-icons/ri';

const iconRoots = new WeakMap();
const iconMap = {
    'ri-add-line': RiAddLine,
    'ri-alarm-line': RiAlarmLine,
    'ri-alarm-warning-line': RiAlarmWarningLine,
    'ri-alert-line': RiAlertLine,
    'ri-arrow-down-s-line': RiArrowDownSLine,
    'ri-arrow-go-back-line': RiArrowGoBackLine,
    'ri-arrow-left-line': RiArrowLeftLine,
    'ri-arrow-left-s-line': RiArrowLeftSLine,
    'ri-arrow-right-line': RiArrowRightLine,
    'ri-arrow-right-s-line': RiArrowRightSLine,
    'ri-arrow-up-line': RiArrowUpLine,
    'ri-arrow-up-s-line': RiArrowUpSLine,
    'ri-award-line': RiAwardLine,
    'ri-bar-chart-line': RiBarChartLine,
    'ri-book-open-line': RiBookOpenLine,
    'ri-box-3-line': RiBox3Line,
    'ri-briefcase-line': RiBriefcaseLine,
    'ri-broadcast-line': RiBroadcastLine,
    'ri-calendar-2-fill': RiCalendar2Fill,
    'ri-calendar-2-line': RiCalendar2Line,
    'ri-calendar-check-line': RiCalendarCheckLine,
    'ri-calendar-event-fill': RiCalendarEventFill,
    'ri-calendar-event-line': RiCalendarEventLine,
    'ri-calendar-line': RiCalendarLine,
    'ri-calendar-schedule-line': RiCalendarScheduleLine,
    'ri-calendar-todo-line': RiCalendarTodoLine,
    'ri-camera-line': RiCameraLine,
    'ri-checkbox-blank-circle-fill': RiCheckboxBlankCircleFill,
    'ri-checkbox-blank-line': RiCheckboxBlankLine,
    'ri-checkbox-circle-fill': RiCheckboxCircleFill,
    'ri-checkbox-circle-line': RiCheckboxCircleLine,
    'ri-checkbox-multiple-line': RiCheckboxMultipleLine,
    'ri-check-double-line': RiCheckDoubleLine,
    'ri-check-line': RiCheckLine,
    'ri-close-circle-line': RiCloseCircleLine,
    'ri-close-line': RiCloseLine,
    'ri-community-line': RiCommunityLine,
    'ri-computer-line': RiComputerLine,
    'ri-contacts-line': RiContactsLine,
    'ri-customer-service-2-line': RiCustomerService2Line,
    'ri-dashboard-line': RiDashboardLine,
    'ri-delete-bin-line': RiDeleteBinLine,
    'ri-drop-line': RiDropLine,
    'ri-edit-2-line': RiEdit2Line,
    'ri-edit-line': RiEditLine,
    'ri-error-warning-line': RiErrorWarningLine,
    'ri-eye-line': RiEyeLine,
    'ri-eye-off-line': RiEyeOffLine,
    'ri-facebook-fill': RiFacebookFill,
    'ri-file-chart-line': RiFileChartLine,
    'ri-file-copy-line': RiFileCopyLine,
    'ri-file-download-line': RiFileDownloadLine,
    'ri-file-line': RiFileLine,
    'ri-file-list-line': RiFileListLine,
    'ri-file-text-line': RiFileTextLine,
    'ri-flag-line': RiFlagLine,
    'ri-folder-line': RiFolderLine,
    'ri-gift-2-line': RiGift2Line,
    'ri-gift-line': RiGiftLine,
    'ri-group-line': RiGroupLine,
    'ri-hand-coin-fill': RiHandCoinFill,
    'ri-hand-coin-line': RiHandCoinLine,
    'ri-hand-heart-fill': RiHandHeartFill,
    'ri-hand-heart-line': RiHandHeartLine,
    'ri-heart-add-line': RiHeartAddLine,
    'ri-heart-fill': RiHeartFill,
    'ri-heart-line': RiHeartLine,
    'ri-heart-pulse-line': RiHeartPulseLine,
    'ri-home-5-line': RiHome5Line,
    'ri-home-heart-line': RiHomeHeartLine,
    'ri-home-line': RiHomeLine,
    'ri-hourglass-line': RiHourglassLine,
    'ri-image-add-line': RiImageAddLine,
    'ri-image-line': RiImageLine,
    'ri-inbox-line': RiInboxLine,
    'ri-information-line': RiInformationLine,
    'ri-instagram-line': RiInstagramLine,
    'ri-leaf-fill': RiLeafFill,
    'ri-line-chart-line': RiLineChartLine,
    'ri-loader-4-line': RiLoader4Line,
    'ri-lock-line': RiLockLine,
    'ri-login-box-line': RiLoginBoxLine,
    'ri-logout-box-r-line': RiLogoutBoxRLine,
    'ri-mail-check-line': RiMailCheckLine,
    'ri-mail-line': RiMailLine,
    'ri-mail-send-line': RiMailSendLine,
    'ri-map-2-line': RiMap2Line,
    'ri-map-pin-2-fill': RiMapPin2Fill,
    'ri-map-pin-2-line': RiMapPin2Line,
    'ri-map-pin-line': RiMapPinLine,
    'ri-medicine-bottle-line': RiMedicineBottleLine,
    'ri-megaphone-line': RiMegaphoneLine,
    'ri-menu-line': RiMenuLine,
    'ri-messenger-line': RiMessengerLine,
    'ri-money-dollar-circle-line': RiMoneyDollarCircleLine,
    'ri-navigation-line': RiNavigationLine,
    'ri-notification-3-line': RiNotification3Line,
    'ri-organization-chart': RiOrganizationChart,
    'ri-pause-circle-line': RiPauseCircleLine,
    'ri-pencil-fill': RiPencilFill,
    'ri-phone-line': RiPhoneLine,
    'ri-play-circle-line': RiPlayCircleLine,
    'ri-price-tag-3-line': RiPriceTag3Line,
    'ri-qr-code-line': RiQrCodeLine,
    'ri-refresh-line': RiRefreshLine,
    'ri-save-3-line': RiSave3Line,
    'ri-search-line': RiSearchLine,
    'ri-send-plane-fill': RiSendPlaneFill,
    'ri-settings-line': RiSettingsLine,
    'ri-share-line': RiShareLine,
    'ri-shield-check-fill': RiShieldCheckFill,
    'ri-shield-check-line': RiShieldCheckLine,
    'ri-shopping-basket-line': RiShoppingBasketLine,
    'ri-stop-circle-line': RiStopCircleLine,
    'ri-target-line': RiFocus3Line,
    'ri-team-line': RiTeamLine,
    'ri-time-line': RiTimeLine,
    'ri-timer-line': RiTimerLine,
    'ri-truck-line': RiTruckLine,
    'ri-t-shirt-line': RiTShirtLine,
    'ri-twitter-x-fill': RiTwitterXFill,
    'ri-upload-2-line': RiUpload2Line,
    'ri-user-3-line': RiUser3Line,
    'ri-user-add-line': RiUserAddLine,
    'ri-user-check-fill': RiUserFollowFill,
    'ri-user-fill': RiUserFill,
    'ri-user-follow-line': RiUserFollowLine,
    'ri-user-heart-line': RiUserHeartLine,
    'ri-user-line': RiUserLine,
    'ri-user-location-line': RiUserLocationLine,
    'ri-user-star-line': RiUserStarLine,
};

function getIconClass(element) {
    return Array.from(element.classList).find((className) => iconMap[className]);
}

function renderIcon(element) {
    const iconClass = getIconClass(element);

    if (!iconClass || element.dataset.tkReactIcon === iconClass) {
        return;
    }

    const Icon = iconMap[iconClass];

    if (!Icon) {
        return;
    }

    let root = iconRoots.get(element);

    if (!root) {
        root = createRoot(element);
        iconRoots.set(element, root);
    }

    element.classList.add('tk-react-icon');
    element.dataset.tkReactIcon = iconClass;
    element.setAttribute('aria-hidden', 'true');

    root.render(React.createElement(Icon, {
        focusable: 'false',
        'aria-hidden': 'true',
    }));
}

function renderIcons(root = document) {
    root.querySelectorAll('[class*="ri-"]').forEach(renderIcon);
}

function observeIcons() {
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.type === 'childList') {
                mutation.addedNodes.forEach((node) => {
                    if (!(node instanceof Element)) {
                        return;
                    }

                    if (getIconClass(node)) {
                        renderIcon(node);
                    }

                    renderIcons(node);
                });
            }

            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                renderIcon(mutation.target);
            }
        });
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true,
        attributes: true,
        attributeFilter: ['class'],
    });
}

function injectIconStyles() {
    if (document.getElementById('tk-react-icon-styles')) {
        return;
    }

    const style = document.createElement('style');
    style.id = 'tk-react-icon-styles';
    style.textContent = `
        .tk-react-icon {
            display: inline-flex;
            width: 1em;
            height: 1em;
            align-items: center;
            justify-content: center;
            color: inherit;
            font-size: inherit;
            line-height: 1;
            vertical-align: -0.125em;
        }

        .tk-react-icon svg {
            display: block;
            width: 1em;
            height: 1em;
            fill: currentColor;
        }

        .tk-react-icon::before {
            display: none !important;
            content: none !important;
        }
    `;
    document.head.appendChild(style);
}

function initReactIcons() {
    injectIconStyles();
    renderIcons();
    observeIcons();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initReactIcons, { once: true });
} else {
    initReactIcons();
}
