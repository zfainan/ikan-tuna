<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login</title>

        <link rel="shortcut icon" href="{{ asset('img/logo-removebg.png') }}" type="image/x-icon">
        <link rel="stylesheet" href="{{ asset('dist/assets/compiled/css/app.css') }}">
        <link rel="stylesheet" href="{{ asset('dist/assets/compiled/css/app-dark.css') }}">
        <link rel="stylesheet" href="{{ asset('dist/assets/compiled/css/auth.css') }}">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=BBH+Bartle&family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    </head>

    <body>
        <script src="{{ asset('dist/assets/static/js/initTheme.js') }}"></script>
        <div id="auth">

            <div class="row h-100">
                <div class="col-lg-4 col-12">
                    <div id="auth-left">
                        <div class="auth-logo" style="margin-bottom: 0px; padding-bottom: 0px;">
                            <a href="/login" class="d-flex justify-content-center">
                                <img src="{{ asset('img/Logo.png') }}" alt="Logo"
                                    style="object-fit: contain; height: auto; width: 250px;">
                            </a>
                        </div>
                        <p class="auth-subtitle mb-2">Masukan username dan password

                            <!-- Display validation errors -->
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Display login error message -->
                            @if (Session::has('login'))
                                <div class="alert alert-danger">
                                    {{ Session::get('login') }}
                                </div>
                            @endif
                        <style>
                            .small-input {
                                height: 38px;
                                font-size: 14px;
                                padding: 6px 10px;
                            }
                        </style>

                        <form action="{{ route('login.store') }}" method="POST">
                            @csrf
                            <div class="form-group position-relative has-icon-left mb-3">
                                <input type="email" class="form-control small-input" name="email"
                                    placeholder="e-mail" value="{{ old('email') }}" required>
                                <div class="form-control-icon">
                                    <i class="bi bi-person"></i>
                                </div>
                            </div>
                            <div class="form-group position-relative has-icon-left mb-3">
                                <input type="password" class="form-control small-input" name="password"
                                    placeholder="Password" required>
                                <div class="form-control-icon">
                                    <i class="bi bi-shield-lock"></i>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block shadow-lg">Login</button>
                        </form>
                    </div>
                </div>
                <div class="col-lg-8 d-none d-lg-block">
                    <div id="auth-right" class="flex h-full items-center p-4">
                        <div class="space-y-5 p-6 text-white" style="max-width: 820px; margin: auto;">
                            <!-- Company Title -->
                            <div class="d-flex justify-content-center mb-4">
                                <img src="{{ asset('img/logo-white.png') }}" alt="Logo"
                                    style="object-fit: contain; height: auto; width: 200px;">
                            </div>

                            <!-- Tujuan -->
                            <div>
                                <h5 class="text-lg font-semibold text-white text-center">Tujuan</h5>
                                <ol type="a" class="mt-2 list-inside list-[lower-alpha] space-y-1 text-sm text-white/80" style="text-align: justify;">
                                    <li>Create awarenes at all levels of management, staff and employess about the importance of the quality management system according to the standart of food safety.</li>
                                        <i>Menciptakan kesadaran di semua level manajemen, staff dan karyawan tentang pentingnya sistem manajemen kualitas.</i>
                                    <li>Creating a system of the quality management system.</li>
                                        <i>Menciptakan sebuah sistem manajemen kualitas sesuai dengan standar keamanan.</i>
                                    <li>Ensure the implementation and monitoring of  quality management system.</li>
                                        <i>Menjamin terlaksananya dan pemantauan sistem manajemen kualitas.</i>
                                    <li>Provide security, legality and quality of products to consumers.</li>
                                        <i>Menyediakan keamanan legalitas dan kualitas produk untuk pelanggan.</i>
                                </ol>
                            </div>

                            <!-- Target -->
                            <div>
                                <h5 class="text-lg font-semibold text-white text-center">Target</h5>
                                <ol type="a" class="mt-2 list-inside list-[lower-alpha] space-y-1 text-sm text-white/80" style="text-align: justify">
                                    <li>The whole range of manager, staff and employess, to explain and understand the importance of the implementation of the quality management system, according to the position and responsibilities of each.</li>
                                        <i>Seluruh manajer, staff dan karyawan, untuk menjelaskan dan paham pentingnya melaksanakan sistem manajemen kualitas sesuai dengan posisi dan tanggung jawabnya masing-masing.</i>
                                    <li>Availability of all manuals and suspporting documents necessary to implement the quality management system according to the standard of food safety.</li>
                                        <i>Tersedianya semua panduan dan dokumen pendukung yang diperlukan untuk melaksanakan sistem manajemen kualitas sesuai dengan standar keamanan.</i>
                                    <li>All levels of management, staff and employess to consistenly implement all  requirements and standards required, both practice and documentation, of all cluases that are in the quality management system according to the standard of food safety.</li>
                                        <i>Semua level manajemen, staff dan karyawan untuk melaksanakan secara konsisten semua persyaratan dan standar yang ada, kedua praktek dan dokumen, semua pasal yang terdapat didalam sistem manajemen kualitas sesuai dengan standar keamanan.</i>
                                    <li>Able to demonstrate the implementation and all documentation to the auditor or the consumer a complete and transparent when requested at any time.</li>
                                        <i>Mampu menyajikan pelaksanaan dan semua dokumen kepada auditor atau pelanggan secara lengkap dan transparan ketikan diminta kapanpun.</i>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>

</html>
