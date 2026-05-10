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
            'history' => ['sometimes', 'array', 'max:8'],
            'history.*.role' => ['required_with:history', 'in:user,assistant'],
            'history.*.content' => ['required_with:history', 'string', 'max:700'],
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
                    $validator->validated()['history'] ?? []
                ),
            ]);
        } catch (Throwable) {
            return response()->json([
                'reply' => TulongKabataanChatbotService::UNKNOWN_REPLY,
            ], 500);
        }
    }
}
