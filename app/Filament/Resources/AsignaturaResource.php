<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AsignaturaResource\Pages;
use App\Filament\Resources\AsignaturaResource\RelationManagers;
use App\Models\Asignatura;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class AsignaturaResource extends Resource
{
    protected static ?string $model = Asignatura::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $navigationSort = 30;

    public static function getLabel(): ?string
    {
        return __('Asignatura');
    }

    public static function getNavigationLabel(): string
    {
        return __('Asignaturas');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                // Aquí comienza el Wizard. Recibe un array y cada item son los pasos.
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make(__('Datos de la Asignatura'))
                        ->schema([
                            Forms\Components\FileUpload::make('imagen')
                                ->label(__('Imagen de la Asignatura'))
                                ->image()
                                ->required()
                                ->directory('asignaturas')
                                ->columnSpanFull(),
                            Forms\Components\Grid::make(3)
                                ->schema([
                                    Select::make('user_id')
                                        ->label(__('Profesor'))
                                        ->required()
                                        ->options(
                                            User::profesores()
                                                ->activo()
                                                ->get()
                                                ->pluck('name', 'id'),
                                        ),
                                    TextInput::make('nombre')
                                        ->label(__('Nombre'))
                                        ->autofocus()
                                        ->required()
                                        ->minLength(6)
                                        ->maxLength(200)
                                        ->unique(static::getModel(), 'nombre', ignoreRecord: true)
                                        ->live(debounce: 500)
                                        ->afterStateUpdated(function (Set $set, ?string $old, ?string $state) {
                                            $set('slug', Str::slug($state));
                                        }),
                                    TextInput::make('slug'),
                                ]),
                            Forms\Components\RichEditor::make('descripcion')
                                ->toolbarButtons([
                                    'attachFiles',
                                    'blockquote',
                                    'bold',
                                    'bulletList',
                                    'codeBlock',
                                    'h2',
                                    'h3',
                                    'italic',
                                    'link',
                                    'orderedList',
                                    'redo',
                                    'strike',
                                    'undo',
                                ])
                                ->label(__('Descripción'))
                                ->required()
                                ->minLength(10)
                                ->maxLength(2000)
                                ->columnSpanFull(),
                        ]),
                    Forms\Components\Wizard\Step::make(__('Configuración'))
                        ->schema([
                            Forms\Components\Checkbox::make('publicada')
                                ->label(__('Publicada')),
                            Forms\Components\Checkbox::make('destacada')
                                ->label(__('Destacada')),
                        ]),
                    Forms\Components\Wizard\Step::make(__('Unidades'))
                        ->schema([
                            Forms\Components\Repeater::make('unidades')
                                ->relationship()
                                ->label(__('Unidades'))
                                ->addActionLabel(__('Añadir unidad'))
                                ->itemLabel(fn (array $state): ?string => $state['nombre'] ?? null)
                                ->reorderableWithButtons()
                                ->collapsible()
                                ->cloneable()
                                ->orderColumn()
                                ->schema([
                                    Forms\Components\Grid::make()
                                        ->schema([
                                            TextInput::make('nombre')
                                                ->label(__('Nombre'))
                                                ->autofocus()
                                                ->required()
                                                ->minLength(6)
                                                ->maxLength(200)
                                                ->unique(static::getModel(), 'nombre', ignoreRecord: true)
                                                ->live(debounce: 500)
                                                ->afterStateUpdated(function (Set $set, ?string $old, ?string $state) {
                                                    $set('slug', Str::slug($state));
                                                }),
                                            TextInput::make('slug'),
                                        ]),
                                    Forms\Components\RichEditor::make('descripcion')
                                        ->toolbarButtons([
                                            'attachFiles',
                                            'blockquote',
                                            'bold',
                                            'bulletList',
                                            'codeBlock',
                                            'h2',
                                            'h3',
                                            'italic',
                                            'link',
                                            'orderedList',
                                            'redo',
                                            'strike',
                                            'undo',
                                        ])
                                        ->label(__('Contenido de la unidad'))
                                        ->required()
                                        ->maxLength(2000)
                                        ->columnSpanFull(),
                                    Forms\Components\Checkbox::make('publicada')
                                        ->label(__('Publicada')),
                                    Forms\Components\Checkbox::make('electiva')
                                        ->label(__('Electiva')),
                                ])
                        ])
                ])
                    ->columnSpanFull()
                    // Cuando estemos en un paso que no sea el principal, se puede refrescar sin perder información.
                    ->persistStepInQueryString('asignatura-wizard-step'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort')
            ->columns([
                Tables\Columns\ImageColumn::make('imagen')
                    ->label(__('Imagen')),
                Tables\Columns\TextColumn::make('nombre')
                    ->label(__('Nombre'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('profesor.name')
                    ->label(__('Profesor'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('publicada')
                    ->label(__('Publicada')),
                Tables\Columns\ToggleColumn::make('electiva')
                    ->label(__('Electiva')),
                Tables\Columns\TextColumn::make('unidades_count')
                    ->label(__('Unidades'))
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('success')
                    ->counts('unidades'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Creado'))
                    ->sortable()
                    ->date('d/m/Y H:i'),
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
            ->emptyStateDescription(__('No hay asignaturas disponibles'));
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\EstudiantesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAsignaturas::route('/'),
            'create' => Pages\CreateAsignatura::route('/create'),
            'edit' => Pages\EditAsignatura::route('/{record}/edit'),
        ];
    }
}
