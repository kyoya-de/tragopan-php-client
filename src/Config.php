<?php
/**
 * This file is part of a marmalade GmbH project
 * It is not Open Source and may not be redistributed.
 * For contact information please visit http://www.marmalade.de
 *
 * @version    0.1
 * @author     Stefan Krenz <krenz@marmalade.de>
 * @link       http://www.marmalade.de
 */

namespace KyoyaDe\Tragopan\PhpClient;

use itguy614\Support\DotArray;
use JMS\Serializer\SerializerInterface;

class Config
{
    public static function load(SerializerInterface $serializer, $file)
    {
        $serialized = file_get_contents($file);
        $unserialized = $serializer->deserialize($serialized, 'array', 'json');
        return DotArray::newDotArray($unserialized);
    }
}
