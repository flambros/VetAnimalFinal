<?php
require_once __DIR__ . '/includes/auth.php';
require_login_page();
$currentPath = '/booking';
$pageTitle = 'Reservar Turno';
$extraScripts = ['assets/js/booking.js'];
require __DIR__ . '/includes/page-start.php';
require __DIR__ . '/includes/header.php';
$__user = current_user();
?>
<script>window.CURRENT_USER_ID = <?= (int)$__user['id'] ?>;</script>

<main class="u-flex-1">
  <div class="wizard-page">

    <div class="wizard-steps">
      <div class="wizard-step active" id="step-indicator-1">
        <div class="step-circle">1</div>
        <span class="label">Mascota y Servicio</span>
      </div>
      <div class="wizard-line" id="line-1"></div>
      <div class="wizard-step" id="step-indicator-2">
        <div class="step-circle">2</div>
        <span class="label">Fecha y Hora</span>
      </div>
      <div class="wizard-line" id="line-2"></div>
      <div class="wizard-step" id="step-indicator-3">
        <div class="step-circle">3</div>
        <span class="label">Revisión</span>
      </div>
    </div>

    <div id="wizard-error" class="alert alert-error" style="max-width:1100px; margin:0 auto 20px; display:none;"></div>

    <!-- STEP 1 -->
    <div id="step-1">
      <h1 class="wizard-title">Elegí tu Mascota y Servicio</h1>
      <p class="wizard-sub">Seleccioná para quién es la consulta y qué tipo de atención necesita.</p>

      <div class="wizard-grid" style="grid-template-columns:1fr;">
        <div class="wizard-panel">
          <h3 class="u-mb-4">Tu mascota</h3>
          <div id="pets-empty" class="empty-state" style="display:none;">
            Todavía no tenés mascotas registradas.
            <div class="u-mt-4">
              <a href="mascota-nueva.php" class="btn btn-primary btn-sm">+ Agregar Mascota</a>
            </div>
          </div>
          <div id="pets-wrapper" style="display:none;">
            <div class="pet-select-grid" id="pet-select-grid"></div>
            <a href="mascota-nueva.php" class="link-accent u-text-sm u-mt-3" style="display:inline-block;">+ Agregar otra mascota</a>
          </div>

          <h3 class="u-mt-8 u-mb-4">Tipo de servicio</h3>
          <div class="service-select-grid" id="service-select-grid"></div>
        </div>
      </div>

      <div class="wizard-actions" style="justify-content:flex-end;">
        <button type="button" id="btn-step1-next" class="btn btn-primary" disabled>Continuar →</button>
      </div>
    </div>

    <!-- STEP 2 -->
    <div id="step-2" style="display:none;">
      <h1 class="wizard-title">Seleccionar Fecha y Hora</h1>
      <p class="wizard-sub" id="step2-sub">Elegí un horario conveniente.</p>

      <div class="wizard-grid">
        <div style="display:flex; gap:24px; flex-wrap:wrap; flex:1;">
          <div class="calendar-panel" style="flex:1; min-width:280px;">
            <div class="cal-head">
              <h3 id="cal-month-label"></h3>
              <div class="cal-nav">
                <button type="button" id="cal-prev">‹</button>
                <button type="button" id="cal-next">›</button>
              </div>
            </div>
            <div class="cal-grid" id="cal-grid"></div>
          </div>

          <div class="times-panel" style="flex:1; min-width:280px;">
            <h3>Horarios Disponibles</h3>
            <div id="times-empty" class="u-muted u-mt-4">Seleccioná primero una fecha en el calendario.</div>
            <div id="times-wrapper" style="display:none;">
              <p class="u-muted" style="margin-top:6px;">Fecha elegida: <span id="selected-date-label"></span></p>
              <div class="times-grid" id="times-grid"></div>
            </div>
          </div>
        </div>

        <div class="summary-card">
          <h3>Resumen del Turno</h3>
          <div class="summary-row">
            <img id="summary-pet-img" src="https://images.unsplash.com/photo-1552053831-71594a27632d?w=200&q=80" alt="mascota">
            <div>
              <small>Paciente</small>
              <strong id="summary-pet-name"></strong>
              <span class="detail" id="summary-pet-detail"></span>
            </div>
          </div>
          <div class="summary-divider"></div>
          <div class="summary-row">
            <div class="icon-badge">🩺</div>
            <div>
              <small>Servicio</small>
              <strong id="summary-service-name"></strong>
              <span class="detail" id="summary-service-detail"></span>
            </div>
          </div>
          <div id="summary-selected" class="summary-selected" style="display:none;">
            <small>Horario Seleccionado</small>
            <strong id="summary-selected-text"></strong>
            <div class="ok" id="summary-ok" style="display:none;">✓ Selección Confirmada</div>
          </div>
        </div>
      </div>

      <div class="wizard-actions">
        <button type="button" id="btn-step2-back" class="btn btn-outline">← Volver</button>
        <button type="button" id="btn-step2-next" class="btn btn-primary" disabled>Continuar →</button>
      </div>
    </div>

    <!-- STEP 3 -->
    <div id="step-3" style="display:none;">
      <h1 class="wizard-title">Revisá tu Turno</h1>
      <p class="wizard-sub">Confirmá los datos antes de reservar.</p>

      <div class="wizard-grid">
        <div class="wizard-panel">
          <div class="review-block">
            <h4>Paciente</h4>
            <div class="summary-row" style="margin-bottom:0;">
              <img id="review-pet-img" src="https://images.unsplash.com/photo-1552053831-71594a27632d?w=200&q=80" alt="mascota">
              <div>
                <strong id="review-pet-name"></strong>
                <span class="detail" id="review-pet-detail"></span>
              </div>
            </div>
          </div>

          <div class="review-block">
            <h4>Servicio</h4>
            <p><strong id="review-service-name"></strong> — <span id="review-service-desc"></span></p>
          </div>

          <div class="review-block">
            <h4>Fecha y hora</h4>
            <p><strong id="review-date"></strong> a las <span id="review-time"></span> hs</p>
          </div>

          <div class="review-block">
            <h4>Notas para el equipo (opcional)</h4>
            <textarea id="notas" class="wizard-notes" placeholder="Contanos si hay algo importante que debamos saber..."></textarea>
          </div>
        </div>

        <div class="summary-card">
          <h3>Resumen del Turno</h3>
          <div class="summary-row">
            <div class="icon-badge">🐾</div>
            <div><small>Paciente</small><strong id="review-summary-pet"></strong></div>
          </div>
          <div class="summary-row">
            <div class="icon-badge">🩺</div>
            <div><small>Servicio</small><strong id="review-summary-service"></strong></div>
          </div>
          <div class="summary-selected">
            <small>Turno</small>
            <strong id="review-summary-datetime"></strong>
          </div>
        </div>
      </div>

      <div class="wizard-actions">
        <button type="button" id="btn-step3-back" class="btn btn-outline">← Volver</button>
        <button type="button" id="btn-confirm" class="btn btn-primary">Confirmar Turno ✓</button>
      </div>
    </div>

    <!-- CONFIRMATION -->
    <div id="step-confirmed" style="display:none;">
      <div style="max-width:560px; margin:60px auto; text-align:center;">
        <div style="font-size:3.4rem;">✅</div>
        <h1 style="margin:18px 0 12px;">¡Turno confirmado!</h1>
        <p class="u-muted" style="margin-bottom:30px;" id="confirmed-text"></p>
        <div class="u-flex u-gap-4 u-justify-center">
          <a href="perfil.php" class="btn btn-primary">Ver Mis Turnos</a>
          <a href="index.php" class="btn btn-outline">Volver al Inicio</a>
        </div>
      </div>
    </div>

  </div>
</main>

<?php
require __DIR__ . '/includes/footer.php';
require __DIR__ . '/includes/page-end.php';
?>
