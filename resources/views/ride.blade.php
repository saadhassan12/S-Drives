<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Ride #{{ $ride->id ?? 'N/A' }}</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
      background: #f7f7f7;
      overflow-x: hidden;
    }

    #map-container {
      height: 65vh;
      width: 100%;
      position: relative;
    }

    .ride-card {
      position: fixed;
      bottom: 0;
      left: 0;
      width: 100%;
      min-height: 35vh;
      background: #fff;
      padding: 20px;
      box-sizing: border-box;
      border-top-left-radius: 20px;
      border-top-right-radius: 20px;
      box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.1);
      z-index: 1000;
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .status-message {
      font-size: 20px;
      font-weight: 600;
      color: #c2185b;
      margin-bottom: 4px;
      text-transform: capitalize;
    }

    .eta-message {
      font-size: 16px;
      color: #444;
      margin-bottom: 10px;
      font-weight: 500;
    }

    .car-details {
      font-size: 15px;
      color: #555;
    }

    .car-plate {
      background: #f2f2f2;
      padding: 8px 12px;
      border-radius: 8px;
      font-weight: bold;
      font-size: 14px;
      color: #333;
    }

    .driver-info {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      border-bottom: 1px solid #eee;
      padding-bottom: 12px;
    }

    .driver-rating-name {
      font-size: 16px;
      font-weight: 500;
      color: #c2185b;
      line-height: 1.5;
    }

    .rating-star {
      color: gold;
      font-size: 1.2em;
    }

    .location {
      display: flex;
      align-items: center;
      font-size: 15px;
      color: #333;
      gap: 8px;
      word-break: break-word;
    }

    .location-icon {
      width: 10px;
      height: 10px;
      border-radius: 50%;
      flex-shrink: 0;
    }

    .pickup-icon {
      background-color: #4caf50;
    }

    .dropoff-icon {
      background-color: #c2185b;
    }

    /* Ride not found message */
    .not-found {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      height: 100vh;
      text-align: center;
      color: #c2185b;
    }

    .not-found h1 {
      font-size: 24px;
      font-weight: 600;
    }

    .not-found p {
      color: #666;
      font-size: 16px;
    }

    @media (max-width: 768px) {
      .ride-card {
        padding: 16px;
        min-height: 40vh;
      }
      .status-message {
        font-size: 18px;
      }
      .eta-message {
        font-size: 15px;
      }
      .driver-rating-name {
        font-size: 15px;
      }
      .car-details {
        font-size: 14px;
      }
    }
  </style>
</head>
<body>

@if(!$ride || in_array(strtolower($ride->status), ['canceled', 'cancelled', 'ride_not_found']))
  <!-- ? Show simple message when canceled or not found -->
  <div class="not-found">
    <h1>Ride Not Found</h1>
    <p>The ride has been canceled or no longer exists.</p>
  </div>
@else

  <div id="map-container">
    <div id="map" style="height: 100%; width: 100%"></div>
  </div>

  <div class="ride-card">
    <div class="status-message" id="ride-status"></div>
    <div class="eta-message" id="eta-text">Calculating ETA...</div>

    <div style="display:flex; justify-content:space-between; align-items:center;">
      <div class="car-details">
{{ $ride->vehicleCategory->name ?? 'Vehicle Category' }} — (Rs {{ number_format($ride->final_fare, 0) }})
      </div>
      <div class="car-plate">{{ $ride->vehicles->registration_number ?? 'N/A' }}</div>
    </div>

    <div class="driver-info">
      <div>
        <span class="driver-rating-name">
          Driver: {{ $ride->driver->first_name ?? 'Driver' }} {{ $ride->driver->last_name ?? '' }}
        </span><br>
        <span class="driver-rating-name">
          Phone: {{ $ride->driver->mobile_number ?? 'N/A' }}
        </span><br>
      </div>
    </div>

    <div class="location">
      <div class="location-icon pickup-icon"></div>
      <span>{{ $ride->start }}</span>
    </div>

    <div class="location">
      <div class="location-icon dropoff-icon"></div>
      <span>{{ $ride->destination }}</span>
    </div>
  </div>

  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script>
    const startLat = {{ $ride->start_latitude }};
    const startLng = {{ $ride->start_longitude }};
    const endLat = {{ $ride->end_latitude }};
    const endLng = {{ $ride->end_longitude }};
    const driverId = {{ $ride->driver_id }};
    const rideStatus = "{{ strtolower($ride->status) }}";
    const passengerName = "{{ $ride->user->first_name ?? 'Passenger' }}";

    const map = L.map("map").setView([startLat, startLng], 13);
    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", { maxZoom: 19 }).addTo(map);

    L.marker([startLat, startLng]).addTo(map).bindPopup("Pickup");
    L.marker([endLat, endLng]).addTo(map).bindPopup("Dropoff");

    let iconUrl = "/images/3097144.png";
    const vehicle = "{{ strtolower($ride->vehicleCategory->name ?? '') }}";
    if (vehicle.includes("bike")) iconUrl = "/images/bike.png";
    else if (vehicle.includes("rickshaw")) iconUrl = "/images/rickshaw.png";

    let driverMarker = L.marker([startLat, startLng], {
      icon: L.icon({ iconUrl, iconSize: [40, 40], iconAnchor: [20, 20] }),
    }).addTo(map).bindPopup("Driver's Current Location");

    let currentPolyline = null;

    // ? All possible ride statuses with clear human messages
    const statusMessages = {
      requested: `${passengerName} has requested a ride.`,
      accepted: `Your ride has been accepted by the driver.`,
      driver_reach: `Driver has reached your pickup point.`,
      started_ride: `Ride has started — enjoy your trip!`,
      ride_pick: `Ride is in progress.`,
      completed: `${passengerName} has completed the ride successfully.`,
      canceled: `Ride has been canceled.`,
      cancelled: `Ride has been canceled.`,
      ride_not_found: `Ride not found.`,
      default: `Ride status: ${rideStatus}`,
    };

    document.getElementById("ride-status").textContent =
      statusMessages[rideStatus] || statusMessages.default;

    // --- Live tracking
    async function drawRoute(fromLat, fromLng, toLat, toLng) {
      try {
        const res = await fetch(`https://router.project-osrm.org/route/v1/driving/${fromLng},${fromLat};${toLng},${toLat}?overview=full&geometries=geojson`);
        const data = await res.json();
        if (data.routes && data.routes[0]) {
          const coords = data.routes[0].geometry.coordinates.map(coord => [coord[1], coord[0]]);
          if (currentPolyline) map.removeLayer(currentPolyline);
          currentPolyline = L.polyline(coords, { color: "#2196F3", weight: 5 }).addTo(map);
          map.fitBounds(currentPolyline.getBounds(), { padding: [50, 50] });
        }
      } catch (error) {
        console.error("Route fetch failed:", error);
      }
    }

    function moveMarker(marker, newLat, newLng) {
      const oldLatLng = marker.getLatLng();
      const steps = 20;
      const deltaLat = (newLat - oldLatLng.lat) / steps;
      const deltaLng = (newLng - oldLatLng.lng) / steps;
      let step = 0;
      const interval = setInterval(() => {
        step++;
        const lat = oldLatLng.lat + deltaLat * step;
        const lng = oldLatLng.lng + deltaLng * step;
        marker.setLatLng([lat, lng]);
        if (step === steps) clearInterval(interval);
      }, 50);
    }

    function getDistanceKm(lat1, lon1, lat2, lon2) {
      const R = 6371;
      const dLat = (lat2 - lat1) * Math.PI / 180;
      const dLon = (lon2 - lon1) * Math.PI / 180;
      const a = Math.sin(dLat / 2) ** 2 +
        Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * Math.sin(dLon / 2) ** 2;
      const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
      return R * c;
    }

    function updateETA(lat, lng) {
      const targetLat = rideStatus === "ride_pick" || rideStatus === "started_ride" ? endLat : startLat;
      const targetLng = rideStatus === "ride_pick" || rideStatus === "started_ride" ? endLng : startLng;
      const distanceKm = getDistanceKm(lat, lng, targetLat, targetLng);
      const avgSpeed = 30;
      const timeMinutes = Math.round((distanceKm / avgSpeed) * 60);
      document.getElementById("eta-text").textContent =
        (rideStatus === "ride_pick" || rideStatus === "started_ride")
          ? `Destination in ~${timeMinutes} min (${distanceKm.toFixed(2)} km left)`
          : `Driver arriving in ~${timeMinutes} min (${distanceKm.toFixed(2)} km away)`;
    }

    async function updateDriverLocation() {
      try {
        const res = await fetch(`/driver-location/${driverId}`);
        const data = await res.json();
        if (data.latitude && data.longitude) {
          const driverLat = data.latitude;
          const driverLng = data.longitude;
          moveMarker(driverMarker, driverLat, driverLng);
          map.panTo([driverLat, driverLng]);
          updateETA(driverLat, driverLng);
          const targetLat = (rideStatus === "ride_pick" || rideStatus === "started_ride") ? endLat : startLat;
          const targetLng = (rideStatus === "ride_pick" || rideStatus === "started_ride") ? endLng : startLng;
          drawRoute(driverLat, driverLng, targetLat, targetLng);
        }
      } catch (err) {
        console.error("Error fetching driver location:", err);
      }
    }

    updateDriverLocation();
    setInterval(updateDriverLocation, 5000);
  </script>
@endif

</body>
</html>
