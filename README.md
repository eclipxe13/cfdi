# Comprobante Fiscal Digital por Internet (CFDI v3.3)

[![Travis](https://img.shields.io/travis/orlandocharles/cfdi.svg?style=flat-square)](https://travis-ci.org/orlandocharles/cfdi) [![Gitter](https://img.shields.io/gitter/room/nwjs/nw.js.svg?style=flat-square)](https://gitter.im/orlandocharles/cfdi) [![License](https://img.shields.io/github/license/orlandocharles/cfdi.svg?style=flat-square)](https://packagist.org/packages/orlandocharles/cfdi) [![Donate](https://img.shields.io/badge/Donate-PayPal-3b7bbf.svg?style=flat-square)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=H2KAFWPGPMKHJ)

- [Instalación](#instalación)
- [Uso](#uso)
- [Licencia](#licencia)
- [Donación](#donación)

## Instalación

> Nota: el proyecto se encuentra en desarrollo.

Para instalar el paquete mediante [Composer](https://getcomposer.org/).

```shell
composer require orlandocharles/cfdi
```

## Uso

- [CFDI](#cfdi)
- [CFDI Relacionado](#cfdi-relacionado)
- [Emisor](#emisor)
- [Receptor](#receptor)
- [Concepto](#concepto)
- [Impuestos](#impuestos)

  - [Traslado](#traslado)

    - [Traslado en comprobante](#traslado-en-comprobante)
    - [Traslado en concepto](#traslado-en-concepto)

  - [Retención](#retención)

    - [Retención en comprobante](#retención-en-comprobante)
    - [Retención en concepto](#retención-en-concepto)

- [Información Aduanera](#información-aduanera)

- [Cuenta Predial](#cuenta-predial)
- [Parte](#parte)
- [Complemento Pagos](#pago)
        - [Documento Relacionado](#pago-documento-relacionado)
        - [Impuesto retenido](#retención-en-pago)
        - [Impuesto trasladado](#traslado-en-pago)
        
        
### CFDI

```php
<?php
use Charles\CFDI\CFDI;

$cer = file_get_contents('.../csd/AAA010101AAA.cer.pem');
$key = file_get_contents('.../csd/AAA010101AAA.key.pem');

$cfdi = new CFDI([
    'Serie' => 'A',
    'Folio' => 'A0101',
    'Fecha' => '2017-06-17T03:00:00',
    'FormaPago' => '01',
    'NoCertificado' => '00000000000000000000',
    'CondicionesDePago' => '',
    'Subtotal' => '',
    'Descuento' => '0.00',
    'Moneda' => 'MXN',
    'TipoCambio' => '1.0',
    'Total' => '',
    'TipoDeComprobante' => 'I',
    'MetodoPago' => 'PUE',
    'LugarExpedicion' => '64000',
], $cer, $key);
```

### CFDI Relacionado

En este nodo se debe expresar la información de los comprobantes fiscales relacionados con el que se ésta generando, se deben expresar tantos numeros de nodos de CfdiRelacionado, como comprobantes se requieran relacionar.

```php
<?php
use Charles\CFDI\CFDI;
use Charles\CFDI\Node\Relacionado;

$cfdi = new CFDI([]);

$cfdi->add(new Relacionado([
    'UUID' => 'XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX',
], [
    'TipoRelacion' => '01',
]));
```

```xml
<cfdi:CfdiRelacionados TipoRelacion="01">
  <cfdi:CfdiRelacionado UUID="XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX"/>
</cfdi:CfdiRelacionados>
```

### Emisor

En este nodo se debe expresar la información del contribuyente que emite el comprobante fiscal.

```php
<?php
use Charles\CFDI\CFDI;
use Charles\CFDI\Node\Emisor;

$cfdi = new CFDI([]);

$cfdi->add(new Emisor([
    'Rfc' => 'XAXX010101000',
    'Nombre' => 'Florería SA de CV',
    'RegimenFiscal' => '601',
]));
```

```xml
<cfdi:Emisor Rfc="XAXX010101000" Nombre="Florería SA de CV" RegimenFiscal="601"/>
```

### Receptor

En este nodo se debe expresar la información del contribuyente receptor del comprobante.

```php
<?php
use Charles\CFDI\CFDI;
use Charles\CFDI\Node\Receptor;

$cfdi = new CFDI([]);

$cfdi->add(new Receptor([
    'Rfc' => 'XEXX010101000',
    'Nombre' => 'Orlando Charles',
    'ResidenciaFiscal' => 'USA',
    'NumRegIdTrib' => '121585958',
    'UsoCFDI' => 'G01',
]));
```

```xml
<cfdi:Receptor Rfc="XEXX010101000" Nombre="Orlando Charles" ResidenciaFiscal="USA" NumRegIdTrib="121585958" UsoCFDI="G01"/>
```

### Concepto

En este nodo se debe expresar la información detallada de un bien o servicio descrito en el comprobante.

```php
<?php
use Charles\CFDI\CFDI;
use Charles\CFDI\Node\Concepto;

$cfdi = new CFDI([]);

$cfdi->add(new Concepto([
    'ClaveProdServ' => '10317331',
    'NoIdentificacion' => 'UT421511',
    'Cantidad' => '24',
    'ClaveUnidad' => 'H87',
    'Unidad' => 'Pieza',
    'Descripcion' => 'Arreglo de 24 tulipanes rosadas recién cortados',
    'ValorUnitario' => '56.00',
    'Importe' => '1344.00',
    'Descuento' => '10.00',
]));

$cfdi->add(new Concepto([
    'ClaveProdServ' => '10317352',
    'NoIdentificacion' => 'UT421510',
    'Cantidad' => '12',
    'ClaveUnidad' => 'H87',
    'Unidad' => 'Pieza',
    'Descripcion' => 'Arreglo de 12 tulipanes rojos recién cortados',
    'ValorUnitario' => '66.00',
    'Importe' => '792.00',
    'Descuento' => '5.00',
]));
```

```xml
<cfdi:Conceptos>
  <cfdi:Concepto ClaveProdServ="10317331" NoIdentificacion="UT421511" Cantidad="24" ClaveUnidad="H87" Unidad="Pieza" Descripcion="Arreglo de 24 tulipanes rosadas recién cortados" ValorUnitario="56.00" Importe="1344.00" Descuento="10.00"/>
  <cfdi:Concepto ClaveProdServ="10317352" NoIdentificacion="UT421510" Cantidad="12" ClaveUnidad="H87" Unidad="Pieza" Descripcion="Arreglo de 12 tulipanes rojos recién cortados" ValorUnitario="66.00" Importe="792.00" Descuento="5.00"/>
</cfdi:Conceptos>
```

### Impuestos

#### Retención

##### Retención en comprobante

En este nodo se debe expresar la información detallada de una retención de un impuesto específico.

```php
use Charles\CFDI\CFDI;
use Charles\CFDI\Node\Impuesto\Retencion;

$cfdi->add(new Retencion([
    'Impuesto' => '002',
    'Importe' => '35000',
]));
```

##### Retención en concepto

```php
use Charles\CFDI\CFDI;
use Charles\CFDI\Node\Concepto;
use Charles\CFDI\Node\Impuesto\Retencion;

$cfdi = new CFDI([...]);

$concepto = new Concepto([
    'ClaveProdServ' => '10317331',
    'NoIdentificacion' => 'UT421511',
    'Cantidad' => '24',
    'ClaveUnidad' => 'H87',
    'Unidad' => 'Pieza',
    'Descripcion' => 'Arreglo de 24 tulipanes rosadas recién cortados',
    'ValorUnitario' => '56.00',
    'Importe' => '1344.00',
    'Descuento' => '10.00',
]);

$concepto->add(new Retencion([

]));

$cfdi->add($concepto);
```

#### Traslado

##### Traslado en comprobante

En este nodo se debe expresar la información detallada de un traslado de impuesto específico.

```php
use Charles\CFDI\CFDI;
use Charles\CFDI\Node\Impuesto\Traslado;

$cfdi->add(new Traslado([
    'Impuesto' => '001',
    'TipoFactor' => 'Tasa',
    'TasaOCuota' => '0.160000',
    'Importe' => '23000',
]));
```

##### Traslado en concepto

```php
use Charles\CFDI\CFDI;
use Charles\CFDI\Node\Concepto;
use Charles\CFDI\Node\Impuesto\Traslado;

$cfdi = new CFDI([...]);

$concepto = new Concepto([
    'ClaveProdServ' => '10317331',
    'NoIdentificacion' => 'UT421511',
    'Cantidad' => '24',
    'ClaveUnidad' => 'H87',
    'Unidad' => 'Pieza',
    'Descripcion' => 'Arreglo de 24 tulipanes rosadas recién cortados',
    'ValorUnitario' => '56.00',
    'Importe' => '1344.00',
    'Descuento' => '10.00',
]);

$concepto->add(new Traslado([

]));

$cfdi->add($concepto);
```

### Información Aduanera

En este nodo se debe expresar la información aduanera correspondiente a cada concepto cuando se trate de ventas de primera mano de mercancías importadas

```php
use Charles\CFDI\CFDI;
use Charles\CFDI\Node\Concepto;
use Charles\CFDI\Node\InformacionAduanera;

$cfdi = new CFDI([...]);

$concepto = new Concepto([
    'ClaveProdServ' => '10317331',
    'NoIdentificacion' => 'UT421511',
    'Cantidad' => '24',
    'ClaveUnidad' => 'H87',
    'Unidad' => 'Pieza',
    'Descripcion' => 'Arreglo de 24 tulipanes rosadas recién cortados',
    'ValorUnitario' => '56.00',
    'Importe' => '1344.00',
    'Descuento' => '10.00',
]);

$concepto->add(new InformacionAduanera([
    'NumeroPedimento' => '00 00 0000 0000000',
]));

$cfdi->add($concepto);
```

```xml
<cfdi:Conceptos>
  <cfdi:Concepto ClaveProdServ="10317331" NoIdentificacion="UT421511" Cantidad="24" ClaveUnidad="H87" Unidad="Pieza" Descripcion="Arreglo de 24 tulipanes rosadas recién cortados" ValorUnitario="56.00" Importe="1344.00" Descuento="10.00">
    <cfdi:InformacionAduanera NumeroPedimento="00 00 0000 0000000"/>
  </cfdi:Concepto>
</cfdi:Conceptos>
```

### Cuenta Predial

En este nodo se puede expresar el número de cuenta predial con el que fue registrado el inmueble en el sistema catastral de la entidad federativa de que trate, o bien para incorporar los datos de identificación del certificado de participación inmobiliaria no amortizable.

```php
use Charles\CFDI\CFDI;
use Charles\CFDI\Node\Concepto;
use Charles\CFDI\Node\CuentaPredial;

$cfdi = new CFDI([...]);
$concepto = new Concepto([...]);

$concepto->add(new CuentaPredial([
    'Numero' => '00000',
]));

$cfdi->add($concepto);
```

### Parte

En este nodo se pueden expresar las partes o componentes que integran la totalidad del concepto expresado en el comprobante fiscal digital por Internet.

```php
use Charles\CFDI\CFDI;
use Charles\CFDI\Node\Concepto;
use Charles\CFDI\Node\Parte;

$cfdi = new CFDI([...]);

$concepto = new Concepto([
    'ClaveProdServ' => '27113201',
    'NoIdentificacion' => 'UT421456',
    'Cantidad' => '1',
    'ClaveUnidad' => 'KT',
    'Unidad' => 'Kit',
    'Descripcion' => 'Kit de destornillador',
    'ValorUnitario' => '217.30',
    'Importe' => '217.30',
    'Descuento' => '0.00',
]);

$tornillo = new Parte([
    'ClaveProdServ' => '31161500',
    'NoIdentificacion' => 'UT367898',
    'Cantidad' => '34',
    'ClaveUnidad' => 'H87',
    'Unidad' => 'Pieza',
    'Descripcion' => 'Tornillo',
    'ValorUnitario' => '00.20',
    'Importe' => '6.80',
]);

$tornilloPerno = new Parte([
    'ClaveProdServ' => '31161501',
    'NoIdentificacion' => 'UT367899',
    'Cantidad' => '14',
    'ClaveUnidad' => 'H87',
    'Unidad' => 'Pieza',
    'Descripcion' => 'Tornillo de Perno',
    'ValorUnitario' => '00.75',
    'Importe' => '10.50',
]);

$destornillador = new Parte([
    'ClaveProdServ' => '27111701',
    'NoIdentificacion' => 'UT367900',
    'Cantidad' => '2',
    'ClaveUnidad' => 'H87',
    'Unidad' => 'Pieza',
    'Descripcion' => 'Destornillador',
    'ValorUnitario' => '100.00',
    'Importe' => '200.00',
]);

$concepto->add($tornillo);
$concepto->add($tornilloPerno);
$concepto->add($destornillador);

$cfdi->add($concepto);
```

```xml
<cfdi:Conceptos>
  <cfdi:Concepto ClaveProdServ="27113201" NoIdentificacion="UT421456" Cantidad="1" ClaveUnidad="KT" Unidad="Kit" Descripcion="Kit de destornillador" ValorUnitario="217.30" Importe="217.30" Descuento="0.00">
    <cfdi:Parte ClaveProdServ="31161500" NoIdentificacion="UT367898" Cantidad="34" ClaveUnidad="H87" Unidad="Pieza" Descripcion="Tornillo" ValorUnitario="00.20" Importe="6.80"/>
    <cfdi:Parte ClaveProdServ="31161501" NoIdentificacion="UT367899" Cantidad="14" ClaveUnidad="H87" Unidad="Pieza" Descripcion="Tornillo de Perno" ValorUnitario="00.75" Importe="10.50"/>
    <cfdi:Parte ClaveProdServ="27111701" NoIdentificacion="UT367900" Cantidad="2" ClaveUnidad="H87" Unidad="Pieza" Descripcion="Destornillador" ValorUnitario="100.00" Importe="200.00"/>
  </cfdi:Concepto>
</cfdi:Conceptos>
```
#Complementos


----------

### Pago

```php
use Charles\CFDI\CFDI;
use Charles\CFDI\Node\Complemento\Pagos\Pago;

$cfdi = new CFDI([...]);

$cfdi->add(new Pago([
    'FechaPago' => '2017-01-01T12:00:00',
    'FormaDePagoP' => '01',
    'MonedaP' => 'USD',
    'TipoCambioP' => '21.00',
    'Monto' => '123.45',
    'NumOperacion' => '83755',
    'RfcEmisorCtaOrd' => 'AAA010101AAA',
    'NomBancoOrdExt' => 'Banco de Jakarta',
    'CtaOrdenante' => '0319849245',
    'RfcEmisorCtaBen' => 'BCO010101AAA',
    'CtaBeneficiario' => '0453946620',
    'TipoCadPago' => '01',
    'CertPago' => 'por confirmar como vamos a proceder con esto',
    'CadPago' => 'por confirmar como vamos a proceder con esto',
    'SelloPago' => 'por confirmar como vamos a proceder con esto',
]));
```

#####Documento relacionado

```php
use Charles\CFDI\CFDI;
use Charles\CFDI\Node\Complemento\Pagos\Pago;
use Charles\CFDI\Node\Complemento\Pagos\DoctoRelacionado;

$cfdi = new CFDI([...]);
$pago = new Pago([...]);

$pago->add(new DoctoRelacionado([
    'IdDocumento' => '11111111-6EF0-4526-8962-2A5E8C040A6C',
    'Serie' => 'ABC',
    'Folio' => '123',
    'MonedaDR' => 'USD',
    'TipoCambioDR' => '21.00',
    'MetodoDePagoDR' => 'PUE',
    'NumParcialidad' => '2',
    'ImpSaldoAnt' => '123.45',
    'ImpPagado' => '123.45',
    'ImpSaldoInsoluto' => '123.45',
]));

$cfdi->add($pago);
```

#####Traslado en pago

```php
use Charles\CFDI\CFDI;
use Charles\CFDI\Node\Complemento\Pagos\Pago;
use Charles\CFDI\Node\Complemento\Pagos\Impuesto\Traslado;

$cfdi = new CFDI([...]);
$pago = new Pago([...]);

$pago->add(new Traslado([
    'Impuesto' => '001',
    'TipoFactor' => 'Tasa',
    'TasaOCuota' => '0.160000',
    'Importe' => '23000',
]));

$cfdi->add($pago);
```

#####Retención en pago

```php
use Charles\CFDI\CFDI;
use Charles\CFDI\Node\Complemento\Pagos\Pago;
use Charles\CFDI\Node\Complemento\Pagos\Impuesto\Retencion;

$cfdi = new CFDI([...]);
$pago = new Pago([...]);

$pago->add(new Retencion([
    'Impuesto' => '001',
    'Importe' => '23000',
]));

$cfdi->add($pago);
```


## XmlResolver

De manera predeterminada, para construir la cadena de origen se usa el mecanismo recomendado por el SAT
de convertir el XML en texto usando XSLT desde el archivo remoto
`http://www.sat.gob.mx/sitio_internet/cfd/3/cadenaoriginal_3_3/cadenaoriginal_3_3.xslt`

Si desea descargar recursivamente los recursos del SAT para almacenarlos localmente puede usar un objeto
`XmlResolver` e insertarlo en su objeto `CFDI`, así puede especificar el lugar donde se van a descargar
y reutilizar los archivos XSLT del SAT.

```php
use Charles\CFDI\CFDI;
use Charles\CFDI\XmlResolver;

// no usará almacenamiento local
$resolver = new XmlResolver('');

// usará la ruta donde está instalada la librería + /build/resources/
$resolver = new XmlResolver();

// usará la ruta especificada
$resolver = new XmlResolver('/cfdi/cache/');

// establezca el objeto en el cfdi después de ser creado
/** @var CFDI $cfdi */
$cfdi->setResolver($resolver);

// también lo puede establecer desde el momento de su construcción
new CFDI($attributes, $cer, $key, $resolver);
```

Si se encuentra detrás de un proxy o quiere usar sus propios métodos de descarga puede implementar
la interfaz `XmlResourceRetriever\Downloader\DownloaderInterface` y establecérsela a su objeto `XmlResolver`

## Licencia

Este paquete no pertenece a ninguna comañia ni entidad gubernamental y esta bajo la Licencia MIT, si quieres saber más al respecto puedes ver el archivo de [Licencia](LICENSE) que se encuentra en este mismo repositorio.

## Donación

Este proyecto ayuda a que otros desarrolladores ahorren horas de trabajo.

[![paypal](https://www.paypalobjects.com/es_XC/MX/i/btn/btn_donateCC_LG.gif)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=H2KAFWPGPMKHJ)
