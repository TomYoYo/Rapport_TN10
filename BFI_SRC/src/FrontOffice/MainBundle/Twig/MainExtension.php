<?php
namespace FrontOffice\MainBundle\Twig;

class MainExtension extends \Twig_Extension
{
    public function __construct($container)
    {
        $this->container = $container;
    }

    public function getFilters()
    {
        return array(
            'lastModification' => new \Twig_Filter_Method($this, 'lastModificationFunction'),
            'lastCommit' => new \Twig_Filter_Method($this, 'lastCommitFunction'),
        );
    }

    public function lastModificationFunction($text)
    {
        $pathGit = $this->container->getParameter('pathGit');
        $lines = file($pathGit);
        $lastLine = end($lines);

        $pathVersion = $this->container->getParameter('pathVersion');
        $versionLines = file($pathVersion);
        $version = end($versionLines);

        $lastCommit = substr($lastLine, 41, 40);

        $lastModification = filemtime($pathGit);

        $response = $text .
            ' <small>'.date('d/m/Y H:i:s', $lastModification).'</small>' .
            ' <small>'.$version.'</small>' .
            ' <small>'.$lastCommit.'</small>'
        ;

        return $response;
    }

    public function getName()
    {
        return 'main_extension';
    }
}
