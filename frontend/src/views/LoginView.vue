<script setup>
import { computed, onMounted, ref } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";

const router = useRouter();

const email = ref("");
const login_code = ref(null);

const waitForOtpVerification = ref(false);

onMounted(() => {
  if (localStorage.getItem("token")) {
    router.push({ name: "landing" });
  }
});

const handleLogin = () => {
  axios
    .post("http://localhost:8000/api/login", {
      email: email.value,
    })
    .then((res) => {
      console.log(res);
      waitForOtpVerification.value = true;
    })
    .catch((err) => {
      console.log(err);
      alert(err.response.data.message);
    });
};

const handleVerification = () => {
  axios.post("http://localhost:8000/api/login/verify", {
      email: email.value,
      login_code: login_code.value,
    })
    .then((res) => {

      localStorage.setItem('token', res.data);

      router.push({
        name: "landing",
      });

    })
    .catch((err) => {
      console.log(err);
      alert(err.response.data.message);
    });
};
</script>

<template>
  <div class="pt-16">
    <form
      v-if="!waitForOtpVerification"
      action="#"
      @submit.prevent="handleLogin"
    >
      <h1 class="text-3xl font-semibold mb-4">Enter Your Email</h1>

      <div
        class="overflow-hidden shadow sm:rounded-md max-w-sm mx-auto text-left"
      >
        <div class="bg-white px-4 py-5 sm:p-6">
          <div>
            <input
              type="email"
              name="email"
              id="email"
              placeholder="Email"
              class="mt-1 block w-full px-3 py-2 rounded-md border border-gray-300 shadow-sm"
              v-model="email"
            />
          </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 text-right sm:px-6">
          <button
            type="submit"
            @submit.prevent="handleLogin"
            class="inline-flex justify-center rounded-md border border-transparent bg-black py-2 text-white p-5"
          >
            Continue
          </button>
        </div>
      </div>
    </form>

    <form v-else action="#" @submit.prevent="handleVerification">
      <h1 class="text-3xl font-semibold mb-4">Enter Verification code sent</h1>

      <div
        class="overflow-hidden shadow sm:rounded-md max-w-sm mx-auto text-left"
      >
        <div class="bg-white px-4 py-5 sm:p-6">
          <div>
            <input
              type="text"
              name="login_code"
              id="login_code"
              placeholder="Enter Code"
              class="mt-1 block w-full px-3 py-2 rounded-md border border-gray-300 shadow-sm"
              v-model="login_code"
            />
          </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 text-right sm:px-6">
          <button
            type="submit"
            @submit.prevent="handleVerification"
            class="inline-flex justify-center rounded-md border border-transparent bg-black py-2 text-white p-5"
          >
            Verifiy
          </button>
        </div>
      </div>
    </form>
  </div>
</template>


<style scoped>
</style>