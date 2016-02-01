<?php
namespace IchHabRecht\Packagemanager\Output;

use Symfony\Component\Console\Output\Output;

class VersionOutput extends Output
{
    /**
     * @var string
     */
    protected $content = '';

    /**
     * @param string $message
     * @param bool $newline
     */
    protected function doWrite($message, $newline)
    {
        $this->content .= $message;

        if ($newline) {
            $this->content .= "\n";
        }
    }

    /**
     * @return string
     */
    public function fetchAll()
    {
        return $this->content;
    }

}
