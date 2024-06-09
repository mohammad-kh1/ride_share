<template>
  <div class="pt-16">
    <h1 class="text-3xl font-semibold mb-4">Where are you going?</h1>
    <form action="" @submit.prevent="handleSuggestionLocation">
      <div
        class="overflow-hidden shadow sm:rounded-md max-w-sm mx-auto text-left"
      >
        <div class="bg-white px-4 py-5 sm:p-6">
          <div class="">
            <GMapAutocomplete
              placeholder="This is a placeholder"
              @place_changed="setPlace"
              class="border p-5 rounded-md shadow w-full mb-5"
            >
            </GMapAutocomplete>
          </div>
          <div class="bg-gray-50 px-4 py-3 text-right sm:px-6">
            <button
              class="inline-flex justify-center rounded-md border border-trasnparent bg-black py-2 text-white p-5"
              @click="handleSuggestionLocation"
            >
              Find A Ride
            </button>
          </div>
        </div>
      </div>
    </form>
  </div>
</template>


<script setup>
import { useLocationStore } from "@/stores/location";
import { ref } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";

const location = useLocationStore();

const searchQuery = ref("");
const suggestions = ref({});

const router = useRouter();

const setPlace = (e) => {
  searchQuery.value = e.city;
  location.$patch({
    destination: {
      name: e.name,
      address: e.formatted_address,
      geometry: {
        lat: e.geometry.location.lat(),
        lng: e.geometry.location.lng(),
      },
    },
  });
};

const handleSuggestionLocation = () => {
  if (location.destination.name !== "") {
    router.push({
      name: "map",
    });
  }
};
</script>

<style scoped>
.suggestions {
  border: 1px solid #ccc;
  border-radius: 4px;
  margin-top: 10px;
}
</style>