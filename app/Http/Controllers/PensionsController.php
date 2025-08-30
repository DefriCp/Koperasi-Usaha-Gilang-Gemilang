<?php

namespace App\Http\Controllers;

use App\Models\Pension;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PensionsController extends Controller
{
    public function index(Request $r)
    {
        $q = Pension::query();

        if ($r->filled('q')) {
            $s = trim((string)$r->input('q'));
            $q->where(function ($w) use ($s) {
                $w->where('nip', 'ilike', "%{$s}%")
                  ->orWhere('name', 'ilike', "%{$s}%");
            });
        }

        $rows = $q->orderBy('name')->paginate(20)->withQueryString();

        return view('pensions.index', compact('rows'));
    }

    public function create()
    {
        $p = new Pension();
        return view('pensions.create', compact('p'));
    }

    public function store(Request $r)
    {
        $data = $this->validatePayload($r, null);
        Pension::create($data);
        return redirect()->route('pensions.index')->with('ok', 'Data pensiun tersimpan.');
    }

    public function edit(Pension $pension)
    {
        $p = $pension;
        return view('pensions.create', compact('p'));
    }

    public function update(Request $r, Pension $pension)
    {
        $data = $this->validatePayload($r, $pension->id);
        $pension->update($data);
        return redirect()->route('pensions.index')->with('ok', 'Data pensiun diperbarui.');
    }

    public function destroy(Pension $pension)
    {
        $pension->delete();
        return back()->with('ok', 'Data pensiun dihapus.');
    }

    public function importForm()
    {
        return view('pensions.import');
    }

    // ===== Import XLSX: map kolom tabel + sisanya masuk ke "extras" =====
    public function importStore(Request $r)
    {
    $r->validate([
        'file' => ['required','file','max:20480', function($attr,$file,$fail){
            $ext = strtolower($file->getClientOriginalExtension() ?: '');
            if (!in_array($ext, ['xlsx','xls','csv'])) $fail('File harus xlsx/xls/csv');
        }],
    ]);

    $spreadsheet = IOFactory::load($r->file('file')->getRealPath());
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray(null, true, true, true);

    if (count($rows) < 2) return back()->withErrors(['file'=>'File kosong.']);

    // simpan row header asli untuk "extras"
    $headerRow = array_shift($rows);            // ['A'=>'NIP', 'B'=>'KODE_CABANG', ...]
    $norm = fn($s)=> strtolower(trim(preg_replace('/\s+/', '_', (string)$s)));

    // peta kolom header -> huruf kolom (pakai key normalized)
    $hdrNorm2Col = [];
    $hdrLabelByCol = [];
    foreach ($headerRow as $col => $label) {
        if ($label === null || $label === '') continue;
        $hdrNorm2Col[$norm($label)] = $col;
        $hdrLabelByCol[$col] = (string)$label; // label asli untuk extras
    }

    // mapping header (normalized) -> kolom tabel
    $fieldMap = [
        'nip'                    => 'nip',
        'nama_pensiunan'         => 'name', 'nama' => 'name',
        'no_ktp'                 => 'ktp','ktp'=>'ktp',
        'npwp'                   => 'npwp',
        'alamat'                 => 'address_line1',
        'nama_dati4'             => 'address_line2',
        'nama_dati2'             => 'address_line3',
        'telepon'                => 'phone','no_telepon'=>'phone','phone'=>'phone',
        'no_hp'                  => 'phone_alt','hp'=>'phone_alt',
        'tgl_lahir_pensiunan'    => 'birth_date','tanggal_lahir'=>'birth_date','tgl_lahir'=>'birth_date',
        'kode_cabang'            => 'branch_code',
        'nama_cabang'            => 'branch_name',
        'kode_jenis_pensiun'     => 'jenis_pensiun_code',
        'nama_jenis_pensiun'     => 'jenis_pensiun_name',
        'kode_jiwa'              => 'kode_jiwa',
        'nomor_skep'             => 'nomor_skep',
        'tmt_pensiun'            => 'tmt_pensiun',
        'tanggal_skep'           => 'tanggal_skep',
        'kode_juru_bayar'        => 'payer_code',
        'nama_juru_bayar'        => 'payer_name',
        'no_rekening'            => 'account_number',
        'penpok'                 => 'penpok',
        'tunjangan_istri'        => 'tunj_istri','tunj_istri'=>'tunj_istri',
        'tunjangan_anak'         => 'tunj_anak','tunj_anak'=>'tunj_anak',
        'tunjangan_beras'        => 'tunj_beras','tunj_beras'=>'tunj_beras',
        'penyesuaian'            => 'penyesuaian',
        'tunjangan_bulat'        => 'tunj_bulat','tunj_bulat'=>'tunj_bulat',
        'total_kotor'            => 'total_kotor',
        'bersih'                 => 'bersih',
    ];

    // helper ambil col huruf dari header normalized
    $getCol = function(array $cands) use ($hdrNorm2Col) {
        foreach ((array)$cands as $n) if (isset($hdrNorm2Col[$n])) return $hdrNorm2Col[$n];
        return null;
    };

    $inserted = 0; $updated = 0;

    foreach ($rows as $row) {
        // value menurut label normalized (biar gampang diakses)
        $vals = [];
        foreach ($hdrNorm2Col as $hn => $col) $vals[$hn] = $row[$col] ?? null;

        $nip = trim((string)($vals['nip'] ?? ''));
        $name = trim((string)($vals['nama_pensiunan'] ?? $vals['nama'] ?? ''));

        if ($nip === '' && $name === '') continue; // kosong
        if ($nip === '') continue;

        // payload utama (kolom tabel)
        $payload = [];
        foreach ($fieldMap as $hNorm => $dbCol) {
            if (!array_key_exists($hNorm,$vals)) continue;
            $val = $vals[$hNorm];

            // normalisasi tipe untuk kolom tertentu
            if (in_array($dbCol, ['birth_date','tmt_pensiun','tanggal_skep'])) {
                $payload[$dbCol] = $this->asDate($val);
            } elseif (in_array($dbCol, ['penpok','tunj_istri','tunj_anak','tunj_beras','penyesuaian','tunj_bulat','total_kotor','bersih'])) {
                $payload[$dbCol] = $this->asNum($val);
            } else {
                $payload[$dbCol] = is_string($val) ? trim($val) : $val;
            }
        }

        // wajib
        $payload['nip']  = $nip;
        $payload['name'] = $name ?: ($payload['name'] ?? null);

        // kumpulkan "extras" = semua header yang tidak dipetakan
        $mappedCols = [];
        foreach ($fieldMap as $hNorm => $dbCol) {
            if (isset($hdrNorm2Col[$hNorm])) $mappedCols[] = $hdrNorm2Col[$hNorm];
        }
        $extras = [];
        foreach ($row as $col => $val) {
            if (!in_array($col, $mappedCols, true)) {
                $labelAsli = $hdrLabelByCol[$col] ?? $col;
                // simpan apa adanya (string/angka/tanggal excel tetap kita simpan string)
                $extras[$labelAsli] = is_string($val) ? trim($val) : $val;
            }
        }
        if ($extras) $payload['extras'] = $extras;

        // upsert by NIP
        $exists = Pension::where('nip', $nip)->first();
        if ($exists) { $exists->update($payload); $updated++; }
        else         { Pension::create($payload); $inserted++; }
    }

    return redirect()->route('pensions.index')
        ->with('ok', "Import selesai: tambah {$inserted}, update {$updated}");
    }


    public function template(): StreamedResponse
    {
    $headers = [
        'NIP','KODE_CABANG','NAMA_CABANG','NOTAS','NO_KTP','KODE_JENIS_PENSIUN','NAMA_JENIS_PENSIUN',
        'KODE_JENIS_DAPEM','NAMA_JENIS_DAPEM','NAMA_PENSIUNAN','TGL_LAHIR_PENSIUNAN','PENPOK',
        'TUNJANGAN_ISTRI','TUNJANGAN_ANAK','TUNJANGAN_BERAS','PENYESUAIAN','TUNJANGAN_BULAT',
        'TOTAL_KOTOR','BERSIH','RAPEL','KODE_JIWA','NAMA_PENERIMA','TGL_LAHIR_PENERIMA',
        'KATEGORI USIA','ALAMAT','NAMA_DATI4','NAMA_DATI2','KODE_JURU_BAYAR','NAMA_JURU_BAYAR',
        'NO_URUT','TMT_PENSIUN','NOMOR_SKEP','TANGGAL_SKEP','NO_REKENING','PENERBIT_SKEP','NPWP',
        'TMT_STOP','KODE_STOP','NAMA_STOP','TELEPON','NO_HP','KODE_PANGKAT','KODE_CABANG',
        'AWAL_FLAG','AKHIR_FLAG','NAMA_KELOMPOK_BAYAR','KODE_HUBUNGAN_KELUARGA','KODE_JENIS_KELAMIN',
        'NAMA_AGAMA','TUNJANGAN_PP','TUNJANGAN_KD','TUNJANGAN_DAHOR','TUNJANGAN_CACAT','TUNJANGAN_PAJAK',
        'POTONGAN_ASKES','POTONGAN_ASSOS','POTONGAN_KASDA','POTONGAN_ALIMENTASI','POTONGAN_SEWA',
        'TGR','RN','CHEKLIST','STATUS LOAN GG',
    ];

    $ss = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $ws = $ss->getActiveSheet();
    foreach ($headers as $i => $h) $ws->setCellValueByColumnAndRow($i+1, 1, $h);

    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($ss, 'Xlsx');
    return new StreamedResponse(function() use ($writer){ $writer->save('php://output'); }, 200, [
        'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'Content-Disposition' => 'attachment; filename="template-data-pensiun.xlsx"',
    ]);
    }

    private function validatePayload(Request $r, ?int $id): array
    {
        return $r->validate([
            'nip'                 => ['required','string','max:30', Rule::unique('pensions','nip')->ignore($id)],
            'name'                => ['required','string','max:200'],

            'ktp'                 => ['nullable','string','max:50'],
            'npwp'                => ['nullable','string','max:50'],

            'address_line1'       => ['nullable','string'],
            'address_line2'       => ['nullable','string'],
            'address_line3'       => ['nullable','string'],

            'phone'               => ['nullable','string','max:50'],
            'phone_alt'           => ['nullable','string','max:50'],
            'birth_date'          => ['nullable','date'],

            'branch_code'         => ['nullable','string','max:20'],
            'branch_name'         => ['nullable','string','max:120'],

            'jenis_pensiun_code'  => ['nullable','string','max:20'],
            'jenis_pensiun_name'  => ['nullable','string','max:120'],

            'kode_jiwa'           => ['nullable','string','max:20'],
            'nomor_skep'          => ['nullable','string','max:100'],
            'tmt_pensiun'         => ['nullable','date'],
            'tanggal_skep'        => ['nullable','date'],

            'payer_code'          => ['nullable','string','max:40'],
            'payer_name'          => ['nullable','string','max:200'],
            'account_number'      => ['nullable','string','max:60'],

            'penpok'              => ['nullable','numeric'],
            'tunj_istri'          => ['nullable','numeric'],
            'tunj_anak'           => ['nullable','numeric'],
            'tunj_beras'          => ['nullable','numeric'],
            'penyesuaian'         => ['nullable','numeric'],
            'tunj_bulat'          => ['nullable','numeric'],
            'total_kotor'         => ['nullable','numeric'],
            'bersih'              => ['nullable','numeric'],
        ]);
    }

    private function asNum($v): ?float
    {
        if ($v === null || $v === '') return null;
        $s = (string)$v;
        $s = str_replace(['.',','], ['','.' ], $s);
        $s = preg_replace('/[^0-9\.\-]+/','',$s);
        return is_numeric($s) ? (float)$s : null;
    }

    private function asDate($v): ?string
    {
        if ($v === null || $v === '') return null;
        if (is_numeric($v)) {
            try { return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($v)->format('Y-m-d'); }
            catch (\Throwable $e) {}
        }
        $try = ['Y-m-d','d/m/Y','d-m-Y','m/d/Y','m-d-Y'];
        foreach ($try as $f) {
            $dt = \DateTime::createFromFormat($f, (string)$v);
            if ($dt) return $dt->format('Y-m-d');
        }
        $ts = strtotime((string)$v);
        return $ts ? date('Y-m-d', $ts) : null;
    }

    private function headerMap(array $firstRow): array
    {
        $norm = fn($s)=> strtolower(trim(preg_replace('/\s+/', '_', (string)$s)));

        $aliases = [
            'nip'                 => ['nip','nopen','nomor_pensiun'],
            'name'                => ['nama_pensiunan','nama'],

            'ktp'                 => ['no_ktp','ktp','nik'],
            'npwp'                => ['npwp'],

            'alamat'              => ['alamat','address','alamat_1','alamat_line1'],
            'nama_dati4'          => ['nama_dati4','kelurahan','alamat_2','alamat_line2'],
            'nama_dati2'          => ['nama_dati2','kabupaten','alamat_3','alamat_line3'],

            'phone'               => ['telepon','no_telepon','phone'],
            'phone_alt'           => ['no_hp','hp','mobile'],

            'birth_date'          => ['tgl_lahir_pensiunan','tanggal_lahir','tgl_lahir'],

            'branch_code'         => ['kode_cabang'],
            'branch_name'         => ['nama_cabang'],

            'jenis_pensiun_code'  => ['kode_jenis_pensiun'],
            'jenis_pensiun_name'  => ['nama_jenis_pensiun'],

            'kode_jiwa'           => ['kode_jiwa'],
            'nomor_skep'          => ['nomor_skep'],
            'tmt_pensiun'         => ['tmt_pensiun'],
            'tanggal_skep'        => ['tanggal_skep'],

            'payer_code'          => ['kode_juru_bayar'],
            'payer_name'          => ['nama_juru_bayar'],
            'account_number'      => ['no_rekening'],

            'penpok'              => ['penpok'],
            'tunj_istri'          => ['tunjangan_istri','tunj_istri'],
            'tunj_anak'           => ['tunjangan_anak','tunj_anak'],
            'tunj_beras'          => ['tunjangan_beras','tunj_beras'],
            'penyesuaian'         => ['penyesuaian'],
            'tunj_bulat'          => ['tunjangan_bulat','tunj_bulat'],
            'total_kotor'         => ['total_kotor'],
            'bersih'              => ['bersih'],
        ];

        $map = [];
        foreach ($firstRow as $col => $label) {
            $n = $norm($label);
            foreach ($aliases as $key => $cands) {
                foreach ($cands as $cand) {
                    if ($n === $cand) { $map[$key] = $col; break 2; }
                }
            }
        }
        return $map;
    }
}
