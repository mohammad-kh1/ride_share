import { createRouter, createWebHistory } from 'vue-router'
import LoginView from '../views/LoginView.vue'
import LandingView from '../views/LandingView.vue'
import LocationView from '../views/LocationView.vue'
import MapView from '../views/MapView.vue'
import TripView from '../views/TripView.vue'
import StandbyView from '../views/StandbyView.vue'
import DriverView from '../views/DriverView.vue'
import DrivingView from '../views/DrivingView.vue'


import axios from 'axios';

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'login',
      component: LoginView
    },
    {
      path: '/landing',
      name: 'landing',
      component: LandingView
    },
    {
      path: '/location',
      name: 'location',
      component: LocationView
    },
    {
      path: '/map',
      name: 'map',
      component: MapView
    },
    {
      path: '/trip',
      name: 'trip',
      component: TripView
    },
    {
      path: '/standby',
      name: 'standby',
      component: StandbyView
    },
    {
      path: '/driver',
      name: 'dirver',
      component: DriverView
    },
    {
      path:'/driving',
      name:'driving',
      component: DrivingView
    }
  ]
})

router.beforeEach((to, from) => {
  if (to.name === "login") {
    return true;
  }
  if (!localStorage.getItem("token")) {
    return {
      name: "login"
    }
  }


  checkToken();

});

const checkToken = () => {
  axios.get("http://localhost:8000/api/user", {
    headers: {
      Authorization: `Bearer ${localStorage.getItem('token')}`
    }
  }).then((res) => {

  }).catch((err) => {
    localStorage.removeItem("token");
    router.push({
      name: 'login'
    })
  });
}

export default router
