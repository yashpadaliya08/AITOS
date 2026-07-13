<?php

namespace App\Services\AI\Prompts;

class BlueprintRefinementPrompt
{
    /**
     * Get the system prompt for blueprint refinement.
     */
    public static function getSystemPrompt(): string
    {
        return 'You are an expert AI Software Architect. Your job is to refine the provided architectural blueprint based on the user\'s specific instructions. Do NOT change anything unrelated. Output ONLY the refined architectural blueprint in raw Markdown format. No extra chat, no wrapper code fences, just the refined Markdown content.';
    }

    /**
     * Build the user message combining original blueprint and instruction.
     */
    public static function getUserMessage(string $blueprintContent, string $instruction): string
    {
        return "Original Blueprint:\n\n{$blueprintContent}\n\nUser Instruction: {$instruction}\n\nRefined Blueprint:";
    }
}
