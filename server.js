  const path = require("path");
  require("dotenv").config({ path: path.join(__dirname, ".env") });

  const express = require("express");
  const http = require("http");
  const cors = require("cors");
  const { Server } = require("socket.io");
  function resolveLaravelApiBase() {
    const explicit = (process.env.LARAVEL_API_URL || "").trim();
    if (explicit) return explicit.replace(/\/+$/, "");

    const appUrl = (process.env.APP_URL || "").trim();
    if (!appUrl) return "http://127.0.0.1";

    try {
      const u = new URL(appUrl);
      const local = u.hostname === "localhost" || u.hostname === "127.0.0.1" || u.hostname === "::1";
      if (local) {
        return `${u.protocol}//${u.host}`.replace(/\/+$/, "");
      }
      const port = u.port ? `:${u.port}` : "";
      return `http://127.0.0.1${port}`;
    } catch {
      return "http://127.0.0.1";
    }
  }

  const SOCKET_PORT = Number(process.env.SOCKET_PORT || 6001);
  const LARAVEL_API_URL = resolveLaravelApiBase();
  const SOCKET_INTERNAL_SECRET = process.env.SOCKET_INTERNAL_SECRET || "";
  const SOCKET_CORS_ORIGIN = process.env.SOCKET_CORS_ORIGIN || "*";
  const fetchFn = (...args) => {
    if (typeof fetch !== "undefined") {
      return fetch(...args);
    }
    return import("node-fetch").then(({ default: fetchPolyfill }) => fetchPolyfill(...args));
  };

  const app = express();
  app.use(express.json({ limit: "10mb" }));
  app.use(cors({ origin: SOCKET_CORS_ORIGIN === "*" ? true : SOCKET_CORS_ORIGIN.split(",") }));

  // Register plain HTTP routes before Socket.IO attaches (avoids "Cannot GET /" on older deploy semantics).
  app.get("/", (_, res) => {
    const portHint = SOCKET_PORT === 6001 ? "" : ` (configured port ${SOCKET_PORT})`;
    res.json({
      ok: true,
      service: "socket-server",
      health: "/health",
      socket_io_path: "/socket.io/",
      hint: `Browser address bar alone does not open a realtime socket. Use the Socket.IO client; URL must be origin only${portHint}.`,
      client_example:
        'io("http://YOUR_HOST:YOUR_PORT", { auth: { token: "<jwt>" } }) — do not append socket URL onto your REST API base path.',
    });
  });

  app.get("/health", (_, res) => {
    res.json({ ok: true, service: "socket-server" });
  });

  const server = http.createServer(app);
  const io = new Server(server, {
    cors: {
      origin: SOCKET_CORS_ORIGIN === "*" ? true : SOCKET_CORS_ORIGIN.split(","),
      methods: ["GET", "POST"],
    },
  });

  const userSockets = new Map(); // userId -> Set(socket.id)
  const socketMeta = new Map(); // socket.id -> { userId, token, roomIds[] }

  function authTokenFromSocket(socket) {
    const fromAuth = socket.handshake.auth && socket.handshake.auth.token;
    if (fromAuth) return fromAuth;
    const authHeader = socket.handshake.headers && socket.handshake.headers.authorization;
    if (!authHeader) return null;
    if (authHeader.startsWith("Bearer ")) return authHeader.slice(7);
    return authHeader;
  }

  function getUserOnlineCount(userId) {
    const set = userSockets.get(userId);
    return set ? set.size : 0;
  }

  function isUserOnline(userId) {
    return getUserOnlineCount(userId) > 0;
  }

  async function laravelFetch(path, options = {}) {
    const url = `${LARAVEL_API_URL}${path}`;
    let response;
    try {
      response = await fetchFn(url, {
        ...options,
        headers: {
          Accept: "application/json",
          ...(options.headers || {}),
        },
      });
    } catch (e) {
      const err = e instanceof Error ? e.message : String(e);
      throw new Error(
        `Cannot reach Laravel at ${url} (${err}). Set LARAVEL_API_URL in .env (e.g. http://127.0.0.1 or http://127.0.0.1:8081) if nginx uses a non-default port.`
      );
    }

    const text = await response.text();
    let body = null;
    try {
      body = text ? JSON.parse(text) : null;
    } catch {
      body = text ? { _nonJson: text.slice(0, 400) } : null;
    }

    if (!response.ok) {
      const message =
        (body && body.message) ||
        (body && body._nonJson) ||
        `Laravel API error ${response.status}`;
      throw new Error(typeof message === "string" ? message : JSON.stringify(message));
    }

    return body;
  }

  async function touchActivity(userId) {
    if (!SOCKET_INTERNAL_SECRET) {
      return;
    }

    try {
      await laravelFetch("/api/socket/internal/activity", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "x-socket-secret": SOCKET_INTERNAL_SECRET,
        },
        body: JSON.stringify({ user_id: userId }),
      });
    } catch (error) {
      console.error("Activity touch failed:", error.message);
    }
  }

  async function setPresence(userId, online, appForeground = null) {
    if (!SOCKET_INTERNAL_SECRET) {
      return;
    }

    try {
      const payload = {
        user_id: userId,
        is_online: !!online,
      };

      if (appForeground !== null) {
        payload.is_app_foreground = !!appForeground;
      }

      await laravelFetch("/api/socket/internal/presence", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "x-socket-secret": SOCKET_INTERNAL_SECRET,
        },
        body: JSON.stringify(payload),
      });
    } catch (error) {
      console.error("Presence update failed:", error.message);
    }
  }

  async function authenticateSocket(socket, token) {
    try {
      const payload = await laravelFetch("/api/socket/me", {
        method: "GET",
        headers: {
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
        },
      });

      if (!payload || typeof payload !== "object") {
        throw new Error("Invalid authentication response from Laravel");
      }

      if (!payload.data) {
        throw new Error("No user data returned from Laravel authentication endpoint");
      }

      return payload.data;
    } catch (error) {
      console.error("[auth] Socket authentication failed:", error.message);
      throw error;
    }
  }
  io.use(async (socket, next) => {
    try {
      const token = authTokenFromSocket(socket);
      if (!token) {
        console.warn("[socket] handshake rejected: missing auth token (use auth: { token } or Authorization: Bearer)", socket.id);
        return next(new Error("Missing auth token"));
      }

      const userData = await authenticateSocket(socket, token);
      
      if (!userData || !userData.id) {
        console.warn("[socket] handshake rejected: invalid user data returned", socket.id);
        return next(new Error("Invalid user data"));
      }

      socket.data.user = userData;
      socket.data.token = token;
      console.log("[socket] authentication successful for user_id=%s, sid=%s", userData.id, socket.id);
      return next();
    } catch (error) {
      const msg = error.message || "Socket authentication failed";
      console.warn("[socket] handshake rejected:", msg, "| sid=", socket.id, "| laravel=", LARAVEL_API_URL);
      return next(new Error(msg));
    }
  });

  /**
   * CRITICAL FIX #2: Connection handler with comprehensive event logging
   * 
   * Issues Fixed:
   * 1. Added global error handler for socket-level errors
   * 2. Added event listener debugging middleware that logs when events are triggered
   * 3. Enhanced console logging for troubleshooting connection issues
   * 4. Proper handling of undefined payload parameters
   * 
   * The debugging middleware wraps all socket.on() calls to log when events fire,
   * which helps identify if the real problem is:
   * - Events not being sent by the client
   * - Events being sent but not received by server
   * - Events received but failing silently inside handlers
   */
  io.on("connection", async (socket) => {
    const { id: userId, room_ids: roomIds = [] } = socket.data.user;
    const token = socket.data.token;

    console.log(
      "[socket] connected user_id=%s sid=%s transport=%s",
      userId,
      socket.id,
      socket.conn?.transport?.name || "?"
    );

    if (!userSockets.has(userId)) {
      userSockets.set(userId, new Set());
    }
    userSockets.get(userId).add(socket.id);
    socketMeta.set(socket.id, { userId, token, roomIds });

    socket.join(`user:${userId}`);
    roomIds.forEach((roomId) => socket.join(`chat:${roomId}`));

    if (getUserOnlineCount(userId) === 1) {
      await setPresence(userId, true, true);
    }

    socket.emit("socket:ready", {
      user_id: userId,
      room_ids: roomIds,
    });

    // 🚀 AUTO-FETCH: Send nearby rides immediately after connection (for drivers)
    // Store driver status for future updates
    let isDriver = false;

    const RIDE_VISIBILITY_MS = 60000;
    let rideHideTimer = null;

    const scheduleRideHideIfNeeded = (count, source = "unknown") => {
      if (rideHideTimer) {
        clearTimeout(rideHideTimer);
        rideHideTimer = null;
      }

      if (count > 0) {
        console.log(
          "[socket] ⏱ Scheduling ride hide in %ds for driver user_id=%s (source=%s)",
          RIDE_VISIBILITY_MS / 1000,
          userId,
          source
        );

        rideHideTimer = setTimeout(() => {
          console.log(
            "[socket] 🙈 Auto-hiding rides after %ds for driver user_id=%s",
            RIDE_VISIBILITY_MS / 1000,
            userId
          );
          socket.emit("driver:nearby-rides:list", {
            success: true,
            data: [],
            count: 0,
            timestamp: new Date().toISOString(),
            hidden: true,
            reason: "visibility_timeout",
          });
          rideHideTimer = null;
        }, RIDE_VISIBILITY_MS);
      }
    };

    const emitNearbyRidesWithAutoHide = (responseData, source = "unknown") => {
      socket.emit("driver:nearby-rides:list", responseData);
      const count = responseData?.count ?? responseData?.data?.length ?? 0;
      scheduleRideHideIfNeeded(count, source);
    };

    // Helper function to refresh nearby rides for this driver
    const refreshNearbyRides = async () => {
      if (!socket.data.isDriver) return;
      
      try {
        const nearbyRides = await laravelFetch("/api/driver/near/by/ride", {
          method: "GET",
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });
        
        console.log("[socket] 🔄 Refreshing nearby rides for driver user_id=%s, count=%d", 
          userId, nearbyRides?.data?.length || 0);
        
        emitNearbyRidesWithAutoHide({
          success: true,
          data: nearbyRides?.data || [],
          count: nearbyRides?.data?.length || 0,
          timestamp: new Date().toISOString(),
        }, "refresh");
      } catch (error) {
        console.error("[socket] ✗ Refresh nearby rides error:", error.message);
      }
    };
    
    (async () => {
      try {
        const userData = await laravelFetch("/api/socket/me", {
          method: "GET",
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });
        
        // Driver UI is based on role.
        if (userData && userData.data && userData.data.role === "driver") {
          isDriver = true;
          socket.data.isDriver = true;
          console.log("[socket] ✓ Driver connected - auto-fetching nearby rides for user_id=%s", userId);
          
          await refreshNearbyRides();
        }
      } catch (error) {
        console.error("[socket] ✗ Auto-fetch nearby rides error:", error.message);
      }
    })();
    
    // Store refresh function for use in broadcast events
    socket.data.refreshNearbyRides = refreshNearbyRides;

    // Global error handler for the socket
    socket.on("error", (error) => {
      console.error("[socket] socket error for user_id=%s:", userId, error);
    });

    // Log when socket events are registered for debugging
    console.log("[socket] registering event listeners for user_id=%s", userId);

    let lastActivityTouch = Date.now();

    // Add a wildcard listener to catch ANY incoming event (for debugging)
    socket.onAny((eventName, ...args) => {
      console.log("[socket] ANY EVENT RECEIVED - user_id=%s, event=%s, args count=%d", userId, eventName, args.length);

      const now = Date.now();
      if (now - lastActivityTouch >= 15000) {
        lastActivityTouch = now;
        touchActivity(userId);
      }
    });

    socket.on("chat:sync", async () => {
      console.log("[socket] chat:sync event fired - user_id=%s", userId);
      try {
        console.log("[socket] chat:sync - user_id=%s, syncing room subscriptions", userId);
        
        const userData = await authenticateSocket(socket, token);
        const nextRoomIds = userData.room_ids || [];
        socketMeta.set(socket.id, { userId, token, roomIds: nextRoomIds });
        nextRoomIds.forEach((roomId) => socket.join(`chat:${roomId}`));
        
        console.log("[socket] chat:sync - user_id=%s, joined %d rooms", userId, nextRoomIds.length);
        socket.emit("chat:sync:ok", { room_ids: nextRoomIds });
      } catch (error) {
        console.error("[socket] chat:sync error:", error.message);
        socket.emit("chat:error", { message: error.message });
      }
    });

    socket.on("chat:join", ({ room_id } = {}) => {
      console.log("[socket] chat:join event fired - user_id=%s", userId);
      if (!room_id) {
        console.warn("[socket] chat:join - missing room_id");
        return;
      }
      console.log("[socket] chat:join - user_id=%s, room_id=%s", userId, room_id);
      socket.join(`chat:${room_id}`);
    });

    socket.on("chat:typing", ({ room_id, is_typing } = {}) => {
      console.log("[socket] chat:typing event fired - user_id=%s", userId);
      if (!room_id) {
        console.warn("[socket] chat:typing - missing room_id");
        return;
      }
      console.log("[socket] chat:typing - user_id=%s, room_id=%s, is_typing=%s", userId, room_id, !!is_typing);
      socket.to(`chat:${room_id}`).emit("chat:typing", {
        room_id,
        user_id: userId,
        is_typing: !!is_typing,
      });
    });
    socket.on("chat:send", async (payload = {}, callback) => {
      console.log("[socket] chat:send event fired - user_id=%s", userId);
      try {
        if (!payload || typeof payload !== "object") {
          throw new Error("Invalid payload");
        }

        console.log("[socket] chat:send - user_id=%s, room_id=%s, ride_id=%s", userId, payload.room_id, payload.ride_id);
        
        const raw = await laravelFetch("/api/chat/messages", {
          method: "POST",
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
            Accept: "application/json",
          },
          body: JSON.stringify({
            room_id: payload.room_id ?? null,
            ride_id: payload.ride_id ?? null,
            message_type: payload.message_type || (payload.image_url || payload.image ? "image" : "text"),
            message: payload.message || null,
            image_url: payload.image_url || payload.image || null,
            image_base64: payload.image_base64 || null,
            meta: payload.meta || null,
          }),
        });

        let message = raw != null && typeof raw === "object" && Object.prototype.hasOwnProperty.call(raw, "data") ? raw.data : null;
        if (Array.isArray(message)) {
          message = message[0] ?? null;
        }

        console.log("[socket] chat:send - message sent successfully, message_id=%s", message?.id || "unknown");
        if (typeof callback === "function") {
          callback(message);
        }
      } catch (error) {
        console.error("[socket] chat:send error:", error.message);
        const errorResponse = { ok: false, message: error.message };
        if (typeof callback === "function") {
          callback(errorResponse);
        } else {
          socket.emit("chat:error", errorResponse);
        }
      }
    });

    socket.on("presence:status", ({ user_id } = {}, callback) => {
      console.log("[socket] presence:status event fired - user_id=%s", userId);
      if (!user_id) {
        console.warn("[socket] presence:status - missing user_id");
        const error = { ok: false, message: "user_id is required" };
        if (typeof callback === "function") {
          callback(error);
        } else {
          socket.emit("presence:status:error", error);
        }
        return;
      }
      
      const online = isUserOnline(Number(user_id));
      console.log("[socket] presence:status - user_id=%s, is_online=%s", user_id, online);
      
      if (typeof callback === "function") {
        callback({ user_id, is_online: online });
      } else {
        socket.emit("presence:status:result", { user_id, is_online: online });
      }
    });

    socket.on("app:foreground", async (_payload, callback) => {
      console.log("[socket] app:foreground - user_id=%s", userId);
      await setPresence(userId, true, true);

      const response = { ok: true, user_id: userId, is_app_foreground: true };
      if (typeof callback === "function") {
        callback(response);
      } else {
        socket.emit("app:foreground:result", response);
      }
    });

    socket.on("app:background", async (_payload, callback) => {
      console.log("[socket] app:background - user_id=%s", userId);
      await setPresence(userId, true, false);

      const response = { ok: true, user_id: userId, is_app_foreground: false };
      if (typeof callback === "function") {
        callback(response);
      } else {
        socket.emit("app:background:result", response);
      }
    });
    socket.on("driver:nearby-rides", async (payload, callback) => {
      console.log("[socket] ✓ driver:nearby-rides listener TRIGGERED - user_id=%s", userId);
      console.log("[socket] driver:nearby-rides - payload=%O, callback present=%s", payload, typeof callback === "function");
      try {
        console.log("[socket] driver:nearby-rides - fetching from Laravel API");
        
        const raw = await laravelFetch("/api/driver/near/by/ride", {
          method: "GET",
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });

        console.log("[socket] ✓ driver:nearby-rides - API response received. Data length=%d", raw?.data?.length || 0);
        
        const responseData = {
          success: true,
          data: raw?.data || [],
          count: raw?.data?.length || 0,
          timestamp: new Date().toISOString(),
        };
        
        if (typeof callback === "function") {
          console.log("[socket] driver:nearby-rides - sending response via callback (ACK)");
          callback(responseData);
          scheduleRideHideIfNeeded(responseData.count, "manual-callback");
        } else {
          console.log("[socket] driver:nearby-rides - broadcasting via emit:driver:nearby-rides:list");
          emitNearbyRidesWithAutoHide(responseData, "manual");
        }
        console.log("[socket] ✓ driver:nearby-rides - response sent successfully");
      } catch (error) {
        console.error("[socket] ✗ driver:nearby-rides error:", error.message);
        const errorResponse = { 
          success: false, 
          message: error.message,
          data: [],
          count: 0,
        };
        console.log("[socket] driver:nearby-rides - sending error response=%O", errorResponse);
        if (typeof callback === "function") {
          callback(errorResponse);
        } else {
          socket.emit("driver:nearby-rides:error", errorResponse);
        }
      }
    });

    // 🚗 CURRENT RIDE SYSTEM: Auto-refresh accepted ride with real-time updates
    let currentRideInterval = null;
    let currentRideData = null;
    
    // Function to fetch and emit current ride
    const fetchAndEmitCurrentRide = async () => {
      try {
        const raw = await laravelFetch("/api/driver/ride/accept", {
          method: "GET",
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });

        const hasRide = raw && raw.data && (Array.isArray(raw.data) ? raw.data.length > 0 : raw.data.id);
        currentRideData = hasRide ? raw.data : null;
        
        console.log("[socket] 🔄 Current ride refresh - user_id=%s, has_ride=%s", userId, hasRide);
        
        socket.emit("driver:current-ride:update", {
          success: true,
          data: currentRideData,
          has_ride: hasRide,
          timestamp: new Date().toISOString(),
        });

        // Adjust refresh interval based on ride status
        if (hasRide) {
          // Has accepted ride - refresh every 5 seconds for real-time updates
          if (currentRideInterval) {
            clearInterval(currentRideInterval);
          }
          currentRideInterval = setInterval(fetchAndEmitCurrentRide, 5000);
          console.log("[socket] ✓ Current ride tracking active - refreshing every 5s");
        } else {
          // No ride - check every 10 seconds in case ride gets accepted
          if (currentRideInterval) {
            clearInterval(currentRideInterval);
          }
          currentRideInterval = setInterval(fetchAndEmitCurrentRide, 10000);
          console.log("[socket] ⏳ Waiting for ride acceptance - checking every 10s");
        }

        return raw;
      } catch (error) {
        console.error("[socket] ✗ Current ride fetch error:", error.message);
        socket.emit("driver:current-ride:error", {
          success: false,
          message: error.message,
        });
        return null;
      }
    };

    socket.on("driver:get-accepted-rides", async (payload, callback) => {
      console.log("[socket] ✓ driver:get-accepted-rides listener TRIGGERED - user_id=%s", userId);
      try {
        console.log("[socket] driver:get-accepted-rides - fetching from Laravel API");
        
        const raw = await fetchAndEmitCurrentRide();

        console.log("[socket] ✓ driver:get-accepted-rides - API response received. Has ride=%s", 
          raw && raw.data ? 'yes' : 'no');
        
        if (typeof callback === "function") {
          console.log("[socket] driver:get-accepted-rides - sending via callback");
          callback({
            success: true,
            data: raw?.data || null,
            has_ride: !!(raw && raw.data),
            timestamp: new Date().toISOString(),
          });
        }
        
        console.log("[socket] ✓ driver:get-accepted-rides - response sent & auto-refresh started");
      } catch (error) {
        console.error("[socket] ✗ driver:get-accepted-rides error:", error.message);
        const errorResponse = { 
          success: false, 
          message: error.message,
          data: null,
          has_ride: false,
        };
        if (typeof callback === "function") {
          callback(errorResponse);
        } else {
          socket.emit("driver:get-accepted-rides:error", errorResponse);
        }
      }
    });

    // Manual stop current ride tracking
    socket.on("driver:stop-ride-tracking", () => {
      console.log("[socket] ✓ driver:stop-ride-tracking - user_id=%s", userId);
      if (currentRideInterval) {
        clearInterval(currentRideInterval);
        currentRideInterval = null;
        currentRideData = null;
        console.log("[socket] ✓ Current ride tracking stopped");
      }
    });

    socket.on("ride:get-bids", async (payload, callback) => {
      console.log("[socket] ✓ ride:get-bids listener TRIGGERED - user_id=%s", userId);
      const rideId = payload && (payload.ride_id || payload.rideId || payload.id);
      if (!rideId) {
        console.warn("[socket] ✗ ride:get-bids - missing ride_id in payload=%O", payload);
        const error = { ok: false, message: "ride_id is required" };
        if (typeof callback === "function") {
          callback(error);
        } else {
          socket.emit("ride:get-bids:error", error);
        }
        return;
      }

      try {
        console.log("[socket] ride:get-bids - fetching bids for ride_id=%s", rideId);
        
        const raw = await laravelFetch(`/api/get-bid/${encodeURIComponent(rideId)}`, {
          method: "GET",
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });

        console.log("[socket] ✓ ride:get-bids - API response received. Bid count=%d", raw?.data?.length || 0);
        
        if (typeof callback === "function") {
          callback(raw);
        } else {
          socket.emit("ride:get-bids:result", raw);
        }
        console.log("[socket] ✓ ride:get-bids - response sent");
      } catch (error) {
        console.error("[socket] ✗ ride:get-bids error:", error.message);
        const errorResponse = { ok: false, message: error.message };
        if (typeof callback === "function") {
          callback(errorResponse);
        } else {
          socket.emit("ride:get-bids:error", errorResponse);
        }
      }
    });

    socket.on("ride:accept-bid", async (payload, callback) => {
      console.log("[socket] ✓ ride:accept-bid listener TRIGGERED - user_id=%s", userId);
      const rideId = payload && (payload.ride_id || payload.rideId);
      const bidId = payload && (payload.bid_id || payload.bidId);
      
      if (!rideId || !bidId) {
        console.warn("[socket] ✗ ride:accept-bid - missing params. rideId=%s, bidId=%s, payload=%O", rideId, bidId, payload);
        const error = { ok: false, message: "ride_id and bid_id are required" };
        if (typeof callback === "function") {
          callback(error);
        } else {
          socket.emit("ride:accept-bid:error", error);
        }
        return;
      }

      try {
        console.log("[socket] ride:accept-bid - accepting bid ride_id=%s, bid_id=%s", rideId, bidId);
        
        const raw = await laravelFetch(`/api/ride/${encodeURIComponent(rideId)}/bid/accept/${encodeURIComponent(bidId)}`, {
          method: "POST",
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
            Accept: "application/json",
          },
          body: JSON.stringify({}),
        });

        console.log("[socket] ✓ ride:accept-bid - API accepted the bid. Response=%O", raw);
        
        if (typeof callback === "function") {
          callback(raw);
        } else {
          socket.emit("ride:accept-bid:result", raw);
        }
        console.log("[socket] ✓ ride:accept-bid - response sent");
      } catch (error) {
        console.error("[socket] ✗ ride:accept-bid error:", error.message);
        const errorResponse = { ok: false, message: error.message };
        if (typeof callback === "function") {
          callback(errorResponse);
        } else {
          socket.emit("ride:accept-bid:error", errorResponse);
        }
      }
    });

    // ═══════════════════════════════════════════════════════════════════════════
    // RIDE MANAGEMENT SOCKET EVENTS (Real-time alternative to API endpoints)
    // ═══════════════════════════════════════════════════════════════════════════

    socket.on("ride:create-booking", async (payload, callback) => {
      console.log("[socket] ✓ ride:create-booking listener TRIGGERED - user_id=%s", userId);
      const required = ['start_latitude', 'start_longitude', 'end_latitude', 'end_longitude', 'start', 'destination'];
      const missing = required.filter(field => !payload || payload[field] == null);
      
      if (missing.length > 0) {
        console.warn("[socket] ✗ ride:create-booking - missing fields=%O", missing);
        const error = { ok: false, message: `Missing required fields: ${missing.join(', ')}` };
        if (typeof callback === "function") {
          callback(error);
        } else {
          socket.emit("ride:create-booking:error", error);
        }
        return;
      }

      try {
        console.log("[socket] ride:create-booking - creating ride from (%s,%s) to (%s,%s)", 
          payload.start_latitude, payload.start_longitude, payload.end_latitude, payload.end_longitude);
        
        const raw = await laravelFetch("/api/booking", {
          method: "POST",
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
            Accept: "application/json",
          },
          body: JSON.stringify({
            start_latitude: payload.start_latitude,
            start_longitude: payload.start_longitude,
            end_latitude: payload.end_latitude,
            end_longitude: payload.end_longitude,
            start: payload.start,
            destination: payload.destination,
          }),
        });

        console.log("[socket] ✓ ride:create-booking - ride created. ride_id=%s", raw?.data?.ride?.id || 'unknown');
        
        if (typeof callback === "function") {
          callback(raw);
        } else {
          socket.emit("ride:create-booking:result", raw);
        }
        console.log("[socket] ✓ ride:create-booking - response sent");
      } catch (error) {
        console.error("[socket] ✗ ride:create-booking error:", error.message);
        const errorResponse = { ok: false, message: error.message };
        if (typeof callback === "function") {
          callback(errorResponse);
        } else {
          socket.emit("ride:create-booking:error", errorResponse);
        }
      }
    });

    socket.on("ride:update-booking", async (payload, callback) => {
      console.log("[socket] ✓ ride:update-booking listener TRIGGERED - user_id=%s", userId);
      const rideId = payload && (payload.ride_id || payload.rideId || payload.id);
      const vehicleCategoryId = payload && (payload.vehicle_category_id || payload.vehicleCategoryId);
      
      if (!rideId || !vehicleCategoryId) {
        console.warn("[socket] ✗ ride:update-booking - missing params. rideId=%s, vehicleCategoryId=%s", rideId, vehicleCategoryId);
        const error = { ok: false, message: "ride_id and vehicle_category_id are required" };
        if (typeof callback === "function") {
          callback(error);
        } else {
          socket.emit("ride:update-booking:error", error);
        }
        return;
      }

      try {
        console.log("[socket] ride:update-booking - updating ride_id=%s with vehicle_category_id=%s", rideId, vehicleCategoryId);
        
        const bodyData = {
          vehicle_category_id: vehicleCategoryId,
        };
        
        if (payload.promo_code) {
          bodyData.promo_code = payload.promo_code;
        }
        
        const raw = await laravelFetch(`/api/booking/${encodeURIComponent(rideId)}`, {
          method: "PUT",
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
            Accept: "application/json",
          },
          body: JSON.stringify(bodyData),
        });

        console.log("[socket] ✓ ride:update-booking - ride updated. status=%s, fare=%s", raw?.data?.status, raw?.data?.estimated_fare);
        
        if (typeof callback === "function") {
          callback(raw);
        } else {
          socket.emit("ride:update-booking:result", raw);
        }
        console.log("[socket] ✓ ride:update-booking - response sent & nearby drivers notified");
      } catch (error) {
        console.error("[socket] ✗ ride:update-booking error:", error.message);
        const errorResponse = { ok: false, message: error.message };
        if (typeof callback === "function") {
          callback(errorResponse);
        } else {
          socket.emit("ride:update-booking:error", errorResponse);
        }
      }
    });

    socket.on("ride:cancel", async (payload, callback) => {
      console.log("[socket] ✓ ride:cancel listener TRIGGERED - user_id=%s", userId);
      const rideId = payload && (payload.ride_id || payload.rideId || payload.id);
      
      if (!rideId) {
        console.warn("[socket] ✗ ride:cancel - missing ride_id in payload=%O", payload);
        const error = { ok: false, message: "ride_id is required" };
        if (typeof callback === "function") {
          callback(error);
        } else {
          socket.emit("ride:cancel:error", error);
        }
        return;
      }

      try {
        console.log("[socket] ride:cancel - canceling ride_id=%s, reason=%s", rideId, payload.reason || 'no reason');
        
        const raw = await laravelFetch(`/api/ride/cancel/${encodeURIComponent(rideId)}`, {
          method: "POST",
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
            Accept: "application/json",
          },
          body: JSON.stringify({
            reason: payload.reason || null,
          }),
        });

        console.log("[socket] ✓ ride:cancel - ride canceled successfully");
        
        if (typeof callback === "function") {
          callback(raw);
        } else {
          socket.emit("ride:cancel:result", raw);
        }
        console.log("[socket] ✓ ride:cancel - response sent & notifications dispatched");
      } catch (error) {
        console.error("[socket] ✗ ride:cancel error:", error.message);
        const errorResponse = { ok: false, message: error.message };
        if (typeof callback === "function") {
          callback(errorResponse);
        } else {
          socket.emit("ride:cancel:error", errorResponse);
        }
      }
    });

    socket.on("ride:update-bid-amount", async (payload, callback) => {
      console.log("[socket] ✓ ride:update-bid-amount listener TRIGGERED - user_id=%s", userId);
      const rideId = payload && (payload.ride_id || payload.rideId || payload.id);
      const finalFare = payload && (payload.final_fare || payload.finalFare || payload.amount);
      
      if (!rideId || finalFare == null) {
        console.warn("[socket] ✗ ride:update-bid-amount - missing params. rideId=%s, finalFare=%s", rideId, finalFare);
        const error = { ok: false, message: "ride_id and final_fare are required" };
        if (typeof callback === "function") {
          callback(error);
        } else {
          socket.emit("ride:update-bid-amount:error", error);
        }
        return;
      }

      try {
        console.log("[socket] ride:update-bid-amount - updating ride_id=%s with final_fare=%s", rideId, finalFare);
        
        const raw = await laravelFetch(`/api/ride/${encodeURIComponent(rideId)}/update-bid-amount`, {
          method: "POST",
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
            Accept: "application/json",
          },
          body: JSON.stringify({
            final_fare: finalFare,
          }),
        });

        console.log("[socket] ✓ ride:update-bid-amount - fare updated successfully");
        
        if (typeof callback === "function") {
          callback(raw);
        } else {
          socket.emit("ride:update-bid-amount:result", raw);
        }
        console.log("[socket] ✓ ride:update-bid-amount - response sent & nearby drivers notified");
      } catch (error) {
        console.error("[socket] ✗ ride:update-bid-amount error:", error.message);
        const errorResponse = { ok: false, message: error.message };
        if (typeof callback === "function") {
          callback(errorResponse);
        } else {
          socket.emit("ride:update-bid-amount:error", errorResponse);
        }
      }
    });

    socket.on("ride:get-accepted", async (payload, callback) => {
      console.log("[socket] ✓ ride:get-accepted listener TRIGGERED - user_id=%s", userId);
      
      try {
        console.log("[socket] ride:get-accepted - fetching accepted rides for passenger");
        
        const raw = await laravelFetch("/api/rides/accept", {
          method: "GET",
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });

        console.log("[socket] ✓ ride:get-accepted - API response received. Rides count=%d", raw?.data?.length || 0);
        
        if (typeof callback === "function") {
          callback(raw);
        } else {
          socket.emit("ride:get-accepted:result", raw);
        }
        console.log("[socket] ✓ ride:get-accepted - response sent");
      } catch (error) {
        console.error("[socket] ✗ ride:get-accepted error:", error.message);
        const errorResponse = { ok: false, message: error.message };
        if (typeof callback === "function") {
          callback(errorResponse);
        } else {
          socket.emit("ride:get-accepted:error", errorResponse);
        }
      }
    });

    socket.on("ride:apply-promo", async (payload, callback) => {
      console.log("[socket] ✓ ride:apply-promo listener TRIGGERED - user_id=%s", userId);
      const rideId = payload && (payload.ride_id || payload.rideId || payload.id);
      const promoCode = payload && (payload.promo_code || payload.promoCode);
      
      if (!rideId || !promoCode) {
        console.warn("[socket] ✗ ride:apply-promo - missing params. rideId=%s, promoCode=%s", rideId, promoCode);
        const error = { ok: false, message: "ride_id and promo_code are required" };
        if (typeof callback === "function") {
          callback(error);
        } else {
          socket.emit("ride:apply-promo:error", error);
        }
        return;
      }

      try {
        console.log("[socket] ride:apply-promo - applying promo '%s' to ride_id=%s", promoCode, rideId);
        
        const raw = await laravelFetch(`/api/apply-promo/${encodeURIComponent(rideId)}`, {
          method: "POST",
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
            Accept: "application/json",
          },
          body: JSON.stringify({
            promo_code: promoCode,
          }),
        });

        console.log("[socket] ✓ ride:apply-promo - promo applied. New fare calculations received");
        
        if (typeof callback === "function") {
          callback(raw);
        } else {
          socket.emit("ride:apply-promo:result", raw);
        }
        console.log("[socket] ✓ ride:apply-promo - response sent");
      } catch (error) {
        console.error("[socket] ✗ ride:apply-promo error:", error.message);
        const errorResponse = { ok: false, message: error.message };
        if (typeof callback === "function") {
          callback(errorResponse);
        } else {
          socket.emit("ride:apply-promo:error", errorResponse);
        }
      }
    });

    socket.on("ride:get-by-id", async (payload, callback) => {
      console.log("[socket] ✓ ride:get-by-id listener TRIGGERED - user_id=%s", userId);
      const rideId = payload && (payload.ride_id || payload.rideId || payload.id);
      
      if (!rideId) {
        console.warn("[socket] ✗ ride:get-by-id - missing ride_id in payload=%O", payload);
        const error = { ok: false, message: "ride_id is required" };
        if (typeof callback === "function") {
          callback(error);
        } else {
          socket.emit("ride:get-by-id:error", error);
        }
        return;
      }

      try {
        console.log("[socket] ride:get-by-id - fetching ride details for ride_id=%s", rideId);
        
        const raw = await laravelFetch(`/api/ride/${encodeURIComponent(rideId)}`, {
          method: "GET",
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });

        console.log("[socket] ✓ ride:get-by-id - ride details received");
        
        if (typeof callback === "function") {
          callback(raw);
        } else {
          socket.emit("ride:get-by-id:result", raw);
        }
        console.log("[socket] ✓ ride:get-by-id - response sent");
      } catch (error) {
        console.error("[socket] ✗ ride:get-by-id error:", error.message);
        const errorResponse = { ok: false, message: error.message };
        if (typeof callback === "function") {
          callback(errorResponse);
        } else {
          socket.emit("ride:get-by-id:error", errorResponse);
        }
      }
    });

    socket.on("ride:get-history", async (payload, callback) => {
      console.log("[socket] ✓ ride:get-history listener TRIGGERED - user_id=%s", userId);
      
      try {
        console.log("[socket] ride:get-history - fetching ride history (completed/canceled)");
        
        const raw = await laravelFetch("/api/ride/get/by/user", {
          method: "GET",
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });

        console.log("[socket] ✓ ride:get-history - history received. Rides count=%d", raw?.data?.length || 0);
        
        if (typeof callback === "function") {
          callback(raw);
        } else {
          socket.emit("ride:get-history:result", raw);
        }
        console.log("[socket] ✓ ride:get-history - response sent");
      } catch (error) {
        console.error("[socket] ✗ ride:get-history error:", error.message);
        const errorResponse = { ok: false, message: error.message };
        if (typeof callback === "function") {
          callback(errorResponse);
        } else {
          socket.emit("ride:get-history:error", errorResponse);
        }
      }
    });

    // ═══════════════════════════════════════════════════════════════════════════
    // END OF RIDE MANAGEMENT EVENTS
    // ═══════════════════════════════════════════════════════════════════════════

    socket.on("disconnect", async (reason) => {
      console.log("[socket] disconnected sid=%s reason=%s user_id=%s", socket.id, reason, userId);
      if (rideHideTimer) {
        clearTimeout(rideHideTimer);
        rideHideTimer = null;
      }
      const meta = socketMeta.get(socket.id);
      if (!meta) return;

      const set = userSockets.get(meta.userId);
      if (set) {
        set.delete(socket.id);
        if (set.size === 0) { 
          userSockets.delete(meta.userId);
          await setPresence(meta.userId, false, false);
        }
      }

      socketMeta.delete(socket.id);
    });
  });

  app.post("/internal/chat-started", (req, res) => {
    const providedSecret = req.headers["x-socket-secret"];
    if (!SOCKET_INTERNAL_SECRET || providedSecret !== SOCKET_INTERNAL_SECRET) {
      return res.status(401).json({ ok: false, message: "Unauthorized" });
    }

    const { chat_room_id, ride_id, passenger_id, driver_id } = req.body || {};
    if (!chat_room_id || !passenger_id || !driver_id) {
      return res.status(422).json({ ok: false, message: "chat_room_id, passenger_id, driver_id are required." });
    }

    const payload = {
      room_id: chat_room_id,
      ride_id: ride_id || null,
      passenger_id,
      driver_id,
      started_at: new Date().toISOString(),
    };

    io.to(`user:${passenger_id}`).emit("chat:started", payload);
    io.to(`user:${driver_id}`).emit("chat:started", payload);
    io.to(`chat:${chat_room_id}`).emit("chat:started", payload);

    res.json({ ok: true });
  });

  app.post("/internal/broadcast-chat-message", (req, res) => {
    const providedSecret = req.headers["x-socket-secret"];
    if (!SOCKET_INTERNAL_SECRET || providedSecret !== SOCKET_INTERNAL_SECRET) {
      return res.status(401).json({ ok: false, message: "Unauthorized" });
    }

    const body = req.body || {};
    const message = body.message;
    const roomId =
      (message && (message.chat_room_id ?? message.chatRoomId)) ?? body.chat_room_id;

    if (!message || roomId == null) {
      return res.status(422).json({ ok: false, message: "chat_room_id and message are required." });
    }

    io.to(`chat:${roomId}`).emit("chat:new-message", message);
    res.json({ ok: true });
  });

  // ═══════════════════════════════════════════════════════════════════════════
  // INTERNAL BROADCAST ENDPOINT (Called by Laravel to trigger socket events)
  // ═══════════════════════════════════════════════════════════════════════════
  app.post("/internal/broadcast", async (req, res) => {
    const providedSecret = req.headers["x-socket-secret"];
    if (!SOCKET_INTERNAL_SECRET || providedSecret !== SOCKET_INTERNAL_SECRET) {
      console.warn("[socket] ✗ Unauthorized broadcast attempt from IP:", req.ip);
      return res.status(401).json({ ok: false, message: "Unauthorized" });
    }

    const { event, data, user_ids, refresh_drivers } = req.body || {};
    
    if (!event) {
      return res.status(422).json({ ok: false, message: "event is required" });
    }

    console.log("[socket] 📡 Broadcasting event=%s to user_ids=%O, refresh_drivers=%s", 
      event, user_ids || 'all connected', refresh_drivers || false);

    try {
      if (user_ids && Array.isArray(user_ids) && user_ids.length > 0) {
        // Broadcast to specific users
        let sentCount = 0;
        user_ids.forEach(userId => {
          const count = getUserOnlineCount(userId);
          if (count > 0) {
            io.to(`user:${userId}`).emit(event, data || {});
            sentCount++;
            console.log("[socket] ✓ Sent event=%s to user_id=%s (connections=%d)", event, userId, count);
          } else {
            console.log("[socket] ⚠ Skipped user_id=%s (offline)", userId);
          }
        });
        
        // Auto-refresh all online drivers if refresh_drivers flag is set
        if (refresh_drivers) {
          console.log("[socket] 🔄 Auto-refreshing all online drivers' nearby rides list");
          let driverRefreshCount = 0;
          
          // Iterate through all connected sockets
          const sockets = await io.fetchSockets();
          for (const sock of sockets) {
            if (sock.data.isDriver && sock.data.refreshNearbyRides) {
              if (data && data.visibility_reset) {
                sock.emit("driver:ride-visibility-reset", {
                  ride_id: data.ride_id,
                  visibility_seconds: data.visibility_seconds || 60,
                  reason: "bid_placed",
                });
              }
              await sock.data.refreshNearbyRides();
              driverRefreshCount++;
            }
          }
          
          console.log("[socket] ✓ Refreshed nearby rides for %d online drivers", driverRefreshCount);
        }
        
        return res.json({ 
          ok: true, 
          event,
          targeted_users: user_ids.length,
          sent_to: sentCount,
          drivers_refreshed: refresh_drivers ? 'yes' : 'no',
          message: `Event sent to ${sentCount}/${user_ids.length} online users`
        });
      } else {
        // Broadcast to all connected clients
        io.emit(event, data || {});
        const totalConnections = io.engine.clientsCount;
        console.log("[socket] ✓ Broadcast event=%s to all clients (count=%d)", event, totalConnections);
        
        // Auto-refresh all drivers if refresh_drivers flag is set
        if (refresh_drivers) {
          console.log("[socket] 🔄 Auto-refreshing all online drivers' nearby rides list");
          let driverRefreshCount = 0;
          
          const sockets = await io.fetchSockets();
          for (const sock of sockets) {
            if (sock.data.isDriver && sock.data.refreshNearbyRides) {
              if (data && data.visibility_reset) {
                sock.emit("driver:ride-visibility-reset", {
                  ride_id: data.ride_id,
                  visibility_seconds: data.visibility_seconds || 60,
                  reason: "bid_placed",
                });
              }
              await sock.data.refreshNearbyRides();
              driverRefreshCount++;
            }
          }
          
          console.log("[socket] ✓ Refreshed nearby rides for %d online drivers", driverRefreshCount);
        }
        
        return res.json({ 
          ok: true, 
          event,
          broadcast: 'all',
          total_connections: totalConnections,
          drivers_refreshed: refresh_drivers ? 'yes' : 'no',
          message: `Event broadcast to all connected clients`
        });
      }
    } catch (error) {
      console.error("[socket] ✗ Broadcast error:", error.message);
      return res.status(500).json({ ok: false, message: error.message });
    }
  });

  app.use((req, res) => {
    if (req.method === "GET" && req.path.startsWith("/http")) {
      return res.status(400).json({
        ok: false,
        message:
          'The socket server URL was used as an HTTP path (e.g. /http://...) — browsers and HTTP clients cannot connect that way.',
        fix: [
          'Use Socket.IO client: io("http://HOST:PORT", { auth: { token } })',
          'Do not do: axios.get(API_BASE + SOCKET_URL) or path: SOCKET_URL inside io(API_BASE).',
        ],
        health_check: `${req.protocol}://${req.get("host")}/health`,
      });
    }

    res.status(404).json({
      ok: false,
      message: `Nothing here for ${req.method} ${req.path}`,
      health: "/health",
      docs: "/ — service info including client usage",
    });
  });

  server.listen(SOCKET_PORT, () => {
    console.log(`
  ╔════════════════════════════════════════════════════════════════════════════╗
  ║                  ✓ Socket.IO Server Ready                                  ║
  ╠════════════════════════════════════════════════════════════════════════════╣
  ║                                                                            ║
  ║  PORT:              ${SOCKET_PORT}
  ║  LARAVEL API BASE:  ${LARAVEL_API_URL}
  ║                                                                            ║
  ║  FULL DEBUGGING ENABLED - Server logs every socket event                   ║
  ║  Look for: "[socket] ANY EVENT RECEIVED" for incoming events               ║
  ║  Look for: "[socket] ✓ <event-name> listener TRIGGERED"                    ║
  ║                                                                            ║
  ║  REAL-TIME EVENT FLOW:                                                     ║
  ║  1. Client sends: socket.emit("driver:nearby-rides", {}, callback)          ║
  ║  2. Server logs: [socket] ANY EVENT RECEIVED                               ║
  ║  3. Server logs: [socket] ✓ driver:nearby-rides listener TRIGGERED         ║
  ║  4. Server logs: [socket] driver:nearby-rides - fetching from Laravel API  ║
  ║  5. Server logs: [socket] ✓ driver:nearby-rides - API response received    ║
  ║  6. Server logs: [socket] ✓ driver:nearby-rides - response sent            ║
  ║  7. Client receives response via callback                                   ║
  ║                                                                            ║
  ║  TROUBLESHOOTING STEPS:                                                    ║
  ║  ─────────────────────                                                     ║
  ║  If no "ANY EVENT RECEIVED" log:                                           ║
  ║    → Event not sent by client                                              ║
  ║    → Client using wrong socket instance                                    ║
  ║    → Network connectivity issue                                            ║
  ║                                                                            ║
  ║  If "ANY EVENT" shows but no "TRIGGERED":                                  ║
  ║    → Event name mismatch (check spelling exactly)                          ║
  ║    → Event handler not registered                                          ║
  ║                                                                            ║
  ║  If shows "TRIGGERED" but no "API response":                               ║
  ║    → Laravel endpoint not responding (check LARAVEL_API_URL)               ║
  ║    → Authentication token invalid                                          ║
  ║    → Network error reaching Laravel                                        ║
  ║                                                                            ║
  ╚════════════════════════════════════════════════════════════════════════════╝
    `);
  });
