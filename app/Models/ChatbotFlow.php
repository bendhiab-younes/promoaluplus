<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatbotFlow extends Model
{
    protected $fillable = [
        'trigger',
        'trigger_type',
        'keywords',
        'message',
        'quick_replies',
        'action',
        'action_value',
        'parent_id',
        'order',
        'is_active',
    ];

    protected $casts = [
        'keywords' => 'array',
        'message' => 'array',
        'quick_replies' => 'array',
        'is_active' => 'boolean',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ChatbotFlow::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ChatbotFlow::class, 'parent_id')->orderBy('order');
    }

    public function getMessageForLocale(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        return $this->message[$locale] ?? $this->message['fr'] ?? '';
    }

    public function getQuickRepliesForLocale(?string $locale = null): array
    {
        $locale = $locale ?? app()->getLocale();
        $replies = $this->quick_replies ?? [];
        
        return array_map(function ($reply) use ($locale) {
            return [
                'text' => $reply['text'][$locale] ?? $reply['text']['fr'] ?? '',
                'action' => $reply['action'] ?? null,
            ];
        }, $replies);
    }

    public static function findByTrigger(string $trigger): ?self
    {
        return static::where('trigger', $trigger)
            ->where('is_active', true)
            ->first();
    }

    public static function findByKeyword(string $text): ?self
    {
        $text = mb_strtolower($text);
        
        return static::where('trigger_type', 'keyword')
            ->where('is_active', true)
            ->get()
            ->first(function ($flow) use ($text) {
                $keywords = $flow->keywords ?? [];
                foreach ($keywords as $keyword) {
                    if (str_contains($text, mb_strtolower($keyword))) {
                        return true;
                    }
                }
                return false;
            });
    }

    public static function getWelcomeFlow(): ?self
    {
        return static::findByTrigger('welcome');
    }

    public static function getFallbackFlow(): ?self
    {
        return static::where('trigger_type', 'fallback')
            ->where('is_active', true)
            ->first();
    }
}
