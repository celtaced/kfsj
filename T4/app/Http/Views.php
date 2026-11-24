<?php
/* Layouts/app.blade.php convertido a PHP puro */
?><!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($titulo) ? htmlspecialchars($titulo) : 'Destinos Turísticos de El Salvador'; ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        
        header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 20px;
            text-align: center;
        }
        
        header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        
        header p {
            font-size: 1.1em;
            opacity: 0.9;
        }
        
        nav {
            background: #f8f9fa;
            padding: 15px 20px;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            gap: 20px;
            justify-content: center;
        }
        
        nav a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }
        
        nav a:hover {
            color: #764ba2;
        }
        
        .content {
            padding: 40px 20px;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #dee2e6;
            color: #666;
        }
        
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .card-image {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3em;
            color: white;
        }
        
        .card-body {
            padding: 20px;
        }
        
        .card-title {
            font-size: 1.3em;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        
        .card-info {
            font-size: 0.9em;
            color: #666;
            margin-bottom: 8px;
        }
        
        .card-price {
            font-size: 1.1em;
            color: #667eea;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
            border: none;
            cursor: pointer;
            font-size: 0.95em;
        }
        
        .btn:hover {
            background: #764ba2;
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }

        .btn-back {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .btn-back:hover {
            background: #764ba2;
        }

        .detail-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .detail-item {
            margin: 15px 0;
            font-size: 1.05em;
        }

        .detail-label {
            font-weight: bold;
            color: #667eea;
        }

        .detail-value {
            color: #333;
            margin-top: 5px;
            padding-left: 10px;
            border-left: 3px solid #667eea;
            padding-left: 15px;
        }

        .caracteristicas-list {
            list-style: none;
            padding-left: 0;
        }

        .caracteristicas-list li {
            padding: 8px 0;
            padding-left: 25px;
            position: relative;
            color: #333;
        }

        .caracteristicas-list li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #667eea;
            font-weight: bold;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: inherit;
            font-size: 1em;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 5px rgba(102, 126, 234, 0.3);
        }

        .error {
            color: #dc3545;
            font-size: 0.9em;
            margin-top: 5px;
        }

        .form-btn {
            background: #667eea;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            font-size: 1em;
            cursor: pointer;
            transition: background 0.3s;
        }

        .form-btn:hover {
            background: #764ba2;
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>🌴 Destinos Turísticos de El Salvador 🌴</h1>
            <p>Explora la belleza natural e histórica de nuestro país</p>
        </header>
        
        <nav>
            <a href="/">Inicio</a>
            <a href="/contacto">Contacto</a>
        </nav>
        
        <div class="content">
            <?php if(isset($_SESSION['success'])): ?>
                <div class="alert success-message">
                    <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>
            
            <?php echo $contenido; ?>
        </div>
        
        <footer>
            <p>&copy; 2024 Destinos Turísticos de El Salvador. Desarrollado con Laravel MVC.</p>
        </footer>
    </div>
</body>
</html>
