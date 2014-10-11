<?php
/**
 * Interface providing the methods for constructing various objects
 *
 * @author Cristian Datculescu <cristian.datculescu@westwing.de>
 */
namespace Amqp\Builder\Iface;

interface Builder
{
    /**
     * Builds the requested object and returns it
     *
     * @param string $name The name of the instance to be built
     * @return object
     *
     * @throw Exception If the instance cannot be built from the provided configuration
     */
    public function build($name);
}