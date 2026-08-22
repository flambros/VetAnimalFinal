<?php
/**
 * Apertura del documento HTML. Cada página define $pageTitle antes de
 * incluir este archivo. Las rutas de CSS/JS son relativas a la raíz del
 * sitio, así que en subcarpetas (admin/) hay que usar $assetBase = '../'.
 */
$assetBase = $assetBase ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' · ' : '' ?>Veterinaria VetAnimal</title>
  <link rel="stylesheet" href="<?= $assetBase ?>assets/css/style.css">
</head>
<body style="min-height:100vh; display:flex; flex-direction:column; justify-content:space-between;">
