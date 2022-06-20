<?php
namespace Nwehrhan\TotpScript;

require_once __DIR__ . '/../vendor/autoload.php';

use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

$google2fa = new Google2FA();

$secretKey = $google2fa->generateSecretKey();
    
echo wrapInDiv('Secret Key would be saved in Database: ' . $secretKey);


$qrCodeUrl = $google2fa->getQRCodeUrl(
    'ResearchGate',
    'thanksForScanningMe@nicholaswehrhan.com',
    $secretKey
);

$writer = new Writer(
    new ImageRenderer(
        new RendererStyle(400),
        new SvgImageBackEnd()
    )
);

echo "&nbsp;";

echo wrapInDiv('Whats being encoded into the QR code: ' . $qrCodeUrl);

$qrcode_image = $writer->writeString(
    $qrCodeUrl
);

echo $qrcode_image;

echo "&nbsp;";
echo "&nbsp;";

echo "
<form> Input code here:
    <input name='code'></input>
    <input type='hidden' name='oldSecret' value='${secretKey}'>
    <input type='submit' value='Submit'>
</form>
";

$code = $_GET["code"] ?? null;
$oldSecret = $_GET["oldSecret"] ?? null;

if ($code) {
    $auth = $google2fa->verifyKey($oldSecret, $code);
    echo($auth ? "✅✅✅✅✅Success key matched ✅✅✅✅✅" : "Did not match");
}

function wrapInDiv(string $string)
{
    return '<div>' . $string . '</div>';
}
