<?php

namespace App\Helpers;

class DataTableHelper
{
    /**
     * Convert snake_case or camelCase to Proper Case.
     *
     * Example: first_name → First Name
     */
    public static function formatTitle(string $column): string
    {
        // Convert camelCase → snake_case first
        $snake = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $column));

        // Replace underscores with spaces and capitalize words
        return ucwords(str_replace('_', ' ', $snake));
    }
}
