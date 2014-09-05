<?php
namespace Amqp\Message;

interface MessageInterface
{
    /**
     * Returns the current routing key
     *
     * @return mixed
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function getRoutingKey();

    /**
     * Sets the routing key
     *
     * @param $key
     *
     * @return bool
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function setRoutingKey($key = null);

    /**
     * Returns the current message attributes
     *
     * @return array
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function getAttributes();

    /**
     * Sets the current message attributes
     *
     * @param array $attributes
     *
     * @return bool
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function setAttributes(array $attributes = array());

    /**
     * Returns the current message flags
     *
     * @return int
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function getFlags();

    /**
     * Sets the current message flags
     *
     * @param int $flags
     *
     * @return mixed
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function setFlags($flags = AMQP_NOPARAM);
}