<?php

namespace App\Filament\Resources\PageSections\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PageSectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('page')
                    ->required()
                    ->default('home'),
                TextInput::make('key')
                    ->required(),
                TextInput::make('heading'),
                TextInput::make('subheading'),
                TextInput::make('settings'),
                Toggle::make('is_visible')
                    ->required(),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
