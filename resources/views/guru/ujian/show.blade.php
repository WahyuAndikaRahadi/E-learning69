@extends('template.main')
@section('content')
    @include('template.navbar.guru')

    <!--  BEGIN CONTENT AREA  -->
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
            {{-- soal ujian & jawaban --}}
            <div id="toggleAccordion" class="shadow">
                <div class="card">
                    <div class="card-header bg-white" id="...">
                        <section class="mb-0 mt-0">
                            <div role="menu" class="" data-toggle="collapse" data-target="#defaultAccordionOne"
                                aria-expanded="true" aria-controls="defaultAccordionOne" style="cursor: pointer;">
                                Soal Ujian & Jawaban (Klik untuk lihat & tutup)
                            </div>
                        </section>
                    </div>

                    <div id="defaultAccordionOne" class="collapse show" aria-labelledby="..."
                        data-parent="#toggleAccordion">
                        <div class="card-body">

                            <div class="row">
                                <div class="col-lg-9">
                                    <form id="examwizard-question" action="#" method="POST">
                                        <div class="widget shadow p-2">
                                            <div>
                                                @php
                                                    $no = 1;
                                                    $soal_hidden = '';
                                                    $siswa_selesai_count = $ujian->waktuujian
                                                        ->whereNotNull('selesai')
                                                        ->count();
                                                @endphp
                                                @foreach ($ujian->detailujian as $soal)
                                                    <div class="question <?= $soal_hidden ?> question-{{ $no }}"
                                                        data-question="{{ $no }}">
                                                        <div class="widget-heading pl-2 pt-2"
                                                            style="border-bottom: 1px solid #e0e6ed;">
                                                            <div class="">
                                                                <h6 class="" style="font-weight: bold">Soal No. <span
                                                                        class="badge badge-primary no-soal">{{ $no }}</span>
                                                                </h6>
                                                            </div>
                                                        </div>

                                                        <div class="widget p-3 mt-3">
                                                            <div class="widget-heading"
                                                                style="border-bottom: 1px solid #e0e6ed;">
                                                                <h6 class="question-title color-green"
                                                                    style="word-wrap: break-word">
                                                                    {!! $soal->soal !!}
                                                                </h6>
                                                            </div>
                                                            <div class="widget-content mt-3">
                                                                <div class="alert alert-danger hidden"></div>
                                                                <div class="green-radio color-green">
                                                                    <ol type="A"
                                                                        style="color: #000; margin-left: -20px;">
                                                                        <li class="answer-number">
                                                                            {{-- Label A secara manual --}}
                                                                            <label for="answer-{{ $soal->id }}-A"
                                                                                class="answer-text" style="color: #000;">
                                                                                @if (str_contains($soal->pg_1, '<img'))
                                                                                    {!! $soal->pg_1 !!}
                                                                                    {{-- Hapus substr(..., 3) --}}
                                                                                @else
                                                                                    <span>{!! $soal->pg_1 !!}</span>
                                                                                    {{-- Hapus substr(..., 3) --}}
                                                                                @endif
                                                                            </label>
                                                                        </li>
                                                                        <li class="answer-number">
                                                                            {{-- Label B secara manual --}}
                                                                            <label for="answer-{{ $soal->id }}-B"
                                                                                class="answer-text" style="color: #000;">
                                                                                @if (str_contains($soal->pg_2, '<img'))
                                                                                    {!! $soal->pg_2 !!}
                                                                                    {{-- Hapus substr(..., 3) --}}
                                                                                @else
                                                                                    <span>{!! $soal->pg_2 !!}</span>
                                                                                    {{-- Hapus substr(..., 3) --}}
                                                                                @endif
                                                                            </label>
                                                                        </li>
                                                                        <li class="answer-number">
                                                                            {{-- Label C secara manual --}}
                                                                            <label for="answer-{{ $soal->id }}-C"
                                                                                class="answer-text" style="color: #000;">
                                                                                @if (str_contains($soal->pg_3, '<img'))
                                                                                    {!! $soal->pg_3 !!}
                                                                                    {{-- Hapus substr(..., 3) --}}
                                                                                @else
                                                                                    <span>{!! $soal->pg_3 !!}</span>
                                                                                    {{-- Hapus substr(..., 3) --}}
                                                                                @endif
                                                                            </label>
                                                                        </li>
                                                                        <li class="answer-number">
                                                                            {{-- Label D secara manual --}}
                                                                            <label for="answer-{{ $soal->id }}-D"
                                                                                class="answer-text" style="color: #000;">
                                                                                @if (str_contains($soal->pg_4, '<img'))
                                                                                    {!! $soal->pg_4 !!}
                                                                                    {{-- Hapus substr(..., 3) --}}
                                                                                @else
                                                                                    <span>{!! $soal->pg_4 !!}</span>
                                                                                    {{-- Hapus substr(..., 3) --}}
                                                                                @endif
                                                                            </label>
                                                                        </li>
                                                                        <li class="answer-number">
                                                                            {{-- Label E secara manual --}}
                                                                            <label for="answer-{{ $soal->id }}-E"
                                                                                class="answer-text" style="color: #000;">
                                                                                @if (str_contains($soal->pg_5, '<img'))
                                                                                    {!! $soal->pg_5 !!}
                                                                                    {{-- Hapus substr(..., 3) --}}
                                                                                @else
                                                                                    <span>{!! $soal->pg_5 !!}</span>
                                                                                    {{-- Hapus substr(..., 3) --}}
                                                                                @endif
                                                                            </label>
                                                                        </li>
                                                                    </ol>
                                                                </div>

                                                                <hr>
                                                                <div class="mt-3">
                                                                    <h6 class="text-center">Analisis Jawaban Siswa per Soal
                                                                    </h6>
                                                                    <canvas id="chart-soal-{{ $no }}"></canvas>
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
                                            <!-- SOAL -->

                                            <input type="hidden" value="1" id="currentQuestionNumber"
                                                name="currentQuestionNumber" />
                                            <input type="hidden" value="{{ $ujian->detailujian->count() }}"
                                                id="totalOfQuestion" name="totalOfQuestion" />
                                            <input type="hidden" value="[]" id="markedQuestion"
                                                name="markedQuestions" />
                                            <!-- END SOAL -->
                                        </div>
                                    </form>

                                </div>

                                <div class="col-lg-3" id="quick-access-section" class="table-responsive">
                                    <div class="widget shadow p-3">
                                        <div class="widget-content">
                                            <table class="table text-center table-hover">
                                                <thead class="question-response-header">
                                                    <tr>
                                                        <th class="text-center">No. Soal</th>
                                                        <th class="text-center">Jawaban</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $no = 1;
                                                    @endphp
                                                    @foreach ($ujian->detailujian as $soal)
                                                        <tr class="question-response-rows"
                                                            data-question="{{ $no }}" style="cursor: pointer;">
                                                            <td style="font-weight: bold;">{{ $no }}</td>
                                                            <td class="question-response-rows-value">{{ $soal->jawaban }}
                                                            </td>
                                                        </tr>
                                                        @php
                                                            $no++;
                                                        @endphp
                                                    @endforeach

                                                </tbody>
                                            </table>
                                            <div class="text-nowrap text-center">
                                                <a href="javascript:void(0)" class="btn btn-success"
                                                    id="quick-access-prev">
                                                    &laquo;
                                                </a>
                                                <span class="alert alert-info" id="quick-access-info"></span>
                                                <a href="javascript:void(0)" class="btn btn-success"
                                                    id="quick-access-next">&raquo;</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <!-- Exmas Footer - Multi Step Pages Footer -->
                            <div class="row mt-3">
                                <div class="col-lg-12 exams-footer p-3">
                                    <div class="row">
                                        <div class="col-sm-1 back-to-prev-question-wrapper text-center">
                                            <a href="javascript:void(0);" id="back-to-prev-question"
                                                class="btn btn-success disabled">
                                                Back
                                            </a>
                                        </div>
                                        <div class="col-sm-2 footer-question-number-wrapper text-center">
                                            <div>
                                                <span id="current-question-number-label">1</span>
                                                <span>Dari <b>{{ $ujian->detailujian->count() }}</b></span>
                                            </div>
                                            <div>
                                                Nomor Soal
                                            </div>
                                        </div>
                                        <div class="col-sm-1 go-to-next-question-wrapper text-center">
                                            <a href="javascript:void(0);" id="go-to-next-question"
                                                class="btn btn-success">
                                                Next
                                            </a>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>
            </div>


            {{-- Ujian siswa & nilai --}}
            <div id="iconsAccordion" class="accordion-icons shadow mt-3">
                <div class="card">
                    <div class="card-header bg-white" id="...">
                        <section class="mb-0 mt-0">
                            <div role="menu" class="" data-toggle="collapse" data-target="#iconAccordionOne"
                                aria-expanded="true" aria-controls="iconAccordionOne" style="cursor: pointer;">
                                Nilai Siswa (Klik untuk lihat & tutup)
                            </div>
                        </section>
                    </div>

                    <div id="iconAccordionOne" class="collapse show" aria-labelledby="..."
                        data-parent="#iconsAccordion">
                        <div class="card-body">

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="widget p-3 shadow">
                                        <div class="widget-heading pl-2 pb-2" style="border-bottom: 1px solid #e0e6ed;">
                                            Nilai Siswa
                                        </div>

                                        <div class="widget-content pt-3">
                                            <a href="{{ url('/guru/ujian_cetak/' . $ujian->kode) }}"
                                                class="btn btn-info btn-sm" target="_blank"><span
                                                    data-feather="printer"></span> Cetak</a>
                                            <a href="{{ url('/guru/ujian_ekspor/' . $ujian->kode) }}"
                                                class="btn btn-success btn-sm" target="_blank"><span
                                                    data-feather="file-text"></span> Ekspor Excel</a>
                                            <div class="table-responsive mt-3">
                                                <table class="table table-bordered text-nowrap">
                                                    <thead>
                                                        <tr class="text-center">
                                                            <th>Nama Siswa</th>
                                                            <th>Benar</th>
                                                            <th>Salah</th>
                                                            <th>Tidak Dijawab</th>
                                                            <th>Pelanggaran</th> {{-- Kolom baru untuk pelanggaran --}}
                                                            <th>Nilai</th>
                                                            <th>opsi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($ujian->waktuujian as $s)
                                                            @if ($s->selesai == null)
                                                                <tr class="text-center">
                                                                    <td>{{ $s->siswa->nama_siswa }}</td>
                                                                    <td colspan="6">Belum Mengerjakan Ujian</td>
                                                                    {{-- Tambah colspan --}}
                                                                </tr>
                                                            @else
                                                                @php
                                                                    $total_soal_pg = $ujian->detailujian->count();
                                                                    $salah = 0;
                                                                    $benar = 0;
                                                                    $tidakDijawab = 0;
                                                                @endphp
                                                                @foreach ($s->pgsiswa as $jawaban)
                                                                    @if ($jawaban->kode == $ujian->kode)
                                                                        @if ($jawaban->benar == '0')
                                                                            @php $salah++ @endphp
                                                                        @endif
                                                                        @if ($jawaban->benar == '1')
                                                                            @php $benar++ @endphp
                                                                        @endif
                                                                        @if ($jawaban->benar === null)
                                                                            @php $tidakDijawab++ @endphp
                                                                        @endif
                                                                    @endif
                                                                @endforeach
                                                                <tr class="text-center">
                                                                    <td>{{ $s->siswa->nama_siswa }}</td>
                                                                    <td>{{ $benar }}</td>
                                                                    <td>{{ $salah }}</td>
                                                                    <td>{{ $tidakDijawab }}</td>
                                                                    <td>{{ $s->jumlah_pelanggaran ?? 0 }}</td>
                                                                    {{-- Menampilkan jumlah pelanggaran --}}
                                                                    <td>
                                                                        @php
                                                                            $nilai = ($benar / $total_soal_pg) * 100;
                                                                        @endphp
                                                                        {{ round($nilai) }} / 100
                                                                    </td>
                                                                    <td>
                                                                        <a href="{{ url('/guru/ujian/' . $ujian->kode . '/' . $s->siswa->id) }}"
                                                                            class="btn btn-info btn-sm"><span
                                                                                data-feather="eye"></span></a>
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-lg-12">
                                    <div class="widget p-3 shadow">
                                        <div class="widget-heading pl-2 pb-2" style="border-bottom: 1px solid #e0e6ed;">
                                            Analisis Keseluruhan Ujian
                                        </div>
                                        <div class="widget-content pt-3">
                                            <h6 class="text-center">Tren Jawaban per Soal</h6>
                                            <canvas id="chart-semua-soal-line"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>


            <a href="{{ url('/guru/ujian') }}" class="btn btn-danger btn-sm mt-3"><span
                    data-feather="arrow-left-circle"></span> kembali</a>
        </div>
        @include('template.footer')
    </div>
    <!--  END CONTENT AREA  -->
    {!! session('pesan') !!}
    @include('error.ew-t-p')

    {{-- CHART.JS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @php
                $soal_data = [];
                $total_siswa_semua = $ujian->waktuujian->count();
                $siswa_selesai_count = $ujian->waktuujian->whereNotNull('selesai')->count();
                $siswa_belum_mengerjakan = $total_siswa_semua - $siswa_selesai_count;

                // Data untuk Bar Chart per Soal
                foreach ($ujian->detailujian as $soal) {
                    $total_benar = 0;
                    $total_salah = 0;
                    $total_tidak_dijawab = 0;

                    foreach ($ujian->waktuujian as $waktu_ujian) {
                        if ($waktu_ujian->selesai) {
                            $jawaban_siswa = $waktu_ujian->pgsiswa->where('kode', $ujian->kode)->where('detail_ujian_id', $soal->id)->first();

                            if ($jawaban_siswa) {
                                if ($jawaban_siswa->benar == '1') {
                                    $total_benar++;
                                } elseif ($jawaban_siswa->benar == '0') {
                                    $total_salah++;
                                } else {
                                    $total_tidak_dijawab++;
                                }
                            } else {
                                // Jika tidak ada jawaban, dianggap tidak dijawab
                                $total_tidak_dijawab++;
                            }
                        }
                    }

                    $soal_data[] = [
                        'total_benar' => $total_benar,
                        'total_salah' => $total_salah,
                        'total_tidak_dijawab' => $total_tidak_dijawab,
                        'total_siswa_semua' => $total_siswa_semua,
                        'total_belum_mengerjakan' => $siswa_belum_mengerjakan,
                    ];
                }

                // Data untuk Line Chart Keseluruhan
                $line_chart_data = [
                    'labels' => [],
                    'benar' => [],
                    'salah' => [],
                    'tidak_dijawab' => [],
                ];

                $no = 1;
                foreach ($ujian->detailujian as $soal) {
                    $benar_count = 0;
                    $salah_count = 0;
                    $tidak_dijawab_count = 0;

                    foreach ($ujian->waktuujian as $waktu_ujian) {
                        if ($waktu_ujian->selesai) {
                            $jawaban_siswa = $waktu_ujian->pgsiswa->where('kode', $ujian->kode)->where('detail_ujian_id', $soal->id)->first();
                            if ($jawaban_siswa) {
                                if ($jawaban_siswa->benar == '1') {
                                    $benar_count++;
                                } elseif ($jawaban_siswa->benar == '0') {
                                    $salah_count++;
                                } else {
                                    $tidak_dijawab_count++;
                                }
                            } else {
                                $tidak_dijawab_count++;
                            }
                        }
                    }

                    $total_responden = $ujian->waktuujian->whereNotNull('selesai')->count();

                    $line_chart_data['labels'][] = 'Soal ' . $no;
                    $line_chart_data['benar'][] = $total_responden > 0 ? ($benar_count / $total_responden) * 100 : 0;
                    $line_chart_data['salah'][] = $total_responden > 0 ? ($salah_count / $total_responden) * 100 : 0;
                    $line_chart_data['tidak_dijawab'][] = $total_responden > 0 ? ($tidak_dijawab_count / $total_responden) * 100 : 0;

                    $no++;
                }
            @endphp

            // Kode untuk Bar Chart per Soal
            const soalData = @json($soal_data);

            soalData.forEach((data, index) => {
                const ctx = document.getElementById('chart-soal-' + (index + 1));
                const total = data.total_siswa_semua;

                let benar_persen = total > 0 ? (data.total_benar / total) * 100 : 0;
                let salah_persen = total > 0 ? (data.total_salah / total) * 100 : 0;
                let tidak_dijawab_persen = total > 0 ? (data.total_tidak_dijawab / total) * 100 : 0;
                let belum_mengerjakan_persen = total > 0 ? (data.total_belum_mengerjakan / total) * 100 : 0;

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Benar', 'Salah', 'Tidak Dijawab', 'Belum Mengerjakan'],
                        datasets: [{
                            label: 'Persentase Jawaban',
                            data: [benar_persen, salah_persen, tidak_dijawab_persen,
                                belum_mengerjakan_persen
                            ],
                            backgroundColor: [
                                'rgba(75, 192, 192, 0.7)',
                                'rgba(255, 99, 132, 0.7)',
                                'rgba(201, 203, 207, 0.7)',
                                'rgba(255, 159, 64, 0.7)'
                            ],
                            borderColor: [
                                'rgb(75, 192, 192)',
                                'rgb(255, 99, 132)',
                                'rgb(201, 203, 207)',
                                'rgb(255, 159, 64)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100,
                                ticks: {
                                    callback: function(value) {
                                        return value + '%';
                                    }
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += context.parsed.y.toFixed(2) + '%';
                                        }
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            });

            // Kode untuk Line Chart Keseluruhan
            const lineChartData = @json($line_chart_data);
            const lineCtx = document.getElementById('chart-semua-soal-line');

            new Chart(lineCtx, {
                type: 'line',
                data: {
                    labels: lineChartData.labels,
                    datasets: [{
                        label: 'Benar',
                        data: lineChartData.benar,
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.5)',
                        fill: false,
                        tension: 0.1
                    }, {
                        label: 'Salah',
                        data: lineChartData.salah,
                        borderColor: 'rgb(255, 99, 132)',
                        backgroundColor: 'rgba(255, 99, 132, 0.5)',
                        fill: false,
                        tension: 0.1
                    }, {
                        label: 'Tidak Dijawab',
                        data: lineChartData.tidak_dijawab,
                        borderColor: 'rgb(201, 203, 207)',
                        backgroundColor: 'rgba(201, 203, 207, 0.5)',
                        fill: false,
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += context.parsed.y.toFixed(2) + '%';
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            title: {
                                display: true,
                                text: 'Persentase'
                            },
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Soal'
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection