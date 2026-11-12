<?php
require_once __DIR__.'/config.php';
function login_user(PDO $pdo,string $email,string $password): bool { $q=$pdo->prepare('SELECT id,name,email,password,role FROM users WHERE email=?'); $q->execute([$email]); $u=$q->fetch(); if(!$u || !password_verify($password,$u['password'])) return false; session_regenerate_id(true); unset($u['password']); $_SESSION['user']=$u; return true; }
