<template>
    <footer class="page-footer font-small text-white blue" style="background-color: #00A0EA;">

    <div>
        <div class="float-left" style="width: 50px; height: 50px;"><img :src="$URL+'/icon/lead.png'" style="width: 100%;"></div>
        <div class="float-right" style="width: 50px; height: 50px;"><img :src="$URL+'/icon/mila.png'" style="width: 100%;"></div>
        <div class="text-center py-3 font-weight-bold">Property of LEAD Group of Companies</div>
    </div>

    </footer>
</template>

<style scoped>
    @media only screen and (min-width: 768px){
        footer {
            position: fixed;
            height: 50px;
            background-color: red;
            bottom: 0px;
            left: 0px;
            right: 0px;
            margin-bottom: 0px;
        }
    }
</style>