document.addEventListener("DOMContentLoaded", () => {
  const DEFAULT_PET_IMG = "https://images.unsplash.com/photo-1552053831-71594a27632d?w=200&q=80";
  const photoPresets = [
    { label: "Perro 1", url: "https://images.unsplash.com/photo-1543466835-00a7907e9de1?w=500&q=80" },
    { label: "Perro 2", url: "https://images.unsplash.com/photo-1583511655857-d19b40a7a54e?w=500&q=80" },
    { label: "Perro 3", url: "https://images.unsplash.com/photo-1537151608828-ea2b11777ee8?w=500&q=80" },
    { label: "Gato 1", url: "https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?w=500&q=80" },
    { label: "Gato 2", url: "https://images.unsplash.com/photo-1573865526739-10659fec78a5?w=500&q=80" },
    { label: "Ave", url: "https://images.unsplash.com/photo-1552728089-57bdde30beb3?w=500&q=80" },
    { label: "Exótico", url: "https://images.unsplash.com/photo-1425082661705-1834bfd09dca?w=500&q=80" },
  ];

  let pets = [];
  let turnos = [];
  let editPresetUrl = "";
  let editCustomUrl = "";
  let editUploadedUrl = "";

  function editCurrentPhoto() {
    return editUploadedUrl || editCustomUrl || editPresetUrl;
  }

  async function loadAll() {
    const [petsRes, turnosRes] = await Promise.all([
      fetch(`api/pets.php?userId=${window.CURRENT_USER_ID}`),
      fetch(`api/turnos.php?userId=${window.CURRENT_USER_ID}`),
    ]);
    pets = await petsRes.json();
    turnos = await turnosRes.json();
    renderPets();
    renderTurnos();
  }

  function renderPets() {
    document.getElementById("pets-count").textContent = pets.length;
    const box = document.getElementById("pets-list");
    if (pets.length === 0) {
      box.innerHTML = `<p class="u-text-sm u-muted" style="grid-column:1/-1;">Aún no tenés mascotas registradas.</p>`;
      return;
    }
    box.innerHTML = pets.map((p) => `
      <div class="u-card-sm" style="background:#fafaf8; display:flex; flex-direction:column; justify-content:space-between;">
        <div class="u-flex u-items-start u-gap-3">
          <img src="${p.foto || DEFAULT_PET_IMG}" alt="${p.nombre}" class="u-avatar" style="width:64px;height:64px; border:2px solid #fff; box-shadow:var(--sombra);">
          <div class="u-flex-1 u-min-w-0">
            <div class="u-flex u-justify-between">
              <h3 class="u-font-bold u-text-base u-truncate">${p.nombre}</h3>
              <span class="u-pill-soft">${p.especie}</span>
            </div>
            <p class="u-text-xs u-muted u-truncate" style="margin-top:2px;">${p.raza || "Sin raza especificada"}</p>
            <div class="u-flex u-items-center u-gap-2 u-text-xs" style="margin-top:4px; color:#4b5563;">
              <span>${p.edad !== undefined && p.edad !== null ? p.edad + " años" : "Edad no especif."}</span>
              <span>•</span>
              <span>${p.peso !== undefined && p.peso !== null ? p.peso + " kg" : "Peso no especif."}</span>
            </div>
          </div>
        </div>
        <div class="u-flex u-items-center u-justify-between u-gap-2 u-mt-4 u-border-t" style="padding-top:12px;">
          <a href="historial.php" class="u-text-xs u-font-semibold" style="color:var(--verde);">📋 Historial</a>
          <div class="u-flex u-items-center u-gap-1">
            <button type="button" class="btn btn-outline btn-sm u-text-xs" data-edit="${p.id}" style="padding:4px 10px;">✏️ Editar</button>
            <button type="button" class="btn btn-danger btn-sm u-text-xs" data-delete="${p.id}" data-name="${p.nombre}" style="padding:4px 8px;">✕</button>
          </div>
        </div>
      </div>
    `).join("");

    box.querySelectorAll("[data-edit]").forEach((btn) => {
      btn.addEventListener("click", () => openEditPet(Number(btn.dataset.edit)));
    });
    box.querySelectorAll("[data-delete]").forEach((btn) => {
      btn.addEventListener("click", () => deletePet(Number(btn.dataset.delete), btn.dataset.name));
    });
  }

  function renderTurnos() {
    if (turnos.length === 0) {
      document.getElementById("turnos-wrap").style.display = "none";
      document.getElementById("turnos-empty").style.display = "block";
      return;
    }
    document.getElementById("turnos-wrap").style.display = "block";
    document.getElementById("turnos-empty").style.display = "none";

    document.getElementById("turnos-body").innerHTML = turnos.map((t) => `
      <tr>
        <td><strong>${t.fecha}</strong><br><small style="color:#6b7280;">${t.hora} hs</small></td>
        <td>${t.mascota_nombre}</td>
        <td>${t.servicio_nombre}</td>
        <td>${t.veterinario_nombre}</td>
        <td><span class="badge badge-${t.estado}">${t.estado.toUpperCase()}</span></td>
        <td>
          ${t.estado !== "cancelado" && t.estado !== "completado"
            ? `<button type="button" class="btn btn-danger btn-sm" data-cancel="${t.id}">Cancelar</button>`
            : ""}
        </td>
      </tr>
    `).join("");

    document.querySelectorAll("[data-cancel]").forEach((btn) => {
      btn.addEventListener("click", () => cancelTurno(Number(btn.dataset.cancel)));
    });
  }

  async function cancelTurno(id) {
    if (!confirm("¿Estás seguro de que deseas cancelar este turno?")) return;
    const res = await fetch(`api/turnos.php?id=${id}&action=estado`, {
      method: "PATCH",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ estado: "cancelado" }),
    });
    if (res.ok) {
      turnos = turnos.map((t) => (t.id === id ? { ...t, estado: "cancelado" } : t));
      renderTurnos();
    }
  }

  async function deletePet(id, nombre) {
    if (!confirm(`¿Estás seguro de que deseas eliminar a ${nombre}?`)) return;
    const res = await fetch(`api/pets.php?id=${id}`, { method: "DELETE" });
    if (res.ok) {
      pets = pets.filter((p) => p.id !== id);
      renderPets();
    }
  }

  // ---------- Edit modal ----------
  function renderEditPresets() {
    const box = document.getElementById("edit-photo-presets");
    const current = editCustomUrl || editPresetUrl;
    box.innerHTML = photoPresets.map((p) => `
      <button type="button" class="u-photo-chip ${current === p.url ? "selected" : ""}" data-url="${p.url}">
        <img src="${p.url}" alt="${p.label}"><span>${p.label}</span>
      </button>
    `).join("");
    box.querySelectorAll("[data-url]").forEach((btn) => {
      btn.addEventListener("click", () => {
        editPresetUrl = btn.dataset.url;
        editCustomUrl = "";
        editUploadedUrl = "";
        document.getElementById("edit-photo-url").value = "";
        renderEditPresets();
        updateEditPreview();
      });
    });
  }

  function updateEditPreview() {
    document.getElementById("edit-photo-preview-img").src = editCurrentPhoto() || DEFAULT_PET_IMG;
  }

  function openEditPet(id) {
    const p = pets.find((x) => x.id === id);
    if (!p) return;

    document.getElementById("edit-pet-id").value = p.id;
    document.getElementById("edit-pet-name-title").textContent = p.nombre;
    document.getElementById("edit-nombre").value = p.nombre;
    document.getElementById("edit-especie").value = p.especie;
    document.getElementById("edit-raza").value = p.raza || "";
    document.getElementById("edit-edad").value = p.edad ?? "";
    document.getElementById("edit-peso").value = p.peso ?? "";
    document.getElementById("edit-alergias").value = p.alergias || "";
    document.getElementById("edit-condiciones").value = p.condiciones_cronicas || "";

    editPresetUrl = p.foto || "";
    editCustomUrl = "";
    editUploadedUrl = "";
    document.getElementById("edit-photo-url").value = "";
    document.getElementById("edit-msg").style.display = "none";

    renderEditPresets();
    updateEditPreview();

    document.getElementById("modal-edit-pet").style.display = "flex";
  }

  document.getElementById("close-edit-modal").addEventListener("click", () => {
    document.getElementById("modal-edit-pet").style.display = "none";
  });
  document.getElementById("cancel-edit-pet").addEventListener("click", () => {
    document.getElementById("modal-edit-pet").style.display = "none";
  });

  document.getElementById("edit-photo-url").addEventListener("input", (e) => {
    editCustomUrl = e.target.value.trim();
    editUploadedUrl = "";
    updateEditPreview();
  });

  document.getElementById("edit-photo-file").addEventListener("change", (e) => {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onloadend = () => {
      editUploadedUrl = reader.result;
      editCustomUrl = "";
      document.getElementById("edit-photo-url").value = "";
      updateEditPreview();
    };
    reader.readAsDataURL(file);
  });

  document.getElementById("edit-pet-form").addEventListener("submit", async (e) => {
    e.preventDefault();
    const id = document.getElementById("edit-pet-id").value;
    const nombre = document.getElementById("edit-nombre").value.trim();
    const msgBox = document.getElementById("edit-msg");

    if (!nombre) {
      msgBox.className = "u-msg u-msg-error";
      msgBox.textContent = "El nombre es obligatorio.";
      msgBox.style.display = "block";
      return;
    }

    const btn = document.getElementById("save-edit-pet");
    btn.disabled = true;
    btn.textContent = "Guardando...";

    try {
      const res = await fetch(`api/pets.php?id=${id}`, {
        method: "PUT",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          nombre,
          especie: document.getElementById("edit-especie").value,
          raza: document.getElementById("edit-raza").value,
          edad: document.getElementById("edit-edad").value,
          peso: document.getElementById("edit-peso").value,
          foto: editCurrentPhoto(),
          alergias: document.getElementById("edit-alergias").value,
          condiciones_cronicas: document.getElementById("edit-condiciones").value,
        }),
      });
      const updated = await res.json();
      btn.disabled = false;
      btn.textContent = "Guardar Cambios ✓";

      if (!res.ok) {
        msgBox.className = "u-msg u-msg-error";
        msgBox.textContent = updated.error || "Error al actualizar la mascota.";
        msgBox.style.display = "block";
        return;
      }

      pets = pets.map((p) => (p.id === Number(id) ? updated : p));
      renderPets();

      msgBox.className = "u-msg u-msg-success";
      msgBox.textContent = "¡Mascota actualizada correctamente!";
      msgBox.style.display = "block";

      setTimeout(() => {
        document.getElementById("modal-edit-pet").style.display = "none";
      }, 900);
    } catch (e) {
      btn.disabled = false;
      btn.textContent = "Guardar Cambios ✓";
      msgBox.className = "u-msg u-msg-error";
      msgBox.textContent = "Error de conexión.";
      msgBox.style.display = "block";
    }
  });

  loadAll();
});
