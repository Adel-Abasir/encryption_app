<?php

namespace App\Http\Controllers;

use App\Services\OpensslCipherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LargeFileController extends Controller
{
    private OpensslCipherService $opensslCipherService;

    public function __construct(){
        $this->opensslCipherService = new OpensslCipherService();
    }

    public function file_processing(Request $request){
        if (!$request->hasFile('file_upload')) {
            return "Error while uploading file, please try again";
        }

        $file = $request->file('file_upload');

        if($request->cipher_type == "encrypt"){
            $dest_file_name = $this->opensslCipherService->encrypt($file, $file->hashName());
        };

        if($request->cipher_type == "decrypt"){
            $dest_file_name = $this->opensslCipherService->decrypt($file, $file->hashName());
        };

        return view(
            'welcome',
            [
                'file_info' => [
                    'file_name' => $file->getClientOriginalName(), 
                    'file_extension' => $file->extension(), 
                    'file_size' => $file->getSize(),
                    'file_hash_name' => $file->hashName(),
                    'end_file_name' => $dest_file_name
                ],
                'process_done' => true,
                'process_info' => $request->cipher_type == 'encrypt' ? "Encryption Done!" : "Decryption Done!",
                'file_selected' => true
            ]
        );
    }    

    public function file_download(Request $request) {
        return Storage::download($request->dest_file_name, $request->download_name);
    }
}