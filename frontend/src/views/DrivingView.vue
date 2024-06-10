<template>
  <div class="pt-16">
    <h1 class="text-3xl font-semibold mb-4">{{ title }}</h1>
    <div>
      <div
        class="overflow-hidden shadow sm:rounded-md max-w-sm mx-auto text-left"
        v-if="!trip.is_complete"
      >
        <div class="bg-white px-4 py-5 sm:p-6">
          <div>
            <GMapMap
              :zoom="14"
              ref="gMap"
              :center="location.current.geometry"
              style="width: 100%; height: 256px"
            >
              <!-- Currnet location -->
              <GMapMarker
                :position="location.current.geometry"
                :icon="currentIcon"
              />
              <!-- Destination ico -->
              <GMapMarker
                :position="trip.destination"
                :icon="destinationIcon"
              />
            </GMapMap>
          </div>
          <div class="mt-2">
            <p class="text-xl">Going to <strong> pick up passenger </strong></p>
          </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 text-right sm:px-6">
          <button
            v-if="trip.is_started"
            class="inline-flex text-white p-5 justify-center rounded-md border border-transparent bg-black py-2"
            @click="handleCompleteTrip"
          >
            Compelete Trip
          </button>

          <button
            v-else
            class="text-white p-5 inline-flex justify-center rounded-md border border-transparent bg-black py-2"
            @click="handlePassengerPickedUp"
          >
            Picked Up Passenger
          </button>
        </div>
      </div>
      <div v-else class="overflow-hidden shadow sm:rounded-md max-w-sm mx-auto text-left">
        <div class="bg-white px-4 py-5 sm:p-6">
            <Tada />
        </div>
      </div>
    </div>
  </div>
</template>


<script setup>
import { onMounted, onUnmounted, ref } from "vue";
import { useLocationStore } from "@/stores/location";
import { useTripStore } from "@/stores/trip";
import axios from "axios";
import { useRouter } from "vue-router";
import Tada from "../components/Tada.vue";

const location = useLocationStore();
const trip = useTripStore();

const gMap = ref(null);
const title = ref("Driving To Passenger ...");

const router = useRouter();

const intervalRef = ref(null);

const currentIcon = {
  url: "https://openmoji.org/data/color/svg/1F698.svg",
  scaledSize: {
    width: 24,
    height: 24,
  },
};

const destinationIcon = {
  url: "https://openmoji.org/data/color/svg/1F920.svg",
  scaledSize: {
    width: 24,
    height: 24,
  },
};

const updateMapBounds = (mapObject) => {
  let originPoint = new google.maps.LatLng(location.current.geometry),
    destinationPoint = new google.maps.LatLng(location.destination.geometry),
    latLngBounds = new google.maps.LatLngBounds();

  latLngBounds.extend(originPoint);
  latLngBounds.extend(destinationPoint);

  mapObject.fitBounds(latLngBounds);
};

const broadcastDriverLocation = () => {
  axios
    .post(
      `http://localhost:8000/api/trip/${trip.id}/location`,
      {
        driver_location: location.current.geometry,
      },
      {
        headers: {
          Authorization: `Bearer ${localStorage.getItem("token")}`,
        },
      }
    )
    .then((res) => {})
    .catch((err) => {
      console.log(err);
    });
};

const handlePassengerPickedUp = () => {
  axios
    .post(
      `http://localhost:8000/api/trip/${trip.id}/start`,
      {},
      {
        headers: {
          Authorization: `Bearer ${localStorage.getItem("token")}`,
        },
      }
    )
    .then((res) => {
      title.value = "Travilg to the destination...";
      location.$patch({
        destination: {
          name: res.data.destination_name,
          geometry: res.data.destination,
        },
      });
      trip.$patch(res.data);
    })
    .catch((err) => {
      console.log(err);
    });
};

const handleCompleteTrip = () => {
  axios
    .post(
      `http://localhost:8000/api/trip/${trip.id}/end`,
      {},
      {
        headers: {
          Authorization: `Bearer ${localStorage.getItem("token")}`,
        },
      }
    )
    .then((res) => {
      title.value = "Trip Completed!";

      trip.$patch(res.data);

      setTimeout(() => {
        trip.reset();
        location.reset();
        router.push({
          name: "standby",
        });
      }, 3000);
    })
    .catch((err) => {
      console.log(err);
    });
};

onMounted(() => {
  gMap.value.$mapPromise.then((mapObject) => {
    updateMapBounds(mapObject);
    intervalRef.value = setInterval(async () => {
      //update dirver current position and update map bounds
      await location.updateCurrentLocation();

      broadcastDriverLocation();
      updateMapBounds(mapObject);
    }, 5000);
  });
});

onUnmounted(() => {
  clearInterval(intervalRef.value);
  intervalRef.value = null;
});
</script>