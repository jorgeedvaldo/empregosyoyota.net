<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DraftResource\Pages;
use App\Filament\Resources\DraftResource\RelationManagers;
use App\Models\Draft;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DraftResource extends Resource
{
    protected static ?string $model = Draft::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(1)
                    ->schema([
                        Forms\Components\TextInput::make('title')->required(),
                        Forms\Components\TextInput::make('company')->required(),
                        Forms\Components\TextInput::make('province')->required(),
                        Forms\Components\RichEditor::make('description')->required(),
                        Forms\Components\TextInput::make('email_or_link')->required(),
                        Forms\Components\FileUpload::make('photo')
                            ->image()
                            ->default('images/jobs/ZZdGXaDjQ7piXh6eG2AQ34zhI2zkrA-metaeXJuTkc4VGl4SjI0Wk5CS2xjVURxSjZXNDJQZkJWLW1ldGFPRGxmUTBsTlFVNUhUMHhCWHpBeFFsOTJiR1IyZG1adE0yZzFMbXB3Wnc9PS0uanBn-.jpg')
                            ->directory('images/jobs'),

                        Forms\Components\Select::make('country')
                            ->relationship('country','name'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('company'),
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
            'index' => Pages\ListDrafts::route('/'),
            'create' => Pages\CreateDraft::route('/create'),
            'edit' => Pages\EditDraft::route('/{record}/edit'),
        ];
    }
}
