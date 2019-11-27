<?php
/**
 * @author  Tharanga Kothalawala <tharanga.kothalawala@hubculture.com>
 * @since   16-09-2018
 */

namespace Hub\HubAPI\Service\Exception;

use RuntimeException;

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
    public $errors;

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
