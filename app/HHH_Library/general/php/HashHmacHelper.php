<?php

namespace App\HHH_Library\general\php;


/**
 * NOTICE:
 *
 * This class is incomplete and is not used anywhere in the program
 * and must be completed when needed.
 */
class HashHmacHelper
{

    /**
     * Compute HMAC SHAT256
     *
     * @param  string $data
     * @param  string $key
     * @param  string $algo
     * @return string
     */
    public static function ComputeHMAC(string $data, string $key, string $algo = "SHA256"): string
    {
        return hash_hmac($algo, $data, $key);
    }

    /**
     * Check validity of received hash
     *
     * @param  mixed $data
     * @param  mixed $key
     * @param  mixed $receivedHash
     * @param  mixed $algo
     * @return bool
     */
    public static function isHashValid(string $data, string $key, string $receivedHash, string $algo = "SHA256"): bool
    {
        return hash_equals(self::ComputeHMAC($data, $key, $algo), $receivedHash);
    }
}
