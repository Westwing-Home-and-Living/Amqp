<?php
namespace Amqp\Message;

abstract class AbstractCollection implements \ArrayAccess, \Iterator, \Countable, \Serializable
{
    /**
     * The internal array holding the values
     * @var array
     */
    protected $internalArray = array();

    /**
     * Current iterator position, default 0
     * @var int
     */
    protected $position = 0;

    /**
     * Rewinds back to the initial position.
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * Returns the current key
     *
     * @return int|mixed
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * Increase the internal position
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function next()
    {
        ++$this->position;
    }

    /**
     * Is the current position valid in the array?
     *
     * @return bool
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function valid()
    {
        return isset($this->internalArray[$this->position]);
    }

    /**
     * Does the offset needed exists?
     *
     * @param mixed $offset
     *
     * @return bool
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function offsetExists($offset)
    {
        if (isset($this->internalArray[$offset])) {
            return true;
        }
        return false;
    }

    /**
     * Unsets a key from the internal array
     *
     * @param mixed $offset
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function offsetUnset($offset)
    {
        if ($this->offsetExists($offset)) {
            unset($this->internalArray[$offset]);
        }
    }

    /**
     * Counts the number of elements in the array
     *
     * @return int
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function count()
    {
        return count($this->internalArray);
    }
}