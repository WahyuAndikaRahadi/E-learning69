@extends('template.main')
@section('content')
    @include('template.navbar.siswa')

    <!-- BEGIN CONTENT AREA -->
    <div id="content" class="main-content">
        <div class="layout-px-spacing">
            <div class="row layout-top-spacing">
                <div class="col-lg-12 layout-spacing">
                    <div class="widget shadow p-3" style="min-height: 450px;">
                        <div class="row">
                            <div class="col-lg-7">
                                <div class="widget-heading">
                                    <h5 class="">Ujian</h5>
                                </div>
                                <div class="table-responsive" style="overflow-x: scroll;">
                                    <table id="datatable-table" class="table text-center text-nowrap">
                                        <thead>
                                            <tr>
                                                <th>Nama</th>
                                                <th>Mapel</th>
                                                <th>Kelas</th>
                                                <th>Mulai</th>
                                                <th>Opsi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($ujian->sortBy('id') as $u)
                                                {{-- Ambil data ujian dari relasi WaktuUjian dengan Ujian --}}
                                                @php
                                                    $ujian_detail = \App\Models\Ujian::firstWhere('kode', $u->kode);
                                                    $tanggal_mulai = \Carbon\Carbon::parse($ujian_detail->tanggal_mulai . ' ' . $ujian_detail->waktu_mulai);
                                                    $sekarang = \Carbon\Carbon::now();
                                                    $sudah_mulai = $sekarang->greaterThanOrEqualTo($tanggal_mulai);
                                                    $url_ujian = $ujian_detail->jenis == 0 ? url('siswa/ujian/' . $u->kode) : url('siswa/ujian_essay/' . $u->kode);
                                                @endphp
                                                <tr>
                                                    <td>{{ $ujian_detail->nama }}</td>
                                                    <td>{{ $ujian_detail->mapel->nama_mapel }}</td>
                                                    <td>{{ $ujian_detail->kelas->nama_kelas }}</td>
                                                    <td>
                                                        {{ $tanggal_mulai->format('d M Y H:i') }}
                                                        <br>
                                                        @if (!$sudah_mulai)
                                                            <span class="countdown" data-timestamp="{{ $tanggal_mulai->timestamp }}"></span>
                                                        @else
                                                            Sudah Mulai
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{-- Tombol dengan data-href untuk menyimpan URL asli --}}
                                                        <a href="#" class="btn btn-primary btn-sm btn-kerjakan" data-href="{{ $url_ujian }}">
                                                            @if ($u->waktu_berakhir == NULL)
                                                                <span data-feather="edit-3"></span> kerjakan
                                                            @else
                                                                @if ($u->selesai == NULL)
                                                                    <span data-feather="edit-3"></span> lanjut kerjakan
                                                                @else
                                                                    <span data-feather="eye"></span> lihat
                                                                @endif
                                                            @endif
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-lg-5 d-flex">
                                <img src="{{ url('assets/img') }}/ujian.svg" class="align-middle" alt="" style="width: 100%;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('template.footer')
    </div>
    <!-- END CONTENT AREA -->

    <script>
        $(document).ready(function() {
            // Inisialisasi DataTable
            $("#datatable-table").DataTable({
                scrollY: "300px",
                scrollX: !0,
                scrollCollapse: !0,
                paging: !0,
                oLanguage: {
                    oPaginate: {
                        sPrevious: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>',
                        sNext: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>'
                    },
                    sInfo: "tampilkan halaman _PAGE_ dari _PAGES_",
                    sSearch: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
                    sSearchPlaceholder: "Cari...",
                    sLengthMenu: "Hasil : _MENU_",
                },
                stripeClasses: [],
                lengthMenu: [
                    [-1, 5, 10, 25, 50],
                    ["All", 5, 10, 25, 50]
                ],
                pageLength: -1
            });

            // Fungsi untuk mengelola timer dan tombol
            function updateUjianStatus() {
                $('tbody tr').each(function() {
                    let row = $(this);
                    let countdownSpan = row.find('.countdown');
                    let tombol = row.find('.btn-kerjakan');

                    // Ambil URL asli dari data-href
                    let urlAsli = tombol.data('href');

                    if (countdownSpan.length) {
                        let timestamp = parseInt(countdownSpan.data('timestamp'));
                        let sekarang = Math.floor(Date.now() / 1000);
                        let selisih = timestamp - sekarang;

                        if (selisih > 0) {
                            // Timer masih berjalan
                            let hari = Math.floor(selisih / (3600 * 24));
                            let jam = Math.floor((selisih % (3600 * 24)) / 3600);
                            let menit = Math.floor((selisih % 3600) / 60);
                            let detik = Math.floor(selisih % 60);

                            let sisa_waktu = '';
                            if (hari > 0) {
                                sisa_waktu += hari + 'h ';
                            }
                            sisa_waktu += jam.toString().padStart(2, '0') + ':' +
                                          menit.toString().padStart(2, '0') + ':' +
                                          detik.toString().padStart(2, '0');

                            countdownSpan.text(sisa_waktu);

                            // Matikan tombol dan set href ke '#'
                            tombol.addClass('disabled');
                            tombol.addClass('btn-secondary');
                            tombol.removeClass('btn-primary');
                            tombol.attr('href', '#');
                        } else {
                            // Timer sudah selesai
                            countdownSpan.text('Sudah Mulai');
                            countdownSpan.removeClass('countdown'); // Hapus kelas countdown agar tidak dihitung lagi

                            // Aktifkan tombol dan set href ke URL asli
                            tombol.removeClass('disabled');
                            tombol.removeClass('btn-secondary');
                            tombol.addClass('btn-primary');
                            tombol.attr('href', urlAsli);
                        }
                    } else {
                        // Jika tidak ada countdown (ujian sudah dimulai saat halaman dimuat), pastikan tombol aktif
                        tombol.removeClass('disabled');
                        tombol.removeClass('btn-secondary');
                        tombol.addClass('btn-primary');
                        tombol.attr('href', urlAsli);
                    }
                });
            }

            // Panggil fungsi pertama kali
            updateUjianStatus();

            // Atur interval untuk memperbarui status setiap detik
            setInterval(updateUjianStatus, 1000);
        });
    </script>
@endsection
