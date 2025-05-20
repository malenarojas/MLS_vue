import L from "leaflet";

export default {
    install(app) {
        app.config.globalProperties.$L = L;
    },
};
