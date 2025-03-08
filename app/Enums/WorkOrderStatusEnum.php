<?php

namespace App\Enums;

use Illuminate\Support\Collection;
use Illuminate\Validation\Rules\Enum;

enum WorkOrderStatusEnum: string
{
    case PENDING = 'pending';

    case IN_PROGRESS = 'in_progress';

    case COMPLETED = 'completed';

    case CANCELED = 'canceled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::IN_PROGRESS => 'In Progress',
            self::COMPLETED => 'Completed',
            self::CANCELED => 'Canceled',
        };
    }

    public static function options(): array
    {
        return array_map(fn($status) => [
            'value' => $status->value,
            'label' => $status->label(),
        ], self::cases());
    }

    public static function except(array $status): Collection
    {
        return collect(self::options())->whereNotIn('value', $status);
    }
}
