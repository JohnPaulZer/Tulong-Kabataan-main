import React from 'react';
import { createRoot } from 'react-dom/client';
import {
    RiAddLine,
    RiAlarmLine,
    RiAlarmWarningLine,
    RiAlertLine,
    RiApps2Line,
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
    RiChatSmile3Line,
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
    RiCustomerServiceLine,
    RiDashboardLine,
    RiDatabase2Line,
    RiDeleteBinLine,
    RiDownloadLine,
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
    RiFileList3Line,
    RiFileListLine,
    RiFileTextLine,
    RiFlagLine,
    RiFlashlightLine,
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
    RiLayoutRightLine,
    RiLeafFill,
    RiLineChartLine,
    RiLoader4Line,
    RiLock2Line,
    RiLockLine,
    RiLoginBoxLine,
    RiLogoutBoxRLine,
    RiMailCheckLine,
    RiMailCloseLine,
    RiMailLine,
    RiMailSendLine,
    RiMap2Line,
    RiMapPin2Fill,
    RiMapPin2Line,
    RiMapPinLine,
    RiMedicineBottleLine,
    RiMegaphoneLine,
    RiMenu3Line,
    RiMenuLine,
    RiMessage3Line,
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
    RiPriceTagLine,
    RiQrCodeLine,
    RiQrScanLine,
    RiRefreshLine,
    RiRepeatLine,
    RiSave3Line,
    RiSaveLine,
    RiSearchLine,
    RiSendPlane2Fill,
    RiSendPlaneFill,
    RiSendPlaneLine,
    RiSettings3Line,
    RiSettingsLine,
    RiShareLine,
    RiShieldCheckFill,
    RiShieldCheckLine,
    RiShieldUserLine,
    RiShoppingBasketLine,
    RiStackLine,
    RiStopCircleLine,
    RiTShirtLine,
    RiTeamLine,
    RiTimeLine,
    RiTimerLine,
    RiToggleLine,
    RiToolsLine,
    RiTrophyLine,
    RiTruckLine,
    RiTwitterXFill,
    RiUpload2Line,
    RiUploadCloud2Line,
    RiUser3Line,
    RiUserAddLine,
    RiUserFill,
    RiUserForbidLine,
    RiUserFollowFill,
    RiUserFollowLine,
    RiUserHeartLine,
    RiUserLine,
    RiUserLocationLine,
    RiUserSearchLine,
    RiUserSettingsLine,
    RiUserStarLine,
} from 'react-icons/ri';

const iconRoots = new WeakMap();
const iconMap = {
    'ri-add-line': RiAddLine,
    'ri-alarm-line': RiAlarmLine,
    'ri-alarm-warning-line': RiAlarmWarningLine,
    'ri-alert-line': RiAlertLine,
    'ri-apps-2-line': RiApps2Line,
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
    'ri-chat-smile-3-line': RiChatSmile3Line,
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
    'ri-customer-service-line': RiCustomerServiceLine,
    'ri-dashboard-line': RiDashboardLine,
    'ri-database-2-line': RiDatabase2Line,
    'ri-delete-bin-line': RiDeleteBinLine,
    'ri-download-line': RiDownloadLine,
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
    'ri-file-list-3-line': RiFileList3Line,
    'ri-file-list-line': RiFileListLine,
    'ri-file-text-line': RiFileTextLine,
    'ri-flag-line': RiFlagLine,
    'ri-flashlight-line': RiFlashlightLine,
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
    'ri-layout-right-line': RiLayoutRightLine,
    'ri-leaf-fill': RiLeafFill,
    'ri-line-chart-line': RiLineChartLine,
    'ri-loader-4-line': RiLoader4Line,
    'ri-lock-2-line': RiLock2Line,
    'ri-lock-line': RiLockLine,
    'ri-login-box-line': RiLoginBoxLine,
    'ri-logout-box-r-line': RiLogoutBoxRLine,
    'ri-mail-check-line': RiMailCheckLine,
    'ri-mail-close-line': RiMailCloseLine,
    'ri-mail-line': RiMailLine,
    'ri-mail-send-line': RiMailSendLine,
    'ri-map-2-line': RiMap2Line,
    'ri-map-pin-2-fill': RiMapPin2Fill,
    'ri-map-pin-2-line': RiMapPin2Line,
    'ri-map-pin-line': RiMapPinLine,
    'ri-medicine-bottle-line': RiMedicineBottleLine,
    'ri-megaphone-line': RiMegaphoneLine,
    'ri-menu-3-line': RiMenu3Line,
    'ri-menu-line': RiMenuLine,
    'ri-message-3-line': RiMessage3Line,
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
    'ri-price-tag-line': RiPriceTagLine,
    'ri-qr-code-line': RiQrCodeLine,
    'ri-qr-scan-line': RiQrScanLine,
    'ri-refresh-line': RiRefreshLine,
    'ri-repeat-line': RiRepeatLine,
    'ri-save-3-line': RiSave3Line,
    'ri-save-line': RiSaveLine,
    'ri-search-line': RiSearchLine,
    'ri-send-plane-2-fill': RiSendPlane2Fill,
    'ri-send-plane-fill': RiSendPlaneFill,
    'ri-send-plane-line': RiSendPlaneLine,
    'ri-settings-3-line': RiSettings3Line,
    'ri-settings-line': RiSettingsLine,
    'ri-share-line': RiShareLine,
    'ri-shield-check-fill': RiShieldCheckFill,
    'ri-shield-check-line': RiShieldCheckLine,
    'ri-shield-user-line': RiShieldUserLine,
    'ri-shopping-basket-line': RiShoppingBasketLine,
    'ri-stack-line': RiStackLine,
    'ri-stop-circle-line': RiStopCircleLine,
    'ri-target-line': RiFocus3Line,
    'ri-team-line': RiTeamLine,
    'ri-time-line': RiTimeLine,
    'ri-timer-line': RiTimerLine,
    'ri-toggle-line': RiToggleLine,
    'ri-tools-line': RiToolsLine,
    'ri-trophy-line': RiTrophyLine,
    'ri-truck-line': RiTruckLine,
    'ri-t-shirt-line': RiTShirtLine,
    'ri-twitter-x-fill': RiTwitterXFill,
    'ri-upload-2-line': RiUpload2Line,
    'ri-upload-cloud-2-line': RiUploadCloud2Line,
    'ri-user-3-line': RiUser3Line,
    'ri-user-add-line': RiUserAddLine,
    'ri-user-check-fill': RiUserFollowFill,
    'ri-user-fill': RiUserFill,
    'ri-user-forbid-line': RiUserForbidLine,
    'ri-user-follow-line': RiUserFollowLine,
    'ri-user-heart-line': RiUserHeartLine,
    'ri-user-line': RiUserLine,
    'ri-user-location-line': RiUserLocationLine,
    'ri-user-search-line': RiUserSearchLine,
    'ri-user-settings-line': RiUserSettingsLine,
    'ri-user-star-line': RiUserStarLine,
};

function getIconClass(element) {
    return Array.from(element.classList).find((className) => iconMap[className]);
}

function renderIcon(element) {
    const iconClass = getIconClass(element);

    if (!iconClass) {
        return;
    }

    if (element.dataset.tkReactIcon === iconClass && element.querySelector('svg')) {
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

function scheduleRenderIcons(root = document) {
    window.requestAnimationFrame(() => renderIcons(root));
}

function bindLivewireIconRendering() {
    document.addEventListener('livewire:init', () => {
        if (!window.Livewire || typeof window.Livewire.hook !== 'function') {
            return;
        }

        window.Livewire.hook('morph.updated', ({ el }) => {
            if (el instanceof Element) {
                scheduleRenderIcons(el);
            }
        });

        window.Livewire.hook('commit', ({ succeed }) => {
            succeed(() => scheduleRenderIcons());
        });
    }, { once: true });
}

function initReactIcons() {
    injectIconStyles();
    renderIcons();
    bindLivewireIconRendering();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initReactIcons, { once: true });
} else {
    initReactIcons();
}
