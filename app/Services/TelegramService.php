<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TelegramService
{
    protected string $token;
    protected string $baseUrl;

    public function __construct(?string $token = null)
    {
        $this->token = $token ?? config('services.telegram.bot_token');
        $this->baseUrl = "https://api.telegram.org/bot{$this->token}";
    }

    /**
     * Send a text message to a channel, group, or user.
     *
     * @param  string       $text                 The message text
     * @param  string|null  $chatId               @username or numeric id; defaults to env
     * @param  string|null  $parseMode            'MarkdownV2' | 'HTML' | null
     * @param  bool         $disablePreview       disable link previews
     * @return array                             Telegram API JSON response
     * @throws \Illuminate\Http\Client\RequestException on HTTP error
     */
    public function sendMessage(
        string $text,
        ?string $chatId = null,
        ?string $parseMode = null,
        bool $disablePreview = false ,
    ): array {
        $chatId = $chatId ?? config('services.telegram.chat_id');

        $payload = array_filter([
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => $parseMode,                 // 'MarkdownV2' or 'HTML'
            'disable_web_page_preview' => $disablePreview,
        ], fn ($v) => $v !== null);

        $response = Http::asForm()->withoutVerifying()->post("{$this->baseUrl}/sendMessage", $payload);

        if ($response->failed()) {
            // Surfaces Telegram error details (e.g., "bot was blocked by the user", "not enough rights")
            $response->throw();
        }

        return $response->json();
    }



}
