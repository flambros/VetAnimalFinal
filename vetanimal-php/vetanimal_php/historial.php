<?php
require_once __DIR__ . '/includes/auth.php';
require_login_page();
$currentPath = '/historial';
$pageTitle = 'Historial Clínico';
$extraScripts = ['assets/js/historial.js'];
require __DIR__ . '/includes/page-start.php';
require __DIR__ . '/includes/header.php';
$__user = current_user();
?>
<script>
  window.CURRENT_USER_ID = <?= (int)$__user['id'] ?>;
  window.CURRENT_USER_ROL = <?= json_encode($__user['rol']) ?>;
</script>

<main class="u-flex-1">
  <div class="container section">

    <div id="empty-no-pets" class="empty-state" style="display:none;">
      <h2>No tenés mascotas registradas.</h2>
      <p class="u-mt-2 u-mb-6" style="color:#4b5563;">Para ver diagnósticos e historial clínico, agregá una mascota.</p>
      <a href="mascota-nueva.php" class="btn btn-primary">+ Registrar Mascota</a>
    </div>

    <div id="hist-content" style="display:none;">
      <div class="hist-title-row">
        <div>
          <h1>Historial Clínico Digital</h1>
          <span class="muted">Registro médico completo y actualizado de tus mascotas.</span>
        </div>
        <div id="pet-selector-wrap" class="u-flex u-items-center u-gap-2" style="display:none;">
          <span class="u-text-sm u-font-semibold">Seleccionar mascota:</span>
          <select id="pet-selector" style="border:1px solid var(--borde); border-radius:8px; padding:8px 12px; background:#fff;"></select>
        </div>
      </div>

      <div class="hist-grid">
        <div class="pet-card">
          <img id="pet-photo" src="https://images.unsplash.com/photo-1552053831-71594a27632d?w=500&q=80" alt="mascota">
          <div class="pet-info">
            <h2 class="u-text-xl u-mb-4" id="pet-name"></h2>
            <div class="pet-info-row"><span>Especie:</span><strong id="pet-especie"></strong></div>
            <div class="pet-info-row"><span>Raza:</span><strong id="pet-raza"></strong></div>
            <div class="pet-info-row"><span>Edad:</span><strong id="pet-edad"></strong></div>
            <div class="pet-info-row"><span>Peso actual:</span><strong id="pet-peso"></strong></div>
            <div class="pet-info-row"><span>Estado de salud:</span><span class="status-pill" id="pet-estado"></span></div>
            <div class="pet-info-row" id="pet-dueno-row" style="display:none;"><span>Dueño:</span><strong id="pet-dueno"></strong></div>
          </div>
        </div>

        <div class="timeline-card">
          <div class="timeline-head">
            <h2>Evolución Médica &amp; Consultas</h2>
            <button type="button" id="btn-add-consulta" class="btn btn-primary btn-sm" style="display:none;">+ Agregar Registro Médico</button>
          </div>
          <div id="timeline-list"></div>
        </div>
      </div>

      <div class="info-grid u-mt-6">
        <div class="info-card">
          <h3>💉 Registro de Vacunas</h3>
          <div id="vacunas-list"></div>
        </div>
        <div class="info-card">
          <h3>🔬 Estudios y Laboratorio</h3>
          <div id="estudios-list"></div>
        </div>
        <div class="info-card">
          <h3>⚠️ Alergias &amp; Alertas</h3>
          <p class="u-text-sm" style="color:#374151;"><strong>Alergias conocidas:</strong></p>
          <div id="alergias-list" class="u-mb-4"></div>
          <p class="u-text-sm u-mt-4" style="color:#374151;"><strong>Condiciones crónicas:</strong></p>
          <p class="u-text-sm u-muted" id="condiciones-text"></p>
        </div>
      </div>
    </div>

  </div>

  <!-- Modal nueva consulta -->
  <div id="modal-consulta" class="u-modal-overlay" style="display:none;">
    <div class="u-modal">
      <h2 class="u-text-xl u-font-bold u-mb-4">Nuevo Registro Médico para <span id="modal-pet-name"></span></h2>
      <form id="consulta-form">
        <div class="field">
          <label>Tipo de atención</label>
          <div class="input-wrap">
            <select id="consulta-tipo">
              <option value="CONTROL">Control de Rutina</option>
              <option value="EMERGENCIA">Emergencia / Urgencia</option>
              <option value="VACUNA">Vacunación</option>
              <option value="CIRUGIA">Cirugía</option>
              <option value="DIAGNOSTICO">Diagnóstico</option>
            </select>
          </div>
        </div>
        <div class="field">
          <label>Título de la consulta</label>
          <div class="input-wrap">
            <input type="text" id="consulta-titulo" placeholder="Ej: Chequeo Semestral" required>
          </div>
        </div>
        <div class="field">
          <label>Descripción y Observaciones Clínicas</label>
          <textarea id="consulta-desc" class="wizard-notes" placeholder="Detalles del examen, diagnóstico o receta..." required></textarea>
        </div>
        <div class="u-flex u-justify-end u-gap-3 u-mt-6">
          <button type="button" id="btn-cancel-consulta" class="btn btn-light">Cancelar</button>
          <button type="submit" id="btn-save-consulta" class="btn btn-primary">Guardar Registro</button>
        </div>
      </form>
    </div>
  </div>
</main>

<?php
require __DIR__ . '/includes/footer.php';
require __DIR__ . '/includes/page-end.php';
?>
