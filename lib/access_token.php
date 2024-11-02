<?php

function hash_email($email): string
{
    return sha1(strtolower(trim($email)));
}

function connection_redis()
{
    try {
        $redis = new Redis(); #no_qa
        $redis->connect(AppConfig::REDIS_DB["HOST"], AppConfig::REDIS_DB["PORT"]);
        return $redis;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Verify access token in Redis.
 *
 * @param string $token if token missed retuen false
 * @param int $expiration by default ACCESS_TOKEN_EXPIRE setings used
 * @return string|false Rerurn email if token found in Redis databasee else false.
 */
function verify_access_token_redis($token, $expiration = AppConfig::SECURITY["ACCESS_TOKEN_EXPIRE"])
{
    if (!$token) {
        return false;
    }
    $redis = connection_redis();
    if (!$redis) {
        return false;
    }
    try {
        $email = $redis->get('token:' . $token);
        if ($email) {
            $expiration_current = $redis->ttl('token:' . $token);
            if ($expiration_current > $expiration) {
                $redis->expire('token:' . $token, $expiration);
            }
            return $email;
        } else {
            return false;
        }
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Purges old tokens associated with the provided email address from Redis.
 * 
 * This function scans the Redis database for tokens associated with the provided email,
 * deletes all the matching keys at once, and returns true if the operation is successful.
 * 
 * @param Redis $redis The Redis instance to interact with the database.
 * @param string $email The email address to purge old tokens for.
 * @return bool True if the old tokens were successfully purged, false otherwise.
 */
function purge_old_tokens_redis($redis, $email)
{
    $hash_email = hash_email($email);
    $pattern = 'token:' . $hash_email . '*';
    $iterator = null;
    $keys = [];
    try {
        while ($keysBatch = $redis->scan($iterator, $pattern)) {
            foreach ($keysBatch as $key) {
                $keys[] = $key;
            }
        }
        if (!empty($keys)) {
            $redis->del($keys);  // Delete all the keys at once
        }
    } catch (Exception $e) {
        return false;
    }
    return true;
}
/**
 * Store an access token in Redis and associate it with the given email.
 * 
 * This function will purge any existing tokens for the given email.
 * 
 * @param string $token The access token to store
 * @param string $email The email to associate with the token
 * @param int $expiration The number of seconds to expire the token in
 * @return bool True on success, false on failure
 */
function store_access_token_redis($token, $email, $expiration = AppConfig::SECURITY["ACCESS_TOKEN_WAIT"]): bool
{
    $redis = connection_redis();
    if (!$redis) {
        return false;
    }
    try {
        purge_old_tokens_redis($redis, $email);
        $redis->set('token:' . $token, $email, $expiration);
    } catch (Exception $e) {
        return false;
    }
    return true;
}

/**
 * Function to generate a unique token
 * 
 * The generated token will be the SHA1 hash of the given email concatenated with
 * a random hexadecimal string of the given length.
 * 
 * @param string $email The email to associate with the token default: ""
 * @param int $length The length of the random hexadecimal string to generate, default: 16
 * @return string The generated token
 */
function generateToken($email = "", $length = 16)
{
    $hash_email = hash_email($email);
    $token = bin2hex(random_bytes($length));
    return $hash_email . $token;
}



function valid_access_token($token)
{
    return (bool) $token;
}

/**
 * Generates a CSRF (Cross-Site Request Forgery) token by hashing the current session ID (or an empty string if no session ID is set)
 * with a provided secret key using the SHA-256 algorithm.
 *
 * @param string $secret A secret key to hash with the session ID
 * @return string The generated CSRF token
 */
function generateCsrfToken($secret)
{
    return hash_hmac('sha256', session_id() ?? '', $secret);
}


/**
 * Validate a CSRF token
 * 
 * Compares the given CSRF token with the expected token that would be generated
 * with the given secret key. The comparison is done in constant time to prevent
 * timing attacks.
 * 
 * @param string $token The CSRF token to validate
 * @param string $secret The secret key used to generate the token
 * @return bool Whether the token is valid
 */
function validateCsrfToken($token, $secret)
{
    $expectedToken = generateCsrfToken($secret);
    return hash_equals($expectedToken, $token);
}
