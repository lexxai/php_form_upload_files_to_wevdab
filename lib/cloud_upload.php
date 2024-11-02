<?php
/**
 * Create a virtual file from an array of key-value pairs.
 *
 * The function will create a virtual file using php://memory or php://temp
 * and populate it with the given key-value pairs in the format:
 * "key: value\n" and return the virtual file as a resource.
 *
 * @param array $parameters An associative array of key-value pairs.
 * @return resource The virtual file as a resource.
 */
function create_virtual_file(array $parameters)
{
    $virtualFileContent = "";
    foreach ($parameters as $key => $value) {
        $virtualFileContent .= "$key: $value\n";
    };
    // Create a virtual file using php://memory or php://temp
    $virtualFile = fopen('php://memory', 'r+');
    fwrite($virtualFile, $virtualFileContent);
    rewind($virtualFile);
    return $virtualFile;
}


/**
 * Upload a file to Nextcloud.
 *
 * If $file is a virtual file (i.e. a resource), skip checking if the file exists.
 * If $timestamp is null, initialize it to the current date and time in the format YYYY-MM-DD_HH-MM-SS.
 *
 * @param resource|string $file The file to be uploaded.
 * @param string $filename The original filename of the file.
 * @param string|null $timestamp The timestamp to be used for the new filename. If null, use current date and time.
 *
 * @return array A two-element array. The first element is a boolean indicating success or failure.
 * The second element is the timestamp used for the new filename.
 */
function cloud_upload($file, $filename, $timestamp = null)
{

    // Validate that the file exists
    // If $file is a virtual file, skip this check
    if (is_resource($file)) {
        rewind($file); // Move to the beginning of the "file" for reading
    } else if (!file_exists($file)) {
        echo "File does not exist: $file";
        return [false, 0]; // Exit the function if the file is not found
    }

    // Initialize timestamp if it is null
    if ($timestamp === null) {
        $timestamp = date('Ymd-His'); // Format: YYYY-MM-DD_HH-MM-SS
    }

    $extension = pathinfo($filename, PATHINFO_EXTENSION); // Get the file extension
    $new_filename = "$timestamp.$extension"; // Create the new filename

    $NEXTCLOUD_URL = 'https://' . AppConfig::NEXTCLOUD['DOMAIN'] . '/remote.php/dav/files/'
        . AppConfig::NEXTCLOUD['USERNAME'] . '/' . AppConfig::NEXTCLOUD['FOLDER'];

    // cURL session initialization
    $remoteFilePath = $NEXTCLOUD_URL . '/' . $new_filename;

    try {
        $ch = curl_init($remoteFilePath);

        // cURL options for file upload
        curl_setopt($ch, CURLOPT_USERPWD, AppConfig::NEXTCLOUD["USERNAME"] . ":" . AppConfig::NEXTCLOUD["PASSWORD"]);
        curl_setopt($ch, CURLOPT_PUT, true);
        curl_setopt($ch, CURLOPT_INFILE, is_resource($file) ? $file : fopen($file, 'r'));
        curl_setopt($ch, CURLOPT_INFILESIZE, is_resource($file) ? fstat($file)['size'] : filesize($file));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // Set to true in production

        // Execute the request and get the response
        $response = curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Close the cURL session
        curl_close($ch);
    } catch (Exception $e) {
        $error_msg = "Cloud upload failed. Exception: " . $e->getMessage();
        error_log($error_msg);
        return [false, $timestamp]; // Indicate failure
    }

    if ($http_status == 201) {
        // echo "File \"$new_filename\" successfully uploaded!<br>";
        return [true, $timestamp]; // Indicate success
    } else {
        $error_msg =  "Failed to upload file \"$new_filename\". HTTP Status: $http_status";
        error_log("Failed uplaod: $error_msg");
        return [false, $timestamp]; // Indicate failure
    }
}

// Example usage
//list($upload_status, $upload_timestamp) = cloud_upload('apple-touch-icon.txt');
//echo "Upload Status: " . ($upload_status ? "Success" : "Failure") . "\n";
//echo "Timestamp: $upload_timestamp\n";
