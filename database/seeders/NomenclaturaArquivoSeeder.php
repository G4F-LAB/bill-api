<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Nomenclature;

class NomenclaturaArquivoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Dados para popular a tabela
        $dados = [
            [
                "nome_arquivo" => "13º - Folha de pagamento",
                "nomeclatura_padrao_arquivo" => "FolhaDePagamento13"
            ],
            [
                "nome_arquivo" => "Admissão - ASO admissional",
                "nomeclatura_padrao_arquivo" => "Admissão_ASOAdmissional"
            ],
            [
                "nome_arquivo" => "Admissão - Comprovação cumprimento dos requisitos para a vaga",
                "nomeclatura_padrao_arquivo" => "Admissão_ComprovaçãoCumprimentoDosRequisitosParaVaga"
            ],
            [
                "nome_arquivo" => "Admissão - Comprovante de escolaridade",
                "nomeclatura_padrao_arquivo" => "Admissão_ComprovanteDeEscolaridade"
            ],
            [
                "nome_arquivo" => "Admissão - Contrato Assinado",
                "nomeclatura_padrao_arquivo" => "Admissão_ContratoAssinado"
            ],
            [
                "nome_arquivo" => "Admissão - Cópia do CPF",
                "nomeclatura_padrao_arquivo" => "Admissões_CPF"
            ],
            [
                "nome_arquivo" => "Admissão - Cópia do RG",
                "nomeclatura_padrao_arquivo" => "Admissões_RG"
            ],
            [
                "nome_arquivo" => "Admissão - Declaração de parentesco",
                "nomeclatura_padrao_arquivo" => "Admissão_DeclaraçãoDeParentesco"
            ],
            [
                "nome_arquivo" => "Admissão - Esocial",
                "nomeclatura_padrao_arquivo" => "Admissão_Esocial"
            ],
            [
                "nome_arquivo" => "Admissão - Ficha Admissional",
                "nomeclatura_padrao_arquivo" => "Admissão_FichaAdmissional"
            ],
            [
                "nome_arquivo" => "Admissão - Ficha de registro",
                "nomeclatura_padrao_arquivo" => "Admissão_FichaDeRegistro"
            ],
            [
                "nome_arquivo" => "Admissão - Foto 3x4",
                "nomeclatura_padrao_arquivo" => "Admissões_Foto"
            ],
            [
                "nome_arquivo" => "Admissão - Termo de confidencialidade",
                "nomeclatura_padrao_arquivo" => "Admissão_TermoDeConfidencialidade"
            ],
            [
                "nome_arquivo" => "Admissão - Opção de VA",
                "nomeclatura_padrao_arquivo" => "Admissão_OpçãoDeVA"
            ],
            [
                "nome_arquivo" => "Admissão - Opção de VT",
                "nomeclatura_padrao_arquivo" => "Admissão_OpçãoDeVT"
            ],
            [
                "nome_arquivo" => "Admissão - Termo de adesão plano de saúde",
                "nomeclatura_padrao_arquivo" => "Admissão_TermoDeAdesãoPlanoDeSaúde"
            ],
            [
                "nome_arquivo" => "Admissão - PIS",
                "nomeclatura_padrao_arquivo" => "Admissão_PIS"
            ],
            [
                "nome_arquivo" => "ASO - Periódico ",
                "nomeclatura_padrao_arquivo" => "ASO_Periódico"
            ],
            [
                "nome_arquivo" => "Benefícios (Benefício Indireto) - Boleto",
                "nomeclatura_padrao_arquivo" => "BenefícioIndireto_Boleto"
            ],
            [
                "nome_arquivo" => "Benefícios (Benefício Indireto) - Nota Fiscal",
                "nomeclatura_padrao_arquivo" => "BenefícioIndireto_NotaFiscal"
            ],
            [
                "nome_arquivo" => "Benefícios (Benefício Indireto) - Relatório detalhado em PDF",
                "nomeclatura_padrao_arquivo" => "BenefícioIndireto_RelatórioDetalhadoemPDF"
            ],
            [
                "nome_arquivo" => "Benefícios (Plano de Saúde) -  Boleto",
                "nomeclatura_padrao_arquivo" => "PlanodeSaúde_Boleto"
            ],
            [
                "nome_arquivo" => "Benefícios (Plano de Saúde) -  Nota Fiscal ",
                "nomeclatura_padrao_arquivo" => "PlanodeSaúde_NotaFiscal"
            ],
            [
                "nome_arquivo" => "Benefícios (Plano de Saúde) -  Relação de Segurados ",
                "nomeclatura_padrao_arquivo" => "PlanodeSaúde_RelaçãoDeSegurados"
            ],
            [
                "nome_arquivo" => "Benefícios (Plano de Saúde) -  Termo de não optante",
                "nomeclatura_padrao_arquivo" => "PlanodeSaúde_TermoDeNãoOptante"
            ],
            [
                "nome_arquivo" => "Benefícios (Plano Odontológico) - Boleto",
                "nomeclatura_padrao_arquivo" => "PlanoOdontológico_Boleto"
            ],
            [
                "nome_arquivo" => "Benefícios (Plano Odontológico) - Nota Fiscal",
                "nomeclatura_padrao_arquivo" => "PlanoOdontológico_NotaFiscal"
            ],
            [
                "nome_arquivo" => "Benefícios (Plano Odontológico) - Relação de Segurados",
                "nomeclatura_padrao_arquivo" => "PlanoOdontológico_RelaçãoDeSegurados"
            ],
            [
                "nome_arquivo" => "Benefícios (Taxa Odontológica Sindicato) - Boleto Bancário",
                "nomeclatura_padrao_arquivo" => "TaxaOdontológicaSindicato_BoletoBancário"
            ],
            [
                "nome_arquivo" => "Benefícios (Taxa Odontológica Sindicato) - Relatório",
                "nomeclatura_padrao_arquivo" => "TaxaOdontológicaSindicato_Relatório"
            ],
            [
                "nome_arquivo" => "Benefícios (Vale Alimentação \/ Vale Refeição) -  Boleto ",
                "nomeclatura_padrao_arquivo" => "ValeAlimentação-ValeRefeição_Boleto"
            ],
            [
                "nome_arquivo" => "Benefícios (Vale Alimentação \/ Vale Refeição) - Nota Fiscal",
                "nomeclatura_padrao_arquivo" => "ValeAlimentação-ValeRefeição_NotaFiscal"
            ],
            [
                "nome_arquivo" => "Benefícios (Vale Alimentação \/ Vale Refeição) - Relatório detalhado em PDF",
                "nomeclatura_padrao_arquivo" => "ValeAlimentação-ValeRefeição_RelatórioDetalhadoemPDF"
            ],
            [
                "nome_arquivo" => "Benefícios (Vale Transporte) -  Boleto",
                "nomeclatura_padrao_arquivo" => "ValeTransporte_Boleto"
            ],
            [
                "nome_arquivo" => "Benefícios (Vale Transporte) -  Nota Fiscal ",
                "nomeclatura_padrao_arquivo" => "ValeTransporte_NotaFiscal"
            ],
            [
                "nome_arquivo" => "Benefícios (Vale Transporte) -  Termo de não optante",
                "nomeclatura_padrao_arquivo" => "ValeTransporte_TermoDeNãoOptante"
            ],
            [
                "nome_arquivo" => "Benefícios (Vale Transporte) - Relatório detalhado em PDF",
                "nomeclatura_padrao_arquivo" => "ValeTransporte_RelatóriodetalhadoemPDF"
            ],
            [
                "nome_arquivo" => "CAGED - Declaração de desobrigatoriedade",
                "nomeclatura_padrao_arquivo" => "G4F_CAGED_DeclaraçãoDeDesobrigatoriedade"
            ],
            [
                "nome_arquivo" => "CCT Atualizada",
                "nomeclatura_padrao_arquivo" => "CCTAtualizada"
            ],
            [
                "nome_arquivo" => "CIPA - Certificado",
                "nomeclatura_padrao_arquivo" => "CIPA_Certificado"
            ],
            [
                "nome_arquivo" => "Consulta Totalizador da Contribuição Previdenciária",
                "nomeclatura_padrao_arquivo" => "ConsultaTotalizadorDaContribuiçãoPrevidenciária"
            ],
            [
                "nome_arquivo" => "Contracheque",
                "nomeclatura_padrao_arquivo" => "Contracheque"
            ],
            [
                "nome_arquivo" => "DCTF WEB (INSS) -  DARF Contribuição Previdenciária",
                "nomeclatura_padrao_arquivo" => "G4F_DCTFWEB-INSS_DARFContribuiçãoPrevidenciária"
            ],
            [
                "nome_arquivo" => "DCTF WEB (INSS) -  DARF CPRB ",
                "nomeclatura_padrao_arquivo" => "G4F_DCTFWEB-INSS_DARFCPRB"
            ],
            [
                "nome_arquivo" => "DCTF WEB (INSS) -  Relatório de créditos",
                "nomeclatura_padrao_arquivo" => "G4F_DCTFWEB-INSS_RelatórioDeCréditos"
            ],
            [
                "nome_arquivo" => "DCTF WEB (INSS) -  Relatório de débitos",
                "nomeclatura_padrao_arquivo" => "G4F_DCTFWEB-INSS_RelatórioDeDébitos"
            ],
            [
                "nome_arquivo" => "DCTF WEB (INSS) -  Resumo de créditos",
                "nomeclatura_padrao_arquivo" => "G4F_DCTFWEB-INSS_ResumoDeCréditos"
            ],
            [
                "nome_arquivo" => "DCTF WEB (INSS) -  Resumo de débitos",
                "nomeclatura_padrao_arquivo" => "G4F_DCTFWEB-INSS_ResumoDeDébitos"
            ],
            [
                "nome_arquivo" => "DCTF WEB (INSS) - Declaração completa",
                "nomeclatura_padrao_arquivo" => "G4F_DCTFWEB-INSS_DeclaraçãoCompleta"
            ],
            [
                "nome_arquivo" => "DCTF WEB (INSS) - Recibo ",
                "nomeclatura_padrao_arquivo" => "G4F_DCTFWEB-INSS_Recibo"
            ],
            [
                "nome_arquivo" => "Férias - Aviso de férias assinado",
                "nomeclatura_padrao_arquivo" => "Férias_AvisoEReciboDeFériasAssinado"
            ],
            [
                "nome_arquivo" => "Férias - Recibo de Férias",
                "nomeclatura_padrao_arquivo" => "Férias_AvisoEReciboDeFériasAssinado"
            ],
            [
                "nome_arquivo" => "FGTS - Extrato individual",
                "nomeclatura_padrao_arquivo" => "FGTS_ExtratoIndividual"
            ],
            [
                "nome_arquivo" => "Folha de pagamento",
                "nomeclatura_padrao_arquivo" => "FolhaDePagamento"
            ],
            [
                "nome_arquivo" => "PCMSO",
                "nomeclatura_padrao_arquivo" => "PCMSO"
            ],
            [
                "nome_arquivo" => "PGR",
                "nomeclatura_padrao_arquivo" => "PGR"
            ],
            [
                "nome_arquivo" => "RAIS - DIRF - Relação Anual de Informações Sociais",
                "nomeclatura_padrao_arquivo" => "RAIS_DIRF_RelaçãoAnualDeInformaçõesSociais"
            ],
            [
                "nome_arquivo" => "Relação de ativos ",
                "nomeclatura_padrao_arquivo" => "RelaçãoDeAtivos"
            ],
            [
                "nome_arquivo" => "Relação de Ativos e inativos do contrato",
                "nomeclatura_padrao_arquivo" => "RelaçãoDeAtivosEInativosDoContrato"
            ],
            [
                "nome_arquivo" => "Relatório de Acidentes de Trabalho",
                "nomeclatura_padrao_arquivo" => "RelatórioDeAcidentesDeTrabalho"
            ],
            [
                "nome_arquivo" => "Relatório de autorização do desconto de empréstimo consignado",
                "nomeclatura_padrao_arquivo" => "RelatórioDeAutorizaçãoDoDescontoDeEmpréstimoConsignado"
            ],
            [
                "nome_arquivo" => "Rescisões -  ASO Demissional",
                "nomeclatura_padrao_arquivo" => "Rescisões_ASODemissional"
            ],
            [
                "nome_arquivo" => "Rescisões -  Aviso prévio",
                "nomeclatura_padrao_arquivo" => "Rescisões_AvisoPrévio"
            ],
            [
                "nome_arquivo" => "Rescisões -  Extrato de FGTS ",
                "nomeclatura_padrao_arquivo" => "Rescisões_ExtratoDeFGTS"
            ],
            [
                "nome_arquivo" => "Rescisões -  Guia GRRF",
                "nomeclatura_padrao_arquivo" => "Rescisões_GuiaGRRF"
            ],
            [
                "nome_arquivo" => "Rescisões -  Relatório Guia GRRF ",
                "nomeclatura_padrao_arquivo" => "Rescisões_RelatórioGuiaGRRF"
            ],
            [
                "nome_arquivo" => "Rescisões - (PPP) Perfil Profissiográfico Previdenciário",
                "nomeclatura_padrao_arquivo" => "Rescisões_PPP"
            ],
            [
                "nome_arquivo" => "Rescisões - Seguro Desemprego",
                "nomeclatura_padrao_arquivo" => "Rescisões_SeguroDesemprego"
            ],
            [
                "nome_arquivo" => "Rescisões - Termo de rescisão assinado e homologado",
                "nomeclatura_padrao_arquivo" => "Rescisões_TRCT"
            ],
            [
                "nome_arquivo" => "RPA Assinada - (Se houver)",
                "nomeclatura_padrao_arquivo" => "RPAAssinada"
            ],
            [
                "nome_arquivo" => "SEFIP (FGTS) - Demonstrativo das contribuições devidas à previdência social e a outras entidades por FPAS",
                "nomeclatura_padrao_arquivo" => "SEFIP-FGTS_DemonstrativoDasContribuiçõesDevidasAPrevidênciaSocialEOutrasEntidadesPorFPAS"
            ],
            [
                "nome_arquivo" => "SEFIP (FGTS) - Guia de Recolhimento do FGTS",
                "nomeclatura_padrao_arquivo" => "SEFIP-FGTS_GuiadeRecolhimentoDoFGTS"
            ],
            [
                "nome_arquivo" => "SEFIP (FGTS) - Protocolo de envio",
                "nomeclatura_padrao_arquivo" => "SEFIP-FGTS_ProtocoloDeEnvio"
            ],
            [
                "nome_arquivo" => "SEFIP (FGTS) - Relação de Tomador Obra RET  - individualizado",
                "nomeclatura_padrao_arquivo" => "SEFIP-FGTS_RelaçãoDeTomadorObraRETIndividualizado"
            ],
            [
                "nome_arquivo" => "SEFIP (FGTS) - Relação de Tomador Obra RET - resumo",
                "nomeclatura_padrao_arquivo" => "SEFIP-FGTS_RelaçãoDeTomadorObraRETResumo"
            ],
            [
                "nome_arquivo" => "SEFIP (FGTS) - Relatório Analítico da GPS",
                "nomeclatura_padrao_arquivo" => "SEFIP-FGTS_RelatórioAnalíticoDaGPS"
            ],
            [
                "nome_arquivo" => "SEFIP (FGTS) - Relatório Analítico da GRF",
                "nomeclatura_padrao_arquivo" => "SEFIP-FGTS_RelatórioAnalíticoDaGRF"
            ],
            [
                "nome_arquivo" => "SEFIP (FGTS) - Relatório RE - Individualizado ",
                "nomeclatura_padrao_arquivo" => "SEFIP-FGTS_RelatórioREIndividualizado"
            ],
            [
                "nome_arquivo" => "SEFIP (FGTS) - Relatório RE - resumo",
                "nomeclatura_padrao_arquivo" => "SEFIP-FGTS_RelatórioREResumo"
            ],
            [
                "nome_arquivo" => "Seguro de Vida - Apólice",
                "nomeclatura_padrao_arquivo" => "G4F_SeguroDeVida_Apólice"
            ],
            [
                "nome_arquivo" => "Seguro de Vida - Boleto Bancário",
                "nomeclatura_padrao_arquivo" => "G4F_SeguroDeVida_Boleto"
            ],
            [
                "nome_arquivo" => "Seguro de Vida - Relação de Segurados",
                "nomeclatura_padrao_arquivo" => "G4F_SeguroDeVida_RelaçãoDeSegurados"
            ],
            [
                "nome_arquivo" => "INSS Relação de Salário de Contribuição",
                "nomeclatura_padrao_arquivo" => "INSS_RelaçãodeSaláriodeContribuição"
            ],
            [
                "nome_arquivo" => "eSocial Empregador – Totalizador Contribuição Previdenciária",
                "nomeclatura_padrao_arquivo" => "eSocial_Empregador_TotalizadorContribuiçãoPrevidenciária"
            ],
            [
                "nome_arquivo" => "eSocial Empregador – Totalizador FGTS",
                "nomeclatura_padrao_arquivo" => "eSocial_Empregador_TotalizadorFGTS"
            ],
            [
                "nome_arquivo" => "eSocial Empregador – Totalizador Imposto de Renda",
                "nomeclatura_padrao_arquivo" => "eSocial_Empregador_TotalizadorImpostodeRenda"
            ],
            [
                "nome_arquivo" => "eSocial Empregado – Totalizador Contribuição Previdenciária",
                "nomeclatura_padrao_arquivo" => "eSocial_Empregado _TotalizadorContribuiçãoPrevidenciária"
            ],
            [
                "nome_arquivo" => "eSocial Empregado – Totalizador FGTS",
                "nomeclatura_padrao_arquivo" => "eSocial_Empregado_TotalizadorFGTS"
            ],
            [
                "nome_arquivo" => "eSocial Empregado – Totalizador Imposto de Renda",
                "nomeclatura_padrao_arquivo" => "eSocial_Empregado_TotalizadorImpostodeRenda"
            ],
            [
                "nome_arquivo" => "eSocial Empregado – Demissão",
                "nomeclatura_padrao_arquivo" => "eSocial_Empregado_Demissão"
            ],
            [
                "nome_arquivo" => "eSocial Empregado – Remuneração Devida",
                "nomeclatura_padrao_arquivo" => "eSocial_Empregado_RemuneraçãoDevida"
            ]

            // Adicione mais dados conforme necessário
        ];

        // Popula a tabela com os dados
        foreach ($dados as $dado) {
            Nomenclature::create($dado);
        }
    }
}
