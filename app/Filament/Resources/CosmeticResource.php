<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CosmeticResource\Pages;
use App\Filament\Resources\CosmeticResource\RelationManagers;
use App\Filament\Resources\CosmeticResource\RelationManagers\TestimonialsRelationManager;
use App\Models\Cosmetic;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CosmeticResource extends Resource
{
    protected static ?string $model = Cosmetic::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Information')
                        ->icon('heroicon-m-information-circle')
                        ->completedIcon('heroicon-m-check')
                        ->description('Information about the product.')
                        ->columns(2)
                        ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\FileUpload::make('thumbnail')
                            ->image()
                            ->required()
                            ->directory('cosmetics/thumbnails')
                            ->disk('public'),
                        Forms\Components\Select::make('brand_id')
                            ->required()
                            ->relationship('brand', 'name')
                            ->placeholder('Select a brand'),
                        Forms\Components\Select::make('category_id')
                            ->required()
                            ->relationship('category', 'name')
                            ->placeholder('Select a category'),
                        Forms\Components\TextInput::make('price')
                            ->prefix('IDR')
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('stock')
                            ->prefix('Qty')
                            ->required()
                            ->numeric(),
                        Forms\Components\Textarea::make('about')
                            ->required(),
                        Forms\Components\Toggle::make('is_popular')
                            ->label('Popular Product??')
                            ->onIcon('heroicon-m-star')
                            ->required()
                            ->offIcon('heroicon-m-cursor-arrow-rays')
                        ]),
                    Wizard\Step::make('Benefits')
                        ->icon('heroicon-m-arrow-trending-up')
                        ->completedIcon('heroicon-m-check')
                        ->description('Benefits of the product.')
                        ->schema([
                            Repeater::make('Benefits')
                                ->relationship('benefits')
                                ->columns(1)
                                ->schema([
                                Forms\Components\TextInput::make('name')
                                ->required()
                                ->maxLength(255),
                                ])
                                ->grid(2)
                                ->defaultItems(4),
                        ]),
                    Wizard\Step::make('Photos')
                        ->icon('heroicon-m-photo')
                        ->completedIcon('heroicon-m-check')
                        ->description('Photos of the product.')
                        ->schema([
                            Repeater::make('Photos')
                                ->relationship('photos')
                                ->columns(1)
                                ->schema([
                                Forms\Components\FileUpload::make('photo')
                                ->required()
                                ->image()
                                ->directory('cosmetics/photos')
                                ->disk('public'),
                                ])
                                ->grid(2)
                                ->defaultItems(3),
                            ]),
                    ])
                    ->columnSpan('full')
                    ->skippable()
                    ->columns(1),
                ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
            Tables\Columns\ImageColumn::make('thumbnail')
                ->disk('public'),
            Tables\Columns\TextColumn::make('name')
                ->sortable()
                ->searchable(),
            Tables\Columns\TextColumn::make('category.name')
                ->label('Category')
                ->sortable()
                ->searchable(),
            Tables\Columns\TextColumn::make('brand.name')
                ->label('Brand')
                ->sortable()
                ->searchable(),
            Tables\Columns\IconColumn::make('is_popular')
                ->boolean()
                ->trueColor('success')
                ->falseColor('danger')
                ->trueIcon('heroicon-o-check-circle')
                ->falseIcon('heroicon-o-x-circle')
                ->label('Popular Product ?')
                ->sortable(),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->relationship('category', 'name')
                    ->label('Category'),
                SelectFilter::make('brand_id')
                    ->relationship('brand', 'name')
                    ->label('Brand'),
            ])
            ->actions([
                ActionGroup::make([
                    ActionGroup::make([
                        Tables\Actions\ViewAction::make(),
                        Tables\Actions\EditAction::make(),
                    ])
                        ->dropdown(false),
                    Tables\Actions\DeleteAction::make(),
                ])
                    ->icon('heroicon-m-bars-3')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            TestimonialsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCosmetics::route('/'),
            'create' => Pages\CreateCosmetic::route('/create'),
            'edit' => Pages\EditCosmetic::route('/{record}/edit'),
        ];
    }
}
