<?php

namespace App\Exports;

use App\Models\Member;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MembersExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Member::query()
            ->select(['nama', 'alamat', 'kontak', 'status', 'tanggal_lahir', 'jenis_kelamin', 'pekerjaan'])
            ->orderBy('nama')
            ->get();
    }

    public function headings(): array
    {
        return ['Nama', 'Alamat', 'Kontak', 'Status', 'Tanggal Lahir', 'Jenis Kelamin', 'Pekerjaan'];
    }
}
