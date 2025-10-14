@extends('template.main')
@section('content')
    @include('template.navbar.siswa')
    <style>
        .btn-white {
            background: #cacaca;
            color: #fff;
        }

        .hidden {
            display: none;
        }

        /* CSS BARU UNTUK WARNA BADGE */
        .badge-warning-custom {
            background-color: #ffc107;
            /* Warna kuning */
            color: #fff;
        }

        .badge-danger-custom {
            background-color: #dc3545;
            /* Warna merah */
            color: #fff;
        }

        /* AKHIR CSS BARU */

        /* CSS untuk SweetAlert2 custom */
        .swal2-popup-custom {
            border-radius: 15px !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3) !important;
        }

        .swal2-title-custom {
            font-size: 24px !important;
            font-weight: bold !important;
        }

        .swal2-html-container {
            font-size: 16px !important;
        }

        .swal2-icon-small.swal2-icon {
            width: 60px !important;
            height: 60px !important;
            font-size: 30px !important;
        }

        .swal2-icon-small.swal2-icon::before {
            font-size: 30px !important;
        }

        .swal2-cancel-primary {
            /* Membuat "Tidak, Tetap Di Sini" lebih menonjol */
            order: 1 !important;
            background-color: #28a745 !important;
            /* Warna hijau */
            border-color: #28a745 !important;
            color: #fff !important;
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Menambahkan SweetAlert2 CDN untuk alert yang lebih bagus --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div id="content" class="main-content">
        <div class="layout-px-spacing">
            <div class="row layout-top-spacing">
                <div class="col-lg-12 layout-spacing">
                    <div class="widget shadow p-3">
                        <div class="widget-heading">
                            <h5 class="">{{ $ujian->nama }}</h5>
                            <table class="mt-2">
                                <tr>
                                    <th>Kelas</th>
                                    <th>: {{ $ujian->kelas->nama_kelas }}</th>
                                </tr>
                                <tr>
                                    <th>Mapel</th>
                                    <th>: {{ $ujian->mapel->nama_mapel }}</th>
                                </tr>
                                <tr>
                                    <th>Jumlah Soal</th>
                                    <th>: {{ $ujian->detailujian->count() }} Soal</th>
                                </tr>
                                <tr>
                                    <th>Waktu Ujian</th>
                                    <th>: {{ $ujian->jam }} Jam {{ $ujian->menit }} Menit</th>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            @if ($waktu_ujian->selesai === null)
                {{-- UJIAN BERLANGSUNG --}}
                <div class="row">
                    <div class="col-lg-9">
                        <form id="examwizard-question" action="{{ url('/siswa/ujian') }}" method="POST">
                            @csrf
                            <input type="hidden" name="kode" value="{{ $ujian->kode }}">
                            <div class="widget shadow p-2">
                                <div class="d-flex float-right">
                                    <div class="badge badge-primary timer-badge"
                                        style="font-size: 18px; font-weight: bold;"><span data-feather="clock"></span> |
                                        <span class="jam_skrng">00 : 00 : 00</span>
                                    </div>
                                </div>
                                <div>
                                    @php
                                        $no = 1;
                                        $soal_hidden = '';
                                    @endphp
                                    @foreach ($pg_siswa as $soal)
                                        <div class="question {{ $soal_hidden }} question-{{ $no }}"
                                            data-question="{{ $no }}">
                                            <div class="widget-heading pl-2 pt-2"
                                                style="border-bottom: 1px solid #e0e6ed;">
                                                <div class="">
                                                    <h6 class="" style="font-weight: bold">Soal No. <span
                                                            class="badge badge-primary no-soal"
                                                            style="font-size: 1rem">{{ $no }}</span>
                                                    </h6>
                                                </div>
                                            </div>

                                            <div class="widget p-3 mt-3">
                                                <div class="widget-heading" style="border-bottom: 1px solid #e0e6ed;">
                                                    <h6 class="question-title color-green" style="word-wrap: break-word">
                                                        {!! $soal->detailujian->soal !!}
                                                    </h6>
                                                </div>
                                                <div class="widget-content mt-3" style="position: relative;">
                                                    <div class="alert alert-danger hidden"></div>
                                                    <div class="timer-check hidden"
                                                        style="position: absolute; width: 100%; height: 100%; background-color: rgba(255, 255, 255, 0.5);">
                                                        <h5
                                                            style="display: flex; justify-content: center; align-items: center; margin-top: 60px;">
                                                            <span class="badge badge-danger">Waktu Habis!</span>
                                                        </h5>
                                                    </div>
                                                    <div class="green-radio color-green">
                                                        <ol type="A" style="color: #000; margin-left: -20px;">
                                                            {{-- Opsi A --}}
                                                            <li class="answer-number">
                                                                <input type="radio" data-alternatetype="radio"
                                                                    name="{{ $soal->detailujian->id }}" value="A"
                                                                    id="soal{{ $no }}-A"
                                                                    data-pg_siswa="{{ $soal->id }}"
                                                                    data-noSoal="{{ $no }}"
                                                                    @if ($soal->jawaban == 'A') checked @endif />
                                                                <label for="soal{{ $no }}-A" class="answer-text"
                                                                    style="color: #000;">
                                                                    <span>{{ strip_tags($soal->detailujian->pg_1) }}</span>
                                                                </label>
                                                            </li>

                                                            {{-- Opsi B --}}
                                                            <li class="answer-number">
                                                                <input type="radio" data-alternatetype="radio"
                                                                    name="{{ $soal->detailujian->id }}" value="B"
                                                                    id="soal{{ $no }}-B"
                                                                    data-pg_siswa="{{ $soal->id }}"
                                                                    data-noSoal="{{ $no }}"
                                                                    @if ($soal->jawaban == 'B') checked @endif />
                                                                <label for="soal{{ $no }}-B" class="answer-text"
                                                                    style="color: #000;">
                                                                    <span>{{ strip_tags($soal->detailujian->pg_2) }}</span>
                                                                </label>
                                                            </li>

                                                            {{-- Opsi C --}}
                                                            <li class="answer-number">
                                                                <input type="radio" data-alternatetype="radio"
                                                                    name="{{ $soal->detailujian->id }}" value="C"
                                                                    id="soal{{ $no }}-C"
                                                                    data-pg_siswa="{{ $soal->id }}"
                                                                    data-noSoal="{{ $no }}"
                                                                    @if ($soal->jawaban == 'C') checked @endif />
                                                                <label for="soal{{ $no }}-C" class="answer-text"
                                                                    style="color: #000;">
                                                                    <span>{{ strip_tags($soal->detailujian->pg_3) }}</span>
                                                                </label>
                                                            </li>

                                                            {{-- Opsi D --}}
                                                            <li class="answer-number">
                                                                <input type="radio" data-alternatetype="radio"
                                                                    name="{{ $soal->detailujian->id }}" value="D"
                                                                    id="soal{{ $no }}-D"
                                                                    data-pg_siswa="{{ $soal->id }}"
                                                                    data-noSoal="{{ $no }}"
                                                                    @if ($soal->jawaban == 'D') checked @endif />
                                                                <label for="soal{{ $no }}-D" class="answer-text"
                                                                    style="color: #000;">
                                                                    <span>{{ strip_tags($soal->detailujian->pg_4) }}</span>
                                                                </label>
                                                            </li>

                                                            {{-- Opsi E --}}
                                                            <li class="answer-number">
                                                                <input type="radio" data-alternatetype="radio"
                                                                    name="{{ $soal->detailujian->id }}" value="E"
                                                                    id="soal{{ $no }}-E"
                                                                    data-pg_siswa="{{ $soal->id }}"
                                                                    data-noSoal="{{ $no }}"
                                                                    @if ($soal->jawaban == 'E') checked @endif />
                                                                <label for="soal{{ $no }}-E"
                                                                    class="answer-text" style="color: #000;">
                                                                    <span>{{ strip_tags($soal->detailujian->pg_5) }}</span>
                                                                </label>
                                                            </li>
                                                        </ol>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        @php
                                            $soal_hidden = 'hidden';
                                            $no++;
                                        @endphp
                                    @endforeach
                                </div>
                                <input type="hidden" value="1" id="currentQuestionNumber"
                                    name="currentQuestionNumber" />
                                <input type="hidden" value="{{ $ujian->detailujian->count() }}" id="totalOfQuestion"
                                    name="totalOfQuestion" />
                                <input type="hidden" value="[]" id="markedQuestion" name="markedQuestions" />
                                </div>
                        </form>

                        <div class="row">
                            <div class="col-lg-12 exams-footer">
                                <div class="row pb-3">
                                    <div class="col-sm-1 back-to-prev-question-wrapper text-center mt-3">
                                        <a href="javascript:void(0);" id="back-to-prev-question"
                                            class="btn btn-primary disabled internal-nav-button">
                                            Back
                                        </a>
                                    </div>

                                    <div class="col-sm-2 footer-question-number-wrapper text-center mt-3">
                                        <div>
                                            <span id="current-question-number-label">1</span>
                                            <span>Dari <b>{{ $ujian->detailujian->count() }}</b></span>
                                        </div>
                                        <div>
                                            Nomor Soal
                                        </div>
                                    </div>
                                    <div class="col-sm-1 go-to-next-question-wrapper text-center mt-3">
                                        <a href="javascript:void(0);" id="go-to-next-question"
                                            class="btn btn-primary internal-nav-button">
                                            Next
                                        </a>
                                    </div>

                                    <div class="col-sm-3 text-center mt-3 ragu-container">
                                        @php
                                            $no = 1;
                                            $hidden = '';
                                        @endphp
                                        @foreach ($pg_siswa as $soal)
                                            <div class="question {{ $hidden }} question-{{ $no }} ragus ragu-{{ $no }}"
                                                data-question="{{ $no }}">
                                                <a href="javascript:void(0);" class="btn btn-warning ragu-ragu-button">
                                                    <input type="checkbox" class="ragu"
                                                        id="ragu{{ $soal->detailujian->id }}"
                                                        data-id_pg="{{ $soal->id }}"
                                                        data-mark_name="{{ $soal->detailujian->id }}"
                                                        data-question="{{ $no }}"
                                                        @if ($soal->ragu !== null) checked @endif>
                                                    <label for="ragu{{ $soal->detailujian->id }}"
                                                        class="mb-0 text-white">Ragu - Ragu</label>
                                                </a>
                                            </div>
                                            @php
                                                $no++;
                                                $hidden = 'hidden';
                                            @endphp
                                        @endforeach
                                    </div>

                                </div>
                            </div>

                        </div>

                    </div>

                    <div class="col-lg-3" id="quick-access-section" class="table-responsive">
                        <div class="widget shadow p-3">
                            <div class="widget-heading pl-2 pt-2" style="border-bottom: 1px solid #e0e6ed;">
                                <h6 style="font-weight: bold;">Nomor Soal</h6>
                            </div>
                            <div class="widget-content">
                                @php
                                    $no = 1;
                                @endphp
                                @foreach ($pg_siswa as $soal)
                                    <div class="question-response-rows d-inline" data-question="{{ $no }}">
                                        <button
                                            class="btn @if ($soal->ragu == null && $soal->jawaban == null) btn-white @endif shadow mt-2 question-response-rows-value @if ($soal->jawaban !== null) btn-info @endif @if ($soal->ragu !== null) btn-warning @endif internal-nav-button"
                                            id="soalId{{ $soal->detailujian->id }}"
                                            style="width: 40px; height: 40px; font-weight: bold;">
                                            {{ $no }}
                                        </button>
                                    </div>
                                    @php
                                        $no++;
                                    @endphp
                                @endforeach
                                <div class="mt-3">
                                    <span class="badge badge-info text-info" style="padding: 0px 6px;">-</span> = Sudah
                                    dikerjakan
                                    <br>
                                    <span class="badge badge-warning text-warning" style="padding: 0px 6px;">-</span> =
                                    Ragu - Ragu
                                    <br>
                                    <span class="badge btn-white" style="color: #cacaca; padding: 0px 6px;">-</span> =
                                    Belum dikerjakan
                                </div>
                            </div>
                        </div>
                        <div class="widget shadow p-3 mt-3">
                            <button class="btn btn-primary btn-block kirim-jawaban">Kirim Jawaban</button>
                        </div>
                    </div>

                </div>
            @else
                {{-- UJIAN SELESAI --}}
                <div class="row">
                    <div class="col-lg-9">
                        <form id="examwizard-question" action="#" method="POST">
                            <div class="widget shadow p-2">
                                <div class="d-flex float-right">
                                    <div class="badge badge-success" style="font-size: 14px; font-weight: bold;">ujian
                                        selesai</div>
                                </div>
                                <div>
                                    @php
                                        $no = 1;
                                        $soal_hidden = '';
                                    @endphp
                                    @foreach ($pg_siswa as $soal)
                                        <div class="question {{ $soal_hidden }} question-{{ $no }}"
                                            data-question="{{ $no }}">
                                            <div class="widget-heading pl-2 pt-2"
                                                style="border-bottom: 1px solid #e0e6ed;">
                                                <div class="">
                                                    <h6 class="" style="font-weight: bold">Soal No. <span
                                                            class="badge badge-primary no-soal"
                                                            style="font-size: 1rem">{{ $no }}</span>
                                                    </h6>
                                                </div>
                                            </div>

                                            <div class="widget p-3 mt-3">
                                                <div class="widget-heading" style="border-bottom: 1px solid #e0e6ed;">
                                                    <h6 class="question-title color-green" style="word-wrap: break-word">
                                                        {!! $soal->detailujian->soal !!}
                                                    </h6>
                                                </div>
                                                <div class="widget-content mt-3">
                                                    <div class="alert alert-danger hidden"></div>
                                                    <div class="green-radio color-green">
                                                        <ol type="A" style="color: #000; margin-left: -20px;">
                                                            <li class="answer-number">
                                                                <label
                                                                    for="soal{{ $no }}-{{ substr($soal->detailujian->pg_1, 0, 1) }}"
                                                                    class="answer-text" style="color: #000;">
                                                                    @if (str_contains($soal->detailujian->pg_1, '<img'))
                                                                        {!! substr($soal->detailujian->pg_1, 3) !!}
                                                                    @else
                                                                        <span>{!! substr($soal->detailujian->pg_1, 3) !!}</span>
                                                                    @endif
                                                                </label>
                                                            </li>
                                                            <li class="answer-number">
                                                                <label
                                                                    for="soal{{ $no }}-{{ substr($soal->detailujian->pg_2, 0, 1) }}"
                                                                    class="answer-text" style="color: #000;">
                                                                    @if (str_contains($soal->detailujian->pg_2, '<img'))
                                                                        {!! substr($soal->detailujian->pg_2, 3) !!}
                                                                    @else
                                                                        <span>{!! substr($soal->detailujian->pg_2, 3) !!}</span>
                                                                    @endif
                                                                </label>
                                                            </li>
                                                            <li class="answer-number">
                                                                <label
                                                                    for="soal{{ $no }}-{{ substr($soal->detailujian->pg_3, 0, 1) }}"
                                                                    class="answer-text" style="color: #000;">
                                                                    @if (str_contains($soal->detailujian->pg_3, '<img'))
                                                                        {!! substr($soal->detailujian->pg_3, 3) !!}
                                                                    @else
                                                                        <span>{!! substr($soal->detailujian->pg_3, 3) !!}</span>
                                                                    @endif
                                                                </label>
                                                            </li>
                                                            <li class="answer-number">
                                                                <label
                                                                    for="soal{{ $no }}-{{ substr($soal->detailujian->pg_4, 0, 1) }}"
                                                                    class="answer-text" style="color: #000;">
                                                                    @if (str_contains($soal->detailujian->pg_4, '<img'))
                                                                        {!! substr($soal->detailujian->pg_4, 3) !!}
                                                                    @else
                                                                        <span>{!! substr($soal->detailujian->pg_4, 3) !!}</span>
                                                                    @endif
                                                                </label>
                                                            </li>
                                                            <li class="answer-number">
                                                                <label
                                                                    for="soal{{ $no }}-{{ substr($soal->detailujian->pg_5, 0, 1) }}"
                                                                    class="answer-text" style="color: #000;">
                                                                    @if (str_contains($soal->detailujian->pg_5, '<img'))
                                                                        {!! substr($soal->detailujian->pg_5, 3) !!}
                                                                    @else
                                                                        <span>{!! substr($soal->detailujian->pg_5, 3) !!}</span>
                                                                    @endif
                                                                </label>
                                                            </li>
                                                        </ol>
                                                    </div>
                                                    <div class="mt-2" style="font-weight: bold;">
                                                        Jawaban Kamu :
                                                        @if ($soal->jawaban === null)
                                                            tidak dijawab
                                                        @endif
                                                        @if ($soal->jawaban !== null)
                                                            {{ $soal->jawaban }}
                                                        @endif
                                                        @if ($soal->benar == '1')
                                                            <span class="badge badge-primary ml-1">Sudah dikerjakan</span>
                                                        @endif
                                                        @if ($soal->benar == '0')
                                                            <span class="badge badge-primary ml-1">Sudah dikerjakan</span>
                                                        @endif
                                                        @if ($soal->ragu == '1')
                                                            <span class="badge badge-warning">Ragu - Ragu</span>
                                                        @endif
                                                    </div>

                                                </div>
                                            </div>

                                        </div>

                                        @php
                                            $soal_hidden = 'hidden';
                                            $no++;
                                        @endphp
                                    @endforeach
                                </div>
                                <input type="hidden" value="1" id="currentQuestionNumber"
                                    name="currentQuestionNumber" />
                                <input type="hidden" value="{{ $ujian->detailujian->count() }}" id="totalOfQuestion"
                                    name="totalOfQuestion" />
                                <input type="hidden" value="[]" id="markedQuestion" name="markedQuestions" />
                                @php
                                    $salah = 0;
                                    $benar = 0;
                                    $tidakDijawab = 0;
                                @endphp
                                @foreach ($pg_siswa as $soal)
                                    @if ($soal->benar == '0')
                                        @php $salah++ @endphp
                                    @endif
                                    @if ($soal->benar == '1')
                                        @php $benar++ @endphp
                                    @endif
                                    @if ($soal->benar === null)
                                        @php $tidakDijawab++ @endphp
                                    @endif
                                @endforeach


                            </div>
                        </form>

                        <div class="row">
                            <div class="col-lg-12 exams-footer">
                                <div class="row pb-3">
                                    <div class="col-sm-1 back-to-prev-question-wrapper text-center mt-3">
                                        <a href="javascript:void(0);" id="back-to-prev-question"
                                            class="btn btn-primary disabled internal-nav-button">
                                            Back
                                        </a>
                                    </div>

                                    <div class="col-sm-2 footer-question-number-wrapper text-center mt-3">
                                        <div>
                                            <span id="current-question-number-label">1</span>
                                            <span>Dari <b>{{ $ujian->detailujian->count() }}</b></span>
                                        </div>
                                        <div>
                                            Nomor Soal
                                        </div>
                                    </div>
                                    <div class="col-sm-1 go-to-next-question-wrapper text-center mt-3">
                                        <a href="javascript:void(0);" id="go-to-next-question"
                                            class="btn btn-primary internal-nav-button">
                                            Next
                                        </a>
                                    </div>

                                </div>
                            </div>

                        </div>

                    </div>

                    <div class="col-lg-3" id="quick-access-section" class="table-responsive">
                        <div class="widget shadow p-3">
                            <div class="widget-heading pl-2 pt-2" style="border-bottom: 1px solid #e0e6ed;">
                                <h6 style="font-weight: bold;">Nomor Soal</h6>
                            </div>
                            <div class="widget-content">
                                @php
                                    $no = 1;
                                @endphp
                                @foreach ($pg_siswa as $soal)
                                    <div class="question-response-rows d-inline" data-question="{{ $no }}">
                                        <button
                                            class="btn @if ($soal->ragu == null && $soal->jawaban == null) btn-white @endif shadow mt-2 question-response-rows-value @if ($soal->jawaban !== null) btn-info @endif @if ($soal->ragu !== null) btn-warning @endif internal-nav-button"
                                            id="soalId{{ $soal->detailujian->id }}"
                                            style="width: 40px; height: 40px; font-weight: bold;">
                                            {{ $no }}
                                        </button>
                                    </div>
                                    @php
                                        $no++;
                                    @endphp
                                @endforeach
                                <div class="mt-3">
                                    <span class="badge badge-info text-info" style="padding: 0px 6px;">-</span> = Sudah
                                    dikerjakan
                                    <br>
                                    <span class="badge badge-warning text-warning" style="padding: 0px 6px;">-</span> =
                                    Ragu - Ragu
                                    <br>
                                    <span class="badge btn-white" style="color: #cacaca; padding: 0px 6px;">-</span> =
                                    Belum dikerjakan
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            @endif

        </div>
        {{-- MENGGANTI session('pesan') DENGAN SWEETALERT --}}
        @if (session('pesan'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const pesan = {!! json_encode(session('pesan')) !!};
                    const match = pesan.match(/Swal\.fire\({[^}]*title: '([^']*)',[^}]*text: '([^']*)',[^}]*icon: '([^']*)'/);
                    if (match) {
                        const [, title, text, icon] = match;
                        Swal.fire({
                            title: title,
                            text: text,
                            icon: icon,
                            confirmButtonText: 'OK',
                            customClass: {
                                popup: 'swal2-popup-custom',
                                title: 'swal2-title-custom',
                            }
                        });
                    }
                });
            </script>
        @endif
        @include('error.ew-s-p')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log("[v3] DOM loaded, initializing enhanced exam monitoring.");

            // Cek apakah ujian masih berlangsung
            const ujianSelesai =
                @if ($waktu_ujian->selesai === null)
                    false
                @else
                    true
                @endif ;

            // Hanya jalankan monitoring jika ujian belum selesai
            if (!ujianSelesai) {
                let jumlahPelanggaran = {{ $waktu_ujian->jumlah_pelanggaran ?? 0 }};
                let isFirstLoad = true;
                let isSubmittingForm = false;
                let isAlertActive = false;
                let isExiting = false;
                let isConfirmingExit = false;
                let isInternalNavigation = false; // Flag untuk navigasi antar soal & aksi aman

                const originalUrl = window.location.href;
                const examPath = window.location.pathname;
                let lastPath = examPath;
                let lastFocusTime = Date.now();

                // Fungsi untuk menandai bahwa navigasi internal (pindah soal/aksi aman) sedang terjadi
                function setInternalNavigation(duration = 500) {
                    isInternalNavigation = true;
                    setTimeout(() => {
                        isInternalNavigation = false;
                    }, duration);
                }

                // 1. Tambahkan listener ke tombol navigasi internal (Back, Next, Nomor Soal)
                document.querySelectorAll('.internal-nav-button').forEach(button => {
                    button.addEventListener('click', function() {
                        // Tandai sebagai navigasi internal sebelum operasi navigasi soal JS dieksekusi
                        setInternalNavigation();
                    });
                });

                // **PERBAIKAN:** Tambahkan listener untuk tombol Ragu-Ragu
                document.querySelectorAll('.ragu-ragu-button').forEach(button => {
                    button.addEventListener('click', function(e) {
                        // Menggunakan setInternalNavigation() agar aksi ragu-ragu tidak dianggap pelanggaran jika terjadi di dekat aksi blur/link click lainnya
                        setInternalNavigation();
                        // Lanjutkan dengan logika ragu-ragu (checkbox/ajax update)
                    });
                });

                // 2. Intercept clicks on links to show exit confirmation, EXCLUDING internal-nav-button
                document.addEventListener('click', function(e) {
                    // Cek jika elemen yang diklik adalah link (A tag) dan bukan tombol internal navigasi
                    if (e.target.closest('a') && e.target.closest('a').href &&
                        !e.target.closest('a').classList.contains('internal-nav-button') &&
                        !e.target.closest('a').classList.contains('ragu-ragu-button') && // Tambahkan pengecualian ragu-ragu
                        !isSubmittingForm && !isAlertActive && !isConfirmingExit && !
                        isInternalNavigation) {
                        e.preventDefault();
                        console.log("[v3] External link click intercepted, showing exit confirmation");
                        showExitConfirmation('link_click', e.target.closest('a').href);
                    }
                });

                // 3. Konfirmasi Keluar (SweetAlert)
                function showExitConfirmation(exitType, targetUrl = null) {
                    if (isConfirmingExit) return;

                    isConfirmingExit = true;
                    isAlertActive = true;

                    Swal.fire({
                        title: '⚠️ Konfirmasi Keluar',
                        html: `
                    <div style="text-align: left; font-size: 16px;">
                        <p><strong>Anda akan meninggalkan halaman ujian!</strong></p>
                        <p style="color: #dc3545;">⚠️ <strong>Peringatan:</strong> Jika Anda memilih "Ya, Keluar", ini akan dicatat sebagai pelanggaran.</p>
                        <p style="color: #28a745;">✅ <strong>Pilihan Aman:</strong> Pilih "Tidak, Tetap Di Sini" untuk menghindari pelanggaran.</p>
                        <p>📊 Total pelanggaran saat ini: <span style="color: #dc3545; font-weight: bold;">${jumlahPelanggaran} kali</span></p>
                    </div>
                `,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#28a745',
                        confirmButtonText: 'Ya, Keluar (Tercatat Pelanggaran)',
                        cancelButtonText: 'Tidak, Tetap Di Sini',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        reverseButtons: true,
                        customClass: {
                            popup: 'swal2-popup-custom',
                            title: 'swal2-title-custom',
                            icon: 'swal2-icon-small',
                            cancelButton: 'swal2-cancel-primary'
                        },
                        showClass: {
                            popup: 'animate__animated animate__shakeX'
                        }
                    }).then((result) => {
                        isConfirmingExit = false;
                        isAlertActive = false;

                        if (result.isConfirmed) {
                            console.log("[v3] User confirmed exit, reporting violation and proceeding");
                            isExiting = true;
                            laporkanPelanggaran(exitType === 'navigation_attempt' ? 'navigation_attempt' :
                                'url_change', true).then(() => {
                                if (targetUrl) {
                                    window.location.href = targetUrl;
                                } else if (exitType === 'navigation_attempt') {
                                    history.back(); // Jika tombol back/forward ditekan
                                }
                            });
                        } else {
                            console.log("[v3] User chose to stay, no violation recorded");
                            // Pastikan tetap di halaman yang benar setelah membatalkan navigasi browser
                            history.pushState(null, null, originalUrl);
                        }
                    });
                }

                // 4. Monitoring Perubahan URL, History, dan Fokus Jendela
                history.pushState(null, null, originalUrl);
                window.addEventListener('popstate', function(event) {
                    event.preventDefault();
                    if (!isSubmittingForm && !isAlertActive && !isExiting && !isConfirmingExit && !
                        isInternalNavigation) {
                        console.log(
                            "[v3] Popstate detected (back/forward attempt), showing exit confirmation");
                        showExitConfirmation('navigation_attempt');
                    }
                    history.pushState(null, null, originalUrl);
                });

                // Deteksi perubahan URL yang lebih kuat (polling setiap 500ms)
                setInterval(() => {
                    const currentPath = window.location.pathname;
                    if (!isSubmittingForm && !isAlertActive && !isExiting && !isConfirmingExit && !
                        isInternalNavigation && currentPath !== lastPath) {
                        console.log("[v3] URL change detected via polling. New path:", currentPath);
                        if (currentPath !== examPath) {
                            showExitConfirmation('url_change');
                        }
                        lastPath = currentPath;
                    }
                }, 500);

                window.addEventListener('blur', function() {
                    lastFocusTime = Date.now();
                });

                window.addEventListener('focus', function() {
                    const focusLostDuration = Date.now() - lastFocusTime;
                    if (focusLostDuration > 2000 && !isSubmittingForm && !isAlertActive && !isExiting && !
                        isFirstLoad && !isConfirmingExit && !isInternalNavigation) {
                        console.log("[v3] Focus lost for more than 2 seconds, reporting violation");
                        laporkanPelanggaran('window_blur');
                    }
                    isFirstLoad = false;
                });

                document.addEventListener('visibilitychange', function() {
                    if (document.hidden && !isSubmittingForm && !isAlertActive && !isExiting && !
                        isConfirmingExit && !isInternalNavigation) {
                        if (!isFirstLoad) {
                            console.log("[v3] User keluar dari tab, melaporkan pelanggaran");
                            laporkanPelanggaran('keluar_tab');
                        }
                    } else {
                        if (isFirstLoad) {
                            isFirstLoad = false;
                        }
                    }
                });

                // 5. Penanganan Pelanggaran
                function laporkanPelanggaran(jenisPelanggaran = 'keluar_tab', force = false) {
                    return new Promise((resolve, reject) => {
                        // Tambahkan pengecualian untuk navigasi internal
                        if ((isSubmittingForm || isAlertActive || isExiting || isConfirmingExit ||
                                isInternalNavigation) && !force) {
                            resolve();
                            return;
                        }

                        console.log("[v3] Melaporkan pelanggaran:", jenisPelanggaran);
                        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content');

                        fetch('{{ route('siswa.ujian.laporkan_pelanggaran') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken
                                },
                                body: JSON.stringify({
                                    kode_ujian: '{{ $ujian->kode }}',
                                    siswa_id: {{ session()->get('id') }},
                                    jenis_pelanggaran: jenisPelanggaran
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.status === 'success') {
                                    jumlahPelanggaran = data.total_pelanggaran;
                                    let warningMessage = 'Pelanggaran terdeteksi! Mohon tetap di halaman ujian.';
                                    // ... (logika warningMessage jika ada) ...

                                    if (!isConfirmingExit) {
                                        isAlertActive = true;
                                        Swal.fire({
                                            title: '⚠️ Pelanggaran Tercatat!',
                                            html: `
                                        <div style="text-align: left; font-size: 16px;">
                                            <p><strong>${warningMessage}</strong></p>
                                            <p>📊 Total pelanggaran: <span style="color: #dc3545; font-weight: bold;">${jumlahPelanggaran} kali</span></p>
                                            <p style="color: #dc3545;">⚠️ <strong>Peringatan:</strong> Terlalu banyak pelanggaran dapat mengakibatkan ujian Anda dibatalkan!</p>
                                            <p style="color: #28a745;">💡 <strong>Tips:</strong> Tetap fokus pada halaman ujian ini!</p>
                                        </div>
                                    `,
                                            icon: 'warning',
                                            confirmButtonText: 'Saya Mengerti',
                                            confirmButtonColor: '#3085d6',
                                            allowOutsideClick: false,
                                            allowEscapeKey: false,
                                            customClass: {
                                                popup: 'swal2-popup-custom',
                                                title: 'swal2-title-custom',
                                                icon: 'swal2-icon-small'
                                            },
                                            showClass: {
                                                popup: 'animate__animated animate__shakeX'
                                            }
                                        }).then(() => {
                                            isAlertActive = false;
                                        });
                                    }
                                }
                                resolve(data);
                            })
                            .catch(error => {
                                console.error('[v3] Error melaporkan pelanggaran:', error);
                                reject(error);
                            });
                    });
                }

                // 6. Beforeunload dan Pagehide
                window.addEventListener('beforeunload', function(e) {
                    if (!isSubmittingForm && !isAlertActive && !isConfirmingExit && !isInternalNavigation) {
                        e.preventDefault();
                        e.returnValue =
                            'Anda yakin ingin meninggalkan halaman ujian? Ini akan dihitung sebagai pelanggaran!';
                        // Gunakan sendBeacon untuk pelaporan, karena fetch/ajax tidak menjamin terkirim di beforeunload
                        laporkanPelanggaranSendBeacon('close_attempt');
                        return e.returnValue;
                    }
                });

                function laporkanPelanggaranSendBeacon(jenisPelanggaran) {
                    const data = JSON.stringify({
                        kode_ujian: '{{ $ujian->kode }}',
                        siswa_id: {{ session()->get('id') }},
                        jenis_pelanggaran: jenisPelanggaran
                    });
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content');

                    if (navigator.sendBeacon) {
                        // Menggunakan sendBeacon untuk transfer data yang handal saat unload
                        const blob = new Blob([data], { type: 'application/json' });
                        // Perlu mengubah route jika server butuh CSRF di payload/query, tapi untuk sendBeacon, header sulit
                        // Sebagai alternatif, kita kirim ke endpoint yang bisa handle CSRF via cookie atau non-CSRF (jika memungkinkan)
                        // Untuk kasus ini, karena kita tidak bisa set header X-CSRF-TOKEN di sendBeacon dengan mudah,
                        // kita akan bergantung pada pagehide/fetch keepalive, atau endpoint tanpa CSRF untuk sendBeacon.
                        // Namun, karena ini Laravel, kita coba kirim tanpa CSRF token di payload/blob, dengan harapan Laravel mengurusnya dari cookie session.
                        navigator.sendBeacon('{{ route('siswa.ujian.laporkan_pelanggaran') }}', blob);
                    } else {
                        // Fallback untuk browser lama
                        fetch('{{ route('siswa.ujian.laporkan_pelanggaran') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: data,
                            keepalive: true
                        }).catch(console.error);
                    }
                }

                window.addEventListener('pagehide', function(e) {
                    if (!isSubmittingForm && !isAlertActive && !isConfirmingExit && !isInternalNavigation) {
                        laporkanPelanggaranSendBeacon('page_close');
                    }
                });

                // 7. Timer & Submit Handler (disederhanakan)
                const waktuBerakhirString = "{{ $waktu_ujian->waktu_berakhir }}";
                const waktuBerakhir = new Date(waktuBerakhirString).getTime();

                function updateTimerBadgeColor() {
                    const waktuSekarang = new Date().getTime();
                    const sisaWaktu = waktuBerakhir - waktuSekarang;
                    const sisaMenit = Math.floor(sisaWaktu / (1000 * 60));

                    const timerBadge = document.querySelector('.timer-badge');
                    if (timerBadge) {
                        timerBadge.classList.remove('badge-primary', 'badge-warning-custom', 'badge-danger-custom');

                        if (sisaWaktu <= 0) {
                            clearInterval(timerInterval);
                            timerBadge.classList.add('badge-danger-custom');
                            // Panggil fungsi untuk submit otomatis jika waktu habis
                            if (!isSubmittingForm) {
                                isSubmittingForm = true;
                                document.getElementById('examwizard-question').submit();
                            }
                        } else if (sisaMenit <= 10) {
                            timerBadge.classList.add('badge-danger-custom');
                        } else if (sisaMenit <= 20) {
                            timerBadge.classList.add('badge-warning-custom');
                        } else {
                            timerBadge.classList.add('badge-primary');
                        }
                    }
                }

                const timerInterval = setInterval(updateTimerBadgeColor, 1000);
                updateTimerBadgeColor();

                const submitButton = document.querySelector('.kirim-jawaban');
                if (submitButton) {
                    submitButton.addEventListener('click', function(e) {
                        e.preventDefault();
                        isSubmittingForm = true;
                        isAlertActive = true;
                        Swal.fire({
                            title: 'Konfirmasi Pengiriman',
                            html: `
                        <div style="text-align: left; font-size: 16px;">
                            <p><strong>Apakah Anda yakin ingin mengirim jawaban?</strong></p>
                            <p>⚠️ Setelah dikirim, Anda tidak dapat mengubah jawaban lagi.</p>
                        </div>
                    `,
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Ya, Kirim Jawaban!',
                            cancelButtonText: 'Batal',
                            allowOutsideClick: false,
                            allowEscapeKey: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                Swal.fire({
                                    title: 'Mengirim Jawaban...',
                                    text: 'Mohon tunggu, jawaban Anda sedang diproses.',
                                    allowOutsideClick: false,
                                    allowEscapeKey: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                    }
                                });
                                document.getElementById('examwizard-question').submit();
                            } else {
                                isSubmittingForm = false;
                                isAlertActive = false;
                            }
                        });
                    });
                }


                // 8. Initial Warning (disimpan di akhir untuk eksekusi)
                setTimeout(function() {
                    isAlertActive = true;
                    Swal.fire({
                        title: '🔒 Sistem Monitoring Ujian Aktif',
                        html: `
                    <div style="text-align: left; font-size: 16px;">
                        <p><strong>Ujian akan segera dimulai dengan sistem monitoring tingkat lanjut.</strong></p>
                        <p><strong>✅ Aksi Aman (Tidak Ada Pelanggaran):</strong></p>
                        <ul style="text-align: left; margin-left: 20px;">
                            <li>Pindah soal (Next/Back/Nomor Soal)</li>
                            <li>Mengklik tombol "Ragu - Ragu"</li>
                        </ul>
                        <p><strong>🚫 TETAP DILARANG:</strong></p>
                        <ul style="text-align: left; margin-left: 20px;">
                            <li>Berganti tab atau aplikasi</li>
                            <li>Menggunakan shortcut keyboard</li>
                            <li>Mencoba keluar dari halaman (akan memicu konfirmasi dan pelanggaran)</li>
                        </ul>
                        <p>📊 Jumlah pelanggaran saat ini: <span style="color: #28a745; font-weight: bold;">${jumlahPelanggaran} kali</span></p>
                        <p style="color: #007bff;">💡 <strong>Tips:</strong> Pilih "Tetap Di Sini" jika tidak sengaja mencoba keluar untuk menghindari pelanggaran.</p>
                    </div>
                `,
                        icon: 'info',
                        iconColor: '#3085d6',
                        confirmButtonText: 'Saya Mengerti, Mulai Ujian',
                        confirmButtonColor: '#28a745',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        customClass: {
                            popup: 'swal2-popup-custom',
                            icon: 'swal2-icon-small'
                        },
                        showClass: {
                            popup: 'animate__animated animate__fadeInDown'
                        }
                    }).then(() => {
                        isAlertActive = false;
                    });
                }, 1000);
            }
        });
    </script>
@endsection