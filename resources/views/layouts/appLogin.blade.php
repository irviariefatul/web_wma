<!doctype html>
<html lang="en">

<head>
    <title>WMA</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('quixlab/images/logo.png') }}">
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

    <link rel="stylesheet" href="{{ asset('login-form-14/css/style.css') }}">

</head>

<body>
    <section class="ftco-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-12 col-lg-10">
                    <div class="wrap d-md-flex">
                        <div class="img"
                            style="background-image: url({{ asset('login-form-14/images/bg-1.jpg') }});">
                        </div>
                        <div class="login-wrap p-4 p-md-5">
                            @yield('content')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="{{ asset('login-form-14/js/jquery.min.js') }}"></script>
    <script src="{{ asset('login-form-14/js/popper.js') }}"></script>
    <script src="{{ asset('login-form-14/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('login-form-14/js/main.js') }}"></script>

</body>

</html>
