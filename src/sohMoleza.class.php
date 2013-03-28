<?php
/**
 * Classe de manipulação geral da rotina de geração das classes.
 * O uso desta classe é intuitivo e segue a linha de raciocínio abaixo.
 *
 * Chamada inicial do script de geração de classes.
 *
 * 	sohMoleza::iniciar()
 *
 *
 * Dados de conexão com o banco de dados.
 *
 *	->setDadosBanco('banco', 'usuario', 'senha', 'ip ou host')
 *
 *
 * Informações acerca do(s) autor(es) da classe, podendo ser informado mais de um.
 * Nota: Este método é opcional
 *
 *	->setAutor('Autor 1', 'autor1@exemplo.com.br')
 *	->setAutor('Autor 2', 'autor2@exemplo.com.br')
 *  ->setAutor('Autor 3', 'autor3@exemplo.com.br')
 *
 *
 * Informações de copyright da classe, podendo ser informado mais de um.
 * Nota: Este método é opcional
 *
 *	->setCopyright('Copyright 1')
 *	->setCopyright('Copyright 2')
 *
 *
 * Informações de pacote da classe.
 * Nota: Este método é opcional
 *
 *	->setPackage('pacote')
 *
 *
 * Tabela(s) a ser(em) usada(s) para a geração da classe, podendo ser informada mais de uma.
 * Nota: Este método é opcional, porém, se não for informado nenhuma tabela, o sistema irá
 * presumir que será gerado classes de todas as tabelas do banco de dados informado no setDadosBanco().
 * Caso tabela informada não exista, o sistema simplesmente ignora ela e passa para a próxima.
 *
 *	->setTabelas('tabela1', 'tabela2', 'tabela3')
 *
 *
 * Determina a validação de dados no método mágico __set()
 * Nota: Este método é opcional.
 *
 *	->validarMetodoSet()
 *
 *
 * Determina se os métodos adicionar() e atualizar() serão privados, bem como a criação
 * do método salvar() que será responsável pela chamada destes métodos acima.
 * Nota: Este método é opcional.
 *
 *	->setMetodoSalvar()
 *
 * Determina a conversão de chaves estrangeiras "fk" para objetos.
 * Nota: Este método é opcional.
 *
 *	->converterFk()
 *
 * Determina a pasta aonde será criado os arquivos PHP com as classes geradas.
 * Nota: Este método é opcional, caso não informado, o sistema apenas irá exibir as classes na tela.
 *
 *	->setPastaDestino('/caminho/da/pasta/')
 *
 *
 * Método final responsável por iniciar o processo de geração das classes.
 *
 *	->gerarClasses()
 *
 * Exemplo de utilização:
 *
 *	sohMoleza::iniciar()
 *		->setDadosBanco('coiote', 'root', '')
 *		->setAutor('Joubert Guimarães de Assis', 'joubert@redrat.com.br')
 *		->setAutor('Durangos Protozoários', 'durangos@redrat.com.br')
 *		->setCopyright('RedRat Consultoria')
 *		->setCopyright('Casa do jão')
 *		->setPackage('Maximus')
 *		->setTabelas('usuarios')
 *		->validarMetodoSet()
 *		->setMetodoSalvar()
 *		->converterFk()
 *		->setPastaDestino(__DIR__ . "/output/")
 *		->gerarClasses();
 *
 * @author Joubert Guimarães de Assis <joubert@redrat.com.br>
 * @package sohMoleza
 * @subpackage Classes
 * @copyright Copyright (c) 2012, RedRat Consultoria
 * @version 1.0
 */

class sohMoleza
{
	/**
	 * Localização do banco de dados.
	 *
	 * @var string.
	 */
	private $host;

	/**
	 * Usuário para acesso ao banco de dados.
	 *
	 * @var string.
	 */
	private $usuario;

	/**
	 * Senha do usuário para acesso ao banco de dados.
	 *
	 * @var string.
	 */
	private $senha;

	/**
	 * Nome do banco de dados.
	 *
	 * @var string.
	 */
	private $banco;

	/**
	 * Listagem das tabelas para o gerador de classes.
	 *
	 * @var array.
	 */
	private $tabelas;

	/**
	 * Lista com os autores da classe.
	 *
	 * @var array
	 */
	public $autor;

	/**
	 * Pacote ao qual a classe vai pertencer.
	 *
	 * @var string
	 */
	public $package;

	/**
	 * Atribuição de copyright à classe.
	 *
	 * @var string
	 */
	public $copyright;

	/**
	 * Caminho aonde será armazenados os arquivos PHP com as classes geradas.
	 *
	 * @var string
	 */
	private $pasta_destino = null;

	/**
	 * Delimitador de conversão de chave estrangeira para objeto.
	 *
	 * @var boolean
	 */
	private $converter_fk = false;

	/**
	 * Delimitador de validação para o método mágico __set().
	 *
	 * @var boolean
	 */	
	private $validar_set = false;

	/**
	 * Delimitador para os métodos adicionar() e atualizar()
	 * passar a ser privados e os mesmos serem chamados
	 * pelo método salvar().
	 *
	 * @var boolean
	 */	
	private $insert_update = false;

	/**
	 * Método construtor e só.
	 *
	 * @return void
	 */
	private function __construct()
	{
		// Nada a fazer.
	}

	/**
	 * Gera uma instância inicial do gerador de classe.
	 *
	 * @return sohMoleza Retorna o próprio objeto.
	 */
	public static function iniciar()
	{
		return new self();
	}

	/**
	 * Determina as configurações de acesso ao banco de dados.
	 *
	 * @return sohMoleza Retorna o próprio objeto.
	 */
	public function setDadosBanco($banco, $usuario, $senha, $host = 'localhost')
	{
		$this->banco = $banco;
		$this->usuario = $usuario;
		$this->senha = $senha;
		$this->host = $host;
		return $this;
	}

	/**
	 * Permite selecionar tabelas específicas para a geração das classes.
	 *
	 * @return sohMoleza Retorna o próprio objeto.
	 * @see sohMoleza
	 */
	public function setTabelas()
	{
		for($i = 0; $i < func_num_args(); $i++)
			$this->tabelas[] = func_get_arg($i);
		return $this;
	}


	/**
	 * Determina a pasta de destino para geração da classe.
	 *
	 * @param string $pasta_destino Pasta aonde será salvo os arquivos PHP gerados.
	 * @return sohMoleza Retorna o próprio objeto.
	 */
	public function setPastaDestino($pasta_destino)
	{
		$this->pasta_destino = $pasta_destino;
		return $this;
	}

	/**
	 * Adiciona um autor à documentação da classe.
	 *
	 * @param string $nome Nome do autor.
	 * @param string $email E-mail do autor.
	 * @return sohMoleza Retorna o próprio objeto.
	 */
	public function setAutor($nome, $email)
	{
		$this->autor[$nome] = $email;
		return $this;
	}

	/**
	 * Adiciona um copyright à documentação da classe.
	 *
	 * @param string $copyright Copyright a ser vinculado as classes.
	 * @return sohMoleza Retorna o próprio objeto.
	 */
	public function setCopyright($copyright)
	{
		$this->copyright[] = $copyright;
		return $this;
	}

	/**
	 * Adiciona um package à documentação da classe.
	 *
	 * @param string $package Package a ser vinculado as classes.
	 * @return sohMoleza Retorna o próprio objeto.
	 */
	public function setPackage($package)
	{
		$this->package = $package;
		return $this;
	}

	/**
	 * Permite a conversão de chaves estrangeiras para objetos.
	 *
	 * @return iMoleza Retorna o próprio objeto.
	 */
	public function converterFk()
	{
		$this->converter_fk = true;
		return $this;
	}

	/**
	 * Permite definição de validação no método __set().
	 *
	 * @return iMoleza Retorna o próprio objeto.
	 */
	public function validarMetodoSet()
	{
		$this->validar_set = true;
		return $this;
	}

	/**
	 * Determina os métodos adicionar() e atualizar() como privados
	 * e cria o método salvar() que será responsável por chamar os 
	 * métodos citados acima.
	 *
	 * @return iMoleza Retorna o próprio objeto.
	 */
	public function setMetodoSalvar()
	{
		$this->insert_update = true;
		return $this;
	}

	/**
	 * Realiza os procedimentos iniciais para a geração da classe.
	 *
	 * @return void
	 */
	public function gerarClasses()
	{
		if(!$this->host || !$this->usuario || !$this->banco)
			exit('Você precisa informar os dados para conexão com o banco de dados, use o método setDadosBanco()');
		if(!$this->pasta_destino)
			header('Content-Type: text/html; charset=utf-8');
		mysql_connect($this->host, $this->usuario, $this->senha);
		mysql_select_db($this->banco);
		if(!$this->tabelas)
		{
			$query = "SHOW TABLES";
			$consulta = mysql_query($query);
			while($dados = mysql_fetch_assoc($consulta))
				$this->tabelas[] = $dados["Tables_in_" . $this->banco];
		}
		require_once(__DIR__ . '/sohMolezaTabela.class.php');
		foreach($this->tabelas as $tabela)
		{
			$query = "SHOW TABLES WHERE Tables_in_" . $this->banco . " = '" . $tabela . "'";
			$consulta = mysql_query($query);
			if(mysql_num_rows($consulta) == 1)
			{
				$NovaClasse = new sohMolezaTabela($tabela);
				if($this->autor)
					$NovaClasse->setAutor($this->autor);
				if($this->package)
					$NovaClasse->setPackage($this->package);
				if($this->copyright)
					$NovaClasse->setCopyright($this->copyright);
				if($this->converter_fk)
					$NovaClasse->converterFk();
				if($this->validar_set)
					$NovaClasse->validarMetodoSet();
				if($this->insert_update)
					$NovaClasse->setMetodoSalvar();				
				$NovaClasse->saida($this->pasta_destino);
			}
		}
	}
}