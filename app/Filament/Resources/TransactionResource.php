<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('category_id')->relationship('category', 'name')->required(),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric(),
                    Forms\Components\Select::make('type')
                    ->required()
                    ->options([
                        'masuk' => 'Masuk',
                        'keluar' => 'Keluar',
                    ]),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\DatePicker::make('transaction_date')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('category.name')
                    ->sortable()->badge()->color('info'),
                    Tables\Columns\TextColumn::make('type')->label('Type')
                    ->numeric()
                    ->sortable()->badge()->color(fn (Transaction $record): string => $record->type === 'masuk' ? 'success' : 'danger'),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()->money('idr')
                    ->sortable(),
                Tables\Columns\TextColumn::make('transaction_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //filter by start date and end date
                Tables\Filters\Filter::make('transaction_date')
                    ->form([
                        Forms\Components\DatePicker::make('start')
                            ->label('Start Date')
                            ->required(),
                        Forms\Components\DatePicker::make('end')
                            ->label('End Date')
                            ->required(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (!empty($data['start']) && !empty($data['end'])) {
                            return $query
                                ->whereDate('transaction_date', '>=', $data['start'])
                                ->whereDate('transaction_date', '<=', $data['end']);
                        }

                        return $query;
                }),

                //filter by category
                Tables\Filters\Filter::make('category')
                    ->form([
                        Forms\Components\Select::make('category_id')
                            ->relationship('category', 'name')
                            ->required(),

                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (!empty($data['category_id'])) {
                            return $query
                                ->where('category_id', $data['category_id']);
                        }

                        return $query;
                    }),

                    //filter by type
                    Tables\Filters\Filter::make('type')
                        ->form([
                            Forms\Components\Select::make('type')
                                ->options([
                                    'masuk' => 'Masuk',
                                    'keluar' => 'Keluar',
                                ])
                                ->required(),
                        ])
                        ->query(function (Builder $query, array $data): Builder {
                            if (!empty($data['type'])) {
                                return $query
                                    ->where('type', $data['type']);
                            }

                            return $query;
                        }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
