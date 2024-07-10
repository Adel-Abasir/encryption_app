<?php

namespace App\Http\Controllers;

use App\Services\LaravelCipherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileController extends Controller
{
    private LaravelCipherService $laravelCipherService;

    public function __construct(){
        $this->laravelCipherService = new LaravelCipherService();
    }

    public function file_processing(Request $request){
        if (!$request->hasFile('file_upload')) {
            return "Error while uploading file, please try again";
        }

        $file = $request->file('file_upload');

        if($request->cipher_type == "encrypt"){
            $this->laravelCipherService->encrypt($file, $file->hashName());
        };

        if($request->cipher_type == "decrypt"){
            $this->laravelCipherService->decrypt($file, $file->hashName());
        };

        return view(
            'welcome',
            [
                'file_info' => [
                    'file_name' => $file->getClientOriginalName(), 
                    'file_extension' => $file->extension(), 
                    'file_size' => $file->getSize(),
                    'file_hash_name' => $file->hashName(),
                    'end_file_name' => ''
                ],
                'process_done' => true,
                'process_info' => $request->cipher_type == 'encrypt' ? "Encryption Done!" : "Decryption Done!",
                'file_selected' => true
            ]
        );
    }    

    public function file_download(Request $request) {
        return Storage::download($request->file_name, $request->download_name);
    }
}