//import { apiService } from "@/src/services/marketanalisis";
import { defineStore } from "pinia";

export const useMarketAnalisisStore = defineStore("marketAnalisis", {
  state: () => ({
    listings: [],
    options: {},
    loading: false,
    error: null,
  	locationResults: [],
	selectedProperties: [], // Propiedades seleccionadas
    summary: {}, // Resumen de promedios
		caseType: null,
  }),

  actions: {

		setSummaryData(summary) {
      this.summary = summary; // Actualiza el resumen
    },
    setSelectedProperties(properties) {
      this.selectedProperties = properties; // Actualiza las propiedades seleccionadas
    },

    async fetchListings(filters) {
      this.loading = true;
      this.error = null;
      try {
        const response = await apiService.filterListings(filters);
        this.listings = response.data;
      } catch (error) {
        this.error = "No se pudo cargar los listados.";
      } finally {
        this.loading = false;
      }
    },



    async fetchOptions() {
      this.loading = true;
      this.error = null;
      try {
        const response = await apiService.getMarketAnalysis();
        this.options = response.data.options;
        console.log("Opciones cargadas:", this.options);
      } catch (error) {
        this.error = "No se pudieron cargar las opciones.";
      } finally {
        this.loading = false;
      }
    },

	  async fetchLocationsByCoordinates(payload) {
		this.loading = true;
		this.error = null;
		try {
		  const response = await apiService.searchByCoordinates(payload); // Envía directamente el payload
		  this.locationResults = response;
		  console.log("Resultados de la búsqueda por coordenadas:", this.locationResults);
		} catch (error) {
		  this.error = "No se pudieron cargar las localizaciones.";
		} finally {
		  this.loading = false;
		}
	  },
		 // Acción para actualizar propiedades seleccionadas y promedios
		updateSelectionData({ selectedProperties, summary }) {

			console.log("📝 Actualizando en el store:");
			console.log("  - selectedProperties:", selectedProperties);
			console.log("  - summary:", summary);
      this.selectedProperties = selectedProperties;
      this.summary = summary;
    },

		async handlePrint(caseType) {
			const payload = {
				selectedProperties: this.selectedProperties,
				summary: this.summary,
				totalListings: this.listings.length,
			};

			try {
				let response;

				console.log("Respuesta del backend:", response);

				// Asegurarte de que el backend está enviando correctamente el PDF como un blob
				if (response && response.data) {
					// Suponiendo que la respuesta contiene el blob del PDF
					return response.data; // Devuelve el blob al frontend
				}

				console.error("No se recibió un PDF");
				return null;
			} catch (error) {
				this.error = "Error al enviar los datos al backend.";
				console.error(error);
				return null;
			}
		}


  },

	},

);
