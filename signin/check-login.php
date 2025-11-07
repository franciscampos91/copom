<?php

require_once ('../app/Model/Policial.php');


class Check_login
{

    public function index()
    {

        session_start();


        if (!($_SERVER['REQUEST_METHOD'] === 'POST')) {
        
            $_SESSION['erro'] = 'Insira as informações de login';
            
            header('Location: index.php');
            exit();
        }
        
        $usuario = $_POST['cpf'];
        $senha   = $_POST['senha'];


        self::verificaUsuarioSenha($usuario, $senha);
        
    }

    public function verificaUsuarioSenha($cpf, $senha)
    {

        $msSis = "PROTOCOLO";
        $msSubSis = "PROTOCOLO";
        //$wsUrl = "http://sistemas.intranet.policiamilitar.sp.gov.br/MS/aws_permxml.aspx?WSDL";
        $wsUrl = "http://sistemasadmin.intranet.policiamilitar.sp.gov.br/MS/aws_permxml.aspx?WSDL";

        $soapOptions = array('trace' => 1);
        $soapParams  = array('Sisnomsis' 		=> $msSis,
                            'Subsisnomsubsis' 	=> $msSubSis,
                            'Usrnumcpf' 		=> (float) $cpf,
        //					 'Usrnumcpf' 		=> (float) $_GET['cpf'],
                            'Tip_fuc' 			=> "M",
                            'Senha' 			=> $senha);
        //					 'Senha' 			=> $_GET['senha_do_holerite']);

        try {
            $ws = new SoapClient($wsUrl, $soapOptions);
        } catch(Exception $e) {
            print "SOAP Constructor Error: ". $e->getMessage();
            return false;
        }

        $rst = $ws->Execute($soapParams);
        $obj = simplexml_load_string($rst->Xml_ws_perm, 'SimpleXMLElement', LIBXML_NOERROR | LIBXML_NOWARNING);
        $principal='../index.php';

        $erro=$obj->Status;

        
        switch ($erro) {
            case 0:
                $policial = Policial::buscaPM($cpf);
                self::criarSessaoUsuario($policial);
                header('Location: ../index.php');
                exit();
            case 2:
                //$msg = 'CPF Inexistente';
                $_SESSION['erro'] = 'CPF ou Senha inválidos';            
                header('Location: index.php');
                exit();
            case 3:
                //$msg = 'Senha inválida';
                $_SESSION['erro'] = 'CPF ou Senha inválidos';            
                header('Location: index.php');
                exit();
            case 4:
                //$msg = 'Sistema inválido';
                $_SESSION['erro'] = 'Sistema inválido';            
                header('Location: index.php');
                exit();
            default:
                //$msg = 'Erro não identificado';
                $_SESSION['erro'] = 'Erro não identificado';            
                header('Location: index.php');
                exit();

        }

    }

    public function criarSessaoUsuario($usuario)
    {
        // Atribuindo valores às variáveis de sessão
        $_SESSION['usuario'] = $usuario->cpf;
        $_SESSION['cpf'] = $usuario->cpf;
        $_SESSION['re'] = $usuario->re;
        $_SESSION['digre'] = $usuario->digre;
        $_SESSION['ptgr'] = $usuario->ptgr;
        $_SESSION['codptgr'] = $usuario->codPtgr;
        $_SESSION['nome'] = $usuario->nome;
        $_SESSION['guerra'] = $usuario->guerra;
        $_SESSION['sexo'] = $usuario->sexo;
        $_SESSION['batalhao'] = $usuario->unidade;
        $_SESSION['unidade'] = $usuario->unidade;
        $_SESSION['cmdo'] = $usuario->cmdo;
        $_SESSION['gcmdo'] = $usuario->gcmdo;
        $_SESSION['codopm'] = $usuario->codopm;
        $_SESSION['situacaoLegal'] = $usuario->situacaoLegal;
        $_SESSION['foto'] = $usuario->foto;
        $_SESSION['fotoBase'] = $usuario->fotoBase;
        $_SESSION['email'] = $usuario->email;
        $_SESSION['telefone'] = $usuario->telefone;
        $_SESSION['funcao'] = $usuario->funcao;
        $_SESSION['funcoes'] = $usuario->funcoes;
        $_SESSION['dataAdmissao'] = $usuario->dataAdmissao;
        $_SESSION['dataNascimento'] = $usuario->dataNascimento;
        $_SESSION['estadoCivil'] = $usuario->estadoCivil;
    }
    
    
}

$check_login = new Check_login();
$check_login->index();






