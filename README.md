<p align="center"><a href="https://github.com/AdelBasiony/encryption_app/tree/master" target="_blank"><img src="https://github.com/AdelBasiony/encryption_app/assets/24531159/ccf49c3d-609b-46a5-8b74-3963aff9519e" width="80%" alt="File Encryption App"></a></p>

## File Encryption App

File Encryption App is an easy-to-use, minimalistic-look cipher web application built using Laravel Framework & OpenSSL for encrypting & decrypting all file types using the AES-256-CBC symmetric key encryption algorithm.

## How to use 

Choose Encrypt or Decrypt, Pick a File & Let's Begin .... That's it.

You'll see the Original file information and a download button to download the encrypted/decrypted file.

## Installation Guide

After Installing Xampp & Composer 

- Run Apache server for localhost
- Clone the Repository in htdocs folder in XAMPP
- Open a terminal window and cd to the project file
- Run the following command
    - ```console
         composer install
      ```
- Copy _.env.example_ and rename it to _.env_
- Run the following commands one by one
    - ```console
         php artisan key:generate
      ```
    - ```console
         php artisan migrate
      ```
    - ```console
         php artisan serve 
      ```
- That's it, Open the provided URL in the terminal and start securing you files.

### Resources List

- [Laravel Documentation](https://laravel.com/docs/11.x)
  - [File Storage](https://laravel.com/docs/11.x/filesystem#file-uploads)
  - [Encryption](https://laravel.com/docs/11.x/encryption)
- [OpenSSL PHP Reference](https://www.php.net/manual/en/ref.openssl.php)
- [Bootstrap Styling](https://getbootstrap.com/docs/5.0/getting-started/introduction/)
