<?php
/*
 * This file is part of the eclipxe/cfdi library.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Carlos C Soto <eclipxe13@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link https://github.com/eclipxe13/cfdi GitHub
 * @link https://github.com/orlandocharles/cfdi Original project
 */
namespace PhpCfdi\CFDITests;

use CfdiUtils\Certificado;
use PhpCfdi\CFDI\CFDI;
use PhpCfdi\CFDI\Node\Concepto;
use PhpCfdi\CFDI\Node\Emisor;
use PhpCfdi\CFDI\Node\Impuesto\Traslado;
use PhpCfdi\CFDI\Node\InformacionAduanera;
use PhpCfdi\CFDI\Node\Receptor;
use PhpCfdi\CFDI\Validator;
use PhpCfdi\CFDI\XmlResolver;
use PHPUnit\Framework\TestCase;

/**
 * This is a class that test that a full invoice can be created with correct data.
 *
 * 1) Gamepad         4 x 500       = 2000 (iva: 240, descuento: 500)
 * 2) Digital screeen 1 x 1000      = 1000 (iva: 160)
 * 3) Flete           1 x 300       =  300 (iva:  48)
 * Subtotal:                          3300
 * Descuento:                          500
 * IVA:                                448
 * Total:                             3248
 */
class FullInvoiceCaseTest extends TestCase
{
    public function testCreateFullInvoice()
    {
        $now = strtotime('2017-10-13 15:16:17'); // friday 13th muahaha!
        $cerfile = Util::asset('certs/CSD01_AAA010101AAA.cer');
        $keyfile = Util::asset('certs/CSD01_AAA010101AAA.key.pem');
        $certificado = new Certificado($cerfile);

        // create the cfdi
        $resolver = new XmlResolver();
        $cfdi = new CFDI([
            'Serie' => 'XXX',
            'Folio' => '0000123456',
            'Fecha' => date('Y-m-d\TH:i:s', $now),
            'FormaPago' => '01', // efectivo
            'Moneda' => 'USD',
            'TipoCambio' => '18.9008', // taken from banxico
            'TipoDeComprobante' => 'I', // ingreso
            'MetodoPago' => 'PUE', // Pago en una sola exhibición
            'LugarExpedicion' => '52000',
            'SubTotal' => '3300', // TODO: calculate (?)
            'Descuento' => '500', // TODO: calculate (?)
            'Total' => '3248', // TODO: calculate (?)
        ], $resolver);

        // add certificate, this add NoCertificado and Certificado attributes
        $cfdi->addCertificado($certificado);

        // add emisor (take data from certificado)
        $cfdi->add(new Emisor([
            'Rfc' => $certificado->getRfc(),
            'Nombre' => $certificado->getName(),
            'RegimenFiscal' => '601', // General de Ley Personas Morales
        ]));

        // add receptor
        $cfdi->add(new Receptor([
            'Rfc' => 'COSC8001137NA',
            'Nombre' => 'Carlos Cortés Soto', // note is an "e" with accent
            'UsoCFDI' => 'G01', // Adquisición de mercancias
        ]));

        // add concepto #1
        $concepto = new Concepto([
            'ClaveProdServ' => '52161557', // Consola portátil de juegos de computador
            'NoIdentificacion' => 'GAMEPAD007',
            'Cantidad' => '4',
            'ClaveUnidad' => 'H87', // Pieza
            'Unidad' => 'PIEZA',
            'Descripcion' => 'Portable tetris gamepad pro++',
            'ValorUnitario' => '500',
            'Importe' => '2000', // TODO: calculate (?)
            'Descuento' => '500', // hot sale: take 4, pay only 3
        ]);
        $concepto->add(new Traslado([
            'Base' => '1500',
            'Impuesto' => '002', // IVA
            'TipoFactor' => 'Tasa', // this is a catalog
            'TasaOCuota' => '0.16',
            'Importe' => '240', // TODO: calculate (?)
        ]));
        // this is an imported product sale by first hand to MEX
        $concepto->add(new InformacionAduanera([
            'NumeroPedimento' => '17  24  3420  7010987',
        ]));
        $concepto->add(new InformacionAduanera([
            'NumeroPedimento' => '17  24  3420  7010123',
        ]));
        $cfdi->add($concepto);

        // add concepto #2
        $concepto = new Concepto([
            'ClaveProdServ' => '43211914', // Pantalla pasiva lcd
            'NoIdentificacion' => 'SCREEN5004',
            'Cantidad' => '1',
            'ClaveUnidad' => 'H87', // Pieza
            'Unidad' => 'PIEZA',
            'Descripcion' => 'Pantalla led 3x4" con entrada HDMI',
            'ValorUnitario' => '1000',
            'Importe' => '1000', // TODO: calculate (?)
        ]);
        $concepto->add(new Traslado([
            'Base' => '1000',
            'Impuesto' => '002', // IVA
            'TipoFactor' => 'Tasa', // this is a catalog
            'TasaOCuota' => '0.16',
            'Importe' => '160', // TODO: calculate (?)
        ]));
        $cfdi->add($concepto);

        // concepto #3 (freight)
        $concepto = new Concepto([
            // - Servicios de Transporte, Almacenaje y Correo
            //   - Manejo y embalaje de material
            //     - Servicios de manejo de materiales
            //       - Tarifa de los fletes
            'ClaveProdServ' => '78121603', // Tarifa de los fletes
            'NoIdentificacion' => 'FLETE-MX',
            'Cantidad' => '1',
            'ClaveUnidad' => 'E48', // Unidad de servicio
            'Unidad' => 'SERVICIO',
            'Descripcion' => 'Servicio de envío de mercancías',
            'ValorUnitario' => '300',
            'Importe' => '300', // TODO: calculate (?)
        ]);
        $concepto->add(new Traslado([
            'Base' => '300',
            'Impuesto' => '002', // IVA
            'TipoFactor' => 'Tasa', // this is a catalog
            'TasaOCuota' => '0.16',
            'Importe' => '48', // TODO: calculate (?)
        ]));
        $cfdi->add($concepto);

        // add impuesto,  TODO: calculate (?)
        $cfdi->add(new Traslado([
            // Traslado
            'Impuesto' => '002', // IVA
            'TipoFactor' => 'Tasa', // this is a catalog
            'TasaOCuota' => '0.16',
            'Importe' => '448', // TODO: calculate (?)
        ], [
            // Traslados
        ], [
            // Impuestos
            'TotalImpuestosTrasladados' => '448',
        ]));

        // add private key before save the xml, this will allow to create the sello
        $cfdi->setPrivateKey(file_get_contents($keyfile));

        $expectedContentsFile = Util::asset('cases/sale-with-discount.xml');
        $this->assertXmlStringEqualsXmlFile($expectedContentsFile, $cfdi->getXML());
    }
}
