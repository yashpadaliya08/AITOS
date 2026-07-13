<?php

namespace App\Services\Architect\Contracts;

interface FrameworkProviderInterface
{
    /**
     * Get the framework identifier (e.g. 'laravel', 'fastapi').
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get the directory layout hierarchy for the framework.
     *
     * @return array List of folder paths
     */
    public function getFolderStructure(): array;

    /**
     * Generate framework-specific skeleton placeholder files based on data schemas and code generation models.
     *
     * @param array $data Compound array structure containing 'schema' and 'code_gen_model'
     * @return array Structure: [filepath => file_content]
     */
    public function getPlaceholderFiles(array $data): array;

    /**
     * Get base configuration file templates (e.g. package.json, composer.json).
     *
     * @return array Structure: [filepath => file_content]
     */
    public function getConfigFileTemplates(): array;
}
