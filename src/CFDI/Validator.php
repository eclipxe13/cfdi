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
namespace PhpCfdi\CFDI;

use CFDIReader\CFDIFactory;
use CFDIReader\CFDIReader;
use CFDIReader\PostValidations\Issues;
use CFDIReader\PostValidations\IssuesTypes;
use CFDIReader\PostValidations\Messages;
use CFDIReader\SchemasValidator\SchemasValidator;
use XmlResourceRetriever\XsdRetriever;
use XmlSchemaValidator\SchemaValidatorException;

class Validator
{
    /** @var string */
    private $xml;

    /** @var XmlResolver */
    private $resolver;

    /** @var Issues */
    private $issues;

    public function __construct(string $xml, XmlResolver $resolver)
    {
        $this->xml = $xml;
        $this->resolver = $resolver;
    }

    /**
     * Constructor helper to get a validator instance from a CFDI
     * @param CFDI $cfdi
     * @return Validator
     */
    public static function newFromCFDI(CFDI $cfdi): Validator
    {
        return new self($cfdi->getXML(), $cfdi->getResolver());
    }

    /**
     * Validate against schemas and rules, set the internal property issues, errors and warnings
     * Returns FALSE only if some error was found, warnings and no errors return TRUE
     *
     * @return bool
     */
    public function validate(): bool
    {
        $this->issues = new Issues();
        try {
            $this->validateSchemas();
        } catch (SchemaValidatorException $exception) {
            $this->issues->add(IssuesTypes::ERROR, $exception->getMessage());
            return false;
        }
        $this->issues = $this->validateRules();
        return (0 === $this->issues->messages(IssuesTypes::ERROR)->count());
    }

    /**
     * Validate the xml contents against its schemas.
     * It uses the XsdRetriever generated by resolver to allow local files
     *
     * @return void
     * @throws \LogicException when newRetriever don't return a XsdRetriever instance
     * @throws SchemaValidatorException
     */
    public function validateSchemas()
    {
        $xsdRetriever = $this->resolver->newRetriever(XmlResolver::TYPE_XSD);
        if (! ($xsdRetriever instanceof XsdRetriever)) {
            throw new \LogicException('Invalid retriever');
        }
        $schemaValidator = new SchemasValidator($xsdRetriever);
        $schemaValidator->validate($this->xml);
    }

    /**
     * Uses the default post validator to check for issues
     * @return Issues|Messages[]
     */
    public function validateRules(): Issues
    {
        $location = ($this->resolver->hasLocalPath()) ? $this->resolver->getLocalPath() : null;
        $validatorFactory = new CFDIFactory($location);
        $validator = $validatorFactory->newPostValidator();
        $reader = new CFDIReader($this->xml, false);
        $validator->validate($reader);
        return $validator->issues;
    }

    public function getXml(): string
    {
        return $this->xml;
    }

    public function getResolver(): XmlResolver
    {
        return $this->resolver;
    }

    /**
     * Get the issues from the last validate call
     * @return Issues|Messages[]
     */
    public function getIssues(): Issues
    {
        return $this->issues;
    }

    /**
     * Get the only the error messages from the last validate call
     * @return Messages|string[]
     */
    public function getErrors(): Messages
    {
        return $this->issues->messages(IssuesTypes::ERROR);
    }

    /**
     * Get the only the warnings messages from the last validate call
     * @return Messages|string[]
     */
    public function getWarnings(): Messages
    {
        return $this->issues->messages(IssuesTypes::WARNING);
    }
}
