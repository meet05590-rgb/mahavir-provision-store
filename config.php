<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();
define('APP_NAME','Mahavir Provision Store');
$host=getenv('DB_HOST') ?: 'localhost'; $db=getenv('DB_NAME') ?: 'mahavir_store'; $user=getenv('DB_USER') ?: 'root'; $pass=getenv('DB_PASS') ?: '';
try { $pdo=new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4",$user,$pass,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false]); }
catch(PDOException $e){ http_response_code(500); exit('Database connection failed. Check config.php and import database.sql.'); }
function e($v): string { return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8'); }
function current_user(): ?array { return $_SESSION['user'] ?? null; }
function require_login(): void { if(!current_user()){ header('Location: ../login.php'); exit; } }
function require_admin(): void { require_login(); if(($_SESSION['user']['role']??'')!=='admin'){ http_response_code(403); exit('Admin access required.'); } }
function csrf_token(): string { if(empty($_SESSION['csrf'])) $_SESSION['csrf']=bin2hex(random_bytes(32)); return $_SESSION['csrf']; }
function verify_csrf(): void { if($_SERVER['REQUEST_METHOD']==='POST' && !hash_equals($_SESSION['csrf']??'',$_POST['csrf']??'')){ http_response_code(419); exit('Invalid form token. Refresh and try again.'); } }
