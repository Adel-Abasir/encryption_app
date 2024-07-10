<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

define('FILE_ENCRYPTION_BLOCKS', 10000);

class LargeFileController extends Controller
{

    public function file_download(Request $request) {
        $file_hash_name = $request->file_hash_name;
        $file_original_name = $request->file_name;
        $end_file_name = $request->end_file_name;

        return Storage::download($end_file_name, $file_original_name);
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
            $end_file_name = $this->file_encryption($file, $file_original_name, $file_hash_name);
        };

        if($process_type == "decrypt"){
            $end_file_name = $this->file_decryption($file, $file_original_name, $file_hash_name);
        };

        return view(
            'welcome',
            [
                'file_info' => [
                    'file_name' => $file_original_name, 
                    'file_extension' => $file_extension, 
                    'file_size' => $file_size,
                    'file_hash_name' => $file_hash_name,
                    'end_file_name' => $end_file_name
                ],
                'process_done' => true,
                'process_info' => $process_type == 'encrypt' ? "Encryption Done!" : "Decryption Done!",
                'file_selected' => true
            ]
        );
    }

    private function file_encryption($file, $file_original_name, $file_hash_name){
        $saved_file = $file->storeAs('/', $file_hash_name);
        $file_path = Storage::path($file_hash_name);
        $uuid = Str::uuid();
        Storage::put($uuid, '');
        $dest_path = Storage::path($uuid);
        $key = env('APP_KEY');

        $cipher = 'aes-256-cbc';
        $ivLenght = openssl_cipher_iv_length($cipher);
        $iv = openssl_random_pseudo_bytes($ivLenght);

        $fpSource = fopen($file_path, 'rb');
        $fpDest = fopen($dest_path, 'w');

        fwrite($fpDest, $iv);

        while (! feof($fpSource)) {
            $plaintext = fread($fpSource, $ivLenght * FILE_ENCRYPTION_BLOCKS);
            $ciphertext = openssl_encrypt($plaintext, $cipher, $key, OPENSSL_RAW_DATA, $iv);
            $iv = substr($ciphertext, 0, $ivLenght);

            fwrite($fpDest, $ciphertext);
        }

        fclose($fpSource);
        fclose($fpDest);
        Storage::delete($saved_file);

        echo "File encrypted using OpenSSL Code!\n";
        echo 'Memory usage: ' . round(memory_get_usage() / 1048576, 2) . "M\n";
        
        return $uuid;        
    }
    
    private function file_decryption($file, $file_original_name, $file_hash_name){
        $saved_file = $file->storeAs('/', $file_hash_name);
        $file_path = Storage::path($file_hash_name);
        $uuid = Str::uuid();
        Storage::put($uuid, '');
        $dest_path = Storage::path($uuid);
        $key = env('APP_KEY');

        $cipher = 'aes-256-cbc';
        $ivLenght = openssl_cipher_iv_length($cipher);

        $fpSource = fopen($file_path, 'rb');
        $fpDest = fopen($dest_path, 'w');

        $iv = fread($fpSource, $ivLenght);

        while (! feof($fpSource)) {
            $ciphertext = fread($fpSource, $ivLenght * (FILE_ENCRYPTION_BLOCKS + 1));
            $plaintext = openssl_decrypt($ciphertext, $cipher, $key, OPENSSL_RAW_DATA, $iv);
            $iv = substr($plaintext, 0, $ivLenght);

            fwrite($fpDest, $plaintext);
        }

        fclose($fpSource);
        fclose($fpDest);
        Storage::delete($saved_file);
       
        echo "File encrypted using OpenSSL Code!\n";
        echo 'Memory usage: ' . round(memory_get_usage() / 1048576, 2) . "M\n";
       
        return $uuid;
    }
}