<?php

namespace App\Utilities;

use App\Enums\WorkOrderStatusEnum;
use Carbon\Carbon;

class Helpers
{
    public static function formatDate($date, $full = false)
    {
        if ($date) {
            $dt = Carbon::parse($date);
            return $full ? $dt->format('d-m-Y H:i') : $dt->format('d-m-Y');
        } else {
            return '-';
        }
    }

    public static function responseCms(bool $status, string $message, mixed $data = null)
    {
        return [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
    }
}
