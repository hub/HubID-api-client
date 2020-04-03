<?php
/**
 * @author  Tharanga Kothalawala <tharanga.kothalawala@hubculture.com>
 * @since   16-09-2018
 */

namespace Hub\HubAPI\Service\Exception;

use RuntimeException;
use Throwable;

/**
 * This represents any API errors and will be thrown if any.
 *
 * @package Hub\HubAPI\Service\Exception
 */
class HubIdApiException extends RuntimeException
{
    /**
     * @var array
     */
    private $errors;

    /**
     * @var string This is the called API during the time this exception is thrown.
     */
    private $calledApi;

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->addError($message);
    }

    /**
     * Returns the called API during the time this exception is thrown.
     *
     * @return string
     */
    public function getCalledApi()
    {
        return $this->calledApi;
    }

    /**
     * Set the called API during the time this exception is thrown.
     *
     * @param string $calledApi The called API url.
     */
    public function setCalledApi($calledApi)
    {
        $this->calledApi = $calledApi;
    }

    /**
     * Use this to add a single error message.
     *
     * @param string $errorMessage The error message that you want to add.
     */
    public function addError($errorMessage)
    {
        $this->errors[] = $errorMessage;
    }

    /**
     * Use this to set a list of errors.
     *
     * @param array $errors
     */
    public function setErrors(array $errors)
    {
        $this->errors = $errors;
    }

    /**
     * Use this to get any errors.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
