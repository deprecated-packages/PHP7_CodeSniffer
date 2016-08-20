<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Sniff\Xml\DataCollector;

use PHP_CodeSniffer\Sniffs\Sniff;
use SimpleXMLElement;
use Symplify\PHP7_CodeSniffer\Sniff\Naming\SniffNaming;

final class SniffPropertyValueDataCollector
{
    /**
     * @var array[]
     */
    private $sniffPropertyValuesBySniffCode = [];

    public function collectFromRuleXmlElement(SimpleXMLElement $ruleXmlElement)
    {
        if (isset($ruleXmlElement->properties)) {
            $properties = (array) $ruleXmlElement->properties;

            if (is_array($properties['property'])) {
                $properties = array_pop($properties);
            }

            $this->addCustomSniffProperties((string) $ruleXmlElement['ref'], $properties);
        }
    }

    public function getForSniff(Sniff $sniff) : array
    {
        $sniffClassName = get_class($sniff);
        return $this->getForSniffClass($sniffClassName);
    }

    private function getForSniffClass(string $sniffClassName) : array
    {
        $sniffCode = SniffNaming::guessCodeByClass($sniffClassName);
        if (!isset($this->sniffPropertyValuesBySniffCode[$sniffCode])) {
            return [];
        }

        return $this->sniffPropertyValuesBySniffCode[$sniffCode];
    }

    private function addCustomSniffProperties(string $sniffCode, array $propertyXmlElements)
    {
        if (!isset($this->sniffPropertyValuesBySniffCode[$sniffCode])) {
            $this->sniffPropertyValuesBySniffCode[$sniffCode] = [];
        }

        $propertyValues = [];
        foreach ($propertyXmlElements as $propertyXmlElement) {
            $name = (string) $propertyXmlElement['name'];
            $value = (string) $propertyXmlElement['value'];
            $propertyValues[$name] = $this->normalizeValue($value);
        }

        $this->sniffPropertyValuesBySniffCode[$sniffCode] = array_merge(
            $this->sniffPropertyValuesBySniffCode[$sniffCode],
            $propertyValues
        );
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    private function normalizeValue($value)
    {
        $value = $this->trimStringValue($value);
        if (is_numeric($value)) {
            return (int) $value;
        }

        return $this->normalizeBoolValue($value);
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    private function trimStringValue($value)
    {
        if (is_string($value)) {
            return trim($value);
        }

        return $value;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    private function normalizeBoolValue($value)
    {
        if ($value === 'true' || $value === 'TRUE') {
            return true;
        }

        if ($value === 'false' || $value === 'FALSE') {
            return false;
        }

        return $value;
    }
}
