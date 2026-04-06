<?php

namespace App\Enums;

enum VehicleWorkStatus: string
{
    case InProgress = 'in_progress';
    case Waiting = 'waiting';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::InProgress => 'En proceso',
            self::Waiting => 'En espera',
            self::Completed => 'Terminado',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $status): array => [$status->value => $status->label()])
            ->all();
    }
}
