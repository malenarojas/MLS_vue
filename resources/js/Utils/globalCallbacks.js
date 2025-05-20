// utils/globalCallbacks.js

let callbacks = {
    clearListings: null,
  };
  
  export function setClearListingsCallback(fn) {
    callbacks.clearListings = fn;
  }
  
  export function triggerClearListings() {
    if (callbacks.clearListings) {
      console.log("✅ Ejecutando callback clearListings desde JS");
      callbacks.clearListings();
    } else {
      console.warn("⚠️ No se ha registrado la función clearListings.");
    }
  }
  