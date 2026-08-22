<?php
require_once __DIR__ . '/includes/auth.php';
if (is_logged_in()) { header('Location: index.php'); exit; }
$pageTitle = 'Crear Cuenta';
$extraScripts = ['assets/js/register.js'];
require __DIR__ . '/includes/page-start.php';
?>

<main class="u-flex-1">
  <div class="auth-wrap">
    <div class="auth-visual" style="background-image:url('https://images.unsplash.com/photo-1544568100-847a948585b9?w=1000&q=80');">
      <a href="index.php" class="logo">Veterinaria VetAnimal</a>
      <h2>Sumate a la<br>familia VetAnimal.</h2>
      <p>Creá tu cuenta para reservar turnos y llevar el historial de tu mascota.</p>
    </div>

    <div class="auth-form-side">
      <div class="auth-form">
        <h1>Creá tu cuenta</h1>
        <p>Es rápido y gratuito. Empezá a cuidar a tu compañero hoy mismo.</p>

        <div id="register-error" class="alert alert-error" style="display:none;"></div>

        <form id="register-form">
          <div class="field">
            <label>Nombre Completo</label>
            <div class="input-wrap">
              <span>👤</span>
              <input type="text" id="nombre" placeholder="Tu nombre" required>
            </div>
          </div>

          <div class="field">
            <label>Dirección de Email</label>
            <div class="input-wrap">
              <span>✉</span>
              <input type="email" id="email" placeholder="nombre@ejemplo.com" required>
            </div>
          </div>

          <div class="field">
            <label>Teléfono</label>
            <div class="input-wrap">
              <span>📞</span>
              <input type="text" id="telefono" placeholder="11-1234-5678">
            </div>
          </div>

          <div class="field">
            <label>Contraseña</label>
            <div class="input-wrap">
              <span>🔒</span>
              <input type="password" id="password" placeholder="Mínimo 6 caracteres" required>
            </div>
          </div>

          <div class="field">
            <label>Confirmar Contraseña</label>
            <div class="input-wrap">
              <span>🔒</span>
              <input type="password" id="password2" placeholder="Repetí tu contraseña" required>
            </div>
          </div>

          <button type="submit" class="btn btn-primary btn-block u-mt-4">Crear Cuenta</button>
        </form>

        <div class="auth-foot">
          ¿Ya tenés una cuenta? <a href="login.php" class="link-accent">Iniciar Sesión</a>
        </div>
      </div>
    </div>
  </div>
</main>

<?php require __DIR__ . '/includes/page-end.php'; ?>
