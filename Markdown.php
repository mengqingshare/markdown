<?php
/**
 * @copyright Copyright (c) 2014 Carsten Brandt
 * @license https://github.com/cebe/markdown/blob/master/LICENSE
 * @link https://github.com/cebe/markdown#readme
 */

namespace cebe\markdown;

// work around https://github.com/facebook/hhvm/issues/1120
defined('ENT_HTML401') || define('ENT_HTML401', 0);

/**
 * Markdown parser for the [initial markdown spec](http://daringfireball.net/projects/markdown/syntax).
 *
 * @author Carsten Brandt <mail@cebe.cc>
 */
class Markdown extends Parser
{
	// include block element parsing using traits
	use block\CodeTrait;
	use block\HeadlineTrait;
	use block\HtmlTrait;
	use block\ListTrait;
	use block\QuoteTrait;
	use block\RuleTrait;
	use block\TableTrait;

	// include inline element parsing using traits
	use inline\CodeTrait;
	use inline\EmphStrongTrait;
	use inline\LinkTrait;

	/**
	 * @var boolean whether to format markup according to HTML5 spec.
	 * Defaults to `false` which means that markup is formatted as HTML4.
	 */
	public $html5 = false;

	/**
	 * @var array these are "escapeable" characters. When using one of these prefixed with a
	 * backslash, the character will be outputted without the backslash and is not interpreted
	 * as markdown.
	 */
	protected $escapeCharacters = [
		'\\', // backslash
		'`', // backtick
		'*', // asterisk
		'_', // underscore
		'{', '}', // curly braces
		'[', ']', // square brackets
		'(', ')', // parentheses
		'#', // hash mark
		'+', // plus sign
		'-', // minus sign (hyphen)
		'.', // dot
		'!', // exclamation mark
		'<', '>',
	];

	/**
	 * @var array a list of defined references in this document.
	 */
	protected $references = [];


	/**
	 * @inheritDoc
	 */
	protected function prepare()
	{
		// reset references
		$this->references = [];
	}

	/**
	 * Consume lines for a paragraph
	 */
	protected function consumeParagraph($lines, $current)
	{
		// consume until newline
		$content = [];
		for ($i = $current, $count = count($lines); $i < $count; $i++) {
			if (ltrim($lines[$i]) !== '' && $lines[$i][0] != "\t" && strncmp($lines[$i], '    ', 4) !== 0 && !$this->identifyHeadline($lines[$i], $lines, $i)) {
				$content[] = $lines[$i];
			} else {
				break;
			}
		}
		$block = [
			'paragraph',
			'content' => $this->parseInline(implode("\n", $content)),
		];
		return [$block, --$i];
	}


	/**
	 * @inheritdocs
	 *
	 * Parses a newline indicated by two spaces on the end of a markdown line.
	 */
	protected function renderText($text)
	{
		return str_replace("  \n", $this->html5 ? "<br>\n" : "<br />\n", $text[1]);
	}



}
