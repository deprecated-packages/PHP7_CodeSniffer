<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Sniff\Xml\Extractor;

use SimpleXMLElement;

final class SniffPropertyValuesExtractor
{
    public function extractFromRuleXmlElement(SimpleXMLElement $ruleXmlElement) : array
    {
        if (!isset($ruleXmlElement->properties)) {
            return [];
        }

        $propertyXmlElements = (array) $ruleXmlElement->properties;
        if (is_array($propertyXmlElements['property'])) {
            $propertyXmlElements = array_pop($propertyXmlElements);
        }

        $propertyValues = [];
        foreach ($propertyXmlElements as $propertyXmlElement) {
            $name = (string) $propertyXmlElement['name'];
            $value = $this->normalizeValue((string) $propertyXmlElement['value'], $propertyXmlElement);
            $propertyValues[$name] = $value;
        }

//        $propertyValues = [];
//        foreach ($ruleElement->properties->property as $property) {
//            $name = (string) $property['name'];
//            $value = $this->resolveValue($property);
//            $propertyValues[$name] = $value;
//        }

        return $propertyValues;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    private function normalizeValue($value, SimpleXMLElement $propertyXmlElement)
    {
        $value = $this->trim($value);

        if (is_numeric($value)) {
            return (int) $value;
        }

        if ($this->isArrayValue($propertyXmlElement)) {
            return $this->normalizeArrayValue($value);
        }

        return $this->normalizeBoolValue($value);
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    private function trim($value)
    {
        if (is_string($value)) {
            return trim($value);
        }

        return $value;
    }

    private function isArrayValue(SimpleXMLElement $property) : bool
    {
        return isset($property['type']) === true && (string)$property['type'] === 'array';
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

    private function normalizeArrayValue(string $value) : array
    {
        $values = [];
        foreach (explode(',', $value) as $val) {
            $v = '';

            list($key, $v) = explode('=>', $val . '=>');
            if ($v !== '') {
                $values[$key] = $v;
            } else {
                $values[] = $key;
            }
        }

        return $values;
    }
}
