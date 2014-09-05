<?php
namespace Amqp\Configuration;

class Queue
{
    /**
     * The queue configuration
     * @var array
     */
    protected $queueConfiguration = array();

    /**
     * There is no required value for the queue
     *
     * @param array $configuration
     *
     * @return bool
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function setConfig(array $configuration = array())
    {
        $this->queueConfiguration = $configuration;

        return true;
    }

    /**
     * Returns the name of the queue if it is declared
     *
     * @return null|string
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function getName()
    {
        if (isset($this->queueConfiguration['name'])) {
            $name = $this->queueConfiguration['name'];
        } else {
            $name = NULL;
        }

        if (isset($this->queueConfiguration['callable_name']) && $this->queueConfiguration['callable_name'] === true) {
            if (is_callable($name)) {
                return call_user_func($name);
            }
        } else {
            return $name;
        }

        return null;
    }

    /**
     * Returns the queue arguments if they are set
     *
     * @return null|mixed
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function getArguments()
    {
        if (isset($this->queueConfiguration['arguments'])) {
            return $this->queueConfiguration['arguments'];
        }

        return array();
    }

    /**
     * Returns the queue flags if they are set
     *
     * @return null|mixed
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function getFlags()
    {
        if (isset($this->queueConfiguration['flags'])) {
            $flags = $this->queueConfiguration['flags'];
            if (is_array($flags)) {
                $bitmask = 0;
                foreach ($flags as $flag) {
                    if (defined($flag)) {
                        $localFlag = constant($flag);
                        $bitmask |= $localFlag;
                    }
                }

                return $bitmask;
            } else {
                if (defined($flags)) {
                    return constant($flags);
                }
            }
        }

        return null;
    }

    /**
     * Returns the routing key if is set.
     *
     * @return null|string
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function getRoutingKey()
    {
        if (isset($this->queueConfiguration['routing_key'])) {
            $routingKey = $this->queueConfiguration['routing_key'];
        } else {
            $routingKey = NULL;
        }

        if (isset($this->queueConfiguration['callable_routing_key'])
            && $this->queueConfiguration['callable_routing_key'] === true
        ) {
            if (is_callable($routingKey)) {
                return call_user_func($routingKey);
            }
        }

        return null;
    }

    /**
     * Returns the additional binding arguments if they are defined
     *
     * @return array
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function getBindingArguments()
    {
        if (isset($this->queueConfiguration['binding_arguments'])) {
            return $this->queueConfiguration['binding_arguments'];
        }

        return array();
    }
}