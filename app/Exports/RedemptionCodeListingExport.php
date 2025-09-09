<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;

class RedemptionCodeListingExport implements FromCollection, WithHeadings
{
    protected $codes;

    public function __construct($codes)
    {
        $this->codes = $codes;
    }

    public function collection()
    {
        $records = $this->codes->with('user:id,email')
            ->select([
                'id',
                'user_id',
                'created_at',
                'acc_name',
                'meta_login',
                'product_name',
                'expired_date',
                'status',
                'serial_number',
            ])
            ->orderBy('id')
            ->get();
    
        $exportData = [];

        foreach ($records as $record) {
            $exportData[] = [
                'Date Created'   => $record->created_at ? Carbon::parse($record->created_at)->format('Y-m-d H:i:s') : '',
                'Name'           => $record->acc_name ?? '',
                'Email'          => $record->user->email ?? '',
                'Meta Login'     => $record->meta_login ?? '',
                'Product'        => $record->product_name ?? '',
                'Product Version'=> $record->version ?? '',
                'Expired Date'   => $record->expired_date ? Carbon::parse($record->expired_date)->format('Y-m-d') : '',
                'Status'         => trans('public.' . $record->status),
                'Serial Number'  => $record->serial_number ?? '',
            ];
        }

        return new Collection($exportData);
    }

    public function headings(): array
    {
        return [
            'Date Created',
            'Name',
            'Email',
            'Meta Login',
            'Product',
            'Product Version',
            'Expired Date',
            'Status',
            'Serial Number',
        ];
    }
}
