<?php
// Root router for repo-level builds
if (file_exists(__DIR__ . '/../web final project/api/index.php')) {
    require __DIR__ . '/../web final project/api/index.php';
} else {
    require __DIR__ . '/index.php';
}
