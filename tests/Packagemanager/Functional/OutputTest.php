<?php
namespace IchHabRecht\Packagemanager\Tests\Functional;

use Symfony\Component\Console\Output\BufferedOutput;

class OutputTest extends ComposerTestCase
{
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

}
