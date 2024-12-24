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
                Transaction::query()
                    ->whereBetween('created_at', [
                        now()->startOfDay(),
                        now()->endOfDay()
                    ])
                    ->orderBy('created_at', 'desc')
                    ->orderBy('id', 'desc')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d M Y H:i')
                    ->label('Tanggal Transaksi'),

                Tables\Columns\TextColumn::make('amount')
                    ->money('idr')
                    ->label('Jumlah'),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (Transaction $record): string =>
                        $record->type === 'masuk' ? 'success' : 'danger'
                    ),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
