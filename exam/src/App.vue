<template>
  <div id="app">
    <TopBar />
    <router-view/>
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
