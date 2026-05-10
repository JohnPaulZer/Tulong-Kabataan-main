<?php

namespace App\Services\Chatbot;

use App\Models\Campaign;
use App\Models\DropOffPoint;
use App\Models\Event;
use App\Models\ImpactReport;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Throwable;

class TulongKabataanKnowledgeService
{
    public function build(): string
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
            '- Support shown publicly: email tulongkabataan.bicol@gmail.com, Facebook @tulongkabataanbicol, emergency relief phone +63 912 345 6789, headquarters at 2nd Floor, Community Center Bldg, Rizal Street, Legazpi City, Albay, Philippines 4500. Visiting hours are Monday to Friday, 9:00 AM to 5:00 PM, and users should schedule by email before visiting.',
        ]);
    }

    /**
     * Build context from public-facing records only. User submissions are treated as untrusted
     * content, so the model is told not to follow instructions inside this section.
     */
    private function dynamicPublicSections(): array
    {
        try {
            return [
                'Latest public campaigns' => $this->campaignContext(),
                'Latest public events' => $this->eventContext(),
                'Active in-kind drop-off points' => $this->dropOffContext(),
                'Latest public impact reports' => $this->impactReportContext(),
                'Context timestamp' => 'Generated from public platform data on ' . Carbon::now()->toDayDateTimeString() . '.',
            ];
        } catch (Throwable) {
            return [
                'Current public platform data' => 'Live public records are temporarily unavailable. Use only the official user-side guidance above and avoid guessing.',
            ];
        }
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
                '- %s: status %s; goal PHP %s; raised PHP %s%s. Summary: %s',
                $this->clean($campaign->title, 120),
                $this->clean($campaign->status, 40),
                number_format((float) $campaign->target_amount, 2),
                number_format((float) $campaign->current_amount, 2),
                $dates ? '; ' . $dates : '',
                $this->clean($campaign->description, 220)
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
                '- %s: %s to %s; location %s; registration deadline %s%s. Summary: %s',
                $this->clean($event->title, 120),
                $this->formatDate($event->start_date),
                $this->formatDate($event->end_date),
                $this->clean($event->location ?: 'not specified', 120),
                $this->formatDate($event->deadline),
                $roles ? '; roles: ' . $roles : '',
                $this->clean($event->description, 220)
            );
        })->implode("\n");
    }

    private function dropOffContext(): string
    {
        $dropOffPoints = DropOffPoint::query()
            ->select(['name', 'address', 'schedule_datetime', 'is_active'])
            ->where('is_active', true)
            ->orderBy('name')
            ->limit(8)
            ->get();

        if ($dropOffPoints->isEmpty()) {
            return 'No active drop-off points are currently listed.';
        }

        return $dropOffPoints->map(function (DropOffPoint $point) {
            return sprintf(
                '- %s: %s%s',
                $this->clean($point->name, 100),
                $this->clean($point->address, 160),
                $point->schedule_datetime ? '; schedule ' . $this->formatDate($point->schedule_datetime) : ''
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
