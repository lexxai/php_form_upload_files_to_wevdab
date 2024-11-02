<?php
$link = $link ?? '#'; // Default link if not set
$app_name = $app_name ?? 'our'; // Default link if not set
$from =  AppConfig::EMAIL_SMTP["FROM_EMAIL"] ?? "$app_name service";

return "
Please upload your file to $app_name's dropbox.<br>
Once activated this temporary link will time out in one hour.<br>
<br>
Your upload link:<br>
<a href=\"$link\">$link</a><br>
<br>
--
<br>$from<br>";
