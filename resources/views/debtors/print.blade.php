<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>PAYMENT SCHEDULE - {{ $debtor->name }}</title>
  <style>
    @page { size: A4 portrait; margin: 14mm; }
    * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    body { font-family: Arial, Helvetica, sans-serif; font-size: 10px; color:#111; margin:0; }
    h1 { font-size: 14px; text-align:center; margin:0 0 8px; }
    table { width:100%; border-collapse: collapse; }
    th, td { border:1px solid #444; padding:3px 5px; }
    th { background:#f3f3f3; font-weight:700; }
    .meta { margin-bottom:8px; width:100%; }
    .meta td { border:none; padding:2px 0; }

    thead { display: table-header-group; }
    tfoot { display: table-row-group; }
    tr { page-break-inside: avoid; }

    .no-print { margin:8px 0; }
    .btn { display:inline-block; border:1px solid #bbb; padding:4px 8px; border-radius:6px; text-decoration:none; color:#111; font-weight:700; }
    .btn:hover { background:#eee; }

    .muted { color:#666; }
    .prepaid { background:#fff6d6; } /* baris angsuran dibayar dimuka */
    @media print { .no-print { display:none; } }
  </style>
</head>
<body>
  <div class="no-print">
    <a href="{{ url()->previous() }}" class="btn">&larr; Kembali</a>
    <a href="#" onclick="window.print();return false;" class="btn">Cetak</a>
  </div>

  <h1>PAYMENT SCHEDULE</h1>

  <table class="meta">
    <tr>
      <td><strong>Nama</strong>: {{ $debtor->name }}</td>
      <td><strong>Nopen</strong>: {{ $debtor->nopen }}</td>
      <td><strong>Project</strong>: {{ $debtor->project?->name ?? '—' }}</td>
    </tr>
    <tr>
      <td><strong>Tenor</strong>: {{ (int)$debtor->tenor }} bulan</td>
      <td><strong>Angs ke (dibayar dimuka)</strong>: {{ (int)$debtor->installment_no }}</td>
      <td><strong>Tgl Akad</strong>:
        {{ $debtor->akad_date ? \Carbon\Carbon::parse($debtor->akad_date)->translatedFormat('d F Y') : '—' }}
      </td>
    </tr>
  </table>

  @php
    // Ringkasan untuk footer
    $sumPokok = $rows->sum('pokok');
    $sumBunga = $rows->sum('bunga');
    $sumAdm   = $rows->sum('adm');
    $sumTotal = $rows->sum('total');
  @endphp

  <table>
    <thead>
      <tr>
        <th style="width:7%">Angs Ke</th>
        <th style="width:18%">Tgl-Bln-Thn</th>
        <th style="width:15%">Out Standing</th>
        <th style="width:15%">Angs. Pokok</th>
        <th style="width:15%">Angs. Bunga</th>
        <th style="width:15%">Adm. Angsuran</th>
        <th style="width:15%">Total Angs.</th>
      </tr>
    </thead>
    <tbody>
      @foreach($rows as $r)
        <tr class="{{ $r->is_prepaid ? 'prepaid' : '' }}">
          <td style="text-align:center">{{ $r->seq }}</td>
          <td>{{ \Carbon\Carbon::parse($r->period_date)->translatedFormat('d F Y') }}</td>
          <td style="text-align:right">Rp {{ number_format($r->outstanding,0,',','.') }}</td>
          <td style="text-align:right">Rp {{ number_format($r->pokok,0,',','.') }}</td>
          <td style="text-align:right">Rp {{ number_format($r->bunga,0,',','.') }}</td>
          <td style="text-align:right">Rp {{ number_format($r->adm,0,',','.') }}</td>
          <td style="text-align:right; font-weight:700">Rp {{ number_format($r->total,0,',','.') }}</td>
        </tr>
      @endforeach
    </tbody>
    <tfoot>
      <tr>
        <td colspan="3" class="muted"><em>Baris berwarna = angsuran dibayar di muka (0).</em></td>
        <td style="text-align:right; font-weight:700">Rp {{ number_format($sumPokok,0,',','.') }}</td>
        <td style="text-align:right; font-weight:700">Rp {{ number_format($sumBunga,0,',','.') }}</td>
        <td style="text-align:right; font-weight:700">Rp {{ number_format($sumAdm,0,',','.') }}</td>
        <td style="text-align:right; font-weight:700">Rp {{ number_format($sumTotal,0,',','.') }}</td>
      </tr>
    </tfoot>
  </table>

  <script>/* Cetak via tombol */</script>
</body>
</html>
