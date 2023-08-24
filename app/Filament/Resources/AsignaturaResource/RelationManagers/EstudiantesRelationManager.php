<?php

namespace App\Filament\Resources\AsignaturaResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class EstudiantesRelationManager extends RelationManager
{
    protected static string $relationship = 'estudiantes';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Estudiantes en asignatura :asignatura', ['asignatura' => $ownerRecord->nombre]);
    }

    protected static function getRecordLabel(): ?string
    {
        return __('Estudiante');
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Nombre')),
                Tables\Columns\TextColumn::make('pivot.aprobada')
                    ->label(__('Aprobada'))
                    ->alignCenter()
                    ->badge()
                    ->state(fn (Model $record) => $record->pivot->aprobada ? __('Sí') : __('No'))
                    ->color(fn (Model $record) => $record->pivot->aprobada ? 'success' : 'danger'),
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
            ->emptyStateDescription(__('No hay estudiantes en esta asignatura todavía'));
    }
}
