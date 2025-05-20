import "@/assets/css/ACM/icon-css.css";
import L from "leaflet";
import "leaflet-draw";
import "leaflet.markercluster";
import { useEventBridgeStore } from '@/Stores/eventBridge';

function triggerClearMapCallback() {
  const bridgeStore = useEventBridgeStore();
  bridgeStore.triggerClearMapCallback();
}
export function useLeafletMapmarket() {
    let map = null;
    let marker = null;
    let circle = null;
    let polygon = null;
    let drawnItems = null;
    let markerClusterGroup = null;
    let markersLayer = null;

    const initializeMap = (
        mapElement,
        { center, zoom },
        onShapeChange = null
    ) => {
        if (!mapElement) throw new Error("El elemento del mapa no es válido.");
        map = L.map(mapElement).setView(center, zoom);

        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            attribution: "&copy; OpenStreetMap contributors",
        }).addTo(map);

        markerClusterGroup = L.markerClusterGroup().addTo(map);

        drawnItems = new L.FeatureGroup();
        map.addLayer(drawnItems);

        const drawControl = new L.Control.Draw({
            draw: {
                polygon: true,
                circle: true,
                rectangle: false,
                polyline: false,
                marker: false,
            },
            edit: {
                featureGroup: drawnItems,
            },
        });
        map.addControl(drawControl);
        map.on(L.Draw.Event.CREATED, (event) => {
            const { layerType, layer } = event;
            const shapeData = {};
            if (layerType === "circle") {
                const data = {
                    type: "circle",
                    center: layer.getLatLng(),
                    radius: layer.getRadius(),
                };
                console.log("Círculo creado:", data);
                onShapeChange(data);
            } else if (layerType === "polygon") {
                const data = {
                    type: "polygon",
                    latlngs: layer.getLatLngs(),
                };
                console.log("Polígono creado:", data);
                onShapeChange(data);
            }
            drawnItems.addLayer(layer); // Agrega la capa al grupo
        });

        map.on(L.Draw.Event.EDITED, (event) => {
            const layers = event.layers;
            layers.eachLayer((layer) => {
                if (layer instanceof L.Circle) {
                    const data = {
                        type: "circle",
                        center: layer.getLatLng(),
                        radius: layer.getRadius(),
                    };
                    console.log("Círculo editado:", data);
                    onShapeChange(data);
                } else if (layer instanceof L.Polygon) {
                    const data = {
                        type: "polygon",
                        latlngs: layer.getLatLngs(),
                    };
                    console.log("Polígono editado:", data);
                    onShapeChange(data);
                }
            });
        });
        map.on(L.Draw.Event.DELETED, (event) => {
            const layers = event.layers;
            layers.eachLayer((layer) => {
                console.log("Capa eliminada:", layer);
            });
            clearMarkers();
            console.log("Todos los marcadores han sido eliminados.");
            //if (onDelete) onDelete();
            //emit("clear-map-listings");
            //triggerClearMapCallback();

        });
    };

    const addMarker = (
        lat,
        lng,
        popupContent = "",
        defaultImage = null,
        price = "",
        id = ""
    ) => {
        if (!map) throw new Error("El mapa no ha sido inicializado.");

        const customIcon = L.divIcon({
            className: "custom-marker",
            html: `
      <div class="marker-container">
        <img src="${defaultImage}" alt="icon" class="marker-image" />
        <div class="price-label">${price}</div>
      </div>
    `,
            iconSize: [40, 60], // Ajusta el tamaño general
            iconAnchor: [20, 60], // Ajusta el punto de anclaje
        });

        const marker = L.marker([lat, lng], {
            icon: customIcon, // Usa el icono personalizado si se proporciona
            draggable: false, // Los puntos no se pueden mover
        }).on("click", markerOnClick);

        function markerOnClick(e) {
            //document.getElementById(id).scrollIntoView();
             // 🔹 Scroll a la tarjeta
             const card = document.getElementById(id);

             if (card) {
               card.scrollIntoView();

               // 🔹 Quitar resaltado de todos
               document.querySelectorAll('tr').forEach(el => {
                 el.classList.remove('highlighted-row');
               });

               // 🔹 Agregar resaltado solo al seleccionado
               card.classList.add('highlighted-row');
             }

        }
        if (popupContent) {
            marker.bindPopup(popupContent);
        }

        markerClusterGroup.addLayer(marker);

        console.log(
            `Marcador agregado en [${lat}, ${lng}] con precio: ${price}`
        );
    };

    const moveMarker = (lat, lng) => {
        if (!marker) throw new Error("El marcador no existe.");
        marker.setLatLng([lat, lng]);
        map.setView([lat, lng]);
    };

    const onMarkerEvent = (event, callback) => {
        if (!marker) throw new Error("El marcador no existe.");
        marker.on(event, callback);
    };

    const drawCircle = (lat, lng, radius = 200) => {
        if (!map) throw new Error("El mapa no ha sido inicializado.");

        // Si ya existe un círculo, lo eliminamos antes de agregar uno nuevo
        if (circle) {
            map.removeLayer(circle);
        }

        // Crear el círculo con opción de edición
        circle = L.circle([lat, lng], {
            radius,
            color: "blue",
            editable: true, // Permite edición
            draggable: true, // Permite mover
        }).addTo(map);

        circle.on("drag", function (event) {
            const newLatLng = event.target.getLatLng();
            console.log(`Círculo movido a: ${newLatLng.lat}, ${newLatLng.lng}`);
        });

        circle.on("resize", function (event) {
            const newRadius = event.target.getRadius();
            console.log(`Nuevo radio del círculo: ${newRadius}`);
        });
    };

    const drawPolygon = (
        latlngs = [
            [-17.7833, -63.1822],
            [-17.5, -63.2],
            [-17.7, -63.3],
        ]
    ) => {
        if (!map) throw new Error("El mapa no ha sido inicializado.");
        if (polygon) polygon.remove();
        polygon = L.polygon(latlngs, { color: "red" }).addTo(map);
    };
    // Nueva función para limpiar solo los marcadores
    const clearMarkers = () => {
        if (markerClusterGroup) {
            markerClusterGroup.clearLayers();
            console.log("🧹 Marcadores del clúster eliminados del mapa.");
            triggerClearMapCallback();

        }
    };

    const clearAllListings = () => {
        if (markerClusterGroup) {
            markerClusterGroup.clearLayers();
            console.log("🧹 Marcadores del clúster eliminados del mapa.");
            //triggerClearMapCallback();
        };

    };

    const destroyMap = () => {
        if (map) {
            map.remove();
            map = null;
        }
        if (marker) {
            marker.remove();
            marker = null;
        }
        if (circle) circle.remove();
        if (polygon) polygon.remove();
        if (drawnItems) drawnItems.clearLayers();

        if (markerClusterGroup) {
            markerClusterGroup.clearLayers();
            console.log("🧹 Marcadores del clúster eliminados del mapa.");
            //triggerClearMapCallback();

        };

        if (map) {
            map.off(); // Quitar eventos
            map.remove();
            map = null;
          }

    };

    return {
        initializeMap,
        addMarker,
        moveMarker,
        drawCircle,
        drawPolygon,
        onMarkerEvent,
        destroyMap,
        clearMarkers,
        clearAllListings,
        triggerClearMapCallback,
        getMap: () => map
    };
}
