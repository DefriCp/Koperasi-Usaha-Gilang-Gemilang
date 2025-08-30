<?php

namespace App\Http\Controllers;

use App\Models\Debtor;
use App\Models\Project;
use App\Models\Repayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;

class DebtorController extends Controller
{
    /* ============================================================
     |  Header alias yang dikenali saat import (minimal nopen & name)
     * ============================================================ */
    private array $ALIAS = [
        'nopen' => [
            'NOPEN','NO.PENSIUN','NO PENSIUN','NO.PENSIUN/NOPEN','NO PENSIUN (NOPEN)','NO.PEN','NO PEN'
        ],
        'name'  => [
            'NAMA','NAMA DEBITUR','NAMA DEBITUR KTP','NAMA DEBITUR SKEP','NAMA PENERIMA','NAMA PEMOHON','NAMA PENSIUNAN'
        ],

        'tenor'    => ['TENOR','TENOR (BULAN)','TENOR BULAN'],
        'angs_ke'  => ['ANGSURAN KE','ANGSURAN KE-','ANGSURAN KE -','ANGSURAN KE –','ANGSURANKE'],
        'angsuran' => ['ANGSURAN PER BULAN','ANGSURAN/BLN','ANGSURAN','ANGS.EFEKTIF PER BULAN','ANGS EFEKTIF PER BULAN','ANGS. EFEKTIF PER BULAN'],
        'plafon'   => ['PLAFOND PINJAMAN','PLAFOND','PLAFOND KREDIT','PLAFON','PLAFON KREDIT'],
        'akad'     => ['TGL.PK','TGL PK','TANGGAL AKAD','TGL AKAD','TGL.PEMBIAYAAN','TGL PEMBIAYAAN','TGL.PEMBAYARAN','TGL PEMBAYARAN'],

        // kolom-kolom yang bisa memuat identitas project/bank
        'project_hint' => ['KREDITUR','KREDITOR','PRODUK LOAN','PRODUK','KREDITUR TAKE OVER','KREDITUR ASAL TAKE OVER','KANTOR BAYAR TUJUAN','KANTOR BAYAR','BANK'],
    ];

    /* ============================================================
     |  Daftar sinonim project (membantu pencocokan — TIDAK bikin project baru)
     * ============================================================ */
    private array $PROJECT_CANON = [
        'kbbank'                     => 'KB BANK',
        'kbbanksyariah'              => 'KB BANK SYARIAH',
        'bankmnc'                    => 'BANK MNC',
        'bprks'                      => 'BPR KS',
        'bprvima'                    => 'BPR VIMA',
        'bprindomitra'               => 'BPR INDOMITRA',
        'bprhosing'                  => 'BPR HOSING',
        'bprhasamitra'               => 'BPR HASAMITRA',
        'bprperdana'                 => 'BPR PERDANA',
        'bprrifi'                    => 'BPR RIFI',
        'bprnbp29'                   => 'BPR NBP29',
        'channelingbankbukopinkbbank'=> 'CHANNELING BANK BUKOPIN / KB BANK',
        'channelingbukopinsyariah'   => 'CHANNELING BUKOPIN SYARIAH',
        'channelingbankmnc'          => 'CHANNELING BANK MNC',
        'channelingbprks'            => 'CHANNELING BPR KS',
        'channelingbprindomitra'     => 'CHANNELING BPR INDOMITRA',
        'channelingbprhosing'        => 'CHANNELING BPR HOSING',
        'channelingbprhasamitra'     => 'CHANNELING BPR HASAMITRA',
        'subchannelingssbbprperdana' => 'SUB CHANNELING SSB BPR PERDANA',
        'channelingssbbprrifi'       => 'CHANNELING SSB BPR RIFI',
        'channelingbprnbp29'         => 'CHANNELING BPR NBP29',
        'channelingbprdhaha'         => 'CHANNELING BPR DHAHA',
        'channelingkopsam'           => 'CHANNELING KOP SAM',
        'channelingkspsms'           => 'CHANNELING KSP SMS',
        'executeeksplat'             => 'EXECUT EKS PLAT',
        'executeeksplatssb'          => 'EXECUT EKS PLAT SSB',
        'executplatssb'              => 'EXECUT PLAT SSB',
        'executplatinum'             => 'EXECUT PLATINUM',
        'subchannelinggrahadi'       => 'SUB CHANNELING GRAHADI',
        'subchannelingkopjas'        => 'SUB CHANNELING KOPJAS',
        'subchannelingkosppibankbanten'=>'SUB CHANNELING KOSPPI BANK BANTEN',
    ];

    /* =================== LIST =================== */
    public function index(Request $r)
    {
        $q = Debtor::query()->with('project');

        if ($r->filled('project')) $q->where('project_id', $r->integer('project'));
        if ($r->filled('search')) {
            $s = $r->string('search');
            $q->where(fn($x) => $x->where('nopen','ilike',"%$s%")->orWhere('name','ilike',"%$s%"));
        }

        /** batasan visibilitas */
        $u = Auth::user();
        if ($u->hasRole('inputer'))      $q->where(fn($x)=> $x->where('user_id',$u->id)->orWhere('status','approved'));
        elseif ($u->hasRole('viewer'))   $q->where('status','approved');

        $debtors  = $q->orderBy('name')->paginate(15)->withQueryString();
        $projects = Project::orderBy('name')->get(['id','name']);

        return view('debtors.index', compact('debtors','projects'));
    }

    /* =================== CREATE =================== */
    public function create()
    {
        $this->authorizeRole(['inputer','checker']);
        $projects = Project::orderBy('name')->get(['id','name']);
        return view('debtors.create', compact('projects'));
    }

    /* =================== STORE (input manual) =================== */
    public function store(Request $r)
    {
        $this->authorizeRole(['inputer','checker']);

        // validasi core Debtor
        $base = $r->validate([
            'project_id'     => ['nullable','exists:projects,id'],
            'nopen'          => ['required','string','max:100','unique:debtors,nopen'],
            'name'           => ['required','string','max:200'],
            'plafond'        => ['required','numeric','min:0'],
            'tenor'          => ['required','integer','min:1'],
            'installment_no' => ['required','integer','min:0'],
            'installment'    => ['required','numeric','min:0'],
            'akad_date'      => ['required','date'],
        ]);

        // validasi detail (opsional)
        $detail = $r->validate([
            'input_date'         => ['nullable','date'],
            'loan_number'        => ['nullable','string','max:100'],
            'project_text'       => ['nullable','string','max:200'],
            'payer'              => ['nullable','string','max:200'],
            'pension'            => ['nullable','string','max:100'],
            'area'               => ['nullable','string','max:50'],
            'branch'             => ['nullable','string','max:100'],
            'submission_type'    => ['nullable','string','max:100'],
            'address'            => ['nullable','string'],
            'kelurahan'          => ['nullable','string','max:120'],
            'kecamatan'          => ['nullable','string','max:120'],
            'kabupaten'          => ['nullable','string','max:120'],
            'provinsi'           => ['nullable','string','max:120'],
            'interest_rate'      => ['nullable'],
            'baa_percent'        => ['nullable'],
            'agreement_date'     => ['nullable','date'],
            'start_credit_date'  => ['nullable','date'], // tanggal pertama debitur menerima uang
            'end_credit_date'    => ['nullable','date'],
            'disbursement_date'  => ['nullable','date'],
            'provisi'            => ['nullable'],
            'administrasi'       => ['nullable'],
            'asuransi'           => ['nullable'],
            'tata_kelola'        => ['nullable'],
            'angsuran_dimuka'    => ['nullable'],
            'birth_date'         => ['nullable','date'],
            'baa_value'          => ['nullable'],
            'total_installment'  => ['nullable'],
            'account_number'     => ['nullable','string','max:100'],
            'bank_alias'         => ['nullable','string','max:200'], // alias Project
        ]);

        // project dari dropdown / alias bank (tidak membuat project baru)
        $projectId = $base['project_id'] ?? null;
        if (!$projectId && !empty($detail['bank_alias'])) {
            $pid = $this->resolveExistingProjectIdFromHint($detail['bank_alias']);
            if ($pid) $projectId = $pid;
        }
        if (!$projectId) {
            return back()
                ->withErrors(['project_id' => 'Pilih Project atau isi kolom Bank (alias Project) yang cocok dengan Master Project.'])
                ->withInput();
        }

        $base['status']     = Auth::user()->hasRole('checker') ? 'approved' : 'pending';
        $base['user_id']    = Auth::id();
        $base['project_id'] = $projectId;

        // ringkasan outstanding & arrears
        [$outstanding, $arrears] = $this->calcAmounts(
            (int)$base['tenor'], (int)$base['installment_no'], (float)$base['installment'], (string)$base['akad_date']
        );
        $base['outstanding'] = $outstanding;
        $base['arrears']     = $arrears;

        $debtor = Debtor::create($base);

        // normalisasi angka pecahan (Rp / %)
        $toFloat = function($v){
            if ($v === null || $v === '') return null;
            $s = (string)$v;
            $s = str_replace(['.',','], ['','.' ], $s);
            $s = preg_replace('/[^0-9\.\-]+/','',$s);
            return is_numeric($s) ? (float)$s : null;
        };
        foreach ([
            'interest_rate','baa_percent','provisi','administrasi','asuransi','tata_kelola',
            'angsuran_dimuka','baa_value','total_installment'
        ] as $k) {
            if (array_key_exists($k,$detail)) $detail[$k] = $toFloat($detail[$k]);
        }

        if (method_exists($debtor, 'details')) {
            $detail['debtor_id'] = $debtor->id;
            $debtor->details()->create($detail);
        }

        if ($base['status'] === 'approved') {
            $this->ensureRepaymentsForDebtor($debtor);
            $this->recalcSummaryFromRepayments($debtor);
        }

        return redirect()->route('debtors.show', $debtor)->with('ok','Debitur berhasil ditambahkan.');
    }

    /* =================== EDIT FORM =================== */
    public function edit(Debtor $debtor)
    {
        $this->ensureCanEdit($debtor);
        $debtor->load('project','details');
        $projects = Project::orderBy('name')->get(['id','name']);
        $detail   = optional($debtor->details()->latest('id')->first());
        return view('debtors.edit', compact('debtor','projects','detail'));
    }

    /* =================== UPDATE =================== */
    public function update(Request $r, Debtor $debtor)
    {
        $this->ensureCanEdit($debtor);

        $base = $r->validate([
            'project_id'     => ['nullable','exists:projects,id'],
            'nopen'          => ['required','string','max:100','unique:debtors,nopen,'.$debtor->id],
            'name'           => ['required','string','max:200'],
            'plafond'        => ['required','numeric','min:0'],
            'tenor'          => ['required','integer','min:1'],
            'installment_no' => ['required','integer','min:0'],
            'installment'    => ['required','numeric','min:0'],
            'akad_date'      => ['required','date'],
        ]);

        $detail = $r->validate([
            'input_date'         => ['nullable','date'],
            'loan_number'        => ['nullable','string','max:100'],
            'project_text'       => ['nullable','string','max:200'],
            'payer'              => ['nullable','string','max:200'],
            'pension'            => ['nullable','string','max:100'],
            'area'               => ['nullable','string','max:50'],
            'branch'             => ['nullable','string','max:100'],
            'submission_type'    => ['nullable','string','max:100'],
            'address'            => ['nullable','string'],
            'kelurahan'          => ['nullable','string','max:120'],
            'kecamatan'          => ['nullable','string','max:120'],
            'kabupaten'          => ['nullable','string','max:120'],
            'provinsi'           => ['nullable','string','max:120'],
            'interest_rate'      => ['nullable'],
            'baa_percent'        => ['nullable'],
            'agreement_date'     => ['nullable','date'],
            'start_credit_date'  => ['nullable','date'],
            'end_credit_date'    => ['nullable','date'],
            'disbursement_date'  => ['nullable','date'],
            'provisi'            => ['nullable'],
            'administrasi'       => ['nullable'],
            'asuransi'           => ['nullable'],
            'tata_kelola'        => ['nullable'],
            'angsuran_dimuka'    => ['nullable'],
            'birth_date'         => ['nullable','date'],
            'baa_value'          => ['nullable'],
            'total_installment'  => ['nullable'],
            'account_number'     => ['nullable','string','max:100'],
            'bank_alias'         => ['nullable','string','max:200'],
        ]);

        // tentukan project
        $projectId = $base['project_id'] ?? null;
        if (!$projectId && !empty($detail['bank_alias'])) {
            $pid = $this->resolveExistingProjectIdFromHint($detail['bank_alias']);
            if ($pid) $projectId = $pid;
        }
        if (!$projectId) {
            return back()
                ->withErrors(['project_id' => 'Pilih Project atau isi kolom Bank (alias Project) yang cocok dengan Master Project.'])
                ->withInput();
        }

        $base['project_id'] = $projectId;

        // hitung ulang ringkasan
        [$outstanding, $arrears] = $this->calcAmounts(
            (int)$base['tenor'], (int)$base['installment_no'], (float)$base['installment'], (string)$base['akad_date']
        );
        $base['outstanding'] = $outstanding;
        $base['arrears']     = $arrears;

        $debtor->update($base);

        // normalisasi angka-angka pecahan di detail
        $toFloat = function($v){
            if ($v === null || $v === '') return null;
            $s = (string)$v;
            $s = str_replace(['.',','], ['','.' ], $s);
            $s = preg_replace('/[^0-9\.\-]+/','',$s);
            return is_numeric($s) ? (float)$s : null;
        };
        foreach ([
            'interest_rate','baa_percent','provisi','administrasi','asuransi','tata_kelola',
            'angsuran_dimuka','baa_value','total_installment'
        ] as $k) {
            if (array_key_exists($k,$detail)) $detail[$k] = $toFloat($detail[$k]);
        }

        if (method_exists($debtor, 'details')) {
            $existing = $debtor->details()->latest('id')->first();
            if ($existing) { $existing->update($detail); }
            else { $detail['debtor_id'] = $debtor->id; $debtor->details()->create($detail); }
        }

        // bila sudah approved, sinkronkan repayment & ringkasan
        if ($debtor->status === 'approved') {
            $this->ensureRepaymentsForDebtor($debtor);
            $this->recalcSummaryFromRepayments($debtor);
        }

        return redirect()->route('debtors.show', $debtor)->with('ok','Perubahan debitur disimpan.');
    }

    /* =================== APPROVE / REJECT (checker) =================== */
    public function approve(Debtor $debtor, Request $r)
    {
        $this->authorizeRole(['checker']);
        $act = $r->validate(['decision'=>'required|in:approved,rejected'])['decision'];

        $debtor->update([
            'status'      => $act,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        if ($act === 'approved') {
            $this->ensureRepaymentsForDebtor($debtor);
            $this->recalcSummaryFromRepayments($debtor);
        }

        return back()->with('ok', "Debitur {$debtor->nopen} -> {$act}.");
    }

    /* =================== DETAIL =================== */
    public function show(Debtor $debtor)
    {
        $debtor->load('project', 'details');

        // Ambil jadwal dari repayments; kalau belum ada, generate on-the-fly
        $rows = Repayment::where('debtor_id',$debtor->id)->orderBy('period_date')->get();

        if ($rows->isEmpty() && $debtor->tenor && $debtor->installment) {
            $anchor   = $this->firstPaymentAnchorDate($debtor) ?? Carbon::now();
            $startIdx = max(0, (int)$debtor->installment_no); // SKIP angsuran yang sudah dibayar
            for ($i = $startIdx; $i < (int)$debtor->tenor; $i++) {
                $rows->push((object)[
                    'period_date' => $anchor->copy()->addMonthsNoOverflow($i)->format('Y-m-d'),
                    'amount_due'  => (float)$debtor->installment,
                    'amount_paid' => 0,
                    'status'      => 'UNPAID',
                    'seq'         => $i + 1, // nomor angsuran sesungguhnya
                ]);
            }
        } else {
            // beri nomor urut sesuai posisi setelah installment_no
            $seqBase = (int)$debtor->installment_no + 1;
            foreach ($rows as $k => $r) { $r->seq = $seqBase + $k; }
        }

        return view('debtors.show', compact('debtor','rows'));
    }

    /* =================== PRINT SCHEDULE =================== */
    public function printSchedule(Debtor $debtor)
    {
        $debtor->load('project');

        $rows = Repayment::where('debtor_id',$debtor->id)->orderBy('period_date')->get();
        if ($rows->isEmpty() && $debtor->tenor && $debtor->installment) {
            $anchor   = $this->firstPaymentAnchorDate($debtor) ?? Carbon::now();
            $startIdx = max(0, (int)$debtor->installment_no);
            for ($i = $startIdx; $i < (int)$debtor->tenor; $i++) {
                $rows->push((object)[
                    'period_date' => $anchor->copy()->addMonthsNoOverflow($i)->format('Y-m-d'),
                    'amount_due'  => (float)$debtor->installment,
                    'amount_paid' => 0,
                    'status'      => 'UNPAID',
                    'seq'         => $i + 1,
                ]);
            }
        } else {
            $seqBase = (int)$debtor->installment_no + 1;
            foreach ($rows as $k => $r) { $r->seq = $seqBase + $k; }
        }

        // view: resources/views/debtors/print.blade.php
        return view('debtors.print', compact('debtor','rows'));
    }

    /* =================== IMPORT (EXCEL) =================== */
    public function importForm()
    {
        $this->authorizeRole(['inputer','checker']);
        $projects = Project::orderBy('name')->get(['id','name']);
        return view('debtors.import', compact('projects'));
    }

    public function importStore(Request $r)
    {
        $this->authorizeRole(['inputer','checker']);

        $r->validate([
            'project_id' => ['nullable','exists:projects,id'],
            'file'       => [
                'required','file','max:20480',
                function ($attr, $value, $fail) {
                    $ext = strtolower($value->getClientOriginalExtension() ?: '');
                    if (!in_array($ext, ['xls','xlsx','xlsm'])) $fail('File harus berekstensi xls/xlsx/xlsm.');
                },
            ],
        ]);

        try { $spreadsheet = $this->loadSpreadsheetSmart($r->file('file')); }
        catch (\Throwable $e) {
            return back()->withErrors(['file' => 'File tidak dikenali sebagai Excel. '.$e->getMessage()]);
        }

        [$sheet, $colMap, $headerMaxRow, $debug] = $this->scanColumnsAnywhere($spreadsheet, $this->ALIAS);
        if (!$sheet || !isset($colMap['nopen']) || !isset($colMap['name'])) {
            return back()->withErrors(['file' => "Header tidak ditemukan (butuh: NO.PENSIUN/NOPEN & NAMA). Debug: {$debug}"]);
        }

        $rows    = $sheet->toArray(null, true, true, true);
        $lastRow = $sheet->getHighestDataRow();

        // cari baris data pertama
        $firstDataRow = null;
        $probeLimit = min($lastRow, $headerMaxRow + 300);
        for ($i = $headerMaxRow + 1; $i <= $probeLimit; $i++) {
            $n  = $this->cleanStr($rows[$i][$colMap['nopen']] ?? '');
            $nm = $this->cleanStr($rows[$i][$colMap['name']]  ?? '');
            if ($this->looksLikeNopen($n) && $this->looksLikeName($nm)) { $firstDataRow = $i; break; }
        }
        $startData = $firstDataRow ?: max($headerMaxRow + 1, 2);

        $inserted=0; $updated=0;
        $skBlank=0; $skBadNopen=0; $skBadName=0; $skNoProject=0;

        $defaultStatus   = Auth::user()->hasRole('checker') ? 'approved' : 'pending';
        $forcedProjectId = $r->integer('project_id') ?: null;
        $batchId         = 'IMP-'.now()->format('Ymd-His').'-U'.Auth::id();

        $blankStreak = 0;
        for ($i = $startData; $i <= $lastRow; $i++) {
            $row = $rows[$i] ?? null;
            if (!$row) { if (++$blankStreak > 25) break; else continue; }
            $blankStreak = 0;

            $nopen     = $this->cleanStr($row[$colMap['nopen']] ?? '');
            $name      = $this->cleanStr($row[$colMap['name']]  ?? '');

            $tenor     = $this->asInt($row[$colMap['tenor']]   ?? null, 0);
            $angsKe    = $this->asInt($row[$colMap['angs_ke']] ?? null, 0);

            $angsuran  = $this->asNum($row[$colMap['angsuran']] ?? null, 0);
            if ($angsuran == 0.0 && isset($colMap['angsefet'])) {
                $angsuran = $this->asNum($row[$colMap['angsefet']] ?? null, 0);
            }

            $plafon    = $this->asNum($row[$colMap['plafon']] ?? null, 0);
            $akadRaw   = $this->cleanStr($row[$colMap['akad']]  ?? '');
            $akadDate  = $this->parseDateLoose($akadRaw)?->format('Y-m-d');

            // cek kosong total
            if ($nopen==='' && $name==='' && $tenor===0 && $angsKe===0 && $angsuran===0.0 && $plafon===0.0 && !$akadDate) {
                $skBlank++; continue;
            }
            if (!$this->looksLikeNopen($nopen)) { $skBadNopen++; continue; }
            if (!$this->looksLikeName($name))   { $skBadName++;  continue; }

            // Tentukan Project (tanpa membuat project baru)
            $projectId = $forcedProjectId ?: $this->projectIdFromMultipleHints($row, $colMap);
            if (!$projectId) { $skNoProject++; continue; }

            [$outstanding,$arrears] = $this->calcAmounts(
                (int)$tenor, (int)$angsKe, (float)$angsuran,
                $akadDate ?: now()->format('Y-m-d')
            );

            $payload = [
                'project_id'     => $projectId,
                'user_id'        => Auth::id(),
                'nopen'          => $nopen,
                'name'           => $name,
                'plafond'        => $plafon,
                'installment'    => $angsuran,
                'tenor'          => $tenor,
                'installment_no' => $angsKe,
                'akad_date'      => $akadDate,
                'outstanding'    => $outstanding,
                'arrears'        => $arrears,
                'status'         => $defaultStatus,
            ];

            $exists = Debtor::where('nopen',$nopen)->first();
            if ($exists) { $exists->update($payload); $updated++; }
            else         { $payload['import_batch']=$batchId; Debtor::create($payload); $inserted++; }
        }

        $totalSkipped = $skBlank + $skBadNopen + $skBadName + $skNoProject;
        $detail = "kosong: {$skBlank}, nopen tidak valid: {$skBadNopen}, nama tidak valid: {$skBadName}, tanpa project: {$skNoProject}.";

        if (($inserted+$updated)===0) {
            return back()->withErrors([
                'file' => "Tidak ada baris terproses. Debug: {$debug}. Saran: isi 'Default Project' di form agar baris tidak di-skip karena project."
            ]);
        }

        return redirect()->route('debtors.index')
            ->with('ok', "Import selesai: tambah {$inserted}, update {$updated}, lewati {$totalSkipped} ({$detail})"
                . ($inserted>0 ? " | Batch: {$batchId} (bisa di-rollback)" : ''));
    }

    /* ============ Rollback batch import (hapus debitur yang baru dibuat oleh batch tsb) ============ */
    public function rollbackImport(string $batch)
    {
        $this->authorizeRole(['checker']); // batasi checker
        $count = Debtor::where('import_batch',$batch)->count();
        Debtor::where('import_batch',$batch)->delete();
        return back()->with('ok', "Rollback '{$batch}' selesai. Dihapus {$count} baris baru.");
    }

    /* ============ HAPUS DEBITUR (inputer & checker) ============ */
    public function destroy(Debtor $debtor)
    {
        $this->authorizeRole(['inputer','checker']);
        // batasi inputer hanya boleh menghapus miliknya sendiri & yang masih pending
        $u = Auth::user();
        if ($u->hasRole('inputer') && ($debtor->user_id !== $u->id || $debtor->status !== 'pending')) {
            abort(403);
        }

        Repayment::where('debtor_id', $debtor->id)->delete();
        if (method_exists($debtor,'details')) $debtor->details()->delete();
        $debtor->delete();

        return redirect()->route('debtors.index')->with('ok','Debitur berhasil dihapus.');
    }

    /* =================== Helpers =================== */

    private function authorizeRole(array $roles){ abort_unless(Auth::user()->hasAnyRole($roles), 403); }

    private function ensureCanEdit(Debtor $debtor): void
    {
        $u = Auth::user();
        if ($u->hasRole('checker')) return; // checker boleh edit semuanya

        if ($u->hasRole('inputer')) {
            // inputer hanya boleh edit miliknya sendiri & masih pending
            abort_unless($debtor->user_id === $u->id && $debtor->status === 'pending', 403);
            return;
        }
        abort(403);
    }

    private function keyize(string $s): string
    {
        $u = Str::of($s)->upper()->toString();
        $u = str_replace("\xC2\xA0", ' ', $u);
        return preg_replace('/[^A-Z0-9]+/u', '', $u);
    }

    private function cleanStr($v): string
    {
        $s = (string)($v ?? '');
        if ($s === '') return '';
        $s = str_replace("\xC2\xA0", ' ', $s);
        $s = html_entity_decode($s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $s = preg_replace('/\s*[a-z-]+\s*=\s*"[^"]*"/i', ' ', $s); // align="..." dll
        $s = strip_tags($s);
        $s = preg_replace('/\s+/u', ' ', $s);
        return trim($s, " \t\n\r\0\x0B\"'");
    }

    private function looksLikeNopen(string $s): bool
    {
        $s = trim($s);
        if ($s === '') return false;
        if (preg_match('/ALIGN|VALIGN|DIV|TD|TH|<\/?/i', $s)) return false;
        $alnum = preg_replace('/[^A-Za-z0-9]+/', '', $s);
        if ($alnum === '') return false;
        if (!preg_match('/\d/', $alnum)) return false;
        return strlen($alnum) >= 6;
    }

    private function looksLikeName(string $s): bool
    {
        if ($s === '') return false;
        if (preg_match('/ALIGN|VALIGN|DIV|TD|TH|ANGS(\.| )?EFEKTIF|^BIAYA$/i', $s)) return false;
        return (bool)preg_match('/\p{L}+/u', $s);
    }

    private function scanColumnsAnywhere($spreadsheet, array $ALIAS): array
    {
        $sheetCount = $spreadsheet->getSheetCount();
        $wantedGroups = ['nopen','name','tenor','angs_ke','angsuran','plafon','akad'];

        $best = ['score'=>-1, 'sheet'=>null, 'map'=>[], 'rowmax'=>1, 'debug'=>''];
        $projectKeywords = array_map([$this,'keyize'],$ALIAS['project_hint']);

        for ($si=0; $si<$sheetCount; $si++) {
            $sheet = $spreadsheet->getSheet($si);
            $rows  = $sheet->toArray(null, true, true, true);
            $rowsN = count($rows);
            if ($rowsN === 0) continue;

            $limit = min($rowsN, 1000);
            $foundMap = [];
            $samples  = [];
            $projCols = [];

            for ($idx=1; $idx <= $limit; $idx++) {
                $cols = $rows[$idx] ?? [];
                if (!$cols) continue;

                foreach ($cols as $colLetter => $rawVal) {
                    $raw  = $this->cleanStr($rawVal);
                    if ($raw === '') continue;

                    $norm = $this->keyize($raw);
                    if (count($samples) < 12) $samples[] = "{$colLetter}{$idx}:{$raw}";

                    foreach ($wantedGroups as $gk) {
                        if (isset($foundMap[$gk])) continue;
                        foreach (($ALIAS[$gk] ?? []) as $alias) {
                            $a = $this->keyize($alias);
                            if ($a !== '' && strlen($norm)>2 && (str_contains($norm,$a) || str_contains($a,$norm))) {
                                $foundMap[$gk] = ['col'=>$colLetter,'row'=>$idx];
                                break 2;
                            }
                        }
                    }

                    foreach ($projectKeywords as $kw) {
                        if ($kw !== '' && str_contains($norm, $kw)) {
                            $projCols[] = ['col'=>$colLetter, 'label'=>$raw, 'row'=>$idx, 'norm'=>$norm];
                            break;
                        }
                    }

                    if (!isset($foundMap['angsefet'])) {
                        foreach (['ANGS.EFEKTIF PER BULAN','ANGS EFEKTIF PER BULAN','ANGS. EFEKTIF PER BULAN'] as $ef) {
                            $a = $this->keyize($ef);
                            if (strlen($norm)>2 && (str_contains($norm,$a) || str_contains($a,$norm))) {
                                $foundMap['angsefet'] = ['col'=>$colLetter,'row'=>$idx];
                                break;
                            }
                        }
                    }
                }
            }

            $score = 0; $rowmax = 1; $colMap = [];
            foreach ($foundMap as $gk => $info) {
                if (in_array($gk, $wantedGroups, true)) $score++;
                $colMap[$gk] = $info['col'];
                $rowmax = max($rowmax, (int)$info['row']);
            }
            if (!empty($projCols)) {
                $colMap['project_cols'] = $projCols;
                foreach ($projCols as $pc) $rowmax = max($rowmax, (int)$pc['row']);
            }

            $debug = 'SHEET "'.$sheet->getTitle().'" | ketemu: '.implode(',', array_keys($foundMap))
                   .' | projCols: '.count($projCols).' | contoh: '.implode(' | ', $samples);

            if ($score > $best['score']) {
                $best = ['score'=>$score,'sheet'=>$sheet,'map'=>$colMap,'rowmax'=>$rowmax,'debug'=>$debug];
            }
        }

        return [$best['sheet'], $best['map'], $best['rowmax'], $best['debug']];
    }

    private function projectIdFromMultipleHints(array $row, array $colMap): ?int
    {
        if (empty($colMap['project_cols'])) return null;

        $prio = [
            'KREDITUR','KREDITOR','PRODUK LOAN','PRODUK',
            'KREDITUR ASAL TAKE OVER','KREDITUR TAKE OVER',
            'KANTOR BAYAR TUJUAN','KANTOR BAYAR','BANK'
        ];

        $cols = $colMap['project_cols'];
        usort($cols, function($a,$b) use($prio){
            $aIdx = $this->indexOfLabel($a['label'],$prio);
            $bIdx = $this->indexOfLabel($b['label'],$prio);
            return $aIdx <=> $bIdx;
        });

        foreach ($cols as $c) {
            $val = $this->cleanStr($row[$c['col']] ?? '');
            if ($val === '') continue;

            $pid = $this->resolveExistingProjectIdFromHint($val); // <-- hanya cari yang sudah ada
            if ($pid) return $pid;
        }
        return null;
    }

    private function indexOfLabel(string $label, array $prio): int
    {
        $L = strtoupper(trim($label));
        foreach ($prio as $i => $p) {
            if (str_contains($L, $p)) return $i;
        }
        return PHP_INT_MAX;
    }

    private function asInt($v, $d=0): int
    {
        if ($v===null || $v==='') return (int)$d;
        $s = preg_replace('/[^0-9\-]+/','',(string)$v);
        return is_numeric($s) ? (int)$s : (int)$d;
    }

    private function asNum($v, $d=0.0): float
    {
        if ($v===null || $v==='') return (float)$d;
        $s = (string)$v;
        $s = str_replace(['.',','], ['','.' ], $s);
        $s = preg_replace('/[^0-9\.\-]+/','',$s);
        return is_numeric($s) ? (float)$s : (float)$d;
    }

    private function parseDateLoose($v): ?\DateTimeImmutable
    {
        $v = $this->cleanStr($v);
        if ($v === '') return null;

        if (is_numeric($v)) {
            try { return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($v)->setTime(0,0); }
            catch (\Throwable $e) {}
        }
        $fmts = ['Y-m-d','d/m/Y','d-m-Y','m/d/Y','m-d-Y','d.m.Y'];
        foreach ($fmts as $f) { $dt = \DateTimeImmutable::createFromFormat($f, $v); if ($dt) return $dt; }
        $ts = strtotime($v);
        return $ts ? (new \DateTimeImmutable())->setTimestamp($ts)->setTime(0,0) : null;
    }

    private function calcAmounts(int $tenor, int $angsKe, float $angs, string $akad): array
    {
        $paid   = max(0, min($tenor, $angsKe));
        $remain = max(0, $tenor - $paid);
        $outstanding = $remain * $angs;

        $start = new \DateTime($akad);
        $now   = new \DateTime();
        $elapsed = ($now->format('Y') - $start->format('Y')) * 12 + ($now->format('n') - $start->format('n'));
        if ((int)$now->format('j') < (int)$start->format('j')) $elapsed--;
        $elapsed = max(0, min($tenor, $elapsed));
        $arrearsInstall = max(0, $elapsed - $paid);
        $arrears = $arrearsInstall * $angs;

        return [$outstanding, $arrears];
    }

    /** Cari project yang SUDAH ADA dari sebuah hint (tanpa membuat baru) */
    private function resolveExistingProjectIdFromHint(?string $hint): ?int
    {
        $hint = $this->cleanStr($hint);
        if ($hint === '') return null;

        // normalisasi
        $normKey = Str::of($hint)->lower()->replaceMatches('/[^a-z0-9]+/u','')->toString();

        // mapping sinonim → label resmi
        $mapContains = [
            'KBBANK'          => 'KB BANK',
            'PLATINUMKBBANK'  => 'KB BANK',
            'BPRHASAMITRA'    => 'BPR HASAMITRA',
            'LINTASHASAMITRA' => 'BPR HASAMITRA',
            'BANKMNC'         => 'BANK MNC',
            'BPRVIMA'         => 'BPR VIMA',
            'BPRKS'           => 'BPR KS',
            'INDOMITRA'       => 'BPR INDOMITRA',
            'HOSING'          => 'BPR HOSING',
            'RIFI'            => 'BPR RIFI',
            'PERDANA'         => 'BPR PERDANA',
            'NBP29'           => 'BPR NBP29',
            'DHAHA'           => 'CHANNELING BPR DHAHA',
            'KOPSAM'          => 'CHANNELING KOP SAM',
            'KSPSMS'          => 'CHANNELING KSP SMS',
            'GRAHADI'         => 'SUB CHANNELING GRAHADI',
            'KOPJAS'          => 'SUB CHANNELING KOPJAS',
            'KOSPPI'          => 'SUB CHANNELING KOSPPI BANK BANTEN',
        ];
        $H = strtoupper($hint);

        $projects = Project::all(['id','name']);
        // 1) coba pakai mapping contain -> official
        foreach ($mapContains as $needle => $official) {
            if (str_contains($H, $needle)) {
                $p = $projects->first(function($x) use($official){
                    return stripos($x->name, $official) !== false;
                });
                if ($p) return $p->id;
            }
        }
        // 2) cocokkan fuzzy: norm(hint) ≈ norm(nama project)
        foreach ($projects as $p) {
            $pn = Str::of($p->name)->lower()->replaceMatches('/[^a-z0-9]+/u','')->toString();
            if ($pn !== '' && (str_contains($pn,$normKey) || str_contains($normKey,$pn))) {
                return $p->id;
            }
        }
        // 3) LIKE biasa
        $existing = Project::where('name','ILIKE',"%{$hint}%")->first();
        return $existing?->id;
    }

    /** Ambil anchor date = tanggal pertama pencairan jika ada, selain itu pakai akad_date */
    private function firstPaymentAnchorDate(Debtor $debtor): ?Carbon
    {
        $date = $debtor->akad_date;

        if (method_exists($debtor, 'details')) {
            $d = $debtor->details()->latest('id')->first();
            if ($d) {
                $date = $d->start_credit_date ?? $d->disbursement_date ?? $d->agreement_date ?? $date;
            }
        }

        return $date ? Carbon::parse($date) : null;
    }

    private function ensureRepaymentsForDebtor(Debtor $debtor): void
    {
        if (!$debtor->tenor || !$debtor->installment) return;

        $anchor = $this->firstPaymentAnchorDate($debtor);
        if (!$anchor) return;

        $tenor  = (int) $debtor->tenor;
        $paidNo = max(0, (int) $debtor->installment_no);
        $amount = (float) $debtor->installment;

        for ($i = $paidNo; $i < $tenor; $i++) {
            $period = $anchor->copy()->addMonthsNoOverflow($i)->format('Y-m-d');

            $existing = Repayment::where('debtor_id', $debtor->id)
                ->whereDate('period_date', $period)->first();

            if (!$existing) {
                Repayment::create([
                    'debtor_id'   => $debtor->id,
                    'period_date' => $period,
                    'amount_due'  => $amount,
                    'amount_paid' => 0,
                    'status'      => 'UNPAID',
                ]);
            } elseif ($existing->amount_due != $amount) {
                $existing->update(['amount_due' => $amount]);
            }
        }
    }

    private function recalcSummaryFromRepayments(Debtor $debtor): void
    {
        $today = Carbon::now()->startOfDay();
        $rows = Repayment::where('debtor_id', $debtor->id)->get(['period_date','amount_due','amount_paid','status']);

        $outstanding = 0.0; $arrears = 0.0;
        foreach ($rows as $r) {
            $remain = max(0, (float)$r->amount_due - (float)$r->amount_paid);
            $outstanding += $remain;
            if ($r->status !== 'PAID' && Carbon::parse($r->period_date)->lt($today)) $arrears += $remain;
        }

        $debtor->update(['outstanding'=>$outstanding,'arrears'=>$arrears]);
    }

    private function loadSpreadsheetSmart($uploaded)
    {
        $ext  = strtolower($uploaded->getClientOriginalExtension() ?: '');
        $path = $uploaded->getRealPath();

        $try = function (string $type) use ($path) {
            $reader = IOFactory::createReader($type);
            $reader->setReadDataOnly(true);
            return $reader->load($path);
        };

        try {
            if ($ext === 'xls')   return $try('Xls');
            if ($ext === 'xlsx')  return $try('Xlsx');
            if ($ext === 'xlsm')  return $try('Xlsx');
        } catch (\Throwable $e) {}

        try { return $try('Xlsx'); } catch (\Throwable $e) {}
        try { return $try('Xls'); }  catch (\Throwable $e) {}
        try { return $try('Xml'); }  catch (\Throwable $e) {}
        try { return $try('Csv'); }  catch (\Throwable $e) {}

        throw new \RuntimeException('Format berkas tidak didukung atau file rusak.');
    }
}
