<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProjectController extends Controller
{
    /* =========================== LIST =========================== */
    public function index(Request $r)
    {
        $this->ensureAuthenticated();

        $q = Project::query();
        if ($r->filled('q')) {
            $key = trim($r->string('q'));
            $q->where('name', 'ILIKE', "%{$key}%");
        }

        $projects = $q->orderBy('name')->paginate(30)->withQueryString();
        return view('projects.index', compact('projects'));
    }

    /* ====================== CREATE / STORE ====================== */
    public function create()
    {
        $this->authorizeChecker();
        return view('projects.create');
    }

    public function store(Request $r)
    {
        $this->authorizeChecker();

        $data = $r->validate([
            'name'      => ['required','string','max:200','unique:projects,name'],
            'is_active' => ['nullable','boolean'],
        ]);

        $data['is_active'] = (bool)($data['is_active'] ?? true);

        Project::create($data);

        return redirect()->route('projects.index')->with('ok', 'Project berhasil ditambahkan.');
    }

    /* ====================== EDIT / UPDATE ======================= */
    public function edit(Project $project)
    {
        $this->authorizeChecker();
        return view('projects.edit', compact('project'));
    }

    public function update(Request $r, Project $project)
    {
        $this->authorizeChecker();

        $data = $r->validate([
            'name'      => ['required','string','max:200',"unique:projects,name,{$project->id}"],
            'is_active' => ['nullable','boolean'],
        ]);
        $data['is_active'] = (bool)($data['is_active'] ?? false);

        $project->update($data);

        return redirect()->route('projects.index')->with('ok', 'Project berhasil diperbarui.');
    }

    /* ========================== DELETE ========================== */
    public function destroy(Project $project)
    {
        $this->authorizeChecker();

        // Aman: tolak hapus jika masih dipakai debitur
        if ($project->debtors()->exists()) {
            return back()->withErrors(['project' => 'Tidak bisa menghapus: project masih dipakai debitur.']);
        }

        $project->delete();
        return redirect()->route('projects.index')->with('ok', 'Project dihapus.');
    }

    /* ===================== IMPORT (MASTER) ====================== */
    public function importForm()
    {
        $this->authorizeChecker();
        return view('projects.import');
    }

    public function importStore(Request $r)
    {
        $this->authorizeChecker();

        $r->validate([
            'file' => [
                'required','file','max:20480',
                function ($attr, $file, $fail) {
                    $ext = strtolower($file->getClientOriginalExtension() ?: '');
                    if (!in_array($ext, ['xls','xlsx','xlsm'])) {
                        $fail('File harus .xls / .xlsx / .xlsm');
                    }
                },
            ],
        ]);

        try {
            $reader = IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true);
            $sheet = $reader->load($r->file('file')->getRealPath())->getActiveSheet();
        } catch (\Throwable $e) {
            return back()->withErrors(['file' => 'Gagal baca Excel: '.$e->getMessage()]);
        }
        $rows  = $sheet->toArray(null, true, true, true);
        $first = min(1000, count($rows));
        $headers = [];
        $targetCols = [];

        $candidates = ['PROJECT','PROJEK','NAMA PROJECT','KREDITUR','KREDITOR','PRODUK LOAN','BANK','KANTOR BAYAR','KANTOR BAYAR TUJUAN'];

        foreach ($rows as $rIdx => $cols) {
            foreach ($cols as $col => $val) {
                $text = $this->cleanStr($val);
                if ($rIdx <= 10 && $text !== '') {
                    $headers["{$col}:{$rIdx}"] = $text;
                    $up = $this->upkey($text);
                    foreach ($candidates as $cand) {
                        if (str_contains($up, $this->upkey($cand))) {
                            $targetCols[] = $col;
                            break;
                        }
                    }
                }
            }
            if ($rIdx >= 10) break;
        }
        $targetCols = array_values(array_unique($targetCols));

        if (empty($targetCols)) {
            return back()->withErrors(['file' => 'Tidak menemukan kolom Project/Bank pada header.']);
        }

        $created = 0; $skipped = 0; $updated = 0;
        $seen = [];

        $startRow = 2;

        foreach ($rows as $i => $cols) {
            if ($i < $startRow) continue;

            $value = '';
            foreach ($targetCols as $C) {
                $raw = $this->cleanStr($cols[$C] ?? '');
                if ($raw !== '') { $value = $raw; break; }
            }
            if ($value === '') { $skipped++; continue; }

            $name = trim($value);
            if (isset($seen[$this->upkey($name)])) { $skipped++; continue; }
            $seen[$this->upkey($name)] = true;

            $existing = Project::where('name','ILIKE',$name)->orWhere('name','ILIKE','%'.$name.'%')->first();
            if ($existing) {
                if ($existing->name !== $name) {
                    $existing->update(['name' => $name]);
                    $updated++;
                } else {
                    $skipped++;
                }
                continue;
            }

            Project::create(['name'=>$name, 'is_active'=>true]);
            $created++;
        }

        return redirect()->route('projects.index')
            ->with('ok', "Import selesai: tambah {$created}, update {$updated}, lewati {$skipped}.");
    }
    public function purgeOrphans()
    {
        $this->authorizeChecker();
        $deleted = Project::whereDoesntHave('debtors')->delete();
        return back()->with('ok', "Hapus project tanpa debitur: {$deleted}");
    }

    /* =========================== Helpers ========================= */
    private function ensureAuthenticated(): void
    {
        abort_unless(Auth::check(), 401);
    }

    private function authorizeChecker(): void
    {
        abort_unless(Auth::check() && Auth::user()->hasRole('checker'), 403, 'USER DOES NOT HAVE THE RIGHT ROLES.');
    }

    private function cleanStr($v): string
    {
        $s = (string)($v ?? '');
        if ($s === '') return '';
        $s = str_replace("\xC2\xA0", ' ', $s);
        $s = strip_tags($s);
        $s = preg_replace('/\s+/u', ' ', $s);
        return trim($s);
    }

    private function upkey(string $s): string
    {
        $u = mb_strtoupper($s, 'UTF-8');
        return preg_replace('/[^A-Z0-9]+/u', '', $u);
    }
}
