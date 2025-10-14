@extends('template.main')
@section('content')
    @include('template.navbar.guru')

    <!--  BEGIN CONTENT AREA  -->
    <div id="content" class="main-content">
        <div class="layout-px-spacing">
            <div class="row layout-top-spacing">
                <div class="col-lg-12 layout-spacing">
                    <div class="widget shadow p-3" style="min-height: 500px;">
                        <div class="row">
                            <div class="col-lg-7">
                                <div class="widget-heading">
                                    <h5 class="">Ujian / Quiz</h5>
                                    <a href="javascript:void(0)" class="btn btn-primary mt-3" data-toggle="modal"
                                        data-target="#tambah_ujian">Tambah</a>
                                </div>
                                <div class="table-responsive mt-3" style="overflow-x: scroll;">
                                    <table id="datatable-table" class="table text-center text-nowrap">
                                        <thead>
                                            <tr>
                                                <th>Nama</th>
                                                <th>Mapel</th>
                                                <th>Kelas</th>
                                                <th>Opsi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($ujian as $u)
                                                <tr>
                                                    <td>{{ $u->nama }}</td>
                                                    <td>{{ $u->mapel->nama_mapel }}</td>
                                                    <td>{{ $u->kelas->nama_kelas }}</td>
                                                    <td>
                                                        @if ($u->jenis == 0)
                                                            <a href="{{ url('/guru/ujian/' . $u->kode) }}"
                                                                class="btn btn-primary btn-sm" title="Lihat">
                                                                <span data-feather="eye"></span>
                                                            </a>
                                                        @endif

                                                        @if ($u->jenis == 1)
                                                            <a href="{{ url('/guru/ujian_essay/' . $u->kode) }}"
                                                                class="btn btn-primary btn-sm" title="Lihat">
                                                                <span data-feather="eye"></span>
                                                            </a>
                                                        @endif

                                                        {{-- BARU :: Tombol Duplikat --}}
                                                        <a href="javascript:void(0);"
                                                            class="btn btn-info btn-sm btn-duplikat" title="Duplikat"
                                                            data-id="{{ $u->id }}">
                                                            <span data-feather="copy"></span>
                                                        </a>

                                                        <form action="{{ url('/guru/ujian/' . $u->kode) }}" method="post"
                                                            class="d-inline" id="formHapus">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm btn-hapus"
                                                                title="Hapus">
                                                                <span data-feather="trash"></span>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-lg-5 d-flex">
                                <img src="{{ url('/assets/img') }}/ujian.svg" style="width: 100%;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('template.footer')
    </div>
    <!--  END CONTENT AREA  -->

    <!-- MODAL TAMBAH -->
    <div class="modal fade" id="tambah_ujian" tabindex="-1" role="dialog" aria-labelledby="tambah_ujianLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambah_ujianLabel">Tambah Ujian</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        x
                    </button>
                </div>
                <div class="modal-body text-center">
                    <a href="{{ url('/guru/ujian/create') }}" class="btn btn-primary">Pilihan Ganda</a>
                    <a href="{{ url('/guru/ujian_essay') }}" class="btn btn-primary ml-2">Essay</a>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i>
                        Batal</button>
                </div>
            </div>
        </div>
    </div>

    <!-- BARU :: MODAL DUPLIKAT -->
    <div class="modal fade" id="duplikat_ujian" tabindex="-1" role="dialog" aria-labelledby="duplikat_ujianLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="{{ url('/guru/ujian/duplikat') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="duplikat_ujianLabel">Duplikat Ujian</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id_ujian" id="id_ujian_duplikat">
                        <div class="form-group">
                            <label for="nama_ujian_duplikat">Nama Ujian / Quiz Baru</label>
                            <input type="text" name="nama" id="nama_ujian_duplikat" class="form-control"
                                placeholder="Nama ujian baru" required>
                        </div>
                        <div class="form-group">
                            <label for="mapel_duplikat">Mapel</label>
                            <select class="form-control" name="mapel" id="mapel_duplikat" required>
                                <option value="">Pilih</option>
                                @foreach ($guru->gurumapel as $gm)
                                    <option value="{{ $gm->mapel->id }}">{{ $gm->mapel->nama_mapel }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="kelas_duplikat">Kelas</label>
                            <select class="form-control" name="kelas" id="kelas_duplikat" required>
                                <option value="">Pilih</option>
                                @foreach ($guru->gurukelas as $gk)
                                    <option value="{{ $gk->kelas->id }}">{{ $gk->kelas->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="tanggal_mulai">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="waktu_mulai">Waktu Mulai</label>
                            <input type="time" name="waktu_mulai" id="waktu_mulai" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i>
                            Batal</button>
                        <button type="submit" class="btn btn-primary">Duplikat</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <script>
        $(document).ready(function() {
            // Script untuk tombol hapus
            $(".btn-hapus").on("click", function(e) {
                var t = $(this);
                e.preventDefault();
                swal({
                    title: "yakin di hapus?",
                    text: "data yang berkaitan akan dihapus dan tidak bisa di kembalikan!",
                    type: "warning",
                    showCancelButton: true,
                    cancelButtonText: "tidak",
                    confirmButtonText: "ya, hapus",
                    padding: "2em"
                }).then(function(e) {
                    e.value && t.parent("form").submit()
                })
            });

            // BARU :: Script untuk tombol duplikat
            $('.btn-duplikat').on('click', function() {
                var id = $(this).data('id');
                $('#id_ujian_duplikat').val(id);
                $('#duplikat_ujian').modal('show');
            });

            // Script untuk datatable
            $("#datatable-table").DataTable({
                scrollY: "300px",
                scrollX: true,
                scrollCollapse: true,
                paging: true,
                oLanguage: {
                    oPaginate: {
                        sPrevious: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>',
                        sNext: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>'
                    },
                    sInfo: "tampilkan halaman _PAGE_ dari _PAGES_",
                    sSearch: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
                    sSearchPlaceholder: "Cari Data...",
                    sLengthMenu: "Hasil :  _MENU_"
                },
                stripeClasses: [],
                lengthMenu: [
                    [-1, 5, 10, 25, 50],
                    ["All", 5, 10, 25, 50]
                ]
            });
        });
    </script>

    {!! session('pesan') !!}
@endsection
