<?php

namespace App\Filament\Resources\AvailabilityChecks\Tables;

use App\Models\AvailabilityCheck;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AvailabilityChecksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('address')
                    ->label('Service address')
                    ->state(fn (AvailabilityCheck $record) => $record->fullAddress())
                    ->searchable(['street', 'unit', 'city', 'state', 'postal_code']),
                TextColumn::make('email')
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('phone')
                    ->state(fn (AvailabilityCheck $record) => $record->formattedPhone())
                    ->searchable()
                    ->placeholder('—'),
                IconColumn::make('located')
                    ->label('On map')
                    ->state(fn (AvailabilityCheck $record) => $record->latitude !== null)
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Received')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('located')
                    ->label('On map')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('latitude'),
                        false: fn (Builder $query) => $query->whereNull('latitude'),
                    ),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
