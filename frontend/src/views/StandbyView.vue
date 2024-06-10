<template>
  <div class="pt-16">
    <h1 class="text-3xl font-semibold mb-4">{{ title }}</h1>

    <div v-if="!trip.id" class="mt-8 flex justify-center">
      <Loader />
    </div>

    <div v-else>
      <div
        class="overflow-hidden shadow sm:rounded-md max-w-sm mx-auto text-left"
      >
        <div class="bg-white px-4 py-5 sm:p-6">
          <div>
            <GMapMap
              :zoom="11"
              :center="trip.destination"
              ref="gMap"
              style="width: 100%; height: 256px"
            >
            </GMapMap>
          </div>
          <div class="mt-2">
            <p class="text-xl">
              <strong>Going to</strong> {{ trip.destination_name }}
            </p>
          </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 text-right sm:px-6 justify-between">
          <button
            @click="acceptTrip"
            class="inline-flex justify-center rounded-md border border-trasnparent bg-black py-2 text-white p-5"
          >
            Accept
          </button>
          <button
            @click="declineTrip"
            class="inline-flex justify-center rounded-md border border-trasnparent bg-black py-2 text-white p-5"
          >
            Decline
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from "vue";
import Loader from "../components/Lodaer.vue";
import Echo from "laravel-echo";
import { useTripStore } from "../stores/trip";
import { useLocationStore } from "../stores/location";
import Pusher from "pusher-js";
import axios from "axios";
import { useRouter } from "vue-router";

const title = ref("Waitng for request...");
const gMap = ref(null);

const trip = useTripStore();
const location = useLocationStore();

const router = useRouter();

const acceptTrip = () => {
  axios
    .post(
      `http://localhost:8000/api/trip/${trip.id}/accept`,
      {
        driver_location: location.current.geometry,
      },
      {
        headers: {
          Authorization: `Bearer ${localStorage.getItem("token")}`,
        },
      }
    )
    .then((res) => {
      location.$patch({
        destination: {
          name: "Passenger",
          geometry: res.data.origin,
        },
      });
      router.push({
        name: "driving",
      });
    })
    .catch((err) => {
      console.log(err);
    });
};

const declineTrip = () => {
  trip.$patch({
    id: null,
    user_id: null,

    origin: {
      lat: null,
      lng: null,
    },

    destination: {
      lat: null,
      lng: null,
    },
    destination_name: "",
  });

  title.value = "Waitng for request...";
};

onMounted(async () => {
  await location.updateCurrentLocation();
  let echo = new Echo({
    broadcaster: "reverb",
    key: "1imhphbk8ucxhcoebih7",
    wsHost: "127.0.0.1",
    wsPort: 8080,
    forceTLS: false,
    enabledTransports: ["ws", "wss"],
  });

  echo.channel("drivers").listen("TripCreated", (e) => {
    title.value = "Ride Requested";
    trip.$patch(e.trip);

    setTimeout(initMapDirections, 2000);
  });
});

const initMapDirections = () => {
  gMap.value.$mapPromise.then((mapObject) => {
    let originPoint = new google.maps.LatLng(trip.origin),
      destinationPoint = new google.maps.LatLng(trip.destination),
      directionsService = new google.maps.DirectionsService(),
      directionsDisplay = new google.maps.DirectionsRenderer({
        map: mapObject,
      });

    directionsService.route(
      {
        origin: originPoint,
        destination: destinationPoint,
        avoidTolls: false,
        avoidHighways: false,
        travelMode: google.maps.TravelMode.DRIVING,
      },
      (res, status) => {
        if (status === google.maps.DirectionsStatus.OK) {
          directionsDisplay.setDirections(res);
        } else {
          console.log(status);
        }
      }
    );
  });
};
</script>