<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Art Gallery | Home</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap');

        body {
            font-family: 'Playfair Display', serif;
            background-color: #121212;
            color: #ffffff;
            transition: background 0.5s ease-in-out, color 0.5s ease-in-out;
        }
        .light-mode {
            background-color: #ffffff;
            color: #121212;
        }
        .nav-menu {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
        }
        .nav-menu a {
            margin: 0 15px;
            font-size: 18px;
            font-weight: bold;
            transition: color 0.3s;
        }
        .nav-menu a:hover {
            color: #b8895c;
        }
        .profile-icon {
            font-size: 24px;
            cursor: pointer;
            color: white;
            transition: color 0.3s;
        }
        .profile-icon:hover {
            color: #b8895c;
        }
        .hero-section {
            position: relative;
            width: 100%;
            height: 100vh;
            background: url('images/art6.jpg') center/cover no-repeat;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        .hero-overlay {
            padding: 60px;
            border-radius: 10px;
            animation: fadeIn 1.5s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            margin-top: 20px;
            font-size: 18px;
            font-weight: bold;
            border-radius: 6px;
            transition: all 0.3s ease-in-out;
        }
        .btn-primary {
            background-color: #b8895c;
            color: white;
        }
        .btn-primary:hover {
            background-color: #9a6b42;
        }
        .section-container {
            max-width: 1200px;
            margin: auto;
            padding: 80px 20px;
            text-align: center;
        }
        .section-heading {
            font-size: 50px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #ffffff;
        }
        .section-subtext {
            font-size: 20px;
            font-style: italic;
            color: #b8895c;
        }
        .search-bar {
            display: flex;
            width: 300px;
            margin-left: 20px;
        }
        .search-bar input {
            flex-grow: 1;
            padding: 10px 15px;
            border: none;
            border-radius: 4px 0 0 4px;
            font-size: 16px;
        }
        .search-bar button {
            padding: 10px 15px;
            background-color: #b8895c;
            color: white;
            border: none;
            border-radius: 0 4px 4px 0;
            cursor: pointer;
        }
        .category-card {
            transition: transform 0.3s ease;
        }
        .category-card:hover {
            transform: translateY(-10px);
        }
        .artwork-card {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .artwork-card:hover {
            box-shadow: 0 10px 20px rgba(0,0,0,0.3);
        }
        .artwork-overlay {
            position: absolute;
            bottom: -100%;
            left: 0;
            right: 0;
            background: rgba(0,0,0,0.7);
            padding: 20px;
            transition: bottom 0.3s ease;
        }
        .artwork-card:hover .artwork-overlay {
            bottom: 0;
        }
    </style>
</head>