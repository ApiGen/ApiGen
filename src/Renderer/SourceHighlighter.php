<?php declare(strict_types = 1);

namespace ApiGenX\Renderer;

use PhpToken;


final class SourceHighlighter
{
	public const PHP_TAG = 'php-tag';
	public const PHP_KEYWORD = 'php-kw';
	public const PHP_NUMBER = 'php-num';
	public const PHP_STRING = 'php-str';
	public const PHP_VARIABLE = 'php-var';
	public const PHP_COMMENT = 'php-comment';

	public array $tokenClass = [
		T_OPEN_TAG => self::PHP_TAG,
		T_OPEN_TAG_WITH_ECHO => self::PHP_TAG,
		T_CLOSE_TAG => self::PHP_TAG,
		T_INCLUDE => self::PHP_KEYWORD,
		T_INCLUDE_ONCE => self::PHP_KEYWORD,
		T_REQUIRE => self::PHP_KEYWORD,
		T_REQUIRE_ONCE => self::PHP_KEYWORD,
		T_LOGICAL_OR => self::PHP_KEYWORD,
		T_LOGICAL_XOR => self::PHP_KEYWORD,
		T_LOGICAL_AND => self::PHP_KEYWORD,
		T_PRINT => self::PHP_KEYWORD,
		T_YIELD => self::PHP_KEYWORD,
		T_YIELD_FROM => self::PHP_KEYWORD,
		T_INSTANCEOF => self::PHP_KEYWORD,
		T_NEW => self::PHP_KEYWORD,
		T_CLONE => self::PHP_KEYWORD,
		T_ELSEIF => self::PHP_KEYWORD,
		T_ELSE => self::PHP_KEYWORD,
		T_EVAL => self::PHP_KEYWORD,
		T_EXIT => self::PHP_KEYWORD,
		T_IF => self::PHP_KEYWORD,
		T_ENDIF => self::PHP_KEYWORD,
		T_ECHO => self::PHP_KEYWORD,
		T_DO => self::PHP_KEYWORD,
		T_WHILE => self::PHP_KEYWORD,
		T_ENDWHILE => self::PHP_KEYWORD,
		T_FOR => self::PHP_KEYWORD,
		T_ENDFOR => self::PHP_KEYWORD,
		T_FOREACH => self::PHP_KEYWORD,
		T_ENDFOREACH => self::PHP_KEYWORD,
		T_DECLARE => self::PHP_KEYWORD,
		T_ENDDECLARE => self::PHP_KEYWORD,
		T_AS => self::PHP_KEYWORD,
		T_SWITCH => self::PHP_KEYWORD,
		T_ENDSWITCH => self::PHP_KEYWORD,
		T_CASE => self::PHP_KEYWORD,
		T_DEFAULT => self::PHP_KEYWORD,
		T_BREAK => self::PHP_KEYWORD,
		T_CONTINUE => self::PHP_KEYWORD,
		T_GOTO => self::PHP_KEYWORD,
		T_FUNCTION => self::PHP_KEYWORD,
		T_FN => self::PHP_KEYWORD,
		T_CONST => self::PHP_KEYWORD,
		T_RETURN => self::PHP_KEYWORD,
		T_CATCH => self::PHP_KEYWORD,
		T_TRY => self::PHP_KEYWORD,
		T_FINALLY => self::PHP_KEYWORD,
		T_THROW => self::PHP_KEYWORD,
		T_USE => self::PHP_KEYWORD,
		T_INSTEADOF => self::PHP_KEYWORD,
		T_GLOBAL => self::PHP_KEYWORD,
		T_STATIC => self::PHP_KEYWORD,
		T_ABSTRACT => self::PHP_KEYWORD,
		T_FINAL => self::PHP_KEYWORD,
		T_PRIVATE => self::PHP_KEYWORD,
		T_PROTECTED => self::PHP_KEYWORD,
		T_PUBLIC => self::PHP_KEYWORD,
		T_VAR => self::PHP_KEYWORD,
		T_UNSET => self::PHP_KEYWORD,
		T_ISSET => self::PHP_KEYWORD,
		T_EMPTY => self::PHP_KEYWORD,
		T_HALT_COMPILER => self::PHP_KEYWORD,
		T_CLASS => self::PHP_KEYWORD,
		T_TRAIT => self::PHP_KEYWORD,
		T_INTERFACE => self::PHP_KEYWORD,
		T_EXTENDS => self::PHP_KEYWORD,
		T_IMPLEMENTS => self::PHP_KEYWORD,
		T_LIST => self::PHP_KEYWORD,
		T_ARRAY => self::PHP_KEYWORD,
		T_NAMESPACE => self::PHP_KEYWORD,
		T_LNUMBER => self::PHP_NUMBER,
		T_DNUMBER => self::PHP_NUMBER,
		T_CONSTANT_ENCAPSED_STRING => self::PHP_STRING,
		T_ENCAPSED_AND_WHITESPACE => self::PHP_STRING,
		T_VARIABLE => self::PHP_VARIABLE,
		T_COMMENT => self::PHP_COMMENT,
		T_DOC_COMMENT => self::PHP_COMMENT,
	];

	public array $identifierClass = [
		'true' => self::PHP_KEYWORD,
		'false' => self::PHP_KEYWORD,
		'null' => self::PHP_KEYWORD,
	];


	public function highlight(string $source): string
	{
		$align = strlen(strval(substr_count($source, "\n")));
		$lineStart = "<div id=\"%1\$d\" class=\"source-line\"><a class=\"source-lineNum\" href=\"#%1\$d\">%1\${$align}d: </a>";
		$lineEnd = '</div>';

		$line = 1;
		$out = sprintf($lineStart, $line);

		foreach ($this->tokenize($source) as $id => $text) {
			if ($text === "\n") {
				$out .= $lineEnd . sprintf($lineStart, ++$line);

			} else {
				$html = htmlspecialchars($text);
				$class = $this->tokenClass[$id] ?? ($id === T_STRING ? $this->identifierClass[strtolower($text)] ?? null : null);
				$out .= $class ? "<span class=\"{$class}\">{$html}</span>" : $html;
			}
		}

		return $out . $lineEnd;
	}


	/**
	 * @return iterable<int, string>
	 */
	private function tokenize(string $source): iterable
	{
		foreach (PhpToken::tokenize($source, TOKEN_PARSE) as $token) {
			$lines = explode("\n", $token->text);
			$lastLine = count($lines) - 1;

			foreach ($lines as $i => $line) {
				yield $token->id => $line;

				if ($i !== $lastLine) {
					yield T_WHITESPACE => "\n";
				}
			}
		}
	}
}
