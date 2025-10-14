@extends('template.main')
@section('content')
@include('template.navbar.admin')

<div id="content" class="main-content">
    <div class="layout-px-spacing">
        <div class="row layout-top-spacing">
            <div class="col-lg-12 layout-spacing">
                <div class="widget shadow p-3">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="widget-heading">
                                <h5 class="">Permintaan Ganti Password</h5>
                                <p class="mt-3">Daftar permintaan ganti password yang diajukan oleh Guru dan Siswa. Gunakan tombol **Setuju** untuk mengubah password sesuai permintaan dan **Tolak** untuk menghapus permintaan.</p>
                            </div>
                            <div class="table-responsive mt-4">
                                <table id="datatable-table" class="table text-center text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Pengaju</th>
                                            <th>Email</th>
                                            <th>Role / Kelas</th>
                                            <th>Password Diinginkan</th>
                                            <th>Tanggal Pengajuan</th>
                                            <th>Aksi Admin</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; ?>
                                        @foreach ($requests as $r)
                                            <tr>
                                                <td>{{ $no++ }}</td>
                                                <td>{{ $r->user_detail['nama'] }}</td>
                                                <td>{{ $r->email }}</td>
                                                <td>{{ $r->user_detail['tipe'] }}</td>
                                                <td>
                                                    @if($r->requested_password)
                                                        <code class="text-success">{{ $r->requested_password }}</code>
                                                    @else
                                                        <span class="text-danger">Tidak Ada</span>
                                                    @endif
                                                </td>
                                                <td>{{ date('d M Y, H:i', strtotime($r->created_at)) }}</td>
                                                <td>
                                                    {{-- Tombol Setuju --}}
                                                    <a href="javascript:void(0)" 
                                                        class="btn btn-success btn-sm btn-agree" 
                                                        data-id="{{ $r->id }}" 
                                                        data-email="{{ $r->email }}"
                                                        data-req-pass="{{ $r->requested_password }}"
                                                        {{ empty($r->requested_password) ? 'disabled' : '' }}>
                                                        <i data-feather="check"></i> Setuju
                                                    </a>
                                                    
                                                    {{-- Tombol Tolak --}}
                                                    <a href="javascript:void(0)" 
                                                        class="btn btn-danger btn-sm btn-disagree" 
                                                        data-id="{{ $r->id }}" 
                                                        data-email="{{ $r->email }}">
                                                        <i data-feather="x"></i> Tolak
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('template.footer')
</div>
{!! session('pesan') !!}

<script>
    $(document).ready(function() {
        // --- Inisialisasi DataTable ---
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
                sSearchPlaceholder: "Cari Data...",
                sLengthMenu: "Hasil :  _MENU_"
            },
            stripeClasses: [],
            lengthMenu: [
                [-1, 5, 10, 25, 50],
                ["All", 5, 10, 25, 50]
            ]
        });

        // --- Logic untuk Tombol AGREE ---
        $(".btn-agree").on('click', function() {
            var requestId = $(this).data('id');
            var requestEmail = $(this).data('email');
            var requestedPass = $(this).data('req-pass');

            if (!requestedPass) {
                // Tombol harusnya sudah disabled, tapi ini untuk safety check
                return;
            }

            swal({
                title: "Yakin Setuju?",
                text: "Password untuk " + requestEmail + " akan diubah menjadi: " + requestedPass + ". Permintaan akan dihapus.",
                type: "warning",
                showCancelButton: true,
                cancelButtonText: "Batal",
                confirmButtonText: "Ya, Setuju",
                padding: "2em"
            }).then(function(result) {
                if (result.value) {
                    var url = "{{ url('/admin/password-requests') }}/" + requestId + "/agree";
                    
                    // Membuat form dinamis untuk POST
                    $('<form>', {
                        'action': url,
                        'method': 'POST'
                    }).append(
                        $('<input>', {
                            'name': '_token',
                            'value': "{{ csrf_token() }}",
                            'type': 'hidden'
                        })
                    ).appendTo('body').submit();
                }
            });
        });
        
        // --- Logic untuk Tombol DISAGREE ---
        $(".btn-disagree").on('click', function() {
            var requestId = $(this).data('id');
            var requestEmail = $(this).data('email');

            swal({
                title: "Yakin Tolak?",
                text: "Permintaan ganti password untuk " + requestEmail + " akan DITOLAK. Password user TIDAK AKAN diubah. Permintaan akan dihapus.",
                type: "warning",
                showCancelButton: true,
                cancelButtonText: "Batal",
                confirmButtonText: "Ya, Tolak",
                padding: "2em"
            }).then(function(result) {
                if (result.value) {
                    var url = "{{ url('/admin/password-requests') }}/" + requestId + "/disagree";
                    
                    // Membuat form dinamis untuk POST
                    $('<form>', {
                        'action': url,
                        'method': 'POST'
                    }).append(
                        $('<input>', {
                            'name': '_token',
                            'value': "{{ csrf_token() }}",
                            'type': 'hidden'
                        })
                    ).appendTo('body').submit();
                }
            });
        });

    });
</script>

@endsection