/**
 * Compara dos objetos y devuelve solo los campos que han cambiado, incluyendo objetos anidados.
 * @param {Object} original - Objeto original antes de modificaciones.
 * @param {Object} current - Objeto actual modificado.
 * @returns {Object} - Campos modificados con valores previos y nuevos.
 */
export const getModifiedFields = (original, current) => {
    let changes = {};

    const deepCompare = (orig, curr, path = "") => {
        Object.keys(curr).forEach((key) => {
            const fullPath = path ? `${path}.${key}` : key; // Guarda la ruta completa (para objetos anidados)
            const originalValue = orig?.[key];
            const currentValue = curr[key] ?? "";

            // Si es un objeto y no es null, llamamos recursivamente
            if (
                typeof originalValue === "object" &&
                typeof currentValue === "object" &&
                originalValue !== null &&
                currentValue !== null
            ) {
                deepCompare(originalValue, currentValue, fullPath);
            } else {
                // Comparación directa de valores
                if (
                    JSON.stringify(originalValue) !==
                    JSON.stringify(currentValue)
                ) {
                    changes[fullPath] = {
                        field_name: fullPath.toString(),
                        old_value: originalValue?.toString() ?? "",
                        new_value: currentValue.toString(),
                    };
                }
            }
        });
    };

    deepCompare(original, current);
    return changes;
};
