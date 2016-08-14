<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Sniff\Xml\Extractor;

use SimpleXMLElement;

final class CustomSniffPropertyValuesExtractor
{
//    public function extractFromRulesetXmlFile(string $rulesetXmlFile) : array
//    {
//        $rulesetXml = simplexml_load_file($rulesetXmlFile);
//
//        $customPropertyValues = [];
//        foreach ($rulesetXml->rule as $ruleElement) {
//            if (!isset($ruleElement['ref'])) {
//                continue;
//            }
//
//            $customPropertyValues = array_merge(
//                $customPropertyValues,
//                $this->extractFromRuleXmlElement($ruleElement)
//            );
//        }
//
//        return $customPropertyValues;
//    }

    public function extractFromRuleXmlElement(SimpleXMLElement $ruleElement) : array
    {
        if (!isset($ruleElement->properties)) {
            return [];
        }

        $sniffCode = (string) $ruleElement['ref'];

        $customPropertyValues = [];
        foreach ($ruleElement->properties->property as $property) {
            $name = (string) $property['name'];
            $value = $this->resolveValue($property);
            $customPropertyValues[$sniffCode]['properties'][$name] = $value;
        }

        return $customPropertyValues;
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
