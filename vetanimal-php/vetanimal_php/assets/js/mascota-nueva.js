document.addEventListener("DOMContentLoaded", () => {
  const photoPresets = [
    { label: "Perro 1", url: "https://images.unsplash.com/photo-1543466835-00a7907e9de1?w=500&q=80" },
    { label: "Perro 2", url: "https://images.unsplash.com/photo-1583511655857-d19b40a7a54e?w=500&q=80" },
    { label: "Perro 3", url: "https://images.unsplash.com/photo-1537151608828-ea2b11777ee8?w=500&q=80" },
    { label: "Gato 1", url: "https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?w=500&q=80" },
    { label: "Gato 2", url: "https://images.unsplash.com/photo-1573865526739-10659fec78a5?w=500&q=80" },
    { label: "Ave", url: "https://images.unsplash.com/photo-1552728089-57bdde30beb3?w=500&q=80" },
    { label: "Exótico", url: "https://images.unsplash.com/photo-1425082661705-1834bfd09dca?w=500&q=80" },
  ];

  let selectedPresetUrl = "";
  let customUrl = "";
  let uploadedDataUrl = "";

  function currentPhoto() {
    return uploadedDataUrl || customUrl || selectedPresetUrl;
  }

  function renderPresets() {
    const box = document.getElementById("photo-presets");
    box.innerHTML = photoPresets.map((p) => `
      <button type="button" class="u-photo-chip ${selectedPresetUrl === p.url ? "selected" : ""}" data-url="${p.url}">
        <img src="${p.url}" alt="${p.label}"><span>${p.label}</span>
      </button>
    `).join("");
    box.querySelectorAll("[data-url]").forEach((btn) => {
      btn.addEventListener("click", () => {
        selectedPresetUrl = btn.dataset.url;
        customUrl = "";
        uploadedDataUrl = "";
        document.getElementById("photo-url").value = "";
        renderPresets();
        updatePreview();
      });
    });
  }

  function updatePreview() {
    const photo = currentPhoto();
    const preview = document.getElementById("photo-preview");
    if (photo) {
      preview.style.display = "flex";
      document.getElementById("photo-preview-img").src = photo;
    } else {
      preview.style.display = "none";
    }
  }

  document.getElementById("photo-url").addEventListener("input", (e) => {
    customUrl = e.target.value.trim();
    uploadedDataUrl = "";
    updatePreview();
  });

  document.getElementById("photo-file").addEventListener("change", (e) => {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onloadend = () => {
      uploadedDataUrl = reader.result;
      customUrl = "";
      document.getElementById("photo-url").value = "";
      updatePreview();
    };
    reader.readAsDataURL(file);
  });

  renderPresets();

  const form = document.getElementById("pet-form");
  const errorBox = document.getElementById("form-error");

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    errorBox.style.display = "none";

    const nombre = document.getElementById("nombre").value.trim();
    if (!nombre) {
      errorBox.textContent = "El nombre de la mascota es obligatorio.";
      errorBox.style.display = "block";
      return;
    }

    const btn = document.getElementById("submit-btn");
    btn.disabled = true;
    btn.textContent = "Guardando...";

    try {
      const res = await fetch("api/pets.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          usuario_id: window.CURRENT_USER_ID,
          nombre,
          especie: document.getElementById("especie").value,
          raza: document.getElementById("raza").value,
          edad: document.getElementById("edad").value,
          peso: document.getElementById("peso").value,
          foto: currentPhoto(),
        }),
      });
      const data = await res.json();
      btn.disabled = false;
      btn.textContent = "Guardar Mascota ✓";

      if (!res.ok) {
        errorBox.textContent = data.error || "Error al guardar mascota";
        errorBox.style.display = "block";
        return;
      }
      window.location.href = "perfil.php";
    } catch (e) {
      btn.disabled = false;
      btn.textContent = "Guardar Mascota ✓";
      errorBox.textContent = "Error de conexión";
      errorBox.style.display = "block";
    }
  });
});
