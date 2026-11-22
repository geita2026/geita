
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Login | GeMEMIS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" href="<?php echo e(asset('images/nembo.png')); ?>">

    <!-- Bootstrap CSS -->
    <link href="<?php echo e(asset('assets/css/bootstrap.css')); ?>" rel="stylesheet">

    <!-- Icons -->
    <link href="<?php echo e(asset('assets/css/icons/icomoon/styles.css')); ?>" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
        }

        body.login-container {
            margin: 0;
            min-height: 100vh;
            font-family: "Segoe UI", Roboto, Arial, sans-serif;
            background: radial-gradient(
                circle at top,
                #0a3d62,
                #043d3a,
                #021f1f
            );
            overflow-x: hidden;
        }

        #networkCanvas {
            position: fixed;
            inset: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            pointer-events: none;
        }

        .login-wrapper {
            position: relative;
            z-index: 2;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.97);
            max-width: 420px;
            width: 100%;
            border-radius: 16px;
            padding: 40px 35px;
            box-shadow: 0 30px 60px rgba(0, 0, 0, .35);
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header img {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            object-fit: contain;
            margin-bottom: 10px;
        }

        .login-header h4 {
            font-weight: 700;
            color: #173b4b;
        }

        .login-header p {
            color: #777;
        }

        .form-floating {
            position: relative;
            margin-bottom: 24px;
        }

        .form-floating input {
            height: 52px;
            padding: 16px 42px 6px 12px;
            border-radius: 8px;
            width: 100%;
            border: 1px solid #ced4da;
            background: #fff;
        }

        .form-floating input:focus {
            border-color: #1e90a8;
            outline: none;
            box-shadow: 0 0 0 3px rgba(30, 144, 168, .12);
        }

        .form-floating label {
            position: absolute;
            top: 50%;
            left: 12px;
            font-size: 14px;
            color: #777;
            transform: translateY(-50%);
            pointer-events: none;
            transition: .2s ease;
            background: #fff;
            padding: 0 4px;
        }

        .form-floating input:focus + label,
        .form-floating input:not(:placeholder-shown) + label {
            top: 0;
            font-size: 12px;
            color: #1e90a8;
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            top: 17px;
            cursor: pointer;
            color: #777;
            z-index: 3;
        }

        .login-links {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 8px;
            font-size: 13px;
        }

        .login-links a {
            color: #1677a0;
            text-decoration: none;
        }

        .login-actions {
            margin-top: 24px;
        }

        #loginBtn {
            width: 100%;
            min-height: 48px;
            border-radius: 8px;
            font-weight: 600;
            background: #087f9c;
            border: none;
            color: #fff;
            transition: .2s ease;
            cursor: pointer;
            opacity: 1;
        }

        #loginBtn:hover:not(:disabled) {
            background: #065e76;
        }

        #loginBtn:disabled {
            opacity: .55;
            cursor: not-allowed;
        }

        .login-footer {
            margin-top: 20px;
            font-size: 12px;
            color: #777;
            text-align: center;
        }

        .login-footer a {
            color: #087f9c;
        }

        .login-divider {
            margin: 22px 0;
            text-align: center;
            color: #888;
        }

        .text-danger {
            display: block;
            margin-top: 5px;
            font-size: 12px;
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 30px 22px;
            }
        }
    </style>
<link rel="stylesheet" href="<?php echo e(asset('css/tantrade-theme.css')); ?>">
<link rel="stylesheet" href="<?php echo e(asset('css/gememis-auth.css')); ?>">
</head>

<body class="login-container">

<canvas id="networkCanvas"></canvas>

<div class="login-wrapper">

    <form action="<?php echo e(route('login')); ?>"
          method="POST"
          id="loginForm"
          class="login-card"
          autocomplete="on">

        <?php echo csrf_field(); ?>

        <!-- LOGIN HEADER -->
        <div class="login-header">

            <img src="<?php echo e(asset('images/nembo.png')); ?>"
                 alt="GeMEMIS Logo">

            <h4>Login to your account</h4>

            <p>Enter your credentials</p>

        </div>

        <!-- SUCCESS MESSAGE -->
        <?php if(session('success')): ?>
            <div class="alert alert-success">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <!-- ERROR MESSAGE -->
        <?php if(session('fail')): ?>
            <div class="alert alert-danger">
                <?php echo e(session('fail')); ?>

            </div>
        <?php endif; ?>

        <!-- VALIDATION ERRORS -->
        <?php if($errors->any()): ?>
            <div class="alert alert-danger">
                <ul style="margin:0;padding-left:18px;">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- USERNAME / EMAIL -->
        <div class="form-group form-floating">

            <input type="text"
                   name="user_name"
                   id="user_name"
                   class="form-control required-field"
                   placeholder=" "
                   value="<?php echo e(old('user_name')); ?>"
                   autocomplete="username"
                   required
                   autofocus>

            <label for="user_name">
                Email or username *
            </label>

            <?php $__errorArgs = ['user_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <span class="text-danger">
                    <?php echo e($message); ?>

                </span>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

        </div>

        <!-- PASSWORD -->
        <div class="form-group form-floating">

            <input type="password"
                   name="password"
                   id="loginPassword"
                   class="form-control required-field"
                   placeholder=" "
                   autocomplete="current-password"
                   required>

            <label for="loginPassword">
                Password *
            </label>

            <i class="icon-eye toggle-password"
               id="togglePassword"
               role="button"
               tabindex="0"
               aria-label="Show password"
               aria-pressed="false"></i>

            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <span class="text-danger">
                    <?php echo e($message); ?>

                </span>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

        </div>

        <!-- REMEMBER / FORGOT PASSWORD -->
        <div class="login-links">

            <label>
                <input type="checkbox"
                       name="remember"
                       value="1"
                       <?php echo e(old('remember') ? 'checked' : ''); ?>>

                Remember me
            </label>

            <a href="<?php echo e(route('password.request')); ?>">
                Forgot password?
            </a>

        </div>

        <!-- LOGIN BUTTON -->
        <div class="login-actions">

            <button type="submit"
                    id="loginBtn"
                    class="btn btn-primary">

                Login
                <i class="icon-arrow-right14"></i>

            </button>

        </div>

        <!-- REGISTRATION -->
        <div class="login-divider">
            <span>Don't have an account?</span>
        </div>

        <a href="<?php echo e(route('registration')); ?>"
           class="btn btn-default btn-block">

            Sign up

        </a>

        <!-- TERMS -->
        <div class="login-footer">

            By continuing, you agree to our

            <a href="<?php echo e(route('terms_and_condition.index')); ?>">
                Terms &amp; Conditions
            </a>

        </div>

    </form>

</div>

<script>

/* =========================================
   LOGIN FORM VALIDATION
========================================= */

document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('loginForm');

    const username = document.getElementById('user_name');

    const password = document.getElementById('loginPassword');

    const loginBtn = document.getElementById('loginBtn');

    const togglePassword = document.getElementById('togglePassword');

    let submitting = false;

    if (!form || !username || !password || !loginBtn) {
        console.error('SAMS Login: Required form elements not found.');
        return;
    }

    function validateLogin() {

        const usernameValid = username.value.trim().length > 0;

        const passwordValid = password.value.length > 0;

        loginBtn.disabled = !(usernameValid && passwordValid);

    }

    username.addEventListener('input', validateLogin);

    password.addEventListener('input', validateLogin);

    username.addEventListener('change', validateLogin);

    password.addEventListener('change', validateLogin);

    // Initial validation
    validateLogin();


    /* =====================================
       SHOW / HIDE PASSWORD
    ===================================== */

    function changePasswordVisibility() {

        const isPassword = password.type === 'password';

        password.type = isPassword ? 'text' : 'password';

        togglePassword.classList.toggle('icon-eye', !isPassword);

        togglePassword.classList.toggle('icon-eye-blocked', isPassword);

        togglePassword.setAttribute(
            'aria-label',
            isPassword ? 'Hide password' : 'Show password'
        );

        togglePassword.setAttribute(
            'aria-pressed',
            isPassword ? 'true' : 'false'
        );

    }

    if (togglePassword) {

        togglePassword.addEventListener('click', changePasswordVisibility);

        togglePassword.addEventListener('keydown', function (event) {

            if (event.key === 'Enter' || event.key === ' ') {

                event.preventDefault();

                changePasswordVisibility();

            }

        });

    }


    /* =====================================
       PREVENT EMPTY SUBMISSION
    ===================================== */

    form.addEventListener('submit', function (event) {

        if (
            username.value.trim().length === 0 ||
            password.value.length === 0
        ) {

            event.preventDefault();

            validateLogin();

            return;

        }

        if (submitting) {

            event.preventDefault();

            return;

        }

        submitting = true;

        loginBtn.disabled = true;

        loginBtn.innerHTML =
            'Logging in... <i class="icon-spinner2 spinner"></i>';

    });

});


/* =========================================
   NETWORK BACKGROUND ANIMATION
========================================= */

(function () {

    const canvas = document.getElementById('networkCanvas');

    if (!canvas) return;

    const ctx = canvas.getContext('2d');

    if (!ctx) return;

    let nodes = [];

    let animationFrame;

    function resizeCanvas() {

        const ratio = window.devicePixelRatio || 1;

        canvas.width = window.innerWidth * ratio;

        canvas.height = window.innerHeight * ratio;

        canvas.style.width = window.innerWidth + 'px';

        canvas.style.height = window.innerHeight + 'px';

        ctx.setTransform(ratio, 0, 0, ratio, 0, 0);

        nodes = [];

        for (let i = 0; i < 70; i++) {

            nodes.push({

                x: Math.random() * window.innerWidth,

                y: Math.random() * window.innerHeight,

                vx: (Math.random() - 0.5) * 0.6,

                vy: (Math.random() - 0.5) * 0.6

            });

        }

    }

    function animateNetwork() {

        const width = window.innerWidth;

        const height = window.innerHeight;

        ctx.clearRect(0, 0, width, height);

        nodes.forEach(function (node) {

            node.x += node.vx;

            node.y += node.vy;

            if (node.x < 0 || node.x > width) {
                node.vx *= -1;
            }

            if (node.y < 0 || node.y > height) {
                node.vy *= -1;
            }

        });

        for (let i = 0; i < nodes.length; i++) {

            for (let j = i + 1; j < nodes.length; j++) {

                const dx = nodes[i].x - nodes[j].x;

                const dy = nodes[i].y - nodes[j].y;

                const distance = Math.sqrt(dx * dx + dy * dy);

                if (distance < 120) {

                    ctx.strokeStyle = 'rgba(0, 200, 180, 0.15)';

                    ctx.beginPath();

                    ctx.moveTo(nodes[i].x, nodes[i].y);

                    ctx.lineTo(nodes[j].x, nodes[j].y);

                    ctx.stroke();

                }

            }

        }

        animationFrame = requestAnimationFrame(animateNetwork);

    }

    resizeCanvas();

    window.addEventListener('resize', resizeCanvas);

    animateNetwork();

})();

</script>

</body>
</html>

<?php /**PATH C:\xampp\htdocs\geita\resources\views/auth/LoginPage.blade.php ENDPATH**/ ?>