<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Http\Request;
use Morilog\Jalali\Jalalian;

class ClientNotification extends Model
{
    use HasFactory;

    protected $table = 'client_notifications';

    protected $fillable = [
        'user_id',
        'type',
        'message'
    ];

    public function scopeFilter($query, Request $request)
    {

        if ($user_id = $request->input('query.user_id')) {
            $query->where('user_id', $user_id);
        }

        if ($id = $request->input('query.id')) {
            $query->where('id', $id);
        }

        if ($from_date = $request->input('query.from_date')) {
            $from_date = Jalalian::fromFormat('Y-m-d', $from_date)->toCarbon();

            $query->whereDate('created_at', '>=', $from_date);
        }

        if ($to_date = $request->input('query.to_date')) {
            $to_date = Jalalian::fromFormat('Y-m-d', $to_date)->toCarbon();

            $query->whereDate('created_at', '<=', $to_date);
        }

        if ($request->sort) {

            switch ($request->sort['field']) {
                case 'id': {
                    $query->orderBy('id', $request->sort['sort']);
                    break;
                }
                default: {
                    if ($this->getConnection()->getSchemaBuilder()->hasColumn($this->getTable(), $request->sort['field'])) {
                        $query->orderBy($request->sort['field'], $request->sort['sort']);
                    }
                }
            }
        }

        return $query;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(ClientNotificationLog::class);
    }
}
