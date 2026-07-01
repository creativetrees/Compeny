<?php

namespace App\Filament\Resources\Testimonials\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class TestimonialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('Testimonial')->columnSpanFull()->persistTabInQueryString()->tabs([
                Tab::make('Detail')->icon('heroicon-o-user-circle')->columns(2)->schema([
                    Select::make('project_id')->label('Project')->relationship('project', 'title')
                        ->searchable()->preload()->prefixIcon('heroicon-m-rectangle-stack')
                        ->placeholder('Link to a project')
                        ->helperText('Optional — the project this testimonial relates to.'),
                    TextInput::make('author')->required()->prefixIcon('heroicon-m-user')
                        ->placeholder('Jane Doe')->helperText('Name of the person quoted.'),
                    TextInput::make('role')->prefixIcon('heroicon-m-briefcase')
                        ->placeholder('Head of Product')->helperText('Their job title.'),
                    TextInput::make('company')->prefixIcon('heroicon-m-building-office-2')
                        ->placeholder('Acme Inc.')->helperText('Their organisation.'),
                ]),
                Tab::make('Content')->icon('heroicon-o-chat-bubble-left-right')->columns(2)->schema([
                    RichEditor::make('quote')->required()->columnSpanFull()
                        ->placeholder('Working with the team was…')
                        ->helperText('The testimonial text shown on the site.'),
                    TextInput::make('rating')->numeric()->minValue(1)->maxValue(5)
                        ->prefixIcon('heroicon-m-star')->placeholder('5')->helperText('1–5 stars.'),
                ]),
                Tab::make('Settings')->icon('heroicon-o-cog-6-tooth')->columns(2)->schema([
                    FileUpload::make('avatar_path')->label('Avatar')
                        ->avatar()->imageEditor()->disk('public')->directory('site/testimonials')->visibility('public')
                        ->maxSize(2048)->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp', 'image/svg+xml'])
                        ->helperText('Author photo. PNG/JPG/WEBP, max 2 MB.'),
                    Toggle::make('is_featured')->label('Featured')->inline(false)
                        ->helperText('Show this testimonial in featured slots on the site.'),
                ]),
            ]),
        ]);
    }
}
