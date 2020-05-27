<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

require_once __DIR__ . '/vendor/autoload.php';
$ENV = parse_ini_file(__DIR__ . "/env.ini", true);

set_exception_handler(function(Throwable $t){
	if(!is_subclass_of($t, "\system\\exceptions\AppExceptionAbstract")){
		$runtimeException = new \system\exceptions\RuntimeException("Erro na resposta do servidor", 0, $t);
		echo json_encode(["error"=>$runtimeException->getMessage()]);
	}else{
		echo json_encode(["error"=>$t->getMessage()]);
	}
});

set_error_handler(function($errno, $errstr, $errfile, $errline){
	$logger = new \system\Logger("index");
	$logger->error("Erro capturado pelo sistema", [
		"errno"=>$errno,
		"errstr"=>$errstr,
		"errfile"=>$errfile,
		"errline"=>$errline
	]);
});

$app = new \Slim\App();

$app->any('/test', function (Request $request, Response $response, $args) {
	return $response->withJson(['online'=>true], 200);
});

$app->run();



