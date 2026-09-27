<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoMeta extends Model
{
    use HasFactory;

    protected $table = 'seo_metas';

    protected $fillable = [
        'page_key',
        'page_name',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'is_indexed',
        'is_followed',
        'og_title',
        'og_description',
        'og_image',
        'twitter_card',
        'json_ld',
    ];

    protected $casts = [
        'is_indexed' => 'boolean',
        'is_followed' => 'boolean',
    ];

    /**
     * Get SEO metadata for a specific page key.
     */
    public static function getForPage(?string $pageKey): ?self
    {
        if (empty($pageKey)) {
            return null;
        }

        return static::where('page_key', $pageKey)->first();
    }

    /**
     * Get the robots directive string (e.g., 'index, follow' or 'noindex, nofollow').
     */
    public function getRobotsDirectiveAttribute(): string
    {
        $index = $this->is_indexed ? 'index' : 'noindex';
        $follow = $this->is_followed ? 'follow' : 'nofollow';
        return "{$index}, {$follow}";
    }

    /**
     * Check if custom JSON-LD schema is valid.
     */
    public function getParsedJsonLdAttribute(): ?array
    {
        if (empty($this->json_ld)) {
            return null;
        }

        $decoded = json_decode($this->json_ld, true);
        return json_last_error() === JSON_ERROR_NONE ? $decoded : null;
    }
}
