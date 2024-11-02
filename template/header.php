<header>
    <div class="w-100 mb-3" style="background-color: black;">
        <img src="/images/Logo_500x80.png" class="img-fluid header_image" alt="<?php echo AppConfig::APP_NAME ?? ""; ?>" />
    </div>
    <div class="container">
        <h1 class="mb-4 text-center"><?php echo $header_01; ?></h1>
        <?php if ($message): ?>
            <div id=" block_message" class="alert <?php echo $upload_status === true ? 'alert-success' : 'alert-danger'; ?> text-center" role="alert">
                <?php echo $message; ?>
            </div>
        <?php else: ?>
            <div id="block_message" class="alert text-center d-none" role="alert">
            </div>
        <?php endif; ?>
    </div>
</header>