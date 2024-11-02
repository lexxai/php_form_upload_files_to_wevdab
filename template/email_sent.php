<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>File Upload with Drag and Drop</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="css/upload.css" />
</head>

<body class="d-flex flex-column">
  <div class="flex-grow-1">
    <?php $header_01 = 'Email was sent';
    include 'template/header.php';
    ?>
    <form id="uploadForm" action="" method="POST" enctype="multipart/form-data" class="container mb-3 limt_wigth">
      <div class="p-3 text-center">
        <h3>Please check your email for the temporary <?php echo AppConfig::APP_NAME ?? "our"; ?> dropbox upload link.</h3>
      </div>
    </form>
  </div>
  <?php include 'template/footer.php'; ?>
</body>

</html>