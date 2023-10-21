<?php

namespace App\Http\Resources\Api\V1\ClientNotification;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Log;

class ClientNotificationCollection extends ResourceCollection
{

    public function toArray($request)
    {
        $data = $this->collection->map(function ($clientNotification) {
            return [
                'id' => $clientNotification->id,
                'user_id' => $clientNotification->user_id,
                'user_fullname' =>
                    $clientNotification->user_id == 0
                        ? ''
                        : $clientNotification->user->first_name . ' ' . $clientNotification->user->last_name,
                'type' => $clientNotification->type,
                'message' => $clientNotification->message,
                'created_at' => $clientNotification->created_at,
                'created_at_jalali' => jdate($clientNotification->created_at)->format('Y-m-d H:i:s'),
            ];
        });

        return [
            'data' => $data->toArray(),
            'meta' => [
                'page' => $this->currentPage(),
                'pages' => $this->lastPage(),
                'perpage' => $this->perPage(),
                'rowIds' => $this->collection->pluck('id')->toArray()
            ]
        ];
    }
}
