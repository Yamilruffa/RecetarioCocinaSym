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