import { icon } from "leaflet";
import { ref, onUnmounted, getCurrentInstance } from "vue";

export function useLeafletMapOffice() {
    const map = ref(null);
    const marker = ref(null);
    const markerEvents = ref({});
    const isMapInitialized = ref(false);

    const instance = getCurrentInstance();
    const L = instance?.appContext.config.globalProperties.$L;

    if (!L) {
        console.error(
            "Leaflet no está disponible globalmente. Asegúrate de haber registrado el plugin."
        );
        return;
    }

    const initializeMap = (
        mapElement,
        { center = [-16.2902, -63.5887], zoom = 15 } = {}
    ) => {
        if (!mapElement) {
            console.warn("El elemento del mapa no es válido.");
            return;
        }

        map.value = L.map(mapElement).setView(center, zoom);

        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            attribution: "&copy; OpenStreetMap contributors",
        }).addTo(map.value);

        isMapInitialized.value = true;
    };

    const addMarker = (lat, lng) => {
        if (!map.value) {
            console.warn("El mapa no ha sido inicializado.");
            return;
        }

        if (marker.value) marker.value.remove();

        const customIcon = L.icon({
            iconUrl: "/img/pointer_1.png", // Update this path
            iconSize: [36, 36], // Adjust size as needed
            iconAnchor: [18, 36], // Center the icon
            popupAnchor: [0, -36], // Adjust popup positioning
        });

        marker.value = L.marker([lat, lng], {
            draggable: true,
            icon: customIcon,
        }).addTo(map.value);

        for (const [event, callback] of Object.entries(markerEvents.value)) {
            marker.value.on(event, callback);
        }
    };

    const moveMarker = (lat, lng, zoom = null) => {
        if (!marker.value) {
            console.warn("El marcador no existe.");
            return;
        }
        marker.value.setLatLng([lat, lng]);

        // Si se proporciona un zoom, ajustamos la vista del mapa
        if (zoom !== null && map.value) {
            map.value.setView([lat, lng], zoom);
        } else {
            map.value.setView([lat, lng]); // Mantiene el zoom actual
        }
    };

    const moveCamera = (lat, lng, zoom = 13.5) => {
        if (!map.value) {
            console.warn("El mapa no ha sido inicializado.");
            return;
        }
        map.value.setView([lat, lng], zoom);
    };

    const onMarkerEvent = (event, callback) => {
        if (!marker.value) {
            console.warn("El marcador no existe.");
            return;
        }
        marker.value.on(event, callback);
        markerEvents.value[event] = callback;
    };

    const removeMarkerEvent = (event) => {
        if (!marker.value || !markerEvents.value[event]) return;
        marker.value.off(event, markerEvents.value[event]);
        delete markerEvents.value[event];
    };

    const destroyMap = () => {
        if (map.value) {
            map.value.remove();
            map.value = null;
        }
        if (marker.value) {
            marker.value.remove();
            marker.value = null;
        }
        markerEvents.value = {};
    };

    const updateToCurrentLocation = (watch = false) => {
        if (!navigator.geolocation) {
            console.error(
                "La geolocalización no es compatible con este navegador."
            );
            moveCamera(-16.2902, -63.5887, 16);
            addMarker(-16.2902, -63.5887);
            return;
        }

        const onSuccess = (position) => {
            const userLat = position.coords.latitude;
            const userLng = position.coords.longitude;
            console.log(`Ubicación del usuario: ${userLat}, ${userLng}`);

            moveCamera(userLat, userLng, 16);
            addMarker(userLat, userLng);
        };

        const onError = () => {
            console.error("No se pudo obtener la ubicación del usuario.");
            moveCamera(-16.2902, -63.5887, 16);
            addMarker(-16.2902, -63.5887);
        };

        if (watch) {
            navigator.geolocation.watchPosition(onSuccess, onError);
        } else {
            navigator.geolocation.getCurrentPosition(onSuccess, onError);
        }
    };

    onUnmounted(() => destroyMap());

    return {
        initializeMap,
        addMarker,
        moveMarker,
        moveCamera,
        onMarkerEvent,
        removeMarkerEvent,
        destroyMap,
        updateToCurrentLocation,
        isMapInitialized,
    };
}
