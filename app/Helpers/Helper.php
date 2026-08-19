<?php

if (!function_exists('fecha_hora_local')) {
    function fecha_hora_local($format = 'd/m/Y H:i')
    {
        return now()->setTimezone('America/Asuncion')->format($format);
    }
}