<?php

namespace App\Http\Middleware;

use Closure;

/**
 * key-value数据解密
 *  注：数据一定要是base64_encode的数据
 *
 * @package App\Http\Middleware
 */
class OpensslDecrypt
{
    protected $private_key = '-----BEGIN PRIVATE KEY-----
MIIBVQIBADANBgkqhkiG9w0BAQEFAASCAT8wggE7AgEAAkEAwHf54K9lGkJwY7WR
YL6sa/i2kPAAGJsCGm79W2+f1F3igieZqgnM1cxtKsG2dNjIdX6YWcR2VQtoJI1R
5ZPoxwIDAQABAkBpFBd0N8323DcH+OT58J+qAwuJbb5fsKEhVV81QGf+RKAEOoQ9
ha4H73zdH0ZX9Rv9Pb4Ds32pkznyNvWKPoRBAiEA9UDp74wLq22SZubXl2ekY11a
NZXhldMGLbIPS/7jqr8CIQDI5vUVH+V+EKP/BS4RQzaZVh6GL4NHVF3AWMkfOHtr
+QIhAOms1vhJ3FiTBvoKmoIE2yldqfUCgLTLIsjpLg//pRZrAiBwPUgTQzytj5Jv
uW8hSdHJHOn0wbqwMRwnh/LlNGMP0QIhAJsx6OX4wNT1uAt+cs4OWxNzt7j5Jo8x
gjA7MUMXMwkl
-----END PRIVATE KEY-----';

    protected $public_key = '-----BEGIN PUBLIC KEY-----
MFwwDQYJKoZIhvcNAQEBBQADSwAwSAJBAMB3+eCvZRpCcGO1kWC+rGv4tpDwABib
Ahpu/Vtvn9Rd4oInmaoJzNXMbSrBtnTYyHV+mFnEdlULaCSNUeWT6McCAwEAAQ==
-----END PUBLIC KEY-----';

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $data = $request->all();

        foreach ($data as $k => $v) {
            if (is_base64($v)) {
                $pi_key = openssl_pkey_get_private($this->private_key);
                openssl_private_decrypt(base64_decode($v), $decrypted, $pi_key);
                if ($decrypted) {
                    $request->offsetSet($k, $decrypted);
                }
            }
        }

        return $next($request);
    }
}
