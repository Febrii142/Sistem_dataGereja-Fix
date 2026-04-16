<?php

namespace App\Exports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CategoryMembersExport implements FromCollection, WithHeadings
{
    public function __construct(private readonly Category $category)
    {
    }

    public function collection()
    {
        return $this->category->members()
            ->orderBy('nama')
            ->get(['nama', 'kontak', 'status', 'jenis_kelamin', 'tanggal_lahir'])
            ->map(function ($member) {
                return [
                    'nama' => $member->nama,
                    'kontak' => $member->kontak,
                    'status' => $member->status,
                    'jenis_kelamin' => $member->jenis_kelamin,
                    'tanggal_lahir' => $member->tanggal_lahir,
                ];
            });
    }

    public function headings(): array
    {
        return ['Nama', 'Kontak', 'Status', 'Jenis Kelamin', 'Tanggal Lahir'];
    }
}
