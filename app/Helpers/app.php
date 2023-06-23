<?php
if (!function_exists('getResponseArray')) {
    function getResponseArray($response){
        return json_decode($response->getContent(), true);
    }
}

if (!function_exists('formatException')) {
    function formatException($error = null){
        return config('app.debug') ? [$error] : ['Error processing request.'];
    }
}

if (!function_exists('formatValidate')) {
    function formatValidate($errors){
        $data = [];
        foreach ($errors->toArray() as $errors) {
            foreach ($errors as $error) {
                $data[] = $error;
            }
        }
        return $data;
    }
}
