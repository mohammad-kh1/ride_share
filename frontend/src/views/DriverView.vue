<template>
  <div class="py-16">
    <h1 class="text-3xl font-semibold mb-4">Driver and car details</h1>

    <form action="" @submit.prevent="handleSaveDriver">
      <div
        class="overflow-hidden shadow sm:dounded-md max-w-sm mx-auto text-left"
      >
        <div class="bg-white px-4 py-4 sm:p-6 p-5 shadow border">
          <div class="border rounded shadow">
            <input
              type="text"
              name="name"
              id="name"
              class="mt-1 block w-full px-3 py-2 rounded-md border-gray-300 shadow-sm"
              placeholder="Full Name"
              v-model="driverDetails.name"
            />
          </div>
          <div class="mt-2 border rounded shadow">
            <input
              type="number"
              name="year"
              id="year"
              class="mt-1 block w-full px-3 py-2 rounded-md border-gray-300 shadow-sm"
              placeholder="Car Year"
              v-model="driverDetails.year"
            />
          </div>
          <div class="mt-2 border rounded shadow">
            <input
              type="text"
              name="make"
              id="make"
              class="mt-1 block w-full px-3 py-2 rounded-md border-gray-300 shadow-sm"
              placeholder="Make"
              v-model="driverDetails.make"
            />
          </div>
          <div class="mt-2 border rounded shadow">
            <input
              type="text"
              name="model"
              id="model"
              class="mt-1 block w-full px-3 py-2 rounded-md border-gray-300 shadow-sm"
              placeholder="Car Model"
              v-model="driverDetails.model"
            />
          </div>
          <div class="mt-2 border rounded shadow">
            <input
              type="color"
              name="color"
              id="color"
              class="mt-1 block w-full px-3 py-2 rounded-md border-gray-300 shadow-sm"
              placeholder="Car Color"
              v-model="driverDetails.color"
            />
          </div>
          <div class="mt-2 border rounded shadow">
            <input
              type="text"
              name="license_plate"
              id="license_plate"
              class="mt-1 block w-full px-3 py-2 rounded-md border-gray-300 shadow-sm"
              placeholder="license plate"
              v-model="driverDetails.license_plate"
            />
          </div>
          <div class="mt-2">
            <button
              type="submit"
              @click.prevent="handleSaveDriver"
              class="rounded-md border border-transparent bg-black py-2 px-4 text-sm font-medium text-white"
            >
              Continue
            </button>
          </div>
        </div>
      </div>
    </form>
  </div>
</template>

<script setup>
import router from "@/router";
import axios from "axios";
import { reactive } from "vue";

const driverDetails = reactive({
  name: "",
  year: null,
  make: "",
  color: "",
  license_plate: "",
});

const handleSaveDriver = () => {
  axios
    .post("https://localhost:8000/api/driver", driverDetails, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem("token")}`,
      },
    })
    .then((res) => {
      router
        .push({
          name: "standby",
        })
        .catch((err) => {
          console.log(err);
        });
    });
};
</script>