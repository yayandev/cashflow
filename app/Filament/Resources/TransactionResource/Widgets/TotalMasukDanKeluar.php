<?php

namespace App\Filament\Resources\TransactionResource\Widgets;

use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TotalMasukDanKeluar extends BaseWidget
{
    protected function getStats(): array
    {

        $totalUangSaatIni = Transaction::where('type', 'masuk')->sum('amount') - Transaction::where('type', 'keluar')->sum('amount');

        $totalUangMasuk = Transaction::where('type', 'masuk')->sum('amount');
        $totalUangMasukHariIni = Transaction::where('type', 'masuk')->whereDate('created_at', now())->sum('amount');
        $totalUangMasukBulanIni = Transaction::where('type', 'masuk')->whereMonth('created_at', now())->sum('amount');
        $totalUangMasukTahunIni = Transaction::where('type', 'masuk')->whereYear('created_at', now())->sum('amount');

        $totalUangKeluar = Transaction::where('type', 'keluar')->sum('amount');
        $totalUangKeluarHariIni = Transaction::where('type', 'keluar')->whereDate('created_at', now())->sum('amount');
        $totalUangKeluarBulanIni = Transaction::where('type', 'keluar')->whereMonth('created_at', now())->sum('amount');
        $totalUangKeluarTahunIni = Transaction::where('type', 'keluar')->whereYear('created_at', now())->sum('amount');
        return [
            //
            Stat::make('Total Uang Saat Ini', "Rp." . number_format($totalUangSaatIni, 0, ',', '.'))
                ->description('Total Uang Saat Ini')
                ->descriptionIcon('heroicon-s-currency-dollar')
                ->color('warning'),
            Stat::make('Total Uang Masuk', "Rp." . number_format($totalUangMasuk, 0, ',', '.'))
                ->description('Total Uang Masuk')
                ->descriptionIcon('heroicon-s-arrow-trending-up')
                ->color('success'),
            Stat::make('Total Uang Masuk Hari Ini', "Rp." . number_format($totalUangMasukHariIni, 0, ',', '.'))
                ->description('Total Uang Masuk Hari Ini')
                ->descriptionIcon('heroicon-s-arrow-trending-up')
                ->color('success'),
            Stat::make('Total Uang Masuk Bulan Ini', "Rp." . number_format($totalUangMasukBulanIni, 0, ',', '.'))
                ->description('Total Uang Masuk Bulan Ini')
                ->descriptionIcon('heroicon-s-arrow-trending-up')
                ->color('success'),
            Stat::make('Total Uang Masuk Tahun Ini', "Rp." . number_format($totalUangMasukTahunIni, 0, ',', '.'))
                ->description('Total Uang Masuk Tahun Ini')
                ->descriptionIcon('heroicon-s-arrow-trending-up')
                ->color('success'),
            Stat::make('Total Uang Keluar', "Rp." . number_format($totalUangKeluar, 0, ',', '.'))
                ->description('Total Uang Keluar')
                ->descriptionIcon('heroicon-s-arrow-trending-down')
                ->color('danger'),
            Stat::make('Total Uang Keluar Hari Ini', "Rp." . number_format($totalUangKeluarHariIni, 0, ',', '.'))
                ->description('Total Uang Keluar Hari Ini')
                ->descriptionIcon('heroicon-s-arrow-trending-down')
                ->color('danger'),
            Stat::make('Total Uang Keluar Bulan Ini', "Rp." . number_format($totalUangKeluarBulanIni, 0, ',', '.'))
                ->description('Total Uang Keluar Bulan Ini')
                ->descriptionIcon('heroicon-s-arrow-trending-down')
                ->color('danger'),
            Stat::make('Total Uang Keluar Tahun Ini', "Rp." . number_format($totalUangKeluarTahunIni, 0, ',', '.'))
                ->description('Total Uang Keluar Tahun Ini')
                ->descriptionIcon('heroicon-s-arrow-trending-down')
                ->color('danger'),
        ];
    }

}
