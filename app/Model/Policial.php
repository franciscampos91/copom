<?php

class Policial
{
    public function __construct(
        $dados, $emailFuncional, $telefone, $funcao, $funcoes, $foto, $fotoBase
    ) {
        $this->cpf = trim($dados->Documentos->FuncionarioDocumento[0]->Numero . $dados->Documentos->FuncionarioDocumento[0]->DigitoDocumento);
        $this->re = trim($dados->numeroREPM);
        $this->digre = trim($dados->digitoREPM);
        $this->ptgr = trim($dados->codigoPostoGraduacaoPM->siglaPostoGraduacaoPM);
        $this->codPtgr = trim($dados->codigoPostoGraduacaoPM->nivelPostoGraduacao);
        $this->nome = trim($dados->nomePM);
        $this->guerra = trim($dados->nomeGuePM);
        $this->sexo = trim($dados->sexoPM);
        $this->unidade = trim($dados->codigoOPMAtualPM->descricaoNivel03OPMBatalhao);
        $this->cmdo = trim($dados->codigoOPMAtualPM->descricaoNivel02OPMCPA);
        $this->gcmdo = trim($dados->codigoOPMAtualPM->descricaoNivel01OPMComando);
        $this->codopm = trim($dados->codigoOPMAtualPM->codigoOPM);
        $this->situacaoLegal = trim($dados->codigoSituacaoLegalPM->descricaoSituacaoLegal);
        $this->dataAdmissao = trim($dados->dataMatriculaPM);
        $this->dataNascimento = trim($dados->dataNascimentoPM);
        $this->estadoCivil = trim($dados->descricaoSituacaoCivilPM);
        $this->foto = $foto;
        $this->fotoBase = $fotoBase;
        $this->email = $emailFuncional;
        $this->telefone = $telefone;
        $this->funcao = $funcao;
        $this->funcoes = $funcoes;
    }


    public static function buscaPM($usuario)
    {
        // Validação do CPF (11 caracteres)
        if (strlen($usuario) == 11) {
            $dados = self::buscaPMporCPF($usuario);

            if (!$dados) {
                // Redireciona de volta com mensagem de erro se nenhum dado for encontrado
                return redirect()->back()->with('error', 'Nenhum dado encontrado para o CPF fornecido.');
            }
        }
        // Validação do RE (6 ou 7 caracteres)
        else if (strlen($usuario) == 6 || strlen($usuario) == 7) {
            $dados = self::buscaPMporRE($usuario);

            if (!$dados) {
                // Redireciona de volta com mensagem de erro se nenhum dado for encontrado
                return redirect()->back()->with('error', 'Nenhum dado encontrado para o RE fornecido.');
            }
        }
        // Caso de entrada inválida
        else {
            // Redireciona de volta com mensagem de erro para entradas inválidas
            //return redirect()->back()->with('error', 'O usuário deve ter 11 caracteres (CPF) ou 6-7 caracteres (RE).');
            return false;
        }

        // Se chegamos aqui, `dados` não é nulo e prosseguimos com o processamento

        // Procura foto
        $foto = self::procuraFoto($dados->numeroREPM);
        $fotoBase = 'data:image/png;base64,' . $foto;

        // Verifica se existe um e-mail que termina com @gmail.com
        $emailFuncional = self::procuraEmailFuncional($dados);

        // Verifica se existe um celular
        $telefone = self::procuraCelular($dados);

        // Busca função ativa principal
        $funcao = self::procuraFuncao($dados);

        // Busca funções ativas
        $funcoes[] = self::procuraFuncoes($dados);

        // Cria instância do Policial
        $policial = new Policial($dados, $emailFuncional, $telefone, $funcao, $funcoes, $foto, $fotoBase);

        // Retorna o objeto Policial
        return $policial;
    }


    public static function buscaPMporCPF($cpf)
    {

        $pmUrl = "http://sistemas.intranet.policiamilitar.sp.gov.br/WSSCPM/Service.asmx?WSDL"; 
        $soapOptions = array('trace' => 1);
        $pm = new \SoapClient($pmUrl,$soapOptions);

        $rst = $pm->procuraPMPorCPF(array('PMCPFNum' => (float) $cpf));
        $dados = $rst->procuraPMPorCPFResult;
        
        return $dados;

    }


    public static function buscaPMporRE($re)
    {

        $pmUrl = "http://sistemas.intranet.policiamilitar.sp.gov.br/WSSCPM/Service.asmx?WSDL"; 
        $soapOptions = array('trace' => 1);
        $pm = new \SoapClient($pmUrl,$soapOptions);

        $rst = $pm->procuraPMPorRE(array('PMRENum' => (float) substr($re,0,6)));
        $dados = $rst->procuraPMPorREResult;

        return $dados;

    }


    public static function procuraFoto($re)
    {
        $pmUrl = "http://sistemas.intranet.policiamilitar.sp.gov.br/WSSCPM/Service.asmx?WSDL"; 
        $soapOptions = array('trace' => 1);
        $pm = new \SoapClient($pmUrl,$soapOptions);

        $rstp = $pm->procuraFotoPorRE(array('RegistroEstatistico' => (float) $re));
        $foto = base64_encode($rstp->procuraFotoPorREResult);

        return $foto;
    }


    public static function procuraEmailFuncional($dados)
    {
        $email = null;
        $emails = [];

        if (isset($dados->dadosContatoFuncionario) && is_object($dados->dadosContatoFuncionario)) {
            // Verifique se 'FuncionarioContato' existe e é um array
            if (isset($dados->dadosContatoFuncionario->FuncionarioContato) && is_array($dados->dadosContatoFuncionario->FuncionarioContato)) {
                foreach ($dados->dadosContatoFuncionario->FuncionarioContato as $contato) {
                    if (isset($contato->emailContato)) {
                        $emails[] = $contato->emailContato;
                        if (substr($contato->emailContato, -25) === '@policiamilitar.sp.gov.br') {
                            return $contato->emailContato;
                        }
                    }
                }
            } else {
                // Caso 'FuncionarioContato' não seja um array, considere-o como um único objeto
                if (isset($dados->dadosContatoFuncionario->FuncionarioContato->emailContato)) {
                    $email = $dados->dadosContatoFuncionario->FuncionarioContato->emailContato;
                    $emails[] = $email;
                    if (substr($email, -25) === '@policiamilitar.sp.gov.br') {
                        return $email;
                    }
                }
            }
        }

        // Se não encontrou um e-mail com '@policiamilitar.sp.gov.br', retorna o primeiro e-mail encontrado
        return $emails[0] ?? null;

    }

    public static function procuraCelular($dados)
    {
        $celular = null;

    if (isset($dados->dadosContatoFuncionario) && is_object($dados->dadosContatoFuncionario)) {
        // Verifique se 'FuncionarioContato' existe e é um array
        if (isset($dados->dadosContatoFuncionario->FuncionarioContato) && is_array($dados->dadosContatoFuncionario->FuncionarioContato)) {
            // Iterar pelos contatos de trás para frente
            for ($i = count($dados->dadosContatoFuncionario->FuncionarioContato) - 1; $i >= 0; $i--) {
                $contato = $dados->dadosContatoFuncionario->FuncionarioContato[$i];
                if (isset($contato->numeroContato) && isset($contato->dddContato)) {
                    $numero = $contato->numeroContato;
                    $ddd = $contato->dddContato;

                    // Verifica se é um número de celular válido (9 dígitos) ou um telefone com 8 dígitos
                    if (preg_match('/^\d{9}$/', $numero) || preg_match('/^\d{8}$/', $numero)) {
                        $celular = $ddd . $numero;
                        break; // Para ao encontrar o primeiro válido
                    }
                }
            }
        } else {
            // Caso 'FuncionarioContato' não seja um array, considere-o como um único objeto
            if (isset($dados->dadosContatoFuncionario->FuncionarioContato->numeroContato) && isset($dados->dadosContatoFuncionario->FuncionarioContato->dddContato)) {
                $numero = $dados->dadosContatoFuncionario->FuncionarioContato->numeroContato;
                $ddd = $dados->dadosContatoFuncionario->FuncionarioContato->dddContato;

                // Verifica se é um número de celular válido (9 dígitos) ou um telefone com 8 dígitos
                if (preg_match('/^\d{9}$/', $numero) || preg_match('/^\d{8}$/', $numero)) {
                    $celular = $ddd . $numero;
                }
            }
        }
    }

    return $celular;

    }


    public static function procuraFuncao($dados)
    {
        $descricaoFuncao = null;

        if (isset($dados->funcoesPM) && is_object($dados->funcoesPM)) {
            // Verifique se 'Funcao' existe e é um array
            if (isset($dados->funcoesPM->Funcao) && is_array($dados->funcoesPM->Funcao)) {
                // Iterar pelos itens do array
                foreach ($dados->funcoesPM->Funcao as $funcao) {
                    if (isset($funcao->Status) && isset($funcao->Principal) && isset($funcao->descricaoFuncaoPM)) {
                        // Verifica se o Status é 'ATIVO' e Principal é 'S'
                        if ($funcao->Status === 'ATIVO' && $funcao->Principal === 'S') {
                            $descricaoFuncao = $funcao->descricaoFuncaoPM;
                            break; // Para ao encontrar o primeiro válido
                        }
                    }
                }
            } else {
                // Caso 'Funcao' não seja um array, considere-o como um único objeto
                if (isset($dados->funcoesPM->Funcao->Status) && isset($dados->funcoesPM->Funcao->Principal) && isset($dados->funcoesPM->Funcao->descricaoFuncaoPM)) {
                    $funcao = $dados->funcoesPM->Funcao;
                    // Verifica se o Status é 'ATIVO' e Principal é 'S'
                    if ($funcao->Status === 'ATIVO' && $funcao->Principal === 'S') {
                        $descricaoFuncao = $funcao->descricaoFuncaoPM;
                    }
                }
            }
        }

        return $descricaoFuncao;
    }


    public static function procuraFuncoes($dados)
    {
        $funcoesAtivas = [];

        if (isset($dados->funcoesPM) && is_object($dados->funcoesPM)) {
            // Verifique se 'Funcao' existe e é um array
            if (isset($dados->funcoesPM->Funcao) && is_array($dados->funcoesPM->Funcao)) {
                // Iterar pelos itens do array
                foreach ($dados->funcoesPM->Funcao as $funcao) {
                    if (isset($funcao->Status) && isset($funcao->descricaoFuncaoPM)) {
                        // Verifica se o Status é 'ATIVO'
                        if ($funcao->Status === 'ATIVO') {
                            $funcoesAtivas[] = $funcao->descricaoFuncaoPM;
                        }
                    }
                }
            } else {
                // Caso 'Funcao' não seja um array, considere-o como um único objeto
                if (isset($dados->funcoesPM->Funcao->Status) && isset($dados->funcoesPM->Funcao->descricaoFuncaoPM)) {
                    $funcao = $dados->funcoesPM->Funcao;
                    // Verifica se o Status é 'ATIVO'
                    if ($funcao->Status === 'ATIVO') {
                        $funcoesAtivas[] = $funcao->descricaoFuncaoPM;
                    }
                }
            }
        }

        return $funcoesAtivas;
    }


}