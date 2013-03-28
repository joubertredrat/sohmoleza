<?php
/**
 * Classe de manipulação dos dados da tabela 
 * para a geração da classe correspondente.
 *
 * @author Joubert Guimarães de Assis <joubert@redrat.com.br>
 * @package sohMoleza
 * @subpackage Classes
 * @copyright Copyright (c) 2012, RedRat Consultoria
 * @version 1.0
 */
class sohMolezaTabela
{
	/**
	 * Nome da tabela que será gerado a classe.
	 *
	 * @var string
	 */
	private $tabela;

	/**
	 * Nome da classe obtido a partir do nome da tabela.
	 *
	 * @var string
	 * @see iMoleza::criarNomeClasse()
	 */
	private $nome_classe;

	/**
	 * Lista com as colunas da tabela.
	 *
	 * @var array
	 */
	private $colunas;

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
	 * passar a ser privados e os mesmos serem chamados pelo
	 * método salvar().
	 *
	 * @var boolean
	 */	
	private $insert_update = false;

	/**
	 * Lista com os autores da classe.
	 *
	 * @var array
	 */
	private $autor;

	/**
	 * Pacote ao qual a classe vai pertencer.
	 *
	 * @var string
	 */
	private $package = null;

	/**
	 * Atribuição de copyright à classe.
	 *
	 * @var string
	 */
	private $copyright = null;

	/**
	 * Nome do gerador de classes.
	 */
	const SOHMOLEZA_NOME = 'Soh Moleza';

	/**
	 * Revisão do gerador de classes.
	 */
	const SOHMOLEZA_VERSAO = '$LastChangedRevision:$';

	/**
	 * E-mail atribuído ao gerador de classes.
	 */
	const SOHMOLELA_EMAIL = 'sohmoleza@redrat.com.br';

	/**
	 * Identificador de chaves primárias.
	 */
	const SOHMOLEZA_PK = 'id_';

	/**
	 * Identificador de chaves estrangeiras.
	 */
	const SOHMOLEZA_FK = 'fk_';

	/**
	 * Delimitador de colunas para chaves primárias da tabela.
	 */
	const LISTA_PK = 'pk';

	/**
	 * Delimitador de colunas para chaves estrangeiras da tabela.
	 */	
	const LISTA_FK = 'fk';

	/**
	 * Delimitador para outras colunas da tabela.
	 */
	const LISTA_OUTROS = 'outros';

	/**
	 * Coluna de nome do campo da tabela.
	 */
	const TABELA_COLUNA_NOME = 'Field';

	/**
	 * Coluna de tipo do campo da tabela.
	 */
	const TABELA_COLUNA_TIPO = 'Type';

	/**
	 * Coluna de dado nulo do campo da tabela.
	 */
	const TABELA_COLUNA_NULO = 'Null';

	/**
	 * Coluna de tipo de índice do campo da tabela.
	 */
	const TABELA_COLUNA_INDICE = 'Key';

	/**
	 * Coluna de comentário do campo da tabela.
	 */
	const TABELA_COLUNA_COMENTARIO = 'Comment';

	/**
	 * Recebe o nome da tabela e realiza o processamento inicial dos dados.
	 *
	 * @param string $tabela nome da tabela que será gerado a classe.
	 * @return void
	 */
	public function __construct($tabela)
	{
		$this->tabela = $tabela;
		$this->criarNomeClasse();
		$this->getAtributos();
	}

	/**
	 * Cria o nome da classe baseado no nome da tabela.
	 *
	 * @return void
	 */
	private function criarNomeClasse()
	{
		$this->nome_classe = str_replace(' ', '', ucwords(str_replace('_', ' ', $this->tabela)));
	}

	/**
	 * Adiciona um autor à documentação da classe.
	 *
	 * @param array $autores Lista com os autores.
	 * @return void
	 */
	public function setAutor($autores)
	{
		$this->autor = $autores;
	}

	/**
	 * Adiciona um pacote à documentação da classe.
	 *
	 * @param string $package Nome do pacote.
	 * @return void
	 */
	public function setPackage($package)
	{
		$this->package = $package;
	}

	/**
	 * Adiciona um copyright à documentação da classe.
	 *
	 * @param string $copyright Nome do copyright.
	 * @return void
	 */
	public function setCopyright($copyright)
	{
		$this->copyright = $copyright;
	}	

	/**
	 * Permite a conversão de chaves estrangeiras para objetos.
	 *
	 * @return iMoleza Retorna o próprio objeto.
	 */
	public function converterFk()
	{
		$this->converter_fk = true;
	}

	/**
	 * Permite definição de validação no método __set().
	 *
	 * @return iMoleza Retorna o próprio objeto.
	 */
	public function validarMetodoSet()
	{
		$this->validar_set = true;
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
	}	

	/**
	 * Cria o cabecalho da classe composta pela documentação da mesma e 
	 * os dados gerais segundo o padrão PHPDOC.
	 *
	 * @return string Retorna o cabelhado devidamente padronizado.
	 */
	private function getDocumentacaoCabecalho()
	{
		$consulta = mysql_query('show table status where Name = "' . $this->tabela . '"');
		$documentacao = mysql_result($consulta, 0, self::TABELA_COLUNA_COMENTARIO) ? mysql_result($consulta, 0, self::TABELA_COLUNA_COMENTARIO) : 'Classe de manipulação da tabela ' . $this->tabela . '.';
		if(!mb_check_encoding($documentacao, 'UTF-8'))
			$documentacao = utf8_encode($documentacao);
		$retorno[] = "/**";
		$retorno[] = " * " . wordwrap($documentacao, 100, "\n * ") . (substr($documentacao, strlen($documentacao) - 1) != '.' ? '.' : '');
		$retorno[] = " *";
		if($this->autor)
		{
			foreach($this->autor as $nome => $email)
				$retorno[] = " * @author " . $nome . " <" . $email . ">";
		}
		$retorno[] = " * @author " . self::SOHMOLEZA_NOME . ", revisão " . self::SOHMOLEZA_VERSAO . " <" . self::SOHMOLELA_EMAIL . ">";
		if($this->package)
		{
			$retorno[] = " * @package " . $this->package;
			$retorno[] = " * @subpackage Classes";
		}
		else
			$retorno[] = " * @package Classes";
		if($this->copyright)
		{
			foreach($this->copyright as $copyright)
				$retorno[] = " * @copyright Copyright (c) " . date('Y') . ", " . $copyright;
		}
		$retorno[] = " */";
		return implode("\n", $retorno);
	}

	/**
	 * Varre a tabela e recupera os atributos a ser usados na classe.
	 *
	 * @return void
	 */
	private function getAtributos()
	{
		$consulta = mysql_query('SHOW FULL COLUMNS FROM ' . $this->tabela);
		while($dados = mysql_fetch_assoc($consulta))
		{
			switch($dados[self::TABELA_COLUNA_INDICE])
			{
				case 'PRI':
					$this->colunas[self::LISTA_PK][$dados[self::TABELA_COLUNA_NOME]] = $dados;
				break;
				case 'MUL':
				case 'UNI':
					$this->colunas[self::LISTA_FK][$dados[self::TABELA_COLUNA_NOME]] = $dados;
				break;
				default:
					$this->colunas[self::LISTA_OUTROS][$dados[self::TABELA_COLUNA_NOME]] = $dados;
				break;
			}
		}
	}

	/**
	 * Converte o nome de chave estrangeira para nome de Objeto.
	 *
	 * @return string Retorna o nome convertido para nome de Objeto.
	 */
	private function converteFkParaObj($fk, $exibir_pk = false)
	{
		return ucwords(str_replace(self::SOHMOLEZA_FK, '', $fk)) . ($exibir_pk ? '->' . str_replace(self::SOHMOLEZA_FK, self::SOHMOLEZA_PK, $fk) : '');
	}

	/**
	 * Prove o tipo de variável de acordo com o tipo de dado informado.
	 *
	 * @return string Retorna o tipo da variável.
	 */
	private function getTipoVar($tipo)
	{
		switch(preg_replace("/[^a-zA-Z\s]/", "", $tipo))
		{
			case 'char':
			case 'varchar':
			case 'text':
			case 'tinytext':
			case 'mediumtext':
			case 'longtext':
				$retorno = 'string';
			break;
			case 'int':
			case 'smallint':
			case 'bigint':
				$retorno = 'integer';
			break;
			case 'decimal':
			case 'float':
			case 'double':
			case 'real':
				$retorno = 'float';
			break;
			case 'tinyint':
				$retorno = 'boolean';
			break;
			case 'timestamp':
			case 'datetime':
			case 'date':
			case 'time':
			case 'year':
				$retorno = 'datetime';
			break;
			default:
				$retorno = 'desconhecido';
			break;
		}
		return $retorno;
	}

	/**
	 * Prove os validadores de acordo com o tipo de variável informado.
	 *
	 * @return string Retorna o código-fonte de validação do tipo de variável.
	 */
	private function getValidateSet($tipo, $objeto = null)
	{
		switch(preg_replace("/[^a-zA-Z\s]/", "", $tipo))
		{
			case 'char':
			case 'varchar':
			case 'text':
			case 'tinytext':
			case 'mediumtext':
			case 'longtext':
				$retorno = "is_string(\$atributo)";
			break;
			case 'int':
			case 'smallint':
			case 'bigint':
			case 'timestamp':
				$retorno = "is_int(\$atributo)";
			break;
			case 'decimal':
			case 'float':
			case 'double':
			case 'real':
				$retorno = "is_float(\$atributo)";
			break;
			case 'tinyint':
				$retorno = "is_bool(\$atributo)";
			break;
			case 'datetime':
				$retorno = "strtotime(\$atributo)";
			break;
			case 'date':
				$retorno = "strtotime(\$atributo . ' 00:00:00')";
			break;
			case 'time':
				$retorno = "strtotime(date('Y-m-d') . ' ' . \$atributo)";
			break;
			case 'year':
				$retorno = "strtotime(\$atributo . '-' . date('m-d') . ' 00:00:00')";
			break;
			case 'obj':
				$retorno = "\$atributo intanceof " . $objeto;
			break;
		}
		return $retorno;
	}	

	/**
	 * Prove os validadores de acordo com o tipo de variável informado.
	 *
	 * @return string Retorna o código-fonte de validação do tipo de variável.
	 */
	private function getTratamentoBanco($tipo, $atributo)
	{
		switch(preg_replace("/[^a-zA-Z\s]/", "", $tipo))
		{
			case 'char':
			case 'varchar':
			case 'text':
			case 'tinytext':
			case 'mediumtext':
			case 'longtext':
			case 'datetime':
			case 'date':
			case 'time':
			case 'year':
				$retorno = '"\'" . mysql_escape_string($this->' . $atributo . ') . "\'"';
			break;
			case 'int':
			case 'smallint':
			case 'bigint':
			case 'timestamp':
			case 'decimal':
			case 'float':
			case 'double':
			case 'real':
				$retorno = '$this->' . $atributo;
			break;
			case 'tinyint':
				$retorno = '$this->' . $atributo . ' ? "TRUE" : "FALSE"';
			break;
		}
		return $retorno;
	}	


	/**
	 * Montra o código-fonte da classe com a declaração dos atributos, bem como
	 * a documentação de cada um destes.
	 *
	 * @return string Retorna código-fonte da declaração dos atributos.
	 */
	private function getDeclaracaoAtributos()
	{
		foreach($this->colunas[self::LISTA_PK] as $coluna => $dados)
		{
			$colunas_pk = array();
			$colunas_pk[] = "/**";
			$documentacao = $dados[self::TABELA_COLUNA_COMENTARIO] ? $dados[self::TABELA_COLUNA_COMENTARIO] : 'Atributo interno da classe ' . $this->nome_classe . '.';
			if(!mb_check_encoding($documentacao, 'UTF-8'))
				$documentacao = utf8_encode($documentacao);
			$colunas_pk[] = " * " . wordwrap($documentacao, 80, "\n	 * ") . (substr($documentacao, strlen($documentacao) - 1) != "." ? "." : "");
			$colunas_pk[] = " *";
			$colunas_pk[] = " * @var " . $this->getTipoVar($dados[self::TABELA_COLUNA_TIPO]);
			$colunas_pk[] = " */";
			$colunas_pk[] = "private $" . $dados[self::TABELA_COLUNA_NOME] . ";";
			$retorno[] = "	" . implode("\n	", $colunas_pk);
		}
		if(isset($this->colunas[self::LISTA_FK]))
		{
			foreach($this->colunas[self::LISTA_FK] as $coluna => $dados)
			{
				$colunas_fk = array();
				$colunas_fk[] = "/**";
				$documentacao = $dados[self::TABELA_COLUNA_COMENTARIO] ? $dados[self::TABELA_COLUNA_COMENTARIO] : 'Atributo interno da classe ' . $this->nome_classe . '.';
				if(!mb_check_encoding($documentacao, 'UTF-8'))
					$documentacao = utf8_encode($documentacao);
				$colunas_fk[] = " * " . wordwrap($documentacao, 80, "\n	 * ") . (substr($documentacao, strlen($documentacao) - 1) != '.' ? '.' : '');
				$colunas_fk[] = " *";
				if($this->converter_fk)
					$colunas_fk[] = " * @var " . $this->converteFkParaObj($dados[self::TABELA_COLUNA_NOME]);
				else
					$colunas_fk[] = " * @var " . $this->getTipoVar($dados[self::TABELA_COLUNA_TIPO]);
				$colunas_fk[] = " */";
				if($this->converter_fk)
					$colunas_fk[] = "private $" . $this->converteFkParaObj($dados[self::TABELA_COLUNA_NOME]) . ";";
				else
					$colunas_fk[] = "private $" . $dados[self::TABELA_COLUNA_NOME] . ";";
				$retorno[] = "	" . implode("\n	", $colunas_fk);
			}
		}
		foreach($this->colunas[self::LISTA_OUTROS] as $coluna => $dados)
		{
			$colunas_outros = array();
			$colunas_outros[] = "/**";
			$documentacao = $dados[self::TABELA_COLUNA_COMENTARIO] ? $dados[self::TABELA_COLUNA_COMENTARIO] : 'Atributo interno da classe ' . $this->nome_classe . '.';
			if(!mb_check_encoding($documentacao, 'UTF-8'))
				$documentacao = utf8_encode($documentacao);
			$colunas_outros[] = " * " . wordwrap($documentacao, 80, "\n	 * ") . (substr($documentacao, strlen($documentacao) - 1) != "." ? "." : "");
			$colunas_outros[] = " *";
			$colunas_outros[] = " * @var " . $this->getTipoVar($dados[self::TABELA_COLUNA_TIPO]);
			$colunas_outros[] = " */";
			$colunas_outros[] = "private $" . $dados[self::TABELA_COLUNA_NOME] . ";";
			$retorno[] = "	" . implode("\n	", $colunas_outros);
		}

		$coluna_final[] = "/**";
		$documentacao = "Delimita se durante a instância do objeto houve alguma alteração em seus atributos, este recurso é utilizado para evitar execução de insert e update desnecessários.";
		if(!mb_check_encoding($documentacao, 'UTF-8'))
			$documentacao = utf8_encode($documentacao);
		$coluna_final[] = " * " . wordwrap($documentacao, 80, "\n	 * ");
		$coluna_final[] = " *";
		$coluna_final[] = " * @var " . $this->getTipoVar('tinyint');
		$coluna_final[] = " */";
		$coluna_final[] = "private \$objeto_alterado = false;";
		$retorno[] = "	" . implode("\n	", $coluna_final);

		return implode("\n\n", $retorno);
	}

	/**
	 * Monta o código-fonte do método construtor do objeto de acordo com as colunas da tabela.
	 *
	 * @return string Retorna o código-fonte do método __constuct().
	 */
	private function getMetodoConstrutor()
	{
		$pk = array_shift($this->colunas[self::LISTA_PK]);
		$this->colunas[self::LISTA_PK][$pk[self::TABELA_COLUNA_NOME]] = $pk;
		$detalhes_metodo = 'Método constutor da classe responsável por popular o objeto de acordo com a chave identificadora do registro informado no parametro ou a criação de um objeto vazio.';
		$documentacao = $pk[self::TABELA_COLUNA_COMENTARIO] ? $pk[self::TABELA_COLUNA_COMENTARIO] : 'Chave identificadora do registro no banco de dados.';
		if(!mb_check_encoding($documentacao, 'UTF-8'))
			$documentacao = utf8_encode($documentacao);
		$retorno[] = "/**";
		$retorno[] = " * " . wordwrap($detalhes_metodo, 80, "\n	 * ");
		$retorno[] = " *";
		$retorno[] = " * @param " . $this->getTipoVar($pk[self::TABELA_COLUNA_TIPO]) . " " . $pk[self::TABELA_COLUNA_NOME] . " " . $documentacao . (substr($documentacao, strlen($documentacao) - 1) != "." ? "." : "");
		$retorno[] = " * @return void";
		$retorno[] = " */";
		$retorno[] = "public function __construct($" . $pk[self::TABELA_COLUNA_NOME] . " = null)";
		$retorno[] = "{";
		$retorno[] = "	if($" . $pk[self::TABELA_COLUNA_NOME] . " !== null)";
		$retorno[] = "	{";
		$retorno[] = "		\$query = 'SELECT * FROM " . $this->tabela . " WHERE " . $pk[self::TABELA_COLUNA_NOME] . " = ' . $" . $pk[self::TABELA_COLUNA_NOME] . ";";
		$retorno[] = "		\$consulta = mysql_query(\$query);";
		$retorno[] = "		if(mysql_error())";
		$retorno[] = "			throw new Exception('Ocorreu um erro durante a consulta na tabela \"" . $this->tabela . "\", query: \"' . \$query . '\".');";
		$retorno[] = "		if(mysql_num_rows(\$consulta) != 1)";
		$retorno[] = "			throw new Exception('Ocorreu um erro durante a consulta na tabela \"" . $this->tabela . "\", a chave identificadora é inválida: \"' . \$" . $pk[self::TABELA_COLUNA_NOME] . " . '\".');";
		$retorno[] = "		\$this->" . $pk[self::TABELA_COLUNA_NOME] . " = $" . $pk[self::TABELA_COLUNA_NOME] . ";";
		if(isset($this->colunas[self::LISTA_FK]))
		{
			foreach($this->colunas[self::LISTA_FK] as $coluna => $dados)
			{
				if($this->converter_fk)
					$retorno[] = "		\$this->" . $this->converteFkParaObj($dados[self::TABELA_COLUNA_NOME]) . " = new " . $this->converteFkParaObj($dados[self::TABELA_COLUNA_NOME]) . "(mysql_result(\$consulta, 0, '" . $dados[self::TABELA_COLUNA_NOME] . "'));";
				else
					$retorno[] = "		\$this->" . $dados[self::TABELA_COLUNA_NOME] . " = mysql_result(\$consulta, 0, '" . $dados[self::TABELA_COLUNA_NOME] . "');";
			}
		}
		foreach($this->colunas[self::LISTA_OUTROS] as $coluna => $dados)
			$retorno[] = "		\$this->" . $dados[self::TABELA_COLUNA_NOME] . " = mysql_result(\$consulta, 0, '" . $dados[self::TABELA_COLUNA_NOME] . "');";
		$retorno[] = "	}";
		$retorno[] = "}";
		return "	" . implode("\n	", $retorno);
	}

	/**
	 * Monta o código-fonte do método mágico set do objeto de acordo com as colunas da tabela.
	 *
	 * @return string Retorna o código-fonte do método __set().
	 */
	private function getMetodoSet()
	{
		$documentacao = "Atribui o dado ao objeto de acordo com o atributo informado ou dispara exceção caso atributo não exista.";
		if(!mb_check_encoding($documentacao, 'UTF-8'))
			$documentacao = utf8_encode($documentacao);
		$retorno[] = "/**";
		$retorno[] = " * " . wordwrap($documentacao, 80, "\n	 * ");
		$retorno[] = " *";
		$retorno[] = " * @param string \$atributo Nome do atributo que irá receber o dado.";
		$retorno[] = " * @param mixed \$valor Dado a ser atribuido ao atributo.";
		$retorno[] = " * @return void";
		$retorno[] = " */";
		$retorno[] = "public function __set(\$atributo, \$valor)";
		$retorno[] = "{";
		$retorno[] = "	switch(\$atributo)";
		$retorno[] = "	{";
		if($this->validar_set)
		{
			foreach($this->colunas[self::LISTA_FK] as $coluna => $dados)
			{
				if($this->converter_fk)
				{
					$retorno[] = "		case '" . $this->converteFkParaObj($dados[self::TABELA_COLUNA_NOME]) . "':";
					$retorno[] = "			if(!" . $this->getValidateSet('obj', $this->converteFkParaObj($dados[self::TABELA_COLUNA_NOME])) . ")";
					$retorno[] = "				throw new Exception('O atributo ' . \$atributo . ' deve receber um objeto " . $this->converteFkParaObj($dados[self::TABELA_COLUNA_NOME]) . ", mas foi informado um valor inválido ' . \$valor . ' do tipo ' . gettype(\$valor));";
					$retorno[] = "			\$this->\$atributo = \$valor;";
					$retorno[] = "		break;";
				}
				else
				{
					$retorno[] = "		case '" . $dados[self::TABELA_COLUNA_NOME] . "':";
					$retorno[] = "			if(!" . $this->getValidateSet($dados[self::TABELA_COLUNA_TIPO]) . ")";
					$retorno[] = "				throw new Exception('O atributo ' . \$atributo . ' deve receber um " . $this->getTipoVar($dados[self::TABELA_COLUNA_TIPO]) . ", mas foi informado um valor inválido ' . \$valor . ' do tipo ' . gettype(\$valor));";
					$retorno[] = "			\$this->\$atributo = \$valor;";
					$retorno[] = "		break;";
				}
			}
			foreach($this->colunas[self::LISTA_OUTROS] as $coluna => $dados)
			{
				$retorno[] = "		case '" . $dados[self::TABELA_COLUNA_NOME] . "':";
				$retorno[] = "			if(!" . $this->getValidateSet($dados[self::TABELA_COLUNA_TIPO]) . ")";
				$retorno[] = "				throw new Exception('O atributo ' . \$atributo . ' deve receber um " . $this->getTipoVar($dados[self::TABELA_COLUNA_TIPO]) . ", mas foi informado um valor inválido ' . \$valor . ' do tipo ' . gettype(\$valor));";
				$retorno[] = "			\$this->\$atributo = \$valor;";
				$retorno[] = "		break;";
			}
		}
		else
		{
			if(isset($this->colunas[self::LISTA_FK]))
			{
				foreach($this->colunas[self::LISTA_FK] as $coluna => $dados)
				{
					if($this->converter_fk)
						$retorno[] = "		case '" . $this->converteFkParaObj($dados[self::TABELA_COLUNA_NOME]) . "':";
					else
						$retorno[] = "		case '" . $dados[self::TABELA_COLUNA_NOME] . "':";
				}
			}
			foreach($this->colunas[self::LISTA_OUTROS] as $coluna => $dados)
				$retorno[] = "		case '" . $dados[self::TABELA_COLUNA_NOME] . "':";
			$retorno[] = "			\$this->\$atributo = \$valor;";
			$retorno[] = "		break;";
		}
		$retorno[] = "		default:";
		$retorno[] = "			throw new Exception('Atributo ' . \$atributo . ' desconhecido ou inválido da classe ' . __CLASS__);";
		$retorno[] = "		break;";
		$retorno[] = "	}";
		$retorno[] = "	\$this->objeto_alterado = true;";
		$retorno[] = "}";
		return "	" . implode("\n	", $retorno);
	}

	/**
	 * Monta o código-fonte do método mágico get do objeto de acordo com as colunas da tabela.
	 *
	 * @return string Retorna o código-fonte do método __get().
	 */
	private function getMetodoGet()
	{
		$documentacao = "Informa o dado do atributo solicitado ou dispara exceção caso atributo não exista.";
		if(!mb_check_encoding($documentacao, 'UTF-8'))
			$documentacao = utf8_encode($documentacao);
		$retorno[] = "/**";
		$retorno[] = " * " . wordwrap($documentacao, 80, "\n	 * ");
		$retorno[] = " *";
		$retorno[] = " * @param string \$atributo Nome do atributo que deseja obter seu respectivo dado.";
		$retorno[] = " * @return mixed Valor do atributo no seu tipo original.";
		$retorno[] = " */";
		$retorno[] = "public function __get(\$atributo)";
		$retorno[] = "{";
		$retorno[] = "	switch(\$atributo)";
		$retorno[] = "	{";
		foreach($this->colunas[self::LISTA_PK] as $coluna => $dados)
			$retorno[] = "		case '" . $dados[self::TABELA_COLUNA_NOME] . "':";
		if(isset($this->colunas[self::LISTA_FK]))
		{
			foreach($this->colunas[self::LISTA_FK] as $coluna => $dados)
			{
				if($this->converter_fk)
					$retorno[] = "		case '" . $this->converteFkParaObj($dados[self::TABELA_COLUNA_NOME]) . "':";
				else
					$retorno[] = "		case '" . $dados[self::TABELA_COLUNA_NOME] . "':";
			}
		}
		foreach($this->colunas[self::LISTA_OUTROS] as $coluna => $dados)
			$retorno[] = "		case '" . $dados[self::TABELA_COLUNA_NOME] . "':";
		$retorno[] = "			return \$this->\$atributo;";
		$retorno[] = "		break;";
		$retorno[] = "		default:";
		$retorno[] = "			throw new Exception('Atributo ' . \$atributo . ' desconhecido ou inválido da classe ' . __CLASS__);";
		$retorno[] = "		break;";
		$retorno[] = "	}";
		$retorno[] = "}";
		return "	" . implode("\n	", $retorno);
	}

	/**
	 * Monta o código-fonte do método de adicionar do objeto de acordo com as colunas da tabela.
	 *
	 * @return string Retorna o código-fonte do método adicionar().
	 */
	private function getMetodoAdicionar()
	{
		$pk = array_shift($this->colunas[self::LISTA_PK]);
		$this->colunas[self::LISTA_PK][$pk[self::TABELA_COLUNA_NOME]] = $pk;		
		$documentacao = "Adiciona os dados do objeto no banco de dados e a geração de sua chave identificadora.";
		if(!mb_check_encoding($documentacao, 'UTF-8'))
			$documentacao = utf8_encode($documentacao);
		$retorno[] = "/**";
		$retorno[] = " * " . wordwrap($documentacao, 80, "\n	 * ");
		$retorno[] = " *";
		$retorno[] = " * @return void";
		$retorno[] = " */";
		$retorno[] = ($this->insert_update ? "private" : "public") . " function adicionar()";
		$retorno[] = "{";
		$retorno[] = "	if(\$this->objeto_alterado)";
		$retorno[] = "	{";
		$retorno[] = "		if(\$this->" . $pk[self::TABELA_COLUNA_NOME] . ")";
		$retorno[] = "			throw new Exception('Tentativa de adicionar ao banco de dados um registro ja existente.');";
		if(isset($this->colunas[self::LISTA_FK]))
		{
			foreach($this->colunas[self::LISTA_FK] as $coluna => $dados)
			{
				if($this->converter_fk)
					$retorno[] = "		\$campos['" . $dados[self::TABELA_COLUNA_NOME] . "'] = \$" . $this->converteFkParaObj($dados[self::TABELA_COLUNA_NOME], true) . ($dados[self::TABELA_COLUNA_NULO] == "NO" ? ";" : " ? \$" . $this->converteFkParaObj($dados[self::TABELA_COLUNA_NOME], true) . " : 'NULL';");
				else
					$retorno[] = "		\$campos['" . $dados[self::TABELA_COLUNA_NOME] . "'] = \$this->" . $dados[self::TABELA_COLUNA_NOME] . ($dados[self::TABELA_COLUNA_NULO] == "NO" ? ";" : " ? \$this->" . $dados[self::TABELA_COLUNA_NOME] . " : 'NULL';");
			}
		}
		foreach($this->colunas[self::LISTA_OUTROS] as $coluna => $dados)
			$retorno[] = "		\$campos['" . $dados[self::TABELA_COLUNA_NOME] . "'] = " . ($dados[self::TABELA_COLUNA_NULO] == "NO" ? $this->getTratamentoBanco($dados[self::TABELA_COLUNA_TIPO], $dados[self::TABELA_COLUNA_NOME]) . ";" : "\$this->" . $dados[self::TABELA_COLUNA_NOME] . " ? " . $this->getTratamentoBanco($dados[self::TABELA_COLUNA_TIPO], $dados[self::TABELA_COLUNA_NOME]) . " : 'NULL';");
		$retorno[] = "		\$query = 'INSERT INTO " . $this->tabela . " (`' . implode('`, `', array_keys(\$campos)) . '`) VALUES (' . implode(', ', \$campos) . ')';";
		$retorno[] = "		mysql_query(\$query);";
		$retorno[] = "		if(mysql_error())";
		$retorno[] = "			throw new Exception('Ocorreu um erro durante a adição de " . strtolower($this->nome_classe) . " no banco de dados: ' . mysql_error());";
		$retorno[] = "		\$this->" . $pk[self::TABELA_COLUNA_NOME] . " = mysql_insert_id();";
		$retorno[] = "		\$this->objeto_alterado = false;";
		$retorno[] = "	}";
		$retorno[] = "}";
		return "	" . implode("\n	", $retorno);
	}

	/**
	 * Monta o código-fonte do método de atualizar do objeto de acordo com as colunas da tabela.
	 *
	 * @return string Retorna o código-fonte do método atualizar().
	 */
	private function getMetodoAtualizar()
	{
		$pk = array_shift($this->colunas[self::LISTA_PK]);
		$this->colunas[self::LISTA_PK][$pk[self::TABELA_COLUNA_NOME]] = $pk;		
		$documentacao = "Atualiza os dados do objeto no banco de dados.";
		if(!mb_check_encoding($documentacao, 'UTF-8'))
			$documentacao = utf8_encode($documentacao);
		$retorno[] = "/**";
		$retorno[] = " * " . wordwrap($documentacao, 80, "\n	 * ");
		$retorno[] = " *";
		$retorno[] = " * @return void";
		$retorno[] = " */";
		$retorno[] = ($this->insert_update ? "private" : "public") . " function atualizar()";
		$retorno[] = "{";
		$retorno[] = "	if(\$this->objeto_alterado)";
		$retorno[] = "	{";
		$retorno[] = "		if(!\$this->" . $pk[self::TABELA_COLUNA_NOME] . ")";
		$retorno[] = "			throw new Exception('Tentativa de atualizar no banco de dados um registro inexistente.');";

		if(isset($this->colunas[self::LISTA_FK]))
		{
			foreach($this->colunas[self::LISTA_FK] as $coluna => $dados)
			{
				if($this->converter_fk)
					$retorno[] = "		\$campos['" . $dados[self::TABELA_COLUNA_NOME] . "'] = \"`" . $dados[self::TABELA_COLUNA_NOME] . "` =  \" . \$" . $this->converteFkParaObj($dados[self::TABELA_COLUNA_NOME], true) . ($dados[self::TABELA_COLUNA_NULO] == "NO" ? ";" : " ? \$" . $this->converteFkParaObj($dados[self::TABELA_COLUNA_NOME], true) . " : 'NULL';");
				else
					$retorno[] = "		\$campos['" . $dados[self::TABELA_COLUNA_NOME] . "'] = \"`" . $dados[self::TABELA_COLUNA_NOME] . "` = \" . \$this->" . $dados[self::TABELA_COLUNA_NOME] . ($dados[self::TABELA_COLUNA_NULO] == "NO" ? ";" : " ? \$this->" . $dados[self::TABELA_COLUNA_NOME] . " : 'NULL';");
			}
		}
		foreach($this->colunas[self::LISTA_OUTROS] as $coluna => $dados)
			$retorno[] = "		\$campos['" . $dados[self::TABELA_COLUNA_NOME] . "'] = \"`" . $dados[self::TABELA_COLUNA_NOME] . "` = \" . " . ($dados[self::TABELA_COLUNA_NULO] == "NO" ? $this->getTratamentoBanco($dados[self::TABELA_COLUNA_TIPO], $dados[self::TABELA_COLUNA_NOME]) . ";" : "\$this->" . $dados[self::TABELA_COLUNA_NOME] . " ? " . $this->getTratamentoBanco($dados[self::TABELA_COLUNA_TIPO], $dados[self::TABELA_COLUNA_NOME]) . " : 'NULL';");

		$retorno[] = "		\$query = 'UPDATE " . $this->tabela . " SET ' . implode(', ', \$campos) . ' WHERE " . $pk[self::TABELA_COLUNA_NOME] . " = ' . \$this->" . $pk[self::TABELA_COLUNA_NOME] . ";";
		$retorno[] = "		mysql_query(\$query);";
		$retorno[] = "		if(mysql_error())";
		$retorno[] = "			throw new Exception('Ocorreu um erro durante a alteração de " . strtolower($this->nome_classe) . " no banco de dados: ' . mysql_error());";
		$retorno[] = "		\$this->objeto_alterado = false;";
		$retorno[] = "	}";
		$retorno[] = "}";
		return "	" . implode("\n	", $retorno);
	}

	/**
	 * Monta o código-fonte do método de salvar do objeto, porém, para uso
	 * deste recurso é necessário ativar a opção antes de gerar a classe.
	 *
	 * @return string Retorna o código-fonte do método salvar().
	 * @see iMoleza::setAdicionarSalvarPrivado()
	 */
	private function getMetodoSalvar()
	{
		$pk = array_shift($this->colunas[self::LISTA_PK]);
		$this->colunas[self::LISTA_PK][$pk[self::TABELA_COLUNA_NOME]] = $pk;
		$documentacao = "Realiza a chamada do método de inserção ou atualização dos dados de acordo com o estado do objeto.";
		if(!mb_check_encoding($documentacao, 'UTF-8'))
			$documentacao = utf8_encode($documentacao);
		$retorno[] = "/**";
		$retorno[] = " * " . wordwrap($documentacao, 80, "\n	 * ");
		$retorno[] = " *";
		$retorno[] = " * @return void";
		$retorno[] = " */";
		$retorno[] = "public function salvar()";
		$retorno[] = "{";
		$retorno[] = "	\$this->" . $pk[self::TABELA_COLUNA_NOME] . " ? \$this->atualizar() : \$this->adicionar();";
		$retorno[] = "}";
		return "	" . implode("\n	", $retorno);	
	}

	/**
	 * Monta o código-fonte do método de deletar do objeto.
	 *
	 * @return string Retorna o código-fonte do método deletar().
	 */
	private function getMetodoDeletar()
	{
		$pk = array_shift($this->colunas[self::LISTA_PK]);
		$this->colunas[self::LISTA_PK][$pk[self::TABELA_COLUNA_NOME]] = $pk;		
		$documentacao = "Deleta o registro no banco de dados.";
		if(!mb_check_encoding($documentacao, 'UTF-8'))
			$documentacao = utf8_encode($documentacao);
		$retorno[] = "/**";
		$retorno[] = " * " . wordwrap($documentacao, 80, "\n	 * ");
		$retorno[] = " *";
		$retorno[] = " * @return void";
		$retorno[] = " */";
		$retorno[] = "public function deletar()";
		$retorno[] = "{";
		$retorno[] = "	if(!\$this->" . $pk[self::TABELA_COLUNA_NOME] . ")";
		$retorno[] = "		throw new Exception('Tentativa de deletar do banco de dados um registro inexistente.');";
		$retorno[] = "	\$query = 'DELETE FROM " . $this->tabela . " WHERE " . $pk[self::TABELA_COLUNA_NOME] . " = ' . \$this->" . $pk[self::TABELA_COLUNA_NOME] . ";";
		$retorno[] = "	mysql_query(\$query);";
		$retorno[] = "	if(mysql_error())";
		$retorno[] = "		throw new Exception('Ocorreu um erro durante a alteração de " . strtolower($this->nome_classe) . " no banco de dados: ' . mysql_error());";
		$retorno[] = "}";
		return "	" . implode("\n	", $retorno);
	}

	/**
	 * Monta o código-fonte do método de busca do objeto de acordo com as colunas da tabela.
	 *
	 * @return string Retorna o código-fonte do método buscar().
	 */
	private function getMetodoBuscar()
	{
		$pk = array_shift($this->colunas[self::LISTA_PK]);
		$this->colunas[self::LISTA_PK][$pk[self::TABELA_COLUNA_NOME]] = $pk;		
		$documentacao = "Realiza a consulta dos registros presentes no banco de dados de acordo com os termos informados para a pesquisa.";
		if(!mb_check_encoding($documentacao, 'UTF-8'))
			$documentacao = utf8_encode($documentacao);
		$retorno[] = "/**";
		$retorno[] = " * " . wordwrap($documentacao, 80, "\n	 * ");
		$retorno[] = " *";
		$retorno[] = " * @param array \$colunas dados a ser obtidos na consulta.";
		$retorno[] = " * 	Exemplo: \$colunas = array('nome', 'sobrenome', 'idade');";
		$retorno[] = " * @param array \$where termos a ser considerados na consulta.";
		$retorno[] = " * 	Exemplo: \$where = array('nome LIKE \"%JOSE%\"', 'idade > 5', 'data IS NULL');";
		$retorno[] = " * @param array \$ordem tipo de ordenação a ser obtido na consulta.";
		$retorno[] = " *	Exemplo: \$ordem = array('coluna' => array('nome', 'sobrenome'), 'ordem' => 'ASC');";
		$retorno[] = " * @param array \$limite registro inicial e quantidade de registros a ser retornados na consulta.";
		$retorno[] = " * 	Exemplo: \$limite = array(0, 30);";
		$retorno[] = " * @return array Retorna o resultado da consulta tendo o índice a chave identificadora apontando para os dados do registro.";
		$retorno[] = " */";
		$retorno[] = "public function buscar(\$colunas = array(), \$where = array(), \$ordem = array(), \$limite = array())";
		$retorno[] = "{";

		$retorno[] = "	\$query  = 'SELECT ' . (\$colunas ? implode(', ', \$colunas) : '*') . ' ';";
		$retorno[] = "	\$query .= 'FROM " . $this->tabela . " ' . (\$where ? 'WHERE ' . implode(' AND ', \$where) : '');";
		$retorno[] = "	\$query .= (\$ordem ? ' ORDER BY ' . implode(', ', \$ordem['coluna']) . ' ' . \$ordem['ordem'] : '');";
		$retorno[] = "	\$query .= (\$limite ? ' LIMIT ' . implode(', ', \$limite) : '');";
		$retorno[] = "	\$consulta = mysql_query(\$query);";
		$retorno[] = "	if(mysql_error())";
		$retorno[] = "		throw new Exception('Ocorreu um erro durante a consulta de dados em " . strtolower($this->nome_classe) . " no banco de dados: ' . mysql_error());";
		$retorno[] = "	\$retorno = array();";
		$retorno[] = "	while(\$dados = mysql_fetch_array(\$consulta))";
		$retorno[] = "	{";
		$retorno[] = "		\$retorno[\$dados['" . $pk[self::TABELA_COLUNA_NOME] . "']]['" . $pk[self::TABELA_COLUNA_NOME] . "'] = \$dados['" . $pk[self::TABELA_COLUNA_NOME] . "'];";
		if(isset($this->colunas[self::LISTA_FK]))
		{
			foreach($this->colunas[self::LISTA_FK] as $coluna => $dados)
				$retorno[] = "		\$retorno[\$dados['" . $pk[self::TABELA_COLUNA_NOME] . "']]['" . $dados[self::TABELA_COLUNA_NOME] . "'] = \$dados['" . $dados[self::TABELA_COLUNA_NOME] . "'];";
		}
		foreach($this->colunas[self::LISTA_OUTROS] as $coluna => $dados)
			$retorno[] = "		\$retorno[\$dados['" . $pk[self::TABELA_COLUNA_NOME] . "']]['" . $dados[self::TABELA_COLUNA_NOME] . "'] = \$dados['" . $dados[self::TABELA_COLUNA_NOME] . "'];";
		$retorno[] = "	}";
		$retorno[] = "	return \$retorno;";
		$retorno[] = "}";
		return "	" . implode("\n	", $retorno);
	}

	/**
	 * Realiza a montagem do código-fonte como um todo e salva em arquivo.
	 *
	 * @return void
	 */
	public function saida($pasta_destino = null)
	{
		$codigo[] = "<?php";
		$codigo[] = 	$this->getDocumentacaoCabecalho();
		$codigo[] = 	"";
		$codigo[] = 	"class " . $this->nome_classe;
		$codigo[] = 	"{";
		$codigo[] = 		$this->getDeclaracaoAtributos();
		$codigo[] = 		"";
		$codigo[] = 		$this->getMetodoConstrutor();
		$codigo[] = 		"";
		$codigo[] = 		$this->getMetodoSet();
		$codigo[] = 		"";
		$codigo[] = 		$this->getMetodoGet();
		$codigo[] = 		"";
		if($this->insert_update)
			$codigo[] = 	$this->getMetodoSalvar();
			$codigo[] = 	"";
		$codigo[] = 		$this->getMetodoAdicionar();
		$codigo[] = 		"";
		$codigo[] = 		$this->getMetodoAtualizar();
		$codigo[] = 		"";
		$codigo[] = 		$this->getMetodoDeletar();
		$codigo[] = 		"";
		$codigo[] = 		$this->getMetodoBuscar();
		$codigo[] = 	"}";
		$codigo[] = "?>";
		highlight_string(implode("\n", $codigo) . "\n");
		if(file_exists($pasta_destino))
			file_put_contents($pasta_destino . $this->nome_classe . '.class.php', implode("\n", $codigo));
	}
}