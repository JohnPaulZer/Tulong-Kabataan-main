<?php

namespace App\Http\Controllers;

use App\Services\Chatbot\TulongKabataanChatbotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class ChatbotController
{
    public function message(Request $request, TulongKabataanChatbotService $chatbot): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'message' => ['required', 'string', 'min:1', 'max:600'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'reply' => TulongKabataanChatbotService::UNKNOWN_REPLY,
            ], 422);
        }

        try {
            return response()->json([
                'reply' => $chatbot->reply(
                    (string) $validator->validated()['message'],
                    $this->safeHistory($request->input('history', []))
                ),
            ]);
        } catch (Throwable) {
            return response()->json([
                'reply' => TulongKabataanChatbotService::UNKNOWN_REPLY,
            ], 500);
        }
    }

    private function safeHistory(mixed $history): array
    {
        if (! is_array($history)) {
            return [];
        }

        return collect($history)
            ->filter(fn ($item) => is_array($item))
            ->map(function (array $item) {
                $role = $item['role'] ?? '';
                $content = trim(strip_tags((string) ($item['content'] ?? '')));

                if (! in_array($role, ['user', 'assistant'], true) || $content === '') {
                    return null;
                }

                return [
                    'role' => $role,
                    'content' => mb_substr($content, 0, 700),
                ];
            })
            ->filter()
            ->take(-8)
            ->values()
            ->all();
v    }
}
