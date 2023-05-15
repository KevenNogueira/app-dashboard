<?php

class Dashboard
{
    private $data_inicio;
    private $data_fim;
    private $numeroVendas;
    private $valorVendas;
    private $clienteAtivo;
    private $clienteInativo;
    private $numeroElogios;
    private $numeroCriticas;
    private $numeroSugestoes;
    private $valorDespesa;

    public function __get($attr)
    {
        return $this->$attr;
    }

    public function __set($attr, $value)
    {
        $this->$attr = $value;
        return $this;
    }

    public function serialize()
    {
        return json_encode(get_object_vars($this));
    }
}

// Conexão com banco

class Conexao
{
    private $host = 'localhost';
    private $dbname = 'dashboard';
    private $user = 'root';
    private $pass = '';

    public function conectar()
    {
        try {
            $conexao = new PDO(
                "mysql:host=$this->host;dbname=$this->dbname",
                "$this->user",
                "$this->pass"
            );
            $conexao->exec('set charset utf8');

            return $conexao;
        } catch (PDOException $e) {
            echo '<p> O erro de conexão com o Bando de Dados!' . $e->getMessage() . '</p>';
        }
    }
}


// Class de manipulação no DB

class Bd
{
    private $conexao;
    private $dashboard;

    public function __construct(Conexao $conexao, Dashboard $dashboard)
    {
        $this->conexao = $conexao->conectar();
        $this->dashboard = $dashboard;
    }

    public function getNumVendas()
    {
        $query = '
            SELECT 
                COUNT(*) AS num_vendas
            FROM 
                tb_vendas tv
            WHERE 
                data_venda 
            BETWEEN 
                ? AND ?
            ';

        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(1, $this->dashboard->__get('data_inicio'));
        $stmt->bindValue(2, $this->dashboard->__get('data_fim'));
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ)->num_vendas;
    }
    public function getValorVendas()
    {
        $query = '
            SELECT 
                SUM(total) AS valor_vendas
            FROM 
                tb_vendas tv
            WHERE 
                data_venda 
            BETWEEN 
                ? AND ?
            ';

        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(1, $this->dashboard->__get('data_inicio'));
        $stmt->bindValue(2, $this->dashboard->__get('data_fim'));
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ)->valor_vendas;
    }

    public function getClienteAtivo()
    {
        $query = '
            SELECT 
                COUNT(*) AS num_cli_ativo
            FROM 
                tb_clientes tc
            WHERE 
                data_admisao 
            BETWEEN 
                ? AND ?
            AND 
                cliente_ativo = 1
        ';

        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(1, $this->dashboard->__get('data_inicio'));
        $stmt->bindValue(2, $this->dashboard->__get('data_fim'));
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ)->num_cli_ativo;
    }
    public function getClienteInativo()
    {
        $query = '
            SELECT 
                COUNT(*) AS num_cli_inativo
            FROM 
                tb_clientes tc
            WHERE 
                data_admisao 
            BETWEEN 
                ? AND ?
            AND 
                cliente_ativo = 0
        ';

        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(1, $this->dashboard->__get('data_inicio'));
        $stmt->bindValue(2, $this->dashboard->__get('data_fim'));
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ)->num_cli_inativo;
    }

    public function getNumeroElogios()
    {
        $query = '
            SELECT 
                COUNT(*) AS num_elogio
            FROM 
                tb_contatos tc 
            WHERE 
                tc.data_contato 
            BETWEEN 
                ? AND ?
            AND 
                tipo_contato  = 3
        ';

        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(1, $this->dashboard->__get('data_inicio'));
        $stmt->bindValue(2, $this->dashboard->__get('data_fim'));
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ)->num_elogio;
    }

    public function getNumeroCriticas()
    {
        $query = '
            SELECT 
                COUNT(*) AS num_criticas
            FROM 
                tb_contatos tc 
            WHERE 
                tc.data_contato 
            BETWEEN 
                ? AND ?
            AND 
                tipo_contato  = 1
        ';

        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(1, $this->dashboard->__get('data_inicio'));
        $stmt->bindValue(2, $this->dashboard->__get('data_fim'));
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ)->num_criticas;
    }

    public function getNumeroSugestoes()
    {
        $query = '
            SELECT 
                COUNT(*) AS num_sugestoes
            FROM 
                tb_contatos tc 
            WHERE 
                tc.data_contato 
            BETWEEN 
                ? AND ?
            AND 
                tipo_contato  = 2
        ';

        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(1, $this->dashboard->__get('data_inicio'));
        $stmt->bindValue(2, $this->dashboard->__get('data_fim'));
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ)->num_sugestoes;
    }

    public function getTotalDespesa()
    {
        $query = '
            SELECT 
                SUM(total) AS total_despesa
            FROM
                tb_despesas td 
            WHERE 
                data_despesa 
            BETWEEN 
                ? AND ?
        ';

        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(1, $this->dashboard->__get('data_inicio'));
        $stmt->bindValue(2, $this->dashboard->__get('data_fim'));
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ)->total_despesa;
    }
}

$dashboard = new Dashboard();

$conexao = new Conexao();

$bd = new Bd($conexao, $dashboard);

$competencia = explode('-', $_GET['competencia']);
$ano = $competencia[0];
$mes = $competencia[1];
$dias_do_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);

$dashboard->__set('data_inicio', $ano . '-' . $mes . '-01');
$dashboard->__set('data_fim', $ano . '-' . $mes . '-' . $dias_do_mes);
$dashboard->__set('numeroVendas', $bd->getNumVendas());
$dashboard->__set('valorVendas', $bd->getValorVendas());
$dashboard->__set('clienteAtivo', $bd->getClienteAtivo());
$dashboard->__set('clienteInativo', $bd->getClienteInativo());
$dashboard->__set('numeroElogios', $bd->getNumeroElogios());
$dashboard->__set('numeroCriticas', $bd->getNumeroCriticas());
$dashboard->__set('numeroSugestoes', $bd->getNumeroSugestoes());
$dashboard->__set('valorDespesa', $bd->getTotalDespesa());


echo $dashboard->serialize();
