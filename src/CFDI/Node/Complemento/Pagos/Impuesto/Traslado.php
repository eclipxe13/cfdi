<?php

/*
 * This file is part of the eclipxe13/cfdi library.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Carlos C Soto <eclipxe13@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link https://github.com/eclipxe13/cfdi GitHub
 * @link https://github.com/orlandocharles/cfdi Original project
 */
namespace Charles\CFDI\Node\Complemento\Pagos\Impuesto;

/**
 * This is the traslado class.
 *
 * @author Orlando Charles <me@orlandocharles.com>
 */
class Traslado extends Impuesto
{
    /**
     * Parent node name.
     *
     * @var string
     */
    protected $parentNodeName = 'pago10:Traslados';

    /**
     * Node name.
     *
     * @var string
     */
    protected $nodeName = 'pago10:Traslado';
}
