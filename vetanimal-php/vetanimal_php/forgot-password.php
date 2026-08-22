<?php
require_once __DIR__ . '/includes/auth.php';
$pageTitle = 'Restablecer contraseña';
$extraScripts = ['assets/js/forgot-password.js'];
require __DIR__ . '/includes/page-start.php';
?>

<main class="u-flex-1 u-flex-col u-items-center u-justify-center" style="display:flex; align-items:center; justify-content:center; padding:24px; background:#f7f6f2;">
  <a href="index.php" class="logo u-text-2xl u-font-bold u-mb-6" style="display:block; margin-bottom:24px;">Veterinaria VetAnimal</a>

  <div class="u-card u-shadow u-max-w-md u-text-center" style="padding:40px;">
    <h1 class="u-text-2xl u-font-semibold u-mb-3">Restablece tu contraseña</h1>
    <p class="u-muted u-mb-6 u-text-sm">
      Ingresa tu dirección de correo electrónico y te enviaremos
      instrucciones para restablecer tu contraseña.
    </p>

    <div id="forgot-success" class="alert alert-success u-text-sm" style="display:none;"></div>

    <form id="forgot-form" class="u-text-left">
      <div class="field">
        <label>Dirección de Correo Electrónico</label>
        <div class="input-wrap">
          <span>✉</span>
          <input type="email" id="email" placeholder="nombre@ejemplo.com" required>
        </div>
      </div>
      <button type="submit" class="btn btn-primary btn-block u-mt-4">➤ Enviar enlace de recuperación</button>
    </form>

    <div class="u-mt-4">
      <a href="login.php" class="link-accent u-text-sm">← Volver al inicio de sesión</a>
    </div>
  </div>
</main>

<?php require __DIR__ . '/includes/page-end.php'; ?>
