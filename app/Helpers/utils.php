<?php

if (!function_exists('previous_page')) {
    /**
     * Convierte un valor Point de Postgresql a un array con dos valores.
     */
    function previous_page($default): string
    {
        $current = request()->fullUrl();
        $session = \Illuminate\Support\Facades\Session::previousUrl();

        return $current == $session ? $default : $session;
    }
}

if (!function_exists('get_age')) {
    /**
     * Convierte un valor Point de Postgresql a un array con dos valores.
     */
    function get_age($date): string
    {
        return (new \Illuminate\Support\Carbon($date . ' 00:00:00'))->age;
    }
}

if (!function_exists('time_ago')) {
    /**
     * Convierte un valor Point de Postgresql a un array con dos valores.
     */
    function time_ago($datetime): string
    {
        return (new \Illuminate\Support\Carbon($datetime))->ago();
    }
}

if (!function_exists('file_url')) {
    /**
     * Convierte un valor Point de Postgresql a un array con dos valores.
     */
    function file_url($path): string
    {
        return \Illuminate\Support\Facades\Storage::url($path);
    }
}

if (!function_exists('status_message')) {
    /**
     * Convierte un valor Point de Postgresql a un array con dos valores.
     */
    function status_message($status, $short = false): string
    {
        $message = '';

        switch ($status) {
            case 'observed':
                $message = $short ? 'Observado' : 'Modificar su información requerida';
                break;
            case 'approved':
                $message = $short ? 'Aprobado' : 'Solicitud aprobada por el responsable GH';
                break;
            case 'no_requested':
                $message = $short ? 'No Solicitado' : 'Debes adjuntar los formatos de préstamo';
                break;
            default:
                $message = $short ? 'Pendiente de Revisión' : 'El responsable GH está revisando su información';
                break;
        }

        return $message;
    }
}

if (!function_exists('array_to_pgarray')) {
    /**
     * Convierte un valor Point de Postgresql a un array con dos valores.
     */
    function array_to_pgarray(?array $dta): ?string
    {
        if (is_null($dta)) {
            return null;
        }

        if (empty($dta)) {
            return '{}';
        }

        return str_replace(['[', ']'], ['{', '}'], json_encode($dta));
    }
}

if (!function_exists('pgarray_to_array')) {
    function pgarray_to_array($s, $start = 0, &$end = null)
    {
        if (empty($s) || '{' != $s[0]) {
            return null;
        }

        $return = [];
        $string = false;
        $quote = '';
        $len = strlen($s);
        $v = '';

        for ($i = $start + 1; $i < $len; ++$i) {
            $ch = $s[$i];

            if (!$string && '}' == $ch) {
                if ('' !== $v || !empty($return)) {
                    $return[] = $v;
                }

                $end = $i;
                break;
            } elseif (!$string && '{' == $ch) {
                $v = pgarray_to_array($s, $i, $i);
            } elseif (!$string && ',' == $ch) {
                $return[] = $v;
                $v = '';
            } elseif (!$string && ('"' == $ch || "'" == $ch)) {
                $string = true;
                $quote = $ch;
            } elseif ($string && $ch == $quote && '\\' == $s[$i - 1]) {
                $v = substr($v, 0, -1) . $ch;
            } elseif ($string && $ch == $quote && '\\' != $s[$i - 1]) {
                $string = false;
            } else {
                $v .= $ch;
            }
        }

        return $return;
    }
}

if (!function_exists('cast_pgarray')) {
    function cast_pgarray(&$row, array $fields = [])
    {
        foreach ($fields as $field) {
            if (\Illuminate\Support\Arr::has($row, $field)) {
                \Illuminate\Support\Arr::set(
                    $row,
                    $field,
                    pgarray_to_array(\Illuminate\Support\Arr::get($row, $field))
                );
            }
        }
    }
}
