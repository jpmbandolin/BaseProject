<?php


namespace usuario;


use ControllerAbstract;
use system\database\DatabaseConnection;
use system\exceptions\DatabaseException;

class Usuario extends ControllerAbstract{
	/**
	 * @var DatabaseConnection
	 */
	protected $con;
	/**
	 * @var int Código único do usuário
	 */
	private $iCodUsuario;
	/**
	 * @var string Nome do usuário
	 */
	private $cNome;

	/**
	 * Usuario constructor.
	 *
	 * @param int|null $iCodUsuario
	 *
	 * @throws DatabaseException
	 */
	public function __construct(?int $iCodUsuario = null){
		parent::__construct();
		if($iCodUsuario !== null){
			$this->getUserData($iCodUsuario);
		}
	}

	/**
	 * @param int $iCodUsuario
	 *
	 * @throws DatabaseException
	 */
	private function getUserData(int $iCodUsuario){
		$sql = "SELECT iCodUsuario, cNome FROM usuario WHERE iCodUsuario = ?";

		try{
			$userObj = $this->con->fetchObject($sql, [$iCodUsuario], 'usuario\\Usuario');
		}catch(\Throwable $t){
			throw new DatabaseException("Erro ao buscar dados do usuário", 0, $t);
		}

		$this->iCodUsuario  = $userObj->iCodUsuario;
		$this->cNome        = $userObj->cNome;
	}
}