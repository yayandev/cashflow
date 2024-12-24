<?php

namespace App\Filament\Resources\TransactionResource\Widgets;

use App\Models\Transaction;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TableTransactionToday extends BaseWidget
{

    protected static ?string $heading = 'Table Pengeluaran Dan Pemasukan Hari ini';
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Transaction::query()->whereDate('transaction_date', date('Y-m-d'))
                    ->orderBy('transaction_date', 'desc')
                    ->orderBy('id', 'desc')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('transaction_date'),
                Tables\Columns\TextColumn::make('amount')->money('idr'),
                    Tables\Columns\TextColumn::make('category.name')->label('Category')->badge()->color('info'),
                Tables\Columns\TextColumn::make('type')->label('Type')->badge()->color(
                    fn (Transaction $record): string => $record->type === 'masuk' ? 'success' : 'danger'
                ),
            ]);
    }
}
