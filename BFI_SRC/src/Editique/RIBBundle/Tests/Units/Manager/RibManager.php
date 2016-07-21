<?php

namespace Editique\RIBBundle\Tests\Units\Manager;

use \Editique\MasterBundle\Tests\Units\Manager\EditiqueManager;

class RibManager extends EditiqueManager
{

    public $filePath = '/app/exchange/editique/in/rib01.xml';
    public $ecritureManager;
    public $lectureManager;
    public $logManager;
    public $entityManager;
    public $windowsFTPManager;
    public $fileManager;
    public $PDFManager;
    public $tplManager;

    // Fonctions d'initialisations (aucun test n'est lancé)
    public function beforeTestMethod()
    {
        parent::beforeTestMethod();

        $this->mockGenerator->generate('\Editique\RIBBundle\Manager\RibManager');
        $this->manager = new \mock\Editique\RIBBundle\Manager\RibManager();
        $this->manager->setEcritureManager($this->ecritureManager);
        $this->manager->setLectureManager($this->lectureManager);
        $this->manager->setEntityManager($this->entityManager);
        $this->manager->setLogManager($this->logManager);
        $this->manager->setFileManager(new \BackOffice\FileBundle\Manager\FileManager($this->logManager));
        $this->manager->setPDFManager($this->pdfManager);
        $this->manager->setTplManager($this->tplManager);

        return $this;
    }

    public function initArrayRib()
    {
        $array['header'] = array(
            'clientLicense' => array(),
            'environnement' => "SABxxxx",
            'groupDroit' => "TODROITOTA",
            'groupMenu' => "SABTELE",
            'language' => 1,
            'sab_agence' => 9999,
            'sab_agence_label' => "SIEGE",
            'sab_etablissement' => 0001,
            'sab_etablissement_label' => "CM xxxxxxxx",
            'sab_interAgence' => array(),
            'sab_service' => "DP",
            'sab_sousService' => "GP",
            'sab_service_label' => "PRODUCTION",
            'userId' => "TOJEAVITW",
            'sessionId' => "TOJEAVITW_0_3",
            'serviceName' => "CptReleveIdentiteBancaire",
            'functionName' => "CptReleveIdentiteBancaire",
            'stepName' => "STEP1",
        );

        $array['body'] = array(
            'libCpt' => "LEONIM      DAV PARTICULIER",
            'codEdt' => array(),
            'cptEdt' => "00000700002",
            'codBnq' => "17xxx",
            'codGui' => 83001,
            'cleRib' => 69,
            'codIbaPap' => "IBAN FR14 1xxx x830 0100 000V 9087 F69",
            'codIbaEle' => "FR141xxxx8300100000V9087F69",
            'codIbi' => "CCUTFR21",
            'nom' => "MR xxxx",
            'prenom' => "Marc",
            'adrTit1' => "463 AV xxxxxx xxxx ",
            'adrTit2' => "L ESCAILLON",
            'adrTit3' => array(),
            'codPosTit' => "8xxxx",
            'vilTit' => "xxxxxx",
            'payTit' => "FRANCE",
            'nomAge' => "AGENCE",
            'adrAge1' => "ADRESSE AGENCE",
            'adrAge2' => array(),
            'adrAge3' => array(),
            'codePosAge' => "8xxxx",
            'vilAge' => "VILLE",
            'telAge' => array()
        );

        $this->manager->ribArray = $array;

        return $this;
    }

    public function initArrayRibInexistant()
    {
        $this->initArrayRib();

        $this->manager->ribArray['body']['cptEdt'] = '999';

        return $this;
    }

    // Tests généraux (construct, errors, dépendances)
    public function testConstruct()
    {
        $this
            ->if($m = new \Editique\RIBBundle\Manager\RibManager())
            ->and($ecritureManager = new \BackOffice\ParserBundle\Manager\EcritureManager($this->logManager))
            ->and($lectureManager = new \BackOffice\ParserBundle\Manager\LectureManager($this->logManager))
            ->variable($m)
            ->isNotNull()
        ;
    }

    public function testErrorsFunctions()
    {
        $this
            ->array($this->manager->getErrors())
            ->isEmpty()
            ->if($this->manager->addError('Ceci est une erreur de test'))
            ->array($this->manager->getErrors())
            ->isNotEmpty()
            ->strictlyContains('Ceci est une erreur de test')
            ->boolean($this->manager->getFatalError())
            ->isFalse()
            ->if($this->manager->addFatalError('Ceci est une fatal erreur de test'))
            ->boolean($this->manager->getFatalError())
            ->isTrue()
        ;
    }

    // Tests de lecture
    public function testReadFile()
    {
        $this
            ->if($this->manager->readFile(''))
            ->boolean($this->manager->getFatalError())
            ->isTrue()
            ->if($this->manager->readFile($this->filePath))
            ->array($this->manager->ribArray)
            ->isNotEmpty()
        ;
    }

    public function testORib()
    {
        $this
            ->initArrayRib()
            ->variable($this->manager->getORib())
            ->isNull()
            ->if($this->manager->setORib())
            ->object($this->manager->getORib())
            ->isNotNull()
        ;
    }

    public function testGetHolder()
    {
        $this
            ->initArrayRib()
            ->array($this->manager->getHolder())
            ->isNotEmpty()
        ;
    }

    public function testGetAddress()
    {
        $this
            ->initArrayRib()
            ->array($this->manager->getAddress())
            ->isNotEmpty()
        ;
    }

    // TODO : avoir EntityManager
    public function testGetIdClient()
    {
        $this
            ->initArrayRibInexistant()
            ->if($this->manager->getIdClient())
            ->boolean($this->manager->getFatalError())
            ->isTrue()
        ;
        $this
            ->initArrayRib()
            ->string($this->manager->getIdClient())
            ->isEqualTo("0000007")
        ;
    }

    public function testGetAccountNumber()
    {
        $this
            ->initArrayRib()
            ->string($this->manager->getAccountNumber())
            ->isEqualTo("00000700002")
        ;
    }

    public function testIban()
    {
        $this
            ->initArrayRib()
            ->string($this->manager->getIban())
            ->isEqualTo("FR14 1xxx x830 0100 000V 9087 F69")
        ;
    }

    public function testBic()
    {
        $this
            ->initArrayRib()
            ->string($this->manager->getBic())
            ->isEqualTo("CCUTFR21")
        ;
    }

    public function testEcrireSortie()
    {
        $dirSortie = $this->container->getParameter('dirSortieEditique');

        $this
            ->initArrayRib()
            ->if($this->manager->setORib())
            ->string($this->manager->ecrireSortie($dirSortie))
                ->contains('1RIB001000000007')
            ->mock($this->manager)
                ->call('writeHolderAndAddress')->once()
                ->call('writeAccountInformations')->once()
                ->call('getFileName')->exactly(2)
                ->call('logEditique')->once()
                ->call('transfertVersServeurFichier')->once()
        ;
    }

    public function testGetPDF()
    {
        $this
            ->initArrayRib()
            ->if($this->manager->setORib())
            ->string($this->manager->getPDF())
            ->isNotEmpty()
        ;
    }

    public function testFileName()
    {
        $this
            ->initArrayRib()
            ->if($this->manager->setORib())
            ->string($this->manager->getFileName('pdf'))
            ->isEqualTo("Avis_0000007_RIB_00000700002_".date('Ymd').".pdf");
    }

    // Tests écriture
    public function testInitRib()
    {
        $this
            ->initArrayRib()
            ->if($this->manager->setORib())
            ->if($this->manager->initRib('001'))
            ->if($content = $this->manager->ecritureManager->getContent())
            ->array($content)
            ->isNotEmpty()
            ->hasSize(4)
            ->string($content[1])
            ->contains('1RIB00100' . $this->manager->oRib->getIdClient())
            ->string($content[2])
            ->isEqualTo(" ")
            ->string($content[3])
            ->isEqualTo(" ")
            ->string($content[4])
            ->isEqualTo(" ")
        ;
    }

    public function testWriteHolderAndAddress()
    {
        $this
            ->initArrayRib()
            ->if($this->manager->setORib())
            ->if($this->manager->writeHolderAndAddress())
            ->if($content = $this->manager->ecritureManager->getContent())
            ->array($content)
            ->isNotEmpty()
            ->hasSize(4)
            ->string($content[1])
            ->contains($this->manager->oRib->getHolder())
            ->string($content[2])
            ->contains($this->manager->oRib->getHolderSuite())
            ->string($content[3])
            ->contains($this->manager->oRib->getAddress())
            ->string($content[4])
            ->contains($this->manager->oRib->getAddressSuite())
        ;
    }

    public function testWriteAccountInformations()
    {
        $this
            ->initArrayRib()
            ->if($this->manager->setORib())
            ->if($this->manager->writeAccountInformations())
            ->if($content = $this->manager->ecritureManager->getContent())
            ->array($content)
            ->isNotEmpty()
            ->hasSize(2)
            ->string($content[1])
            ->contains($this->manager->oRib->getIban())
            ->contains($this->manager->oRib->getBic())
            ->string($content[2])
            ->contains('RIB')
        ;
    }

    public function testContentSortie()
    {
        $this
            ->array($this->manager->getContentSortie())
            ->isEmpty()
            ->if($this->manager->contentSortie = array('test'))
            ->array($this->manager->getContentSortie())
            ->isNotEmpty()
            ->contains('test')
        ;
    }
}
