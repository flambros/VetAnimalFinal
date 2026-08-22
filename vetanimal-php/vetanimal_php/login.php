<?php
require_once __DIR__ . '/includes/auth.php';
if (is_logged_in()) { header('Location: index.php'); exit; }
$pageTitle = 'Iniciar Sesión';
$extraScripts = ['assets/js/login.js'];
require __DIR__ . '/includes/page-start.php';
?>

<main class="u-flex-1">
  <div class="auth-wrap">
    <div class="auth-visual" style="background-image:url('https://images.unsplash.com/photo-1583337130417-3346a1be7dee?w=1000&q=80');">
      <a href="index.php" class="logo">Veterinaria VetAnimal</a>
      <h2>Cuidado Compasivo<br>para Cada Compañero.</h2>
      <p>La salud y felicidad de tu mascota son nuestra máxima prioridad.</p>
    </div>

    <div class="auth-form-side">
      <div class="auth-form">
        <h1>¡Hola de nuevo!</h1>
        <p>Iniciá sesión para gestionar turnos, ver registros y conectarte con tu equipo de atención.</p>

        <div id="login-error" class="alert alert-error" style="display:none;"></div>

        <form id="login-form">
          <div class="field">
            <label>Dirección de Email</label>
            <div class="input-wrap">
              <span>✉</span>
              <input type="email" id="email" name="email" placeholder="nombre@ejemplo.com" required>
            </div>
          </div>

          <div class="field">
            <label>Contraseña</label>
            <div class="input-wrap">
              <span>🔒</span>
              <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>
          </div>

          <div class="field-row">
            <label class="checkbox-row">
              <input type="checkbox" id="remember"> Recordarme
            </label>
            <a href="forgot-password.php" class="link-accent">¿Olvidaste tu contraseña?</a>
          </div>

          <button type="submit" class="btn btn-primary btn-block">Iniciar Sesión</button>
        </form>

        <div class="divider-text">O continúa con</div>
        <button type="button" class="oauth-btn" onclick="alert('Integración disponible próximamente.')">Google</button>
        <button type="button" class="oauth-btn" onclick="alert('Integración disponible próximamente.')">Apple</button>

        <div class="auth-foot">
          ¿No tienes una cuenta? <a href="register.php" class="link-accent">Registrarse</a>
        </div>

        <div class="alert u-mt-6" style="background:#eef0eb; color:#5c6b60;">
          <strong class="u-font-bold" style="display:block; margin-bottom:4px; color:#1f2937;">Cuentas de prueba rápidas:</strong>
          <div class="u-flex u-gap-2 u-mt-2">
            <button type="button" id="quick-cliente" class="btn btn-sm btn-outline u-text-xs" style="background:#fff;">Ingresar como Cliente</button>
            <button type="button" id="quick-vet" class="btn btn-sm btn-primary u-text-xs">Ingresar como Vet</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<?php require __DIR__ . '/includes/page-end.php'; ?>
