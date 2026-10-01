<?php

namespace App\Filament\Resources\AvailabilityChecks\Schemas;

use App\Support\UsStates;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AvailabilityCheckForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Contact')
                    ->columns(2)
                    ->components([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        TextInput::make('email')
                            ->email()
                            ->maxLength(255),
                        // Stored as 10 digits (like the public form); shown and typed formatted.
                        TextInput::make('phone')
                            ->tel()
                            ->mask('(999) 999-9999')
                            ->placeholder('(555) 555-0123')
                            ->regex('/^\(\d{3}\) \d{3}-\d{4}$/')
                            ->formatStateUsing(fn (?string $state) => $state && strlen($state) === 10
                                ? sprintf('(%s) %s-%s', substr($state, 0, 3), substr($state, 3, 3), substr($state, 6))
                                : $state)
                            ->dehydrateStateUsing(fn (?string $state) => $state ? preg_replace('/\D/', '', $state) : null),
                    ]),

                // Changing the address re-geocodes it for the map automatically.
                Section::make('Address')
                    ->columns(4)
                    ->components([
                        TextInput::make('street')
                            ->label('Service address')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        TextInput::make('unit')
                            ->label('Apartment or unit number')
                            ->maxLength(255)
                            ->columnSpanFull(),
                        TextInput::make('city')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),
                        Select::make('state')
                            ->options(UsStates::abbreviations())
                            ->required(),
                        TextInput::make('postal_code')
                            ->label('ZIP code')
                            ->regex('/^\d{5}(-\d{4})?$/'),
                    ]),
            ]);
    }
}
