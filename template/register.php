<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>File Upload with Drag and Drop</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="css/upload.css" />
  <script src="https://js.hcaptcha.com/1/api.js" async defer></script>
</head>

<body class="d-flex flex-column">
  <div class="flex-grow-1">
    <?php $header_01 = 'Register to Get Upload Link';
    include 'template/header.php';
    ?>
    <form id="registerForm" action="" method="POST" enctype="multipart/form-data" class="container mb-3 limt_wigth">
      <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
      <input type="hidden" name="from_action" value="registration">
      <div class="p-3">
        <h3>Please submit your email address to receive a temporary <?php echo AppConfig::APP_NAME ?? "our"; ?> dropbox upload link.</h3>
      </div>
      <div class="mb-3">
        <label for="email" class="form-label">Email:</label>
        <input type="email" name="email" required id="email" value="<?php echo trim($email) ?>" class="form-control" />
      </div>
      <button id="btn_upload" type="submit" class="btn btn-primary">Register</button>
      <div class="h-captcha mt-4 mb-3" data-sitekey="<?php echo AppConfig::SECURITY["HCAPTCHA_SITEKEY"]; ?>"></div>
    </form>
  </div>
  <?php include 'template/footer.php'; ?>
  <script src="js/register.js"></script>
</body>

</html>