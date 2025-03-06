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




//modales abrir y cerrar y eliminar pasos 
document.addEventListener("DOMContentLoaded", function () {
    // Función para abrir un modal por su ID
    function openModal(modalId) {
        let modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = "block";
        }
    }

    // Función para cerrar un modal por su ID
    function closeModal(modalId) {
        let modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = "none";
        }
    }

    // Función para abrir los modales de confirmación de eliminación
    function openDeleteModal(modalId) {
        openModal(modalId);
    }

    // Función para cerrar los modales de confirmación de eliminación
    function closeDeleteModal(modalId) {
        closeModal(modalId);
    }

    // Agregar eventos a los botones de eliminar receta
    document.querySelectorAll(".btn-delete").forEach(button => {
        button.addEventListener("click", function () {
            let modalId = this.getAttribute("onclick").match(/'([^']+)'/)[1];
            openDeleteModal(modalId);
        });
    });

    // Agregar eventos a los botones de cerrar modales
    document.querySelectorAll(".modal .close").forEach(closeBtn => {
        closeBtn.addEventListener("click", function () {
            let modal = this.closest(".modal");
            if (modal) {
                closeModal(modal.id);
            }
        });
    });

    // Cerrar el modal si se hace clic fuera de él
    window.addEventListener("click", function (event) {
        document.querySelectorAll(".modal").forEach(modal => {
            if (event.target === modal) {
                closeModal(modal.id);
            }
        });
    });
});
