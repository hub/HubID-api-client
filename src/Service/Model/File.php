<?php
/**
 * @author        Tharanga Kothalawala <tharanga.kothalawala@hubculture.com>
 * @copyright (c) 2020 by HubCulture Ltd.
 */

namespace Hub\HubAPI\Service\Model;

use InvalidArgumentException;

/**
 * This represents a file to be uploaded to the hub id api.
 *
 * @package Hub\HubAPI\Service\Model
 */
final class File
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $absolutePath;

    /**
     * @var string
     */
    private $fileName;

    /**
     * File constructor.
     *
     * @param string $name         Name of the file to be used as the payload key.
     * @param string $absolutePath Absolute file path. Ex: /tmp/test-image.jpg
     * @param string $fileName     [optional] Name of the file to be used instead the actual file name
     *
     * @throws InvalidArgumentException when file is not found or readable.
     */
    public function __construct($name, $absolutePath, $fileName = null)
    {
        if (!is_readable($absolutePath)) {
            throw new InvalidArgumentException(sprintf('File is not readable at location \"%s\"', $absolutePath));
        }
        $this->name = $name;
        $this->absolutePath = $absolutePath;
        $this->fileName = $fileName;
    }

    /**
     * Returns the name of the file.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the absolute path to the file.
     *
     * @return string
     */
    public function getAbsolutePath()
    {
        return $this->absolutePath;
    }

    /**
     * Returns the name of the file to be used instead the actual file name.
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Returns a array representation of the file with file contents as a resource.
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'name' => $this->getName(),
            'contents' => fopen($this->getAbsolutePath(), 'r'),
            'filename' => $this->getFileName(),
        );
    }
}
