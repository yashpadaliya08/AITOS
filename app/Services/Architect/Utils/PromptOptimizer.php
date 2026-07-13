<?php

namespace App\Services\Architect\Utils;

class PromptOptimizer
{
    /**
     * Remove duplicated information, trailing whitespace, and excess newlines for token efficiency.
     *
     * @param string $text Raw prompt content
     * @return string Optimized prompt content
     */
    public static function optimize(string $text): string
    {
        // 1. Remove duplicate lines
        $lines = explode("\n", $text);
        $seen = [];
        $uniqueLines = [];
        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '') {
                $uniqueLines[] = $line;
                continue;
            }
            if (isset($seen[$trimmed])) {
                continue;
            }
            $seen[$trimmed] = true;
            $uniqueLines[] = $line;
        }

        $optimized = implode("\n", $uniqueLines);

        // 2. Reduce multiple sequential newlines to double newlines
        $optimized = preg_replace("/\n{3,}/", "\n\n", $optimized);

        return trim($optimized);
    }
}
