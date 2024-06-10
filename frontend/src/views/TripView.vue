<template>
  <div class="pt-16">
    <h1 class="text-3xl font-semibold mb-4">{{ title }}</h1>

    <div>
      <div
        class="overflow-hidden shadow sm:rounded-md max-w-sm mx-auto text-left"
      >
        <div class="bg-white px-4 py-5 sm:p-6">
          <div>
            <GMapMap
              :zoom="14"
              :center="location.current.geometry"
              ref="gMap"
              style="width: 100%; height: 256px"
            >
              <GMapMarker
                :position="trip.driver_location"
                :icon="currentIcon"
              />
            </GMapMap>
          </div>
          <div class="bg-gray-50 px-4 py-3 text-right sm:px-6">
            <span>
              {{ message }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useLocationStore } from "@/stores/location";
import { useTripStore } from "@/stores/trip";
import { onMounted, ref } from "vue";
import Echo from "laravel-echo";
import Pusher from "pusher-js";
import { useRouter } from "vue-router";

const trip = useTripStore();
const location = useLocationStore();

const router = useRouter();

const title = ref("Waiting on a driver...");
const message = ref(
  "When a Driver accepts the trip , thier info will appear here."
);

const currentIcon = {
  url: "https://openmoji.org/data/color/svg/1F698.svg",
  scaledSize: {
    width: 24,
    height: 24,
  },
};

onMounted(() => {
  let echo = new Echo({
    broadcaster: "reverb",
    key: "1imhphbk8ucxhcoebih7",
    wsHost: "127.0.0.1",
    wsPort: 8080,
    forceTLS: false,
    enabledTransports: ["ws", "wss"],
  });

  echo
    .channel(`passenger_${trip.user_id}`)
    .listen("TripAccepted", (e) => {
      trip.$patch(e.trip);
      console.log(e.trip);
      title.value = "A driver is on the way!";
      message.value = `${e.trip.driver.user.name} is coming in a ${e.trip.driver.make} with color of ${e.trip.driver.color}`;
    })
    .listen("TripLocationUpdated", (e) => {
      trip.$patch(e.trip);
    })
    .listen("TripStarted", (e) => {
      trip.$patch(e.trip);
      location.$patch({
        current: {
          geometry: e.trip.destination,
        },
      });

      title.value = "You're on your way!";
      message.value = `You are headed to ${e.trip.destination_name}`;
    })
    .listen("TripEnd", (e) => {
      trip.$patch(e.trip);

      title.value = "You've Arrived !";
      message.value = `Hope you enjoyed your ride with ${e.trip.driver.user.name}`;
      setTimeout(() => {
        trip.reset();
        location.reset();

        router.push({
          name: "landing",
        });
      });
    });
});
</script>
