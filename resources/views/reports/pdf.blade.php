<h2>Laporan Analytics Jemaat</h2>
<h4>Demografi</h4>
<ul>
    @foreach($demografi as $item)
        <li>{{ $item->jenis_kelamin }}: {{ $item->total }}</li>
    @endforeach
</ul>
<h4>Pertumbuhan</h4>
<ul>
    @foreach($pertumbuhan as $item)
        <li>{{ $item->bulan }}: {{ $item->total }}</li>
    @endforeach
</ul>
<h4>Kehadiran</h4>
<table width="100%" border="1" cellspacing="0" cellpadding="6">
    <thead><tr><th>Tanggal</th><th>Nama</th><th>Status</th></tr></thead>
    <tbody>
    @foreach($kehadiran as $item)
        <tr><td>{{ $item->service_date->format('Y-m-d') }}</td><td>{{ $item->member->nama ?? '-' }}</td><td>{{ $item->hadir ? 'Hadir' : 'Tidak Hadir' }}</td></tr>
    @endforeach
    </tbody>
</table>
