<?php


namespace system;

use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\SignatureInvalidException;
use system\exceptions\PermissionException;

class Authentication extends JWT{
	private $key;
	private $issuer;
	private $audience;
	private const UMA_SEMANA_EM_SEGUNDOS = 604800;

	public function __construct(){
		global $ENV;
		$this->key      = $ENV['JWT']['jwt_key'];
		$this->issuer   = $ENV['JWT']['issued_by'];
		$this->audience = $ENV['JWT']['audience'];
	}

	/**
	 * @param $payload
	 *
	 * @return string
	 */
	public function generateJWT($payload):string{
		$defaultPayload = [
			"iss"=>$this->issuer, //issued by
			"aud"=>$this->audience, //audience
			"iat"=>time(), //issued at
			"exp"=>time() + (self::UMA_SEMANA_EM_SEGUNDOS) //expire at
		];
		return self::encode(array_merge((array)$payload, $defaultPayload), $this->key);
	}

	/**
	 * @param string $jwt
	 *
	 * @return object
	 * @throws PermissionException
	 */
	public function getPayload(string $jwt){
		try{
			return self::decode($jwt, $this->key, array('HS256'));
		}catch(SignatureInvalidException $t){
			throw new PermissionException("JWT Inválida", 0, $t);
		}catch(BeforeValidException $t){
			throw new PermissionException("JWT ainda não é valida", 0, $t);
		}catch(ExpiredException $t){
			throw new PermissionException("JWT expirada", 0, $t);
		}
	}
}