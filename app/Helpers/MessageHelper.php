<?php

namespace App\Helpers;

use App\Models\ResponseTemplates;

class MessageHelper
{
    /**
     * Get message template by key and replace placeholders
     *
     * @param string $key
     * @param array $replacements
     * @return string
     */
    public static function get(string $name, array $replacements = []): string
    {
        $template = ResponseTemplates::where('name', $name)->first();

        $message = $template ? $template->message : '';

        foreach ($replacements as $placeholder => $value) {
            $message = str_replace("{{$placeholder}}", $value, $message);
        }

        return $message;
    }
}
