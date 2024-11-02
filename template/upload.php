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
    <?php $header_01 = 'Upload file';
    include 'template/header.php';
    ?>
    <form id="uploadForm" action="" method="POST" enctype="multipart/form-data" class="container mb-3 limt_wigth">
      <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
      <input type="hidden" name="from_action" value="upload">
      <div class="mb-3">
        <label for="name" class="form-label">Name:</label>
        <input type="text" name="name" id="name" value="<?php echo trim($name) ?>" class="form-control" />
      </div>
      <div class="mb-3">
        <label for="email" class="form-label">Email:</label>
        <input type="email" name="email" id="email" value="<?php echo trim($email) ?>" class="form-control" <?php echo $email ? 'disabled' : ''; ?> />
      </div>
      <div class="mb-3">
        <label for="image" class="form-label">File:</label>
        <div id="drop-area" class="drop-area" style="word-wrap: anywhere;">
          <div id="drop-area-content" class="drop-area-content d-flex align-items-center justify-content-center text-center ps-2 pe-2" style="min-height: 100%; cursor: pointer;" title="Drag &amp; drop your file here, or click this area to open file browser.">
            Drag &amp; drop your file here, or click this area to open file browser.
          </div>
          <input type="file" name="image" id="image" accept="*" required="" style="display: none">
        </div>
      </div>
      <div class="progress mb-4 d-none" id="progressWrapper" data-bs-toggle="tooltip" data-bs-title="Progress: 0%">
        <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated"
          role="progressbar" value="0" max="100" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
      </div>
      <button id="btn_upload" type="submit" class="btn btn-primary disabled">Upload</button>
      <button id="btn_cancel" type="submit" class="btn btn-secondary d-none">Cancel</button>

    </form>
  </div>
  <?php include 'template/footer.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
  <script src="js/upload.js"></script>
</body>

</html>