<?php

namespace App\Services\Chatbot;

use App\Models\Campaign;
use App\Models\CampaignUpdate;
use App\Models\DropOffPoint;
use App\Models\Event;
use App\Models\ImpactReport;
use App\Models\SiteSetting;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Throwable;

class TulongKabataanKnowledgeService
{
    /**
     * Cache key used for the rendered knowledge snapshot.
     */
    public const CACHE_KEY = 'tkb.chatbot.knowledge.v1';

    /**
     * Short TTL keeps things fast when many users chat at once, while model observers
     * bust the cache the moment a user-facing record changes. 60 seconds is an upper bound.
     */
    public const CACHE_TTL_SECONDS = 60;

    /**
     * Public-only tables whose changes should refresh the chatbot knowledge.
     * This explicitly excludes donor, verification, DNC, admin, and user PII tables.
     */
    public const WATCHED_MODELS = [
        Campaign::class,
        CampaignUpdate::class,
        Event::class,
        \App\Models\VolunteerRole::class,
        DropOffPoint::class,
        ImpactReport::class,
        SiteSetting::class,
    ];

    public function build(): string
    {
        // Serve from cache when fresh — observers bust this on any user-side change.
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL_SECONDS, function () {
            return $this->render();
        });
    }

    /**
     * Invalidate the cached knowledge snapshot so the next chat call rebuilds it.
     * Called by model observers when user-side records change.
     */
    public static function forget(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    protected function render(): string
    {
        $sections = [
            'Official user-side guidance' => $this->staticGuidance(),
        ];

        foreach ($this->dynamicPublicSections() as $title => $content) {
            if ($content !== '') {
                $sections[$title] = $content;
            }
        }

        return collect($sections)
            ->map(fn (string $content, string $title) => "## {$title}\n{$content}")
            ->implode("\n\n");
    }

    private function staticGuidance(): string
    {
        return implode("\n", [
            '- Tulong Kabataan user pages include Home, Campaigns, Events, Donate/In-Kind, About Us, Login, Register, Profile, Profile Dashboard, Profile Events, Profile In-Kind, Verification, Notifications, and In-Kind Tracking.',
            '- Registration asks for first name, last name, email, phone number, birthday, and password. Users must verify their email after registering. Google login is also available.',
            '- Login uses email and password. If the account email is not verified, the user is sent to the email verification notice and can request another verification email.',
            '- Password reset starts from the login page by entering the account email and following the reset link sent by email.',
            '- Profile lets users update name, phone number, profile photo, and password.',
            '- Account verification is user-side and supports PhilID and driver license details. Users submit ID photos, a face photo, a selfie with ID, and matching personal details. Driver license verification also requires the back of the ID and an expiry date. Images must be jpeg, png, or webp and up to 7 MB.',
            '- Campaigns: users can browse campaigns, open a campaign page, donate, or create a campaign. Creating a campaign requires a title, organizer name, target amount, description, featured image, GCash QR code, GCash number, and schedule details.',
            '- Campaign donation requires amount, GCash reference number, and proof image. Guests provide donor name and optional email. Users can choose anonymous donation. Submitted donations start as pending.',
            '- Campaign creators can review their campaigns in Profile Dashboard, see campaign analytics, add campaign updates, view donation activity, and report suspicious manual donation requests.',
            '- Events: users can browse events, open event details, register as volunteers, choose an available role, and optionally request reminders. The platform blocks duplicate registrations and warns logged-in users about schedule conflicts.',
            '- Event registration asks for name, email, phone, and may ask for messenger link, age, sex, address, role, and reminder preference.',
            '- In-kind donations: users can submit donated items, choose a drop-off point, and provide donor email, optional donor name/phone, item name, category, quantity, and optional description. Submitted in-kind donations start as Scheduled.',
            '- Users can track in-kind donations through the Donate/In-Kind area and Profile In-Kind page. Logged-in users receive notifications for registered events, in-kind donation confirmation, campaign activity, and donation updates.',
            '- Account status controlled by admin Settings: an account can be Active, Unverified (email not yet verified), or Suspended. Suspended accounts cannot sign in with email and password and cannot sign in with Google. If sign-in is blocked because of suspension, the user is shown a message asking them to contact support.',
            '- Admin Settings control several user-side features, and these may be turned on or off at any time: the announcement banner on the landing page, maintenance mode (the public site is paused and a maintenance message is shown), new user registration, Google sign-in, the floating chatbot assistant, and public access to the Campaigns, Events, and In-kind pages.',
            '- When registration is turned off, the Sign Up page redirects to the Login page and new accounts cannot be created until an admin re-enables it. When Google sign-in is turned off, the Google button stops working and users must use email and password. When the chatbot is turned off, the floating assistant is hidden on the user side. When maintenance mode is on, users see a maintenance notice at the top of the landing page.',
            '- Admin Settings also include a Users tab where admins can search, suspend, activate, or delete user accounts. Deleting an account is permanent. Suspending an account blocks future sign-ins but keeps the records.',
            '- Support shown publicly: email tulongkabataan.bicol@gmail.com, Facebook @tulongkabataanbicol, emergency relief phone +63 912 345 6789, headquarters at 2nd Floor, Community Center Bldg, Rizal Street, Legazpi City, Albay, Philippines 4500. Visiting hours are Monday to Friday, 9:00 AM to 5:00 PM, and users should schedule by email before visiting.',
        ]);
    }

    /**
     * Build context from public-facing records only. User submissions are treated as untrusted
     * content, so the model is told not to follow instructions inside this section.
     * Strictly limited to user-side sources — no donors, verification, DNC, or admin data.
     */
    private function dynamicPublicSections(): array
    {
        try {
            return [
                'Current user-side platform settings' => $this->platformSettingsContext(),
                'Recent user-side changes' => $this->recentChangesContext(),
                'Latest public campaigns' => $this->campaignContext(),
                'Latest campaign updates' => $this->campaignUpdatesContext(),
                'Latest public events' => $this->eventContext(),
                'Active in-kind drop-off points' => $this->dropOffContext(),
                'Latest public impact reports' => $this->impactReportContext(),
                'Context timestamp' => 'Generated from public platform data on ' . Carbon::now()->toDayDateTimeString() . '.',
                'Context signature' => 'Freshness signature: ' . $this->freshnessSignature(),
            ];
        } catch (Throwable) {
            return [
                'Current public platform data' => 'Live public records are temporarily unavailable. Use only the official user-side guidance above and avoid guessing.',
            ];
        }
    }

    private function platformSettingsContext(): string
    {
        $s = SiteSetting::all_keyed();

        $yesNo = fn (bool $on) => $on ? 'on' : 'off';

        $lines = [
            '- Registration of new accounts is currently ' . $yesNo((bool) ($s['user.registration.enabled'] ?? true)) . '.',
            '- Google sign-in is currently ' . $yesNo((bool) ($s['user.google_login.enabled'] ?? true)) . '.',
            '- Floating chatbot assistant on the user side is currently ' . $yesNo((bool) ($s['user.chatbot.enabled'] ?? true)) . '.',
            '- Public Campaigns page is currently ' . $yesNo((bool) ($s['user.campaigns.public'] ?? true)) . '.',
            '- Public Events page is currently ' . $yesNo((bool) ($s['user.events.public'] ?? true)) . '.',
            '- Public In-kind page is currently ' . $yesNo((bool) ($s['user.inkind.public'] ?? true)) . '.',
            '- Maintenance mode is currently ' . $yesNo((bool) ($s['site.maintenance.enabled'] ?? false)) . '.',
        ];

        if (!empty($s['site.maintenance.enabled']) && !empty($s['site.maintenance.message'])) {
            $lines[] = '- Current maintenance message: ' . $this->clean($s['site.maintenance.message'], 220);
        }

        if (!empty($s['site.announcement.enabled'])) {
            $title = trim((string) ($s['site.announcement.title'] ?? ''));
            $msg = trim((string) ($s['site.announcement.message'] ?? ''));
            if ($title !== '' || $msg !== '') {
                $lines[] = '- Current announcement banner: ' .
                    ($title !== '' ? $title . ' — ' : '') . $this->clean($msg, 220);
            } else {
                $lines[] = '- Announcement banner is on but has no content.';
            }
        } else {
            $lines[] = '- No announcement banner is being shown right now.';
        }

        $lines[] = '- User accounts can be Active, Unverified, or Suspended. Suspended accounts cannot sign in with email and password and cannot sign in with Google.';

        return implode("\n", $lines);
    }

    /**
     * A short digest of the most recent user-side changes across public tables.
     * Helps the chatbot surface "what's new" without fetching every record.
     */
    private function recentChangesContext(): string
    {
        $lines = [];

        $addLine = function (string $kind, $record, string $timestamp = 'updated_at') use (&$lines) {
            if (! $record) {
                return;
            }
            $when = $record->{$timestamp} ?? $record->updated_at ?? null;
            $lines[] = sprintf(
                '- %s: "%s" (%s)',
                $kind,
                $this->clean((string) ($record->title ?? $record->name ?? 'Untitled'), 120),
                $when ? Carbon::parse($when)->diffForHumans() : 'recently'
            );
        };

        $latestCampaign = Campaign::query()->latest('updated_at')->first(['campaign_id', 'title', 'updated_at']);
        $addLine('Campaign', $latestCampaign);

        $latestEvent = Event::query()->latest('updated_at')->first(['event_id', 'title', 'updated_at']);
        $addLine('Event', $latestEvent);

        $latestDropOff = DropOffPoint::query()->where('is_active', true)->latest('updated_at')->first(['name', 'updated_at']);
        $addLine('Drop-off point', $latestDropOff);

        $latestReport = ImpactReport::query()->latest('updated_at')->first(['title', 'updated_at']);
        $addLine('Impact report', $latestReport);

        $latestUpdate = CampaignUpdate::query()
            ->with(['campaign:campaign_id,title'])
            ->latest('updated_at')
            ->first(['update_id', 'campaign_id', 'message', 'updated_at']);
        if ($latestUpdate) {
            $lines[] = sprintf(
                '- Campaign update on "%s" (%s): %s',
                $this->clean((string) optional($latestUpdate->campaign)->title ?: 'a campaign', 100),
                $latestUpdate->updated_at ? Carbon::parse($latestUpdate->updated_at)->diffForHumans() : 'recently',
                $this->clean((string) $latestUpdate->message, 180)
            );
        }

        $latestSetting = SiteSetting::query()->latest('updated_at')->first(['key', 'updated_at']);
        if ($latestSetting) {
            $lines[] = sprintf(
                '- Platform setting change (%s): %s',
                $this->clean((string) $latestSetting->key, 80),
                $latestSetting->updated_at ? Carbon::parse($latestSetting->updated_at)->diffForHumans() : 'recently'
            );
        }

        if (empty($lines)) {
            return 'No recent user-side changes have been recorded yet.';
        }

        return implode("\n", $lines);
    }

    private function campaignContext(): string
    {
        $campaigns = Campaign::query()
            ->select(['campaign_id', 'title', 'description', 'target_amount', 'current_amount', 'status', 'starts_at', 'ends_at', 'updated_at'])
            ->whereIn('status', ['active', 'scheduled', 'ended', 'completed'])
            ->latest('updated_at')
            ->limit(6)
            ->get();

        if ($campaigns->isEmpty()) {
            return 'No public campaign records are currently available.';
        }

        return $campaigns->map(function (Campaign $campaign) {
            $dates = collect([
                $campaign->starts_at ? 'starts ' . $campaign->starts_at->toFormattedDateString() : null,
                $campaign->ends_at ? 'ends ' . $campaign->ends_at->toFormattedDateString() : null,
            ])->filter()->implode(', ');

            return sprintf(
                '- %s: status %s; goal PHP %s; raised PHP %s%s; last updated %s. Summary: %s',
                $this->clean($campaign->title, 120),
                $this->clean($campaign->status, 40),
                number_format((float) $campaign->target_amount, 2),
                number_format((float) $campaign->current_amount, 2),
                $dates ? '; ' . $dates : '',
                $campaign->updated_at ? $campaign->updated_at->diffForHumans() : 'recently',
                $this->clean($campaign->description, 220)
            );
        })->implode("\n");
    }

    /**
     * Public campaign updates (organizer posts) associated with public campaigns.
     * Private donor information is never included.
     */
    private function campaignUpdatesContext(): string
    {
        // MongoDB: no Schema::hasTable check needed (schemaless)
        $activeCampaignIds = Campaign::whereIn('status', ['active', 'scheduled', 'ended', 'completed'])
            ->pluck('_id');

        $updates = CampaignUpdate::query()
            ->with(['campaign'])
            ->whereIn('campaign_id', $activeCampaignIds)
            ->latest('updated_at')
            ->limit(5)
            ->get();

        if ($updates->isEmpty()) {
            return 'No public campaign updates have been posted yet.';
        }

        return $updates->map(function (CampaignUpdate $u) {
            return sprintf(
                '- "%s" update (%s): %s',
                $this->clean((string) optional($u->campaign)->title ?: 'Campaign', 100),
                $u->updated_at ? Carbon::parse($u->updated_at)->diffForHumans() : 'recently',
                $this->clean((string) $u->message, 220)
            );
        })->implode("\n");
    }

    private function eventContext(): string
    {
        $events = Event::with(['volunteerRoles' => fn ($query) => $query->select(['vroles_id', 'event_id', 'name', 'description'])])
            ->select(['event_id', 'title', 'description', 'start_date', 'end_date', 'location', 'deadline', 'updated_at'])
            ->latest('updated_at')
            ->limit(6)
            ->get();

        if ($events->isEmpty()) {
            return 'No public event records are currently available.';
        }

        return $events->map(function (Event $event) {
            $roles = $event->volunteerRoles
                ->pluck('name')
                ->filter()
                ->map(fn ($role) => $this->clean((string) $role, 60))
                ->implode(', ');

            return sprintf(
                '- %s: %s to %s; location %s; registration deadline %s%s; last updated %s. Summary: %s',
                $this->clean($event->title, 120),
                $this->formatDate($event->start_date),
                $this->formatDate($event->end_date),
                $this->clean($event->location ?: 'not specified', 120),
                $this->formatDate($event->deadline),
                $roles ? '; roles: ' . $roles : '',
                $event->updated_at ? Carbon::parse($event->updated_at)->diffForHumans() : 'recently',
                $this->clean($event->description, 220)
            );
        })->implode("\n");
    }

    private function dropOffContext(): string
    {
        $dropOffPoints = DropOffPoint::query()
            ->select(['dropoff_id', 'name', 'address', 'schedule_datetime', 'is_active', 'updated_at'])
            ->where('is_active', true)
            ->orderBy('name')
            ->limit(8)
            ->get();

        if ($dropOffPoints->isEmpty()) {
            return 'No active drop-off points are currently listed.';
        }

        return $dropOffPoints->map(function (DropOffPoint $point) {
            return sprintf(
                '- %s: %s%s; last updated %s',
                $this->clean($point->name, 100),
                $this->clean($point->address, 160),
                $point->schedule_datetime ? '; schedule ' . $this->formatDate($point->schedule_datetime) : '',
                $point->updated_at ? Carbon::parse($point->updated_at)->diffForHumans() : 'recently'
            );
        })->implode("\n");
    }

    private function impactReportContext(): string
    {
        $reports = ImpactReport::query()
            ->select(['title', 'description', 'report_date', 'updated_at'])
            ->latest('report_date')
            ->limit(5)
            ->get();

        if ($reports->isEmpty()) {
            return 'No public impact reports are currently listed.';
        }

        return $reports->map(function (ImpactReport $report) {
            return sprintf(
                '- %s (%s): %s',
                $this->clean($report->title, 120),
                $this->formatDate($report->report_date),
                $this->clean($report->description, 220)
            );
        })->implode("\n");
    }

    /**
     * A short hash of the latest updated_at values across watched tables.
     * Changes whenever any user-side record changes, so the LLM can see
     * the context has been refreshed.
     */
    private function freshnessSignature(): string
    {
        $parts = [];

        $tables = [
            'campaigns',
            'campaign_updates',
            'events',
            'volunteer_roles',
            'drop_off_points',
            'impact_reports',
            'site_settings',
        ];

        foreach ($tables as $table) {
            try {
                // MongoDB-compatible: use the model's collection to get count and latest updated_at
                $collection = \Illuminate\Support\Facades\DB::connection('mongodb')->collection($table);
                $count = $collection->count();
                $latest = $collection->orderBy('updated_at', 'desc')->first();
                $m = $latest['updated_at'] ?? '-';
                if ($m instanceof \MongoDB\BSON\UTCDateTime) {
                    $m = $m->toDateTime()->format('Y-m-d H:i:s');
                }
                $parts[] = $table . '=' . $m . ':' . $count;
            } catch (\Throwable) {
                // Ignore if a collection is missing or query fails; keep the signature best-effort.
            }
        }

        return substr(sha1(implode('|', $parts)), 0, 10);
    }

    private function formatDate(mixed $value): string
    {
        if (blank($value)) {
            return 'not specified';
        }

        try {
            return Carbon::parse($value)->toDayDateTimeString();
        } catch (Throwable) {
            return $this->clean((string) $value, 80);
        }
    }

    private function clean(?string $value, int $limit): string
    {
        $text = trim((string) Str::of($value ?? '')
            ->stripTags()
            ->replaceMatches('/\s+/', ' '));

        return $text === '' ? 'not specified' : Str::limit($text, $limit);
    }
}
