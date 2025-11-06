<?php

include __DIR__.'/init_basic.php';	

if(isset($_GET['GWSESSID'])){
	session_id($_GET['GWSESSID']);
}else{
	session_start();
}


// Your secret key
list($publickey, $secret) = GW::s('SOLVE_RECAPTCHA_PUBLIC_PRIVATE');

// If form submitted, verify captcha
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = $_POST['g-recaptcha-response'] ?? '';
    $remoteip = $_SERVER['REMOTE_ADDR'];

    $verify = file_get_contents(
        "https://www.google.com/recaptcha/api/siteverify?secret={$secret}&response={$response}&remoteip={$remoteip}"
    );
    $result = json_decode($verify, true);

    if (!empty($result['success'])) {
        $_SESSION['human_verified'] = true;
        // Redirect back to original page or home
        $redirect = $_SESSION['redirect_after_captcha'] ?? '/';
        unset($_SESSION['redirect_after_captcha']);
        header("Location: {$redirect}");
        exit;
    } else {
        $error = "Please complete the reCAPTCHA!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Verification Required</title>
	<script>
	function onCaptchaSuccess(token) {
	    // token is the reCAPTCHA response if you need it
	    alert('Thank you, please click ok');
	    document.getElementById('captchaForm').submit();
	    
	}
	</script>    
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
    <h1>Verify you are human</h1>
    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST" id="captchaForm">
	    <div class="g-recaptcha" data-sitekey="<?php echo $publickey;?>"  data-callback="onCaptchaSuccess"></div>
        <br/>
        <button type="submit">Verify</button>
    </form>
</body>
</html>