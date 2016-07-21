<?php

namespace Editique\ReleveBundle\Tests\Units\Manager;

use \Editique\MasterBundle\Tests\Units\Manager\EditiqueManager;
use Editique\ReleveBundle\Entity\Releve;
use Editique\ReleveBundle\Entity\Operation;

class ReleveManager extends EditiqueManager
{

    public $line =
    '07/01/2013 GE MONEY BANK        180         07/01/2013                       8,00                             ';
    public $filePath = '/../fichiers_entrees_test/releve8.txt';
    public $filePath87 = '/../fichiers_entrees_test/releve87.txt';
    public $filePath22 = '/../fichiers_entrees_test/releve22.txt';
    public $filePath15 = '/../fichiers_entrees_test/releve15.txt';
    public $filePath120 = '/../fichiers_entrees_test/releve120.txt';
    public $objetReleve = null;


    /**
     * Initialise
     *      l'objet releveManager à tester
     *      les ecriture et lecture manager qui vont avec
     */
    public function beforeTestMethod()
    {
        parent::beforeTestMethod();

        $this->manager = new \mock\Editique\ReleveBundle\Manager\ReleveManager();
        $this->manager->setEcritureManager($this->ecritureManager);
        $this->manager->setLectureManager($this->lectureManager);
        $this->manager->setEntityManager($this->entityManager);
        $this->manager->setLogManager($this->logManager);
        $this->manager->setFileManager($this->fileManager);
        //$this->manager->setWindowsFTPManager($this->windowsFTPManager);
        $this->manager->setPDFManager($this->pdfManager);
        $this->manager->setTplManager($this->tplManager);

        $this->manager->oReleve = new Releve();
        $this->manager->oReleve->setTypeCompte('courant');

    }


    /**************************************************************************
     *
     *              TESTS GENERAUX
     *
     * *************************************************************************/
    public function testConstruct()
    {
        $this->if($m = new \Editique\ReleveBundle\Manager\ReleveManager())
            ->and($ecritureManager = new \BackOffice\ParserBundle\Manager\EcritureManager($this->logManager))
            ->and($lectureManager = new \BackOffice\ParserBundle\Manager\LectureManager($this->logManager))
            ->variable($m)
            ->isNotNull()
        ;
    }

    public function testGetFileName()
    {
        $this
            ->if($this->manager->oReleve->setIdClient('123'))
            ->and($this->manager->oReleve->setNumCompte('456'))
            ->string($this->manager->getFileName('txt'))
            ->isEqualTo(
                "Avis_" .
                $this->manager->oReleve->getIdClient() .
                "_REL_CPT_" .
                $this->manager->oReleve->getNumCompte() .
                "_" .
                date('Ymd') .
                ".txt"
            )
        ;
    }

    public function testGetFileContent()
    {
        $this
            ->if($this->manager->fileContent = 'toto')
            ->string($this->manager->getFileContent())
            ->isEqualTo('toto')
        ;
    }

    public function testGetOReleve()
    {
        $this
            ->object($this->manager->getOReleve())
            ->isInstanceOf('Editique\ReleveBundle\Entity\Releve')
        ;
    }

    public function testGetRelevesFromFile()
    {
        $this
            ->array($this->manager->getRelevesFromFile($this->filePath))
            ->hasSize(1)
        ;
    }

    public function testSetTypeCompte()
    {
        $this
            ->if($this->manager->setTypeCompte('toto'))
            ->string($this->manager->typeCompte)
            ->isEqualTo('toto')
        ;
    }

    /*     * ************************************************************************
     *
     *              TESTS LECTURE
     *
     * ************************************************************************ */
    public function testGetOpeDate()
    {
        $this
            ->if($this->manager->lectureManager->setCurrentLine($this->line, 0))
            ->string($this->manager->getOpeDate())
            ->isEqualTo('07/01/2013')
        ;
    }

    public function testGetOpeLibelle()
    {
        $this
            ->if($this->manager->currentLine = $this->line)
            ->string($this->manager->getOpeLibelle())
            ->hasLength(30)
            ->isEqualTo('GE MONEY BANK        180      ')
        ;
    }

    public function testGetOpertions()
    {
        $this->manager->debutOpe = 20;
        $this->manager->finOpe = 192;
        $this->manager->lines = file(__DIR__ . $this->filePath87);
        $this
            ->if($this->manager->getOperations())
            ->integer(count($this->manager->oReleve->getOperations()))
            ->isEqualTo(87)
        ;
    }

    public function testIsNouvelleOpe()
    {
        $OldLine = '            sdf654sf65s4';
        $newLine = '65464';
        $this
            ->if($this->manager->currentLine = $OldLine)
            ->boolean($this->manager->isNouvelleOpe())
            ->isFalse()
            ->if($this->manager->currentLine = $newLine)
            ->boolean($this->manager->isNouvelleOpe())
            ->isTrue()
        ;
    }

    public function testGetOpeDebit()
    {
        $lineOp = '07/01/2013 GE MONEY BANK        180         07/01/2013                       ';
        $lineOp .= '8,00                    15,10        ';
        $this
            ->if($this->manager->currentLine = $lineOp)
            ->string($this->manager->getOpeDebit())
            ->contains('8,00')
        ;
    }

    public function testGetOpeCredit()
    {
        $lineOp = '07/01/2013 GE MONEY BANK        180         07/01/2013                       ';
        $lineOp .= '8,00                    15,10        ';
        $this
            ->if($this->manager->currentLine = $lineOp)
            ->string($this->manager->getOpeCredit())
            ->contains('15,10')
        ;
    }

    public function testLireReleve()
    {
        $releve = file_get_contents(__DIR__ . $this->filePath);
        $this
            ->if($this->manager->lireReleve($releve))
            ->array($this->manager->lines)
            ->hasSize(44)
            ->string($this->manager->oReleve->getIdClient())
            ->isEqualTo('0009664')
            ->string($this->manager->oReleve->getSoldeCrediteur())
            ->contains(' 246,50')
        ;
    }

    /*
     * *************************************************************************
     *
     *              TESTS ECRITURE
     *
     * ************************************************************************
     */

    /*
     * Initialise l'objet releve pour le fichier test Releve1.txt'
     * Il contient une seule opération
     */
    public function initObjetReleve1()
    {
        $this->manager->oReleve = new Releve();
        $this->manager->oReleve
            ->setAdresse("XXX AV LEOPOLD HEBER\n97300  CAYENNE\n       GUYANE")
            ->setAncienSoldeCrediteur('187,13')
            ->setAncienSoldeDebiteur('654')
            ->setDateDebut('01/01/2013')
            ->setDateFin('31/01/2013')
            ->setDateFinPrecedente('31/12/2012')
            ->setIdClient('0009664')
            ->setIdeSab('idesab')
            ->setMajRemuneration('')
            ->setMessageCommerciaux(array(
                'Message commercial 1',
                'Message commercial 2'
            ))
            ->setNumCompte('00000Z8904G')
            ->setNumReleve('2')
            ->setSoldeCrediteur('246,50')
            ->setSoldeDebiteur('123456')
            ->setTEG('12.19')
            ->setTitulaire('MR XXXXXX CLAUDE')
            ->setTotalCommissions('')
            ->setTotalInteretAcquis('')
            ->setTotalInteretDebiteur('')
            ->setTotalMvtCrediteur('311,70')
            ->setTotalMvtDebiteur('251,33')
            ->setTxInteret('')
            ->setTxRemuneration('')
        ;

        $operation = new Operation();
        $operation
            ->setCredit('153,30')
            ->setDebit('8,00')
            ->setDateOperation('07/01/2013')
            ->setDateValeur('08/01/2013')
            ->setIdOperation(1)
            ->setLibelle("GE MONEY BANK\n000015241")
        ;

        $this->manager->oReleve->addOperation($operation);
        $this->manager->nbLignesOperationsRestantes = 1;
        return $this;
    }

    /*
     * Initialise l'objet releve pour le fichier test Releve1.txt'
     * Il contient une seule opération
     */
    public function initObjetReleve150()
    {
        $this->manager->oReleve = new Releve();

        for ($i = 0; $i < 150; $i++) {
            $operation = new Operation();
            $operation
                ->setCredit('13,30')
                ->setDebit('8,00')
                ->setDateOperation('07/01/2013')
                ->setDateValeur('08/01/2013')
                ->setIdOperation(1)
                ->setLibelle("GE MONEY BANK\n000015241")
            ;

            $this->manager->oReleve->addOperation($operation);
        }

        $this->manager->oReleve
            ->setAdresse("123 AV 150 opération HEBER\n97300  CAYENNE\n       GUYANE")
            ->setAncienSoldeCrediteur('187,13')
            ->setAncienSoldeDebiteur('654')
            ->setDateDebut('01/01/2013')
            ->setDateFin('31/01/2013')
            ->setDateFinPrecedente('31/12/2012')
            ->setIdClient('0009664')
            ->setIdeSab('idesab')
            ->setMajRemuneration('')
            ->setMessageCommerciaux(array(
                'Message commercial 1',
                'Message commercial 2'
            ))
            ->setNumCompte('00000Z8904G')
            ->setNumReleve('2')
            ->setSoldeCrediteur('246,50')
            ->setSoldeDebiteur('123456')
            ->setTEG('12.19')
            ->setTitulaire('MR XXXXXX CLAUDE')
            ->setTotalCommissions('')
            ->setTotalInteretAcquis('')
            ->setTotalInteretDebiteur('')
            ->setTotalMvtCrediteur('311,70')
            ->setTotalMvtDebiteur('251,33')
            ->setTxInteret('')
            ->setTxRemuneration('')
        ;

        return $this;
    }

    public function testInitRLV()
    {
        $this
            ->initObjetReleve1()
            ->if($this->manager->initRLV('001'))
            ->object($this->manager->oReleve)
            ->isNotNull()
            ->if($content = $this->manager->ecritureManager->getContent())
            ->array($content)
            ->isNotEmpty()
            ->hasSize(4)
            ->string($content[1])
            // on verifie que la premier ligne contient l'identifiant de la maaquette
            ->contains('1RLV00100' . $this->manager->oReleve->getIdClient())
            ->string($content[2])
            ->isEqualTo(' ')
            ->string($content[3])
            ->isEqualTo(' ')
        // todo : test type de compte qd onb l'aura
        ;
    }

    public function testEcrireLigneInfosGenerales()
    {
        $this
            ->initObjetReleve1()
            ->if($this->manager->ecrireLigneInfosGenerales())
            ->object($this->manager->oReleve)
            ->isNotNull()
            ->if($content = $this->manager->ecritureManager->getContent())
            ->array($content)
            ->isNotEmpty()
            ->hasSize(1)
            // todo ; test nb page et num page
            ->string($content[1])
            ->contains($this->manager->oReleve->getDateDebut())
            ->contains($this->manager->oReleve->getDateFin())
            ->contains($this->manager->oReleve->getNumReleve())
            ->contains($this->manager->oReleve->getIdClient())
            ->contains($this->manager->oReleve->getNumCompte())
            ->contains($this->manager->oReleve->getIdeSab())
        ;
    }

    public function testEcrireLigneTitulaireEtAdresse()
    {
        $this
            ->initObjetReleve1()
            ->if($this->manager->ecrireLigneTitulaireEtAdresse())
            ->object($this->manager->oReleve)
            ->isNotNull()
            ->if($content = $this->manager->ecritureManager->getContent())
            ->array($content)
            ->isNotEmpty()
            ->hasSize(4)
            ->if($adresseLines = explode("\n", $this->manager->oReleve->getAdresse()))
            ->string($content[1])
            ->contains($this->manager->oReleve->getTitulaire())
            ->string($content[2])
            ->contains(trim($adresseLines[0]))
            ->contains(trim($adresseLines[1]))
            ->string($content[3])
            ->contains(trim($adresseLines[2]))
            ->string($content[4])
            ->contains('  ') // Même vide, rajoute 2 espaces (pour commencer colonne 3)
        ;
    }

    // test avec function ecrireLigneTitulaireEtAdresse argumentée à true
    public function testEcrireLigneTitulaireEtAdresseTrue()
    {
        $this
            ->initObjetReleve1()
            ->if($this->manager->ecrireLigneTitulaireEtAdresse(true))
            ->object($this->manager->oReleve)
            ->isNotNull()
            ->if($content = $this->manager->ecritureManager->getContent())
            ->array($content)
            ->isNotEmpty()
            ->hasSize(4)
            ->string($content[4])
            ->contains($this->manager->oReleve->getDateFinPrecedente())
        ;
    }

    public function testEcrireMessagesCommerciaux()
    {
        $this
            ->initObjetReleve1()
            ->if($this->manager->ecrireMessagesCommerciaux(3))
            ->object($this->manager->oReleve)
            ->isNotNull()
            ->if($content = $this->manager->ecritureManager->getContent())
            ->array($content)
            ->isNotEmpty()
            ->hasSize(3)
            ->string($content[1])
            ->contains('Message commercial 1')
            ->string($content[2])
            ->contains('Message commercial 2')
            ->string($content[3])
            ->contains('  ') // Même vide, rajoute 3 espaces (pour commencer colonne 3)
        ;
    }

    public function testEcrireLigneAnciensSoldes()
    {
        $this
            ->initObjetReleve1()
            ->if($this->manager->ecrireLigneAnciensSoldes())
            ->object($this->manager->oReleve)
            ->isNotNull()
            ->if($content = $this->manager->ecritureManager->getContent())
            ->array($content)
            ->isNotEmpty()
            ->hasSize(1)
            ->string($content[1])
            ->contains($this->manager->oReleve->getAncienSoldeDebiteur())
            ->contains($this->manager->oReleve->getAncienSoldeCrediteur())
        ;
    }

    public function testEcrireLigneTotauxEtSoldes()
    {
        $this
            ->initObjetReleve1()
            ->if($this->manager->ecrireLigneTotauxEtSoldes())
            ->object($this->manager->oReleve)
            ->isNotNull()
            ->if($content = $this->manager->ecritureManager->getContent())
            ->array($content)
            ->isNotEmpty()
            ->hasSize(2)
            ->string($content[1])
            ->contains($this->manager->oReleve->getTotalMvtDebiteur())
            ->contains($this->manager->oReleve->getTotalMvtCrediteur())
            ->string($content[2])
            ->contains($this->manager->oReleve->getSoldeDebiteur())
            ->contains($this->manager->oReleve->getSoldeCrediteur())
        ;
    }

    public function testEcrireLignesOperations()
    {
        $this
            ->initObjetReleve1()
            ->if($this->manager->ecrireLignesOperations(2))
            ->object($this->manager->oReleve)
            ->isNotNull()
            ->if($content = $this->manager->ecritureManager->getContent())
            ->array($content)
            ->isNotEmpty()
            ->hasSize(3)
            ->if($operations = $this->manager->oReleve->getOperations())
            ->array($operations->toArray())
            ->hasSize(1)
            ->if($tabLibelle = explode("\n", $operations[0]->getLibelle()))
            ->string($content[1])
            ->contains($operations[0]->getDateOperation())
            ->contains($tabLibelle[0])
            ->contains($operations[0]->getDateValeur())
            ->string($content[2])
            ->contains($operations[0]->getCredit())
            ->contains($operations[0]->getDebit())
            ->string($content[3])
            ->contains($tabLibelle[1])
        ;

        $this
            ->initObjetReleve150()
            ->if($this->manager->ecrireLignesOperations(150))
            ->object($this->manager->oReleve)
            ->isNotNull()
            ->if($operations = $this->manager->oReleve->getOperations())
            ->array($operations->toArray())
            ->hasSize(150)
        ;
    }

    public function testGetTotalInteretAcquisGlobal()
    {
        $this
            ->initObjetReleve1()
            ->if($s = $this->manager->getTotalInteretAcquisGlobal())
            ->string($s)
            ->isEqualTo(
                $this->manager->oReleve->getTotalInteretAcquis() . ' euros au ' . $this->manager->oReleve->getDateFin()
            )
        ;
    }

    public function testEcrireSortie1operationCourant()
    {
        $releve = file_get_contents(__DIR__ . $this->filePath);
        $dirSortie = '/home/FIDUCIAL/spool_fichiers/test/';

        $this
            ->string($this->manager->ecrireSortie($releve, $dirSortie))
            ->isEqualTo($this->manager->getFileName('pdf'))
            ->boolean(file_exists($dirSortie . $this->manager->getFileName('pdf')))
            ->isTrue()
            ->integer(count($this->manager->oReleve->getOperations()))
            ->isEqualTo(8)
            ->integer($this->manager->debutOpe)
            ->isEqualTo(20)
            ->integer($this->manager->finOpe)
            ->isEqualTo(32)
            ->mock($this->manager)
            ->call('getRLV001')->once()
            ->call('getRLV002')->never()
            ->call('getRLV003')->never()
            ->call('getRLV004')->never()
            ->call('getRLV005')->never()
            ->call('getRLV006')->never()
            ->call('getRLV007')->never()
            ->call('getRLV008')->never()
            ->call('getRLV009')->never()
            ->call('getRLV010')->never()
            ->call('getRLV011')->never()
            ->call('getRLV012')->never()
            ->call('lireReleve')->once()
        ;
    }

    public function testEcrireSortie1operationEpargne()
    {
        $releve = file_get_contents(__DIR__ . $this->filePath);
        $dirSortie = '/home/FIDUCIAL/spool_fichiers/test/';

        $this->manager->setTypeCompte('epargne');

        $this
            ->string($this->manager->ecrireSortie($releve, $dirSortie))
            ->isEqualTo($this->manager->getFileName('pdf'))
            ->boolean(file_exists($dirSortie . $this->manager->getFileName('pdf')))
            ->isTrue()
            ->integer(count($this->manager->oReleve->getOperations()))
            ->isEqualTo(8)
            ->integer($this->manager->debutOpe)
            ->isEqualTo(20)
            ->integer($this->manager->finOpe)
            ->isEqualTo(32)
            ->mock($this->manager)
            ->call('getRLV001')->never()
            ->call('getRLV002')->once()
            ->call('getRLV003')->never()
            ->call('getRLV004')->never()
            ->call('getRLV005')->never()
            ->call('getRLV006')->never()
            ->call('getRLV007')->never()
            ->call('getRLV008')->never()
            ->call('getRLV009')->never()
            ->call('getRLV010')->never()
            ->call('getRLV011')->never()
            ->call('getRLV012')->never()
            ->call('lireReleve')->once()
        ;
    }

    public function testEcrireSortie87operation()
    {
        $dirSortie = '/home/FIDUCIAL/spool_fichiers/test/';
        $releve = file_get_contents(__DIR__ . $this->filePath87);
        $this
            ->string($this->manager->ecrireSortie($releve, $dirSortie))
            ->isEqualTo($this->manager->getFileName('pdf'))
            ->boolean(file_exists($dirSortie . $this->manager->getFileName('pdf')))
            ->isTrue()
            ->integer($this->manager->debutOpe)
            ->isEqualTo(20)
            ->integer($this->manager->finOpe)
            ->isEqualTo(192)
            ->integer(count($this->manager->oReleve->getOperations()))
            ->isEqualTo(87)
            ->mock($this->manager)
            ->call('getRLV001')->never()
            ->call('getRLV002')->never()
            ->call('getRLV003')->never()
            ->call('getRLV004')->once()
            ->call('getRLV005')->never()
            ->call('getRLV006')->once()
            ->call('getRLV007')->once()
            ->call('getRLV008')->once()
            ->call('getRLV009')->never()
            ->call('getRLV010')->never()
            ->call('getRLV011')->never()
            ->call('getRLV012')->never()
            ->call('lireReleve')->once()
        ;
    }

    public function testEcrireSortie87operationEpargne()
    {
        $dirSortie = '/home/FIDUCIAL/spool_fichiers/test/';
        $releve = file_get_contents(__DIR__ . $this->filePath87);

        $this->manager->setTypeCompte('epargne');

        $this
            ->string($this->manager->ecrireSortie($releve, $dirSortie))
            ->isEqualTo($this->manager->getFileName('pdf'))
            ->boolean(file_exists($dirSortie . $this->manager->getFileName('pdf')))
            ->isTrue()
            ->integer($this->manager->debutOpe)
            ->isEqualTo(20)
            ->integer($this->manager->finOpe)
            ->isEqualTo(192)
            ->integer(count($this->manager->oReleve->getOperations()))
            ->isEqualTo(87)
            ->mock($this->manager)
            ->call('getRLV001')->never()
            ->call('getRLV002')->never()
            ->call('getRLV003')->never()
            ->call('getRLV004')->never()
            ->call('getRLV005')->once()
            ->call('getRLV006')->once()
            ->call('getRLV007')->once()
            ->call('getRLV008')->once()
            ->call('getRLV009')->never()
            ->call('getRLV010')->never()
            ->call('getRLV011')->never()
            ->call('getRLV012')->never()
            ->call('lireReleve')->once()
        ;
    }

    public function testEcrireSortie22operation()
    {
        $dirSortie = '/home/FIDUCIAL/spool_fichiers/test/';
        $releve = file_get_contents(__DIR__ . $this->filePath22);

        $this
            ->string($this->manager->ecrireSortie($releve, $dirSortie))
            ->isEqualTo($this->manager->getFileName('pdf'))
            ->boolean(file_exists($dirSortie . $this->manager->getFileName('pdf')))
            ->isTrue()
            ->integer($this->manager->debutOpe)
            ->isEqualTo(20)
            ->integer($this->manager->finOpe)
            ->isEqualTo(72)
            ->integer(count($this->manager->oReleve->getOperations()))
            ->isEqualTo(28)
            ->mock($this->manager)
            ->call('getRLV001')->never()
            ->call('getRLV002')->never()
            ->call('getRLV003')->never()
            ->call('getRLV004')->never()
            ->call('getRLV005')->never()
            ->call('getRLV006')->once()
            ->call('getRLV007')->never()
            ->call('getRLV008')->never()
            ->call('getRLV009')->once()
            ->call('getRLV010')->never()
            ->call('getRLV011')->never()
            ->call('getRLV012')->never()
            ->call('lireReleve')->once()
        ;
    }

    public function testEcrireSortie15operation()
    {
        $dirSortie = '/home/FIDUCIAL/spool_fichiers/test/';
        $releve = file_get_contents(__DIR__ . $this->filePath15);

        $this
            ->string($this->manager->ecrireSortie($releve, $dirSortie))
            ->isEqualTo($this->manager->getFileName('pdf'))
            ->boolean(file_exists($dirSortie . $this->manager->getFileName('pdf')))
            ->isTrue()
            ->integer($this->manager->debutOpe)
            ->isEqualTo(20)
            ->integer($this->manager->finOpe)
            ->isEqualTo(58)
            ->integer(count($this->manager->oReleve->getOperations()))
            ->isEqualTo(21)
            ->mock($this->manager)
            ->call('getRLV001')->never()
            ->call('getRLV002')->never()
            ->call('getRLV003')->once()
            ->call('getRLV004')->once()
            ->call('getRLV005')->never()
            ->call('getRLV006')->never()
            ->call('getRLV007')->never()
            ->call('getRLV008')->never()
            ->call('getRLV009')->never()
            ->call('getRLV010')->never()
            ->call('getRLV011')->never()
            ->call('getRLV012')->never()
            ->call('lireReleve')->once()
        ;
    }

    public function testEcrireSortie22operationEpargne()
    {
        $dirSortie = '/home/FIDUCIAL/spool_fichiers/test/';
        $releve = file_get_contents(__DIR__ . $this->filePath22);

        $this->manager->setTypeCompte('epargne');

        $this
            ->string($this->manager->ecrireSortie($releve, $dirSortie))
            ->isEqualTo($this->manager->getFileName('pdf'))
            ->boolean(file_exists($dirSortie . $this->manager->getFileName('pdf')))
            ->isTrue()
            ->integer($this->manager->debutOpe)
            ->isEqualTo(20)
            ->integer($this->manager->finOpe)
            ->isEqualTo(72)
            ->integer(count($this->manager->oReleve->getOperations()))
            ->isEqualTo(28)
            ->mock($this->manager)
            ->call('getRLV001')->never()
            ->call('getRLV002')->never()
            ->call('getRLV003')->never()
            ->call('getRLV004')->never()
            ->call('getRLV005')->never()
            ->call('getRLV006')->once()
            ->call('getRLV007')->never()
            ->call('getRLV008')->never()
            ->call('getRLV009')->never()
            ->call('getRLV010')->once()
            ->call('getRLV011')->never()
            ->call('getRLV012')->never()
            ->call('lireReleve')->once()
        ;
    }

    public function testEcrireSortie120operation()
    {
        $dirSortie = '/home/FIDUCIAL/spool_fichiers/test/';
        $releve = file_get_contents(__DIR__ . $this->filePath120);
        $this
            ->string($this->manager->ecrireSortie($releve, $dirSortie))
            ->isEqualTo($this->manager->getFileName('pdf'))
            ->boolean(file_exists($dirSortie . $this->manager->getFileName('pdf')))
            ->isTrue()
            ->integer($this->manager->debutOpe)
            ->isEqualTo(20)
            ->integer($this->manager->finOpe)
            ->isEqualTo(258)
            ->integer(count($this->manager->oReleve->getOperations()))
            ->isEqualTo(120)
            ->mock($this->manager)
            ->call('getRLV001')->never()
            ->call('getRLV002')->never()
            ->call('getRLV003')->never()
            ->call('getRLV004')->once()
            ->call('getRLV005')->never()
            ->call('getRLV006')->once()
            ->call('getRLV007')->once()
            ->call('getRLV008')->exactly(2)
            ->call('getRLV009')->never()
            ->call('getRLV010')->never()
            ->call('getRLV011')->never()
            ->call('getRLV012')->never()
            ->call('lireReleve')->once()
        ;
    }

    public function testEcrireSortie120operationEpargne()
    {
        $dirSortie = '/home/FIDUCIAL/spool_fichiers/test/';
        $releve = file_get_contents(__DIR__ . $this->filePath120);

        $this->manager->setTypeCompte('epargne');

        $this
            ->string($this->manager->ecrireSortie($releve, $dirSortie))
            ->isEqualTo($this->manager->getFileName('pdf'))
            ->boolean(file_exists($dirSortie . $this->manager->getFileName('pdf')))
            ->isTrue()
            ->integer($this->manager->debutOpe)
            ->isEqualTo(20)
            ->integer($this->manager->finOpe)
            ->isEqualTo(258)
            ->integer(count($this->manager->oReleve->getOperations()))
            ->isEqualTo(120)
            ->mock($this->manager)
            ->call('getRLV001')->never()
            ->call('getRLV002')->never()
            ->call('getRLV003')->never()
            ->call('getRLV004')->never()
            ->call('getRLV005')->once()
            ->call('getRLV006')->once()
            ->call('getRLV007')->once()
            ->call('getRLV008')->exactly(2)
            ->call('getRLV009')->never()
            ->call('getRLV010')->never()
            ->call('getRLV011')->never()
            ->call('getRLV012')->never()
            ->call('lireReleve')->once()
        ;
    }

    public function testEcrireSortie8operationDegradee()
    {
        $dirSortie = '/home/FIDUCIAL/spool_fichiers/test/';
        $releve = file_get_contents(__DIR__ . $this->filePath);

        $this
            ->string($this->manager->ecrireSortieDegradee($releve, $dirSortie))
            ->isEqualTo($this->manager->getFileName('pdf'))
            ->boolean(file_exists($dirSortie . $this->manager->getFileName('pdf')))
            ->isTrue()
            ->integer(count($this->manager->oReleve->getOperations()))
            ->isEqualTo(8)
            ->integer($this->manager->debutOpe)
            ->isEqualTo(20)
            ->integer($this->manager->finOpe)
            ->isEqualTo(32)
            ->mock($this->manager)
            ->call('getRLV001')->never()
            ->call('getRLV002')->never()
            ->call('getRLV003')->never()
            ->call('getRLV004')->never()
            ->call('getRLV005')->never()
            ->call('getRLV006')->never()
            ->call('getRLV007')->never()
            ->call('getRLV008')->never()
            ->call('getRLV009')->never()
            ->call('getRLV010')->never()
            ->call('getRLV011')->once()
            ->call('getRLV012')->never()
            ->call('lireReleve')->once()
        ;
    }

    public function testEcrireSortie120operationDegradee()
    {
        $dirSortie = '/home/FIDUCIAL/spool_fichiers/test/';
        $releve = file_get_contents(__DIR__ . $this->filePath120);

        $this
            ->string($this->manager->ecrireSortieDegradee($releve, $dirSortie))
            ->isEqualTo($this->manager->getFileName('pdf'))
            ->boolean(file_exists($dirSortie . $this->manager->getFileName('pdf')))
            ->isTrue()
            ->integer($this->manager->debutOpe)
            ->isEqualTo(20)
            ->integer($this->manager->finOpe)
            ->isEqualTo(258)
            ->integer(count($this->manager->oReleve->getOperations()))
            ->isEqualTo(120)
            ->mock($this->manager)
            ->call('getRLV001')->never()
            ->call('getRLV002')->never()
            ->call('getRLV003')->never()
            ->call('getRLV004')->never()
            ->call('getRLV005')->never()
            ->call('getRLV006')->once()
            ->call('getRLV007')->never()
            ->call('getRLV008')->exactly(2)
            ->call('getRLV009')->never()
            ->call('getRLV010')->never()
            ->call('getRLV011')->never()
            ->call('getRLV012')->once()
            ->call('lireReleve')->once()
        ;
    }
}
