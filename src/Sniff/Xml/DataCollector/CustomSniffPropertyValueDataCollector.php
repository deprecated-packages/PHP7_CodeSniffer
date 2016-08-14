<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Sniff\Xml\DataCollector;

use SimpleXMLElement;
use Symplify\PHP7_CodeSniffer\Sniff\Naming\SniffNaming;

final class CustomSniffPropertyValueDataCollector
{
    /**
     * @var array[]
     */
    private $customSniffPropertyValuesBySniffCode = [];

    public function collectFromRuleXmlElement(SimpleXMLElement $ruleXmlElement)
    {
        if (isset($ruleXmlElement->properties)) {
            $this->addCustomSniffProperty(
                (string) $ruleXmlElement['ref'],
                $ruleXmlElement->properties
            );
        }
    }

    public function getForSniffClass(string $sniffClassName) : array
    {
        $sniffCode = SniffNaming::guessCodeByClass($sniffClassName);
        if (!isset($this->customSniffPropertyValuesBySniffCode[$sniffCode])) {
            return [];
        }

        return $this->normalizeValues($this->customSniffPropertyValuesBySniffCode[$sniffCode]);
    }

    private function addCustomSniffProperty(string $sniffCode, array $properties)
    {
        $this->customSniffPropertyValuesBySniffCode[$sniffCode] = array_merge(
            $this->customSniffPropertyValuesBySniffCode[$sniffCode],
            $properties
        );
    }

    private function normalizeValues(array $customSniffPropertyValues) : array
    {
        foreach ($customSniffPropertyValues as $property => $value) {
            $customSniffPropertyValues[$property] = $this->normalizeValue($value);
        }
        return $customSniffPropertyValues;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    private function normalizeValue($value)
    {
        $value = $this->trimStringValue($value);
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
