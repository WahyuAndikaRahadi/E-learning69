<?php

namespace App\Imports;

use App\Models\DetailUjian;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithValidation;

class PgImport implements ToModel, WithHeadingRow, SkipsEmptyRows, WithValidation
{
    protected $kode;

    public function __construct($kode)
    {
        $this->kode = $kode;
    }

    /**
     * Validasi untuk setiap baris berdasarkan header kolom Excel.
     */
    public function rules(): array
    {
        return [
            'soal'    => ['required'],
            'a'       => ['required'],
            'b'       => ['required'],
            'c'       => ['required'],
            'd'       => ['required'],
            'e'       => ['required'],
            'jawaban' => ['required'],
        ];
    }

    /**
     * Mapping data dari Excel ke model Eloquent.
     */
    public function model(array $row)
    {
        return new DetailUjian([
            'kode'    => $this->kode,
            'soal'    => $row['soal'],
            'pg_1'    => 'A. ' . $row['a'],
            'pg_2'    => 'B. ' . $row['b'],
            'pg_3'    => 'C. ' . $row['c'],
            'pg_4'    => 'D. ' . $row['d'],
            'pg_5'    => 'E. ' . $row['e'],
            'jawaban' => $row['jawaban'],
        ]);
    }
}
