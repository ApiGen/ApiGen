<?php declare(strict_types = 1);

namespace ApiGenX;

use PhpToken;


final class SourceHighlighter
{
	public array $tokenClass = [ // TODO: update for PHP 8
		T_OPEN_TAG => 'php-tag',
		T_OPEN_TAG_WITH_ECHO => 'php-tag',
		T_CLOSE_TAG => 'php-tag',
		T_INCLUDE => 'php-kw',
		T_INCLUDE_ONCE => 'php-kw',
		T_REQUIRE => 'php-kw',
		T_REQUIRE_ONCE => 'php-kw',
		T_LOGICAL_OR => 'php-kw',
		T_LOGICAL_XOR => 'php-kw',
		T_LOGICAL_AND => 'php-kw',
		T_PRINT => 'php-kw',
		T_YIELD => 'php-kw',
		T_YIELD_FROM => 'php-kw',
		T_INSTANCEOF => 'php-kw',
		T_NEW => 'php-kw',
		T_CLONE => 'php-kw',
		T_ELSEIF => 'php-kw',
		T_ELSE => 'php-kw',
		T_EVAL => 'php-kw',
		T_EXIT => 'php-kw',
		T_IF => 'php-kw',
		T_ENDIF => 'php-kw',
		T_ECHO => 'php-kw',
		T_DO => 'php-kw',
		T_WHILE => 'php-kw',
		T_ENDWHILE => 'php-kw',
		T_FOR => 'php-kw',
		T_ENDFOR => 'php-kw',
		T_FOREACH => 'php-kw',
		T_ENDFOREACH => 'php-kw',
		T_DECLARE => 'php-kw',
		T_ENDDECLARE => 'php-kw',
		T_AS => 'php-kw',
		T_SWITCH => 'php-kw',
		T_ENDSWITCH => 'php-kw',
		T_CASE => 'php-kw',
		T_DEFAULT => 'php-kw',
		T_BREAK => 'php-kw',
		T_CONTINUE => 'php-kw',
		T_GOTO => 'php-kw',
		T_FUNCTION => 'php-kw',
		T_FN => 'php-kw',
		T_CONST => 'php-kw',
		T_RETURN => 'php-kw',
		T_TRY => 'php-kw',
		T_CATCH => 'php-kw',
		T_FINALLY => 'php-kw',
		T_THROW => 'php-kw',
		T_USE => 'php-kw',
		T_INSTEADOF => 'php-kw',
		T_GLOBAL => 'php-kw',
		T_STATIC => 'php-kw',
		T_ABSTRACT => 'php-kw',
		T_FINAL => 'php-kw',
		T_PRIVATE => 'php-kw',
		T_PROTECTED => 'php-kw',
		T_PUBLIC => 'php-kw',
		T_VAR => 'php-kw',
		T_UNSET => 'php-kw',
		T_ISSET => 'php-kw',
		T_EMPTY => 'php-kw',
		T_HALT_COMPILER => 'php-kw',
		T_CLASS => 'php-kw',
		T_TRAIT => 'php-kw',
		T_INTERFACE => 'php-kw',
		T_EXTENDS => 'php-kw',
		T_IMPLEMENTS => 'php-kw',
		T_LIST => 'php-kw',
		T_ARRAY => 'php-kw',
		T_NAMESPACE => 'php-kw',
		T_LNUMBER => 'php-num',
		T_DNUMBER => 'php-num',
		T_CONSTANT_ENCAPSED_STRING => 'php-str',
		T_ENCAPSED_AND_WHITESPACE => 'php-str',
		T_VARIABLE => 'php-var',
		T_COMMENT => 'php-comment',
		T_DOC_COMMENT => 'php-comment',
	];

	public array $identifierClass = [
		'true' => 'php-kw',
		'false' => 'php-kw',
		'null' => 'php-kw',
	];


	public function highlight(string $source): string
	{
		$align = strlen(strval(substr_count($source, "\n")));

		$lineStart = "<div id=\"%1\$d\" class=\"source-line\"><a class=\"source-lineNum\" href=\"#%1\$d\">%1\${$align}d: </a>"; // TODO: use PHP 8 * modifier
		$lineEnd = '</div>';

		$line = 1;
		$out = sprintf($lineStart, $line);

		foreach ($this->tokenize($source) as $id => $text) {
			if ($text === "\n") {
				$out .= $lineEnd . sprintf($lineStart, ++$line);

			} else {
				$html = htmlspecialchars($text);
				$class = $this->tokenClass[$id] ?? ($id === T_STRING ? $this->identifierClass[strtolower($text)] ?? null : null);
				$out .= $class ? sprintf('<span class="%s">%s</span>', $class, $html) : $html;
			}
		}

		return $out . $lineEnd;
	}


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
