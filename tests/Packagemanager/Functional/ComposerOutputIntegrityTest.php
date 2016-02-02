<?php
namespace IchHabRecht\Packagemanager\Tests\Functional;

use Composer\Config;
use Symfony\Component\Console\Output\BufferedOutput;

class ComposerOutputIntegrity extends ComposerTestCase
{
    protected function setUp()
    {
        parent::setUp();

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
        ];
    }

    public function testComposerOutputForRequireIsIntact()
    {
        $bufferedOutput = new BufferedOutput();

        self::callComposer(
            'require',
            false,
            [
                '--no-update',
                'ichhabrecht/packagemanager',
            ],
            null,
            $bufferedOutput
        );

        $this->assertContains('Using version ^1.0 for ichhabrecht/packagemanager', $bufferedOutput->fetch());
    }

    public function testComposerOutputForInstallIsIntact()
    {
        $composerFile = self::createComposerJson();
        $fileHandle = fopen($composerFile, 'wb');
        fwrite($fileHandle, json_encode([
            'require' => [
                'ichhabrecht/packagemanager' => '1.0',
            ],
        ]));
        fclose($fileHandle);

        $bufferedOutput = new BufferedOutput();

        self::callComposer(
            'install',
            false,
            [
                '--dry-run',
            ],
            null,
            $bufferedOutput
        );

        $this->assertContains('  - Installing ichhabrecht/packagemanager (1.0)', $bufferedOutput->fetch());
    }

    public function testComposerOutputForUpdateIsIntact()
    {
        $composerFile = self::createComposerJson();
        $fileHandle = fopen($composerFile, 'wb');
        fwrite($fileHandle, json_encode([
            'require' => [
                'ichhabrecht/packagemanager' => '1.0',
            ],
        ]));
        fclose($fileHandle);

        $bufferedOutput = new BufferedOutput();

        self::callComposer(
            'update',
            false,
            [
                '--dry-run',
            ],
            null,
            $bufferedOutput
        );

        $this->assertContains('  - Installing ichhabrecht/packagemanager (1.0)', $bufferedOutput->fetch());
    }

}
