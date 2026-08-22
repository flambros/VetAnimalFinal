<?php
require_once __DIR__ . '/includes/auth.php';
require_login_page();
$pageTitle = 'Agregar Mascota';
$extraScripts = ['assets/js/mascota-nueva.js'];
require __DIR__ . '/includes/page-start.php';
require __DIR__ . '/includes/header.php';
$__user = current_user();
?>
<script>window.CURRENT_USER_ID = <?= (int)$__user['id'] ?>;</script>

<main class="u-flex-1">
  <div class="container section u-max-w-lg">
    <a href="booking.php" class="back-link u-mb-6">← Volver</a>

    <div class="panel-card">
      <h1 class="u-text-2xl u-font-bold u-mb-2">Agregar Nueva Mascota</h1>
      <p class="u-muted u-mb-6 u-text-sm">
        Registrá los datos de tu compañero para poder solicitar turnos y llevar su historial clínico.
      </p>

      <div id="form-error" class="alert alert-error u-mb-4" style="display:none;"></div>

      <form id="pet-form">
        <div class="field">
          <label>Nombre de la mascota *</label>
          <div class="input-wrap">
            <span>🐾</span>
            <input type="text" id="nombre" placeholder="Ej: Max, Luna..." required>
          </div>
        </div>

        <div class="field">
          <label>Especie *</label>
          <div class="input-wrap">
            <select id="especie">
              <option value="Perro">Perro</option>
              <option value="Gato">Gato</option>
              <option value="Ave">Ave</option>
              <option value="Exótico">Exótico / Otro</option>
            </select>
          </div>
        </div>

        <div class="field">
          <label>Raza</label>
          <div class="input-wrap">
            <input type="text" id="raza" placeholder="Ej: Golden Retriever, Siamés...">
          </div>
        </div>

        <div class="u-grid u-grid-2 u-gap-4">
          <div class="field">
            <label>Edad (años)</label>
            <div class="input-wrap">
              <input type="number" id="edad" placeholder="Ej: 3" min="0">
            </div>
          </div>
          <div class="field">
            <label>Peso (kg)</label>
            <div class="input-wrap">
              <input type="number" id="peso" step="0.1" placeholder="Ej: 12.5" min="0">
            </div>
          </div>
        </div>

        <div class="field">
          <label>Foto de la mascota</label>
          <p class="u-text-xs u-muted u-mb-2">Elegí un avatar predeterminado, subí una foto de tu dispositivo o ingresá una URL:</p>

          <div class="u-photo-presets" id="photo-presets"></div>

          <div class="u-grid u-grid-2 u-gap-3 u-mb-3">
            <div>
              <label class="u-text-xs u-font-semibold" style="display:block; margin-bottom:4px;">Subir imagen:</label>
              <input type="file" id="photo-file" accept="image/*">
            </div>
            <div>
              <label class="u-text-xs u-font-semibold" style="display:block; margin-bottom:4px;">O pegar URL de imagen:</label>
              <input type="url" id="photo-url" placeholder="https://..." class="u-w-full u-text-xs" style="padding:8px; border:1px solid var(--borde); border-radius:8px;">
            </div>
          </div>

          <div id="photo-preview" class="u-photo-preview" style="display:none;">
            <img id="photo-preview-img" class="u-avatar u-avatar-48" src="" alt="Previsualización">
            <span class="u-text-xs u-font-semibold">✓ Vista previa de foto seleccionada</span>
          </div>
        </div>

        <div class="u-flex u-justify-end u-gap-3 u-mt-6">
          <a href="booking.php" class="btn btn-light">Cancelar</a>
          <button type="submit" id="submit-btn" class="btn btn-primary">Guardar Mascota ✓</button>
        </div>
      </form>
    </div>
  </div>
</main>

<?php
require __DIR__ . '/includes/footer.php';
require __DIR__ . '/includes/page-end.php';
?>
