<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logo Design</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Reset some default styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background-color: #f0f0f0;
}

.logo-container {
    text-align: center;
}

.logo {
    display: inline-flex;
    font-size: 100px;
    line-height: 1;
    letter-spacing: -5px;
}

.letter-b {
    color: orange;
    font-weight: bold;
    font-style: italic;
}

.letter-g {
    color: green;
    font-weight: bold;
    font-style: italic;
}

.tagline {
    font-size: 24px;
    color: purple;
    margin-top: 10px;
}
    </style>
<link rel="stylesheet" href="{{ asset('css/tantrade-theme.css') }}">
</head>
<body>
    <div class="logo-container">
        <div class="logo">
            <span class="letter-b">B</span>
            <span class="letter-g">G</span>
        </div>
        <div class="tagline">
            BUSINESS LINK
        </div>
    </div>
</body>
</html>
