export const useListingScroll = () => {
    let scrollContainer = null; // Almacena el contenedor específico para el evento de scroll

    // Método para inicializar el scroll infinito
    const initScrollListener = (container) => {
        scrollContainer = container;

        if (scrollContainer) {
            scrollContainer.addEventListener("scroll", handleScroll);
        } else {
            console.error(
                "No se pudo inicializar el scroll, contenedor no definido."
            );
        }
    };

    // Manejar el scroll infinito
    const handleScroll = async () => {
        if (!scrollContainer) return;

        const { scrollTop, scrollHeight, clientHeight } = scrollContainer;

        // Verifica si estamos cerca del final del contenedor
        if (scrollTop + clientHeight >= scrollHeight - 100) {
            // Tiene mas pagina o si no esta cargando
            if (
                listingQuery.hasNextPage.value &&
                !listingQuery.isFetchingNextPage.value
            ) {
                await listingQuery.fetchNextPage();
            }
        }
    };

    // Método para limpiar el evento de scroll
    const removeScrollListener = () => {
        if (scrollContainer) {
            scrollContainer.removeEventListener("scroll", handleScroll);
            scrollContainer = null;
        }
    };

    return {
        initScrollListener,
        removeScrollListener,
        handleScroll,
    };
};
