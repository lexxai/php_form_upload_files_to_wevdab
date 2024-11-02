<?php

/**
 * Validate a given hCaptcha response.
 *
 * @param string $captcha_response The user's response to the hCaptcha challenge.
 * @return bool Whether the response is valid.
 */
function validate_captcha($captcha_response)
{
    if (!$captcha_response) {
        return false;
    }
    $secret = AppConfig::SECURITY["HCAPTCHA_SECRET"];
    $data = array(
        'secret' => $secret,
        'response' => $captcha_response
    );
    $verify = curl_init();
    curl_setopt($verify, CURLOPT_URL, "https://hcaptcha.com/siteverify");
    curl_setopt($verify, CURLOPT_POST, true);
    curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($verify);
    // var_dump($response);
    $responseData = json_decode($response);
    return $responseData->success;
}
