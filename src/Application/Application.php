<?php

namespace Documentor\src\Application;

use Documentor\src\Application\Controllers\CodeCoverageController;
use Documentor\src\Application\Controllers\DocumentationController;
use Documentor\src\Application\Controllers\GuideController;
use Documentor\src\Application\Controllers\MainController;
use Documentor\src\Application\Controllers\UnitTestController;
use phpOMS\System\File\Local\Directory;
use phpOMS\Utils\ArrayUtils;
use phpOMS\Utils\StringUtils;

class Application
{
    private $docController = null;
    private $codeCoverageController = null;
    private $unitTestController = null;
    private $guideController = null;
    private $mainController = null;

    public function __construct(array $argv)
    {
        $help        = ArrayUtils::getArg('-h', $argv);
        $source      = ArrayUtils::getArg('-s', $argv);
        $destination = ArrayUtils::getArg('-d', $argv);

        if (isset($help) || !isset($source) || !isset($destination)) {
            $this->printUsage();
        } else {
            $destination = rtrim($destination, '/\\');
            $source      = rtrim($source, '/\\');
            $this->createDocumentation($source, $destination, $argv);
        }
    }

    private function createDocumentation(string $source, string $destination, array $argv)
    {
        $unitTest     = ArrayUtils::getArg('-u', $argv);
        $codeCoverage = ArrayUtils::getArg('-c', $argv);
        $guide        = ArrayUtils::getArg('-g', $argv);
        $base         = ArrayUtils::getArg('-b', $argv) ?? $destination;
        $base         = rtrim($base, '/\\');
        $sources      = new Directory($source, '*');

        $this->mainController         = new MainController($destination, $base);
        $this->codeCoverageController = new CodeCoverageController($destination, $base, $codeCoverage);
        $this->unitTestController     = new UnitTestController($destination, $base, $unitTest);
        $this->docController          = new DocumentationController($destination, $base, $this->codeCoverageController, $this->unitTestController);
        $this->guideController        = new GuideController($destination, $base, $guide);

        $this->parse($sources);
        $this->docController->createTableOfContents();
        $this->docController->createSearchSet();
    }

    private function printUsage()
    {
        echo 'Usage: -s <SOURCE_PATH> -d <DESTINATION_PATH> -c <COVERAGE_PATH>' . "\n\n";
        echo "\t" . '-s Source path of the code to create the documentation from.' . "\n";
        echo "\t" . '-d Destination of the finished documentation.' . "\n";
        echo "\t" . '-c Code coverage xml log generated by `coverage-clover` in PHPUnit.' . "\n";
        echo "\t" . '-u Unit test log generated by `junit` in PHPUnit.' . "\n";
        echo "\t" . '-g Directory containing the html guide.' . "\n";
        echo "\t" . '-b Base uri for web access (e.g. http://www.yoururl.com).' . "\n";
    }

    private function parse(Directory $sources)
    {
        foreach ($sources as $source) {
            if ($source instanceof Directory) {
                $this->parse($source);
            } elseif (StringUtils::endsWith($source->getPath(), '.php')) {
                $this->docController->parse($source);
            }
        }
    }
}