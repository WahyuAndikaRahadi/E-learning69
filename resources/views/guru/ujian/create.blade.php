@extends('template.main')
@section('content')
    @include('template.navbar.guru')

    {{-- CSRF Token for AJAX --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Font Awesome for Icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />

    <div id="content" class="main-content">
        <a href="javascript:void(0);" class="btn btn-primary tambah-pg"
            style="position: fixed; right: -10px; top: 50%; z-index: 9999;">Tambah Soal</a>
        <div class="layout-px-spacing">
            <form action="{{ url('/guru/ujian') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row layout-top-spacing">
                    <div class="col-lg-12 layout-spacing">
                        <div class="widget shadow p-3">
                            <div class="widget-heading">
                                <h5 class="">Ujian Pilihan Ganda</h5>
                                <a href="javascript:void(0);" class="btn btn-primary my-2" data-toggle="modal"
                                    data-target="#excel_ujian">Import Excel</a>
                                <div class="row mt-2">
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="">Nama Ujian / Quiz</label>
                                            <input type="text" name="nama" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="">Mapel</label>
                                            <select class="form-control" name="mapel" id="mapel" required>
                                                <option value="">Pilih</option>
                                                @foreach ($guru_mapel as $gm)
                                                    <option value="{{ $gm->mapel->id }}">{{ $gm->mapel->nama_mapel }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    {{-- START MODIFIED: Mengganti dropdown kelas dengan checkbox group --}}
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="kelas_checkbox_group">Kelas (Pilih satu atau lebih)</label>
                                            <div id="kelas_checkbox_group" class="border p-2" style="max-height: 150px; overflow-y: auto; border-radius: 5px;">
                                                @foreach ($guru_kelas as $gk)
                                                    <div class="custom-control custom-checkbox">
                                                        {{-- name diubah menjadi kelas[] untuk menampung banyak nilai --}}
                                                        <input type="checkbox" class="custom-control-input checkbox-kelas" name="kelas[]"
                                                            value="{{ $gk->kelas->id }}" id="kelas-{{ $gk->kelas->id }}">
                                                        <label class="custom-control-label"
                                                            for="kelas-{{ $gk->kelas->id }}">{{ $gk->kelas->nama_kelas }}</label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    {{-- END MODIFIED --}}
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="">Waktu Jam</label>
                                            <input type="number" name="jam" class="form-control" value="0"
                                                required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="">Waktu Menit</label>
                                            <input type="number" name="menit" class="form-control" value="0"
                                                required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="tanggal_mulai">Tanggal Mulai</label>
                                            <input type="date" name="tanggal_mulai" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="waktu_mulai">Waktu Mulai</label>
                                            <input type="time" name="waktu_mulai" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-lg-12">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="customCheck1"
                                                name="acak" value="1">
                                            <label class="custom-control-label" for="customCheck1">Acak Soal
                                                Siswa</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row layout-top-spacing">
                    <div class="col-lg-12 layout-spacing">
                        <div class="widget shadow p-3">
                            {{-- CORRECTED CODE --}}
                            <div class="widget-heading">
                                <h5 class="">Soal Ujian</h5>
                            </div>
                            <div id="soal_pg">
                                <div class="isi_soal">
                                    <div class="form-group">
                                        <label for="">Soal No. 1</label>
                                        <textarea name="soal[]" cols="30" rows="2" class="summernote" wrap="hard" required></textarea>
                                    </div>
                                    {{-- The audio button is now inside each question --}}
                                    <a href="javascript:void(0);" class="btn btn-sm btn-success my-2 tambah-audio-btn">
                                        <i class="fa fa-file-audio"></i> Tambah Audio
                                    </a>
                                    <div class="row mt-2">
                                        {{-- Modified: Options A-E now use textarea with Summernote for rich text and image support --}}
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="">Pilihan A</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">A</span>
                                                    </div>
                                                    <textarea name="pg_1[]" class="form-control summernote" placeholder="Opsi A (bisa tambah gambar)" required></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="">Pilihan B</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">B</span>
                                                    </div>
                                                    <textarea name="pg_2[]" class="form-control summernote" placeholder="Opsi B (bisa tambah gambar)" required></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="">Pilihan C</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">C</span>
                                                    </div>
                                                    <textarea name="pg_3[]" class="form-control summernote" placeholder="Opsi C (bisa tambah gambar)" required></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="">Pilihan D</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">D</span>
                                                    </div>
                                                    <textarea name="pg_4[]" class="form-control summernote" placeholder="Opsi D (bisa tambah gambar)" required></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="">Pilihan E</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">E</span>
                                                    </div>
                                                    <textarea name="pg_5[]" class="form-control summernote" placeholder="Opsi E (bisa tambah gambar)" required></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="">Jawaban</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">
                                                            <svg viewBox="0 0 24 24" width="24" height="24"
                                                                stroke="currentColor" stroke-width="2" fill="none"
                                                                stroke-linecap="round" stroke-linejoin="round"
                                                                class="css-i6dzq1">
                                                                <polyline points="20 6 9 17 4 12"></polyline>
                                                            </svg>
                                                        </span>
                                                    </div>
                                                    <input type="text" name="jawaban[]" class="form-control"
                                                        placeholder="Contoh : A" autocomplete="off" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4">
                                <button class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        @include('template.footer')
    </div>
    {{-- Hidden file input for audio --}}
    <input type="file" id="audio_file_input" accept="audio/*" style="display: none;">

    <div class="modal fade" id="excel_ujian" tabindex="-1" role="dialog" aria-labelledby="excel_ujianLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form action="{{ url('/guru/pg_excel') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="excel_ujianLabel">Import Soal via Excel</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            x
                        </button>
                    </div>
                    <div class="modal-body">
                        {{-- Modal Content --}}
                        <div class="row mt-2">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="">Nama Ujian / Quiz</label>
                                    <input type="text" name="e_nama_ujian" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="">Mapel</label>
                                    <select class="form-control" name="e_mapel" id="e_mapel" required>
                                        <option value="">Pilih</option>
                                        @foreach ($guru_mapel as $gm)
                                            <option value="{{ $gm->mapel->id }}">{{ $gm->mapel->nama_mapel }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            {{-- START MODIFIED: Mengganti dropdown kelas di modal dengan checkbox group --}}
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="e_kelas_checkbox_group">Kelas (Pilih satu atau lebih)</label>
                                    <div id="e_kelas_checkbox_group" class="border p-2" style="max-height: 150px; overflow-y: auto; border-radius: 5px;">
                                        @foreach ($guru_kelas as $gk)
                                            <div class="custom-control custom-checkbox">
                                                {{-- name diubah menjadi e_kelas[] untuk menampung banyak nilai --}}
                                                <input type="checkbox" class="custom-control-input checkbox-e-kelas" name="e_kelas[]"
                                                    value="{{ $gk->kelas->id }}" id="e-kelas-{{ $gk->kelas->id }}">
                                                <label class="custom-control-label"
                                                    for="e-kelas-{{ $gk->kelas->id }}">{{ $gk->kelas->nama_kelas }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            {{-- END MODIFIED --}}
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="">Waktu Jam</label>
                                    <input type="number" name="e_jam" class="form-control" value="0"
                                        required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="">Waktu Menit</label>
                                    <input type="number" name="e_menit" class="form-control" value="0"
                                        required>
                                </div>
                            </div>
                            {{-- NEW: Tanggal Mulai dan Waktu Mulai untuk Import Excel --}}
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="e_tanggal_mulai">Tanggal Mulai</label>
                                    <input type="date" name="e_tanggal_mulai" class="form-control">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="e_waktu_mulai">Waktu Mulai</label>
                                    <input type="time" name="e_waktu_mulai" class="form-control">
                                </div>
                            </div>
                            {{-- END NEW --}}
                        </div>
                        <div class="row mt-2">
                            <div class="col-lg-12">
                                <div class="custom-control custom-checkbox">
                                    <input type="hidden" name="e_acak" value="0">
                                    <input type="checkbox" class="custom-control-input" id="acak"
                                        name="e_acak" value="1">
                                    <label class="custom-control-label" for="acak">Acak Soal Siswa</label>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="">File Excel</label><br>
                                    <input type="file" name="excel" accept=".xls, .xlsx">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <label for="">Template</label><br>
                                <a href="{{ url('/summernote/unduh') }}/template-pg-excel.xlsx"
                                    class="btn btn-success" target="_blank">Download Template</a>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="reset" value="reset" class="btn" data-dismiss="modal"><i
                                class="flaticon-cancel-12"></i> Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            // Variable to store the editor targeted by the audio button
            let targetEditor = null;

            // Function to handle media uploads (image or audio)
            function uploadMedia(file, editor) {
                let formData = new FormData();
                formData.append("file", file);

                $.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    url: "{{ route('summernote_upload') }}",
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                    type: "post",
                    success: function(url) {
                        if (file.type.startsWith('audio/')) {
                            let audioNode = `<audio controls><source src="${url}" type="${file.type}"></audio><br>`;
                            editor.summernote('pasteHTML', audioNode);
                        } else if (file.type.startsWith('image/')) {
                            let imgNode = document.createElement('img');
                            imgNode.src = url;
                            editor.summernote('insertNode', imgNode);
                        }
                    },
                    error: function(e) {
                        console.log('Error uploading file:', e);
                        alert('Failed to upload file.');
                    }
                });
            }

            // Function to handle media deletion
            function deleteMedia(src) {
                $.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    data: { src: src },
                    type: "post",
                    url: "{{ route('summernote_delete') }}",
                    cache: false,
                    success: function(res) {
                        console.log('File deleted:', res);
                    }
                });
            }

            // Function to initialize summernote
            function initSummernote(selector) {
                $(selector).summernote({
                    placeholder: "Tulis opsi atau tambah gambar di sini...",
                    tabsize: 2,
                    height: 120,
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'underline', 'clear']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['table', ['table']],
                        ['insert', ['link', 'picture', 'video']],
                        ['view', ['fullscreen', 'help']]
                    ],
                    callbacks: {
                        onImageUpload: function(files) {
                            for (let i = 0; i < files.length; i++) {
                                uploadMedia(files[i], $(this));
                            }
                        },
                        onMediaDelete: function(target) {
                            if (target[0].tagName === 'IMG') {
                                deleteMedia(target[0].src);
                            } else if (target[0].tagName === 'AUDIO') {
                                const audioSource = $(target).find('source').attr('src');
                                if(audioSource) {
                                    deleteMedia(audioSource);
                                }
                            }
                        }
                    }
                });
            }

            // Initialize summernote on all summernote elements (soal and options)
            initSummernote($('.summernote'));

            // START NEW VALIDATION LOGIC: Memastikan minimal satu kelas terpilih
            
            // Validation for the main form (when creating/editing ujian)
            $('form[action="{{ url('/guru/ujian') }}"]').on('submit', function(e) {
                if ($('.checkbox-kelas:checked').length === 0) {
                    e.preventDefault();
                    alert('Mohon pilih minimal satu Kelas untuk melanjutkan pembuatan ujian.');
                    return false;
                }
            });

            // Validation for the Excel Import form
            $('#excel_ujian form').on('submit', function(e) {
                if ($('.checkbox-e-kelas:checked').length === 0) {
                    e.preventDefault();
                    alert('Mohon pilih minimal satu Kelas untuk import excel.');
                    return false;
                }
            });
            
            // END NEW VALIDATION LOGIC


            // Event delegation for the audio button
            $('#soal_pg').on('click', '.tambah-audio-btn', function() {
                targetEditor = $(this).closest('.isi_soal').find('textarea.summernote');
                $('#audio_file_input').click();
            });

            // Event when an audio file is selected
            $('#audio_file_input').on('change', function(e) {
                if (e.target.files.length > 0 && targetEditor) {
                    uploadMedia(e.target.files[0], targetEditor);
                    $(this).val('');
                    targetEditor = null;
                }
            });

            // --- LOGIC FOR ADDING AND REMOVING QUESTIONS (CORRECTED) ---

            // 1. Logic for ADDING questions
            $('.tambah-pg').click(function() {
                // Get the next question number based on the number of existing questions
                var next_question_number = $('.isi_soal').length + 1;

                // Create the HTML for the new question block (modified for textarea on options)
                const pg = `
                    <div class="isi_soal">
                        <hr>
                        <div class="form-group">
                            <label for="">Soal No. ${next_question_number}</label>
                            <textarea name="soal[]" class="summernote-new" cols="30" rows="2" wrap="hard" required></textarea>
                        </div>
                        <a href="javascript:void(0);" class="btn btn-sm btn-success my-2 tambah-audio-btn">
                            <i class="fa fa-file-audio"></i> Tambah Audio
                        </a>
                        <div class="row mt-2">
                            <div class="col-lg-4"><div class="form-group"><label>Pilihan A</label><div class="input-group"><div class="input-group-prepend"><span class="input-group-text">A</span></div><textarea name="pg_1[]" class="form-control summernote-new" placeholder="Opsi A (bisa tambah gambar)" required></textarea></div></div></div>
                            <div class="col-lg-4"><div class="form-group"><label>Pilihan B</label><div class="input-group"><div class="input-group-prepend"><span class="input-group-text">B</span></div><textarea name="pg_2[]" class="form-control summernote-new" placeholder="Opsi B (bisa tambah gambar)" required></textarea></div></div></div>
                            <div class="col-lg-4"><div class="form-group"><label>Pilihan C</label><div class="input-group"><div class="input-group-prepend"><span class="input-group-text">C</span></div><textarea name="pg_3[]" class="form-control summernote-new" placeholder="Opsi C (bisa tambah gambar)" required></textarea></div></div></div>
                            <div class="col-lg-4"><div class="form-group"><label>Pilihan D</label><div class="input-group"><div class="input-group-prepend"><span class="input-group-text">D</span></div><textarea name="pg_4[]" class="form-control summernote-new" placeholder="Opsi D (bisa tambah gambar)" required></textarea></div></div></div>
                            <div class="col-lg-4"><div class="form-group"><label>Pilihan E</label><div class="input-group"><div class="input-group-prepend"><span class="input-group-text">E</span></div><textarea name="pg_5[]" class="form-control summernote-new" placeholder="Opsi E (bisa tambah gambar)" required></textarea></div></div></div>
                            <div class="col-lg-4"><div class="form-group"><label>Jawaban</label><div class="input-group"><div class="input-group-prepend"><span class="input-group-text"><svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="20 6 9 17 4 12"></polyline></svg></span></div><input type="text" name="jawaban[]" class="form-control" placeholder="Contoh : A" autocomplete="off" required></div></div></div>
                        </div>
                        <a href="javascript:void(0);" class="btn btn-danger hapus-pg">Hapus</a>
                    </div>`;

                // Convert HTML string to a jQuery object
                var newSoalBlock = $(pg);
                
                // Append the new question block to the DOM
                $('#soal_pg').append(newSoalBlock);

                // Initialize Summernote ONLY on the textarea within the newly added block.
                // We use a temporary class `summernote-new` to isolate it.
                initSummernote(newSoalBlock.find('textarea.summernote-new'));

                // Remove the temporary class so it doesn't get re-initialized later.
                newSoalBlock.find('textarea.summernote-new').removeClass('summernote-new').addClass('summernote');
            });

            // 2. Logic for REMOVING questions
            $("#soal_pg").on("click", ".isi_soal .hapus-pg", function() {
                let soalBlock = $(this).closest(".isi_soal");

                // IMPORTANT: Destroy the Summernote instance before removing its element from the DOM
                soalBlock.find('.summernote').summernote('destroy');

                // Remove the question block from the DOM
                soalBlock.remove();

                // Update the question numbering after removal
                $('.isi_soal').each(function(index) {
                    $(this).find('label').first().text('Soal No. ' + (index + 1));
                });
            });
        });
    </script>
    {!! session('pesan') !!}
@endsection