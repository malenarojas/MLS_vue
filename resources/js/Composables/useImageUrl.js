
export function useImageUrl(baseApiUrl = "http://127.0.0.1:8000") {
  const getFullImageUrl = (relativeUrl) => {
    if (!relativeUrl) return null;
    return `${baseApiUrl}${relativeUrl}`;
  };

  return {
    getFullImageUrl,
  };
}