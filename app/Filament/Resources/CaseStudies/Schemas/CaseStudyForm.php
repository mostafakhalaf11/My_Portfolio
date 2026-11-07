<?php

namespace App\Filament\Resources\CaseStudies\Schemas;

use App\Enums\ContentStatus;
use App\Enums\ProjectStage;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CaseStudyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Case study')
                    ->columnSpanFull()
                    ->tabs([
                        Tabs\Tab::make('Overview')
                            ->schema([
                                TextInput::make('title')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (string $operation, $state, callable $set) => $operation === 'create'
                                        ? $set('slug', Str::slug((string) $state))
                                        : null),

                                TextInput::make('slug')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->helperText('URL segment. Changing it breaks existing links.'),

                                TextInput::make('subtitle')
                                    ->columnSpanFull(),

                                TextInput::make('client_label')
                                    ->label('Client (anonymised)')
                                    ->placeholder('50+ employee logistics client')
                                    ->helperText('Never a real client name — keep employer work unattributable.'),

                                TextInput::make('role')
                                    ->placeholder('Lead engineer'),

                                TextInput::make('year_from')->numeric()->minValue(2015)->maxValue(2100),
                                TextInput::make('year_to')->numeric()->minValue(2015)->maxValue(2100),

                                Textarea::make('summary')
                                    ->required()
                                    ->rows(3)
                                    ->maxLength(320)
                                    ->helperText('Shown on the card and used as the meta description.')
                                    ->columnSpanFull(),

                                TagsInput::make('tech')
                                    ->placeholder('Laravel 13')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),

                        Tabs\Tab::make('Narrative')
                            ->schema([
                                Textarea::make('context')
                                    ->rows(4)
                                    ->helperText('What existed before, and why it needed to change.')
                                    ->columnSpanFull(),

                                Textarea::make('constraints')
                                    ->rows(4)
                                    ->helperText('What limited the solution space: data volume, uptime, team size, deadlines.')
                                    ->columnSpanFull(),

                                Repeater::make('options_rejected')
                                    ->label('Options rejected')
                                    ->helperText('The section that separates senior from mid. Show what you did not build.')
                                    ->schema([
                                        TextInput::make('option')->required(),
                                        Textarea::make('why_rejected')->required()->rows(2),
                                    ])
                                    ->itemLabel(fn (array $state): ?string => $state['option'] ?? null)
                                    ->collapsed()
                                    ->defaultItems(0)
                                    ->columnSpanFull(),

                                Textarea::make('decision')
                                    ->rows(4)
                                    ->helperText('What you chose, and the reasoning that made it the right call.')
                                    ->columnSpanFull(),

                                Textarea::make('implementation')
                                    ->rows(5)
                                    ->helperText('How it was built and rolled out.')
                                    ->columnSpanFull(),

                                Repeater::make('outcomes')
                                    ->label('Measured outcomes')
                                    ->schema([
                                        TextInput::make('metric')->required()->placeholder('Legacy tables migrated'),
                                        TextInput::make('value')->required()->placeholder('Zero'),
                                        TextInput::make('note'),
                                    ])
                                    ->itemLabel(fn (array $state): ?string => $state['metric'] ?? null)
                                    ->collapsed()
                                    ->defaultItems(0)
                                    ->columnSpanFull(),
                            ]),

                        Tabs\Tab::make('Diagram & links')
                            ->schema([
                                Repeater::make('architecture_diagram.nodes')
                                    ->label('Diagram nodes')
                                    ->helperText('Rendered as an inline SVG on the case study page.')
                                    ->schema([
                                        TextInput::make('id')->required()->placeholder('tenancy'),
                                        TextInput::make('label')->required()->placeholder('Tenancy resolution'),
                                        TextInput::make('group')->placeholder('middleware'),
                                    ])
                                    ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                                    ->collapsed()
                                    ->defaultItems(0)
                                    ->columnSpanFull(),

                                Repeater::make('architecture_diagram.edges')
                                    ->label('Diagram edges')
                                    ->schema([
                                        TextInput::make('from')->required(),
                                        TextInput::make('to')->required(),
                                        TextInput::make('label'),
                                    ])
                                    ->itemLabel(fn (array $state): string => trim(($state['from'] ?? '').' -> '.($state['to'] ?? '')))
                                    ->collapsed()
                                    ->defaultItems(0)
                                    ->columnSpanFull(),

                                TextInput::make('repo_url')
                                    ->url()
                                    ->prefixIcon('heroicon-m-code-bracket')
                                    ->helperText('Public repositories only — a private repo is a 404 for visitors.'),
                                TextInput::make('live_url')->url()->prefixIcon('heroicon-m-globe-alt'),
                            ])
                            ->columns(2),

                        Tabs\Tab::make('Status & roadmap')
                            ->schema([
                                Select::make('stage')
                                    ->options(ProjectStage::class)
                                    ->helperText('Badge on the card and page. Leave empty to hide it.'),

                                Repeater::make('roadmap')
                                    ->label("What's next")
                                    ->helperText('Upcoming work, one line each. Shown on the project page.')
                                    ->simple(TextInput::make('item')->required()->maxLength(200))
                                    ->defaultItems(0)
                                    ->columnSpanFull(),
                            ]),
                    ]),

                Section::make('Publishing')
                    ->columns(4)
                    ->columnSpanFull()
                    ->schema([
                        Select::make('status')
                            ->options(ContentStatus::class)
                            ->default(ContentStatus::Draft)
                            ->required(),

                        DateTimePicker::make('published_at')
                            ->helperText('Leave empty to publish immediately.'),

                        Toggle::make('is_featured')
                            ->helperText('Pinned to the top of the homepage.'),

                        TextInput::make('sort_order')
                            ->numeric()
                            ->default(0)
                            ->required(),
                    ]),
            ]);
    }
}
