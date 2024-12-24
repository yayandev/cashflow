<?php

namespace App\Filament\Imports;

use App\Models\Transaction;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Carbon\Carbon;

class TransactionImporter extends Importer
{
    protected static ?string $model = Transaction::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('category_id')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('amount')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('description'),
            ImportColumn::make('transaction_date')
                ->requiredMapping()
                ->rules(['required', 'date_format:m/d/Y']),
            ImportColumn::make('type')
                ->requiredMapping()
                ->rules(['required']),
        ];
    }

    public function resolveRecord(): ?Transaction
    {
        if (isset($this->data['transaction_date'])) {
            try {
                // Konversi tanggal dari format 'm/d/Y' ke 'Y-m-d'
                $this->data['transaction_date'] = Carbon::createFromFormat('m/d/Y', $this->data['transaction_date'])->format('Y-m-d');
            } catch (\Exception $e) {
                // Jika format salah, set nilai menjadi null atau tambahkan error ke kolom khusus
                $this->data['transaction_date'] = null;
                $this->data['transaction_error'] = 'Invalid date format.';
            }
        }

        return new Transaction();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your transaction import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
