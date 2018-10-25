<?php

require_once __DIR__."/../app/vendor/autoload.php";

$dotenv = new \Dotenv\Dotenv(__DIR__.'/../');
$dotenv->safeLoad();

function getArrayValue (array $arr, $key)
{
    if (isset($arr[$key])) {
        return $arr[$key];
    }
    throw new \Exception ("Undefined {$key}");
}

function githubKeyIsOK ($githubSecret, $githubSignature, $body)
{
    $hmac = hash_hmac('sha1', $body, $githubSecret);
    $calcSignature = 'sha1='.$hmac;
    return $calcSignature === $githubSignature;
}

function runDeploy()
{
  $dir = __DIR__.'/../';
  return shell_exec("cd {$dir} && make deploy");
}

function logToFile($content, $file)
{
    $time = date('d-m-Y H:i:s');
    file_put_contents($file, "[{$time}]:".$content.PHP_EOL, FILE_APPEND);
}

$pathLogFile = __DIR__."/deploy.log";

try {

    $githubSecret = getArrayValue($_ENV, 'GITHUB_SECRET');
    $githubSignature = getArrayValue($_SERVER, 'HTTP_X_HUB_SIGNATURE');
    $body = file_get_contents('php://input');
    
    if (!githubKeyIsOK($githubSecret, $githubSignature, $body)) {
        throw new \Exception ("Github key is not valid");
    }

    $resDeploy = runDeploy();
    logToFile($resDeploy, $pathLogFile);

} catch (\Exception $e) {

    logToFile($e->getMessage(), $pathLogFile);

}
