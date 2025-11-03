<?php
// JWT minimal, configurable por variables de entorno
class JWT {
    private static function secret() {
        return getenv('JWT_SECRET') ?: '';
    }
    private static function algo() {
        return getenv('JWT_ALGO') ?: 'HS512';
    }

    public static function encode($payload) {
        $algo = self::algo();
        $header = json_encode(['typ' => 'JWT', 'alg' => $algo]);
        $payload = json_encode($payload);

        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

        $hashAlgo = strtolower($algo) === 'hs256' ? 'sha256' : (strtolower($algo) === 'hs384' ? 'sha384' : 'sha512');
        $signature = hash_hmac($hashAlgo, $base64UrlHeader . "." . $base64UrlPayload, self::secret(), true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    public static function decode($token) {
        if (empty($token)) return null;

        $parts = explode('.', $token);
        if (count($parts) !== 3) return null;

        $headerJson = base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[0]));
        $payloadJson = base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[1]));
        $signatureProvided = $parts[2];

        $header = json_decode($headerJson, true);
        $algo = isset($header['alg']) ? strtolower($header['alg']) : strtolower(self::algo());
        $hashAlgo = $algo === 'hs256' ? 'sha256' : ($algo === 'hs384' ? 'sha384' : 'sha512');

        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($headerJson));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payloadJson));
        $signature = hash_hmac($hashAlgo, $base64UrlHeader . "." . $base64UrlPayload, self::secret(), true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        if ($base64UrlSignature !== $signatureProvided) return null;

        $payload = json_decode($payloadJson, true);
        if (!$payload) return null;
        if (isset($payload['exp']) && $payload['exp'] < time()) return null;
        return $payload;
    }
}
?>