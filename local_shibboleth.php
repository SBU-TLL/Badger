<?php
// Only run this logic if the application is running locally via DDEV
if (getenv('IS_DDEV_PROJECT') == 'true') {
    $envFile = __DIR__ . '/.env';
    
    if (file_exists($envFile)) {
        // parse_ini_file natively handles simple key=value pairs
        $envVars = parse_ini_file($envFile);
        
        if ($envVars) {
            foreach ($envVars as $key => $value) {
                // Inject into $_SERVER exactly as mod_shib would in production
                $_SERVER[$key] = $value;
            }
        }
    }
}
?>
