<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class NormalizeMultilingualInput
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('POST') || $request->isMethod('PUT') || $request->isMethod('PATCH')) {
            $input = $request->all();
            
            if ($this->normalizeMultilingual($input)) {
                $request->replace($input);
            }
        }

        return $next($request);
    }

    /**
     * Recursively normalize multilingual inputs by auto-filling empty fields.
     */
    private function normalizeMultilingual(array &$data): bool
    {
        $modified = false;

        foreach ($data as $key => &$value) {
            if (is_array($value)) {
                if ($this->normalizeMultilingual($value)) {
                    $modified = true;
                }
            } elseif (is_string($key)) {
                if (str_ends_with($key, '_ar')) {
                    $base = substr($key, 0, -3);
                    $enKey = $base . '_en';
                    if (!empty($value) && empty($data[$enKey])) {
                        $data[$enKey] = $value;
                        $modified = true;
                    }
                } elseif (str_ends_with($key, '_en')) {
                    $base = substr($key, 0, -3);
                    $arKey = $base . '_ar';
                    if (!empty($value) && empty($data[$arKey])) {
                        $data[$arKey] = $value;
                        $modified = true;
                    }
                }
            }
        }

        return $modified;
    }
}
