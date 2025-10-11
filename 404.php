<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <link href='./assets/img/iconlogo.png' rel='icon' type='image/x-icon' />
    <link href="https://fonts.googleapis.com/css?family=Arvo" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arvo', serif;
            background: #fff;
        }

        .page_404 {
            padding: 40px 0;
            background: #fff;
            font-family: 'Arvo', serif;
        }

        .page_404 img {
            width: 100%;
        }

        .four_zero_four_bg {
            background-image: url(https://cdn.dribbble.com/users/285475/screenshots/2083086/dribbble_1.gif);
            height: 400px;
            background-position: center;
            background-repeat: no-repeat;
        }

        .four_zero_four_bg h1 {
            font-size: 80px;
        }

        .four_zero_four_bg h3 {
            font-size: 80px;
        }

        .link_404 {
            color: #fff !important;
            padding: 10px 20px;
            background: #39ac31;
            margin: 20px 0;
            display: inline-block;
            text-decoration: none;
            border-radius: 4px;
            transition: background 0.3s ease;
        }

        .link_404:hover {
            background: #2d8a26;
        }

        .contant_box_404 {
            margin-top: -50px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -15px;
        }

        .col-sm-12 {
            flex: 0 0 100%;
            max-width: 100%;
            padding: 0 15px;
        }

        .col-sm-10 {
            flex: 0 0 83.333333%;
            max-width: 83.333333%;
            padding: 0 15px;
        }

        .col-sm-offset-1 {
            margin-left: 8.333333%;
        }

        .text-center {
            text-align: center;
        }

        .contant_box_404 h3 {
            font-size: 2em;
            margin-bottom: 10px;
        }

        .contant_box_404 p {
            font-size: 1.2em;
            margin-bottom: 20px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .col-sm-10 {
                flex: 0 0 100%;
                max-width: 100%;
            }

            .col-sm-offset-1 {
                margin-left: 0;
            }

            .four_zero_four_bg {
                height: 300px;
            }

            .four_zero_four_bg h1 {
                font-size: 60px;
            }

            .contant_box_404 h3 {
                font-size: 1.5em;
            }

            .contant_box_404 p {
                font-size: 1em;
            }
        }
    </style>
</head>
<body>
    <section class="page_404">
        <div class="container">
            <div class="row">	
                <div class="col-sm-12">
                    <div class="col-sm-10 col-sm-offset-1 text-center">
                        <div class="four_zero_four_bg">
                            <h1 class="text-center">404</h1>
                        </div>
                        
                        <div class="contant_box_404">
                            <h3 class="h2">
                                Look like you're lost
                            </h3>
                            
                            <p>The page you are looking for is not available!</p>
                            
                            <a href="#" class="link_404" id="home-link">Go to Home</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="./js/path-manager.js"></script>
    <script>
        // Set dynamic home link when page loads
        document.addEventListener('DOMContentLoaded', function() {
            const homeLink = document.getElementById('home-link');
            if (homeLink && window.pathManager) {
                // Use clean URL without .php extension
                homeLink.href = window.pathManager.getHomeUrl();
            } else {
                // Fallback if pathManager is not available
                homeLink.href = './';
            }
        });
    </script>
</body>
</html>

