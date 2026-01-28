<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $pageTitle }}</title>

    <style>
        * {
            font-family: 'montserrat', sans-serif;
        }

        body {
            margin: 0;
            padding: 0;
        }

        .page {
            background: #f1f1f1;
            display: flex;
            flex-wrap: wrap;
        }

        .col {
            flex: 1;
            height: 100vh;
            position: relative;
        }

        .countdown-col {
            background: url(https://fadzrinmadu.github.io/hosted-assets/responsive-coming-soon-page-with-awesome-newsletter-html-css-and-js/bg.png) no-repeat center;
            background-size: cover;
        }

        .middle {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: white;
        }

        .back-btn {
            margin-top: 20px;
            display: inline-block;
            padding: 12px 25px;
            background: rgba(255, 255, 255, 0.85);
            color: #333;
            text-decoration: none;
            font-weight: bold;
            border-radius: 25px;
            transition: 0.3s;
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 1);
            transform: scale(1.05);
        }
    </style>
</head>

<body>

    <div class="page">
        <div class="countdown-col col">
            <div class="middle">
                <h1>Coming Soon</h1>

                <a href="{{ url()->previous() }}" class="back-btn">
                    &larr; Kembali
                </a>
            </div>
        </div>
    </div>

</body>

</html>
