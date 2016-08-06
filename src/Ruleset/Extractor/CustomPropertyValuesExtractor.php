<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Ruleset\Extractor;

use SimpleXMLElement;

final class CustomPropertyValuesExtractor
{
    public function extractFromRuleXmlElement(SimpleXMLElement $ruleElement) : array
    {
        if (!isset($ruleElement->properties)) {
            return [];
        }

        $sniffCode = (string) $ruleElement['ref'];

        $modifiedPropertyValues = [];
        foreach ($ruleElement->properties->property as $property) {
            $name = (string) $property['name'];
            $modifiedPropertyValues[$sniffCode]['properties'][$name] = $this->resolveValue($property);
        }

        return $modifiedPropertyValues;
    }

    /**
     * @return array|string
     */
    private function resolveValue(SimpleXMLElement $property)
    {
        if ($this->isArrayValue($property)) {
            return $this->resolveArrayValue($property);
        }

        return (string)$property['value'];
    }

    private function isArrayValue(SimpleXMLElement $property) : bool
    {
        return isset($property['type']) === true && (string)$property['type'] === 'array';
    }

    private function resolveArrayValue(SimpleXMLElement $arrayProperty) : array
    {
        $value = (string) $arrayProperty['value'];

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
