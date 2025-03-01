// modal de la imagen de los pasos de la receta
document.addEventListener('DOMContentLoaded', function () {
    // Obtener todos los enlaces con la clase 'image-link'
    const imageLinks = document.querySelectorAll('.image-link');

    // Para cada enlace, agregar un evento de clic
    imageLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault(); // Prevenir la acción por defecto del enlace

            // Obtener la URL de la imagen desde el atributo 'data-image'
            let imageUrl = link.getAttribute('data-image');

            // Ajustar la URL si es necesario
            if (imageUrl.startsWith('/RecetasIMG/')) {
                imageUrl = '/recetariococinasym/public' + imageUrl; // Asegurarse de que la ruta sea completa
            }

            // Verificar la URL en la consola
            console.log('URL de la imagen:', imageUrl);

            // Mostrar la imagen en el modal
            showImageModal(imageUrl);
        });
    });

    // Función para mostrar la imagen en un modal
    function showImageModal(imageUrl) {
        const modal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');

        // Mostrar el modal
        modal.style.display = 'flex';

        // Establecer la fuente de la imagen
        modalImage.src = imageUrl;

        // Agregar el evento para cerrar el modal cuando se haga clic en la 'X'
        const closeModal = document.getElementById('closeModal');
        closeModal.addEventListener('click', function () {
            modal.style.display = 'none'; // Ocultar el modal
        });

        // Cerrar el modal si se hace clic fuera de la imagen
        window.addEventListener('click', function (event) {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
    }
});

// Función para abrir el modal de pasos
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'block';
    }
}

// Función para cerrar el modal de pasos
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
    }
}

// Cerrar el modal al hacer clic fuera del contenido de pasos
window.onclick = function (event) {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
};

document.addEventListener('DOMContentLoaded', () => {
    const track = document.querySelector('.carousel-track');
    const slides = Array.from(track.children);
    const prevButton = document.querySelector('.carousel-button-left');
    const nextButton = document.querySelector('.carousel-button-right');
    const indicators = document.querySelectorAll('.indicator');

    let currentIndex = 0;

    function updateSlidePosition() {
        track.style.transform = `translateX(-${currentIndex * 100}%)`;
        indicators.forEach((indicator, index) => {
            indicator.classList.toggle('active', index === currentIndex);
        });
    }

    nextButton.addEventListener('click', () => {
        currentIndex = (currentIndex + 1) % slides.length; // Ciclo continuo
        updateSlidePosition();
    });

    prevButton.addEventListener('click', () => {
        currentIndex = (currentIndex - 1 + slides.length) % slides.length; // Ciclo continuo
        updateSlidePosition();
    });

    indicators.forEach((indicator, index) => {
        indicator.addEventListener('click', () => {
            currentIndex = index;
            updateSlidePosition();
        });
    });

    // Función para mover automáticamente las diapositivas
    function autoMoveSlide() {
        currentIndex = (currentIndex + 1) % slides.length; // Ciclo continuo
        updateSlidePosition();
    }

    // Configurar el intervalo para que el carrusel se mueva automáticamente cada 3 segundos
    const intervalTime = 3000; // Tiempo en milisegundos (3 segundos)
    setInterval(autoMoveSlide, intervalTime); // Llamada a autoMoveSlide cada 3 segundos
});

// eliminar recetas
// Mostrar el modal correspondiente cuando se hace clic en "Eliminar"
document.querySelectorAll('.btn-delete').forEach(function(btn) {
    btn.onclick = function(event) {
        event.preventDefault(); // Evita que el enlace haga su acción por defecto
        var recetumId = btn.getAttribute('data-id'); // Obtén el ID de la receta
        var modal = document.getElementById('deleteModal-' + recetumId); // Encuentra el modal correspondiente
        modal.style.display = "block";
    };
});

// Cerrar el modal cuando se hace clic en el botón de cerrar (×)
document.querySelectorAll('.close').forEach(function(span) {
    span.onclick = function() {
        var modal = span.closest('.modal'); // Encuentra el modal padre
        modal.style.display = "none";
    };
});

// Cerrar el modal cuando se hace clic en el botón "Cancelar"
document.querySelectorAll('.cancelBtn').forEach(function(cancelBtn) {
    cancelBtn.onclick = function() {
        var modal = cancelBtn.closest('.modal'); // Encuentra el modal padre
        modal.style.display = "none";
    };
});

// Cerrar el modal cuando se hace clic fuera de él
window.onclick = function(event) {
    document.querySelectorAll('.modal').forEach(function(modal) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });
};





//modal para las instrucciones flotantes
document.addEventListener("DOMContentLoaded", function () {
    const instrucciones = document.getElementById("instrucciones");
    const toggleBtn = document.getElementById("toggle-instrucciones");
    const titulo = instrucciones.querySelector("h2");

    toggleBtn.addEventListener("click", function () {
        instrucciones.classList.toggle("minimizado");
        
        if (instrucciones.classList.contains("minimizado")) {
            toggleBtn.textContent = "+";
            titulo.style.display = "block"; // Muestra el título cuando está minimizado
        } else {
            toggleBtn.textContent = "−";
            titulo.style.display = "block"; // Mantiene el título visible
        }
    });
});




//modal para eliminar pasos en los pasos de la receta del molal de la receta de la receta ah
document.addEventListener("DOMContentLoaded", function () {
    let pasoIdEliminar = null; // Guardará el ID del paso a eliminar
    let filaEliminar = null; // Guardará la fila del paso a eliminar

    // Capturar el modal y botones
    const confirmacionModal = document.getElementById("deleteModal");
    const confirmDeleteBtn = confirmacionModal.querySelector(".confirmDelete");
    const cancelDeleteBtn = confirmacionModal.querySelector(".cancelBtn");

    // Cuando se hace clic en un botón "Eliminar"
    document.querySelectorAll(".btn-eliminar").forEach(button => {
        button.addEventListener("click", function () {
            pasoIdEliminar = this.getAttribute("data-id");
            let url = this.getAttribute("data-url");
            let token = this.getAttribute("data-token");

            filaEliminar = this.closest("tr"); // Captura la fila para eliminarla después

            console.log("ID del paso:", pasoIdEliminar);
            console.log("URL de eliminación:", url);
            console.log("Token CSRF:", token);

            // Guardar la URL y el token en el botón de confirmación
            confirmDeleteBtn.setAttribute("data-url", url);
            confirmDeleteBtn.setAttribute("data-token", token);

            // Mostrar el modal de confirmación
            confirmacionModal.style.display = "block";
        });
    });

    // Cuando el usuario confirma la eliminación
    confirmDeleteBtn.addEventListener("click", function () {
        let url = this.getAttribute("data-url");
        let token = this.getAttribute("data-token");

        fetch(url, {
            method: "POST",
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ _token: token })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error("Error en la respuesta del servidor: " + response.status);
            }
            return response.json(); // Convertir la respuesta a JSON
        })
        .then(data => {
            if (data.success) {
                if (filaEliminar) {
                    filaEliminar.remove(); // ✅ Eliminar la fila del DOM
                }
                confirmacionModal.style.display = "none"; // ✅ Cerrar modal

                // Mostrar solo un mensaje de éxito
                alert("Paso eliminado con éxito");
            } else {
                throw new Error(data.error || "No se pudo eliminar el paso.");
            }
        })
        .catch(error => {
            console.error("Error en fetch:", error);
            alert("No se pudo eliminar el paso: " + error.message);
        });
    });

    // Cuando el usuario cancela la eliminación
    cancelDeleteBtn.addEventListener("click", function () {
        confirmacionModal.style.display = "none";
    });

    // Cerrar modal si se hace clic fuera del contenido
    window.addEventListener("click", function (event) {
        if (event.target === confirmacionModal) {
            confirmacionModal.style.display = "none";
        }
    });
});



// modal eliminar pasos en los pasos
document.addEventListener("DOMContentLoaded", function () {
    console.log("Script cargado correctamente.");

    let pasoIdAEliminar = null;
    let deleteUrl = null;
    let csrfToken = null;

    // Manejo de apertura del modal de eliminación
    document.querySelectorAll(".btn-eliminar").forEach((btn) => {
        btn.addEventListener("click", function () {
            pasoIdAEliminar = this.getAttribute("data-id");
            deleteUrl = this.getAttribute("data-url");
            csrfToken = this.getAttribute("data-token");

            console.log(`Intentando eliminar el paso ID: ${pasoIdAEliminar}`);

            let modal = document.getElementById("deleteModal");
            if (modal) {
                modal.style.display = "block";
            } else {
                console.error(`Modal no encontrado para el paso ID: ${pasoIdAEliminar}`);
            }
        });
    });

    // Manejo de cierre del modal de eliminación
    document.querySelector(".cancelBtn").addEventListener("click", function () {
        document.getElementById("deleteModal").style.display = "none";
    });

    document.querySelector(".close").addEventListener("click", function () {
        document.getElementById("deleteModal").style.display = "none";
    });

    // Confirmar eliminación
    document.querySelector(".confirmDelete").addEventListener("click", function () {
        if (!pasoIdAEliminar || !deleteUrl) {
            console.error("No se ha seleccionado ningún paso para eliminar.");
            return;
        }

        console.log(`Enviando petición para eliminar paso ID: ${pasoIdAEliminar}`);

        fetch(deleteUrl, {
            method: "POST",
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: `_token=${csrfToken}`
        }).then(response => {
            if (response.ok) {
                console.log(`Paso ID ${pasoIdAEliminar} eliminado con éxito.`);

                // Cerrar el modal de eliminación
                document.getElementById("deleteModal").style.display = "none";

                // Eliminar la fila de la tabla
                let row = document.getElementById(`row-${pasoIdAEliminar}`);
                if (row) {
                    row.remove();
                } else {
                    console.error(`No se encontró la fila del paso ID: ${pasoIdAEliminar}`);
                }

                // Mostrar el modal de confirmación
                let confirmacionModal = document.getElementById("confirmacionModal");
                confirmacionModal.style.display = "block";

                // Cerrar automáticamente el modal de confirmación después de 2 segundos
                setTimeout(() => {
                    confirmacionModal.style.display = "none";
                }, 2000);
            } else {
                console.error(`Error al eliminar el paso ID: ${pasoIdAEliminar}`);
            }
        }).catch(error => {
            console.error("Error en la petición:", error);
        });
    });

    // Cerrar manualmente el modal de confirmación
    document.getElementById("confirmacionCerrar").addEventListener("click", function () {
        document.getElementById("confirmacionModal").style.display = "none";
    });

    // Cerrar cualquier modal si se hace clic fuera del contenido
    window.addEventListener("click", function (event) {
        let modal = document.getElementById("deleteModal");
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });
});
