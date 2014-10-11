<?php
/**
 * Interface providing various decoding implementations like json, serialized, etc.
 * Useful for translating configurations into php objects.
 *
 * @author Cristian Datculescu <cristian.datculescu@westwing.de>
 */
namespace Amqp\Config\Iface;

interface Decoder
{
    /**
     * Decodes the given string based on the actual implementation
     *
     * @param string $string The string to be decoded
     * @return mixed
     *
     * @throws Exception If the string cannot be decoded using the current decoder implementation
     */
    public function decode($string);
}