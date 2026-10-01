<?php

namespace App\Filament\Resources\AvailabilityChecks\Schemas;

use App\Models\AvailabilityCheck;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AvailabilityCheckInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Contact')
                    ->columns(3)
                    ->components([
                        TextEntry::make('name'),
                        TextEntry::make('email')
                            ->copyable()
                            ->placeholder('Not given'),
                        TextEntry::make('phone')
                            ->state(fn (AvailabilityCheck $record) => $record->formattedPhone())
                            ->copyable()
                            ->placeholder('Not given'),
                    ]),

                Section::make('Address')
                    ->columns(3)
                    ->components([
                        TextEntry::make('address')
                            ->label('Service address')
                            ->state(fn (AvailabilityCheck $record) => $record->fullAddress())
                            ->columnSpan(2),
                        TextEntry::make('coordinates')
                            ->label('Map location')
                            ->state(fn (AvailabilityCheck $record) => $record->latitude === null
                                ? null
                                : round($record->latitude, 5).', '.round($record->longitude, 5))
                            ->placeholder('Not located'),
                    ]),

                Section::make()
                    ->columns(2)
                    ->components([
                        TextEntry::make('created_at')
                            ->label('Received')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->label('Last updated')
                            ->dateTime(),
                    ]),
            ]);
    }
}
