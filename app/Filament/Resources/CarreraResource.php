<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CarreraResource\Pages;
use App\Filament\Resources\CarreraResource\RelationManagers\UsersRelationManager;
use App\Models\Carrera;
use Faker\Provider\Text;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CarreraResource extends Resource
{
    protected static ?string $model = Carrera::class;

    protected static ?int $navigationSort = 20;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function getLabel(): ?string
    {
        return __('Carrera');
    }

    public static function getNavigationLabel(): string
    {
        return __('Carreras');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nombre')
                    ->label(__('Nombre'))
                    ->autofocus()
                    ->required()
                    ->unique(static::getModel(), 'nombre', ignoreRecord: true)
                    ->live(debounce: 500)
                    ->afterStateUpdated(function (Set $set, ?string $old, ?string $state) {
                        $set('slug', Str::slug($state));
                    }),
                TextInput::make('slug'),
                TextInput::make('descripcion')
                    ->required()
                    ->maxLength(200)
                    ->label(__('Descripción')),
                TextInput::make('arancel')
                    ->label(__('Arancel'))
                    ->required()
                    ->maxLength(100)
                    ->suffix('UF'),
                Checkbox::make('activo')
                    ->label(__('Activo')),
                Checkbox::make('destacada')
                    ->label(__('Destacado')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('orden')
            ->defaultSort('orden')
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable()
                    ->sortable()
                    ->label(__('Nombre'))
                    ->description(fn (Carrera $carrera) => $carrera->descripcion),
                Tables\Columns\TextColumn::make('slug')
                    ->label(__('Slug')),
                Tables\Columns\TextColumn::make('arancel')
                    ->sortable()
                    //->money('clp')
                    ->label(__('Arancel')),
                Tables\Columns\ToggleColumn::make('activo')
                    ->label(__('Activo')),
                Tables\Columns\ToggleColumn::make('destacada')
                    ->label(__('Destacado')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->emptyStateDescription(__('No hay carreras disponibles.'));
    }

    public static function getRelations(): array
    {
        return [
            UsersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCarreras::route('/'),
            'create' => Pages\CreateCarrera::route('/create'),
            'edit' => Pages\EditCarrera::route('/{record}/edit'),
        ];
    }
}
