<?php

namespace App\Http\Controllers;

use App\Models\FileModel;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SummernoteController extends Controller
{
    public function upload(Request $request)
    {
        // Memastikan ada file yang diunggah dengan kunci 'file'
        if ($request->hasFile('file')) {
            // Mengambil file dari request menggunakan kunci 'file'
            $path = $request->file('file')->store('assets/files');
            // Mengambil nama file dari path yang dikembalikan
            $nama_file = basename($path);
            echo asset('assets/files/' . $nama_file);
        } else {
            // Jika tidak ada file, kirim respons error
            return response()->json(['error' => 'No file uploaded'], 400);
        }
    }

    public function delete(Request $request)
    {
        $array = explode('/', $request->src);
        $nama_file = $array[count($array) - 1];
        
        Storage::delete('assets/files/' . $nama_file);
        echo "berhasil di hapus";
    }

    public function delete_file(Request $request)
    {
        FileModel::where('nama', $request->src)->delete();
        Storage::delete('assets/files/' . $request->src);
    }

    public function unduh($file)
    {
        return Storage::download('assets/files/' . $file);
    }
}
