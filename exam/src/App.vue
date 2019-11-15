<template>
  <div id="app">
    <TopBar />
    <router-view/>
    <vue-progress-bar />
  </div>
</template>

<script>
import TopBar from '@/components/TopBar'

export default {
  name: 'app-main',
  components: {
    TopBar
  }
}
</script>
