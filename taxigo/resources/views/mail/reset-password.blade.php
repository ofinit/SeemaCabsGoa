<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Url</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
        rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-md">
        <div class="container justify-content-center">
            <a class="navbar-brand mx-0" href="javascript:void(0);">
                <img src="{{getMailLogo()??''}}"
                    alt="Logo " height="100px" width="200px"></a>
        </div>
    </nav>
    <main class="two-shadow">
        <div class="main-content text-white d-flex align-items-center container">
            <div class="onboarding-successful mx-4 mx-sm-0 flex-sm-row flex-column">
                <div class="onboarding-content">
                    <p>Hi, {{ $user_name }}</p>
                    <p class="w-75">
                        Please click on bellow button for update password.
                        <a href="{{$url??''}}">Update Password</a>
                    </p>
                </div>
            </div>
        </div>
        <footer>
            <p>Copyright © Seema Cab | All rights reserved.</p>
        </footer>
    </main>
</body>

</html>