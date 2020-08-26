<?php


namespace usuario;


use ControllerAbstract;
use system\Authentication;
use system\exceptions\{BadRequestException, DatabaseException, PermissionException};

class Usuario extends ControllerAbstract{

	/**
	 * @param $params
	 *
	 * @return string
	 * @throws DatabaseException
	 * @throws PermissionException
	 */
	public function login($params):string{
		$sql = "SELECT iCodUsuario, cNome FROM usuario WHERE (cEmail = ? OR cLogin = ?) AND cSenha = MD5(?) AND cAtivo = 'S' AND cExcluido = 'N'";

		try{
			$userData = $this->con->fetchObject($sql, [$params->clogin, $params->cLogin, $params->cSenha]);
		}catch(\Throwable $t){
			throw new DatabaseException("Erro ao buscar os dados de usuário.", 0, $t);
		}

		if(!isset($userData->iCodUsuario)){
			throw new PermissionException("Login ou senha incorretos");
		}

		return (new Authentication)->generateJWT($userData);
	}

	/**
	 * Salva um novo usuário
	 * @param $params
	 *
	 * @throws BadRequestException
	 * @throws DatabaseException
	 */
	public function cadastrar($params){
		$this->validarDadosCadastrais($params);

		if($params->cSenha != $params->cConfirmaSenha){
			throw new BadRequestException('Os campos de senha e confirmação de senhas não estão iguais.');
		}

		$sql = "INSERT INTO usuario (cNome, cTipoUsuario, cLogin, cEmail, cSenha) VALUES (?, ?, ?, ?, MD5(?))";

		try{
			$this->con->prepareAndExecute($sql, [
				$params->cNome,     $params->cTipoUsuario, $params->cLogin,
				$params->cEmail,    $params->cSenha
			]);
		}catch(\Throwable $t){
			throw new DatabaseException("Erro ao salvar os dados do usuário.");
		}
	}

	public function alterar(){

	}

	public function logout(){

	}

	/**
	 * Valida se todos os campos necessários para um cadastro foram preenchidos
	 * @param $params
	 *
	 * @throws BadRequestException
	 */
	private function validarDadosCadastrais($params){
		$camposObrigatorios = ['cNome'=>'Nome', 'cEmail'=>'Email', 'cLogin'=>'Login', 'cSenha'=>'Senha', 'cTipoUsuario'=>'Tipo de Usuario'];
		$camposInvalidos = [];
		foreach($camposObrigatorios as $key=>$campo){
			if(!isset($params->{$key})){
				$camposInvalidos[] = $campo;
			}
		}

		if(count($camposInvalidos)){
			throw new BadRequestException("Campos inválidos: " . implode(', ', $camposInvalidos));
		}
	}
}