
<?php 
require "vendor/autoload.php";
use \Firebase\JWT\JWT;

$secretkey = "example_keyssss";
$payload = array(
    "author" => "Sigit Prasetyo N",
    "authoruri" => "https://seegatesite.com",
    "exp" => time()+1000,
    );
try{
    $jwt = JWT::encode($payload, $secretkey);
    print_r($jwt);
}catch (UnexpectedValueException $e) {
    echo $e->getMessage();
}
print_r('<br/>');

try{
    $decoded = JWT::decode($jwt, $secretkey, array('HS256'));
    print_r($decoded);
}catch (UnexpectedValueException $e) {
    echo $e->getMessage();
}

?>
