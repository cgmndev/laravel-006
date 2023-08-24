<?php

namespace App\Filament\Resources\CarreraResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Usuarios de la carrera :carrera', ['carrera' => $ownerRecord->nombre]);
    }

    protected static function getRecordLabel(): ?string
    {
        return __('Usuario');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Nombre')),
                Tables\Columns\ToggleColumn::make('pivot.activo')
                    ->label(__('Activo'))
                    ->updateStateUsing(function ($record, $state) {
                        $record->pivot->activo = $state;
                        $record->pivot->save();
                    })
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make(),
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\AttachAction::make(),
            ])
            ->emptyStateDescription(__('No hay usuarios para esta carrera.'));
    }
}
