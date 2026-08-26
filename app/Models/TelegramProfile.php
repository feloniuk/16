<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TelegramProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'telegram_user_id', 'telegram_chat_id', 'username', 'first_name', 'last_name',
        'language_code', 'phone', 'is_bot', 'default_branch_id', 'default_room_number',
        'is_profile_complete', 'notifications_enabled', 'contact_consent', 'is_blocked',
        'last_interaction_at',
    ];

    protected function casts(): array
    {
        return ['telegram_user_id' => 'integer', 'telegram_chat_id' => 'integer', 'is_bot' => 'boolean', 'is_profile_complete' => 'boolean', 'notifications_enabled' => 'boolean', 'contact_consent' => 'boolean', 'is_blocked' => 'boolean', 'last_interaction_at' => 'datetime'];
    }

    public function defaultBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'default_branch_id');
    }
}
