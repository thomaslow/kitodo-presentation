<?php

declare(strict_types=1);

/**
 * (c) Kitodo. Key to digital objects e.V. <contact@kitodo.org>
 *
 * This file is part of the Kitodo and TYPO3 projects.
 *
 * @license GNU General Public License version 3 or later.
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Kitodo\Dlf\Validation;

use DOMDocument;

/**
 * The validator combines the configured schemes into one schema and validates the provided DOMDocument against this.
 *
 * @package TYPO3
 * @subpackage dlf
 *
 * @access public
 */
class XmlSchemesValidator extends AbstractDlfValidator
{
    use LibXmlTrait;

    private array $schemes;

    public function __construct(array $configuration)
    {
        parent::__construct(DOMDocument::class);
        $this->schemes = $configuration;
    }

    /**
     * Combines the schemes to one schema and validates the DOMDocument against this.
     *
     * @param $value DOMDocument The value to validate
     * @return bool True if is valid
     */
    protected function isSchemeValid(DOMDocument $value): bool
    {
        $xsd = '<?xml version="1.0" encoding="utf-8"?><xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema" elementFormDefault="qualified">';
        foreach ($this->schemes as $scheme) {
            $xsd .= '<xs:import namespace="' . $scheme["namespace"] . '" schemaLocation="' . $scheme["schemaLocation"] . '"/>';
        }
        $xsd .= '</xs:schema>';
        return $value->schemaValidateSource($xsd);
    }

    protected function isValid($value): void
    {
        $this->enableErrorBuffer();
        if (!$this->isSchemeValid($value)) {
            $this->addErrorsOfBuffer();
        }
        $this->disableErrorBuffer();
    }
}