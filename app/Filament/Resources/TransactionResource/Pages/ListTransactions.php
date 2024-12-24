<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Exports\TransactionExporter;
use App\Filament\Imports\TransactionImporter;
use App\Filament\Resources\TransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\ExportAction::make()->exporter(TransactionExporter::class)->label('Export Excel'),
            Actions\ImportAction::make()->importer(TransactionImporter::class)->label('Import Excel'),
        ];
    }
}
