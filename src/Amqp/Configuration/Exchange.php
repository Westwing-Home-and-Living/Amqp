<?php
namespace Amqp\Configuration;

use Amqp\Exception\ConfigurationException;

class Exchange
{
    protected $exchangeConfiguration = array();

    /**
     * Exchange needs at least the name in order to be usable
     *
     * @param array $configuration
     *
     * @throws ConfigurationException If the name of the exchange is not configured
     */
    public function __construct(array $configuration)
    {
        if (!isset($configuration['name'])) {
            throw new ConfigurationException(
                "Missing required value for exchange configuration: name"
            );
        }

        if (!isset($configuration['type'])) {
            throw new ConfigurationException(
                "Missing required value for exchange configuration: type"
            );
        }
        $this->exchangeConfiguration = $configuration;
    }

    /**
     * Allows overriding the configuration.
     *
     * @param array $configuration
     * @throws \Amqp\Exception\ConfigurationException If the name of the exchange is not
     *                                       found
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function setConfig(array $configuration = array())
    {
        if (!isset($configuration['name'])) {
            throw new ConfigurationException(
                "Missing required value for exchange configuration: name"
            );
        }

        if (!isset($configuration['type'])) {
            throw new ConfigurationException(
                "Missing required value for exchange configuration: type"
            );
        }
        $this->exchangeConfiguration = $configuration;
    }

    /**
     * Returns the name of the exchange
     *
     * @return mixed
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function getName()
    {
        return $this->exchangeConfiguration['name'];
    }

    /**
     * Returns the current exchange flags
     *
     * @return null
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function getFlags()
    {
        if (isset($this->exchangeConfiguration['flags'])) {
            $flags = $this->exchangeConfiguration['flags'];
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
     * Returns the current exchange arguments
     *
     * @return null
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function getArguments()
    {
        if (isset($this->exchangeConfiguration['arguments'])) {
            return $this->exchangeConfiguration['arguments'];
        }

        return array();
    }

    /**
     * Returns the current exchange type
     *
     * @return null
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function getType()
    {
        if (defined($this->exchangeConfiguration['type'])) {
            $type = constant($this->exchangeConfiguration['type']);
        } else {
            $type = AMQP_EX_TYPE_DIRECT;
        }
        return $type;
    }
}