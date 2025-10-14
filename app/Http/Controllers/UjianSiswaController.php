<?php

namespace App\Http\Controllers;

use App\Models\DetailEssay;
use App\Models\DetailUjian;
use App\Models\EssaySiswa;
use App\Models\Notifikasi;
use App\Models\PgSiswa;
use App\Models\Siswa;
use App\Models\TugasSiswa;
use App\Models\Ujian;
use App\Models\WaktuUjian;
use Exception;
use Illuminate\Http\Request;

class UjianSiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $notif_tugas = TugasSiswa::where('siswa_id', session()->get('id'))
            ->where('date_send', null)
            ->get();
        $notif_ujian = WaktuUjian::where('siswa_id', session()->get('id'))
            ->where('selesai', null)
            ->get();
        $ujian = WaktuUjian::where('siswa_id', session()->get('id'))
            ->orderBy('id', 'desc')
            ->get();


        return view('siswa.ujian.index', [
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
            'siswa' => Siswa::firstWhere('id', session()->get('id')),
            'notif_tugas' => $notif_tugas,
            'notif_materi' => Notifikasi::where('siswa_id', session()->get('id'))->get(),
            'notif_ujian' => $notif_ujian,
            'ujian' => $ujian
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        WaktuUjian::where('kode', $request->kode)
            ->where('siswa_id', session()->get('id'))
            ->update(['selesai' => 1]);

       return redirect('/siswa/ujian/' . $request->kode)->with('pesan', [
    'title' => 'Berhasil Terkirim! 🎉',
    'text' => 'Jawaban ujian Essay Anda telah berhasil dikirimkan.',
    'icon' => 'success',
]);

    }
    public function store_essay(Request $request)
    {
        WaktuUjian::where('kode', $request->kode)
            ->where('siswa_id', session()->get('id'))
            ->update(['selesai' => 1]);

        return redirect('/siswa/ujian_essay/' . $request->kode)->with('pesan', "
            <script>
                swal({
                    title: 'Success!',
                    text: 'ujian sudah dikerjakan!',
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
        $notif_tugas = TugasSiswa::where('siswa_id', session()->get('id'))
            ->where('date_send', null)
            ->get();
        $notif_ujian = WaktuUjian::where('siswa_id', session()->get('id'))
            ->where('selesai', null)
            ->get();

        $pg_siswa = PgSiswa::where('kode', $ujian->kode)
            ->where('siswa_id', session()->get('id'))
            ->get();

        // Inisialisasi jumlah_pelanggaran jika belum ada di waktu_ujian
        $waktu_ujian = WaktuUjian::where('kode', $ujian->kode)
            ->where('siswa_id', session()->get('id'))
            ->first();

        // Jika belum ada entry pg_siswa, buat entry baru dan set waktu berakhir
        if ($pg_siswa->count() == 0) {
            $data_pg_siswa = [];
            foreach ($ujian->detailujian as $soal) {
                array_push($data_pg_siswa, [
                    'detail_ujian_id' => $soal->id,
                    'kode' => $soal->kode,
                    'siswa_id' => session()->get('id')
                ]);
            }

            if ($ujian->acak == 1) {
                $randomize = collect($data_pg_siswa)->shuffle();
                $soal_pg_siswa = $randomize->toArray();
            } else {
                $soal_pg_siswa = $data_pg_siswa;
            }

            $timestamp = strtotime(date('Y-m-d H:i', time()));
            $waktu_berakhir =  date('Y-m-d H:i', strtotime("+$ujian->jam hour +$ujian->menit minute", $timestamp));

            // Periksa apakah $waktu_ujian sudah ada, jika tidak, buat baru atau update
            if (!$waktu_ujian) {
                 // Ini mungkin terjadi jika siswa langsung masuk ke ujian tanpa WaktuUjian
                 // Maka buat record WaktuUjian baru
                WaktuUjian::create([
                    'kode' => $ujian->kode,
                    'siswa_id' => session()->get('id'),
                    'waktu_berakhir' => $waktu_berakhir,
                    'selesai' => null,
                    'jumlah_pelanggaran' => 0 // Inisialisasi pelanggaran
                ]);
            } else {
                $waktu_ujian->update([
                    'waktu_berakhir' => $waktu_berakhir,
                    'jumlah_pelanggaran' => 0 // Reset atau inisialisasi pelanggaran
                ]);
            }

            PgSiswa::insert($soal_pg_siswa);
        }

        // Ambil ulang waktu_ujian setelah potensi pembuatan/update
        $waktu_ujian = WaktuUjian::where('kode', $ujian->kode)
            ->where('siswa_id', session()->get('id'))
            ->first();
        $pg_siswa = PgSiswa::where('kode', $ujian->kode)
            ->where('siswa_id', session()->get('id'))
            ->get();

        return view('siswa.ujian.show', [
            'title' => 'Ujian Pilihan Ganda',
            'plugin' => '
                <link href="' . url("/assets") . '/ew/css/style.css" rel="stylesheet" type="text/css" />
                <script src="' . url("/assets") . '/ew/js/examwizard.js"></script>
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" />
            ',
            'menu' => [
                'menu' => 'ujian',
                'expanded' => 'ujian'
            ],
            'siswa' => Siswa::firstWhere('id', session()->get('id')),
            'notif_tugas' => $notif_tugas,
            'notif_materi' => Notifikasi::where('siswa_id', session()->get('id'))->get(),
            'notif_ujian' => $notif_ujian,
            'ujian' => $ujian,
            'pg_siswa' => $pg_siswa,
            'waktu_ujian' => $waktu_ujian
        ]);
    }
    public function essay(Ujian $ujian)
    {
        $notif_tugas = TugasSiswa::where('siswa_id', session()->get('id'))
            ->where('date_send', null)
            ->get();
        $notif_ujian = WaktuUjian::where('siswa_id', session()->get('id'))
            ->where('selesai', null)
            ->get();

        $essay_siswa = EssaySiswa::where('kode', $ujian->kode)
            ->where('siswa_id', session()->get('id'))
            ->get();

        $waktu_ujian = WaktuUjian::where('kode', $ujian->kode)
            ->where('siswa_id', session()->get('id'))
            ->first();

        if ($essay_siswa->count() == 0) {
            $data_essay_siswa = [];
            foreach ($ujian->detailessay as $soal) {
                array_push($data_essay_siswa, [
                    'detail_ujian_id' => $soal->id,
                    'kode' => $soal->kode,
                    'siswa_id' => session()->get('id')
                ]);
            }

            $timestamp = strtotime(date('Y-m-d H:i', time()));
            $waktu_berakhir =  date('Y-m-d H:i', strtotime("+$ujian->jam hour +$ujian->menit minute", $timestamp));

            if (!$waktu_ujian) {
                WaktuUjian::create([
                    'kode' => $ujian->kode,
                    'siswa_id' => session()->get('id'),
                    'waktu_berakhir' => $waktu_berakhir,
                    'selesai' => null,
                    'jumlah_pelanggaran' => 0 // Inisialisasi pelanggaran
                ]);
            } else {
                $waktu_ujian->update([
                    'waktu_berakhir' => $waktu_berakhir,
                    'jumlah_pelanggaran' => 0 // Reset atau inisialisasi pelanggaran
                ]);
            }

            EssaySiswa::insert($data_essay_siswa);
        }

        // Ambil ulang waktu_ujian setelah potensi pembuatan/update
        $waktu_ujian = WaktuUjian::where('kode', $ujian->kode)
            ->where('siswa_id', session()->get('id'))
            ->first();
        $essay_siswa = EssaySiswa::where('kode', $ujian->kode)
            ->where('siswa_id', session()->get('id'))
            ->get();

        return view('siswa.ujian.show-essay', [
            'title' => 'Ujian Essay',
            'plugin' => '
                <link href="' . url("/assets") . '/ew/css/style.css" rel="stylesheet" type="text/css" />
                <script src="' . url("/assets") . '/ew/js/examwizard.js"></script>
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" />
            ',
            'menu' => [
                'menu' => 'ujian',
                'expanded' => 'ujian'
            ],
            'siswa' => Siswa::firstWhere('id', session()->get('id')),
            'notif_tugas' => $notif_tugas,
            'notif_materi' => Notifikasi::where('siswa_id', session()->get('id'))->get(),
            'notif_ujian' => $notif_ujian,
            'ujian' => $ujian,
            'essay_siswa' => $essay_siswa,
            'waktu_ujian' => $waktu_ujian
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Ujian  $ujian
     * @return \Illuminate\Http\Response
     */
    public function edit(Ujian $ujian)
    {
        //
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
        //
    }

    public function simpan_pg(Request $request)
    {
        $id_detail_ujian = $request->idDetail;
        $id_pg = $request->id_pg;
        $jawaban = $request->jawaban;

        $detail_ujian = DetailUjian::firstWhere('id', $id_detail_ujian);

        if ($jawaban == $detail_ujian->jawaban) {
            $benar = 1;
        } else {
            $benar = 0;
        }

        $data = [
            'jawaban' => $jawaban,
            'benar' => $benar
        ];

        try {
            PgSiswa::where('id', $id_pg)
                ->update($data);
            echo 'jawaban dikirim';
        } catch (\Exception $exeption) {
            echo $exeption->getMessage();
        }
    }
    public function ragu_pg(Request $request)
    {
        PgSiswa::where('id', $request->id_pg)
            ->update(['ragu' => $request->ragu]);
        echo 'checked ragu';
    }
    public function simpan_essay(Request $request)
    {
        $id_detail_ujian = $request->idDetail;
        $id_essay = $request->id_essay;
        $jawaban = $request->jawaban;
        try {
            EssaySiswa::where('id', $id_essay)
                ->update(['jawaban' => $jawaban]);
            echo 'jawaban dikirim';
        } catch (\Exception $exeption) {
            echo $exeption->getMessage();
        }
    }
    public function ragu_essay(Request $request)
    {
        EssaySiswa::where('id', $request->id_essay)
            ->update(['ragu' => $request->ragu]);

        echo 'checked ragu';
    }

    /**
     * Menyimpan data pelanggaran yang dilakukan siswa.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function laporkan_pelanggaran(Request $request)
    {
        $kode_ujian = $request->input('kode_ujian');
        $siswa_id = $request->input('siswa_id');
        $jenis_pelanggaran = $request->input('jenis_pelanggaran'); // Untuk logging jika diperlukan

        // Cari record waktu_ujian siswa
        $waktuUjian = WaktuUjian::where('kode', $kode_ujian)
                                ->where('siswa_id', $siswa_id)
                                ->first();

        if ($waktuUjian) {
            // Tingkatkan jumlah pelanggaran
            $waktuUjian->increment('jumlah_pelanggaran');

            return response()->json([
                'status' => 'success',
                'message' => 'Pelanggaran berhasil dicatat.',
                'total_pelanggaran' => $waktuUjian->jumlah_pelanggaran
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Data ujian atau siswa tidak ditemukan.'
            ], 404);
        }
    }
}
