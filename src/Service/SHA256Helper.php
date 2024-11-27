namespace App\Service;

/**
 * Description of SHA256Helper
 *
 * @author murrel
 */
class SHA256Helper {
    public static function SHA256(?string $s): string
    {
        $s = (string)$s;
        return rtrim(strtr(base64_encode(hash('sha256', $s, true)), '+/', '-_'), '=');
    }
}
