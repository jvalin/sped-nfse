<?php

namespace NFePHP\NFSe\Models\Issnet;

/**
 * Classe para a comunicação com os webservices da
 * conforme o modelo ISSNET
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Models\Issnet\Tools
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

use NFePHP\NFSe\Models\Issnet\Factories\CancelarNfseEnvio;
use NFePHP\NFSe\Models\Issnet\Factories\ConsultarNfseEnvio;
use NFePHP\NFSe\Common\Tools as ToolsBase;
use NFePHP\Common\Soap\SoapCurl;

class Tools extends ToolsBase
{
    /**
     * @param $numeroNFSe
     *
     * @return string
     */
    public function cancelamentoNFSe(
        $numeroNFSe
    ) {
	    $this->method = 'CancelarNfseEnvio';
	    $fact = new CancelarNfseEnvio($this->certificate);
	    $fact->setSignAlgorithm($this->algorithm);
	    $message = $fact->render(
            $this->versao,
		    $this->remetenteTipoDoc,
		    $this->remetenteCNPJCPF,
		    $this->inscricaoMunicipal,
            $this->codigoMunicipio,
            $numeroNFSe
	    );
        return $message;
//	    return $this->sendRequest('', $message);

    }
    
    public function consultaNFSePorRPS()
    {
    }
    
    public function consultarLoteRps()
    {
    }

    /**
     * @param $cnpjPrestador
     * @param $cpfPrestador
     * @param $imPrestador
     * @param $dtInicio
     * @param $dtFim
     * @param $pagina
     *
     * @return string
     */
    public function consultaNFSeEmitidas(
        $dtInicio,
        $dtFim,
        $cnpjPrestador = '',
        $cpfPrestador = '',
        $imPrestador = '',
        $pagina = ''
    ) {
        $this->method = 'ConsultarNFseEnvio';
        $fact = new ConsultarNFSeEnvio($this->certificate);
        $fact->setSignAlgorithm($this->algorithm);
        $message = $fact->render(
            $this->remetenteTipoDoc,
            $this->remetenteCNPJCPF,
            $this->inscricaoMunicipal,
            $dtInicio,
            $dtFim
        );
        return $this->sendRequest('', $message);
    }
    
    public function consultarNFSePorRPS()
    {
    }
    
    public function consultarSituacaoLoteRPS()
    {
    }
    
    public function consultarUrlVisualizacaoNfse()
    {
    }
    
    public function consultarUrlVisualizacaoNfseSerie()
    {
    }
    
    public function consultaSituacaoLoteRPS()
    {
    }
    
    public function recepcionarLoteRps()
    {
    }
    
    protected function sendRequest($url, $message)
    {
        //no caso do ISSNET o URL é unico para todas as ações
        $url = $this->url[$this->config->tpAmb];
        if (!is_object($this->soap)) {
            $this->soap = new SoapCurl($this->certificate);
        }
        $messageText = $message;
        if ($this->withcdata) {
            $messageText = $this->stringTransform("<?xml version=\"1.0\" encoding=\"UTF-8\"?>".$message);
        }
        $request = "<". $this->method . " xmlns=\"".$this->xmlns."\">"
            . "<xml>$messageText</xml>"
            . "</". $this->method . ">";
        
        $params = [
            'xml' => $message
        ];
        $action = "\"". $this->xmlns ."/". $this->method ."\"";
        return $this->soap->send(
            $url,
            $this->method,
            $action,
            $this->soapversion,
            $params,
            $this->namespaces[$this->soapversion],
            $request
        );
    }
}
