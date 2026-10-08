<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Create During Booking</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
        rel="stylesheet">
        <style>
            @import url(https://cdn.jsdelivr.net/npm/@xz/fonts@1/serve/hk-grotesk.min.css);

            body {
                font-size: 16px;
                background: #f6f6f5;
                font-family: "HK Grotesk", sans-serif;
            }
            p {
                margin-top: 20px;
                margin-bottom: 24px;
                line-height: 1.5;
                text-align: justify;
            }
            table {
                width: 100%;
            }
            a {
            color: #000000;
                font-weight: 600;
            }
            img {
                width: 100%;
                height: auto;
            }
            .wrapper {
                width: 100%;
                max-width: 567px;
                margin: 32px auto;
            }
            .header {
                padding: 24px 32px;
            }
            .content {
                padding: 20px 32px;
                background-color: #ffffff;
            }
            .footer {
                padding: 20px 32px 24px;
                background-color: #000000;
                color: #ffffff;
                font-size: 14px;
                font-weight: 300;
                line-height: 1.6;
            }
            .footer a {
                font-weight: 600;
                text-decoration: none;
                color: #ffffff;
            }
            a.underline {
                text-decoration: underline;
            }
            a.call-to-action {
                background: #64d068;
                color: #ffffff;
                padding: 16px 67px;
                border-radius: 5px;
                text-decoration: none;
                font-weight: 600px;
            }
            .delivery-logo {
                width: 200px;
            }
            .borderless-logo {
                width: 75%;
                max-width: 178px;
            }
            .social-icon {
                width: 16px;
                height: 16px;
                margin-left 4px;
                text-align: right;
            }
            .social-icons td {
                text-align: right;
            }
            .text-lg {
                font-size: 24px;
            }
            .font-bold {
                font-weight: 600px;
            }

        </style>
</head>

<body>
    <div class="wrapper">
        <div class="header">
          <a href="https://borderless.delivery">
            <img class="delivery-logo" src="{{getMailLogo()??''}}"  height="300px" width="300px" /></a>
        </div>
        <div class="content">
          <table>
            <tr>
              <td>
                <p>Hello {{ $user_name??'' }}</p>
              </td>
            </tr>
            <tr>
              <td>
                    <span><strong>Password</strong>: {{$password}}</span>
              </td>
            </tr>
            <tr>
              <td>
                <p>
                  Best regards, <br />
                  Seema Cab.
                </p>
              </td>
            </tr>
          </table>
        </div>
    </div>
</body>

</html>
