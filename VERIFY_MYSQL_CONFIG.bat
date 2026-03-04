@echo off
REM ============================================
REM MySQL Configuration Verification Script
REM ============================================
echo.
echo ============================================
echo MySQL CONFIGURATION VERIFICATION
echo ============================================
echo.
echo Checking current MySQL configuration...
echo.

cd /d "%~dp0"

php -r "try { $pdo = new PDO('mysql:host=127.0.0.1;dbname=autolearn_db', 'root', ''); echo 'Database connection: OK' . PHP_EOL; $queries = [ 'innodb_buffer_pool_size' => ['Expected' => '536870912 (512M)', 'Query' => 'SELECT @@innodb_buffer_pool_size as value'], 'innodb_log_file_size' => ['Expected' => '134217728 (128M)', 'Query' => 'SELECT @@innodb_log_file_size as value'], 'innodb_flush_log_at_trx_commit' => ['Expected' => '2', 'Query' => 'SELECT @@innodb_flush_log_at_trx_commit as value'], 'sql_mode' => ['Expected' => 'Contains STRICT_TRANS_TABLES', 'Query' => 'SELECT @@sql_mode as value'], 'collation_server' => ['Expected' => 'utf8mb4_unicode_ci', 'Query' => 'SELECT @@collation_server as value'], 'time_zone' => ['Expected' => '+00:00', 'Query' => 'SELECT @@time_zone as value'], ]; echo PHP_EOL . '----------------------------------------' . PHP_EOL; foreach ($queries as $name => $config) { $stmt = $pdo->query($config['Query']); $result = $stmt->fetch(PDO::FETCH_ASSOC); $value = $result['value']; echo $name . ':' . PHP_EOL; echo '  Current: ' . $value . PHP_EOL; echo '  Expected: ' . $config['Expected'] . PHP_EOL; if ($name === 'sql_mode') { echo '  Status: ' . (strpos($value, 'STRICT_TRANS_TABLES') !== false ? '[OK]' : '[NEEDS FIX]') . PHP_EOL; } elseif ($name === 'innodb_buffer_pool_size') { echo '  Status: ' . ($value == 536870912 ? '[OK]' : '[NEEDS FIX]') . PHP_EOL; } elseif ($name === 'innodb_log_file_size') { echo '  Status: ' . ($value == 134217728 ? '[OK]' : '[NEEDS FIX]') . PHP_EOL; } elseif ($name === 'innodb_flush_log_at_trx_commit') { echo '  Status: ' . ($value == 2 ? '[OK]' : '[INFO: Set to 1 for production]') . PHP_EOL; } elseif ($name === 'collation_server') { echo '  Status: ' . ($value === 'utf8mb4_unicode_ci' ? '[OK]' : '[INFO: Different collation]') . PHP_EOL; } elseif ($name === 'time_zone') { echo '  Status: ' . ($value === '+00:00' ? '[OK]' : '[NEEDS FIX]') . PHP_EOL; } echo '----------------------------------------' . PHP_EOL; } } catch (Exception $e) { echo 'ERROR: ' . $e->getMessage() . PHP_EOL; exit(1); }"

echo.
echo Verification complete!
echo.
pause
