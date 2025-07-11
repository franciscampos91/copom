<?php

class PolicialController
{

    public function index()
    {
        $medalhas = self::buscaMedalhas(38586286893);

        var_dump($medalhas);

        // Acessa o array de medalhas dentro do objeto retornado
        if (isset($medalhas->ProcuraMedalhasPorCPFResult->Medalha) && is_array($medalhas->ProcuraMedalhasPorCPFResult->Medalha)) {
            foreach ($medalhas->ProcuraMedalhasPorCPFResult->Medalha as $medalha) {
                echo "Id: " . $medalha->Id . "<br>";
                echo "Código da Medalha: " . $medalha->CodigoMedalha . "<br>";
                echo "Descrição: " . $medalha->Descricao . "<br>";
                echo "Boletim: " . $medalha->Boletim . "<br>";
                // Converte a data para o formato brasileiro
                $data = new DateTime($medalha->Data);
                echo "Data: " . $data->format('d/m/Y') . "<br><br>";
            }
        } else {
            echo "Nenhuma medalha encontrada.";
        }
    }


    public function pesquisarPM()
    {

        try {

            $loader = new \Twig\Loader\FilesystemLoader('app/View');
            $twig = new \Twig\Environment($loader);
            $template = $twig->load('pesquisa-policial.html');

            $policial = $this::buscaPM($_POST['re']);

            $conteudo = $template->render((array) $policial);

            echo $conteudo;


        } catch (Exception $ex) {
            echo $ex->getMessage();
        }

    }



    public static function buscaPM($usuario)
    {
        // Validação do CPF (11 caracteres)
        if (strlen($usuario) == 11) {
            $dados = self::buscaPMporCPF($usuario);

            if (!$dados) {
                // Redireciona de volta com mensagem de erro se nenhum dado for encontrado
               // return redirect()->back()->with('erro', 'Nenhum dado encontrado para o CPF fornecido.');
               return false;
            }
        }
        // Validação do RE (6 ou 7 caracteres)
        else if (strlen($usuario) == 6 || strlen($usuario) == 7) {
            $dados = self::buscaPMporRE($usuario);

            if (!$dados) {
                // Redireciona de volta com mensagem de erro se nenhum dado for encontrado
             //   return redirect()->back()->with('erro', 'Nenhum dado encontrado para o RE fornecido.');
             return false;
            }
        }
        // Caso de entrada inválida
        else {
            // Redireciona de volta com mensagem de erro para entradas inválidas
            //return redirect()->back()->with('error', 'O usuário deve ter 11 caracteres (CPF) ou 6-7 caracteres (RE).');
            return false;
        }

        // Se chegamos aqui, `dados` não é nulo e prosseguimos com o processamento
        

        $erroCodigo = $dados->erroCodigo;

        if ($erroCodigo == -1) {
            return false;
        } else {
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

            //procurar GCMDO
            //$gcmdo = self::procuraGCMDO(substr($dados->codigoOPMAtualPM->codigoOPM, 0, 5));
            $gcmdo = '';

            // Busca documentos
            $documentos = self::buscaDocumentos($dados);

                        
            // Busca medalhas
            $medalhas = self::buscaMedalhas(trim($dados->Documentos->FuncionarioDocumento[0]->Numero . $dados->Documentos->FuncionarioDocumento[0]->DigitoDocumento));

            // Cria instância do Policial
            $policial = new Policial($dados, $emailFuncional, $telefone, $funcao, $funcoes, $foto, $fotoBase, $gcmdo, $documentos, $medalhas);

             // Retorna o objeto Policial
            return $policial;
        }


        

    }


    public static function buscaPMporCPF($cpf)
    {

        // verificar se o serviço está disponível antes de prosseguir nas buscas
        $pmUrl = "http://sistemas.intranet.policiamilitar.sp.gov.br/WSSCPM/Service.asmx?WSDL";
        // Verifica se o serviço está disponível
        if (!self::isServiceAvailable($pmUrl)) {

            return redirect()->back()->with('erro', 'O serviço de consulta está temporariamente indisponível. Tente novamente mais tarde. Erro 503');

            //  return response()->json(['erro' => 'O serviço está temporariamente indisponível. Tente novamente mais tarde.'], 503);
        } else {
            $soapOptions = array('trace' => 1);
            $pm = new \SoapClient($pmUrl, $soapOptions);

            $rst = $pm->procuraPMPorCPF(array('PMCPFNum' => (float) $cpf));
            $dados = $rst->procuraPMPorCPFResult;

            return $dados;

        }


    }


    public static function buscaPMporRE($re)
    {

        $pmUrl = "http://sistemas.intranet.policiamilitar.sp.gov.br/WSSCPM/Service.asmx?WSDL";
        $soapOptions = array('trace' => 1);
        $pm = new \SoapClient($pmUrl, $soapOptions);

        $rst = $pm->procuraPMPorRE(array('PMRENum' => (float) substr($re, 0, 6)));
        $dados = $rst->procuraPMPorREResult;

        return $dados;

    }


    public static function procuraFoto($re)
    {
        $pmUrl = "http://sistemas.intranet.policiamilitar.sp.gov.br/WSSCPM/Service.asmx?WSDL";
        $soapOptions = array('trace' => 1);
        $pm = new \SoapClient($pmUrl, $soapOptions);

        /* Foto com tamanho reduzido       
        $rstp = $pm->procuraFotoPorRE(array('RegistroEstatistico' => (float) $re));
        $foto = base64_encode($rstp->procuraFotoPorREResult);
         */

        $rstp = $pm->procuraFotoPorRESemReducao(array('RegistroEstatistico' => (float) $re));
        $foto = base64_encode($rstp->procuraFotoPorRESemReducaoResult);

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


    public static function buscaMedalhas($cpf)
    {

        $pmUrl = "http://sistemas.intranet.policiamilitar.sp.gov.br/WSSCPM/Service.asmx?WSDL";
        // Verifica se o serviço está disponível

        $soapOptions = array('trace' => 1);
        $pm = new \SoapClient($pmUrl, $soapOptions);

        $medalhas = $pm->ProcuraMedalhasPorCPF(array('pmCPF' => (float) $cpf));

        return $medalhas;



    }

    public static function buscaDocumentos($dados)
    {
        $documentos = [
            'tituloEleitor' => null,
            'zonaEleitoral' => null,
            'secaoEleitoral' => null,
            'municipioEleitoral' => null,
            'cnh' => null,
            'rg' => null,
            'dgrg' => null,
            'pispasep' => null,
        ];

        if (!isset($dados->Documentos->FuncionarioDocumento)) {
            return $documentos;
        }

        foreach ($dados->Documentos->FuncionarioDocumento as $doc) {
            switch ($doc->codigoTipoDocumento) {
                case 1:
                    // CPF
                    break;

                case 2:
                    $documentos['pispasep'] = $doc->Numero;
                    break;

                case 3:
                    $documentos['rg'] = $doc->Numero;
                    $documentos['dgrg'] = isset($doc->DigitoDocumento) ? trim($doc->DigitoDocumento) : null;
                    $documentos['ufrg'] = $doc->UFDocumento;
                    $documentos['emissorrg'] = $doc->EmissorDocumento;

                    break;

                case 4:
                    $documentos['tituloEleitor'] = $doc->Numero;
                    if (isset($doc->InformacoesComplementares->InformacaoComplementar)) {
                        foreach ($doc->InformacoesComplementares->InformacaoComplementar as $info) {
                            if ($info->Chave === 'Zona_Eleitoral') {
                                $documentos['zonaEleitoral'] = $info->Valor;
                            }
                            if ($info->Chave === 'Secao_Eleitoral') {
                                $documentos['secaoEleitoral'] = $info->Valor;
                            }
                            if ($info->Chave === 'Municipio_Eleitoral') {
                                $documentos['municipioEleitoral'] = $info->Valor;
                            }
                        }
                    }
                    break;

                case 5:
                    $documentos['cnh'] = $doc->Numero;
                    break;
            }
        }

        return $documentos;
    }



    


    /*public static function procuraGCMDO($codopm)
    {
        try {
            $gcmdo = DB::table('unidades2')
                ->where('cod', $codopm)
                ->pluck('gcmdo')
                ->first();

            return $gcmdo;
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao buscar o GCMDO pelo CODOPM: ' . $e->getMessage(), 'funcao' => 'UsuarioController@procuraGCMDO'], 500);
        }

    }*/

}


class Policial
{

    public function __construct(
        $dados,
        $emailFuncional,
        $telefone,
        $funcao,
        $funcoes,
        $foto,
        $fotoBase,
        $gcmdo,
        $documentos,
        $medalhas
    ) {
        $this->cpf = trim($dados->Documentos->FuncionarioDocumento[0]->Numero . $dados->Documentos->FuncionarioDocumento[0]->DigitoDocumento);
        $this->re = trim($dados->numeroREPM);
        $this->digre = trim($dados->digitoREPM);
        $this->dgre = trim($dados->digitoREPM);
        $this->ptgr = trim($dados->codigoPostoGraduacaoPM->siglaPostoGraduacaoPM);
        $this->codPtgr = trim($dados->codigoPostoGraduacaoPM->nivelPostoGraduacao);
        $this->nome = trim($dados->nomePM);
        $this->guerra = trim($dados->nomeGuePM);
        $this->sexo = trim($dados->sexoPM);
        $this->unidade = trim($dados->codigoOPMAtualPM->descricaoNivel03OPMBatalhao);
        $this->opm = trim($dados->codigoOPMAtualPM->descricaoNivel03OPMBatalhao);
        $this->cmdo = trim($dados->codigoOPMAtualPM->descricaoNivel02OPMCPA);
        $this->descricaoNivel01OPMComando = trim($dados->codigoOPMAtualPM->descricaoNivel01OPMComando);
        $this->codopm = trim($dados->codigoOPMAtualPM->codigoOPM);
        $this->situacaoLegal = trim($dados->codigoSituacaoLegalPM->descricaoSituacaoLegal);
        $this->dataNascimento = substr(trim($dados->dataNascimentoPM), 0, 10);
        $this->estadoCivil = trim($dados->descricaoSituacaoCivilPM);
        $this->foto = $foto;
        $this->fotoBase = $fotoBase;
        $this->email = $emailFuncional;
        $this->telefone = $telefone;
        $this->funcao = $funcao;
        $this->funcoes = $funcoes;
        $this->gcmdo = $gcmdo;
        $this->erroCodigo = $dados->erroCodigo;

        $this->rg = $documentos['rg'];
        $this->dgrg = $documentos['dgrg'];
        $this->ufrg = $documentos['ufrg'];        
        $this->emissorrg = $documentos['emissorrg'];
        $this->tituloEleitor = $documentos['tituloEleitor'];
        $this->zonaEleitoral = $documentos['zonaEleitoral'];
        $this->secaoEleitoral = $documentos['secaoEleitoral'];
        $this->municipioEleitoral = $documentos['municipioEleitoral'];
        $this->cnh = $documentos['cnh'];
        $this->medalhas = $medalhas;
        $this->natural = trim($dados->codigoMunicipioNascimentoPM);
        $this->codigoOPMAnteriorPM = $dados->codigoOPMAnteriorPM;
        $this->codigoOPMEfetivaPM = $dados->codigoOPMEfetivaPM;        
        $this->religiao = $dados->descricaoReligiaoPM;
        $this->tipoSanguineo = $dados->codigoTipoSanguineoPM;
        $this->fatorSanguineo = $dados->codigoFatorSanguineoPM;
        $this->altura = $dados->numeroAlturaPM;
        $this->bolGPosse = $dados->numeroBoletimGeralPossePM;
        $this->dataPrimeiroEmprego = $dados->dataPrimeiroEmpregoPM;        
        $this->bairroOpm = trim($dados->codigoOPMAtualPM->bairroOPM);
        $this->cepOPM = trim($dados->codigoOPMAtualPM->cepOPM);
        $this->enderecoOPM = trim($dados->codigoOPMAtualPM->enderecoOPM);
        $this->dataAdmissao = substr(trim($dados->dataAdmissaoPM), 0, 10);
        $this->idade = self::idade($dados->dataNascimentoPM);
        $this->tempoServico = self::calcularTempoServico($dados->dataAdmissaoPM);


    }


    public static function idade($dataNascimento)
    {
        if (!empty($dataNascimento) && $dataNascimento != '0001-01-01') {
            $nascimento = new DateTime($dataNascimento);
            $hoje = new DateTime();
            $idade = $nascimento->diff($hoje)->y;
        }

        return $idade;
    }

    public static function calcularTempoServico($dataAdmissao)
    {
        if (empty($dataAdmissao) || $dataAdmissao == '0001-01-01') {
            return null;
        }

        $dataAdmissao = substr($dataAdmissao, 0, 10);
        $admissao = new DateTime($dataAdmissao);
        $hoje = new DateTime();
        $diferenca = $admissao->diff($hoje);

        return sprintf(
            "%d ano%s, %d mês%s e %d dia%s",
            $diferenca->y,
            $diferenca->y != 1 ? "s" : "",
            $diferenca->m,
            $diferenca->m != 1 ? "es" : "",
            $diferenca->d,
            $diferenca->d != 1 ? "s" : ""
        );
    }

}