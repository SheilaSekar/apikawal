const R = 6371;
export function haversineKm(a, b) {
  const rad = d => d * Math.PI / 180;
  const dLat = rad(b.lat - a.lat), dLng = rad(b.lng - a.lng);
  const x = Math.sin(dLat / 2) ** 2 + Math.cos(rad(a.lat)) * Math.cos(rad(b.lat)) * Math.sin(dLng / 2) ** 2;
  return 2 * R * Math.asin(Math.sqrt(x));
}
export function boundingBox(lat, lng, radiusKm) {
  const latDelta = radiusKm / 110.574;
  const lngDelta = radiusKm / (111.320 * Math.max(.01, Math.cos(lat * Math.PI / 180)));
  return { minLat: lat - latDelta, maxLat: lat + latDelta, minLng: lng - lngDelta, maxLng: lng + lngDelta };
}
export const roundCoord = (n, places = 3) => Number(Number(n).toFixed(places));
