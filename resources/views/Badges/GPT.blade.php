@php
        #Nembo ya taifa
    $image = Image::make(public_path('badgeAssets/coat_of_arms.png'));
    $base64_tantradeLogo = 'data:image/png;base64,' . base64_encode($image->encode('png'));
    // ***********************
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Geita Mineral Exhibition Badge</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Arial', sans-serif;
      background: #f9f9f9;
      padding: 40px;
      display: flex;
      justify-content: center;
    }

    .badge {
      width: 380px;
      height: 560px;
      background: white;
      border: 1px solid #ccc;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
      position: relative;
      overflow: hidden;
      text-align: center;
    }

    /* Decorative curved border top/bottom */
    .badge::before,
    .badge::after {
      content: "";
      position: absolute;
      width: 100%;
      height: 80px;
      background: url('https://i.imgur.com/l4xAjfs.png') no-repeat center;
      background-size: cover;
      z-index: 1;
    }

    .badge::before {
      top: 0;
    }

    .badge::after {
      bottom: 0;
      transform: rotate(180deg);
    }

    /* Number border sides */
    .side-numbers {
      position: absolute;
      top: 90px;
      bottom: 90px;
      width: 20px;
      background: repeating-linear-gradient(to bottom, #2f2f7f 0px, #2f2f7f 14px, transparent 14px, transparent 20px);
      font-size: 12px;
      color: #2f2f7f;
      writing-mode: vertical-rl;
      text-align: center;
      letter-spacing: 2px;
    }

    .left-numbers {
      left: 0;
    }

    .right-numbers {
      right: 0;
    }

    .logo-top {
      margin-top: 10px;
      width: 60px;
    }

    .tantrade-logo {
      width: 120px;
      margin: 5px auto;
    }

    .title {
      font-size: 13px;
      font-weight: bold;
      margin-top: 3px;
      color: #2f2f7f;
    }

    .event {
      font-size: 14px;
      font-weight: bold;
      margin-top: 3px;
    }

    .event-dates {
      color: green;
      font-weight: bold;
      font-size: 13px;
      margin-top: 3px;
    }

    .profile-wrapper {
      position: relative;
      margin: 15px auto 10px;
      width: 160px;
      height: 160px;
      border-radius: 50%;
      background: white;
      border: 4px solid #004d26;
      z-index: 2;
    }

    .profile-wrapper::before {
      content: "";
      position: absolute;
      top: -8px;
      left: -8px;
      right: -8px;
      bottom: -8px;
      border-radius: 50%;
      border: 3px solid #2f2f7f;
    }

    .profile-pic {
      width: 100%;
      height: 100%;
      border-radius: 50%;
      background-image: url('https://via.placeholder.com/160'); /* Replace dynamically */
      background-size: cover;
      background-position: center;
    }

    .name {
      font-size: 20px;
      font-weight: bold;
      margin-top: 8px;
    }

    .group {
      font-size: 18px;
      font-weight: bold;
      background: #2f4a94;
      color: white;
      padding: 5px 0;
      margin: 5px auto 0;
      width: 80%;
      border-radius: 3px;
    }

    .qrcode {
      margin-top: 12px;
      width: 80px;
      height: 80px;
      margin-left: auto;
      margin-right: auto;
      background-size: cover;
      background-position: center;
      border: 2px solid #ccc;
    }

    .footer {
      display: flex;
      justify-content: space-around;
      align-items: center;
      margin-top: 10px;
      z-index: 2;
      position: relative;
    }

    .footer img {
      height: 30px;
    }

    .website {
      font-size: 13px;
      margin-top: 5px;
      color: #004080;
    }
  </style>
</head>
<body>

<div class="badge">
  <!-- Side Decorative Numbers -->
  <div class="side-numbers left-numbers">49494949494949</div>
  <div class="side-numbers right-numbers">49494949494949</div>

  <!-- Top Logos -->
  <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/f/fb/Coat_of_arms_of_Tanzania.svg/120px-Coat_of_arms_of_Tanzania.svg.png" class="logo-top" alt="Coat of Arms">

  <!-- Title and Event -->
  <div class="title">GEITA MINERAL EXHIBITION SECRETARIAT</div>
  <div class="event">GEITA MINERAL EXHIBITION</div>

  <!-- Profile Image -->
  <div class="profile-wrapper">
    <div class="profile-pic" style="background-image: url('https://via.placeholder.com/160');"></div>
  </div>

  <!-- Name and Group -->
  <div class="name">LATIFA KHAMIS</div>
  <div class="group">GEITA MINERAL EXHIBITION STAFF</div>

  <!-- QR Code -->
  <div class="qrcode" style="background-image: url('https://api.qrserver.com/v1/create-qr-code/?data=https://gememis.go.tz&size=100x100');"></div>

  <!-- Footer Logos -->
  <div class="footer">
    <img src="https://via.placeholder.com/30x30?text=TBS" alt="TBS">
  </div>
  <div class="website">www.gememis.go.tz</div>
</div>

</body>
</html>
