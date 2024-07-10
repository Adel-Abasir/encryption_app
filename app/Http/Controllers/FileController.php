<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileController extends Controller
{
    protected static $files_info = array();

    public function file_download(Request $request) {
        $file_hash_name = $request->file_hash_name;
        $file_original_name = $request->file_name;

        return Storage::download($file_hash_name, $file_original_name);
    }

    public function file_processing(Request $request){
        if (!$request->hasFile('file_upload')) {
            return "Error while uploading file, please try again";
        }

        $process_type = $request->encrypt_decrypt_radio;

        $file = $request->file('file_upload');
        [$file_original_name, $file_extension, $file_size, $file_hash_name] = [
            $file->getClientOriginalName(),
            $file->extension(),
            $file->getSize(),
            $file->hashName()
        ];

        if($process_type == "encrypt"){
            $this->file_encryption($file, $file_original_name, $file_hash_name);
        };

        if($process_type == "decrypt"){
            $this->file_decryption($file, $file_original_name, $file_hash_name);
        };

        return view(
            'welcome',
            [
                'file_info' => [
                    'file_name' => $file_original_name, 
                    'file_extension' => $file_extension, 
                    'file_size' => $file_size,
                    'file_hash_name' => $file_hash_name,
                    'end_file_name' => ''
                ],
                'process_done' => true,
                'process_info' => $process_type == 'encrypt' ? "Encryption Done!" : "Decryption Done!",
                'file_selected' => true
            ]
        );
    }

    private function read_file_content($file, $file_hash_name){
        $file_path = $file->storeAs('/', $file_hash_name);
        $file_content = Storage::get($file_path);
        Storage::delete($file_path);

        return $file_content;
    }

    private function file_encryption($file, $file_original_name, $file_hash_name){
        $file_content = $this->read_file_content($file, $file_hash_name);

        try {
            $encrypted_content = Crypt::encryptString($file_content);

            echo "File encrypted using laravel helper functions!\n";
            echo 'Memory usage: ' . round(memory_get_usage() / 1048576, 2) . "M\n";
            
            return Storage::put($file_hash_name, $encrypted_content);   
        } catch (DecryptException $e) {
            dump(["Oops, Something went wrong", $e]);
        }
        
    }
    
    private function file_decryption($file, $file_original_name, $file_hash_name){
        $file_content = $this->read_file_content($file, $file_hash_name);

        try {
            $decrypted_content = Crypt::decryptString($file_content);
           
            echo "File encrypted using laravel helper functions!\n";
            echo 'Memory usage: ' . round(memory_get_usage() / 1048576, 2) . "M\n";
           
            return Storage::put($file_hash_name, $decrypted_content);
        } catch (DecryptException $e) {
            dump(["Oops, Something went wrong", $e]);
        }
    }
}