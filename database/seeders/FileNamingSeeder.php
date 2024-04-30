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
                "standard_file_naming" => "FolhaDePagamento13",
                "file_type_id" => 1
            ],
            [
                "file_name" => "Admissão - ASO admissional",
                "standard_file_naming" => "Admissão_ASOAdmissional",
                "file_type_id" => 1
            ],
            [
                "file_name" => "Admissão - Comprovação cumprimento dos requisitos para a vaga",
                "standard_file_naming" => "Admissão_ComprovaçãoCumprimentoDosRequisitosParaVaga",
                "file_type_id" => 1
            ],
            [
                "file_name" => "Admissão - Comprovante de escolaridade",
                "standard_file_naming" => "Admissão_ComprovanteDeEscolaridade",
                "file_type_id" => 1
            ],
            [
                "file_name" => "Admissão - Contrato Assinado",
                "standard_file_naming" => "Admissão_ContratoAssinado",
                "file_type_id" => 1
            ],
            [
                "file_name" => "Admissão - Cópia do CPF",
                "standard_file_naming" => "Admissões_CPF",
                "file_type_id" => 1
            ],
            [
                "file_name" => "Admissão - Cópia do RG",
                "standard_file_naming" => "Admissões_RG",
                "file_type_id" => 1
            ],
            [
                "file_name" => "Admissão - Declaração de parentesco",
                "standard_file_naming" => "Admissão_DeclaraçãoDeParentesco",
                "file_type_id" => 1
            ],
            [
                "file_name" => "Admissão - Esocial",
                "standard_file_naming" => "Admissão_Esocial",
                "file_type_id" => 1
            ],
            [
                "file_name" => "Admissão - Ficha Admissional",
                "standard_file_naming" => "Admissão_FichaAdmissional",
                "file_type_id" => 1
            ],
            [
                "file_name" => "Admissão - Ficha de registro",
                "standard_file_naming" => "Admissão_FichaDeRegistro",
                "file_type_id" => 1
            ],
            [
                "file_name" => "Admissão - Foto 3x4",
                "standard_file_naming" => "Admissões_Foto",
                "file_type_id" => 1
            ],
            [
                "file_name" => "Admissão - Termo de confidencialidade",
                "standard_file_naming" => "Admissão_TermoDeConfidencialidade",
                "file_type_id" => 1
            ],
            [
                "file_name" => "Admissão - Opção de VA",
                "standard_file_naming" => "Admissão_OpçãoDeVA",
                "file_type_id" => 1
            ],
            [
                "file_name" => "Admissão - Opção de VT",
                "standard_file_naming" => "Admissão_OpçãoDeVT",
                "file_type_id" => 1
            ],
            [
                "file_name" => "Admissão - Termo de adesão plano de saúde",
                "standard_file_naming" => "Admissão_TermoDeAdesãoPlanoDeSaúde",
                "file_type_id" => 1
            ],
            [
                "file_name" => "Admissão - PIS",
                "standard_file_naming" => "Admissão_PIS",
                "file_type_id" => 1
            ],
            [
                "file_name" => "ASO - Periódico ",
                "standard_file_naming" => "ASO_Periódico",
                "file_type_id" => 1
            ],
            [
                "file_name" => "Benefícios (Benefício Indireto) - Boleto",
                "standard_file_naming" => "BenefícioIndireto_Boleto",
                "file_type_id" => 1
            ],
            [
                "file_name" => "Benefícios (Benefício Indireto) - Nota Fiscal",
                "standard_file_naming" => "BenefícioIndireto_NotaFiscal",
                "file_type_id" => 1
            ],
            [
                "file_name" => "Benefícios (Benefício Indireto) - Relatório detalhado em PDF",
                "standard_file_naming" => "BenefícioIndireto_RelatórioDetalhadoemPDF",
                "file_type_id" => 1
            ],
            [
                "file_name" => "Benefícios (Plano de Saúde) -  Boleto",
                "standard_file_naming" => "PlanodeSaúde_Boleto",
                "file_type_id" => 1
            ],
            [
                "file_name" => "Benefícios (Plano de Saúde) -  Nota Fiscal ",
                "standard_file_naming" => "PlanodeSaúde_NotaFiscal",
                "file_type_id" => 1
            ],
            [
                "file_name" => "Benefícios (Plano de Saúde) -  Relação de Segurados ",
                "standard_file_naming" => "PlanodeSaúde_RelaçãoDeSegurados",
                "file_type_id" => 1
            ],
            [
                "file_name" => "Benefícios (Plano de Saúde) -  Termo de não optante",
                "standard_file_naming" => "PlanodeSaúde_TermoDeNãoOptante",
                "file_type_id" => 1
            ],
            [
                "file_name" => "Benefícios (Plano Odontológico) - Boleto",
                "standard_file_naming" => "PlanoOdontológico_Boleto",
                "file_type_id" => 1
            ],
            [
                "file_name" => "Benefícios (Plano Odontológico) - Nota Fiscal",
                "standard_file_naming" => "PlanoOdontológico_NotaFiscal",
                "file_type_id" => 1
            ],
            [
                "file_name" => "Benefícios (Plano Odontológico) - Relação de Segurados",
                "standard_file_naming" => "PlanoOdontológico_RelaçãoDeSegurados",
                "file_type_id" => 1
            ],
            [
                "file_name" => "Benefícios (Taxa Odontológica Sindicato) - Boleto Bancário",
                "standard_file_naming" => "TaxaOdontológicaSindicato_BoletoBancário",
                "file_type_id" => 1
            ],
            [
                "file_name" => "Benefícios (Taxa Odontológica Sindicato) - Relatório",
                "standard_file_naming" => "TaxaOdontológicaSindicato_Relatório",
                "file_type_id" => 1
            ],
            [
                "file_name" => "Benefícios (Vale Alimentação \/ Vale Refeição) -  Boleto ",
                "standard_file_naming" => "ValeAlimentação-ValeRefeição_Boleto",
                "file_type_id" => 1
            ],
            [
                "file_name" => "Benefícios (Vale Alimentação \/ Vale Refeição) - Nota Fiscal",
                "standard_file_naming" => "ValeAlimentação-ValeRefeição_NotaFiscal",
                "file_type_id" => 1
            ],
            [
                "file_name" => "Benefícios (Vale Alimentação \/ Vale Refeição) - Relatório detalhado em PDF",
                "standard_file_naming" => "ValeAlimentação-ValeRefeição_RelatórioDetalhadoemPDF",
                "file_type_id" => 1
            ],
            [
                "file_name" => "Benefícios (Vale Transporte) -  Boleto",
                "standard_file_naming" => "ValeTransporte_Boleto",
                "file_type_id" => 1
            ],
            [
                "file_name" => "Benefícios (Vale Transporte) -  Nota Fiscal ",
                "standard_file_naming" => "ValeTransporte_NotaFiscal",
                "file_type_id" => 1
            ],
            [
                "file_name" => "Benefícios (Vale Transporte) -  Termo de não optante",
                "standard_file_naming" => "ValeTransporte_TermoDeNãoOptante",
                "file_type_id" => 1
            ],
            [
                "file_name" => "Benefícios (Vale Transporte) - Relatório detalhado em PDF",
                "standard_file_naming" => "ValeTransporte_RelatóriodetalhadoemPDF",
                "file_type_id" => 1
            ],
            [
                "file_name" => "CAGED - Declaração de desobrigatoriedade",
                "standard_file_naming" => "G4F_CAGED_DeclaraçãoDeDesobrigatoriedade",
                "file_type_id" => 1
            ],
            [
                "file_name" => "CCT Atualizada",
                "standard_file_naming" => "CCTAtualizada",
                "file_type_id" => 1
            ],
            [
                "file_name" => "CIPA - Certificado",
                "standard_file_naming" => "CIPA_Certificado",
                "file_type_id" => 1
            ],
            [
                "file_name" => "Consulta Totalizador da Contribuição Previdenciária",
                "standard_file_naming" => "ConsultaTotalizadorDaContribuiçãoPrevidenciária",
                "file_type_id" => 1
            ],
            [
                "file_name" => "Contracheque",
                "standard_file_naming" => "Contracheque",
                "file_type_id" => 1
            ],
            [
                "file_name" => "DCTF WEB (INSS) -  DARF Contribuição Previdenciária",
                "standard_file_naming" => "G4F_DCTFWEB-INSS_DARFContribuiçãoPrevidenciária",
                "file_type_id" => 1
            ],
            [
                "file_name" => "DCTF WEB (INSS) -  DARF CPRB ",
                "standard_file_naming" => "G4F_DCTFWEB-INSS_DARFCPRB",
                "file_type_id" => 1
            ],
            [
                "file_name" => "DCTF WEB (INSS) -  Relatório de créditos",
                "standard_file_naming" => "G4F_DCTFWEB-INSS_RelatórioDeCréditos",
                "file_type_id" => 1
            ],
            [
                "file_name" => "DCTF WEB (INSS) -  Relatório de débitos",
                "standard_file_naming" => "G4F_DCTFWEB-INSS_RelatórioDeDébitos",
                "file_type_id" => 1
            ],
            [
                "file_name" => "DCTF WEB (INSS) -  Resumo de créditos",
                "standard_file_naming" => "G4F_DCTFWEB-INSS_ResumoDeCréditos",
                "file_type_id" => 1
            ],
            [
                "file_name" => "DCTF WEB (INSS) -  Resumo de débitos",
                "standard_file_naming" => "G4F_DCTFWEB-INSS_ResumoDeDébitos",
                "file_type_id" => 1
            ],
            [
                "file_name" => "DCTF WEB (INSS) - Declaração completa",
                "standard_file_naming" => "G4F_DCTFWEB-INSS_DeclaraçãoCompleta",
                "file_type_id" => 1
            ],
            [
                "file_name" => "DCTF WEB (INSS) - Recibo ",
                "standard_file_naming" => "G4F_DCTFWEB-INSS_Recibo",
                "file_type_id" => 1
            ],
            [
                "file_name" => "Férias - Aviso de férias assinado",
                "standard_file_naming" => "Férias_AvisoEReciboDeFériasAssinado",
                "file_type_id" => 1
            ],
            [
                "file_name" => "Férias - Recibo de Férias",
                "standard_file_naming" => "Férias_AvisoEReciboDeFériasAssinado",
                "file_type_id" => 1
            ],
            [
                "file_name" => "FGTS - Extrato individual",
                "standard_file_naming" => "FGTS_ExtratoIndividual",
                "file_type_id" => 1
            ],
            [
                "file_name" => "Folha de pagamento",
                "standard_file_naming" => "FolhaDePagamento",
                "file_type_id" => 1
            ],
            [
                "file_name" => "PCMSO",
                "standard_file_naming" => "PCMSO",
                "file_type_id" => 1
            ],
            [
                "file_name" => "PGR",
                "standard_file_naming" => "PGR",
                "file_type_id" => 1
            ],
            [
                "file_name" => "RAIS - DIRF - Relação Anual de Informações Sociais",
                "standard_file_naming" => "RAIS_DIRF_RelaçãoAnualDeInformaçõesSociais",
                "file_type_id" => 1
            ],
            [
                "file_name" => "Relação de ativos ",
                "standard_file_naming" => "RelaçãoDeAtivos",
                "file_type_id" => 1
            ],
            [
                "file_name" => "Relação de Ativos e inativos do contrato",
                "standard_file_naming" => "RelaçãoDeAtivosEInativosDoContrato",
                "file_type_id" => 1
            ],
            [
                "file_name" => "Relatório de Acidentes de Trabalho",
                "standard_file_naming" => "RelatórioDeAcidentesDeTrabalho",
                "file_type_id" => 1
            ],
            [
                "file_name" => "Relatório de autorização do desconto de empréstimo consignado",
                "standard_file_naming" => "RelatórioDeAutorizaçãoDoDescontoDeEmpréstimoConsignado",
                "file_type_id" => 1
            ],
            [
                "file_name" => "Rescisões -  ASO Demissional",
                "standard_file_naming" => "Rescisões_ASODemissional",
                "file_type_id" => 1
            ],
            [
                "file_name" => "Rescisões -  Aviso prévio",
                "standard_file_naming" => "Rescisões_AvisoPrévio",
                "file_type_id" => 1
            ],
            [
                "file_name" => "Rescisões -  Extrato de FGTS ",
                "standard_file_naming" => "Rescisões_ExtratoDeFGTS",
                "file_type_id" => 1
            ],
            [
                "file_name" => "Rescisões -  Guia GRRF",
                "standard_file_naming" => "Rescisões_GuiaGRRF",
                "file_type_id" => 1
            ],
            [
                "file_name" => "Rescisões -  Relatório Guia GRRF ",
                "standard_file_naming" => "Rescisões_RelatórioGuiaGRRF",
                "file_type_id" => 1
            ],
            [
                "file_name" => "Rescisões - (PPP) Perfil Profissiográfico Previdenciário",
                "standard_file_naming" => "Rescisões_PPP",
                "file_type_id" => 1
            ],
            [
                "file_name" => "Rescisões - Seguro Desemprego",
                "standard_file_naming" => "Rescisões_SeguroDesemprego",
                "file_type_id" => 1
            ],
            [
                "file_name" => "Rescisões - Termo de rescisão assinado e homologado",
                "standard_file_naming" => "Rescisões_TRCT",
                "file_type_id" => 1
            ],
            [
                "file_name" => "RPA Assinada - (Se houver)",
                "standard_file_naming" => "RPAAssinada",
                "file_type_id" => 1
            ],
            [
                "file_name" => "SEFIP (FGTS) - Demonstrativo das contribuições devidas à previdência social e a outras entidades por FPAS",
                "standard_file_naming" => "SEFIP-FGTS_DemonstrativoDasContribuiçõesDevidasAPrevidênciaSocialEOutrasEntidadesPorFPAS",
                "file_type_id" => 1
            ],
            [
                "file_name" => "SEFIP (FGTS) - Guia de Recolhimento do FGTS",
                "standard_file_naming" => "SEFIP-FGTS_GuiadeRecolhimentoDoFGTS",
                "file_type_id" => 1
            ],
            [
                "file_name" => "SEFIP (FGTS) - Protocolo de envio",
                "standard_file_naming" => "SEFIP-FGTS_ProtocoloDeEnvio",
                "file_type_id" => 1
            ],
            [
                "file_name" => "SEFIP (FGTS) - Relação de Tomador Obra RET  - individualizado",
                "standard_file_naming" => "SEFIP-FGTS_RelaçãoDeTomadorObraRETIndividualizado",
                "file_type_id" => 1
            ],
            [
                "file_name" => "SEFIP (FGTS) - Relação de Tomador Obra RET - resumo",
                "standard_file_naming" => "SEFIP-FGTS_RelaçãoDeTomadorObraRETResumo",
                "file_type_id" => 1
            ],
            [
                "file_name" => "SEFIP (FGTS) - Relatório Analítico da GPS",
                "standard_file_naming" => "SEFIP-FGTS_RelatórioAnalíticoDaGPS",
                "file_type_id" => 1
            ],
            [
                "file_name" => "SEFIP (FGTS) - Relatório Analítico da GRF",
                "standard_file_naming" => "SEFIP-FGTS_RelatórioAnalíticoDaGRF",
                "file_type_id" => 1
            ],
            [
                "file_name" => "SEFIP (FGTS) - Relatório RE - Individualizado ",
                "standard_file_naming" => "SEFIP-FGTS_RelatórioREIndividualizado",
                "file_type_id" => 1
            ],
            [
                "file_name" => "SEFIP (FGTS) - Relatório RE - resumo",
                "standard_file_naming" => "SEFIP-FGTS_RelatórioREResumo",
                "file_type_id" => 1
            ],
            [
                "file_name" => "Seguro de Vida - Apólice",
                "standard_file_naming" => "G4F_SeguroDeVida_Apólice",
                "file_type_id" => 1
            ],
            [
                "file_name" => "Seguro de Vida - Boleto Bancário",
                "standard_file_naming" => "G4F_SeguroDeVida_Boleto",
                "file_type_id" => 1
            ],
            [
                "file_name" => "Seguro de Vida - Relação de Segurados",
                "standard_file_naming" => "G4F_SeguroDeVida_RelaçãoDeSegurados",
                "file_type_id" => 1
            ],
            [
                "file_name" => "INSS Relação de Salário de Contribuição",
                "standard_file_naming" => "INSS_RelaçãodeSaláriodeContribuição",
                "file_type_id" => 1
            ],
            [
                "file_name" => "eSocial Empregador – Totalizador Contribuição Previdenciária",
                "standard_file_naming" => "eSocial_Empregador_TotalizadorContribuiçãoPrevidenciária",
                "file_type_id" => 1
            ],
            [
                "file_name" => "eSocial Empregador – Totalizador FGTS",
                "standard_file_naming" => "eSocial_Empregador_TotalizadorFGTS",
                "file_type_id" => 1
            ],
            [
                "file_name" => "eSocial Empregador – Totalizador Imposto de Renda",
                "standard_file_naming" => "eSocial_Empregador_TotalizadorImpostodeRenda",
                "file_type_id" => 1
            ],
            [
                "file_name" => "eSocial Empregado – Totalizador Contribuição Previdenciária",
                "standard_file_naming" => "eSocial_Empregado _TotalizadorContribuiçãoPrevidenciária",
                "file_type_id" => 1
            ],
            [
                "file_name" => "eSocial Empregado – Totalizador FGTS",
                "standard_file_naming" => "eSocial_Empregado_TotalizadorFGTS",
                "file_type_id" => 1
            ],
            [
                "file_name" => "eSocial Empregado – Totalizador Imposto de Renda",
                "standard_file_naming" => "eSocial_Empregado_TotalizadorImpostodeRenda",
                "file_type_id" => 1
            ],
            [
                "file_name" => "eSocial Empregado – Demissão",
                "standard_file_naming" => "eSocial_Empregado_Demissão",
                "file_type_id" => 1
            ],
            [
                "file_name" => "eSocial Empregado – Remuneração Devida",
                "standard_file_naming" => "eSocial_Empregado_RemuneraçãoDevida",
                "file_type_id" => 1
            ],

            // Financeiro
            [
                "file_name" => "Certidão negativa CFDF",
                "standard_file_naming" => "G4F_CertidaoNegativa_CFDF",
                "file_type_id" => 2
            ],
            [
                "file_name" => "Certidão negativa CNPJ matriz",
                "standard_file_naming" => "G4F_CertidaoNegativa_CNPJ_Matriz",
                "file_type_id" => 2
            ],
            [
                "file_name" => "Certidão negativa Debito Trabalhista",
                "standard_file_naming" => "G4F_CertidaoNegativa_DebTrabalhistas",
                "file_type_id" => 2
            ],
            [
                "file_name" => "Certidão negativa Declaração de Idoneidade",
                "standard_file_naming" => "G4F_CertidaoNegativa_DeclaraçãodeIdoneidade",
                "file_type_id" => 2
            ],
            [
                "file_name" => "Certidão negativa Falencia Concordata",
                "standard_file_naming" => "G4F_CertidaoNegativa_FalenciaConcordata",
                "file_type_id" => 2
            ],
            [
                "file_name" => "Certidão negativa FGTS",
                "standard_file_naming" => "G4F_CertidaoNegativa_FGTS",
                "file_type_id" => 2
            ],
            [
                "file_name" => "Certidão negativa GDF",
                "standard_file_naming" => "G4F_CertidaoNegativa_GDF",
                "file_type_id" => 2
            ],

            [
                "file_name" => "Certidão negativa Receita Federal",
                "standard_file_naming" => "G4F_CertidaoNegativa_ReceitaFederal",
                "file_type_id" => 2
            ],
            [
                "file_name" => "Certidão negativa SICAF",
                "standard_file_naming" => "G4F_CertidaoNegativa_SICAF",
                "file_type_id" => 2
            ],
            [
                "file_name" => "Certidão negativa INSS",
                "standard_file_naming" => "G4F_CertidaoNegativa_INSS",
                "file_type_id" => 2
            ],
            [
                "file_name" => "Declaração desoneração",
                "standard_file_naming" => "G4F_DeclaraçãoDesoneração",
                "file_type_id" => 2
            ],
            [
                "file_name" => "DCTF-Web",
                "standard_file_naming" => "G4F_GuiaDARF_CPRB_DCTFWeb_Comprovante",
                "file_type_id" => 2
            ],
            [
                "file_name" => "DCTF-Web e IRRF",
                "standard_file_naming" => "G4F_GuiaDARF_DCTFWeb_IRRF_Comprovante",
                "file_type_id" => 2
            ],
            [
                "file_name" => "Plano de Saúde Bradesco",
                "standard_file_naming" => "G4F_PlanoDeSaúde_Bradesco_Comprovante",
                "file_type_id" => 2
            ],
            [
                "file_name" => "Plano de Saúde HapvidaBA",
                "standard_file_naming" => "G4F_PlanoDeSaúde_HapvidaBA_Comprovante",
                "file_type_id" => 2
            ],
            [
                "file_name" => "Plano de Saúde HapvidaBA Coparticipação",
                "standard_file_naming" => "G4F_PlanoDeSaúde_HapvidaBA_Copart_Comprovante",
                "file_type_id" => 2
            ],
            [
                "file_name" => "Plano de Saúde HapvidaMG Coparticipação",
                "standard_file_naming" => "G4F_PlanoDeSaúde_HapvidaMG_Copart_Comprovante",
                "file_type_id" => 2
            ],
            [
                "file_name" => "Plano de Saúde HapvidaMG Principal",
                "standard_file_naming" => "G4F_PlanoDeSaúde_HapvidaMG_Principal_Comprovante",
                "file_type_id" => 2
            ],
            [
                "file_name" => "Plano de Saúde HapvidaPE Hemobrás",
                "standard_file_naming" => "G4F_PlanoDeSaúde_HapvidaPE_Hemobrás_Comprovante",
                "file_type_id" => 2
            ],
            [
                "file_name" => "Plano de Saúde HapvidaPE Principal",
                "standard_file_naming" => "G4F_PlanoDeSaúde_HapvidaPE_Principal_Comprovante",
                "file_type_id" => 2
            ],
            [
                "file_name" => "Plano de Saúde Quality ABDI",
                "standard_file_naming" => "G4F_PlanoDeSaúde_Quality_ABDI_Comprovante",
                "file_type_id" => 2
            ],
            [
                "file_name" => "Plano de Saúde Unimed",
                "standard_file_naming" => "G4F_PlanoDeSaúde_Unimed_Comprovante",
                "file_type_id" => 2
            ],
            [
                "file_name" => "Plano de Saúde Unimed Coparticipação",
                "standard_file_naming" => "G4F_PlanoDeSaúde_Unimed_Copart_Comprovante",
                "file_type_id" => 2
            ],
            [
                "file_name" => "Plano Odontológico HapvidaBA",
                "standard_file_naming" => "G4F_PlanoOdontológico_HapvidaBA_Comprovante",
                "file_type_id" => 2
            ],
            [
                "file_name" => "Plano Odontológico HapvidaPE Hemobrás",
                "standard_file_naming" => "G4F_PlanoOdontológico_HapvidaPE_Hemobrás_Comprovante",
                "file_type_id" => 2
            ],
            [
                "file_name" => "Plano Odontológico METLIFE",
                "standard_file_naming" => "G4F_PlanoOdontológico_METLIFE_Comprovante",
                "file_type_id" => 2
            ],
            [
                "file_name" => "Plano Odontológico Sorriden",
                "standard_file_naming" => "G4F_PlanoOdontológico_Sorriden_Comprovante",
                "file_type_id" => 2
            ],
            [
                "file_name" => "Vale Transporte",
                "standard_file_naming" => "ValeTransporte",
                "file_type_id" => 2
            ],
            [
                "file_name" => "Férias",
                "standard_file_naming" => "Férias",
                "file_type_id" => 2
            ],
            [
                "file_name" => "Recisão Complementar",
                "standard_file_naming" => "RecisãoComplementar",
                "file_type_id" => 2
            ],
        ];

        // Popula a tabela com os dados
        foreach ($dados as $dado) {
            FileNaming::create($dado);
        }
    }
}
