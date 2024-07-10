<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <title>File Encryption App</title>

    <!-- add icon link -->
    <link rel="icon" href="{{ asset('assets/logo.png') }}" type="image/x-icon"> 
</head>

<body>
    <div class="container align-items-center my-4 mx-auto">
        <div class="row justify-content-center">
            <a class="col-6 m-2 text-center text-decoration-none" href="/">
                <img src="{{ asset('assets/logo.png') }}" alt="" width="150"
                    class="d-inline-block align-text-top">

                <div class="row text-center p-4">
                    <h1 class="text-body">File Encryption App</h1>
                </div>
            </a>
        </div>
    </div>

    <div class="container">
        <div class="row justify-content-center pt-2">
            <div class="col-12">
                <form action="{{ route('file.processing') }}" method="POST" enctype="multipart/form-data"
                    class="row">
                    @csrf

                    <div class="col-12 mb-3  fs-5">
                        <div class="row justify-content-evenly">

                            <div class="form-check col-10 col-sm-4 pl-5">
                                <input class="form-check-input" type="radio" name="encrypt_decrypt_radio"
                                    id="encyrpt_radio" value="encrypt" checked>
                                <label class="form-check-label" for="encyrpt_radio">
                                    Encrypt Files
                                </label>
                            </div>
                            <div class="form-check col-10 col-sm-4 pl-5">
                                <input class="form-check-input" type="radio" name="encrypt_decrypt_radio"
                                    id="decrypt_radio" value="decrypt">
                                <label class="form-check-label" for="decrypt_radio">
                                    Decrypt Files
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-2 mb-3 fs-5">
                        <label for="formFileMultiple" class="form-label">Select Files</label>
                    </div>
                    <div class="col-12 col-lg-8 mb-3">
                        <input class="form-control" type="file" id="formFileMultiple" name="file_upload">
                    </div>
                    <div class="col-4 col-lg-2 mb-3">
                        <button type="submit" class="btn btn-primary w-100">let's begin</button>
                    </div>
                </form>
            </div>
        </div>

        @if (!empty($file_info))
            <div class="row justify-content-center pt-2">
                <div class="col-12 table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Extension</th>
                                <th scope="col">Size</th>
                                <th scope="col">Process</th>
                                <th scope="col">Download</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td width="60%" class=" text-wrap">{{ $file_info['file_name'] }}</td>
                                <td width="10%" class="">{{ $file_info['file_extension'] }}</td>
                                <td width="10%" class="">{{ number_format($file_info['file_size'] / 1000, 0) }}KB</td>
                                <td width="10%" class="">{{ $process_info }}</td>
                                <td width="10%" class="">
                                    <form action="{{ route('file.download') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        {{-- <a href="{{ route('file.encrypt') }}" class="btn btn-success w-100">Encrypt</a> --}}
                                        <input type="hidden" name="file_name" value="{{ $file_info['file_name'] }}">
                                        <input type="hidden" name="file_hash_name" value="{{ $file_info['file_hash_name'] }}">
                                        <input type="hidden" name="end_file_name" value="{{ $file_info['end_file_name'] }}">
                                        <button type="submit" id="encrypt_file_btn" class="btn btn-light" @if (!$process_done) disabled @endif>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32"
                                                fill="currentColor" class="bi bi-file-earmark-arrow-down"
                                                viewBox="0 0 16 16">
                                                <path
                                                    d="M8.5 6.5a.5.5 0 0 0-1 0v3.793L6.354 9.146a.5.5 0 1 0-.708.708l2 2a.5.5 0 0 0 .708 0l2-2a.5.5 0 0 0-.708-.708L8.5 10.293z" />
                                                <path
                                                    d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2M9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z" />
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        @endif


        <!-- Option 1: Bootstrap Bundle with Popper -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
        </script>
</body>

</html>