<?php

/*
 * This file is part of the CFDI project.
 *
 * (c) Orlando Charles <me@orlandocharles.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Charles\CFDI\Node\Complemento\Pagos\Impuesto;

/**
 * This is the retención class.
 *
 * @author Orlando Charles <me@orlandocharles.com>
 */
class Retencion extends Impuesto
{
    /**
     * Parent node name.
     *
     * @var string
     */
    protected $parentNodeName = 'pago10:Retenciones';

    /**
     * Node name.
     *
     * @var string
     */
    protected $nodeName = 'pago10:Retencion';
}
