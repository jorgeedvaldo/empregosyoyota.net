<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CurriculoResource\Pages;
use App\Filament\Resources\CurriculoResource\RelationManagers;
use App\Models\Curriculo;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CurriculoResource extends Resource
{
    protected static ?string $model = Curriculo::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(1)
                    ->schema([
                        Forms\Components\TextInput::make('title')->required(),
                        Forms\Components\TextInput::make('author')->required(),
                        Forms\Components\RichEditor::make('description')->required(),
                        Forms\Components\TextInput::make('link')->required(),
                        Forms\Components\FileUpload::make('photo')
                            ->default('images/curriculos/default.jpg')
                            ->directory('images/curriculos')
                            ->image(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('title'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListCurriculos::route('/'),
            'create' => Pages\CreateCurriculo::route('/create'),
            'edit' => Pages\EditCurriculo::route('/{record}/edit'),
        ];
    }    
}
