<template>
  <div id="app">
    <TopBar />
    <router-view class="mb-5"/>
    <vue-progress-bar />
    <Footer />
  </div>
</template>

<script>
import TopBar from '@/components/TopBar'
import Footer from '@/components/Footer'

export default {
  name: 'app-main',
  components: {
    TopBar,
    Footer
  }
}
</script>
