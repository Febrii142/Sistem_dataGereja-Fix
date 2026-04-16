<?php

namespace App\Imports;

use App\Models\Member;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class MembersImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        $rows->skip(1)->each(function (Collection $row) {
            if (! $row->get(0)) {
                return;
            }

            Member::create([
                'nama' => (string) $row->get(0),
                'alamat' => (string) ($row->get(1) ?? '-'),
                'kontak' => (string) ($row->get(2) ?? '-'),
                'status' => in_array($row->get(3), ['aktif', 'tidak_aktif'], true) ? $row->get(3) : 'aktif',
                'tanggal_lahir' => now()->parse((string) ($row->get(4) ?? now()->toDateString()))->toDateString(),
                'jenis_kelamin' => $row->get(5) === 'P' ? 'P' : 'L',
                'pekerjaan' => (string) ($row->get(6) ?? ''),
            ]);
        });
    }
}
