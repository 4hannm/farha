<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscribePackageResource\Pages;
use App\Filament\Resources\SubscribePackageResource\RelationManagers;
use App\Models\SubscribePackage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubscribePackageResource extends Resource
{
    protected static ?string $model = SubscribePackage::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //

                Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),

                Forms\Components\FileUpload::make('icon')
                ->required()
                ->image(),

                Forms\Components\TextInput::make('price')
                ->required()
                ->numeric()
                ->prefix('IDR'),

                Forms\Components\TextInput::make('duration')
                ->required()
                ->numeric()
                ->prefix('Days'),

                Forms\Components\Repeater::make('subscribeBenefit')
                ->relationship('subscribeBenefit')
                ->schema([
                    Forms\Components\TextInput::make('name')
                    ->required(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //

                Tables\Columns\TextColumn::make('name')
                ->searchable(),

                Tables\Columns\TextColumn::make('price')
                ->money('IDR', true),

                Tables\Columns\ImageColumn::make('icon'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListSubscribePackages::route('/'),
            'create' => Pages\CreateSubscribePackage::route('/create'),
            'edit' => Pages\EditSubscribePackage::route('/{record}/edit'),
        ];
    }
}
