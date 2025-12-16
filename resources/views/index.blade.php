<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Order System') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            width: 100%;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 10px;
            font-size: 2.5rem;
        }
        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 40px;
            font-size: 1.1rem;
        }
        .links {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .link-card {
            display: block;
            padding: 25px 30px;
            background: #f8f9fa;
            border-radius: 12px;
            text-decoration: none;
            color: #333;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        .link-card:hover {
            background: #e9ecef;
            border-color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }
        .link-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 5px;
            color: #667eea;
        }
        .link-description {
            color: #666;
            font-size: 0.95rem;
        }
        @media (max-width: 600px) {
            .container {
                padding: 30px 20px;
            }
            h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Order System</h1>
        <p class="subtitle">Select your access portal</p>
        <div class="links">
            <a href="/admin" class="link-card">
                <div class="link-title">Admin Portal</div>
                <div class="link-description">Access the admin dashboard</div>
            </a>
            <a href="/partner" class="link-card">
                <div class="link-title">Partner Portal</div>
                <div class="link-description">Access the partner dashboard</div>
            </a>
            <a href="/customer" class="link-card">
                <div class="link-title">Customer Portal</div>
                <div class="link-description">Access the customer dashboard</div>
            </a>
        </div>
    </div>
</body>
</html>

