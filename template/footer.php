<footer class="footer mt-auto py-3 bg-light text-center">
    <div class="container">
        <span class="text-muted">&copy; <?php echo date('Y'); ?></span>
        <span class="text-muted">
            <?php echo (AppConfig::APP_NAME ? trim(AppConfig::APP_NAME) . ". " : "") . "Version: " . AppConfig::APP_VERSION; ?>
        </span>
    </div>
</footer>