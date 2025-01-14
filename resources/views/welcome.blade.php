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
    <div class="container d-flex flex-column justify-content-center" style="height: 80vh">
        <div class="row justify-content-center w-100">
            <a class="col-6 m-2 text-center text-decoration-none" href="/">
                <img src="{{ asset('assets/logo.png') }}" alt="" width="150"
                    class="d-inline-block align-text-top">

                <div class="row text-center p-4">
                    <h1 class="text-body">File Encryption App</h1>
                </div>
            </a>
        </div>

        @if (session('error'))
            <div id="error-message" class="row justify-content-center pt-2 w-100">
                <div class="col-12">
                    <div class="alert alert-danger">{{ session('error') }}</div>
                </div>
            </div>
        @endif

        <div id="error-message-js" class="row justify-content-center pt-2 w-100" style="display: none">
            <div class="col-12">
                <div id="error-msg-content" class="alert alert-danger"></div>
            </div>
        </div>

        <div class="row justify-content-center pt-2 w-100">
            <div class="col-12">
                <form enctype="multipart/form-data" class="row">
                    @csrf

                    <div class="col-12 mb-3 ps-4 fs-5">
                        <div class="row">

                            <div class="form-check col-10 col-sm-5 pl-5">
                                <input class="form-check-input" type="radio" name="cipher_type" id="encyrpt_radio"
                                    value="encrypt" checked required>
                                <label class="form-check-label" for="encyrpt_radio">
                                    Encrypt Files
                                </label>
                            </div>
                            <div class="form-check col-10 col-sm-5 pl-5">
                                <input class="form-check-input" type="radio" name="cipher_type" id="decrypt_radio"
                                    value="decrypt">
                                <label class="form-check-label" for="decrypt_radio">
                                    Decrypt Files
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-10 mb-3">
                        <input type="file" id="browseFile" class="form-control" name="file_upload" required>
                    </div>
                    <div class="col-4 col-lg-2 mb-3">
                        <button id="uploadBtn" type="submit" class="btn btn-primary w-100">let's begin</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row justify-content-center pt-2 w-100">
            <div class="col-12">
                <div style="display: none; height: 2rem;" class="progress">
                    <div class="progress-bar bg-dark progress-bar-animated" role="progressbar"
                        aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 75%; height: 100%">
                        Uploading...75%
                    </div>
                </div>
            </div>
        </div>

        {{-- @if (!empty($file_info)) --}}
        <div id="file_info_row" class="row justify-content-center pt-2" style="display: none">
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
                            <td id="file_name" width="60%" class=" text-wrap"></td>
                            <td id="file_mime_type" width="10%"></td>
                            <td id="file_size" width="10%"></td>
                            <td id="file_process_info" width="10%"></td>
                            <td width="10%">
                                <form action="{{ route('file.download') }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="download_name" value="">
                                    <input type="hidden" name="dest_file_name" value="">
                                    <button type="submit" id="file_download_btn" class="btn btn-light">
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
        {{-- @endif --}}
    </div>

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/resumablejs@1.1.0/resumable.min.js"></script>

    <script>
        window.routes = {
            file_upload: "{{ route('upload') }}",
            file_processing: "{{ route('file.processing') }}"
        };
    </script>

    <script src="{{ asset('assets/js/app.js') }}"></script>


</body>

</html>
