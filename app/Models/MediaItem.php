<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MediaItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'album_id', 'tipe', 'file_path', 'video_url', 'caption', 'sort_order',
        'publication_approved_at', 'publication_approved_by',
    ];

    protected function casts(): array
    {
        return ['publication_approved_at' => 'datetime'];
    }

    public function album(): BelongsTo
    {
        return $this->belongsTo(MediaAlbum::class, 'album_id');
    }

    public function youtubeVideoId(): ?string
    {
        if (! is_string($this->video_url) || $this->video_url === '') {
            return null;
        }

        $host = strtolower((string) parse_url($this->video_url, PHP_URL_HOST));
        if (! in_array($host, ['youtube.com', 'www.youtube.com', 'm.youtube.com', 'youtu.be', 'www.youtu.be'], true)) {
            return null;
        }

        $path = trim((string) parse_url($this->video_url, PHP_URL_PATH), '/');
        $videoId = '';

        if ($host === 'youtu.be' || $host === 'www.youtu.be') {
            $videoId = $path;
        } else {
            parse_str((string) parse_url($this->video_url, PHP_URL_QUERY), $query);
            $videoId = $query['v'] ?? '';
        }

        if ($videoId === '' && preg_match('#^(?:embed|shorts)/([A-Za-z0-9_-]{11})$#', $path, $matches)) {
            $videoId = $matches[1];
        }

        return preg_match('/^[A-Za-z0-9_-]{11}$/', $videoId) === 1 ? $videoId : null;
    }
}
