<?php

namespace App\Filament\Resources\AvailabilityChecks;

use App\Filament\Resources\AvailabilityChecks\Pages\CreateAvailabilityCheck;
use App\Filament\Resources\AvailabilityChecks\Pages\EditAvailabilityCheck;
use App\Filament\Resources\AvailabilityChecks\Pages\ListAvailabilityChecks;
use App\Filament\Resources\AvailabilityChecks\Pages\ViewAvailabilityCheck;
use App\Filament\Resources\AvailabilityChecks\Schemas\AvailabilityCheckForm;
use App\Filament\Resources\AvailabilityChecks\Schemas\AvailabilityCheckInfolist;
use App\Filament\Resources\AvailabilityChecks\Tables\AvailabilityChecksTable;
use App\Models\AvailabilityCheck;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AvailabilityCheckResource extends Resource
{
    protected static ?string $model = AvailabilityCheck::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return AvailabilityCheckForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AvailabilityCheckInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AvailabilityChecksTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAvailabilityChecks::route('/'),
            'create' => CreateAvailabilityCheck::route('/create'),
            'view' => ViewAvailabilityCheck::route('/{record}'),
            'edit' => EditAvailabilityCheck::route('/{record}/edit'),
        ];
    }
}
