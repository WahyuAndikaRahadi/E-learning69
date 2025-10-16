<?php

namespace App\Http\Controllers;

use App\Exports\EssayExport;
use App\Exports\PgExport;
use App\Models\Guru;
use App\Models\Ujian;
use App\Models\Gurukelas;
use App\Models\Gurumapel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Imports\PgImport;
use App\Mail\NotifUjian;
use App\Models\DetailEssay;
use Illuminate\Support\Facades\DB;
use App\Models\DetailUjian;
use App\Models\EmailSettings;
use App\Models\EssaySiswa;
use App\Models\PgSiswa;
use App\Models\Siswa;
use App\Models\WaktuUjian;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class UjianGuruController extends Controller
{
    /**
     * Helper method to get the current Guru's ID.
     *
     * @return int
     */
    private function getGuruId()
    {
        return session()->get('id');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $guruId = $this->getGuruId();
        $guru = Guru::with(['gurukelas.kelas', 'gurumapel.mapel'])->firstWhere('id', $guruId);

        return view('guru.ujian.index', [
            'title' => 'Data Ujian',
            'plugin' => '
                <link rel="stylesheet" type="text/css" href="' . url("/assets/cbt-malela") . '/plugins/table/datatable/datatables.css">
                <link rel="stylesheet" type="text/css" href="' . url("/assets/cbt-malela") . '/plugins/table/datatable/dt-global_style.css">
                <script src="' . url("/assets/cbt-malela") . '/plugins/table/datatable/datatables.js"></script>
                <script src="https://cdn.datatables.net/fixedcolumns/4.1.0/js/dataTables.fixedColumns.min.js"></script>
            ',
            'menu' => [
                'menu' => 'ujian',
                'expanded' => 'ujian'
            ],
            'guru' => $guru,
            'ujian' => Ujian::where('guru_id', $guruId)->get()
        ]);
    }

    /**
     * Show the form for creating a new Pilihan Ganda (PG) resource.
     * Replaces the old 'create' and 'tambah_kecermatan' methods.
     *
     * @return \Illuminate\Http\Response
     */
    public function create_pg()
    {
        $guruId = $this->getGuruId();
        return view('guru.ujian.create', [
            'title' => 'Tambah Ujian Pilihan Ganda',
            'plugin' => '
                <link href="' . url("/assets/cbt-malela") . '/plugins/file-upload/file-upload-with-preview.min.css" rel="stylesheet" type="text/css" />
                <script src="' . url("/assets/cbt-malela") . '/plugins/file-upload/file-upload-with-preview.min.js"></script>
                <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
                <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
            ',
            'menu' => [
                'menu' => 'ujian',
                'expanded' => 'ujian'
            ],
            'guru' => Guru::firstWhere('id', $guruId),
            'guru_kelas' => Gurukelas::where('guru_id', $guruId)->get(),
            'guru_mapel' => Gurumapel::where('guru_id', $guruId)->get(),
        ]);
    }

    /**
     * Old 'create' method is now aliased to 'create_pg'.
     */
    public function create()
    {
        return $this->create_pg();
    }

    public function create_essay()
    {
        $guruId = $this->getGuruId();
        return view('guru.ujian.create-essay', [
            'title' => 'Tambah Ujian Essay',
            'plugin' => '
                <link href="' . url("/assets/cbt-malela") . '/plugins/file-upload/file-upload-with-preview.min.css" rel="stylesheet" type="text/css" />
                <script src="' . url("/assets/cbt-malela") . '/plugins/file-upload/file-upload-with-preview.min.js"></script>
                <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
                <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
            ',
            'menu' => [
                'menu' => 'ujian',
                'expanded' => 'ujian'
            ],
            'guru' => Guru::firstWhere('id', $guruId),
            'guru_kelas' => Gurukelas::where('guru_id', $guruId)->get(),
            'guru_mapel' => Gurumapel::where('guru_id', $guruId)->get(),
        ]);
    }

/**
     * Store a newly created Pilihan Ganda resource in storage (Manual Input).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $guruId = $this->getGuruId();
        
        // 1. Normalize and Validate Inputs
        $kelas_ids_raw = $request->kelas;
        $kelas_ids = is_array($kelas_ids_raw) ? $kelas_ids_raw : ($kelas_ids_raw ? [$kelas_ids_raw] : []);
        
        $mapel_id = is_array($request->mapel) ? head($request->mapel) : $request->mapel;
        $acak_input = is_array($request->acak) ? head($request->acak) : $request->acak;
        $acak_val = $acak_input ?? 0;

        if (empty($kelas_ids)) {
            return redirect('/guru/ujian/create')->with('pesan', "<script>
                swal({ title: 'Error!', text: 'Silakan pilih minimal satu kelas!', type: 'error', padding: '2em' })
            </script>
            ")->withInput();
        }

        // 2. Siapkan data Soal (DetailUjian) sekali di luar loop
        $soal_base_data = [];
        $nama_soal = $request->soal;

        if (!is_array($nama_soal) || empty($nama_soal)) {
             return redirect('/guru/ujian/create')->with('pesan', "<script>
                swal({ title: 'Error!', text: 'Data soal (pertanyaan) tidak ditemukan!', type: 'error', padding: '2em' })
            </script>
            ")->withInput();
        }
        
        foreach ($nama_soal as $index => $soal) {
            $soal_base_data[] = [
                'soal' => $soal,
                'pg_1' => $request->pg_1[$index],
                'pg_2' => $request->pg_2[$index],
                'pg_3' => $request->pg_3[$index],
                'pg_4' => $request->pg_4[$index],
                'pg_5' => $request->pg_5[$index],
                'jawaban' => $request->jawaban[$index]
            ];
        }

        // 3. Loop untuk setiap kelas yang dipilih
        $all_students = collect();
        $successful_insertions = false;
        
        foreach ($kelas_ids as $kelas_id) {
            $siswa_in_class = Siswa::where('kelas_id', $kelas_id)->get();

            if ($siswa_in_class->isEmpty()) {
                continue; 
            }

            $successful_insertions = true;
            $kode = Str::random(30); 
            $all_students = $all_students->merge($siswa_in_class);
            $timestamp = date('Y-m-d H:i:s'); 

            // A. Insert Ujian record
            $ujian = [
                'kode' => $kode,
                'nama' => $request->nama,
                'jenis' => 0,
                'guru_id' => $guruId,
                'kelas_id' => $kelas_id, 
                'mapel_id' => $mapel_id, 
                'jam' => $request->jam,
                'menit' => $request->menit,
                'acak' => $acak_val, 
                'tanggal_mulai' => $request->tanggal_mulai,
                'waktu_mulai' => $request->waktu_mulai,
                'created_at' => $timestamp, 
                'updated_at' => $timestamp, 
            ];
            // Ujian::insert($ujian); // Gunakan Model::insert() karena Ujian adalah record tunggal
            // Namun, untuk konsistensi, lebih baik pakai DB jika ada masalah.
            DB::table('ujian')->insert($ujian);

            // B. Insert DetailUjian (Questions) - FIX: Menggunakan DB::table()->insert()
            $detail_ujian_for_insert = array_map(function($data) use ($kode, $timestamp) {
                $data['kode'] = $kode;
                $data['created_at'] = $timestamp;
                $data['updated_at'] = $timestamp;
                return $data;
            }, $soal_base_data);
            
            DB::table('detail_ujian')->insert($detail_ujian_for_insert);
            
            // C. Insert WaktuUjian 
            $waktu_ujian = $siswa_in_class->map(function ($s) use ($kode) { 
                return [
                    'kode' => $kode,
                    'siswa_id' => $s->id,
                    'jumlah_pelanggaran' => 0,
                ];
            })->toArray();
            
            DB::table('waktu_ujian')->insert($waktu_ujian); // Ganti ke DB untuk konsistensi
        }
        
        // 4. Handle jika tidak ada ujian yang berhasil dibuat
        if (!$successful_insertions) {
            return redirect('/guru/ujian/create')->with('pesan', "<script>
                swal({ title: 'Error!', text: 'Tidak ada siswa yang ditemukan di kelas yang dipilih, Ujian gagal dibuat!', type: 'error', padding: '2em' })
            </script>
            ")->withInput();
        }

        // 5. Send Email Notification
        $email_siswa = $all_students->unique('email')->pluck('email')->implode(',');
        $email_siswa_array = explode(',', $email_siswa);

        $email_settings = EmailSettings::first();
        if ($email_settings && $email_settings->notif_ujian == '1') {
            $details = [
                'nama_guru' => session()->get('nama_guru'),
                'nama_ujian' => $request->nama,
                'jam' => $request->jam,
                'menit' => $request->menit,
            ];
            Mail::to($email_siswa_array)->send(new NotifUjian($details)); 
        }

        return redirect('/guru/ujian')->with('pesan', "<script>
            swal({ title: 'Success!', text: 'Ujian sudah diposting untuk semua kelas yang dipilih!', type: 'success', padding: '2em' })
        </script>");
    }


/**
     * Store a newly created Pilihan Ganda resource in storage (Import Excel).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function pg_excel(Request $request)
    {
        $guruId = $this->getGuruId();
        
        // 1. Normalize and Validate Inputs
        $kelas_ids_raw = $request->e_kelas;
        $kelas_ids = is_array($kelas_ids_raw) ? $kelas_ids_raw : ($kelas_ids_raw ? [$kelas_ids_raw] : []);
        
        $mapel_id = is_array($request->e_mapel) ? head($request->e_mapel) : $request->e_mapel;
        $acak_input = is_array($request->e_acak) ? head($request->e_acak) : $request->e_acak;
        $acak_val = $acak_input ?? 0;

        if (empty($kelas_ids)) {
             return redirect('/guru/ujian/create')->with('pesan', "<script>
                swal({ title: 'Error!', text: 'Silakan pilih minimal satu kelas!', type: 'error', padding: '2em' })
            </script>
            ")->withInput();
        }
        
        // 2. Loop untuk setiap kelas yang dipilih
        $all_students = collect();
        $kode_pertama = null;
        $successful_insertions = false;

        foreach ($kelas_ids as $kelas_id) {
            $siswa_in_class = Siswa::where('kelas_id', $kelas_id)->get();

            if ($siswa_in_class->isEmpty()) {
                continue;
            }

            $successful_insertions = true;
            $kode = Str::random(30); 
            $all_students = $all_students->merge($siswa_in_class);
            $timestamp = date('Y-m-d H:i:s'); 

            // A. Insert Ujian record
            $ujian = [
                'kode' => $kode,
                'nama' => $request->e_nama_ujian,
                'jenis' => 0,
                'guru_id' => $guruId,
                'kelas_id' => $kelas_id, 
                'mapel_id' => $mapel_id, 
                'jam' => $request->e_jam,
                'menit' => $request->e_menit,
                'acak' => $acak_val, 
                'tanggal_mulai' => $request->e_tanggal_mulai,
                'waktu_mulai' => $request->e_waktu_mulai,
                'created_at' => $timestamp, 
                'updated_at' => $timestamp, 
            ];
            
            DB::table('ujian')->insert($ujian);
            
            // B. Penanganan DetailUjian (Soal): Import sekali, Duplikasi untuk kelas berikutnya.
            if ($kode_pertama === null) {
                // Kelas Pertama: Lakukan import Excel
                // ASUMSI: PgImport menggunakan Model::create() atau DB::insert() yang berhasil
                Excel::import(new PgImport($kode), $request->excel);
                $kode_pertama = $kode; 
            } else {
                // Kelas Berikutnya: Duplikasi soal dari kode pertama
                
                // FIX KRITIS: Menggunakan DB::table()->get() untuk MENGHINDARI masalah Model/Eloquent 
                $soal_data = DB::table('detail_ujian')
                    ->where('kode', $kode_pertama)
                    ->select('soal', 'pg_1', 'pg_2', 'pg_3', 'pg_4', 'pg_5', 'jawaban')
                    ->get(); // Mengambil sebagai Collection of stdClass Objects
                
                if ($soal_data->isEmpty()) {
                    // Jika data soal asli kosong, artinya import Excel awal gagal.
                    // Anda bisa log error di sini, tetapi untuk saat ini kita lewati insert
                    continue; 
                }

                // Tambahkan 'kode' baru dan HAPUS kolom ID/Timestamps yang tidak perlu
                $soal_baru = $soal_data->map(function ($soal) use ($kode) {
                    $data = (array) $soal; 
                    
                    // UNTUK AMAN, kita hapus created_at/updated_at/id jika terdeteksi, 
                    // meskipun sudah dikecualikan di select.
                    unset($data['id']);
                    unset($data['created_at']);
                    unset($data['updated_at']);
                    
                    $data['kode'] = $kode;
                    return $data;
                })->toArray();
                
                // FIX: Menggunakan DB::table()->insert()
                if (!empty($soal_baru)) {
                    DB::table('detail_ujian')->insert($soal_baru); 
                }
            }
            
            // C. Insert WaktuUjian (TANPA Timestamps)
            $waktu_ujian = $siswa_in_class->map(function ($s) use ($kode) { 
                return [
                    'kode' => $kode,
                    'siswa_id' => $s->id,
                    'jumlah_pelanggaran' => 0,
                ];
            })->toArray();
            
            DB::table('waktu_ujian')->insert($waktu_ujian);
        }

        // 3. Handle jika tidak ada ujian yang berhasil dibuat
        if (!$successful_insertions) {
            return redirect('/guru/ujian/create')->with('pesan', "<script>
                swal({ title: 'Error!', text: 'Tidak ada siswa yang ditemukan di kelas yang dipilih, Ujian gagal dibuat!', type: 'error', padding: '2em' })
            </script>
            ")->withInput();
        }

        // 4. Send Email Notification
        $email_siswa = $all_students->unique('email')->pluck('email')->implode(',');
        $email_siswa_array = explode(',', $email_siswa);

        $email_settings = EmailSettings::first();
        if ($email_settings && $email_settings->notif_ujian == '1') {
            $details = [
                'nama_guru' => session()->get('nama_guru'),
                'nama_ujian' => $request->e_nama_ujian,
                'jam' => $request->e_jam,
                'menit' => $request->e_menit,
            ];
            Mail::to($email_siswa_array)->send(new NotifUjian($details));
        }

        return redirect('/guru/ujian')->with('pesan', "<script>
            swal({ title: 'Success!', text: 'Ujian sudah diposting untuk semua kelas yang dipilih!', type: 'success', padding: '2em' })
        </script>");
    }
    /**
     * Store a newly created Essay resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store_essay(Request $request)
    {
        $guruId = $this->getGuruId();
        $siswa = Siswa::where('kelas_id', $request->kelas)->get();

        if ($siswa->isEmpty()) {
            return redirect('/guru/ujian_essay')->with('pesan', "
                <script>
                    swal({
                        title: 'Error!',
                        text: 'Belum ada siswa di kelas tersebut!',
                        type: 'error',
                        padding: '2em'
                    })
                </script>
            ")->withInput();
        }

        $kode = Str::random(30);
        $ujian = [
            'kode' => $kode,
            'nama' => $request->nama,
            'jenis' => 1, // 1 for Essay
            'guru_id' => $guruId,
            'kelas_id' => $request->kelas,
            'mapel_id' => $request->mapel,
            'jam' => $request->jam,
            'menit' => $request->menit,
            'tanggal_mulai' => $request->tanggal_mulai,
            'waktu_mulai' => $request->waktu_mulai,
        ];

        $detail_ujian = [];
        $nama_soal = $request->soal;
        foreach ($nama_soal as $soal) {
            array_push($detail_ujian, [
                'kode' => $kode,
                'soal' => $soal
            ]);
        }

        $email_siswa = $siswa->pluck('email')->implode(',');
        $email_siswa_array = explode(',', $email_siswa);

        $waktu_ujian = $siswa->map(function ($s) use ($kode) {
            return [
                'kode' => $kode,
                'siswa_id' => $s->id,
                'jumlah_pelanggaran' => 0
            ];
        })->toArray();

        $email_settings = EmailSettings::first();
        if ($email_settings && $email_settings->notif_ujian == '1') {
            $details = [
                'nama_guru' => session()->get('nama_guru'),
                'nama_ujian' => $request->nama,
                'jam' => $request->jam,
                'menit' => $request->menit,
            ];
            Mail::to($email_siswa_array)->send(new NotifUjian($details));
        }

        Ujian::insert($ujian);
        DetailEssay::insert($detail_ujian);
        WaktuUjian::insert($waktu_ujian);

        return redirect('/guru/ujian')->with('pesan', "
            <script>
                swal({
                    title: 'Success!',
                    text: 'Ujian sudah diposting!',
                    type: 'success',
                    padding: '2em'
                })
            </script>
        ");
    }

    /**
     * Display the specified Pilihan Ganda resource.
     *
     * @param  \App\Models\Ujian  $ujian
     * @return \Illuminate\Http\Response
     */
    public function show(Ujian $ujian)
    {
        $guruId = $this->getGuruId();
        return view('guru.ujian.show', [
            'title' => 'Detail Ujian Pilihan Ganda',
            'plugin' => '
                <link href="' . url("/assets") . '/ew/css/style.css" rel="stylesheet" type="text/css" />
                <script src="' . url("/assets") . '/ew/js/examwizard.js"></script>
            ',
            'menu' => [
                'menu' => 'ujian',
                'expanded' => 'ujian'
            ],
            'guru' => Guru::firstWhere('id', $guruId),
            'ujian' => $ujian,
        ]);
    }

    /**
     * Display the specified Pilihan Ganda student submission.
     */
    public function pg_siswa($kode, $siswa_id)
    {
        $guruId = $this->getGuruId();
        $ujian_siswa = PgSiswa::where('kode', $kode)
            ->where('siswa_id', $siswa_id)
            ->get();

        $waktu_ujian_siswa = WaktuUjian::where('kode', $kode)
                                     ->where('siswa_id', $siswa_id)
                                     ->first();

        return view('guru.ujian.show-siswa', [
            'title' => 'Detail Ujian Siswa',
            'plugin' => '
                <link href="' . url("/assets") . '/ew/css/style.css" rel="stylesheet" type="text/css" />
                <script src="' . url("/assets") . '/ew/js/examwizard.js"></script>
            ',
            'menu' => [
                'menu' => 'ujian',
                'expanded' => 'ujian'
            ],
            'guru' => Guru::firstWhere('id', $guruId),
            'ujian_siswa' => $ujian_siswa,
            'ujian' => Ujian::firstWhere('kode', $kode),
            'siswa' => Siswa::firstWhere('id', $siswa_id),
            'waktu_ujian_siswa' => $waktu_ujian_siswa
        ]);
    }

    /**
     * Display the specified Essay resource.
     *
     * @param  \App\Models\Ujian  $ujian
     * @return \Illuminate\Http\Response
     */
    public function show_essay(Ujian $ujian)
    {
        $guruId = $this->getGuruId();
        return view('guru.ujian.show-essay', [
            'title' => 'Detail Ujian Essay',
            'plugin' => '
                <link href="' . url("/assets") . '/ew/css/style.css" rel="stylesheet" type="text/css" />
                <script src="' . url("/assets") . '/ew/js/examwizard.js"></script>
            ',
            'menu' => [
                'menu' => 'ujian',
                'expanded' => 'ujian'
            ],
            'guru' => Guru::firstWhere('id', $guruId),
            'ujian' => $ujian,
        ]);
    }

    /**
     * Display the specified Essay student submission.
     */
    public function essay_siswa($kode, $siswa_id)
    {
        $guruId = $this->getGuruId();
        $ujian_siswa = EssaySiswa::where('kode', $kode)
            ->where('siswa_id', $siswa_id)
            ->get();

        $waktu_ujian_siswa = WaktuUjian::where('kode', $kode)
                                     ->where('siswa_id', $siswa_id)
                                     ->first();

        return view('guru.ujian.show-essay-siswa', [
            'title' => 'Detail Ujian Essay Siswa',
            'plugin' => '
                <link href="' . url("/assets") . '/ew/css/style.css" rel="stylesheet" type="text/css" />
                <script src="' . url("/assets") . '/ew/js/examwizard.js"></script>
            ',
            'menu' => [
                'menu' => 'ujian',
                'expanded' => 'ujian'
            ],
            'guru' => Guru::firstWhere('id', $guruId),
            'ujian_siswa' => $ujian_siswa,
            'ujian' => Ujian::firstWhere('kode', $kode),
            'siswa' => Siswa::firstWhere('id', $siswa_id),
            'waktu_ujian_siswa' => $waktu_ujian_siswa
        ]);
    }

    /**
     * Update the score of an Essay submission.
     */
    public function nilai_essay(Request $request)
    {
        $request->validate(['id' => 'required|numeric', 'nilai' => 'required|numeric']);
        EssaySiswa::where('id', $request->id)
            ->update(['nilai' => $request->nilai]);

        return 'berhasil dinilai';
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Ujian  $ujian
     * @return \Illuminate\Http\Response
     */
    public function edit(Ujian $ujian)
    {
        // Not implemented in the original file, leaving as is.
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Ujian  $ujian
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Ujian $ujian)
    {
        // Not implemented in the original file, leaving as is.
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Ujian  $ujian
     * @return \Illuminate\Http\Response
     */
    public function destroy(Ujian $ujian)
    {
        // Transaction could be used here for data integrity.

        WaktuUjian::where('kode', $ujian->kode)
            ->delete();

        if ($ujian->jenis == 0) { // Pilihan Ganda
            DetailUjian::where('kode', $ujian->kode)
                ->delete();

            PgSiswa::where('kode', $ujian->kode)
                ->delete();
        } else { // Essay
            DetailEssay::where('kode', $ujian->kode)
                ->delete();

            EssaySiswa::where('kode', $ujian->kode)
                ->delete();
        }

        Ujian::destroy($ujian->id);

        return redirect('/guru/ujian')->with('pesan', "
            <script>
                swal({
                    title: 'Success!',
                    text: 'Ujian dihapus!',
                    type: 'success',
                    padding: '2em'
                })
            </script>
        ");
    }


    public function ujian_cetak($kode)
    {
        return view('guru.ujian.cetak-pg', [
            'ujian' => Ujian::firstWhere('kode', $kode)
        ]);
    }

    public function ujian_ekspor($kode)
    {
        $ujian = Ujian::firstWhere('kode', $kode);
        $nama_kelas = $ujian->kelas->nama_kelas;
        return Excel::download(new PgExport($ujian), "nilai-pg-kelas-$nama_kelas.xlsx");
    }

    public function essay_cetak($kode)
    {
        return view('guru.ujian.cetak-essay', [
            'ujian' => Ujian::firstWhere('kode', $kode)
        ]);
    }

    public function essay_ekspor($kode)
    {
        $ujian = Ujian::firstWhere('kode', $kode);
        $nama_kelas = $ujian->kelas->nama_kelas;
        return Excel::download(new EssayExport($ujian), "nilai-essay-kelas-$nama_kelas.xlsx");
    }

    /**
     * Replaced the redundant 'tambah_kecermatan' with a clearer 'create_pg' alias.
     */
    // public function tambah_kecermatan()
    // {
    //     return $this->create_pg();
    // }

    /**
     * BARU :: Method untuk duplikat ujian.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function duplikat(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'id_ujian' => 'required|exists:ujian,id',
            'nama' => 'required|string|max:255',
            'mapel' => 'required|exists:gurumapel,mapel_id', // Menggunakan mapel_id di gurumapel
            'kelas' => 'required|exists:gurukelas,kelas_id', // Menggunakan kelas_id di gurukelas
            'tanggal_mulai' => 'required|date',
            'waktu_mulai' => 'required|date_format:H:i',
        ]);

        $guruId = $this->getGuruId();

        // 2. Cek apakah ada siswa di kelas tujuan
        $siswa_baru = Siswa::where('kelas_id', $request->kelas)->get();
        if ($siswa_baru->isEmpty()) {
            return redirect('/guru/ujian')->with('pesan', "
                <script>
                    swal({
                        title: 'Error!',
                        text: 'Belum ada siswa di kelas tujuan!',
                        type: 'error',
                        padding: '2em'
                    })
                </script>
            ");
        }

        // 3. Ambil data ujian asli
        $ujian_asli = Ujian::find($request->id_ujian);

        // 4. Replikasi data ujian asli ke ujian baru
        $kode_baru = Str::random(30);
        $ujian_baru = $ujian_asli->replicate()->fill([
            'kode' => $kode_baru,
            'nama' => $request->nama,
            'guru_id' => $guruId, // Pastikan guru_id tetap guru yang duplikat
            'kelas_id' => $request->kelas,
            'mapel_id' => $request->mapel,
            'tanggal_mulai' => $request->tanggal_mulai,
            'waktu_mulai' => $request->waktu_mulai,
        ]);
        $ujian_baru->save();

        // 5. Duplikat soal (DetailUjian untuk PG, DetailEssay untuk Essay)
        if ($ujian_asli->jenis == 0) { // Pilihan Ganda
            $soal_asli = DetailUjian::where('kode', $ujian_asli->kode)->get();
            $soal_baru = $soal_asli->map(function ($soal) use ($kode_baru) {
                return $soal->replicate()->fill(['kode' => $kode_baru])->toArray();
            })->toArray();
            DetailUjian::insert($soal_baru);
        } else { // Essay
            $soal_asli = DetailEssay::where('kode', $ujian_asli->kode)->get();
            $soal_baru = $soal_asli->map(function ($soal) use ($kode_baru) {
                return $soal->replicate()->fill(['kode' => $kode_baru])->toArray();
            })->toArray();
            DetailEssay::insert($soal_baru);
        }

        // 6. Buat WaktuUjian untuk semua siswa di kelas baru
        $waktu_ujian_baru = $siswa_baru->map(function ($s) use ($kode_baru) {
            return [
                'kode' => $kode_baru,
                'siswa_id' => $s->id,
                'jumlah_pelanggaran' => 0
            ];
        })->toArray();
        WaktuUjian::insert($waktu_ujian_baru);

        // TODO: Kirim notifikasi email untuk ujian duplikat?

        // 7. Redirect kembali dengan pesan sukses
        return redirect('/guru/ujian')->with('pesan', "
            <script>
                swal({
                    title: 'Success!',
                    text: 'Ujian berhasil diduplikasi!',
                    type: 'success',
                    padding: '2em'
                })
            </script>
        ");
    }
}