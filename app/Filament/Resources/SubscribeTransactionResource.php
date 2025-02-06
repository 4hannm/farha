<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscribeTransactionResource\Pages;
use App\Filament\Resources\SubscribeTransactionResource\RelationManagers;
use App\Models\SubscribePackage;
use App\Models\SubscribeTransaction;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\FormsComponent;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubscribeTransactionResource extends Resource
{
    protected static ?string $model = SubscribeTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //

                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Product and Price')
                    ->schema([

                        Grid::make(2)
                        ->schema([
                            Forms\Components\Select::make('subscribe_package_id')
                            ->relationship('subscribePackage', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {

                                $subscribePackage = SubscribePackage::find($state);
                                $price = $subscribePackage ? $subscribePackage->price : 0;
                                $duration = $subscribePackage ? $subscribePackage->duration : 0;

                                $set('price', $price);
                                $set('duration', $duration);

                                $tax = 0.11;
                                $totalTaxAmount = $tax * $price;

                                $totalAmount = $price + $totalTaxAmount;
                                $set('total_amount', number_format($totalAmount, 0, '', ''));
                                $set('total_tax_amount', number_format($totalTaxAmount, 0, '', ''));
                            })

                            ->afterStateHydrated(function (callable $get, callable $set, $state) {
                                 
                                $subscribePackageId = $state;
                                if ($subscribePackageId) {
                                    $subscribePackage = SubscribePackage::find($subscribePackageId);
                                    $price = $subscribePackage ? $subscribePackage->price : 0;
                                    $set('price', $price);

                                    $tax = 0.11;
                                    $totalTaxAmount = $tax * $price;
                                    $set('total_tax_amount', number_format($totalTaxAmount, 0, '', ''));
                                }
                            }),

                            Forms\Components\TextInput::make('price')
                            ->required()
                            ->readOnly()
                            ->numeric()
                            ->prefix('IDR'),

                            Forms\Components\TextInput::make('total_amount')
                            ->required()
                            ->readOnly()
                            ->numeric()
                            ->prefix('IDR'),

                            Forms\Components\TextInput::make('total_tax_amount')
                            ->required()
                            ->readOnly()
                            ->numeric()
                            ->prefix('IDR'),

                            Forms\Components\DatePicker::make('started_at')
                            ->required(),

                            Forms\Components\DatePicker::make('ended_at')
                            ->required(),

                            Forms\Components\TextInput::make('duration')
                            ->readOnly()
                            ->required()
                            ->numeric()
                            ->prefix('Days'),
                        ]),
                    ]),

                    Forms\Components\Wizard\Step::make('Customer Information')
                    ->schema([

                        Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                            Forms\Components\TextInput::make('phone')
                            ->required()
                            ->maxLength(255),

                            Forms\Components\TextInput::make('email')
                            ->required()
                            ->maxLength(255)
                        ]),
                    ]),

                    Forms\Components\Wizard\Step::make('Payment Information')
                    ->schema([
                        Forms\Components\TextInput::make('booking_trx_id')
                        ->required()
                        ->maxLength(255),

                        ToggleButtons::make('is_paid')
                        ->label('Apakah sudah membayar ?')
                        ->boolean()
                        ->grouped()
                        ->icons([
                            true => 'heroicon-o-pencil',
                            false => 'heroicon-o-clock',
                        ])
                        ->required(),

                        Forms\Components\FileUpload::make('proof')
                        ->image()
                        ->required(),

                        ]),
                ])
                ->columnSpan('full')
                ->columns(1)
                ->skippable()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\ImageColumn::make('subscribePackage.icon'),

                Tables\Columns\TextColumn::make('name')
                ->searchable(),

                Tables\Columns\TextColumn::make('booking_trx_id')
                ->searchable(),

                Tables\Columns\IconColumn::make('is_paid')
                ->boolean()
                ->trueColor('success')
                ->falseColor('danger')
                ->trueIcon('heroicon-o-check-circle')
                ->falseIcon('heroicon-o-x-circle')
                ->label('Terverifikasi'),
            ])
            ->filters([
                //
                SelectFilter::make('subscribe_package_id')
                ->label('Subscribe Package')
                ->relationship('subscribePackage', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(), 
                Tables\Actions\ViewAction::make(),


                Tables\Actions\Action::make('approve')
                ->label('Approve')
                ->action(function(SubscribeTransaction $record) {
                    $record->is_paid = true;
                    $record->save();

                    // Trigger the custom notification

                    Notification::make()
                        ->title('Transaction Approved')
                        ->success()
                        ->body('The transaction has benn successfully approved.')
                        ->send();
                })
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn (SubscribeTransaction $record) => !$record->is_paid),
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
            'index' => Pages\ListSubscribeTransactions::route('/'),
            'create' => Pages\CreateSubscribeTransaction::route('/create'),
            'edit' => Pages\EditSubscribeTransaction::route('/{record}/edit'),
        ];
    }
}
