<?php

namespace App\Http\Controllers;

use App\Mail\ForgotPassword;
use App\Models\Guru;
use App\Models\Admin;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Token;
use App\Models\PasswordRequest; // <-- TAMBAH: Import Model PasswordRequest
use Illuminate\Support\Str;
use App\Mail\VerifikasiAkun;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Artisan;

class AuthController extends Controller
{
    public function __construct()
    {
    }

    public function index()
    {
        if (session('admin') != null) {
            return redirect('/admin');
        }

        return view('auth.login', [
            "title" => "Login Form",
            "admin" => Admin::all()
        ]);
    }
    public function login(Request $request)
    {
        $admin = Admin::firstWhere('email', $request->input('email'));
        if ($admin) {
            if (Hash::check($request->input('password'), $admin->password)) {
                $request->session()->put('id', $admin->id);
                $request->session()->put('email', $admin->email);
                $request->session()->put('role', 1);
                return redirect()->intended('/admin')->with('pesan', "
                    <script>
                        swal({
                            title: 'Berhasil!',
                            text: 'Selamat datang admin " . $admin->email . "',
                            type: 'success',
                            padding: '2em'
                        })
                    </script>
                ");
            }
        }

        $guru = Guru::firstWhere('email', $request->input('email'));
        if ($guru) {
            if (Hash::check($request->input('password'), $guru->password)) {
                $request->session()->put('id', $guru->id);
                $request->session()->put('email', $guru->email);
                $request->session()->put('role', 2);
                $request->session()->put('nama_guru', $guru->nama_guru);
                return redirect()->intended('/guru')->with('pesan', "
                    <script>
                        swal({
                            title: 'Berhasil!',
                            text: 'Selamat datang guru " . $guru->nama_guru . "',
                            type: 'success',
                            padding: '2em'
                        })
                    </script>
                ");
            }
        }

        $siswa = Siswa::firstWhere('email', $request->input('email'));
        if ($siswa) {
            if ($siswa->is_active == 0) {
                return redirect('/')->with('pesan', "
                    <script>
                        swal({
                            title: 'Error!',
                            text: 'Akun anda belum di aktifasi, silahkan cek email anda',
                            type: 'error',
                            padding: '2em'
                        })
                    </script>
                ");
            }
            if (Hash::check($request->input('password'), $siswa->password)) {
                $request->session()->put('id', $siswa->id);
                $request->session()->put('email', $siswa->email);
                $request->session()->put('role', 3);
                return redirect()->intended('/siswa')->with('pesan', "
                    <script>
                        swal({
                            title: 'Berhasil!',
                            text: 'Selamat datang siswa " . $siswa->nama_siswa . "',
                            type: 'success',
                            padding: '2em'
                        })
                    </script>
                ");
            }
        }

        return redirect('/')->with('pesan', "
            <script>
                swal({
                    title: 'Error!',
                    text: 'Email atau password salah',
                    type: 'error',
                    padding: '2em'
                })
            </script>
        ");
    }

    public function install()
    {
        return view('auth.install');
    }
    public function install_(Request $request)
    {
        $password = bcrypt($request->password);

        Admin::create([
            'email' => $request->email,
            'password' => $password
        ]);

        return redirect('/')->with('pesan', "
            <script>
                swal({
                    title: 'Berhasil!',
                    text: 'Akun admin telah dibuat',
                    type: 'success',
                    padding: '2em'
                })
            </script>
        ");
    }

    public function register()
    {
        return view('auth.register', [
            'title' => 'Register Form',
            'kelas' => Kelas::all()
        ]);
    }
    public function register_(Request $request)
    {
        $validate = $request->validate([
            'nama_siswa' => 'required',
            'gender' => 'required',
            'kelas_id' => 'required',
            'email' => 'required|email:dns|unique:siswa',
            'password' => 'required|min:5|max:255',
        ]);
        $validate['password'] = bcrypt($validate['password']);
        $validate['is_active'] = 0;
        Siswa::create($validate);

        $token = Str::random(20);
        Token::create([
            'email' => $request->email,
            'token' => $token,
            'role' => 3
        ]);

        Mail::to($request->email)->send(new VerifikasiAkun($token));
        return redirect('/')->with('pesan', "
            <script>
                swal({
                    title: 'Berhasil!',
                    text: 'Akun telah dibuat, silahkan cek email untuk aktivasi',
                    type: 'success',
                    padding: '2em'
                })
            </script>
        ");
    }

    public function aktivasi(Token $token)
    {
        if ($token->role == 3) {
            Siswa::where('email', $token->email)
                ->update(['is_active' => 1]);
        }
        Token::where('id', $token->id)
            ->delete();
        return redirect('/')->with('pesan', "
            <script>
                swal({
                    title: 'Berhasil!',
                    text: 'Akun telah di aktifasi, silahkan login',
                    type: 'success',
                    padding: '2em'
                })
            </script>
        ");
    }

    // --- UBAH: recovery yang lama diganti dengan form pengajuan ke Admin ---
    public function recovery()
    {
        $admin = Admin::all();
        if ($admin->count() == 0) {
            return redirect('/')->with('pesan', "
                <script>
                    swal({
                        title: 'Error!',
                        text: 'Akun admin belum dibuat',
                        type: 'error',
                        padding: '2em'
                    })
                </script>
            ");
        }

        return view('auth.recovery', [ 
            'title' => 'Ajukan Ganti Password ke Admin'
        ]);
    }
    
    // --- BARU: Logic untuk mengirim permintaan ganti password ---
    public function requestPasswordChange(Request $request)
    {
        $request->validate([
            'email' => 'required|email:dns',
            'new_password_request' => 'required|min:6', // Diubah menjadi required
        ]);

        $user = null;
        $role = null;
        
        // Cek Admin (Admin tidak boleh mengajukan)
        if (Admin::firstWhere('email', $request->input('email'))) {
             return redirect('/recovery')->with('pesan', "
                <script>
                    swal({
                        title: 'Error!',
                        text: 'Admin tidak perlu mengajukan ganti password di sini.',
                        type: 'error',
                        padding: '2em'
                    })
                </script>
            ");
        }

        $guru = Guru::firstWhere('email', $request->input('email'));
        if ($guru) {
            $user = $guru;
            $role = 2; // Role Guru
        }
        $siswa = Siswa::firstWhere('email', $request->input('email'));
        if ($siswa) {
            $user = $siswa;
            $role = 3; // Role Siswa
        }

        if ($user == null) {
            return redirect('/recovery')->with('pesan', "
                <script>
                    swal({
                        title: 'Error!',
                        text: 'Email tidak ditemukan',
                        type: 'error',
                        padding: '2em'
                    })
                </script>
            ");
        }

        // Cek apakah permintaan dengan email yang sama sudah ada
        $existingRequest = PasswordRequest::where('email', $request->email)->first();

        if ($existingRequest) {
            return redirect('/recovery')->with('pesan', "
                <script>
                    swal({
                        title: 'Warning!',
                        text: 'Anda sudah mengajukan permintaan. Mohon tunggu proses dari Admin.',
                        type: 'warning',
                        padding: '2em'
                    })
                </script>
            ");
        }

        // Buat record permintaan baru
        PasswordRequest::create([
            'email' => $request->email,
            'role' => $role,
            // Simpan password yang diminta
            'requested_password' => $request->input('new_password_request'), 
        ]);

        return redirect('/')->with('pesan', "
            <script>
                swal({
                    title: 'Berhasil!',
                    text: 'Permintaan ganti password sudah diajukan. Admin akan memprosesnya.',
                    type: 'success',
                    padding: '2em'
                })
            </script>
        ");
    }

    public function logout(Request $request)
    {
        $request->session()->remove('id');
        $request->session()->remove('email');
        $request->session()->remove('role');

        return redirect('/')->with('pesan', "
            <script>
                swal({
                    title: 'Berhasil!',
                    text: 'Anda telah logout',
                    type: 'success',
                    padding: '2em'
                })
            </script>
        ");

        return redirect('/');
    }
}
