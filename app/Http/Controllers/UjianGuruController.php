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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $guru = Guru::with(['gurukelas.kelas', 'gurumapel.mapel'])->firstWhere('id', session()->get('id'));

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
            'ujian' => Ujian::where('guru_id', session()->get('id'))->get()
        ]);
    }

    // ... (other methods remain the same)

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
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
            'guru' => Guru::firstWhere('id', session()->get('id')),
            'guru_kelas' => Gurukelas::where('guru_id', session()->get('id'))->get(),
            'guru_mapel' => Gurumapel::where('guru_id', session()->get('id'))->get(),
        ]);
    }
    public function create_essay()
    {
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
            'guru' => Guru::firstWhere('id', session()->get('id')),
            'guru_kelas' => Gurukelas::where('guru_id', session()->get('id'))->get(),
            'guru_mapel' => Gurumapel::where('guru_id', session()->get('id'))->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $siswa = Siswa::where('kelas_id', $request->kelas)->get();
        if ($siswa->count() == 0) {
            return redirect('/guru/ujian/create')->with('pesan', "
                <script>
                    swal({
                        title: 'Error!',
                        text: 'belum ada siswa di kelas tersebut!',
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
            'jenis' => 0,
            'guru_id' => session()->get('id'),
            'kelas_id' => $request->kelas,
            'mapel_id' => $request->mapel,
            'jam' => $request->jam,
            'menit' => $request->menit,
            'acak' => $request->acak,
            'tanggal_mulai' => $request->tanggal_mulai, // Tambahkan ini
            'waktu_mulai' => $request->waktu_mulai,     // Tambahkan ini
        ];

        $detail_ujian = [];
        $index = 0;
        $nama_soal =  $request->soal;
        foreach ($nama_soal as $soal) {
            array_push($detail_ujian, [
                'kode' => $kode,
                'soal' => $soal,
                'pg_1' => $request->pg_1[$index],  // Diubah: hilangkan prefix 'A. ' karena sekarang rich text (HTML)
                'pg_2' => $request->pg_2[$index],  // Diubah: hilangkan prefix 'B. '
                'pg_3' => $request->pg_3[$index],  // Diubah: hilangkan prefix 'C. '
                'pg_4' => $request->pg_4[$index],  // Diubah: hilangkan prefix 'D. '
                'pg_5' => $request->pg_5[$index],  // Diubah: hilangkan prefix 'E. '
                'jawaban' => $request->jawaban[$index]
            ]);

            $index++;
        }

        $email_siswa = '';
        $waktu_ujian = [];
        foreach ($siswa as $s) {
            $email_siswa .= $s->email . ',';

            array_push($waktu_ujian, [
                'kode' => $kode,
                'siswa_id' => $s->id,
                'jumlah_pelanggaran' => 0 // Inisialisasi
            ]);
        }

        $email_siswa = Str::replaceLast(',', '', $email_siswa);
        $email_siswa = explode(',', $email_siswa);

        $email_settings = EmailSettings::first();
        if ($email_settings->notif_ujian == '1') {
            $details = [
                'nama_guru' => session()->get('nama_guru'),
                'nama_ujian' => $request->nama,
                'jam' => $request->jam,
                'menit' => $request->menit,
            ];
            Mail::to($email_siswa)->send(new NotifUjian($details));
        }


        Ujian::insert($ujian);
        DetailUjian::insert($detail_ujian);
        WaktuUjian::insert($waktu_ujian);

        return redirect('/guru/ujian')->with('pesan', "
            <script>
                swal({
                    title: 'Success!',
                    text: 'ujian sudah di posting!',
                    type: 'success',
                    padding: '2em'
                })
            </script>
        ");
    }
    public function pg_excel(Request $request)
    {
        $siswa = Siswa::where('kelas_id', $request->e_kelas)->get();
        if ($siswa->count() == 0) {
            return redirect('/guru/ujian/create')->with('pesan', "
                <script>
                    swal({
                        title: 'Error!',
                        text: 'belum ada siswa di kelas tersebut!',
                        type: 'error',
                        padding: '2em'
                    })
                </script>
            ")->withInput();
        }

        $kode = Str::random(30);
        $ujian = [
            'kode' => $kode,
            'nama' => $request->e_nama_ujian,
            'jenis' => 0,
            'guru_id' => session()->get('id'),
            'kelas_id' => $request->e_kelas,
            'mapel_id' => $request->e_mapel,
            'jam' => $request->e_jam,
            'menit' => $request->e_menit,
            'acak' => $request->e_acak,
            'tanggal_mulai' => $request->e_tanggal_mulai, // Tambahkan ini
            'waktu_mulai' => $request->e_waktu_mulai,     // Tambahkan ini
        ];

        $email_siswa = '';
        $waktu_ujian = [];
        foreach ($siswa as $s) {
            $email_siswa .= $s->email . ',';

            array_push($waktu_ujian, [
                'kode' => $kode,
                'siswa_id' => $s->id,
                'jumlah_pelanggaran' => 0 // Inisialisasi
            ]);
        }

        $email_siswa = Str::replaceLast(',', '', $email_siswa);
        $email_siswa = explode(',', $email_siswa);

        $email_settings = EmailSettings::first();
        if ($email_settings->notif_ujian == '1') {
            $details = [
                'nama_guru' => session()->get('nama_guru'),
                'nama_ujian' => $request->e_nama_ujian,
                'jam' => $request->e_jam,
                'menit' => $request->e_menit,
            ];
            Mail::to($email_siswa)->send(new NotifUjian($details));
        }

        Ujian::insert($ujian);
        Excel::import(new PgImport($kode), $request->excel);
        WaktuUjian::insert($waktu_ujian);

        return redirect('/guru/ujian')->with('pesan', "
            <script>
                swal({
                    title: 'Success!',
                    text: 'ujian sudah di posting!',
                    type: 'success',
                    padding: '2em'
                })
            </script>
        ");
    }

    public function store_essay(Request $request)
    {
        $siswa = Siswa::where('kelas_id', $request->kelas)->get();
        if ($siswa->count() == 0) {
            return redirect('/guru/ujian_essay')->with('pesan', "
                <script>
                    swal({
                        title: 'Error!',
                        text: 'belum ada siswa di kelas tersebut!',
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
            'jenis' => 1,
            'guru_id' => session()->get('id'),
            'kelas_id' => $request->kelas,
            'mapel_id' => $request->mapel,
            'jam' => $request->jam,
            'menit' => $request->menit,
            'tanggal_mulai' => $request->tanggal_mulai, // Tambahkan ini
            'waktu_mulai' => $request->waktu_mulai,     // Tambahkan ini
        ];

        $detail_ujian = [];
        $index = 0;
        $nama_soal =  $request->soal;
        foreach ($nama_soal as $soal) {
            array_push($detail_ujian, [
                'kode' => $kode,
                'soal' => $soal
            ]);

            $index++;
        }

        $email_siswa = '';
        $waktu_ujian = [];
        foreach ($siswa as $s) {
            $email_siswa .= $s->email . ',';

            array_push($waktu_ujian, [
                'kode' => $kode,
                'siswa_id' => $s->id,
                'jumlah_pelanggaran' => 0 // Inisialisasi
            ]);
        }

        $email_siswa = Str::replaceLast(',', '', $email_siswa);
        $email_siswa = explode(',', $email_siswa);

        $email_settings = EmailSettings::first();
        if ($email_settings->notif_ujian == '1') {
            $details = [
                'nama_guru' => session()->get('nama_guru'),
                'nama_ujian' => $request->nama,
                'jam' => $request->jam,
                'menit' => $request->menit,
            ];
            Mail::to($email_siswa)->send(new NotifUjian($details));
        }

        Ujian::insert($ujian);
        DetailEssay::insert($detail_ujian);
        WaktuUjian::insert($waktu_ujian);

        return redirect('/guru/ujian')->with('pesan', "
            <script>
                swal({
                    title: 'Success!',
                    text: 'ujian sudah di posting!',
                    type: 'success',
                    padding: '2em'
                })
            </script>
        ");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Ujian  $ujian
     * @return \Illuminate\Http\Response
     */
    public function show(Ujian $ujian)
    {
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
            'guru' => Guru::firstWhere('id', session()->get('id')),
            'ujian' => $ujian,
        ]);
    }
    public function pg_siswa($kode, $siswa_id)
    {
        $ujian_siswa = PgSiswa::where('kode', $kode)
            ->where('siswa_id', $siswa_id)
            ->get();

        // Ambil data WaktuUjian untuk mendapatkan jumlah pelanggaran
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
            'guru' => Guru::firstWhere('id', session()->get('id')),
            'ujian_siswa' => $ujian_siswa,
            'ujian' => Ujian::firstWhere('kode', $kode),
            'siswa' => Siswa::firstWhere('id', $siswa_id),
            'waktu_ujian_siswa' => $waktu_ujian_siswa // Teruskan data WaktuUjian
        ]);
    }

    public function show_essay(Ujian $ujian)
    {
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
            'guru' => Guru::firstWhere('id', session()->get('id')),
            'ujian' => $ujian,
        ]);
    }
    public function essay_siswa($kode, $siswa_id)
    {
        $ujian_siswa = EssaySiswa::where('kode', $kode)
            ->where('siswa_id', $siswa_id)
            ->get();

        // Ambil data WaktuUjian untuk mendapatkan jumlah pelanggaran
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
            'guru' => Guru::firstWhere('id', session()->get('id')),
            'ujian_siswa' => $ujian_siswa,
            'ujian' => Ujian::firstWhere('kode', $kode),
            'siswa' => Siswa::firstWhere('id', $siswa_id),
            'waktu_ujian_siswa' => $waktu_ujian_siswa // Teruskan data WaktuUjian
        ]);
    }
    public function nilai_essay(Request $request)
    {
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Ujian  $ujian
     * @return \Illuminate\Http\Response
     */
    public function destroy(Ujian $ujian)
    {

        WaktuUjian::where('kode', $ujian->kode)
            ->delete();

        if ($ujian->jenis == 0) {
            DetailUjian::where('kode', $ujian->kode)
                ->delete();

            PgSiswa::where('kode', $ujian->kode)
                ->delete();
        } else {
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
                    text: 'ujian di hapus!',
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
        $ujian =  Ujian::firstWhere('kode', $kode);
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
        $ujian =  Ujian::firstWhere('kode', $kode);
        $nama_kelas = $ujian->kelas->nama_kelas;
        return Excel::download(new EssayExport($ujian), "nilai-essay-kelas-$nama_kelas.xlsx");
    }
    public function tambah_kecermatan()
    {
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
            'guru' => Guru::firstWhere('id', session()->get('id')),
            'guru_kelas' => Gurukelas::where('guru_id', session()->get('id'))->get(),
            'guru_mapel' => Gurumapel::where('guru_id', session()->get('id'))->get(),
        ]);
    }

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
        'mapel' => 'required|exists:mapel,id',
        'kelas' => 'required|exists:kelas,id',
        'tanggal_mulai' => 'required|date',
        'waktu_mulai' => 'required|date_format:H:i',
    ]);

    // 2. Cek apakah ada siswa di kelas tujuan
    $siswa_baru = Siswa::where('kelas_id', $request->kelas)->get();
    if ($siswa_baru->count() == 0) {
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
        'kelas_id' => $request->kelas,
        'mapel_id' => $request->mapel,
        'tanggal_mulai' => $request->tanggal_mulai, // Gunakan kolom baru
        'waktu_mulai' => $request->waktu_mulai, // Gunakan kolom baru
    ]);
    $ujian_baru->save();

    // 5. Duplikat soal (DetailUjian untuk PG, DetailEssay untuk Essay)
    if ($ujian_asli->jenis == 0) { // Pilihan Ganda
        $soal_asli = DetailUjian::where('kode', $ujian_asli->kode)->get();
        $soal_baru = [];
        foreach ($soal_asli as $soal) {
            $soal_baru[] = $soal->replicate()->fill(['kode' => $kode_baru])->toArray();
        }
        DetailUjian::insert($soal_baru);
    } else { // Essay
        $soal_asli = DetailEssay::where('kode', $ujian_asli->kode)->get();
        $soal_baru = [];
        foreach ($soal_asli as $soal) {
            $soal_baru[] = $soal->replicate()->fill(['kode' => $kode_baru])->toArray();
        }
        DetailEssay::insert($soal_baru);
    }

    // 6. Buat WaktuUjian untuk semua siswa di kelas baru
    $waktu_ujian_baru = [];
    foreach ($siswa_baru as $s) {
        $waktu_ujian_baru[] = [
            'kode' => $kode_baru,
            'siswa_id' => $s->id,
            'jumlah_pelanggaran' => 0 // Inisialisasi
        ];
    }
    WaktuUjian::insert($waktu_ujian_baru);

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
