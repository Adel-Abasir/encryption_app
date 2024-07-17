<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

define('APP_KEY', env('APP_KEY'));
define('FILE_ENCRYPTION_BLOCKS', 10000);
define('CIPHER', 'aes-256-cbc');

class OpensslCipherService {
    public function encrypt(UploadedFile $file, string $file_hash_name){
        return $this->file_encryption($file, $file_hash_name);
    }

    public function decrypt(UploadedFile $file, string $file_hash_name){
        return $this->file_decryption($file, $file_hash_name);
    }

    private function store_uploaded_file(UploadedFile $file, string $saving_name):string|false {
        return $file->storeAs('/', $saving_name);
    }

    private function get_file_full_path(string $file_name):string {
        return Storage::path($file_name);
    }

    private function save_file(string $file_path, string $content = ''):bool {
        return Storage::put($file_path, $content);
    }

    private function delete_file(string $file_path):bool {
        return Storage::delete($file_path);
    }

    private function prepare_cipher_files(UploadedFile $file, string $file_name){
        $unique_name = Str::uuid();
        $this->save_file($unique_name);

        return [
            $this->store_uploaded_file($file, $file_name),
            $this->get_file_full_path($file_name),
            $this->get_file_full_path($unique_name),
            $unique_name
        ];
    }

    private function file_encryption(UploadedFile $file, string $file_name){
        [
            $uploaded_file_path,
            $file_full_path,
            $dest_file_full_path,
            $dest_file_name
        ] = $this->prepare_cipher_files($file, $file_name);

        $ivLenght = openssl_cipher_iv_length(CIPHER);
        $iv = openssl_random_pseudo_bytes($ivLenght);

        $fpSource = fopen($file_full_path, 'rb');
        $fpDest = fopen($dest_file_full_path, 'w');

        fwrite($fpDest, $iv);

        while (!feof($fpSource)) {
            $plaintext = fread($fpSource, $ivLenght * FILE_ENCRYPTION_BLOCKS);
            $ciphertext = openssl_encrypt($plaintext, CIPHER, APP_KEY, OPENSSL_RAW_DATA, $iv);
            $iv = substr($ciphertext, 0, $ivLenght);

            fwrite($fpDest, $ciphertext);
        }

        fclose($fpSource);
        fclose($fpDest);

        $this->delete_file($uploaded_file_path);

        return $dest_file_name;        
    }

    private function file_decryption(UploadedFile $file, string $file_name){
        [
            $uploaded_file_path,
            $file_full_path,
            $dest_file_full_path,
            $dest_file_name
        ] = $this->prepare_cipher_files($file, $file_name);

        $ivLenght = openssl_cipher_iv_length(CIPHER);

        $fpSource = fopen($file_full_path, 'rb');
        $fpDest = fopen($dest_file_full_path, 'w');

        $iv = fread($fpSource, $ivLenght);

        while (! feof($fpSource)) {
            $ciphertext = fread($fpSource, $ivLenght * (FILE_ENCRYPTION_BLOCKS + 1));
            $plaintext = openssl_decrypt($ciphertext, CIPHER, APP_KEY, OPENSSL_RAW_DATA, $iv);
            $iv = substr($ciphertext, 0, $ivLenght);

            fwrite($fpDest, $plaintext);
        }

        fclose($fpSource);
        fclose($fpDest);

        $this->delete_file($uploaded_file_path);

        return $dest_file_name;
    }

}