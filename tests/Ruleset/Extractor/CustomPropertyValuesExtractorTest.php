<?php

namespace Symplify\PHP7_CodeSniffer\Tests\Ruleset\Extractor;

use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\Ruleset\Extractor\CustomPropertyValuesExtractor;

final class CustomPropertyValuesExtractorTest extends TestCase
{
    /**
     * @var CustomPropertyValuesExtractor
     */
    private $customPropertyValuesExtractor;

    protected function setUp()
    {
        $this->customPropertyValuesExtractor = new CustomPropertyValuesExtractor();
    }

    public function testProcess()
    {
        $rule = new \SimpleXMLElement('<rule ref="PSR1"/>');
        $ruleset = $this->customPropertyValuesExtractor->extractFromRuleXmlElement($rule);
        $this->assertSame([], $ruleset);

        $rule = new \SimpleXMLElement('<rule ref="Generic.Files.LineEndings">
                <properties>
                    <property name="eolChar" value="\n"/>
                </properties>
            </rule>');
        $ruleset = $this->customPropertyValuesExtractor->extractFromRuleXmlElement($rule);
        $this->assertSame([
            'Generic.Files.LineEndings' => [
                'properties' => [
                    'eolChar' => '\n'
                ]
            ]
        ], $ruleset);

        $rule = new \SimpleXMLElement(' <rule ref="Generic.WhiteSpace.ScopeIndent">
            <properties>
                <property name="ignoreIndentationTokens" type="array" value="T_COMMENT,T_DOC_COMMENT_OPEN_TAG"/>
            </properties>
        </rule>');
        $ruleset = $this->customPropertyValuesExtractor->extractFromRuleXmlElement($rule);
        $this->assertSame([
            'Generic.WhiteSpace.ScopeIndent' => [
                'properties' => [
                    'ignoreIndentationTokens' => [
                        0 => 'T_COMMENT',
                        1 => 'T_DOC_COMMENT_OPEN_TAG'
                    ]
                ]
            ]
        ], $ruleset);
    }
}
