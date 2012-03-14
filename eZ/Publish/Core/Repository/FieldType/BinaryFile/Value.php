<?php
/**
 * File containing the BinaryFile Value class
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Publish\Core\Repository\FieldType\BinaryFile;
use eZ\Publish\Core\Repository\FieldType\ValueInterface,
    eZ\Publish\Core\Repository\FieldType\Value as BaseValue,
    eZ\Publish\API\Repository\IOService,
    ezp\Base\Exception\PropertyNotFound;

/**
 * Value for BinaryFile field type
 *
 * @property-read string $filename The internal name of the file (generated by the system)
 * @property-read string $mimeType The MIME type of the file (for example "audio/wav").
 * @property-read string $mimeTypeCategory The MIME type category (for example "audio").
 * @property-read string $mimeTypePart The MIME type part (for example "wav").
 * @property-read int $filesize The size of the file (number of bytes).
 * @property-read string $filepath The path to the file (including the filename).
 */
class Value extends BaseValue implements ValueInterface
{
    /**
     * BinaryFile object
     *
     * @var \eZ\Publish\API\Repository\Values\IO\BinaryFile
     */
    public $file;

    /**
     * Original file name
     *
     * @var string
     */
    public $originalFilename;

    /**
     * Number of times the file has been downloaded through content/download module
     *
     * @var int
     */
    public $downloadCount = 0;

    /**
     * @var \eZ\Publish\Core\Repository\FieldType\BinaryFile\Handler
     */
    protected $handler;

    /**
     * Construct a new Value object.
     * To affect a BinaryFile object to the $file property, use the handler:
     * <code>
     * use \eZ\Publish\Core\Repository\FieldType\BinaryFile;
     * $binaryValue = new BinaryFile\Value;
     * $binaryValue->file = $binaryValue->getHandler()->createFromLocalPath( '/path/to/local/file.txt' );
     * </code>
     *
     * @param \eZ\Publish\API\Repository\IOService $IOService
     * @param string|null $file
     */
    public function __construct( IOService $IOService, $file = null )
    {
        $this->handler = new Handler( $IOService );
        if ( $file !== null )
        {
            $this->file = $this->handler->createFromLocalPath( $file );
            $this->originalFilename = basename( $file );
        }
    }

    /**
     * @see \eZ\Publish\Core\Repository\FieldType\Value
     * @return \eZ\Publish\Core\Repository\FieldType\BinaryFile\Value
     */
    public static function fromString( $stringValue )
    {
        return new static( $stringValue );
    }

    /**
     * @see \eZ\Publish\Core\Repository\FieldType\Value
     */
    public function __toString()
    {
        if ( !isset( $this->file->id ) )
            return "";

        return $this->file->id;
    }

    public function __get( $name )
    {
        switch ( $name )
        {
            case 'filename':
                return basename( $this->file->id );

            case 'mimeType':
                return $this->file->contentType;

            case 'filesize':
                return $this->file->size;

            case 'filepath':
                return $this->file->id;

            default:
                throw new PropertyNotFound( $name, get_class( $this ) );
        }
    }

    /**
     * @see \eZ\Publish\Core\Repository\FieldType\ValueInterface::getTitle()
     */
    public function getTitle()
    {
        return $this->originalFilename;
    }
}
