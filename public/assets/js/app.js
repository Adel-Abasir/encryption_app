let browseFile = $("#browseFile");
let uploadBtn = $("#uploadBtn");
let errorMsg = $("#error-message");
let errorMsgJs = $("#error-message-js");

let resumable = new Resumable({
    target: window.routes.file_upload,
    query: {
        _token: $("input[name='_token']").val(),
    }, // CSRF token
    fileType: [],
    chunkSize: 5 * 1024 * 1024, // default is 1*1024*1024, this should be less than your maximum limit in php.ini
    headers: {
        Accept: "application/json",
    },
    testChunks: false,
    throttleProgressCallbacks: 1,
    maxFiles: 1,
    clearInput: false,
    forceChunkSize: true,
    maxChunkRetries: 5,
});

resumable.assignBrowse(browseFile);

$(document).ready(function () {
    uploadBtn.click(function () {
        if (browseFile[0].files.length == 0) return;

        errorMsg.hide();
        errorMsgJs.hide();

        $("#encrypt_radio").prop("disabled", true);
        $("#decrypt_radio").prop("disabled", true);
        browseFile.prop("disabled", true);
        uploadBtn.prop("disabled", true);
        $("#file_info_row").hide();
        showProgress();
        resumable.upload(); // to actually start uploading.
    });
});

resumable.on("fileProgress", function (file) {
    // trigger when file progress update
    updateProgress(Math.floor(file.progress() * 100));
});

resumable.on("fileSuccess", function (file, response) {
    // trigger when file upload complete
    browseFile.val("");

    response = JSON.parse(response);
    hideProgress();

    response.fileInfo.cipher_type = $(
        "input[name='cipher_type']:checked"
    ).val();
    let process_msg =
        response.fileInfo.cipher_type == "encrypt"
            ? "Encrypting..."
            : "Decrypting...";
    $("#file_name").html(response.fileInfo.name);
    $("#file_mime_type").html(response.fileInfo.extension);
    $("#file_size").html(response.fileInfo.size);
    $("#file_process_info").html(`
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">${process_msg}</span>
                </div>
            `);
    $("#file_download_btn").prop("disabled", true);
    $("#file_info_row").show();

    $.ajax({
        type: "POST",
        url: window.routes.file_processing,
        headers: {
            "X-CSRF-Token": $("input[name='_token']").val(),
        },
        data: JSON.stringify(response.fileInfo),
        dataType: "json",
        contentType: "application/json; charset=utf-8",
        success: function (response) {
            let process_msg =
                response.data.cipher_type == "encrypt"
                    ? "Encryption Done!"
                    : "Decryption Done!";
            $("#file_process_info").html(process_msg);
            $("#file_download_btn").prop("disabled", false);

            $("input[name='download_name']").val(response.data.name);
            $("input[name='dest_file_name']").val(response.data.dest_file_name);
        },
        error: function (error) {
            errorMsgJs.show();
            $("#error-msg-content").text(error.responseText);

            $("#file_process_info").html("Failed");
        },
    });

    $("#encrypt_radio").prop("disabled", false);
    $("#decrypt_radio").prop("disabled", false);
    browseFile.prop("disabled", false);
    uploadBtn.prop("disabled", false);
});

resumable.on("fileError", function (file, response) {
    hideProgress();

    $("#encrypt_radio").prop("disabled", false);
    $("#decrypt_radio").prop("disabled", false);
    browseFile.val("");
    browseFile.prop("disabled", false);
    uploadBtn.prop("disabled", false);

    errorMsgJs.show();
    $("#error-msg-content").text("File uploading error, please try agian.");
});

let progress = $(".progress");

function showProgress() {
    progress.find(".progress-bar").css("width", "0%");
    progress.find(".progress-bar").html("Uploading...0%");
    progress.find(".progress-bar").removeClass("bg-success");
    progress.show();
}

function updateProgress(value) {
    progress.find(".progress-bar").css("width", `${value}%`);
    progress.find(".progress-bar").html(`Uploading...${value}%`);
}

function hideProgress() {
    progress.hide();
}
