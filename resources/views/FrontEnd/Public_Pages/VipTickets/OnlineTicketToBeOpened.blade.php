<!DOCTYPE html>  
<html lang="en">  

<head>  
    <meta charset="UTF-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1.0">  
    <title>Sabasaba Online Ticket System</title>  
    <style>  
        body {  
            font-family: Arial, sans-serif;  
            background-color: #f4f4f4;  
            margin: 0;  
            padding: 0;  
           padding: 0.5%;
            align-items: center;  
            justify-content: center;  
            height: 95vh;  
            color: #333;  
        }  

        h1 {  
            color: #28a745;  
        }  

       
        button {  
            background-color: #007bff;  
            color: white;  
            border: none;  
            padding: 10px 20px;  
            font-size: 18px;  
            cursor: pointer;  
            border-radius: 5px;  
            transition: background-color 0.3s;  
        }  

        button:hover {  
            background-color: #0056b3;  
        }  

        footer {  
            position: absolute;  
            bottom: 10px;  
            font-size: 14px;  
            color: #666;  
        }  
    </style>  
</head>  

<body >  
    <h1 style="font-size: 2vmax;">Welcome to Sabasaba Online Ticket System!</h1>  
    <p>We are excited to announce that our ticketing platform will open on <strong>May 1, 2025</strong>.</p>
    <p style="text-align: left">
        <b>In three minutes and a few steps, you'll receive your entry ticket.</b><br>
        Phone Number verification, Select and request control number of tickets of your interest
        , Pay and you will receive your tickets details shortly.
    </p> 
    <p>Get ready to purchase your tickets conveniently online!</p>  
    <button onclick="window.location.href='{{ env('APP_URL') }}'">Back to home page</button>  

    <footer>  
        &copy; 2025 Sabasaba Online Ticket System. All rights reserved.  
    </footer>  
</body>  

</html>  