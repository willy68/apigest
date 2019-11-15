<?php
/**
 * JSON Web Token implementation, based on this spec:
 * http://tools.ietf.org/html/draft-ietf-oauth-json-web-token-06
 *
 * PHP version 5
 *
 * @category Authentication
 * @package  Authentication_JWT
 * @author   Neuman Vong <neuman@twilio.com>
 * @author   Anant Narayanan <anant@php.net>
 * @license  http://opensource.org/licenses/BSD-3-Clause 3-clause BSD
 * @link     https://github.com/firebase/php-jwt
 */
class JWT
{

	/**
	 * When checking nbf, iat or expiration times,
	 * we want to provide some extra leeway time to
	 * account for clock skew.
	 */
	public static $leeway = 0;

	/**
	 * Decodes a JWT string into a PHP object.
	 *
	 *
     * Extract the jwt from the Bearer
     *
     * list($jwt) = sscanf( $authHeader->toString(), 'Authorization: Bearer %s');
	 *
	 * header('HTTP/1.0 401 Unauthorized');
	 *
	 * Javascript client
	 * store.decodeToken = function(jwt){
     *    var a = jwt.split(".");
     *    return  b64utos(a[1]);
	 * }
	 *
	 * @param string      $jwt    The JWT
	 * @param string|null $key    The secret key
	 * @param bool        $verify Don't skip verification process 
	 *
	 * @return object      The JWT's payload as a PHP object
	 * @throws UnexpectedValueException Provided JWT was invalid
	 * @throws DomainException          Algorithm was not provided
	 * 
	 * @uses jsonDecode
	 * @uses urlsafeB64Decode
	 */
	public static function decode($jwt, $key = null, $verify = true)
	{
		$tks = explode('.', $jwt);
		if (count($tks) != 3) {
			throw new UnexpectedValueException('Wrong number of segments');
		}
		list($headb64, $bodyb64, $cryptob64) = $tks;
		if (null === ($header = JWT::jsonDecode(JWT::urlsafeB64Decode($headb64)))) {
			throw new UnexpectedValueException('Invalid segment encoding');
		}
		if (null === ($payload = JWT::jsonDecode(JWT::urlsafeB64Decode($bodyb64)))) {
			throw new UnexpectedValueException('Invalid segment encoding');
		}
		$sig = JWT::urlsafeB64Decode($cryptob64);
		if ($verify) {
			if (empty($header->alg)) {
				throw new DomainException('Empty algorithm');
			}
			if ($sig != JWT::sign("$headb64.$bodyb64", $key, $header->alg)) {
				throw new UnexpectedValueException('Signature verification failed');
			}
			
			$timestamp = time();
			
			// Check if the nbf if it is defined. This is the time that the
			// token can actually be used. If it's not yet that time, abort.
			if (isset($payload->nbf) && $payload->nbf > ($timestamp + static::$leeway)) {
				throw new UnexpectedValueException(
					'Cannot handle token prior to ' . date(DateTime::ISO8601, $payload->nbf)
				);
			}

			// Check that this token has been created before 'now'. This prevents
			// using tokens that have been created for later use (and haven't
			// correctly used the nbf claim).
			if (isset($payload->iat) && $payload->iat > ($timestamp + static::$leeway)) {
				throw new UnexpectedValueException(
					'Cannot handle token prior to ' . date(DateTime::ISO8601, $payload->iat)
				);
			}
			// Check if this token has expired.
			if (isset($payload->exp) && ($timestamp - static::$leeway) >= $payload->exp) {
				throw new UnexpectedValueException('Expired token');
			}
		}
		return $payload;
	}
	/**
	 * Converts and signs a PHP object or array into a JWT string.
	 *
	 * Header
	 * {
	 *  "alg": "HS256",
	 *  "typ": "JWT"
	 * }
	 *
	 * Registered Claims
     * iss: The issuer of the token
     * sub: The subject of the token
     * aud: The audience of the token
     * exp: This will probably be the registered claim most often used. This will define the expiration in NumericDate value. The expiration MUST be after the current date/time.
     * nbf: Defines the time before which the JWT MUST NOT be accepted for processing
     * iat: The time the JWT was issued. Can be used to determine the age of the JWT
     * jti: Unique identifier for the JWT. Can be used to prevent the JWT from being replayed. This is helpful for a one time use token.
	 *
	 * $data = [
     *   'iat'  => $issuedAt,         // Issued at: time when the token was generated
     *   'jti'  => $tokenId,          // Json Token Id: an unique identifier for the token
     *   'iss'  => $serverName,       // Issuer
     *   'nbf'  => $notBefore,        // Not before
     *   'exp'  => $expire,           // Expire
     *   'data' => [                  // Data related to the signer user
     *       'userId'   => $rs['id'], // userid from the users table
     *       'userName' => $username, // User name
     *   ]
     * ];
	 *
	 * @param object|array $payload PHP object or array
	 * @param string       $key     The secret key
	 * @param string       $algo    The signing algorithm. Supported
	 *                              algorithms are 'HS256', 'HS384' and 'HS512'
	 *
	 * @return string      A signed JWT
	 * @uses jsonEncode
	 * @uses urlsafeB64Encode
	 */
	public static function encode($payload, $key, $algo = 'HS256')
	{
		$header = array('typ' => 'JWT', 'alg' => $algo);
		$segments = array();
		$segments[] = JWT::urlsafeB64Encode(JWT::jsonEncode($header));
		$segments[] = JWT::urlsafeB64Encode(JWT::jsonEncode($payload));
		$signing_input = implode('.', $segments);
		$signature = JWT::sign($signing_input, $key, $algo);
		$segments[] = JWT::urlsafeB64Encode($signature);
		return implode('.', $segments);
	}
	/**
	 * Sign a string with a given key and algorithm.
	 *
	 * @param string $msg    The message to sign
	 * @param string $key    The secret key
	 * @param string $method The signing algorithm. Supported
	 *                       algorithms are 'HS256', 'HS384' and 'HS512'
	 *
	 * @return string          An encrypted message
	 * @throws DomainException Unsupported algorithm was specified
	 */
	public static function sign($msg, $key, $method = 'HS256')
	{
		$methods = array(
			'HS256' => 'sha256',
			'HS384' => 'sha384',
			'HS512' => 'sha512',
		);
		if (empty($methods[$method])) {
			throw new DomainException('Algorithm not supported');
		}
		return hash_hmac($methods[$method], $msg, $key, true);
	}
	/**
	 * Decode a JSON string into a PHP object.
	 *
	 * @param string $input JSON string
	 *
	 * @return object          Object representation of JSON string
	 * @throws DomainException Provided string was invalid JSON
	 */
	public static function jsonDecode($input)
	{
		$obj = json_decode($input);
		if (function_exists('json_last_error') && $errno = json_last_error()) {
			JWT::_handleJsonError($errno);
		} else if ($obj === null && $input !== 'null') {
			throw new DomainException('Null result with non-null input');
		}
		return $obj;
	}
	/**
	 * Encode a PHP object into a JSON string.
	 *
	 * @param object|array $input A PHP object or array
	 *
	 * @return string          JSON representation of the PHP object or array
	 * @throws DomainException Provided object could not be encoded to valid JSON
	 */
	public static function jsonEncode($input)
	{
		$json = json_encode($input);
		if (function_exists('json_last_error') && $errno = json_last_error()) {
			JWT::_handleJsonError($errno);
		} else if ($json === 'null' && $input !== null) {
			throw new DomainException('Null result with non-null input');
		}
		return $json;
	}
	/**
	 * Decode a string with URL-safe Base64.
	 *
	 * @param string $input A Base64 encoded string
	 *
	 * @return string A decoded string
	 */
	public static function urlsafeB64Decode($input)
	{
		$remainder = strlen($input) % 4;
		if ($remainder) {
			$padlen = 4 - $remainder;
			$input .= str_repeat('=', $padlen);
		}
		return base64_decode(strtr($input, '-_', '+/'));
	}
	/**
	 * Encode a string with URL-safe Base64.
	 *
	 * @param string $input The string you want encoded
	 *
	 * @return string The base64 encode of what you passed in
	 */
	public static function urlsafeB64Encode($input)
	{
		return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
	}
	/**
	 * Helper method to create a JWT refresh Token.
	 *
	 * @param obj $jwt jwt object to refresh
	 *
	 * @return jwt token encoded
	 */
	public static function refreshToken($jwt, $key = null, $exp = 900)
	{
		$tks = explode('.', $jwt);
		if (count($tks) != 3) {
			throw new UnexpectedValueException('Wrong number of segments');
		}
		list($headb64, $bodyb64, $cryptob64) = $tks;
		if (null === ($header = JWT::jsonDecode(JWT::urlsafeB64Decode($headb64)))) {
			throw new UnexpectedValueException('Invalid segment encoding');
		}
		if (null === ($payload = JWT::jsonDecode(JWT::urlsafeB64Decode($bodyb64)))) {
			throw new UnexpectedValueException('Invalid segment encoding');
		}

		$algo = $header->alg;
		$timestamp = time();
		$leeway = (isset($payload->nbf) && isset($payload->iat)) ? ($payload->nbf - $payload->iat) : static::$leeway;
		
		
		// Check that this token has been created before 'now'. This prevents
		// using tokens that have been created for later use (and haven't
		// correctly used the nbf claim).
		if (isset($payload->iat)) {
			$payload->iat = $timestamp;
		}
		// Check if the nbf if it is defined. This is the time that the
		// token can actually be used.
		if (isset($payload->nbf)) {
			$payload->nbf = $timestamp + $leeway;
		}

		// Check if this token has expired.
		if (isset($payload->exp)) {
			$payload->exp = $timestamp + $leeway + $exp;
		}

		return JWT::encode($payload, $key, $algo);
	}
	/**
	 * Helper method to create a JSON error.
	 *
	 * @param int $errno An error number from json_last_error()
	 *
	 * @return void
	 */
	private static function _handleJsonError($errno)
	{
		$messages = array(
			JSON_ERROR_DEPTH => 'Maximum stack depth exceeded',
			JSON_ERROR_CTRL_CHAR => 'Unexpected control character found',
			JSON_ERROR_SYNTAX => 'Syntax error, malformed JSON'
		);
		throw new DomainException(
			isset($messages[$errno])
			? $messages[$errno]
			: 'Unknown JSON error: ' . $errno
		);
	}
}
