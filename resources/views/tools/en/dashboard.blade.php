<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://ao.empregosyoyota.net/assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://ao.empregosyoyota.net/assets/css/font-awesome.min.css" />
    <link rel="stylesheet" href="https://ao.empregosyoyota.net/assets/css/main.css" />
    <title>Group of Apps - Empregos Yoyota</title>
    <style>
        .app-block {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }
        .app-icon {
            font-size: 50px;
            margin-bottom: 15px;
        }
        .footer-margin {
            margin-bottom: 40px;
        }
    </style>
</head>
<body>
    <div id="app">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg bg-white">
            <div class="container">
                <a class="navbar-brand" href="https://ao.empregosyoyota.net"><img src="https://ao.empregosyoyota.net/storage/images/logo.png"></a>
                <div class="navbar-collapse justify-content-end" id="navbarNav">
                    <ul class="nav nav-pills nav-fill">
                        <li class="nav-item">
                            <a href="?lang=en" class="nav-link">EN</a>
                        </li>
                        <li class="nav-item">
                            <a href="?lang=pt" class="nav-link">PT</a>
                        </li>
                        <li class="nav-item">
                            <a href="https://ao.empregosyoyota.net" class="nav-link">
                                <i class="fa fa-home"></i> Back to Home
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        
        <!-- Main Content -->
        <div class="container mt-4 footer-margin">
            <h1 class="text-center mb-4">Group of Apps</h1>
            <div class="row">
                <!-- OCR App Section -->
                <div class="col-md-4 d-flex">
                    <div class="app-block text-center">
                        <i class="fa fa-camera app-icon"></i>
                        <h2>Online OCR</h2>
                        <p>A free tool to convert images and PDFs into text with multi-language support.</p>
                        <a href="https://ao.empregosyoyota.net/en/onlineocr" class="btn btn-primary mt-auto">Go to OCR App</a>
                    </div>
                </div>
                
                <!-- Speech-to-Text App Section -->
                <div class="col-md-4 d-flex">
                    <div class="app-block text-center">
                        <i class="fa fa-microphone app-icon"></i>
                        <h2>Speech to Text</h2>
                        <p>A powerful app to convert spoken words into text efficiently and accurately.</p>
                        <a href="https://ao.empregosyoyota.net/en/speechtotext" class="btn btn-primary mt-auto">Go to Speech-to-Text App</a>
                    </div>
                </div>

                <!-- Placeholder for Future App -->
                <div class="col-md-4 d-flex">
                    <div class="app-block text-center">
                        <i class="fa fa-cogs app-icon"></i>
                        <h2>Future App</h2>
                        <p>An app for future development.</p>
                        <a href="#" class="btn btn-primary mt-auto">Coming Soon</a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <footer class="page-footer navbar-dark bg-dark font-small mdb-color pt-4">
            <div class="container text-center text-md-left">
                <div class="row text-center text-md-left mt-3 pb-3">
                    <div class="col-md-3 col-lg-6 col-xl-3 mx-auto mt-3">
                        <h6 class="text-uppercase mb-4 font-weight-bold">Empregos Yoyota</h6>
                        <a href="https://ao.empregosyoyota.net/terms">Terms</a>
                        <p><a href="https://ao.empregosyoyota.net/en/onlineocr">OCR Online</a></p>
                    </div>
                    <hr class="w-100 clearfix d-md-none">
                    <div class="col-md-4 col-lg-6 col-xl-3 mx-auto mt-3">
                        <h6 class="text-uppercase mb-4 font-weight-bold">Social Network</h6>
                        <p><i class="fa fa-facebook mr-3"></i> <a href="http://facebook.com/empregosyoyota">Empregos Yoyota</a></p>
                        <p><i class="fa fa-instagram mr-3"></i><a href="http://instagram.com/empregosyoyota">empregosyoyota</a></p>
                        <p><i class="fa fa-linkedin mr-3"></i><a href="http://linkedin.com/company/empregosyoyota">Empregos Yoyota</a></p>
                    </div>
                    <div class="col-md-4 col-lg-6 col-xl-3 mx-auto mt-3">
                        <h6 class="text-uppercase mb-4 font-weight-bold">Partners</h6>
                        <p><a href="https://aocursos.com" target="_blank">aoCursos</a></p>
                        <p><a href="https://procureaqui.net" target="_blank">Procure Aqui</a></p>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>