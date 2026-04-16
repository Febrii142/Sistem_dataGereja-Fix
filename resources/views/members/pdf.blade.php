<h2>Data Jemaat Gereja</h2>
<table width="100%" border="1" cellspacing="0" cellpadding="6">
    <thead><tr><th>Nama</th><th>Kontak</th><th>Status</th><th>Tanggal Lahir</th></tr></thead>
    <tbody>
    @foreach($members as $member)
        <tr><td>{{ $member->nama }}</td><td>{{ $member->kontak }}</td><td>{{ $member->status }}</td><td>{{ $member->tanggal_lahir }}</td></tr>
    @endforeach
    </tbody>
</table>
