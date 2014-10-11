<?php
/**
 * Interface specifying methods for building readers (memory, file, database, etc)
 *
 * @author Cristian Datculescu <cristian.datculescu@westwing.de>
 */
namespace Amqp\Config\Iface;

interface Reader
{
    /**
     * Read and return the contents read
     *
     * @return string Read values
     */
    public function read();
}