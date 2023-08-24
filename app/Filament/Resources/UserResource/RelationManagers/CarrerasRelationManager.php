<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class CarrerasRelationManager extends RelationManager
{
    protected static string $relationship = 'carreras';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Carreras del usuario :user', ['user' => $ownerRecord->name]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nombre')
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->label(__('Nombre')),
                Tables\Columns\ToggleColumn::make('pivot.activo')
                    ->label(__('Activo'))
                    ->updateStateUsing(function ($record, $state) {
                        $record->pivot->active = $state;
                        $record->pivot->save();
                    }),
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
            ->emptyStateDescription(__('Este usuario no tiene carreras actualmente'));
    }
}
