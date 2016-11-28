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

trait ConfigAwareTrait
{
    protected $config;

    /**
     * @param DotArray|array $config
     */
    public function setConfig($config)
    {
        $this->config = DotArray::newDotArray($config);
    }
}
