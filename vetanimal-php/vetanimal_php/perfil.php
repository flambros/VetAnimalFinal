<?php
require_once __DIR__ . '/includes/auth.php';
require_login_page();
$pageTitle = 'Mi Perfil';
$extraScripts = ['assets/js/perfil.js'];
require __DIR__ . '/includes/page-start.php';
require __DIR__ . '/includes/header.php';
$__user = current_user();
?>
<script>window.CURRENT_USER_ID = <?= (int)$__user['id'] ?>;</script>

<main class="u-flex-1">
  <div class="container section">
    <div class="u-flex u-flex-wrap u-items-center u-justify-between u-gap-4 u-mb-8">
      <div>
        <h1 class="u-text-3xl u-font-bold">Mi Perfil</h1>
        <p style="color:#4b5563;">Gestioná tus datos personales, mascotas y turnos solicitados.</p>
      </div>
      <?php if ($__user['rol'] === 'veterinario'): ?>
        <a href="admin/dashboard.php" class="btn btn-primary">Ir al Panel Veterinario →</a>
      <?php endif; ?>
    </div>

    <div class="u-grid" style="grid-template-columns:1fr 2fr; gap:24px; margin-bottom:40px;">
      <div class="u-card u-shadow">
        <div class="u-flex u-items-center u-gap-4 u-mb-4">
          <div class="u-avatar u-avatar-64 u-flex u-items-center u-justify-center u-font-bold u-text-2xl" style="background:var(--verde-claro); color:var(--verde-oscuro); border:1px solid var(--borde);">
            <?= htmlspecialchars(mb_strtoupper(mb_substr($__user['nombre'], 0, 1))) ?>
          </div>
          <div>
            <h2 class="u-font-bold u-text-lg"><?= htmlspecialchars($__user['nombre']) ?></h2>
            <span class="u-badge-role"><?= $__user['rol'] === 'veterinario' ? 'Veterinario / Admin' : 'Cliente' ?></span>
          </div>
        </div>
        <div class="u-text-sm" style="color:#374151;">
          <p><strong>Email:</strong> <?= htmlspecialchars($__user['email']) ?></p>
          <p class="u-mt-2"><strong>Teléfono:</strong> <?= htmlspecialchars($__user['telefono'] ?: 'No especificado') ?></p>
        </div>
      </div>

      <div class="u-card u-shadow">
        <div class="u-flex u-justify-between u-items-center u-mb-4">
          <h2 class="u-font-bold u-text-lg">Mis Mascotas (<span id="pets-count">0</span>)</h2>
          <a href="mascota-nueva.php" class="btn btn-primary btn-sm">+ Agregar Mascota</a>
        </div>
        <div id="pets-list" class="u-grid u-grid-2 u-gap-4"></div>
      </div>
    </div>

    <div class="u-card u-shadow">
      <div class="u-flex u-justify-between u-items-center u-mb-6">
        <h2 class="u-font-bold u-text-xl">Mis Turnos Solicitados</h2>
        <a href="booking.php" class="btn btn-primary btn-sm">+ Reservar Nuevo Turno</a>
      </div>
      <div id="turnos-wrap" style="overflow-x:auto;">
        <table class="data-table">
          <thead>
            <tr><th>Fecha y Hora</th><th>Mascota</th><th>Servicio</th><th>Veterinario</th><th>Estado</th><th>Acciones</th></tr>
          </thead>
          <tbody id="turnos-body"></tbody>
        </table>
      </div>
      <div id="turnos-empty" class="u-text-center u-py-10" style="color:#6b7280; display:none;">
        No tenés turnos registrados actualmente.
      </div>
    </div>
  </div>

  <!-- Modal editar mascota -->
  <div id="modal-edit-pet" class="u-modal-overlay" style="display:none;">
    <div class="u-modal">
      <button type="button" class="u-modal-close" id="close-edit-modal">✕</button>
      <h2 class="u-text-xl u-font-bold u-mb-1">✏️ Editar Mascota: <span style="color:var(--verde-oscuro);" id="edit-pet-name-title"></span></h2>
      <p class="u-text-xs u-muted u-mb-4">Modificá los datos personales, avatar o foto de tu mascota.</p>

      <div id="edit-msg" class="u-msg" style="display:none;"></div>

      <form id="edit-pet-form">
        <input type="hidden" id="edit-pet-id">
        <div class="u-grid u-grid-2 u-gap-3">
          <div>
            <label class="u-text-xs u-font-bold" style="display:block; margin-bottom:4px;">Nombre *</label>
            <input type="text" id="edit-nombre" required class="u-w-full u-text-sm" style="padding:8px; border:1px solid #d1d5db; border-radius:8px;">
          </div>
          <div>
            <label class="u-text-xs u-font-bold" style="display:block; margin-bottom:4px;">Especie *</label>
            <select id="edit-especie" class="u-w-full u-text-sm" style="padding:8px; border:1px solid #d1d5db; border-radius:8px;">
              <option value="Perro">Perro</option>
              <option value="Gato">Gato</option>
              <option value="Ave">Ave</option>
              <option value="Exótico">Exótico / Otro</option>
            </select>
          </div>
          <div>
            <label class="u-text-xs u-font-bold" style="display:block; margin-bottom:4px;">Raza</label>
            <input type="text" id="edit-raza" class="u-w-full u-text-sm" style="padding:8px; border:1px solid #d1d5db; border-radius:8px;">
          </div>
          <div class="u-grid u-grid-2 u-gap-2">
            <div>
              <label class="u-text-xs u-font-bold" style="display:block; margin-bottom:4px;">Edad (años)</label>
              <input type="number" id="edit-edad" min="0" class="u-w-full u-text-sm" style="padding:8px; border:1px solid #d1d5db; border-radius:8px;">
            </div>
            <div>
              <label class="u-text-xs u-font-bold" style="display:block; margin-bottom:4px;">Peso (kg)</label>
              <input type="number" id="edit-peso" step="0.1" min="0" class="u-w-full u-text-sm" style="padding:8px; border:1px solid #d1d5db; border-radius:8px;">
            </div>
          </div>
        </div>

        <div class="u-mt-3 u-border-t" style="padding-top:12px;">
          <label class="u-text-xs u-font-bold" style="display:block; margin-bottom:4px;">Foto de la Mascota</label>
          <p class="u-text-xs u-muted u-mb-2">Elegí un avatar predeterminado, subí un archivo o pegá la URL directa de la foto:</p>
          <div class="u-photo-presets" id="edit-photo-presets"></div>
          <div class="u-grid u-grid-2 u-gap-3 u-mb-3">
            <div>
              <label class="u-text-xs u-font-semibold" style="display:block; margin-bottom:4px;">Subir desde equipo:</label>
              <input type="file" id="edit-photo-file" accept="image/*">
            </div>
            <div>
              <label class="u-text-xs u-font-semibold" style="display:block; margin-bottom:4px;">O pegar URL de imagen:</label>
              <input type="url" id="edit-photo-url" placeholder="https://..." class="u-w-full u-text-xs" style="padding:8px; border:1px solid #d1d5db; border-radius:8px;">
            </div>
          </div>
          <div class="u-photo-preview">
            <img id="edit-photo-preview-img" class="u-avatar u-avatar-48" src="" alt="Vista previa">
            <div>
              <span class="u-text-xs u-font-bold" style="display:block;">Vista previa del avatar</span>
              <span class="u-text-xs u-muted">Así se mostrará en los turnos y el perfil.</span>
            </div>
          </div>
        </div>

        <div class="u-grid u-grid-2 u-gap-3 u-mt-3 u-border-t" style="padding-top:12px;">
          <div>
            <label class="u-text-xs u-font-bold" style="display:block; margin-bottom:4px;">Alergias Conocidas</label>
            <input type="text" id="edit-alergias" placeholder="Ej: Polen, Algún fármaco..." class="u-w-full u-text-xs" style="padding:8px; border:1px solid #d1d5db; border-radius:8px;">
          </div>
          <div>
            <label class="u-text-xs u-font-bold" style="display:block; margin-bottom:4px;">Condiciones Crónicas</label>
            <input type="text" id="edit-condiciones" placeholder="Ej: Displasia leve..." class="u-w-full u-text-xs" style="padding:8px; border:1px solid #d1d5db; border-radius:8px;">
          </div>
        </div>

        <div class="u-flex u-justify-end u-gap-3 u-mt-4 u-border-t" style="padding-top:16px;">
          <button type="button" id="cancel-edit-pet" class="btn btn-light btn-sm">Cancelar</button>
          <button type="submit" id="save-edit-pet" class="btn btn-primary btn-sm">Guardar Cambios ✓</button>
        </div>
      </form>
    </div>
  </div>
</main>

<?php
require __DIR__ . '/includes/footer.php';
require __DIR__ . '/includes/page-end.php';
?>
