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
        <link
            href="https://fonts.googleapis.com/css2?family=BBH+Bartle&family=League           +Spartan:wght@100..900&display=swap"
            rel="stylesheet">
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
                        <p class="auth-subtitle mb-4">Masukan username dan password

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

                        <form action="{{ route('login.store') }}" method="POST">
                            @csrf
                            <div class="form-group position-relative has-icon-left mb-4">
                                <input type="email" class="form-control form-control-xl" name="email"
                                    placeholder="e-mail" value="{{ old('email') }}" required>
                                <div class="form-control-icon">
                                    <i class="bi bi-person"></i>
                                </div>
                            </div>
                            <div class="form-group position-relative has-icon-left mb-4">
                                <input type="password" class="form-control form-control-xl" name="password"
                                    placeholder="Password" required>
                                <div class="form-control-icon">
                                    <i class="bi bi-shield-lock"></i>
                                </div>
                            </div>
                            {{-- <div class="form-group position-relative has-icon-left mb-4">
                                <div class="captcha">
                                    <span>{!! captcha_img('math') !!}</span>
                                    <button type="button" class="btn btn-danger reload"
                                        id="reload">&#x21bb;</button>
                                </div>
                                <input type="text" name="captcha"
                                    class="form-control @error('captcha') is-invalid @enderror mt-2"
                                    placeholder="Masukan Captcha">
                                @error('captcha')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div> --}}

                            <button type="submit" class="btn btn-primary btn-block btn-lg mt-5 shadow-lg">Log
                                in</button>
                        </form>
                    </div>
                </div>
                <div class="col-lg-8 d-none d-lg-block">
                    <div id="auth-right" class="flex h-full items-center p-4">
                        <div class="space-y-5 p-6 text-white" style="max-width: 600px; margin: auto;">
                            <!-- Company Title -->
                            <div class="d-flex justify-content-center mb-4">
                                <img src="{{ asset('img/logo-white.png') }}" alt="Logo"
                                    style="object-fit: contain; height: auto; width: 250px;">
                            </div>

                            <!-- Tujuan -->
                            <div>
                                <h5 class="text-lg font-semibold text-white text-center">Tujuan</h5>
                                <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-white/80">
                                    <li>Mengembangkan produk digital yang mudah digunakan dan andal</li>
                                    <li>Membantu bisnis beradaptasi dengan transformasi teknologi</li>
                                    <li>Mengutamakan keamanan, performa, dan kepuasan pengguna</li>
                                </ul>
                            </div>

                            <!-- Target -->
                            <div>
                                <h5 class="text-lg font-semibold text-white text-center">Target</h5>
                                <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-white/80">
                                    <li>UMKM dan perusahaan yang ingin meningkatkan efisiensi operasional</li>
                                    <li>Bisnis yang membutuhkan otomasi dan sistem digital terintegrasi</li>
                                    <li>Organisasi yang ingin meningkatkan kualitas layanan pelanggan</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <script src="{{ asset('dist/assets/extensions/jquery/jquery.min.js') }}"></script>

        <script>
            $('#reload').click(function() {
                $.ajax({
                    type: "GET",
                    url: "reload-captcha",
                    success: function(data) {
                        $(".captcha span").html(data.captcha);
                    }
                });
            });
        </script>

    </body>

</html>
