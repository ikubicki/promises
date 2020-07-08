<?php

namespace Irekk\Promises;

use Closure;

class Promise
{
    
    const STATE_PENDING = 0;
    const STATE_RESOLVED = 1;
    const STATE_REJECTED = -1;
    
    /**
     * @var array $callbacks
     */
    protected $callbacks = [];
    
    /**
     * @var Closure $catch
     */
    protected $catch;
    
    /**
     * @var integer $state
     */
    protected $state = self::STATE_PENDING;
    
    /**
     * @var mixed $result
     */
    protected $result;
    
    /**
     * @var string $reason
     */
    protected $reason;
    
    /**
     * 
     * @author ikubicki
     * @param Closure $callback
     */
    public function __construct($callback = null)
    {
        if ($callback) {
            $this->then($callback);
        }
    }

    /**
     * 
     * @author ikubicki
     * @return string
     */
    public function __toString()
    {
        return json_encode($this->resolve());
    }

    /**
     * 
     * @author ikubicki
     * @param Closure $callback
     * @return Promise
     */
    public function then($callback)
    {
        $this->callbacks[] = $callback;
        return $this;
    }
    
    /**
     * 
     * @author ikubicki
     * @return mixed|boolean
     */
    public function resolve()
    {
        if ($this->isPending()) {
            $arguments = func_get_args();
            array_unshift($arguments, null, $this);
            foreach ($this->callbacks as $callback) {
                $result = call_user_func_array($callback, $arguments);
                if ($this->isRejected()) {
                    return $this->triggerRejection();
                }
                $arguments[0] = $this->result = $result;
            }
            $this->state = self::STATE_RESOLVED;
            return $this->result;
        }
        if ($this->isRejected()) {
            return false;
        }
        if ($this->isResolved()) {
            return $this->result;
        }
    }

    /**
     * 
     * @author ikubicki
     * @param Closure $callback
     * @return Promise
     */
    public function promise($callback)
    {
        return new self($callback);
    }
    
    /**
     * 
     * @author ikubicki
     * @param string $reason
     */
    public function reject($reason = null)
    {
        $this->reason = $reason;
        $this->state = self::STATE_REJECTED;
    }

    
    /**
     * 
     * @author ikubicki
     * @param Closure $callback
     * @return Promise
     */
    public function catch($callback)
    {
        $this->catch = $callback;
        return $this;
    }
    
    /**
     * 
     * @author ikubicki
     * @return boolean
     */
    public function isPending()
    {
        return $this->state == self::STATE_PENDING;
    }
    
    /**
     * 
     * @author ikubicki
     * @return boolean
     */
    public function isResolved()
    {
        return $this->state == self::STATE_RESOLVED;
    }
    
    /**
     * 
     * @author ikubicki
     * @return boolean
     */
    public function isRejected()
    {
        return $this->state == self::STATE_REJECTED;
    }

    /**
     * 
     * @author ikubicki
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * 
     * @author ikubicki
     * @return mixed
     * @throws Exceptions\RejectedPromise
     */
    protected function triggerRejection()
    {
        if ($this->catch) {
            $result = call_user_func_array($this->catch, [$this->getReason(), $this->result]);
            $this->result = null;
            return $result;
        }
        $this->result = null;
        throw new Exceptions\RejectedPromise($this->getReason());
    }
}