<template>
  <div class="pt-16">
    <h1 class="text-3xl font-semibold mb-4">Uber</h1>

    <div
      class="overflow-hidden shadow sm:rounded-md max-w-sm mx-auto text-left"
    >
      <div class="bg-white px-4 py-5 sm:p-6">
        <div class="flex justify-between">
          <button
            class="rounded-md border border-transparent bg-black py-2 px-4 text-sm font-medium text-white"
            @click="handleStartDriving"
          >
            Start Driving
          </button>
          <button
            class="rounded-md border border-transparent bg-black py-2 px-4 text-sm font-medium text-white"
            @click="handleFindARide"
          >
            Find A Ride
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from "axios";
import { useRouter } from "vue-router";

const handleStartDriving = () => {
  axios
    .get("http://localhost:8000/api/driver", {
      headers: {
        Authorization: `Bearer ${localStorage.getItem("token")}`,
      },
    })
    .then((res) => {
      if (typeof res.data.driver == "object") {
        router.push({
          name: "standby",
        });
      } else {
        router.push({
          name: "driver",
        });
      }
    })
    .catch((error) => {
      console.log(error);
    });
};

const router = useRouter();

const handleFindARide = () => {
  router.push({
    name: "location",
  });
};
</script>