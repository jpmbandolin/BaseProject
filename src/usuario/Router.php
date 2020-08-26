<?php


namespace usuario;


use Slim\App;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class Router{
	public static function routes(App $app = null){
		$usuario = new Usuario();

		$app->post('', function (Request $request, Response $response, $args) use ($usuario){
			$usuario->cadastrar((object)$request->getParsedBody());
			return $response->withStatus(202);
		});

		$app->post('/login', function (Request $request, Response $response, $args) use ($usuario){
			return $response->withJson(['token'=>$usuario->login((object)$request->getParsedBody())], 200);
		});
	}
}