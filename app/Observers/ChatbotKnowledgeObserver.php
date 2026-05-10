<?php

namespace App\Observers;

use App\Services\Chatbot\TulongKabataanKnowledgeService;

/**
 * Refresh the chatbot's knowledge snapshot whenever a watched user-side model changes.
 * Limited to public user-side tables only — donor, verification, DNC, and admin records
 * are not watched and never feed the chatbot.
 */
class ChatbotKnowledgeObserver
{
    public function created($model): void
    {
        TulongKabataanKnowledgeService::forget();
    }

    public function updated($model): void
    {
        TulongKabataanKnowledgeService::forget();
    }

    public function deleted($model): void
    {
        TulongKabataanKnowledgeService::forget();
    }

    public function restored($model): void
    {
        TulongKabataanKnowledgeService::forget();
    }

    public function forceDeleted($model): void
    {
        TulongKabataanKnowledgeService::forget();
    }
}
