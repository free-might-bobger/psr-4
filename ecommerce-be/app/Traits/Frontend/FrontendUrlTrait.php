<?php 
namespace App\Traits\Frontend;

trait FrontendUrlTrait {
    
    public static function getUrl(){
        $frontendUrl = rtrim((string) env('FRONTEND_URL', ''), '/');

        if ($frontendUrl === '') {
            $request = request();
            $host = (string) $request->getHost();
            $scheme = (string) $request->getScheme();

            // Common deployment pattern: API on api.example.com, SPA on example.com.
            $frontendHost = preg_replace('/^api\./i', '', $host) ?: $host;
            $frontendUrl = $scheme . '://' . $frontendHost;
        }
        return $frontendUrl;
    }
}
