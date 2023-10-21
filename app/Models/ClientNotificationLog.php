<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientNotificationLog extends Model
{
    use HasFactory;

    protected $table = 'client_notifications_log';

    protected $fillable = [
        'user_id',
        'client_notifications_id'
    ];

    public function notification(): BelongsTo
    {
        return $this->belongsTo(ClientNotification::class);
    }
}
