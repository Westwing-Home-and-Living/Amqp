<?php
/**
 * File reader implementing the reader interface
 *
 * @author Cristian Datculescu <cristian.datculescu@wwstwing.de>
 */
namespace Amqp\Config\Reader;

use Amqp\Config\Iface\Reader;

class File implements Reader
{
    /**
     * The file to be read
     * @var string
     */
    protected $file;

    /**
     * @param $file string
     * @throws Exception If the file does not exist
     *                   If the file cannot be read
     */
    public function __construct($file)
    {
        if (!file_exists($file)) {
            throw new Exception("The file " . $file . " does not exist!");
        }

        if (!is_readable($file)) {
            throw new Exception("The file " . $file . " cannot be read!");
        }
        $this->file = $file;
    }

    public function read()
    {
        $contents = file_get_contents($this->file);
        return $contents;
    }
}