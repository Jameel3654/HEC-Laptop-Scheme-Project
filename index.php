<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HEC Laptop Scheme Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Inter', 'Segoe UI', Roboto, sans-serif;
            overflow-x: hidden;
        }

        
        .bg-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: linear-gradient(rgba(0, 0, 0, 0.45), rgba(0, 0, 0, 0.45)), 
                        url('assets/images/a2.jpg') no-repeat center center;
            background-size: cover;
            filter: blur(5px); 
            transform: scale(1.05); 
        }

        .content-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, 0.1); 
        }

        h2 {
            color: white;
            font-weight: 800;
            letter-spacing: -0.5px;
            text-shadow: 0px 4px 10px rgba(0,0,0,0.3);
            margin-bottom: 40px;
        }

       
        .card {
            border: 1px solid rgba(255, 255, 255, 0.2); 
            border-radius: 12px;
          
            background: rgba(255, 255, 255, 0.75); 
            backdrop-filter: blur(15px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        
        .card:hover {
            background: rgba(255, 255, 255, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
        }

        .card-title {
            color: #1a1a1a;
            font-weight: 700;
            margin-top: 10px;
            margin-bottom: 15px;
            text-transform: uppercase;
            font-size: 1.25rem;
            letter-spacing: 0.5px;
        }

        .text-muted {
            font-size: 1rem;
            color: #444 !important;
            line-height: 1.5;
        }

        
        .btn {
            border-radius: 6px;
            padding: 12px 20px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.85rem;
            border: none;
        }

        .btn-primary {
            background: #0062ff;
        }

        .btn-dark {
            background: #212529;
        }
    </style>
</head>

<body>

<div class="bg-container"></div>

<div class="content-wrapper">
    <div class="container">
        <h2 class="text-center">HEC Laptop Scheme Management System</h2>

        <div class="row justify-content-center g-4">
            <div class="col-md-4 col-sm-10">
                <div class="card p-4 text-center h-100">
                    <div class="card-body d-flex flex-column">
                        <h4 class="card-title">Student Portal</h4>
                        <p class="text-muted mb-4">Signup or Login to your account to apply for the HEC laptop scheme.</p>
                        <div class="mt-auto">
                            <a href="student/login.php" class="btn btn-primary w-100">Enter Student Portal</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-sm-10">
                <div class="card p-4 text-center h-100">
                    <div class="card-body d-flex flex-column">
                        <h4 class="card-title">Admin Panel</h4>
                        <p class="text-muted mb-4">University focal persons and HEC officials verification dashboard.</p>
                        <div class="mt-auto">
                            <a href="admin/login.php" class="btn btn-dark w-100">Admin Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>