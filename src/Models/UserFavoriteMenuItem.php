<?php

namespace JacquesTredoux\VueSidebarMenu\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserFavoriteMenuItem extends Model
{
    protected $fillable = [
        'user_id',
        'menu_key',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    /**
     * Get the user that owns this favorite menu item.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model', 'App\Models\User'));
    }
}

