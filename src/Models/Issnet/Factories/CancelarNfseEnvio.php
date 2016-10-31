<?php

namespace NFePHP\NFSe\Models\Issnet\Factories;

use NFePHP\NFSe\Models\Issnet\Factories\Factory;

class CancelarNfseEnvio extends Factory
{
    public function render(
        $versao,
        $remetenteTipoDoc,
        $remetenteCNPJCPF,
        $prestadorIM,
        $codigoMunicipio,
        $numeroNFSe
    ){
        $method = "CancelarNfseEnvio";
        $xsd = 'servico_cancelar_nfse_envio';
        $content = $this->requestFirstPart($method, $xsd);
        $content .= "<Pedido>";
        $content .= "<tc:InfPedidoCancelamento>";
        if (is_array($numeroNFSe)) {
            if (count($numeroNFSe) > 50) {
                throw InvalidArgumentException("No máximo pode ser solicitado o cancelamento de 50 NFSe por vez.");
            }
            foreach ($numeroNFSe as $num) {
                $content .= $this->detalhe($prestadorIM, $num, $remetenteTipoDoc, $remetenteCNPJCPF, $codigoMunicipio);
            }
        } else {
            $content .= $this->detalhe($prestadorIM, $numeroNFSe, $remetenteTipoDoc, $remetenteCNPJCPF, $codigoMunicipio);
        }
        $content .= "</tc:InfPedidoCancelamento>";
        $content .= "</Pedido>";
        $content .= "</$method>";
        $content = $this->signer($content, $method, '', [false,false,null,null]);
        $body = $this->clear($content);
        print_r($content);
        $this->validar($versao, $body, 'Issnet', $xsd, '');
        return $body;
    }

    private function detalhe($prestadorIM, $numeroNFSe, $remetenteTipoDoc, $remetenteCNPJCPF, $codigoMunicipio)
    {
        $detalhe = "<tc:IdentificacaoNfse>";
        $detalhe .= $this->check("tc:Numero",$numeroNFSe);
        if ($remetenteTipoDoc == 2) {
            $detalhe .= $this->check("tc:Cnpj",$remetenteCNPJCPF);
        } else {
            $detalhe .= $this->check("tc:Cpf",$remetenteCNPJCPF);
        }
        $detalhe .= $this->check("tc:InscricaoMunicipal",$prestadorIM);
        $detalhe .= $this->check("tc:CodigoMunicipio",$codigoMunicipio);
        $detalhe .= "</tc:IdentificacaoNfse>";
        return $detalhe;
    }
}
