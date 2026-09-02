<?php

namespace App\Filament\Resources\Experiences\Schemas;

use App\Enums\ContentStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ExperienceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                TextInput::make('company')
                    ->required(),
                TextInput::make('company_url')
                    ->url(),
                TextInput::make('location'),
                TextInput::make('employment_type'),
                DatePicker::make('started_on')
                    ->required(),
                DatePicker::make('ended_on'),
                Textarea::make('summary')
                    ->columnSpanFull(),
                TextInput::make('highlights'),
                TextInput::make('tech'),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
                Select::make('status')
                    ->options(ContentStatus::class)
                    ->default('draft')
                    ->required(),
            ]);
    }
}
