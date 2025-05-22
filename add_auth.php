<?php
$files = ['cases.php', 'reports.php', 'archive.php', 'settings.php'];

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        if (strpos($content, 'require_once \'configs/auth.php\';') === false) {
            $newContent = "<?php\nrequire_once 'configs/auth.php';\ncheckAuth();\n?>\n" . $content;
            file_put_contents($file, $newContent);
            echo "Added authentication to $file\n";
        } else {
            echo "Authentication already exists in $file\n";
        }
    } else {
        echo "File $file not found\n";
    }
}
?> 