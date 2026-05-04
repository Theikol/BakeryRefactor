<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    public function handle(Request $request)
    {
        $userMessage = trim((string) $request->input('message'));

        if ($userMessage === '') {
            return response()->json(['reply' => 'Pesan tidak boleh kosong.'], 422);
        }

        $message = strtolower($userMessage);

        // =========================================================
        // 1. RULE-BASED: cepat, tanpa AI
        // =========================================================

        if ($this->isGreeting($message)) {
            return response()->json([
                'reply' => 'Halo! Selamat datang di Lumineè Bakery 😊 Ada yang bisa saya bantu?',
                'source' => 'rule',
            ]);
        }

        $products = Product::where('stock', '>', 0)
            ->orderBy('category')
            ->take(10)
            ->get();

        if ($this->isProductListRequest($message)) {
            if ($products->isEmpty()) {
                return response()->json([
                    'reply' => 'Saat ini belum ada produk yang tersedia.',
                    'source' => 'rule',
                ]);
            }

            $list = $products->map(function ($p) {
                return "{$p->name} (Rp " . number_format($p->price, 0, ',', '.') . ")";
            })->join(', ');

            return response()->json([
                'reply' => "Berikut beberapa produk kami: {$list}",
                'source' => 'rule',
            ]);
        }

        $matchedProduct = $this->findProductByName($userMessage);

        if ($matchedProduct) {
            return response()->json([
                'reply' => "{$matchedProduct->name} tersedia dengan harga Rp " .
                    number_format($matchedProduct->price, 0, ',', '.') .
                    " dan stok {$matchedProduct->stock}.",
                'source' => 'rule',
            ]);
        }

        // =========================================================
        // 2. LOCAL AI (Python :5090)
        // =========================================================

        $localReply = null;
        $localConfidence = 0.0;

        try {
            $localResponse = Http::timeout(5)
                ->acceptJson()
                ->post('http://127.0.0.1:5090/chat', [
                    'message' => $userMessage,
                ]);

            if ($localResponse->ok()) {
                $localData = $localResponse->json();

                Log::info('Local AI response', is_array($localData) ? $localData : ['raw' => $localData]);

                $localReply = $localData['reply'] ?? null;
                $localConfidence = (float) ($localData['confidence'] ?? 0);

                if ($localReply && $localConfidence >= 0.70) {
                    return response()->json([
                        'reply' => trim($localReply),
                        'source' => 'local-ai',
                    ]);
                }
            } else {
                Log::warning('Local AI non-OK response', [
                    'status' => $localResponse->status(),
                    'body'   => $localResponse->body(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('Local AI failed', ['error' => $e->getMessage()]);
        }

        // =========================================================
        // 3. GROQ FALLBACK
        // =========================================================

        $groqApiKey = config('services.groq.api_key');

        if (!$groqApiKey) {
            // Kalau Groq belum diset, fallback ke local reply jika ada
            if (!empty($localReply)) {
                return response()->json([
                    'reply' => trim($localReply),
                    'source' => 'local-ai-fallback',
                ]);
            }

            return response()->json([
                'reply' => 'Layanan AI belum dikonfigurasi.',
                'source' => 'system',
            ], 503);
        }

        $productList = $products->map(function ($p) {
            return "- {$p->name} | Rp " . number_format($p->price, 0, ',', '.') . " | Stok: {$p->stock}";
        })->join("\n");

        $systemPrompt = <<<PROMPT
Kamu adalah asisten chatbot untuk Lumineè Bakery di Bali.

Tugas:
- Rekomendasi produk
- Jawab pertanyaan pelanggan
- Gunakan bahasa Indonesia yang ramah, singkat, dan jelas
- Maksimal 3 kalimat
- Jangan mengarang informasi di luar data yang diberikan

Produk tersedia saat ini:
{$productList}
PROMPT;

        try {
            $response = Http::connectTimeout(5)
                ->timeout(30)
                ->acceptJson()
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $groqApiKey,
                    'Content-Type'  => 'application/json',
                ])
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => 'llama3-8b-8192',
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $userMessage],
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 200,
                ]);

            if ($response->failed()) {
                Log::error('Groq API error', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);

                if (!empty($localReply)) {
                    return response()->json([
                        'reply' => trim($localReply),
                        'source' => 'local-ai-fallback',
                    ]);
                }

                return response()->json([
                    'reply' => 'Maaf, chatbot sedang sibuk. Silakan coba lagi.',
                    'source' => 'groq',
                ], 500);
            }

            $reply = $response->json('choices.0.message.content');

            if (!$reply) {
                if (!empty($localReply)) {
                    return response()->json([
                        'reply' => trim($localReply),
                        'source' => 'local-ai-fallback',
                    ]);
                }

                return response()->json([
                    'reply' => 'Maaf, saya tidak menemukan jawaban.',
                    'source' => 'groq',
                ], 500);
            }

            return response()->json([
                'reply' => trim($reply),
                'source' => 'groq',
            ]);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Groq timeout', ['error' => $e->getMessage()]);

            if (!empty($localReply)) {
                return response()->json([
                    'reply' => trim($localReply),
                    'source' => 'local-ai-fallback',
                ]);
            }

            return response()->json([
                'reply' => 'Server sedang sibuk. Silakan coba lagi sebentar lagi.',
                'source' => 'groq',
            ], 503);
        } catch (\Throwable $e) {
            Log::error('Unexpected error', ['error' => $e->getMessage()]);

            if (!empty($localReply)) {
                return response()->json([
                    'reply' => trim($localReply),
                    'source' => 'local-ai-fallback',
                ]);
            }

            return response()->json([
                'reply' => 'Terjadi kesalahan sistem.',
                'source' => 'system',
            ], 500);
        }
    }

    private function isGreeting(string $message): bool
    {
        return str_contains($message, 'halo')
            || str_contains($message, 'hai')
            || str_contains($message, 'pagi')
            || str_contains($message, 'siang')
            || str_contains($message, 'sore')
            || str_contains($message, 'malam');
    }

    private function isProductListRequest(string $message): bool
    {
        return str_contains($message, 'produk')
            || str_contains($message, 'menu')
            || str_contains($message, 'daftar')
            || str_contains($message, 'rekomendasi');
    }

    private function findProductByName(string $userMessage): ?Product
    {
        return Product::where('name', 'like', '%' . $userMessage . '%')->first();
    }
}