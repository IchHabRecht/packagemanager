<?php
namespace IchHabRecht\Packagemanager\Tests\Functional;

use Composer\Config;
use Composer\Console\Application;
use Composer\Util\Filesystem;
use IchHabRecht\Packagemanager\Tests\TestCase;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class ComposerTestCase extends TestCase
{
    /**
     * @var bool
     */
    protected static $composerIsAvailable = false;

    /**
     * @var string
     */
    protected static $tempDirectory = null;

    /**
     * @var array
     */
    protected static $workingDirectory = [];

    public static function setUpBeforeClass()
    {
        // Reset packagist repository
        Config::$defaultRepositories = [
            'packagemanager-1-0' => [
                'type' => 'package',
                'package' => json_decode(file_get_contents(
                    implode(
                        DIRECTORY_SEPARATOR,
                        [
                            __DIR__,
                            'Fixtures',
                            'Repository',
                            'packagemanager',
                            '1-0.json',
                        ]
                    )
                ), true),
            ],
            'packagemanager-2-0' => [
                'type' => 'package',
                'package' => json_decode(file_get_contents(
                    implode(
                        DIRECTORY_SEPARATOR,
                        [
                            __DIR__,
                            'Fixtures',
                            'Repository',
                            'packagemanager',
                            '2-0.json',
                        ]
                    )
                ), true),
            ],
            'packagemanager-3-0' => [
                'type' => 'package',
                'package' => json_decode(file_get_contents(
                    implode(
                        DIRECTORY_SEPARATOR,
                        [
                            __DIR__,
                            'Fixtures',
                            'Repository',
                            'packagemanager',
                            '3-0.json',
                        ]
                    )
                ), true),
            ],
        ];

        self::$tempDirectory = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $workingDirectory = self::createNewWorkingDirectory();

        self::callComposer('init');

        if (file_exists($workingDirectory . 'composer.json')) {
            unlink($workingDirectory . 'composer.json');
            self::$composerIsAvailable = true;
        }
        rmdir($workingDirectory);
        self::$workingDirectory = [];
    }

    protected function setUp()
    {
        if (!self::$composerIsAvailable) {
            $this->markTestSkipped('No composer executable found or ended with error');
        }

        parent::setUp();
    }

    protected function tearDown()
    {
        if (!empty(self::$workingDirectory)) {
            $filesystem = new Filesystem();
            foreach (self::$workingDirectory as $workingDirectory) {
                $filesystem->removeDirectory($workingDirectory);
            }
            self::$workingDirectory = [];
        }
        parent::tearDown();
    }

    /**
     * @return string
     */
    protected static function createNewWorkingDirectory()
    {
        $workingDirectory = self::$workingDirectory[] = self::$tempDirectory . time() . mt_rand(0, 1000) . DIRECTORY_SEPARATOR;
        mkdir($workingDirectory);

        return $workingDirectory;
    }

    /**
     * @return string
     */
    protected static function getCurrentWorkingDirectory()
    {
        return empty(self::$workingDirectory) ? self::createNewWorkingDirectory() : static::$workingDirectory[count(static::$workingDirectory) - 1];
    }

    /**
     * @return string
     */
    protected static function createComposerJson()
    {
        $composerFile = self::getCurrentWorkingDirectory() . 'composer.json';
        touch($composerFile);

        return $composerFile;
    }

    /**
     * @param string $command
     * @param bool $quiet
     * @param array $arguments
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected static function callComposer($command, $quiet = true, array $arguments = [], InputInterface $input = null, OutputInterface $output = null)
    {
        self::setServerArguments($command, $quiet, $arguments);

        $application = new Application();
        $application->setAutoExit(false);

        return $application->run($input, $output);
    }

    /**
     * @param string $command
     * @param bool $quite
     * @param array $arguments
     */
    protected static function setServerArguments($command, $quite, array $arguments = [])
    {
        $inputArguments = array_merge([
            'composer',
            $command,
            $quite ? '--quiet' : '',
            '--no-interaction',
            '--working-dir',
            self::getCurrentWorkingDirectory(),
        ], $arguments);

        $inputArguments = array_filter($inputArguments, 'strlen');

        $_SERVER['argv'] = $inputArguments;
    }
}
