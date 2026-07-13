<?php

namespace App\Services\Architect\Utils;

class AISessionManager
{
    /**
     * Normalize and format session data for storage inside the AITOS package.
     *
     * @param array $sessions Raw sessions from the client state
     * @return array Mapped sessions by filename key
     */
    public static function formatSessions(array $sessions): array
    {
        $files = [];

        foreach ($sessions as $index => $session) {
            $provider = $session['provider'] ?? 'Generic AI';
            $model = $session['model'] ?? 'Unknown Model';
            $date = $session['date'] ?? date('Y-m-d H:i');
            $developer = $session['developer'] ?? 'Developer';
            $task = $session['task'] ?? 'General Development';
            $summary = $session['summary'] ?? 'Session completed successfully.';
            $filesModified = $session['files_modified'] ?? [];
            $decisions = $session['decisions'] ?? [];
            $status = $session['status'] ?? 'completed';

            $sessionContent = "# AI Development Session Report\n\n" .
                "- **Date:** {$date}\n" .
                "- **Developer/Operator:** {$developer}\n" .
                "- **AI Provider:** {$provider}\n" .
                "- **AI Model:** {$model}\n" .
                "- **Assigned Task:** {$task}\n" .
                "- **Status:** " . strtoupper($status) . "\n\n" .
                "## Session Summary\n" .
                "{$summary}\n\n" .
                "## Files Modified\n";
            
            if (empty($filesModified)) {
                $sessionContent .= "- None\n";
            } else {
                foreach ($filesModified as $file) {
                    $sessionContent .= "- `{$file}`\n";
                }
            }

            $sessionContent .= "\n## Decisions Recorded\n";
            if (empty($decisions)) {
                $sessionContent .= "- None\n";
            } else {
                foreach ($decisions as $decision) {
                    $sessionContent .= "- **" . ($decision['title'] ?? 'Decision') . "**: " . ($decision['desc'] ?? '') . "\n";
                }
            }

            $safeFilename = 'session_' . date('Ymd_His', strtotime($date)) . '_' . ($index + 1) . '.md';
            $files[$safeFilename] = $sessionContent;
        }

        return $files;
    }
}
