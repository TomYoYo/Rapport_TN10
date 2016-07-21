<?php
/**
 * Created by PhpStorm.
 * User: t.pueyo
 * Date: 28/04/2016
 * Time: 09:43
 */

namespace BackOffice\CustomerBundle\Command;

use BackOffice\CustomerBundle\Entity\Anomalie;
use BackOffice\CustomerBundle\Entity\Customer;
use BackOffice\CustomerBundle\Entity\SettingsCivility;
use BackOffice\CustomerBundle\Entity\SettingsJuridique;
use BackOffice\CustomerBundle\Entity\SettingsQuality;
use BackOffice\CustomerBundle\Entity\SettingsStateCode;
use BackOffice\CustomerBundle\Manager\InformationsManager;
use BackOffice\MonitoringBundle\Manager\LogManager;
use Doctrine\ORM\EntityManager;
use mageekguy\atoum\asserters\string;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InitializeSettingsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('monitoring:customer:settings')
            ->setDescription('Initialisation des parmaétrages ');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /**
         * @var EntityManager $em
         */
        $em = $this->getContainer()->get('doctrine')->getManager();
        $civilities = array(
            '004-PERSONNE MORALE',
            '005-MR ENTREPRENEUR INDIVIDUEL',
            '006-MME ENTREPRENEUR INDIVIDUEL',
            '007-MLLE ENTREPRENEUR INDIVIDUEL',
        );
        for($cpt = 0;$cpt<4;$cpt++){
            $setting = new SettingsCivility();
            $setting->setIntitule($civilities[$cpt]);
            $setting->setCivilityCode(substr($civilities[$cpt],0,3));
            $setting->setJuridiqueForm(array());
            if($cpt == 0)
            {
                $setting->setCustomerCode('PMCL');
            }
            else
            {
                $setting->setCustomerCode('EICL');
            }
            $em->persist($setting);

        }
        $em->flush();

        $qualities = array(
            "011 - ASSOCIATION",
            "025 - ADMINISTRATIONS PRIVEES",
            "022 - ADMINISTRATIONS PUBLIQUES LOCA",
            "010 - ADMI PUBLIQUE SECURITE SOCIALE",
            "007 - ADMINISTRATION PUBLIQUE ETAT",
            "001 - BANQUES CENTRALES",
            "005 - BANQUE MUTUALISTE OU COOPERATI",
            "003 - BANQUES ETABLISSEMENT CRÉDIT",
            "026 - BANQUES ETRANGERES",
            "006 - CAISSE DE CRÉDIT MUNICIPAL",
            "002 - CCP",
            "004 - CAISSE D'EPARGNE ET CDC",
            "018 - ENTREPRENEURS INDIVIDUELS",
            "016 - FONDS COMMUNS DE CREANCES",
            "021 - FONDS DE PENSION",
            "028 - NON EMUM NRES NFIN ADM PUBLI A",
            "015 - OPCVM MONETAIRE",
            "027 - OPCVM NON MONETAIRES",
            "019 - PARTICULIERS",
            "098 - PAS DE VENTILATION PAR AG ECON",
            "014 - SIEGES A L'ETRANGER",
            "017 - SOC ET QUASI-SOCIETES NON FINA",
            "020 - SOCIETES ASSURANCE",
            "009 - SOCIETES FINANCIERES",
            "008 - TRESOR PUBLIC"

        );

        for($cpt=0;$cpt<25;$cpt++)
        {
            $setting = new SettingsQuality();
            $setting->setIntitule($qualities[$cpt]);
            $setting->setCode(substr($qualities[$cpt],0,3));
            $setting->setFormes(array());
            $em->persist($setting);
        }
        $em->flush();

        $setting = new SettingsStateCode();
        $setting->setIntitule('ADMI - ADMINISTRATION');
        $setting->setAbrege('ADMI');
        $setting->setJuridiqueForme(array());
        $em->persist($setting);
        $setting = new SettingsStateCode();
        $setting->setIntitule('ASSO - ASSOCIATION  ');
        $setting->setAbrege('ASSO');
        $setting->setJuridiqueForme(array());
        $em->persist($setting);
        $setting = new SettingsStateCode();
        $setting->setIntitule('ASSU - SOCIETE D ASSURANCE  ');
        $setting->setAbrege('ASSU');
        $setting->setJuridiqueForme(array());
        $em->persist($setting);
        $setting = new SettingsStateCode();
        $setting->setIntitule('BANQ - BANQUE BANQUE  ');
        $setting->setAbrege('BANQ');
        $setting->setJuridiqueForme(array());
        $em->persist($setting);
        $setting = new SettingsStateCode();
        $setting->setIntitule('CE - COMITE D ENTREPRISE');
        $setting->setAbrege('CE');
        $setting->setJuridiqueForme(array());
        $em->persist($setting);
        $setting = new SettingsStateCode();
        $setting->setIntitule('COPR - COPROPRIETE');
        $setting->setAbrege('COPR');
        $setting->setJuridiqueForme(array());
        $em->persist($setting);
        $setting = new SettingsStateCode();
        $setting->setIntitule('EIML - ENT.INDIVIDUELLE MADEMOISELLE');
        $setting->setAbrege('EIML');
        $setting->setJuridiqueForme(array());
        $em->persist($setting);
        $setting = new SettingsStateCode();
        $setting->setIntitule('EIMM - ENT.INDIVIDUELLE MADAME  ');
        $setting->setAbrege('EIMM');
        $setting->setJuridiqueForme(array());
        $em->persist($setting);
        $setting = new SettingsStateCode();
        $setting->setIntitule('EIMR - ENT.INDIVIDUELLE MONSIEUR');
        $setting->setAbrege('EIMR');
        $setting->setJuridiqueForme(array());
        $em->persist($setting);
        $setting = new SettingsStateCode();
        $setting->setIntitule('ETF - ETABLISSEMENT FINANCIER  ');
        $setting->setAbrege('ETF');
        $setting->setJuridiqueForme(array());
        $em->persist($setting);
        $setting = new SettingsStateCode();
        $setting->setIntitule('HUI - HUISSIER');
        $setting->setAbrege('HUI');
        $setting->setJuridiqueForme(array());
        $em->persist($setting);
        $setting = new SettingsStateCode();
        $setting->setIntitule('INDI - INDIVISION');
        $setting->setAbrege('INDI');
        $setting->setJuridiqueForme(array());
        $em->persist($setting);
        $setting = new SettingsStateCode();
        $setting->setIntitule('MME - MADAME');
        $setting->setAbrege('MME');
        $setting->setJuridiqueForme(array());
        $em->persist($setting);
        $setting = new SettingsStateCode();
        $setting->setIntitule('MLLE - MADEMOISELLE');
        $setting->setAbrege('MLLE');
        $setting->setJuridiqueForme(array());
        $em->persist($setting);
        $setting = new SettingsStateCode();
        $setting->setIntitule('MR - MONSIEUR');
        $setting->setAbrege('MR');
        $setting->setJuridiqueForme(array());
        $em->persist($setting);
        $setting = new SettingsStateCode();
        $setting->setIntitule('MUTU - MUTUELLE');
        $setting->setAbrege('MUTU');
        $setting->setJuridiqueForme(array());

        $em->persist($setting);
        $setting = new SettingsStateCode();
        $setting->setIntitule('SCI - SCI');
        $setting->setAbrege('SCI');
        $setting->setJuridiqueForme(array());
        $em->persist($setting);
        $setting = new SettingsStateCode();
        $setting->setIntitule('SCM - SCM');
        $setting->setAbrege('SCM');
        $setting->setJuridiqueForme(array());
        $em->persist($setting);
        $setting = new SettingsStateCode();
        $setting->setIntitule('SCP - SCP');
        $setting->setAbrege('SCP');
        $setting->setJuridiqueForme(array());
        $em->persist($setting);
        $setting = new SettingsStateCode();
        $setting->setIntitule('STE - SOCIETE');
        $setting->setAbrege('STE');
        $setting->setJuridiqueForme(array());
        $em->persist($setting);
        $setting = new SettingsStateCode();
        $setting->setIntitule('TRIB - TRIBUNAL');
        $setting->setAbrege('TRIB');
        $setting->setJuridiqueForme(array());
        $em->persist($setting);
        $em->flush();



        /*$em->getConnection()->query('TRUNCATE TABLE settingsjuridique');
        $formes = array(
            "1100 - Artisan-commerçant",
            "1200 - Commerçant",
            "1300 - Artisan",
            "1400 - Officier public ou ministeriel",
            "1500 - Profession libérale",
            "1600 - Exploitant agricole",
            "1700 - Agent commercial",
            "1800 - Associé gerant de Société",
            "1900 - (Autre) Personne physique",
            "2110 - Indivision entre personnes physiques",
            "2120 - Indivision avec personne morale",
            "2210 - Société créée de fait entre personnes physiques",
            "2220 - Société créée de fait avec personne morale",
            "2310 - Société en participation entre personnes physiques",
            "2320 - Société en participation avec personne morale",
            "2385 - Société en participation de professions libérales",
            "2400 - Fiducie",
            "2700 - Paroisse hors zone concordataire",
            "2900 - Autre groupement de droit privé non doté de la personnalité",
            "3110 - Représentation ou agence commerciale d'état ou organisme ..",
            "3120 - Société étrangère immatriculée au RCS",
            "3205 - Organisation internationale",
            "3210 - etat collectivite ou etablissement public etranger",
            "3220 - Société étrangère non immatriculée au RCS",
            "3290 - (Autre) personne morale de droit étranger",
            "4110 - Etablissement public national à caractère industriel 1",
            "4120 - Etablissement public national a caractère industriel 2",
            "4130 - Exploitant public",
            "4140 - Établissement public local à caractère indust. ou commercial",
            "4150 - Régie d'une collectivité locale à caractère ind. ou com.",
            "4160 - Institution Banque de France",
            "5191 - Société de caution mutuelle",
            "5192 - Société coopérative de banque populaire",
            "5193 - Caisse de crédit maritime mutuel",
            "5194 - Caisse (fédérale) de crédit mutuel",
            "5195 - Association coopérative inscrite -droit local Alsace Moselle",
            "5196 - Caisse d'épargne et de prévoyance à forme coopérative",
            "5202 - Société en nom collectif",
            "5203 - Société en nom collectif coopérative",
            "5306 - Société en commandite simple",
            "5307 - Société en commandite simple coopérative",
            "5308 - Société en commandite par actions",
            "5309 - Société en commandite par actions coopérative",
            "5370 - Société de Particip Financières de Profession Libérale",
            "5385 - Société d'exercice libéral en commandite par actions",
            "5410 - SARL nationale",
            "5415 - SARL d'économie mixte",
            "5422 - SARL immobilière pour le commerce et l'industrie (SICOMI)",
            "5426 - SARL immobilière de gestion",
            "5430 - SARL d'aménagement foncier et d'équipement rural  SAFER",
            "5431 - SARL mixte d'intérêt agricole (SMIA)",
            "5432 - SARL d'intérêt collectif agricole (SICA)",
            "5442 - SARL d'attribution",
            "5443 - SARL coopérative de construction",
            "5451 - SARL coopérative de consommation",
            "5453 - SARL coopérative artisanale",
            "5454 - SARL coopérative d'intérêt maritime",
            "5455 - SARL coopérative de transport",
            "5458 - SARL coopérative ouvrière de production et de crédit (SCOP)",
            "5459 - SARL union de sociétés coopératives",
            "5460 - Autre SARL coopérative",
            "5470 - Société de Participations Financières de Profession Libérale",
            "5485 - Société d'exercice libéral à responsabilité limitée",
            "5498 - SARL unipersonnelle",
            "5499 - Société à responsabilite limitée (sans autre indication)",
            "5505 - SA à participation ouvrière à conseil d'administration",
            "5510 - SA nationale à conseil d'administration",
            "5515 - SA d'économie mixte à conseil d'administration",
            "5520 - Société d'invest. à capital var. (SICAV) à conseil d'admin.",
            "5522 - SA immobilière pour le commerce et l'industrie (SICOMI)",
            "5525 - SA immobilière d'investissement à conseil d'administration",
            "5530 - Safer anonyme à conseil d'administration",
            "5531 - SA mixte d'intérêt agricole (SICA) à conseil d'administ.",
            "5532 - SA d'intérêt collectif agricole (SICA) a conseil d'admin.",
            "5542 - Société anonyme d'attribution à conseil d'administration",
            "5543 - SA coopérative de construction à conseil d'administration",
            "5546 - SA de HLM à conseil d'administration",
            "5547 - SA coopérative de production de HLM à conseil d'admin.",
            "5548 - SA de crédit immobilier à conseil d'administration",
            "5551 - SA coopérative de consommation à conseil d'administration",
            "5552 - SA coopérative de commerçants détaillants à conseil d'admin.",
            "5553 - SA coopérative artisanale à conseil d'administration",
            "5554 - SA coopérative (d'intérêt) maritime à conseil d'admin.",
            "5555 - SA coopérative de transports à conseil d'administration",
            "5558 - SCOP à conseil d'administration",
            "5559 - SA union de sociétés coopératives à conseil d'administration",
            "5560 - Autre SA coopérative à conseil d'administration",
            "5570 - Societe de Participations Financieres de Profession Liberale",
            "5585 - Société d'exercice libéral à forme anonyme à conseil d'admin",
            "5599 - Autre SA à conseil d'administration",
            "5605 - SA à participation ouvrière à directoire",
            "5610 - SA nationale à directoire",
            "5615 - SA d'économie mixte à directoire",
            "5620 - SICAV à directoire",
            "5622 - SICOMI a directoire",
            "5625 - Société immobilière d'investissement anonyme à directoire",
            "5630 - Safer anonyme à directoire",
            "5631 - Société anonyme mixte d'intérêt agricole (SMIA)",
            "5632 - Société anonyme d'intérêt collectif agricole (SICA)",
            "5642 - Société anonyme d'attribution à directoire",
            "5643 - Société anonyme coopérative de construction à directoire",
            "5646 - Société anonyme de HLM à directoire",
            "5647 - Société coopérative de production de HLM anonyme à directoir",
            "5648 - SA de crédit immobilier à directoire",
            "5651 - SA coopérative de consommation à directoire",
            "5652 - SA coopérative de commerçants détaillants à directoire",
            "5653 - SA coopérative artisanale à directoire",
            "5654 - SA coopérative (d'intérêt) maritime à directoire",
            "5655 - SA coopérative de transport à directoire",
            "5658 - SA coop. ouvrière de production et de crédit (SCOP) à direct",
            "5659 - SA union de sociétés coopératives à directoire",
            "5660 - (Autre) SA coopérative à directoire",
            "5670 - Societe de Participations Financieres de Profession Liberale",
            "5685 - Société d'exercice libéral à forme anonyme à directoire",
            "5699 - (Autre) SA à directoire",
            "5710 - Société par actions simplifiée (SAS)",
            "5720 - Société par actions simpl. associé unique ou société par act",
            "5770 - Societe de Part Financieres de Profession Liberale",
            "5785 - Société d'exercice libéral par action simplifiée",
            "5800 - Société européenne",
            "6100 - Caisse d'Épargne et de Prévoyance",
            "6210 - Groupement européen d'intérêt économique (GEIE)",
            "6220 - Groupement d'intérêt économique (GIE)",
            "6316 - Coopérative d'utilisation de matériel agricole en commun",
            "6317 - Société coopérative agricole",
            "6318 - Union de sociétés coopératives agricoles",
            "6411 - Société d'assurance à forme mutuelle",
            "6511 - Societes Interprofessionnelles de Soins Ambulatoires",
            "6521 - Societe civile de placement collectif immobilier (SCPI)",
            "6532 - Societe civile d interet collectif agricole (SICA)",
            "6533 - Groupement agricole d exploitation en commun (GAEC)",
            "6534 - Groupement foncier agricole",
            "6535 - Groupement agricole foncier",
            "6536 - Groupement forestier",
            "6537 - Groupement pastoral",
            "6538 - Groupement foncier et rural",
            "6539 - Société civile foncière",
            "6540 - Société civile immobilière",
            "6541 - Société civile immobilière de construction-vente",
            "6542 - Société civile d'attribution",
            "6543 - Société civile coopérative de construction",
            "6544 - Société civile immobilière d'accession prog.. à la propriété",
            "6551 - Société civile coopérative de consommation",
            "6554 - Société civile coopérative d'intérêt maritime",
            "6558 - Société civile coopérative entre médecins",
            "6560 - Autre société civile coopérative",
            "6561 - SCP d'avocats",
            "6562 - SCP d'avocats aux conseils",
            "6563 - SCP d'avoués d'appel",
            "6564 - SCP d'huissiers",
            "6565 - SCP de notaires",
            "6566 - SCP de commissaires-priseurs",
            "6567 - SCP de greffiers de tribunal de commerce",
            "6568 - SCP de conseils juridiques",
            "6569 - SCP de commissaires aux comptes",
            "6571 - SCP de médecins",
            "6572 - SCP de dentistes",
            "6573 - SCP d'infirmiers",
            "6574 - SCP de masseurs-kinésitherapeutes",
            "6575 - SCP de directeurs de laboratoire d'analyse médicale",
            "6576 - SCP de vétérinaires",
            "6577 - SCP de géomètres experts",
            "6578 - SCP d'architectes",
            "6585 - Autre société civile professionnelle",
            "6588 - Societe civile laitiere",
            "6589 - Société civile de moyens",
            "6595 - Caisse locale de crédit mutuel",
            "6596 - Caisse de crédit agricole mutuel",
            "6597 - Société civile d'exploitation agricole",
            "6598 - Exploitation agricole à responsabilite limitée",
            "6599 - Autre société civile",
            "6901 - Autre personne de droit privé inscrite au registre du c./s.",
            "7111 - Autorité constitutionnelle",
            "7112 - Autorite administrative independante",
            "7113 - Ministère",
            "7120 - Service central d un ministère",
            "7150 - Service du ministère de la Défense",
            "7160 - Service déconcentré a compétence nationale d un ministère",
            "7171 - Service déconcentré de l'etat a compétence (inter) régionale",
            "7172 - Service déconcentré de l'état a compétence (inter) départem.",
            "7179 - (Autre) Service deconcentre de l etat a competence ter",
            "7190 - Ecole nationale non dotée de la personnalité morale",
            "7210 - Commune et commune nouvelle",
            "7220 - Département",
            "7225 - Collectivité et territoire d'Outre Mer",
            "7229 - (Autre) Collectivité territoriale",
            "7230 - Région",
            "7312 - Commune associée et commune déléguée",
            "7313 - Section de commune",
            "7314 - Ensemble urbain",
            "7321 - Association syndicale autorisée",
            "7322 - Association foncière urbaine",
            "7323 - Association foncière de remembrement",
            "7331 - Etablissement public local d'enseignement",
            "7340 - Pôle métropolitain",
            "7341 - Secteur de commune",
            "7342 - District urbain",
            "7343 - Communauté urbaine",
            "7344 - Metropole",
            "7345 - Syndicat intercommunal à vocation multiple (SIVOM)",
            "7346 - Communauté de communes",
            "7347 - Communauté de villes",
            "7348 - Communauté d'agglomération",
            "7349 - Autre établissement public local de coopération non spé.",
            "7351 - Institution interdépartementale ou entente",
            "7352 - Institution interregionale ou entente",
            "7353 - Syndicat intercommunal à vocation unique (SIVU)",
            "7354 - Syndicat mixte communal",
            "7355 - Autre syndicat mixte",
            "7356 - Commission syndicale pour la gestion des biens indivis des c",
            "7361 - Centre communal d'action sociale",
            "7362 - Caisse des écoles",
            "7363 - Caisse de crédit municipal",
            "7364 - Etablissement d'hospitalisation",
            "7365 - Syndicat inter hospitalier",
            "7366 - Etablissement public local social et médico-social",
            "7371 - Office public d'habitation à loyer modéré (OPHLM)",
            "7372 - Service départemental d'incendie",
            "7373 - Etablissement public local culturel",
            "7378 - Regie d'une collectivité locale à caractère administratif",
            "7379 - (Autre) Etablissement public administratif local",
            "7381 - Organisme consulaire",
            "7382 - Etabl. public national fonction d'administration centrale",
            "7383 - Etabl public national à caractère scientifique cult. et pro.",
            "7384 - Autre établ. public national d'enseignement",
            "7385 - Autre etabl. public national administratif à compétence ter.",
            "7389 - Etablissement public national à caractère administratif",
            "7410 - Groupement d'intérêt public (GIP)",
            "7430 - Etablissement public des cultes d'Alsace-Lorraine",
            "7450 - Etabl. public administratif, cercle et foyer dans les armées",
            "7470 - Groupement de coopération sanitaire à gestion publique",
            "7490 - Autre personne morale de droit administratif",
            "8110 - Régime général de la Securité Sociale",
            "8120 - Régime spécial de Securité Sociale",
            "8130 - Institution de retraite complémentaire",
            "8140 - Mutualité sociale agricole",
            "8150 - Régime maladie des non-salariés non agricoles",
            "8160 - Régime vieillesse ne dépendant pas du régime général de la S",
            "8170 - Régime d'assurance chômage",
            "8190 - Autre régime de prévoyance sociale",
            "8210 - Mutuelle",
            "8250 - Assurance mutuelle agricole",
            "8290 - Autre organisme mutualiste",
            "8310 - Comité central d'entreprise",
            "8311 - Comité d'établissement",
            "8410 - Syndicat de salariés",
            "8420 - Syndicat patronal",
            "8450 - Ordre professionnel ou assimilé",
            "8470 - Centre technique industriel ou comité professionnel du dév.",
            "8490 - Autre organisme professionnel",
            "8510 - Institution de prévoyance",
            "8520 - Institution de retraite supplémentaire",
            "9110 - Syndicat de copropriété",
            "9150 - Association syndicale libre",
            "9210 - Association non declarée",
            "9220 - Association déclarée",
            "9221 - Association déclarée d'insertion par l'économique",
            "9222 - Association intermédiaire",
            "9223 - Groupement d employeurs",
            "9224 - Association d'avocats à responsabilite professionnelle indiv",
            "9230 - Association declarée, reconnue d'utilité publique",
            "9240 - Congrégation",
            "9260 - Association de droit local (Bas-Rhin, Haut-Rhin et Moselle)",
            "9300 - Fondation",
            "9900 - Autre personne morale de droit privé",
            "9970 - Groupement de coopération sanitaire à gestion privée"
        );
        for($cpt = 0;$cpt<266;$cpt++){
            $setting = new SettingsJuridique();
            $setting->setCode(substr($formes[$cpt],0,4));
            $setting->setIntitule($formes[$cpt]);
            $em->persist($setting);
            if($cpt %10 == 0)
            {
                $output->writeln($cpt);
            }
        }
        $em->flush();*/

    }
}
