<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JobResource\Pages;
use App\Filament\Resources\JobResource\RelationManagers;
use App\Models\Job;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class JobResource extends Resource
{
    protected static ?string $model = Job::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    
    protected static ?string $navigationLabel = 'Jobs';

    protected static ?string $navigationGroup = 'JOBS';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(1)
                    ->schema([
                        Forms\Components\TextInput::make('title')->required(),
                        Forms\Components\TextInput::make('company')->required(),
                        Forms\Components\TextInput::make('province')->required(),
                        Forms\Components\RichEditor::make('description')
							->required()
							->default('<p><br></p><h2>-------------</h2><h2>Empregos Yoyota - Aqui você encontra o seu emprego ideal.</h2><p>Encontre aqui as melhores vagas de emprego para 2025, oportunidades de recrutamento em Angola disponíveis no nosso portal para candidaturas. Também informamos sobre concurso público para 2025 e muito mais.<br /><strong>Tags:</strong>&nbsp;Empregos Yoyota, Empregos Yoyota 2025, emprego em Angola, Emprego em Angola 2025, Emprego em Luanda, Recrutamento 2025, Recrutamento em Angola, angoemprego, ango emprego, procure aqui, angovagas, angorecruta, Vagas de Emprego, vagas de emprego 2025</p><h2>Não recrutamos ninguém, a nossa missão é informar as vagas de emprego publicadas no Jornal de Angola e de outras fontes credíveis.</h2>'),
                        Forms\Components\TextInput::make('email_or_link')->required(),
                        Forms\Components\FileUpload::make('photo')
                            ->directory('images/jobs')
                            ->image()
                            ->required()
							->default('images/jobs/default.jpg'), 

                        Forms\Components\MultiSelect::make('categories')
                            ->relationship('categories','name'),
                    ])  
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('country.name')->sortable(),
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
            'index' => Pages\ListJobs::route('/'),
            'create' => Pages\CreateJob::route('/create'),
            'edit' => Pages\EditJob::route('/{record}/edit'),
        ];
    }    
}
