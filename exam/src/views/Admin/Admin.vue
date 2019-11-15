<template>
    <div class="row mt-5 justify-content-center">
        <div class="col-md-12">
            <div class="container">
                <div class="col-md-12">
                    <div class="card">
                        <router-view></router-view>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'admin',
}
</script>