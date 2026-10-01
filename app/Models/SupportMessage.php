<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class SupportMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ticket_number',
        'name',
        'email',
        'subject',
        'message',
        'attachment_urls',
        'attachment_public_ids',
        'status',
    ];

    protected $casts = [
        'attachment_urls'       => 'array',
        'attachment_public_ids' => 'array',
    ];

    /**
     * Auto-generate a unique, non-predictable ticket number on creation.
     * Format: TKT-XXXXXXXX (8 random uppercase alphanumeric chars)
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $model) {
            do {
                $number = 'TKT-' . strtoupper(Str::random(8));
            } while (static::where('ticket_number', $number)->exists());

            $model->ticket_number = $number;
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
