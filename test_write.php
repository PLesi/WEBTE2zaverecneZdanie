<?php
$testLogFile = '/tmp/php_write_test.log';
$message = "Test log entry from " . date('Y-m-d H:i:s') . "\n";

// Attempt to write using file_put_contents (basic file I/O)
$fp = fopen($testLogFile, 'a');
if ($fp) {
    fwrite($fp, "FILE_PUT_CONTENTS: " . $message);
    fclose($fp);
    echo "Attempted file_put_contents write to $testLogFile.<br>";
} else {
    echo "Failed to open $testLogFile for writing with fopen/fwrite.<br>";
}

// Attempt to write using error_log (more PHP-specific logging)
if (error_log("ERROR_LOG (mode 3): " . $message, 3, $testLogFile)) {
    echo "Attempted error_log (mode 3) write to $testLogFile.<br>";
} else {
    echo "Failed error_log (mode 3) write to $testLogFile.<br>";
}

echo "If you see this, PHP is executing. Check $testLogFile for output.<br>";
?>