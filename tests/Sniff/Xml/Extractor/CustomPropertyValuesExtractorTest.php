<?php

namespace Symplify\PHP7_CodeSniffer\Tests\Sniff\Xml\Extractor;

use PHPUnit\Framework\TestCase;
use SimpleXMLElement;
use Symplify\PHP7_CodeSniffer\Sniff\Xml\Extractor\CustomSniffPropertyValuesExtractor;

final class CustomSniffPropertyValuesExtractorTest extends TestCase
{
    /**
     * @var CustomSniffPropertyValuesExtractor
     */
    private $customSniffPropertyValuesExtractor;

    protected function setUp()
    {
        $this->customSniffPropertyValuesExtractor = new CustomSniffPropertyValuesExtractor();
    }

    /**
     * @dataProvider provideDataForExtractFromRuleXmlElement()
     */
    public function testProcess(string $elementData, array $expectedCustomPropertyValues)
    {
        $rule = new SimpleXMLElement($elementData);
        $ruleset = $this->customSniffPropertyValuesExtractor->extractFromRuleXmlElement($rule);
        $this->assertSame($expectedCustomPropertyValues, $ruleset);
    }

    public function provideDataForExtractFromRuleXmlElement() : array
    {
        return [
            ['<rule ref="PSR1"/>', []],
            [
                '<rule ref="Generic.Files.LineEndings"> 
                    <properties>
                        <property name="eolChar" value="\n"/>
                    </properties>
                </rule>', [
                    'Generic.Files.LineEndings' => [
                        'properties' => [
                            'eolChar' => '\n'
                        ]
                    ],
                ]
            ],
            [
                '<rule ref="Generic.WhiteSpace.ScopeIndent"> 
                    <properties>
                        <property name="ignoreIndentationTokens"
                            type="array" value="T_COMMENT,T_DOC_COMMENT_OPEN_TAG"/>
                    </properties>
                </rule>', [
                    'Generic.WhiteSpace.ScopeIndent' => [
                        'properties' => [
                            'ignoreIndentationTokens' => [
                                0 => 'T_COMMENT',
                                1 => 'T_DOC_COMMENT_OPEN_TAG'
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}
