<?php

namespace App\Http\Controllers;

use App\Services\OpensslCipherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LargeFileController extends Controller
{
    private OpensslCipherService $opensslCipherService;

    public function __construct()
    {
        $this->opensslCipherService = new OpensslCipherService();
    }

    public function file_processing(Request $request)
    {
        try {
            if ($request->cipher_type == "encrypt") {
                $dest_file_name = $this->opensslCipherService->encrypt($request->hash_name);
            };

            if ($request->cipher_type == "decrypt") {
                $dest_file_name = $this->opensslCipherService->decrypt($request->hash_name);
            };
        } catch (\Throwable $e) {
            return $e->getMessage();
        }

        return response()->json([
            'message' => 'Cipher Prossess Completed Successfully',
            'data' => array_merge($request->all(), ['dest_file_name' => $dest_file_name]),
        ]);
    }

    public function file_download(Request $request)
    {
        if (!Storage::exists('cipher-files/' . $request->dest_file_name))
            return back()->withError("File does not exists or it's been deleted, Please try uploading the file again.")->withInput();

        return Storage::download('cipher-files/' . $request->dest_file_name, $request->download_name);
    }
}
