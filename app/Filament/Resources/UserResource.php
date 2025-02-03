<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Filament\Resources\UserResource\RelationManagers\OrdersRelationManager;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?int $navigationSort =1;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->label('Name')->required(),
                Forms\Components\TextInput::make('email')->label('Email Adress')->email()->maxlength(255)->unique(ignoreRecord:true)->required(),
                Forms\Components\DateTimePicker::make('email_verified_at')->label("Email verified at")->default(now()),
                Forms\Components\TextInput::make("password")->label("Password")->password()->dehydrated(fn($state)=>filled($state))->required(fn(Page $livewire):bool => $livewire instanceof CreateRecord),
            ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('email_verified_at')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make("created_at")->dateTime()->sortable()
                
            ])
            ->filters([
            ])
            ->actions([
                Tables\Actions\ViewAction::make(), // the view user action
                Tables\Actions\EditAction::make(), // the edit user action
                Tables\Actions\DeleteAction::make(), // the delete user action
                // we can replace them with action group 
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
            OrdersRelationManager::class,
        ];
    }
    public static function getGloballySearchableAttributes(): array
    {
       return ["name",'email'];
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
