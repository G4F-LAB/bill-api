<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FileNaming;

class FileNamingSeeder extends Seeder
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
                "file_name" => "13º - Folha de pagamento",
                "standard_file_naming" => "FolhaDePagamento13"
            ],
            [
                "file_name" => "Admissão - ASO admissional",
                "standard_file_naming" => "Admissão_ASOAdmissional"
            ],
            [
                "file_name" => "Admissão - Comprovação cumprimento dos requisitos para a vaga",
                "standard_file_naming" => "Admissão_ComprovaçãoCumprimentoDosRequisitosParaVaga"
            ],
            [
                "file_name" => "Admissão - Comprovante de escolaridade",
                "standard_file_naming" => "Admissão_ComprovanteDeEscolaridade"
            ],
            [
                "file_name" => "Admissão - Contrato Assinado",
                "standard_file_naming" => "Admissão_ContratoAssinado"
            ],
            [
                "file_name" => "Admissão - Cópia do CPF",
                "standard_file_naming" => "Admissões_CPF"
            ],
            [
                "file_name" => "Admissão - Cópia do RG",
                "standard_file_naming" => "Admissões_RG"
            ],
            [
                "file_name" => "Admissão - Declaração de parentesco",
                "standard_file_naming" => "Admissão_DeclaraçãoDeParentesco"
            ],
            [
                "file_name" => "Admissão - Esocial",
                "standard_file_naming" => "Admissão_Esocial"
            ],
            [
                "file_name" => "Admissão - Ficha Admissional",
                "standard_file_naming" => "Admissão_FichaAdmissional"
            ],
            [
                "file_name" => "Admissão - Ficha de registro",
                "standard_file_naming" => "Admissão_FichaDeRegistro"
            ],
            [
                "file_name" => "Admissão - Foto 3x4",
                "standard_file_naming" => "Admissões_Foto"
            ],
            [
                "file_name" => "Admissão - Termo de confidencialidade",
                "standard_file_naming" => "Admissão_TermoDeConfidencialidade"
            ],
            [
                "file_name" => "Admissão - Opção de VA",
                "standard_file_naming" => "Admissão_OpçãoDeVA"
            ],
            [
                "file_name" => "Admissão - Opção de VT",
                "standard_file_naming" => "Admissão_OpçãoDeVT"
            ],
            [
                "file_name" => "Admissão - Termo de adesão plano de saúde",
                "standard_file_naming" => "Admissão_TermoDeAdesãoPlanoDeSaúde"
            ],
            [
                "file_name" => "Admissão - PIS",
                "standard_file_naming" => "Admissão_PIS"
            ],
            [
                "file_name" => "ASO - Periódico ",
                "standard_file_naming" => "ASO_Periódico"
            ],
            [
                "file_name" => "Benefícios (Benefício Indireto) - Boleto",
                "standard_file_naming" => "BenefícioIndireto_Boleto"
            ],
            [
                "file_name" => "Benefícios (Benefício Indireto) - Nota Fiscal",
                "standard_file_naming" => "BenefícioIndireto_NotaFiscal"
            ],
            [
                "file_name" => "Benefícios (Benefício Indireto) - Relatório detalhado em PDF",
                "standard_file_naming" => "BenefícioIndireto_RelatórioDetalhadoemPDF"
            ],
            [
                "file_name" => "Benefícios (Plano de Saúde) -  Boleto",
                "standard_file_naming" => "PlanodeSaúde_Boleto"
            ],
            [
                "file_name" => "Benefícios (Plano de Saúde) -  Nota Fiscal ",
                "standard_file_naming" => "PlanodeSaúde_NotaFiscal"
            ],
            [
                "file_name" => "Benefícios (Plano de Saúde) -  Relação de Segurados ",
                "standard_file_naming" => "PlanodeSaúde_RelaçãoDeSegurados"
            ],
            [
                "file_name" => "Benefícios (Plano de Saúde) -  Termo de não optante",
                "standard_file_naming" => "PlanodeSaúde_TermoDeNãoOptante"
            ],
            [
                "file_name" => "Benefícios (Plano Odontológico) - Boleto",
                "standard_file_naming" => "PlanoOdontológico_Boleto"
            ],
            [
                "file_name" => "Benefícios (Plano Odontológico) - Nota Fiscal",
                "standard_file_naming" => "PlanoOdontológico_NotaFiscal"
            ],
            [
                "file_name" => "Benefícios (Plano Odontológico) - Relação de Segurados",
                "standard_file_naming" => "PlanoOdontológico_RelaçãoDeSegurados"
            ],
            [
                "file_name" => "Benefícios (Taxa Odontológica Sindicato) - Boleto Bancário",
                "standard_file_naming" => "TaxaOdontológicaSindicato_BoletoBancário"
            ],
            [
                "file_name" => "Benefícios (Taxa Odontológica Sindicato) - Relatório",
                "standard_file_naming" => "TaxaOdontológicaSindicato_Relatório"
            ],
            [
                "file_name" => "Benefícios (Vale Alimentação \/ Vale Refeição) -  Boleto ",
                "standard_file_naming" => "ValeAlimentação-ValeRefeição_Boleto"
            ],
            [
                "file_name" => "Benefícios (Vale Alimentação \/ Vale Refeição) - Nota Fiscal",
                "standard_file_naming" => "ValeAlimentação-ValeRefeição_NotaFiscal"
            ],
            [
                "file_name" => "Benefícios (Vale Alimentação \/ Vale Refeição) - Relatório detalhado em PDF",
                "standard_file_naming" => "ValeAlimentação-ValeRefeição_RelatórioDetalhadoemPDF"
            ],
            [
                "file_name" => "Benefícios (Vale Transporte) -  Boleto",
                "standard_file_naming" => "ValeTransporte_Boleto"
            ],
            [
                "file_name" => "Benefícios (Vale Transporte) -  Nota Fiscal ",
                "standard_file_naming" => "ValeTransporte_NotaFiscal"
            ],
            [
                "file_name" => "Benefícios (Vale Transporte) -  Termo de não optante",
                "standard_file_naming" => "ValeTransporte_TermoDeNãoOptante"
            ],
            [
                "file_name" => "Benefícios (Vale Transporte) - Relatório detalhado em PDF",
                "standard_file_naming" => "ValeTransporte_RelatóriodetalhadoemPDF"
            ],
            [
                "file_name" => "CAGED - Declaração de desobrigatoriedade",
                "standard_file_naming" => "G4F_CAGED_DeclaraçãoDeDesobrigatoriedade"
            ],
            [
                "file_name" => "CCT Atualizada",
                "standard_file_naming" => "CCTAtualizada"
            ],
            [
                "file_name" => "CIPA - Certificado",
                "standard_file_naming" => "CIPA_Certificado"
            ],
            [
                "file_name" => "Consulta Totalizador da Contribuição Previdenciária",
                "standard_file_naming" => "ConsultaTotalizadorDaContribuiçãoPrevidenciária"
            ],
            [
                "file_name" => "Contracheque",
                "standard_file_naming" => "Contracheque"
            ],
            [
                "file_name" => "DCTF WEB (INSS) -  DARF Contribuição Previdenciária",
                "standard_file_naming" => "G4F_DCTFWEB-INSS_DARFContribuiçãoPrevidenciária"
            ],
            [
                "file_name" => "DCTF WEB (INSS) -  DARF CPRB ",
                "standard_file_naming" => "G4F_DCTFWEB-INSS_DARFCPRB"
            ],
            [
                "file_name" => "DCTF WEB (INSS) -  Relatório de créditos",
                "standard_file_naming" => "G4F_DCTFWEB-INSS_RelatórioDeCréditos"
            ],
            [
                "file_name" => "DCTF WEB (INSS) -  Relatório de débitos",
                "standard_file_naming" => "G4F_DCTFWEB-INSS_RelatórioDeDébitos"
            ],
            [
                "file_name" => "DCTF WEB (INSS) -  Resumo de créditos",
                "standard_file_naming" => "G4F_DCTFWEB-INSS_ResumoDeCréditos"
            ],
            [
                "file_name" => "DCTF WEB (INSS) -  Resumo de débitos",
                "standard_file_naming" => "G4F_DCTFWEB-INSS_ResumoDeDébitos"
            ],
            [
                "file_name" => "DCTF WEB (INSS) - Declaração completa",
                "standard_file_naming" => "G4F_DCTFWEB-INSS_DeclaraçãoCompleta"
            ],
            [
                "file_name" => "DCTF WEB (INSS) - Recibo ",
                "standard_file_naming" => "G4F_DCTFWEB-INSS_Recibo"
            ],
            [
                "file_name" => "Férias - Aviso de férias assinado",
                "standard_file_naming" => "Férias_AvisoEReciboDeFériasAssinado"
            ],
            [
                "file_name" => "Férias - Recibo de Férias",
                "standard_file_naming" => "Férias_AvisoEReciboDeFériasAssinado"
            ],
            [
                "file_name" => "FGTS - Extrato individual",
                "standard_file_naming" => "FGTS_ExtratoIndividual"
            ],
            [
                "file_name" => "Folha de pagamento",
                "standard_file_naming" => "FolhaDePagamento"
            ],
            [
                "file_name" => "PCMSO",
                "standard_file_naming" => "PCMSO"
            ],
            [
                "file_name" => "PGR",
                "standard_file_naming" => "PGR"
            ],
            [
                "file_name" => "RAIS - DIRF - Relação Anual de Informações Sociais",
                "standard_file_naming" => "RAIS_DIRF_RelaçãoAnualDeInformaçõesSociais"
            ],
            [
                "file_name" => "Relação de ativos ",
                "standard_file_naming" => "RelaçãoDeAtivos"
            ],
            [
                "file_name" => "Relação de Ativos e inativos do contrato",
                "standard_file_naming" => "RelaçãoDeAtivosEInativosDoContrato"
            ],
            [
                "file_name" => "Relatório de Acidentes de Trabalho",
                "standard_file_naming" => "RelatórioDeAcidentesDeTrabalho"
            ],
            [
                "file_name" => "Relatório de autorização do desconto de empréstimo consignado",
                "standard_file_naming" => "RelatórioDeAutorizaçãoDoDescontoDeEmpréstimoConsignado"
            ],
            [
                "file_name" => "Rescisões -  ASO Demissional",
                "standard_file_naming" => "Rescisões_ASODemissional"
            ],
            [
                "file_name" => "Rescisões -  Aviso prévio",
                "standard_file_naming" => "Rescisões_AvisoPrévio"
            ],
            [
                "file_name" => "Rescisões -  Extrato de FGTS ",
                "standard_file_naming" => "Rescisões_ExtratoDeFGTS"
            ],
            [
                "file_name" => "Rescisões -  Guia GRRF",
                "standard_file_naming" => "Rescisões_GuiaGRRF"
            ],
            [
                "file_name" => "Rescisões -  Relatório Guia GRRF ",
                "standard_file_naming" => "Rescisões_RelatórioGuiaGRRF"
            ],
            [
                "file_name" => "Rescisões - (PPP) Perfil Profissiográfico Previdenciário",
                "standard_file_naming" => "Rescisões_PPP"
            ],
            [
                "file_name" => "Rescisões - Seguro Desemprego",
                "standard_file_naming" => "Rescisões_SeguroDesemprego"
            ],
            [
                "file_name" => "Rescisões - Termo de rescisão assinado e homologado",
                "standard_file_naming" => "Rescisões_TRCT"
            ],
            [
                "file_name" => "RPA Assinada - (Se houver)",
                "standard_file_naming" => "RPAAssinada"
            ],
            [
                "file_name" => "SEFIP (FGTS) - Demonstrativo das contribuições devidas à previdência social e a outras entidades por FPAS",
                "standard_file_naming" => "SEFIP-FGTS_DemonstrativoDasContribuiçõesDevidasAPrevidênciaSocialEOutrasEntidadesPorFPAS"
            ],
            [
                "file_name" => "SEFIP (FGTS) - Guia de Recolhimento do FGTS",
                "standard_file_naming" => "SEFIP-FGTS_GuiadeRecolhimentoDoFGTS"
            ],
            [
                "file_name" => "SEFIP (FGTS) - Protocolo de envio",
                "standard_file_naming" => "SEFIP-FGTS_ProtocoloDeEnvio"
            ],
            [
                "file_name" => "SEFIP (FGTS) - Relação de Tomador Obra RET  - individualizado",
                "standard_file_naming" => "SEFIP-FGTS_RelaçãoDeTomadorObraRETIndividualizado"
            ],
            [
                "file_name" => "SEFIP (FGTS) - Relação de Tomador Obra RET - resumo",
                "standard_file_naming" => "SEFIP-FGTS_RelaçãoDeTomadorObraRETResumo"
            ],
            [
                "file_name" => "SEFIP (FGTS) - Relatório Analítico da GPS",
                "standard_file_naming" => "SEFIP-FGTS_RelatórioAnalíticoDaGPS"
            ],
            [
                "file_name" => "SEFIP (FGTS) - Relatório Analítico da GRF",
                "standard_file_naming" => "SEFIP-FGTS_RelatórioAnalíticoDaGRF"
            ],
            [
                "file_name" => "SEFIP (FGTS) - Relatório RE - Individualizado ",
                "standard_file_naming" => "SEFIP-FGTS_RelatórioREIndividualizado"
            ],
            [
                "file_name" => "SEFIP (FGTS) - Relatório RE - resumo",
                "standard_file_naming" => "SEFIP-FGTS_RelatórioREResumo"
            ],
            [
                "file_name" => "Seguro de Vida - Apólice",
                "standard_file_naming" => "G4F_SeguroDeVida_Apólice"
            ],
            [
                "file_name" => "Seguro de Vida - Boleto Bancário",
                "standard_file_naming" => "G4F_SeguroDeVida_Boleto"
            ],
            [
                "file_name" => "Seguro de Vida - Relação de Segurados",
                "standard_file_naming" => "G4F_SeguroDeVida_RelaçãoDeSegurados"
            ],
            [
                "file_name" => "INSS Relação de Salário de Contribuição",
                "standard_file_naming" => "INSS_RelaçãodeSaláriodeContribuição"
            ],
            [
                "file_name" => "eSocial Empregador – Totalizador Contribuição Previdenciária",
                "standard_file_naming" => "eSocial_Empregador_TotalizadorContribuiçãoPrevidenciária"
            ],
            [
                "file_name" => "eSocial Empregador – Totalizador FGTS",
                "standard_file_naming" => "eSocial_Empregador_TotalizadorFGTS"
            ],
            [
                "file_name" => "eSocial Empregador – Totalizador Imposto de Renda",
                "standard_file_naming" => "eSocial_Empregador_TotalizadorImpostodeRenda"
            ],
            [
                "file_name" => "eSocial Empregado – Totalizador Contribuição Previdenciária",
                "standard_file_naming" => "eSocial_Empregado _TotalizadorContribuiçãoPrevidenciária"
            ],
            [
                "file_name" => "eSocial Empregado – Totalizador FGTS",
                "standard_file_naming" => "eSocial_Empregado_TotalizadorFGTS"
            ],
            [
                "file_name" => "eSocial Empregado – Totalizador Imposto de Renda",
                "standard_file_naming" => "eSocial_Empregado_TotalizadorImpostodeRenda"
            ],
            [
                "file_name" => "eSocial Empregado – Demissão",
                "standard_file_naming" => "eSocial_Empregado_Demissão"
            ],
            [
                "file_name" => "eSocial Empregado – Remuneração Devida",
                "standard_file_naming" => "eSocial_Empregado_RemuneraçãoDevida"
            ]

            // Adicione mais dados conforme necessário
        ];

        // Popula a tabela com os dados
        foreach ($dados as $dado) {
            FileNaming::create($dado);
        }
    }
}
