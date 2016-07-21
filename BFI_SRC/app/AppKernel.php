<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new WhiteOctober\PagerfantaBundle\WhiteOctoberPagerfantaBundle(),
            new Ps\PdfBundle\PsPdfBundle(),
            new FrontOffice\MainBundle\FrontOfficeMainBundle(),
            new Editique\CreditBundle\EditiqueCreditBundle(),
            new Editique\MasterBundle\EditiqueMasterBundle(),
            new Editique\ReleveBundle\EditiqueReleveBundle(),
            new Editique\RIBBundle\EditiqueRIBBundle(),
            new Editique\DATBundle\EditiqueDATBundle(),
            new Editique\LivretBundle\EditiqueLivretBundle(),
            new BackOffice\MonitoringBundle\BackOfficeMonitoringBundle(),
            new BackOffice\ParserBundle\BackOfficeParserBundle(),
            new BackOffice\ConnexionBundle\BackOfficeConnexionBundle(),
            new BackOffice\FileBundle\BackOfficeFileBundle(),
            new BackOffice\ActionBundle\BackOfficeActionBundle(),
            new BackOffice\UserBundle\BackOfficeUserBundle(),
            new Fiscalite\ODBundle\FiscaliteODBundle(),
            new Editique\CompteBundle\EditiqueCompteBundle(),
            new Editique\LettreBundle\EditiqueLettreBundle(),
            new BackOffice\CleanBundle\BackOfficeCleanBundle(),
            new Editique\TitreBundle\EditiqueTitreBundle(),
            new Monetique\CardBundle\MonetiqueCardBundle(),
            new BackOffice\EvolutionBundle\BackOfficeEvolutionBundle(),
            new Fiscalite\EtatsBundle\FiscaliteEtatsBundle(),
            new Editique\FiscalBundle\EditiqueFiscalBundle(),
            new Fiscalite\BudgetBundle\FiscaliteBudgetBundle(),
            new BackOffice\HabilitationBundle\BackOfficeHabilitationBundle(),
            new Transverse\PartenaireBundle\TransversePartenaireBundle(),
            new BackOffice\CustomerBundle\BackOfficeCustomerBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new atoum\AtoumBundle\AtoumAtoumBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config/config_' . $this->getEnvironment() . '.yml');
    }

}
