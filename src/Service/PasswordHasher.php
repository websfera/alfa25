<?php

namespace App\Service;

final class PasswordHasher
{
    public const ALGO = PASSWORD_DEFAULT;

    /**
     * Zahashuje heslo do hash dle nastaveného algo.
     *
     * @param string $plainPassword Heslo v čitelné podobě
     * @return string Vrací zahashované heslo
     */
    public function hash(#[\SensitiveParameter] string $plainPassword): string
    {
        return password_hash($plainPassword, self::ALGO);
    }

    public function verify(#[\SensitiveParameter] string $plainPassword, string $hashedPassword): bool
    {
        return password_verify($plainPassword, $hashedPassword);
    }

    public function needRehash(string $hashedPassword): bool
    {
        return password_needs_rehash($hashedPassword, self::ALGO);
    }
}
