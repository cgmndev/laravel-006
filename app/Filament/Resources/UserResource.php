<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers\CarrerasRelationManager;
use App\Models\Rol;
use App\Models\User;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function getLabel(): ?string
    {
        return __('Usuario');
    }

    public static function getNavigationLabel(): string
    {
        return __('Usuarios');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('avatar')
                    ->image()
                    ->disk('public')
                    ->directory('avatars')
                    ->label(__('Avatar'))
                    // Utilizar todo el ancho de la columna
                    ->columnSpanFull(),

                // Utilizar 3 columnas
                Grid::make(3)
                    ->schema([
                        Select::make('rol_id')
                            ->relationship('rol', 'descripcion')
                            ->required()
                            ->label(__('Rol')),
                        TextInput::make('name')
                            ->autofocus()
                            ->required()
                            ->maxLength(200)
                            ->label(__('Nombre')),
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(200)
                            // Validar que el email sea único, ignorando el registro actual
                            ->unique(static::getModel(), 'email', ignoreRecord: true)
                            ->label(__('Correo electrónico')),
                    ]),
                TextInput::make('password')
                    // Ocultar la contraseña
                    ->password()
                    // Encriptar la contraseña
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    // Si estamos en modo edición y solo modificamos el nombre, la contraseña no se modifica.
                    ->dehydrated(fn ($state) => filled($state))
                    // Es requerido solo en create.
                    ->required(fn (string $context): bool => $context === 'create')
                    ->confirmed()
                    ->minLength(8)
                    ->maxLength(200)
                    ->label(__('Contraseña')),
                TextInput::make('password_confirmation')
                    ->password()
                    ->label(__('Confirmar contraseña')),
                Checkbox::make('activo')
                    ->label(__('Activo')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')
                    ->label(__('Avatar')),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Nombre'))
                    ->sortable()
                    ->searchable()
                    ->description(fn (User $user) => $user->email),
                Tables\Columns\TextColumn::make('rol_id')
                    ->label(__('Rol'))
                    ->sortable()
                    ->badge()
                    ->state(fn (User $user) => $user->rol->descripcion)
                    ->color(fn (User $user) => match ($user->rol_id) {
                        Rol::ADMIN => 'danger',
                        Rol::PROFESOR => 'warning',
                        Rol::ESTUDIANTE => 'success'
                    }),
                Tables\Columns\ToggleColumn::make('activo')
                    ->label(__('Activo'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Creado'))
                    ->sortable()
                    ->date('d/m/Y H:i'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('rol_id')
                    ->label(__('Rol'))
                    ->options(Rol::pluck('descripcion', 'id')->toArray()),
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
            ]);
    }

    public static function getRelations(): array
    {
        return [
            CarrerasRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
