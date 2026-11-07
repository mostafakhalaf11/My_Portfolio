<?php

namespace App\Filament\Resources\PageSections\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;

class PageSectionForm
{
    /** The blocks Home.vue renders. A row with any other key would never appear. */
    public const KEYS = [
        'hero' => 'Hero',
        'case_studies' => 'Featured projects',
        'stack' => 'Technical stack',
        'experience' => 'Experience',
        'contact' => 'Contact',
    ];

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('page')
                    ->required()
                    ->default('home'),

                Select::make('key')
                    ->label('Block')
                    ->options(self::KEYS)
                    ->required()
                    ->live()
                    ->disabledOn('edit')
                    ->unique(ignoreRecord: true, modifyRuleUsing: fn (Unique $rule, Get $get) => $rule->where('page', $get('page')))
                    ->helperText('Which homepage block this row controls. Fixed once created.'),

                TextInput::make('heading')
                    ->maxLength(255),

                TextInput::make('subheading')
                    ->maxLength(255),

                Toggle::make('is_visible')
                    ->required(),

                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),

                // `settings` is a JSON column: each key the template reads gets a typed field.
                // Blocks without a settings section never send `settings`, so their stored JSON is kept.
                Section::make('Hero settings')
                    ->visible(fn (Get $get): bool => $get('key') === 'hero')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Toggle::make('settings.show_availability')
                            ->label('Show the "open to roles" badge'),

                        TextInput::make('settings.proof')
                            ->label('Highlights line')
                            ->maxLength(160)
                            ->helperText('Short capabilities line under the headline, e.g. "Multi-tenant architecture · Business-rule engines". No employer figures. Leave empty to hide it.')
                            ->columnSpanFull(),

                        TextInput::make('settings.now')
                            ->label('"Now" line')
                            ->maxLength(200)
                            ->helperText('One line on what you are working on right now, shown in About. Leave empty to hide it.')
                            ->columnSpanFull(),
                    ]),

                Section::make('Stack settings')
                    ->visible(fn (Get $get): bool => $get('key') === 'stack')
                    ->columnSpanFull()
                    ->schema([
                        Toggle::make('settings.primary_only')
                            ->label('Show primary skills only')
                            ->helperText('Hides the long tail so the key skills stand out.'),
                    ]),
            ]);
    }
}
