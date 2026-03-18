<!-- FULLY INTEGRATED WITH FLOATING LABEL INPUTS AND RESPONSIVE DESIGN -->
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register | Tulong Kabataan</title>
  <link rel="icon" href="{{ asset ('img/log2.png') }}" type="image/png">
  <!-- Remixicon -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
  <!-- Google Fonts: Playfair Display & Open Sans -->
  <link
        href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,300;0,400;0,700;0,900;1,400&family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap"
        rel="stylesheet">
  <link rel="stylesheet" href="{{ asset ('css/register.css') }}">
</head>
  
<body>
  <div class="container">
    <div class="panel-left">
      <a href="{{ route('landpage') }}" class="logo"><img src="{{ asset ('img/log.png') }}" alt="TKA Logo" style="height: 50px;"></a>
      <a href="{{ route('login.page') }}"><button class="back-btn">&larr; Back</button></a>
      <div class="panel-image" id="panelImage"></div>
      <div class="caption">Capturing Moments,<br />Creating Memories</div>
      <div class="dots">
        <div class="dot active"></div>
        <div class="dot"></div>
        <div class="dot"></div>
      </div> 
    </div>
    <div class="panel-right">
      <h2>Create an account</h2>
      <div class="subtext">
        Already have an account?
        <a href="{{ route('login.page') }}">Log in</a>
      </div>

      <form action="{{ route('register.acc') }}" method="POST">
        @csrf
        <div class="form-row">
          <div class="form-group">
            <input type="text" placeholder="First name" name= "first_name" required>
            <label class="form-label">First Name</label>
          </div>

          <div class="form-group">
            <input type="text" placeholder="Last name" name= "last_name" required>
            <label class="form-label">Last Name</label>
          </div>
        </div>

       <div class="form-group" style="position: relative;">
        <input type="email" id="email" placeholder="" name="email" required>
        <label class="form-label">Email</label>
        <span id="emailFeedback" style="
            color: red;
            font-size: 0.75rem;
            position: absolute;
            top: 0;
            right: 0;
            transform: translateY(-120%);
            display: block;
            white-space: nowrap;
        "></span>
      </div>
       <div class="form-group" style="position: relative;">
      <input type="tel" id="phone" placeholder="Contact Number" name="phone_number" required maxlength="20">
      <label class="form-label">Contact Number</label>
      <span id="phoneFeedback" style="
          color: red;
          font-size: 0.75rem;
          position: absolute;
          top: 0;
          right: 0;
          transform: translateY(-120%);
          display: block;
          white-space: nowrap;
      "></span>
    </div>

      <div class="form-group" style="position: relative;">
          <input type="date" id="birthday" name="birthday" required>
          <label class="form-label">Birthday</label>
          <span id="birthdayFeedback" style="
              color: red;
              font-size: 0.75rem;
              position: absolute;
              top: 0;
              right: 0;
              transform: translateY(-120%);
              display: block;
              white-space: nowrap;
          "></span>
        </div>

        <div class="form-group" style="position: relative;">
        <input type="password" id="password" placeholder="Enter your password" name="password" required>
        <label class="form-label">Password</label>
        <i class="ri-eye-off-line password-toggle" id="togglePassword"></i>
        <span id="passwordFeedback" style="
            color: red;
            font-size: 0.75rem;
            position: absolute;
            top: 0;
            right: 0;
            transform: translateY(-120%);
            display: block;
            white-space: nowrap;
        "></span>
      </div>
       <button id="submitBtn" class="btn-main" type="submit" disabled>Create account</button>
      </form>
      </div>
    </div>
  </div>
    <script src="{{ asset('js/register.js') }}"></script>
</body>
</html>