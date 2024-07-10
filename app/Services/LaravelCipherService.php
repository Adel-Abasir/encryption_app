<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\EncryptException;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\UploadedFile;

class LaravelCipherService 
{
    public function encrypt(UploadedFile $file, string $file_hash_name){
        return $this->file_encryption($file, $file_hash_name);
    }

    public function decrypt(UploadedFile $file, string $file_hash_name){
        return $this->file_decryption($file, $file_hash_name);
    }

    private function store_uploaded_file(UploadedFile $file, string $saving_name):string|false {
        return $file->storeAs('/', $saving_name);
    }

    private function read_file_content(string $file_path):string|null {
        return Storage::get($file_path);
    }

    private function save_file(string $file_path, string $content = ''):bool {
        return Storage::put($file_path, $content);
    }

    private function delete_file(string $file_path):bool {
        return Storage::delete($file_path);
    }

    private function file_encryption(UploadedFile $file, string $file_name){
        $file_path = $this->store_uploaded_file($file, $file_name);

        $file_content = $this->read_file_content($file_path);

        $this->delete_file($file_path);

        try {
            $encrypted_content = Crypt::encryptString($file_content);
            return $this->save_file($file_path, $encrypted_content);
        } catch (EncryptException $e) {
            return ["Oops, Something went wrong. Please try again!", $e];
        }        
    }
    
    private function file_decryption(UploadedFile $file, string $file_name){
        $file_path = $this->store_uploaded_file($file, $file_name);

        $file_content = $this->read_file_content($file_path);

        $this->delete_file($file_path);

        try {
            $decrypted_content = Crypt::decryptString($file_content);
            return $this->save_file($file_path, $decrypted_content);
        } catch (DecryptException $e) {
            return ["Oops, Something went wrong. Please try again!", $e];
        }
    }
}