<?php

declare(strict_types=1);

namespace Emsifa\Graphit;

use GraphQL\Error\Error;
use GraphQL\Error\InvariantViolation;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Utils\Utils;
use Psr\Http\Message\UploadedFileInterface;

class FileType extends ScalarType
{
    /**
     * @var string
     */
    public $name = 'File';

    /**
     * @var string
     */
    public $description = 'Special type represents a file to be uploaded in the same HTTP request as specified by [graphql-multipart-request-spec](https://github.com/jaydenseric/graphql-multipart-request-spec).';

    /**
     * @var array
     */
    protected $mimes = [];

    /**
     * @var int
     */
    protected $minSize = 0;

    /**
     * @var int
     */
    protected $maxSize = 0;

    function __construct(array $config = [])
    {
        parent::__construct($config);
        Utils::assertValidName($this->name);

        if (isset($config['mimes'])) {
            if (!is_array($config['mimes'])) {
                throw new \UnexpectedValueException("Config 'mimes' must be array.");
            }
            $this->mimes = $config['mimes'];
        }

        if (isset($config['minSize'])) $this->minSize = (int) $config['minSize'];
        if (isset($config['maxSize'])) $this->maxSize = (int) $config['maxSize'];
    }

    /**
     * Serializes an internal value to include in a response.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function serialize($value)
    {
        throw new InvariantViolation('`'. $this->name .'` cannot be serialized');
    }

    /**
     * Parses an externally provided value (query variable) to use as an input
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function parseValue($value)
    {
        if (!$value instanceof UploadedFileInterface) {
            throw new \UnexpectedValueException('Could not get uploaded file, be sure to conform to GraphQL multipart request specification. Instead got: ' . Utils::printSafe($value));
        }

        $this->validate($value);

        return $value;
    }

    /**
     * Parses an externally provided literal value (hardcoded in GraphQL query) to use as an input
     *
     * @param \GraphQL\Language\AST\Node $valueNode
     *
     * @return mixed
     */
    public function parseLiteral($valueNode)
    {
        throw new Error('`'. $this->name .'` cannot be hardcoded in query, be sure to conform to GraphQL multipart request specification. Instead got: ' . $valueNode->kind, [$valueNode]);
    }

    protected function validate(UploadedFileInterface $file)
    {
        if (!empty($this->mimes)) {
            $this->validateMimes($file);
        }

        if ($this->minSize) {
            $this->validateMinSize($file);
        }

        if ($this->maxSize) {
            $this->validateMaxSize($file);
        }
    }

    protected function validateMimes(UploadedFileInterface $file)
    {
        $tmpFile = $file->getStream()->getMetadata('uri');
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $tmpFile);
        finfo_close($finfo);

        if (!in_array($mime, $this->mimes)) {
            $mimes = implode('|', $this->mimes);
            throw new Error("File only can be {$mimes}. Instead got: {$mime}.");
        }
    }

    protected function validateMinSize(UploadedFileInterface $file)
    {
        if ($file->getSize() < $this->minSize) {
            throw new Error("File size too small. Minimum file size is {$this->minSize}B");
        }
    }

    protected function validateMaxSize(UploadedFileInterface $file)
    {
        if ($file->getSize() > $this->maxSize) {
            throw new Error("File size too large. Maximum file size is {$this->maxSize}B");
        }
    }

}
