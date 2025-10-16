<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <title>{{ $title }}</title>
    <link rel="icon" type="image/x-icon" href="{{ url('/assets/img') }}/smkn69.png" />
    <link href="https://fonts.googleapis.com/css?family=Quicksand:400,500,600,700&amp;display=swap" rel="stylesheet">
    <link href="{{ url('/assets/cbt-malela') }}/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ url('/assets/cbt-malela') }}/assets/css/plugins.css" rel="stylesheet" type="text/css" />
    <link href="{{ url('/assets/cbt-malela') }}/assets/css/authentication/form-1.css" rel="stylesheet"
        type="text/css" />
    <link rel="stylesheet" type="text/css"
        href="{{ url('/assets/cbt-malela') }}/assets/css/forms/theme-checkbox-radio.css">
    <link rel="stylesheet" type="text/css" href="{{ url('/assets/cbt-malela') }}/assets/css/forms/switches.css">
    <link href="{{ url('/assets/cbt-malela') }}/plugins/sweetalerts/sweetalert2.min.css" rel="stylesheet"
        type="text/css" />
    <link href="{{ url('/assets/cbt-malela') }}/plugins/sweetalerts/sweetalert.css" rel="stylesheet" type="text/css" />
    <script src="{{ url('/assets/cbt-malela') }}/assets/js/libs/jquery-3.1.1.min.js"></script>
    <script src="{{ url('/assets/cbt-malela') }}/plugins/sweetalerts/sweetalert2.min.js"></script>
    <script src="{{ url('/assets/cbt-malela') }}/plugins/sweetalerts/custom-sweetalert.js"></script>

    <style>
        /* 1. Paksa Container menjadi Full Screen Flexbox */
        .form-container {
            display: flex;
            min-height: 100vh;
            width: 100%;
            padding: 0 !important; /* Pastikan tidak ada padding yang mengganggu */
        }
        
        /* 2. OVERRIDE: Form Image (ILUSTRASI) sekarang di KIRI, ambil 45% lebar */
        .form-container .form-image {
            /* TIMPA position: fixed; right: 0; */
            position: relative; /* Ganti ke posisi relatif/normal */
            width: 45%; 
            order: 1; /* Pastikan dia di urutan pertama (kiri) */
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            /* Tambahkan garis pemisah di sisi kanan */
            border-right: 2px solid #e0e6ed; 
            background-color: #f7f9fc;
        }

        /* 3. OVERRIDE: Form Form (FORM LOGIN) sekarang di KANAN, ambil 55% lebar */
        .form-container .form-form {
            width: 55%; 
            order: 2; /* Pastikan dia di urutan kedua (kanan) */
            min-height: 100vh;
            display: flex; 
            align-items: center;
            /* TIMPA margin: 0 auto; di dalam form-form-wrap */
            justify-content: center; 
        }

        /* 4. Kontrol lebar konten form agar tidak terlalu melebar, tapi tetap di tengah kolom 55% */
        .form-container .form-form .form-form-wrap {
            max-width: 520px; 
            width: 90%; 
            margin: 0; /* TIMPA margin: 0 auto; agar tidak terpusat di layar total */
            padding: 20px;
        }
        
        /* 5. Styling untuk gambar poster yang diperkecil */
        .form-image .l-image img {
            max-width: 92%; 
            height: auto;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15); 
        }
        
        /* 6. Responsif: Sembunyikan poster di layar kecil */
        @media (max-width: 991px) {
            .form-container .form-image {
                display: none; 
            }
            .form-container .form-form {
                width: 100%; 
                max-width: 100%;
            }
        }
    </style>
    </head>

<body class="form">
    <div class="form-container">
        
        <div class="form-image">
            <div class="l-image d-flex justify-content-center align-items-center w-100 h-100">
                <img src="/assets/img/cbt-poster.png" alt="Login Illustration">
            </div>
        </div>

        <div class="form-form">
            <div class="form-form-wrap">
                <div class="form-container">
                    <div class="form-content">
                        <div style="border:3px solid #1b55e2; margin: 0px; padding: 40px; border-radius: 15px;">

                            <h1 class="">Login to <a href=""><span class="brand-name">e-Learning 69</span></a></h1>
                            <form action="{{ url('/login') }}" method="POST" class="text-left">
                                <div class="form">
                                    @csrf
                                    <div id="username-field" class="field-wrapper input">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="feather feather-user">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                    <input type="email" id="username" name="email"
                                        class="form-control" value="{{ old('email') }}" placeholder="Username/Email"
                                        required>
                                </div>

                                <div id="password-field" class="field-wrapper input mb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="feather feather-lock">
                                        <rect x="3" y="11" width="18" height="11" rx="2"
                                            ry="2"></rect>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                    </svg>
                                    <input type="password" id="password" name="password"
                                        class="form-control" placeholder="Password" required>
                                </div>
                                <div class="d-sm-flex justify-content-between">
                                    <div class="field-wrapper toggle-pass">
                                        <p class="d-inline-block">Show Password</p>
                                        <label class="switch s-primary">
                                            <input type="checkbox" id="toggle-password" class="d-none">
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                    <div class="field-wrapper">
                                        <button type="submit" class="btn btn-primary" value="">Log In</button>
                                    </div>

                                </div>
                            </div>
                        </form><br>
                        <p class="signup-link">
                            Lupa Password? <a href="{{ url('/recovery') }}">Klik Disini</a>
                        </p>
    </div>
                        <p class="terms-conditions" style="margin-top: 30px;">© 2025 e-Learning 69 All Rights Reserved.
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src="{{ url('/assets/cbt-malela') }}/bootstrap/js/popper.min.js"></script>
    <script src="{{ url('/assets/cbt-malela') }}/bootstrap/js/bootstrap.min.js"></script>

    <script src="{{ url('/assets/cbt-malela') }}/assets/js/authentication/form-1.js"></script>

    {!! session('pesan') !!}
</body>

</html>