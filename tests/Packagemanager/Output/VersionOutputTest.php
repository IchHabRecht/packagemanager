<?php
namespace IchHabRecht\Packagemanager\Tests;

use IchHabRecht\Packagemanager\Output\VersionOutput;

class ComposerTest extends ComposerTestCase
{
    public function testComposerOutputForRequireIsIntact()
    {
        $versionOutput = new VersionOutput();

        self::callComposer(
            'require',
            false,
            [
                '--no-update',
                'ichhabrecht/packagemanager',
            ],
            null,
            $versionOutput
        );

        $this->assertContains('Using version ^1.0 for ichhabrecht/packagemanager', $versionOutput->fetchAll());
    }
}
