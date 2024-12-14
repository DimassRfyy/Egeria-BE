<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingTransactionResource\Pages;
use App\Filament\Resources\BookingTransactionResource\RelationManagers;
use App\Models\BookingTransaction;
use App\Models\Cosmetic;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BookingTransactionResource extends Resource
{
    protected static ?string $model = BookingTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationGroup = 'Transactions';

    public static function getNavigationBadge(): ?string
    {
        return (string) BookingTransaction::where('is_paid', false)->count();
    }

    public static function updateTotals(Get $get, Set $set): void {
        $selectedCosmetics = collect($get('transactionDetails'))->filter(fn($item) => !empty($item['cosmetic_id']) && !empty($item['quantity']));

        $prices = Cosmetic::find($selectedCosmetics->pluck('cosmetic_id'))->pluck('price', 'id');
        
        $subTotal = $selectedCosmetics->reduce(function ($subTotal, $item) use ($prices) {
            return $subTotal + ($prices[$item['cosmetic_id']] * $item['quantity']);
        }, 0);

        $totalTaxAmount = round($subTotal * 0.11);
        $totalAmount = round($subTotal + $totalTaxAmount);
        $totalQuantity = $selectedCosmetics->sum('quantity');

        $set('total_amount', number_format($totalAmount, 0, ',', '.'));
        $set('total_tax_amount', number_format($totalTaxAmount, 0, ',', '.'));
        $set('sub_total_amount', number_format($subTotal, 0, ',', '.'));
        $set('quantity', $totalQuantity);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Product & Price')
                        ->icon('heroicon-m-shopping-bag')
                        ->completedIcon('heroicon-m-check')
                        ->description('Add your product and price.')
                        ->schema([
                            Grid::make(2)
                            ->schema([
                                Repeater::make('Transaction Details')
                                ->relationship('transactionDetails')
                                ->columns(1)
                                ->schema([
                                Forms\Components\Select::make('cosmetic_id')
                                ->relationship('cosmetic', 'name')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->label('Select Product')
                                ->live()
                                ->afterStateUpdated(function ($state, callable $set) {
                                    $cosmetic = Cosmetic::find($state);
                                    $set('price', $cosmetic ? $cosmetic->price : 0);
                                }),
                                Forms\Components\TextInput::make('price')
                                ->required()
                                ->prefix('IDR')
                                ->numeric()
                                ->readOnly()
                                ->label('Price')
                                ->hint('Price well be calculated automatically based on the product you choose.'),
                                Forms\Components\TextInput::make('quantity')
                                ->required()
                                ->integer()
                                ->default(1),
                                ])
                                ->live()
                                ->afterStateUpdated(function (Get $get, Set $set) {
                                    self::updateTotals($get, $set);
                                })
                                ->defaultItems(1)
                                ->columnSpanFull()
                                ->label('Choose Product'),
                            ]),
                            Grid::make(4)
                            ->schema([
                                Forms\Components\TextInput::make('quantity')
                                ->integer()
                                ->prefix('Qty')
                                ->label('Total Quantity')
                                ->readOnly()
                                ->default(1),
                                Forms\Components\TextInput::make('sub_total_amount')
                                ->prefix('IDR')
                                ->numeric()
                                ->readOnly()
                                ->label('Sub Total Amount'),
                                Forms\Components\TextInput::make('total_amount')
                                ->numeric()
                                ->prefix('IDR')
                                ->readOnly()
                                ->label('Total Amount'),
                                Forms\Components\TextInput::make('total_tax_amount')
                                ->numeric()
                                ->prefix('IDR')
                                ->readOnly()
                                ->label('Total Tax (11%)'),
                            ]),
                        ]),
                    Wizard\Step::make('Customer Information')
                        ->icon('heroicon-m-user')
                        ->completedIcon('heroicon-m-check')
                        ->description('Add your customer information.')
                        ->columns(2)
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('email')
                                ->required()
                                ->email()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('phone')
                                ->required()
                                ->numeric()
                                ->maxLength(15),
                            Forms\Components\Textarea::make('address')
                                ->required(),
                            Forms\Components\TextInput::make('city')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('post_code')
                                ->required()
                                ->numeric()
                                ->maxLength(5),
                        ]),
                    Wizard\Step::make('Payment Information')
                        ->icon('heroicon-m-credit-card')
                        ->completedIcon('heroicon-m-check')
                        ->description('Customer payment information.')
                        ->columns(3)
                        ->schema([
                            Forms\Components\TextInput::make('trx_id')
                            ->required()
                            ->maxLength(255),
                            Forms\Components\ToggleButtons::make('is_paid')
                            ->required()
                            ->grouped()
                            ->label('Apakah sudah membayar?')
                            ->boolean()
                            ->icons([
                                true => 'heroicon-o-pencil',
                                false => 'heroicon-o-clock',
                            ]),
                            Forms\Components\FileUpload::make('proof')
                            ->required()
                            ->image()
                            ->directory('proofs')
                            ->disk('public'),
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
                Tables\Columns\TextColumn::make('trx_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sub_total_amount')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_paid')
                    ->icon(function ($record) {
                        return $record->is_paid ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle';
                    })
                    ->label('Paid')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    ActionGroup::make([
                        Tables\Actions\ViewAction::make(),
                        Tables\Actions\EditAction::make(),
                    ])
                        ->dropdown(false),
                        Tables\Actions\Action::make('approve')
                        ->label('Approve')
                        ->action( function (BookingTransaction $record) {
                            $record->is_paid = true;
                            $record->save();
        
                            Notification::make()
                            ->title('Transaction Approve')
                            ->success()
                            ->body('Transaction has been approved')
                            ->send();
                        })
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn (BookingTransaction $record) => !$record->is_paid),
                        Tables\Actions\DeleteAction::make()
                        ->visible(fn (BookingTransaction $record) => $record->is_paid),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookingTransactions::route('/'),
            'create' => Pages\CreateBookingTransaction::route('/create'),
            'edit' => Pages\EditBookingTransaction::route('/{record}/edit'),
        ];
    }
}
