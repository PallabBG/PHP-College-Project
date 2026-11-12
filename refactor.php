<?php
$dir = new RecursiveDirectoryIterator(__DIR__);
$ite = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($ite, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

$replacements = [
    '/<\?php\s+if\s*\((.*?)\):\s*\?>/' => '<?php if ($1) { ?>',
    '/<\?php\s+elseif\s*\((.*?)\):\s*\?>/' => '<?php } elseif ($1) { ?>',
    '/<\?php\s+else:\s*\?>/' => '<?php } else { ?>',
    '/<\?php\s+endif;\s*\?>/' => '<?php } ?>',
    '/<\?php\s+foreach\s*\((.*?)\):\s*\?>/' => '<?php foreach ($1) { ?>',
    '/<\?php\s+endforeach;\s*\?>/' => '<?php } ?>',
    '/if\s*\(\$_SERVER\[\'REQUEST_METHOD\'\]\s*===\s*\'POST\'\)/' => 'if (isset($_POST[\'submit\']))'
];

foreach($files as $file) {
    $path = $file[0];
    if (strpos($path, 'refactor.php') !== false) continue;
    
    $content = file_get_contents($path);
    $original = $content;
    
    foreach ($replacements as $pattern => $replacement) {
        $content = preg_replace($pattern, $replacement, $content);
    }
    
    if ($content !== $original) {
        file_put_contents($path, $content);
        echo "Refactored: $path\n";
    }
}
echo "Done.\n";
