<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; // Untuk hashing password
use App\Models\Admin;  // Impor model Admin
use App\Models\Guru;   // Impor model Guru
use App\Models\Siswa;  // Impor model Siswa
use App\Models\Kelas;  // Impor model Kelas

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // --- Seeding untuk tabel 'admins' ---
        Admin::create([
            'nama_admin' => 'Admin Utama',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'avatar' => 'default.png',
            'role' => 1,
            'is_active' => 1,
        ]);

        Admin::create([
            'nama_admin' => 'Admin Kedua',
            'email' => 'admin2@example.com',
            'password' => Hash::make('password123'),
            'avatar' => 'default.png',
            'role' => 1,
            'is_active' => 1,
        ]);

        // --- Seeding untuk tabel 'guru' ---
        Guru::create([
            'nama_guru' => 'Agung Iswanto',
            'gender' => 'Laki-laki',
            'email' => 'agungiswanto53@gmail.com',
            'password' => Hash::make('password'),
            'avatar' => 'default.png',
            'role' => 2,
            'is_active' => 1,
        ]);

        Guru::create([
            'nama_guru' => 'Siti Aminah',
            'gender' => 'Perempuan',
            'email' => 'siti.aminah@example.com',
            'password' => Hash::make('password123'),
            'avatar' => 'default.png',
            'role' => 2,
            'is_active' => 0,
        ]);

        // --- Seeding untuk tabel 'siswa' ---
        // ==========================================================
        // --- DATA DUMMY KELAS XII (AWAL) ---
        // ==========================================================
        
        // 1. Buat data kelas baru (XII SIJA 1 dan XII SIJA 2)
// Kelas SIJA
$kelasXSIJA1 = Kelas::firstOrCreate(['nama_kelas' => 'X SIJA 1']);
$kelasXSIJA2 = Kelas::firstOrCreate(['nama_kelas' => 'X SIJA 2']);
$kelasXISIJA1 = Kelas::firstOrCreate(['nama_kelas' => 'XI SIJA 1']);
$kelasXISIJA2 = Kelas::firstOrCreate(['nama_kelas' => 'XI SIJA 2']);
$kelasXIISIJA1 = Kelas::firstOrCreate(['nama_kelas' => 'XII SIJA 1']);
$kelasXIISIJA2 = Kelas::firstOrCreate(['nama_kelas' => 'XII SIJA 2']);

// Kelas MEKA
$kelasXMEKA1 = Kelas::firstOrCreate(['nama_kelas' => 'X MEKA 1']);
$kelasXMEKA2 = Kelas::firstOrCreate(['nama_kelas' => 'X MEKA 2']);
$kelasXIMEKA1 = Kelas::firstOrCreate(['nama_kelas' => 'XI MEKA 1']);
$kelasXIMEKA2 = Kelas::firstOrCreate(['nama_kelas' => 'XI MEKA 2']);
$kelasXIIMEKA1 = Kelas::firstOrCreate(['nama_kelas' => 'XII MEKA 1']);
$kelasXIIMEKA2 = Kelas::firstOrCreate(['nama_kelas' => 'XII MEKA 2']);

// Kelas OTO
$kelasXOTO1 = Kelas::firstOrCreate(['nama_kelas' => 'X OTO 1']);
$kelasXOTO2 = Kelas::firstOrCreate(['nama_kelas' => 'X OTO 2']);
$kelasXIOTO1 = Kelas::firstOrCreate(['nama_kelas' => 'XI OTO 1']);
$kelasXIOTO2 = Kelas::firstOrCreate(['nama_kelas' => 'XI OTO 2']);
$kelasXIIOTO1 = Kelas::firstOrCreate(['nama_kelas' => 'XII OTO 1']);
$kelasXIIOTO2 = Kelas::firstOrCreate(['nama_kelas' => 'XII OTO 2']);



}
}
